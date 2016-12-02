<?php
namespace PrjCronds;
/**
 * php /var/www/licai_php/public/crond.php "__=crond/rungrab&task=Standalone.CrondProducts&ymdh=20150819"
 * 
 * 产品表
 * @author yixiu
 *
 */

class CrondProducts extends \Rpt\Misc\DataCrondGather {
	public function init () {
	    parent::init();
		$this->db_rpt = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_p2prpt);
	    $this->_iissStartAfter = 600;
		$this->_secondsRunAgain = 600;
	}
	protected $db_produce;
	protected $db_rpt;

	public function free() {
		$this->db_produce = null;
		$this->db_rpt = null;
		parent::free();
	}


	protected function gather () {
	    $this->printLogOfTimeRang();
		$startTime = date('YmdHis', $this->dtFrom);
		$endTime = date('YmdHis', $this->dtTo);
// 目前没有使用timeStartReal字段
		$where = ['timeStartReal]'=>$startTime, 'timeStartReal['=>$endTime];
		$arr_product_real = \Prj\Data\Wares::loopFindRecordsByFields($where, null, 'waresId', 'getCol');

		$where = ['timeStartPlan]'=>$startTime, 'timeStartPlan['=>$endTime];
		$arr_product_plan = \Prj\Data\Wares::loopFindRecordsByFields($where, null, 'waresId', 'getCol');

		$arr_product = array_merge($arr_product_real, $arr_product_plan);
		$arr_product_tmp = $arr_product;
		$arr_product = array_unique($arr_product);

		$this->ret->total = sizeof($arr_product);
		if(!empty($arr_product)) {
			foreach($arr_product as $pid) {
				$o = \Prj\Data\Wares::getCopy($pid);
				$o->load(\Rpt\fields::$tb_products_produce_fields);
				$r = $o->dump();
				$o->free();
//var_log($r, 'r>>>>');
				$tmp = [
					'waresId'=>$r['waresId'],
					'waresName'=>$r['waresName'],
					'waresSN'=>$r['waresSN'],
					'deadLine'=>$r['deadLine'],
					'dlUnit'=>$r['dlUnit'],
					'tags'=>$r['tags'],
					'mainType'=>$r['mainType'],
					'subType'=>$r['subType'],
					'userLimit'=>$r['userLimit'],
					'vipLevel'=>$r['vipLevel'],
					'priceStart'=>$r['priceStart'],
					'priceStep'=>$r['priceStep'],
					'amount'=>$r['amount'],
					'remain'=>$r['remain'],
					'realRaise'=>$r['realRaise'],
					'yieldStatic'=>$r['yieldStatic'],
					'yieldStaticAdd'=>$r['yieldStaticAdd'],
					'yieldFloatFrom'=>$r['yieldFloatFrom'],
					'yieldFloatTo'=>$r['yieldFloatTo'],
					'shelfId'=>$r['shelfId'],
					'statusCode'=>$r['statusCode'],
					'returnType'=>$r['returnType'],
					'interestStartType'=>$r['interestStartType'],
					'payGift'=>$r['payGift'],
					'repay'=>$r['repay'],
					'borrowerId'=>$r['borrowerId'],
				    'introDisplay'=>json_encode($r['introDisplay'])
				];

				if(!empty($r['timeStartPlan'])) {
					$tmp['ymdStartPlan'] = date('Ymd', strtotime($r['timeStartPlan']));
				}
				if(!empty($r['timeEndPlan'])) {
					$tmp['ymdEndPlan'] = date('Ymd', strtotime($r['timeEndPlan']));
				}
				if(!empty($r['timeStartReal'])) {
					$tmp['ymdStartReal'] = date('Ymd', strtotime($r['timeStartReal']));
				}
				if(!empty($r['timeEndReal'])) {
					$tmp['ymdEndReal'] = date('Ymd', strtotime($r['timeEndReal']));
				}
				if(!empty($r['ymdPayPlan'])){
					$tmp['ymdPayPlan'] = date('Ymd', $r['ymdPayPlan']);
				}
				if(!empty($r['ymdPayReal'])) {
					$tmp['ymdPayReal'] =date('Ymd', $r['ymdPayReal']);
				}
				if(!empty($r['payYmd'])) {
					$tmp['payYmd'] = date('Ymd', strtotime($r['payYmd']));
				}
//var_log($tmp, 'tmp>>>>>>');

				try{
					\Sooh\DB\Broker::errorMarkSkip();
					$this->db_rpt->addRecord(\Rpt\Tbname::tb_products_final, $tmp);
					$this->ret->newadd++;
				}catch(\ErrorException $e){
					if (\Sooh\DB\Broker::errorIs($e)){
						unset($tmp['waresId']);
						$this->db_rpt->updRecords(\Rpt\Tbname::tb_products_final, $tmp,array('waresId'=>$pid));
						$this->ret->newupd++;
					}else {
						error_log($e->getMessage()."\n".$e->getTraceAsString());
					}
				}

			}
			$arr_product = null;
		}

		/**
		 *
		 * 更新尚未还款结束的产品
		 */

		$where = ['statusCode<'=>\Prj\Consts\Wares::status_close];
		$arr_product_upd = $this->db_rpt->getCol(\Rpt\Tbname::tb_products_final, 'waresId', $where);
		if(!empty($arr_product_upd)) {
			foreach($arr_product_upd as $pid) {
				if(in_array($pid, $arr_product_tmp)) {
					continue;
				}
				$o = \Prj\Data\Wares::getCopy($pid);
				$o->load(\Rpt\Fields::$tb_products_produce_fields);
				$r = $o->dump();
				$o->free();
				$tmp = [
						'waresName'=>$r['waresName'],
						'waresSN'=>$r['waresSN'],
						'deadLine'=>$r['deadLine'],
						'dlUnit'=>$r['dlUnit'],
						'tags'=>$r['tags'],
						'mainType'=>$r['mainType'],
						'subType'=>$r['subType'],
						'userLimit'=>$r['userLimit'],
						'vipLevel'=>$r['vipLevel'],
						'priceStart'=>$r['priceStart'],
						'priceStep'=>$r['priceStep'],
						'amount'=>$r['amount'],
						'remain'=>$r['remain'],
						'realRaise'=>$r['realRaise'],
						'yieldStatic'=>$r['yieldStatic'],
						'yieldStaticAdd'=>$r['yieldStaticAdd'],
						'yieldFloatFrom'=>$r['yieldFloatFrom'],
						'yieldFloatTo'=>$r['yieldFloatTo'],
						'shelfId'=>$r['shelfId'],
						'statusCode'=>$r['statusCode'],
						'returnType'=>$r['returnType'],
						'interestStartType'=>$r['interestStartType'],
						'payGift'=>$r['payGift'],
						'repay'=>$r['repay'],
						'borrowerId'=>$r['borrowerId'],
				        'introDisplay'=>json_encode($r['introDisplay'])
				];


				if(!empty($r['timeStartPlan'])) {
					$tmp['ymdStartPlan'] = date('Ymd', strtotime($r['timeStartPlan']));
				}
				if(!empty($r['timeEndPlan'])) {
					$tmp['ymdEndPlan'] = date('Ymd', strtotime($r['timeEndPlan']));
				}
				if(!empty($r['timeStartReal'])) {
					$tmp['ymdStartReal'] = date('Ymd', strtotime($r['timeStartReal']));
				}
				if(!empty($r['timeEndReal'])) {
					$tmp['ymdEndReal'] = date('Ymd', strtotime($r['timeEndReal']));
				}
				if(!empty($r['ymdPayPlan'])){
					$tmp['ymdPayPlan'] = date('Ymd', $r['ymdPayPlan']);
				}
				if(!empty($r['ymdPayReal'])) {
					$tmp['ymdPayReal'] =date('Ymd', $r['ymdPayReal']);
				}
				if(!empty($r['payYmd'])) {
					$tmp['payYmd'] = date('Ymd', strtotime($r['payYmd']));
				}

				$this->db_rpt->updRecords(\Rpt\Tbname::tb_products_final, $tmp, ['waresId'=>$pid]);
				$this->ret->total++;
				$this->ret->newupd++;
			}
		}
		$this->lastMsg = $this->ret->toString();
		error_log('[ Trace ] ### '.__CLASS__.' ### LastMsg:'.$this->lastMsg);
		return true;

	}
}
