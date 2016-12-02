<?php
use Sooh\Base\Form\Item as form_def;

class DevwareController extends \Prj\ManagerCtrl {
	/**
	 * 标的资金流水的
	 */
	public function waremoneylogAction()
	{
		$pageSize = 10000000;
		$frm = \Sooh\Base\Form\Broker::getCopy('default')->init(\Sooh\Base\Tools::uri(), 'get',
			\Sooh\Base\Form\Broker::type_s);
		$frm->addItem('_waresId_eq', form_def::factory('标的ID', '', form_def::text) )
			->addItem('pageId', 1)
			->addItem('pageSize', $pageSize);
		$frm->fillValues();
		if ($frm->flgIsThisForm) {
			$where = $frm->getWhere();
		} else {
			$where = [];
		}
		$pager = new \Sooh\DB\Pager($pageSize, [$pageSize], false);
		if(empty($where['waresId='])){
			$pager->init(-1, 1);
			$this->_view->assign('pager', $pager);
			$this->_view->assign('header', $this->header);
			$this->_view->assign('rs', []);
			return;
		}
		$this->db = \Sooh\DB\Broker::getInstance();
		$this->waresId = $where['waresId='];//1458976994781025
		$pager->init(-1, 1);
		/**
		 * 找出所有的参与投资的用户以及对应的订单号
		 */
		$this->find_investOrder_user();
		/**
		 * 找出平台中对应用户对应订单的钱包流水
		 */
		$this->find_wallyTally_platform();
		/**
		 * 找出平台中系统户流水
		 */
		$this->find_sysTally_platform();	
		error_log('999999999999999999999999999999999999999999999999999999999999999999999');
		/**
		 * 找出网关中对应用户对应投资记录
		 */
		$this->find_investment_gateway();
		/**
		 * 找出网关中对应回款记录
		 */
		$this->find_return_gateway();
		$this->_view->assign('pager', $pager);
		$this->_view->assign('header', $this->header);
		$this->_view->assign('rs', $this->getRecords());
	}
	/**
	 *
	 * @var \Sooh\DB\Interfaces\All 
	 */
	protected $db;
	protected $waresId;
	protected $investOrder_user;
	protected function find_investOrder_user()
	{
		$fields = 'userId,ordersId,orderTime,amount,amountExt,amountFake';
		$usersAndOrders = \Prj\Data\Investment::loopFindRecordsByFields(['orderStatus]'=>'8','waresId'=>$this->waresId], null, $fields);
		foreach($usersAndOrders as $r){
			$this->investOrder_user[$r['ordersId']]=$r['userId'];
			$this->setRecord('平台',$r['userId'],$r['orderTime'],'下单本金',-$r['amount'],$r['ordersId']);
			if($r['amountExt']>0){
				$this->redpackUsed+=$r['amountExt'];
				$this->setRecord('平台',0,$r['orderTime'],'累计红包',-$this->redpackUsed,'0');
			}
		}
	}
	protected $redpackUsed=0;
	protected function find_wallyTally_platform()
	{
		foreach($this->investOrder_user as $orderId=>$userId){
			$tally = \Prj\Data\WalletTally::getCopy($userId);
			$rs = $tally->db()->getRecords($tally->tbname(), '*',['orderId'=>$orderId]);
			foreach($rs as $r){
				$cmp = explode('_', $r['codeCreate']);
				$dt = \Prj\Misc\View::fmtYmd($r['timeCreate'], 'time');
				//wares_1458964467467471_flow
				//buy_wares_1458964467467471
				//return_fund_1459418015917544
				switch ($cmp[0]){
					case 'buy':
						if($cmp[1]=='wares'){$intro='';	//下单本金
						}else{	$intro = $r['codeCreate'];}
						break;
					case 'return':
						if($r['tallyType']==50){
							$intro='回息@'.$r['sn'];
						}elseif($r['tallyType']==55){
							$intro='回本';
						}
						break;
					case 'wares':
						if($cmp[2]=='flow'){$intro='流标@'.substr($dt,0,10);
						}else{	$intro = $r['codeCreate'];	}
						break;
				}
				if(!empty($intro)){
					$this->setRecord('平台',$userId,$r['timeCreate'],$intro,$r['nAdd'],$r['sn']);
				}
			}
		}
	}
	protected function find_sysTally_platform()
	{
		$rs = $this->db->getRecords('db_p2p.tb_systally_0','*', ['waresId'=>  $this->waresId]);
		foreach($rs as $r){
			$intro = $this->platform_systally[$r['type']].'@'.$r['sn'];
			$this->setRecord('平台',0,$r['tallyYmd'],$intro,$r['amount'],$r['sn']);
		}
	}
	protected function find_investment_gateway(){
		$rs = $this->db->getRecords('db_p2ppay.investment','*',['waresId'=>$this->waresId,'orderStatus'=>['PAY_FINISHED','TRADE_FINISHED']]);
		foreach($rs as $r){
			$chk = $this->db->getRecord('db_p2ppay.collect_trade', 'invest_sn,transSN',['out_trade_no'=>$r['orderId']]);
			if(!empty($chk['invest_sn'])){//扣款成功
				$redpackOrder = $chk['transSN'];
				$this->setRecord('网关',$r['userId'],$r['orderTime'],'下单本金',-$r['amount'],$r['orderId']);
			}
		}
		if(!empty($redpackOrder)){
			$chk = $this->db->getOne('db_p2ppay.collect_trade', 'amount',['out_trade_no'=>$redpackOrder]);
			if(!empty($chk)){
				$this->setRecord('网关',0,$r['orderTime'],'累计红包',-$chk,'0');
			}
		}
		
	}
	protected function find_return_gateway()
	{
		$rs = $this->db->getRecords('db_p2ppay.db_sina_return_fund', '*',['waresId'=>$this->waresId,'Msg'=>['PAY_FINISHED','TRADE_FINISHED']]);
		foreach($rs as $r){
			if($r['amount']){
				$this->setRecord('网关',$r['userId'],$r['dt'],'回本',$r['amount'],$r['SN']);
			}
			if($r['interest']){
				$this->setRecord('网关',$r['userId'],$r['dt'],'回息@'.$r['SN'],$r['interest'],$r['SN']);
			}
		}
		
		$rs = $this->db->getRecords('db_p2ppay.db_company_profit', '*',['waresId'=>$this->waresId]);
		foreach($rs as $r){
			if($r['step']==1){
				$sn = $r['randomSN'];
				$status = $r['backStatus'];
				$pre = '转账服务费';
			}else{
				$sn = $r['collectRandomSN'];
				$status = $r['collectBackStatus'];
				$pre = '还款手续费';
			}
			//error_log(">>>>>>>>>>>>>>>>>>>>>$pre $sn $status ".$r['amountFee']);
			if(in_array($status, ['PAY_FINISHED','TRADE_FINISHED'])){
				$this->setRecord('网关',0,$r['requestTime'],$pre.'@'.$sn,$r['amountFee'],$sn);
			}
		}
	}
	protected $errsFound=[];
	protected $header=[
			'说明'=>100,
			'账户'=>70,
			'时间'=>60,	
			'订单号'=>100,
			'平台登记变化'=>60,
			'网关登记变化'=>60,
	];
	protected $platform_systally=[
		100=>'还款垫付',
		300=>'返利',
		400=>'转账垫付(红包)',
		500=>'转账服务费',
		600=>'还款手续费',
		700=>'平台贴息',	
	];
	protected function setRecord($locate,$userId,$dt,$desc,$chg,$orderid)
	{
		if(is_numeric($dt)){
			$dt = \Prj\Misc\View::fmtYmd($dt, 'time');
		}
		$key = $desc.'_'.$userId.'_'.$orderid;
		switch ($locate){
			case '平台':$fieldChg='chg_platform';break;
			case '网关':$fieldChg='chg_gateway';break;
			default : throw new \ErrorException('unknown location of ware-money-flow');
		}
		if(!isset($this->rs[$key])){
			$this->rs[$key] = ['desc'=>$desc,'u'=>$userId,'dt'=>$dt,'orderId'=>$orderid];
			$this->rs[$key]['chg_platform']=0;
			$this->rs[$key]['chg_gateway']=0;
			$this->rs[$key]['_pkey_val_']=md5($key);
		}
		$this->rs[$key][$fieldChg]=$chg;

	}
	protected $rs=[];
	protected function getRecords()
	{
		ksort($this->rs);
		foreach($this->rs as $k=>$r){
			if(!empty($r['u'])){
				$tmp = \Prj\Data\User::getCopy($r['u']);
				$tmp->load();
				//var_log($tmp);
				$this->rs[$k]['u'] = $tmp->getField('phone');//.'('.$this->rs[$k]['u'].')';
			}
		}
		
		return $this->rs;
	}
}