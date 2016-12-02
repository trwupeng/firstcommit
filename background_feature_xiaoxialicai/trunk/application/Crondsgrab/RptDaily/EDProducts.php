<?php
namespace PrjCronds;
/**
 * OK:
 * 日报标的统计
 * todo: 状态位参与判定
 * @author Simon Wang <hillstill_simon@163.com>
 */
class EDProducts extends \Sooh\Base\Crond\Task{
	public function init() {
		parent::init();
		$this->toBeContinue=true;
		$this->_secondsRunAgain=1800;//每30分钟启动一次
		$this->_iissStartAfter=155;//每小时02分后启动

		$this->ret = new \Sooh\Base\Crond\Ret();

	}
	public function free() {
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
	protected function oneday($ymd)
	{
		$db = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_p2prpt);
		error_log('标的统计需要状态位参与判定');
		$prdtNumNew = \Rpt\EvtDaily\PrdtNumNew::getCopy('PrdtNumNew');
		$prdtNumNew->reset();
		$prdtAmountNew  = \Rpt\EvtDaily\PrdtAmountNew::getCopy('PrdtAmountNew');
		$prdtAmountNew->reset();
		$prdtNumOlder  = \Rpt\EvtDaily\PrdtNumOlder::getCopy('PrdtNumOlder');
		$prdtNumOlder->reset();
		
		$rs = $db->getRecords(\Rpt\Tbname::tb_products_final, 'shelfId, count(*) as n, sum(amount)/100 as a ',
				['ymdStartReal'=>$ymd, 'statusCode>'=>\Prj\Consts\Wares::status_ready],'groupby shelfId');
		foreach ($rs as $r){
			$prdtAmountNew->add($r['a'], 0, 0, $r['shelfId']);
			$prdtNumNew->add($r['n'], 0, 0, $r['shelfId']);
		}
		$prdtAmountNew->save($db, $ymd);
		$prdtNumNew->save($db, $ymd);
		//var_log($rs);
		//var_log(\Sooh\DB\Broker::lastCmd(false));
		
		$rs = $db->getRecords(\Rpt\Tbname::tb_products_final, 'shelfId, count(*) as n, sum(amount)/100 as a ',
				['ymdStartReal<'=>$ymd,'ymdEndReal]'=>$ymd, 'statusCode>'=>\prj\Consts\Wares::status_ready],'groupby shelfId');
//error_log(\Sooh\DB\Broker::lastCmd());
		foreach ($rs as $r){
			//$prdtAmountNew->add($r['a'], 0, 0, $r['shelfId'],$r['mainType']);
			$prdtNumOlder->add($r['n'], 0, 0, $r['shelfId']);
		}
		$rs = $db->getRecords(\Rpt\Tbname::tb_products_final, 'shelfId, count(*) as n, sum(amount)/100 as a ',
				['ymdStartReal<'=>$ymd,'ymdEndReal'=>0, 'statusCode>'=>\prj\Consts\Wares::status_ready],'groupby shelfId');
		if(!empty($rs)) {
			foreach($rs as $r) {
				$prdtNumOlder->add($r['n'], 0, 0, $r['shelfId']);
			}
		}
//		var_log($rs);
//		var_log(\Sooh\DB\Broker::lastCmd(false));
		$prdtNumOlder->save($db, $ymd);
//		var_log(\Sooh\DB\Broker::lastCmd(false),"[trace-save]".$accounts->totalAdd);
		$this->lastMsg = 'New:'.$prdtNumNew->totalAdd;//要在运行日志中记录的信息
	}
}
