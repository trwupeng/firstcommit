<?php
namespace PrjCronds;
/**
 * OK
 * php /var/www/licai_php/public/crond.php "__crond/run&task=RptDaily.EDBuyers&ymdh=20160126"
 *
 * 日报购买统计
 * 快快贷这里，`flagUser`==1的超级用户不参与统计
 * @author Simon Wang <hillstill_simon@163.com>
 */
class EDBuyers extends \Sooh\Base\Crond\Task{
	public function init() {
		parent::init();
		$this->toBeContinue=true;
		$this->_secondsRunAgain=840;//每14分钟启动一次
		$this->_iissStartAfter=455;//每小时05分后启动

		$this->ret = new \Sooh\Base\Crond\Ret();
		$this->db = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_p2prpt);
	}
	/**
	 *
	 * @var \Sooh\DB\Interfaces\All 
	 */
	protected $db;
	protected $ymd;
	public function free() {
		$this->db = null;
		parent::free();
	}

	/**
	 * @param \Sooh\Base\Time $dt
	 */
	protected function onRun($dt) {
		$this->oneday($dt->YmdFull);
		if(!$this->_isManual && $dt->hour<=6){
			$dt0 = strtotime($dt->YmdFull);
			switch ($dt->hour){
				case 1: $this->oneday(date('Ymd',$dt0-86400*10));break;
				case 2: $this->oneday(date('Ymd',$dt0-86400*7));break;
				case 3: $this->oneday(date('Ymd',$dt0-86400*4));break;
				case 4: $this->oneday(date('Ymd',$dt0-86400*3));break;
				case 5: $this->oneday(date('Ymd',$dt0-86400*2));break;
				case 6: $this->oneday(date('Ymd',$dt0-86400*1));break;
			}
		}
		return true;
	}
	protected function oneday($ymd){
		$this->ymd = $ymd;
		error_log("Trace007#".__CLASS__.'->'.__FUNCTION__.'('.$ymd.')');


		$this->gowith(	\Rpt\EvtDaily\BuyNUsrAll::getCopy('BuyNUsrAll'), 
						\Rpt\EvtDaily\BuyNAmountAll::getCopy('BuyNAmountAll'),
					$this->sqlBaseNone('tb_orders_final.ymd<='.$ymd)
		);

		//今日注册并购买
		$this->gowith(	\Rpt\EvtDaily\BuyNUsrNew0::getCopy('BuyNUsrNew0'), 
						\Rpt\EvtDaily\BuyNAmountNew0::getCopy('BuyNAmountNew0'),
				$this->sqlBaseNone('tb_orders_final.ymd='.$ymd.' and tb_user_final.ymdReg='.$ymd)
		);
		$this->gowith(	\Rpt\EvtDaily\BuyCUsrNew0::getCopy('BuyCUsrNew0'), 
						\Rpt\EvtDaily\BuyCAmountNew0::getCopy('BuyCAmountNew0'),
				$this->sqlBaseClient('tb_orders_final.ymd='.$ymd.' and tb_user_final.ymdReg='.$ymd)
		);
		$this->gowith(	\Rpt\EvtDaily\BuyPUsrNew0::getCopy('BuyPUsrNew0'), 
						\Rpt\EvtDaily\BuyPAmountNew0::getCopy('BuyPAmountNew0'),
				$this->sqlBasePrdt('tb_orders_final.ymd='.$ymd.' and tb_user_final.ymdReg='.$ymd)
		);
		//往日注册，今日首购
		$NewBuy = $this->db->getCol(\Rpt\Tbname::tb_orders_final,'distinct(userId)',['firstTimeInAll'=>1, 'ymd'=>$ymd]);
		//error_log('total new buy:'.sizeof($NewBuy).'#'. \Sooh\DB\Broker::lastCmd());
		if(!empty($NewBuy)){
			$oldRegNewBuy = $this->db->getCol(\Rpt\Tbname::tb_user_final, 'userId',['userId'=>$NewBuy,'ymdReg!'=>$ymd]);//排除今日注册的
			//error_log('total new buy:'.sizeof($NewBuy).'#'. \Sooh\DB\Broker::lastCmd());
//var_log($oldRegNewBuy, '$oldRegNewBuy>>>>');
			if(!empty($oldRegNewBuy)){
				$this->gowith(	\Rpt\EvtDaily\BuyNUsrNew1::getCopy('BuyNUsrNew1'), 
								\Rpt\EvtDaily\BuyNAmountNew1::getCopy('BuyNAmountNew1'),
						$this->sqlBaseNone('tb_orders_final.ymd='.$ymd.'  and tb_orders_final.userId in (\''.implode("','",$oldRegNewBuy).'\')')
				);
				$this->gowith(	\Rpt\EvtDaily\BuyCUsrNew1::getCopy('BuyCUsrNew1'),
								\Rpt\EvtDaily\BuyCAmountNew1::getCopy('BuyCAmountNew1'),
						$this->sqlBaseClient('tb_orders_final.ymd='.$ymd.'  and tb_orders_final.userId in (\''.implode("','",$oldRegNewBuy).'\')')
				);
				$this->gowith(	\Rpt\EvtDaily\BuyPUsrNew1::getCopy('BuyPUsrNew1'), 
								\Rpt\EvtDaily\BuyPAmountNew1::getCopy('BuyPAmountNew1'),
						$this->sqlBasePrdt('tb_orders_final.ymd='.$ymd.'  and tb_orders_final.userId in (\''.implode("','",$oldRegNewBuy).'\')')
				);
			}
		}

		//往日注册，再次购买
		$where= 'tb_orders_final.ymd='.$ymd;
		if(!empty($NewBuy)){
			$where.=' and tb_orders_final.userId not in (\''.implode("','",$NewBuy).'\')';
		}
		$this->gowith(	\Rpt\EvtDaily\BuyNUsrNew1::getCopy('BuyNUsrOlder'), 
						\Rpt\EvtDaily\BuyNAmountNew1::getCopy('BuyNAmountOlder'),
				$this->sqlBaseNone($where)
		);
		$this->gowith(	\Rpt\EvtDaily\BuyCUsrNew1::getCopy('BuyCUsrOlder'), 
						\Rpt\EvtDaily\BuyCAmountNew1::getCopy('BuyCAmountOlder'),
				$this->sqlBaseClient($where)
		);
		$this->gowith(	\Rpt\EvtDaily\BuyPUsrNew1::getCopy('BuyPUsrOlder'), 
						\Rpt\EvtDaily\BuyPAmountNew1::getCopy('BuyPAmountOlder'),
				$this->sqlBasePrdt($where)
		);
		
		$BuyAmountDay = \Rpt\EvtDaily\BuyNAmountAll::getCopy('BuyNAmountDay');
		$BuyUsrDay = \Rpt\EvtDaily\BuyNUsrAll::getCopy('BuyNUsrDay');
		$this->lastMsg = ' Today:'.$BuyUsrDay->numOfAct($this->db, $this->ymd) .' amount:'.$BuyAmountDay->numOfAct($this->db, $this->ymd);//要在运行日志中记录的信息
	}


	protected function sqlBaseNone($where)
	{
		return 'select 0 as clientType,0 as prdtType,tb_user_final.copartnerId, tb_user_final.flagUser as uType, count(distinct(tb_orders_final.userId)) as u,sum(tb_orders_final.amount)/100 as n'
				. ' from db_p2prpt.tb_orders_final '
				. ' left join db_p2prpt.tb_user_final on tb_orders_final.userid=tb_user_final.userId '
				. ' where '.$where . ' and tb_orders_final.orderStatus in(2,3,8,10,21,20,38,39)'//and tb_user_final.flagUser!=1
				. ' group by  tb_user_final.copartnerId,tb_user_final.flagUser';
	}
	protected function sqlBaseClient($where)
	{
		return 'select tb_user_final.clientType as clientType,0 as prdtType,tb_user_final.copartnerId, tb_user_final.flagUser as uType, count(distinct(tb_orders_final.userId)) as u,sum(tb_orders_final.amount)/100 as n'
				. ' from db_p2prpt.tb_orders_final '
				. ' left join db_p2prpt.tb_user_final on tb_orders_final.userid=tb_user_final.userId '
				. ' where '.$where . ' and tb_orders_final.orderStatus in(2,3,8,10,21,20,38,39)'//and tb_user_final.flagUser!=1
				. ' group by tb_user_final.clientType,tb_user_final.copartnerId,tb_user_final.flagUser';
	}
	protected function sqlBasePrdt($where)
	{
		return 'select 0 as clientType,tb_orders_final.shelfId as prdtType,tb_user_final.copartnerId, tb_user_final.flagUser as uType, count(distinct(tb_orders_final.userId)) as u,sum(tb_orders_final.amount)/100 as n'
				. ' from db_p2prpt.tb_orders_final '
				. ' left join db_p2prpt.tb_user_final on tb_orders_final.userid=tb_user_final.userId '
				. ' where '.$where . ' and tb_orders_final.orderStatus in(2,3,8,10,21,20,38,39)'//and tb_user_final.flagUser!=1
				. ' group by tb_orders_final.shelfId,tb_user_final.copartnerId,tb_user_final.flagUser';
	}	
	/**
	 * @param \Rpt\EvtDaily\Base $user
	 * @param \Rpt\EvtDaily\Base $amount
	 * @param string $sql select u,n,clientType,copartnerId,prdtType,uType
	 */
	protected function gowith($user,$amount,$sql)
	{
//		var_log($user->displayName(), $user->getActName().'##################################');
//		var_log($amount->displayName(), $user->getActName().'##################################');
//		error_log($sql);
		$result = $this->db->execCustom(['sql'=>$sql]);
		$rs = $this->db->fetchAssocThenFree($result);
		$user->reset();
		$amount->reset();
		foreach ($rs as $r){
			$user->add($r['u'], $r['clientType']>0?$r['clientType']:900, $r['copartnerId'],$r['prdtType'],$r['uType']-0);
			$amount->add($r['n'], $r['clientType']>0?$r['clientType']:900, $r['copartnerId'],$r['prdtType'],$r['uType']-0);
		}
//		var_log($rs,  \Sooh\DB\Broker::lastCmd());
		$user->save($this->db, $this->ymd);
		$amount->save($this->db, $this->ymd);
	}
}
