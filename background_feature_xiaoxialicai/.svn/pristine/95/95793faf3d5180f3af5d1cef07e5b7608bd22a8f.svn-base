<?php
/**
 * 给渠道展示的数据，有规则限制展示数据。以后渠道都给这个数据。
 *
 */
class CopartneryestdController extends \Yaf_Controller_Abstract
{
    public function viewsimpleAction() {
		$authCode = $this->_request->get('code');
		$copartnerid = $this->_request->get('copartnerid')-0;
		
        $sys = \Prj\Data\Copartner::getCopy();
		$this->db_p2p=$sys->db();
		$this->db_rpt= \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_p2prpt);
		if(!empty($authCode) && !empty($copartnerid)){
			$r = $sys->db()->getRecord($sys->tbname(), '*',['copartnerId'=>$copartnerid]);
			if(empty($r) || $r['authCode']!=$authCode){
				$this->_view->assign('msg','参数错误');
			}else{
				$arr_contractid = $this->db_p2p->getPair('tb_contract_0', 'contractId','remarks', ['copartnerAbs'=>$r['copartnerAbs'], 'flgDisplay'=>1]);
				$contractIds = array_keys($arr_contractid);
				if (empty($contractIds)) {
					$this->_view->assign('msg', '暂无数据');
					return;
				}
				$this->rs = $this->db_rpt->getRecords(\Rpt\Tbname::tb_user_final, 'userId,realname,ymdReg,length(idCard) as flg,phone,contractId',['flagDisplay=1', 'contractId'=>$contractIds,'ymdReg>'=>date('Ymd',time()-45*86400)],'rsort ymdReg');

				$tmp = array();
				foreach($this->rs as $i=>$r){

					$this->rs[ $i ]['phone']=substr_replace($this->rs[ $i ]['phone'], '****', 3, 4);
					if(!empty($this->rs[ $i ]['realname']))	{
						$this->rs[ $i ]['realname'] = substr_replace($this->rs[ $i ]['realname'], '*', 3,3);
					}

					$tmp[$i]=$r['userId'];
					$this->rs[$i]['phone'] = empty($r['phone'])?"":substr($r['phone'],0,4).'***'.substr($r['phone'],-4);
					if(sizeof($tmp)>=50){
						$this->fillOrder($tmp);
						$tmp=[];
					}
				}
				$this->fillOrder($tmp);
			}

			// 协议描述信息
			$this->_view->assign('contractid_reamrk', $arr_contractid);
			$this->_view->assign('records',$this->rs);
		}else{
			$this->_view->assign('msg','参数错误');
		}
    }
	/**
	 *
	 * @var \Sooh\DB\Interfaces\All 
	 */
	protected $db_p2p;
	protected $db_rpt;
	protected $rs;
	protected function fillOrder($users)
	{
		if(empty($users)){
			return;
		}
		$failed_order_status = [
				\Prj\Consts\OrderStatus::created,
				\Prj\Consts\OrderStatus::abandon,
				\Prj\Consts\OrderStatus::failed,
				\Prj\Consts\OrderStatus::unusual
		];

		$rs = $this->db_rpt->getRecords(\Rpt\Tbname::tb_orders_final, 'userId,ymd,amount,shelfId',['userId'=>$users,'firstTimeInAll'=>1,'orderStatus!'=>$failed_order_status]);
//		$sumamount_ttz = $this->db_rpt->getPair(\Rpt\Tbname::tb_orders_final,'userId', 'sum(amount) as n',['userId'=>$users, 'shelfId'=>0,'orderStatus!'=>$failed_order_status], 'groupby userId');

//		暂时没有活期
		$sumamount_ttz = [];
		$sumamount_diya = $this->db_rpt->getPair(\Rpt\Tbname::tb_orders_final, 'userId', 'sum(amount) as n', ['userId'=>$users, 'shelfId>'=>0, 'orderStatus!'=>$failed_order_status], 'groupby userId');
		$users = array_combine($users, array_keys($users));
		foreach($rs as $r){
			$u = $r['userId'];
			$i = $users[$u];
			$this->rs[ $i ]['firstType'] = ($r['shelfId']>0?'定期':'活期'); // TODO: 有活期的时候需要修正
			$this->rs[ $i ]['ymdBuy']=$r['ymd'];
			$this->rs[ $i ]['amount']=$r['amount'];
			$this->rs[ $i ]['ttz']=$sumamount_ttz[$u];
			$this->rs[ $i ]['diya'] = $sumamount_diya[$u];
		}
	}
}