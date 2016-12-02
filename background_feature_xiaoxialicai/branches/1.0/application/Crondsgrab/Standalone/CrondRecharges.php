<?php
namespace PrjCronds;
/**
 * php /var/www/licai_php/run/crond.php "__=crond/rungrab&task=Standalone.CrondRecharges&ymdh=20151229"
 * 时间段内成功充值提现的流水
 * 
 * 抓取指定时间段内成功的充值和提现订单
 * 检查并更新每个用户tb_user_final中的ymdFirstCharge, numFirstCharge
 * 更新每个用户tb_user_final中的ymdLastCharge,numLastCharge，这两个数据是获取的指定日期内用户的最后成功订单。
 * 更新每个用户tb_user_final中的maxChargeAmount
 *
 * loopFindRecordsByFields: 这个函数用到了排序，充值表是多表的时候，如果一个用户的充值只保存在相同的表里，就没有问题。
 * 
 */

class CrondRecharges extends \Rpt\Misc\DataCrondGather {
	public function init() {
		parent::init();
		$this->_iissStartAfter = 200;
		$this->_secondsRunAgain = 300;
		$this->dbMysql = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_p2prpt);
	}
	protected $dbMysql;
	
	public function free() {
		parent::free();
		$this->dbMysql = null;
	}
	

	protected function gather()
	{
		$this->printLogOfTimeRang();

		/**
		 *
		 * 抓取充值记录
		 */
		$startTime = date('YmdHis', $this->dtFrom);
		$endTime = date('YmdHis', $this->dtTo);
		$where = array('orderTime]' => $startTime,
				'orderTime[' => $endTime, 'orderStatus' => \Prj\Consts\OrderStatus::done, 'amountFlg' => \Prj\Consts\OrderType::recharges);
		$arr_user_id = \Prj\Data\Recharges::loopFindRecordsByFields($where, null, 'distinct(userId) as uid', 'getCol');
//var_log($arr_user_id, '时间段内充值的用户：');
		if (!empty($arr_user_id)) {
			foreach ($arr_user_id as $k => $uid) {
				// 抓取用户的充值记录
				$o = \Prj\Data\Recharges::getCopy($uid);
				$db = $o->db();
				$tbname = $o->tbname();
				$where['userId'] = $uid;
				$records = $db->getRecords($tbname, \Rpt\Fields::$tb_recharge_produce_fields, $where);
//var_log($records, $k. '用户的充值信息');
				foreach ($records as $r) {
					$this->ret->total++;
					$tmp = [
							'ordersId' => $r['ordersId'],
							'userId' => $r['userId'],
							'amount' => $r['amount'],
							'poundage' => $r['poundage'],
							'ymd' => substr($r['orderTime'], 0, 8),
							'hhiiss' => substr($r['orderTime'], -6),
							'bankCard' => $r['bankCard'],
//							'couponId' => $r['couponId'],
							'orderStatus' => $r['orderStatus'],
							'finishYmd' => substr($r['payTime'], 0, 8),
					];

					try {
						\Sooh\DB\Broker::errorMarkSkip();
						$this->dbMysql->addRecord(\Rpt\Tbname::tb_recharges_final, $tmp);
						$this->ret->newadd++;
					} catch (\ErrorException $e) {
						if (\Sooh\DB\Broker::errorIs($e)) {
							unset($tmp['ordersId']);
							$this->dbMysql->updRecords(\Rpt\Tbname::tb_recharges_final, $tmp, ['ordersId' => $r['ordersId']]);
//error_log('充值订单信息：'.\Sooh\DB\Broker::lastCmd());
							$this->ret->newupd++;
						} else {
							error_log($e->getMessage() . "\n" . $e->getTraceAsString());
						}
					}
				}

				$o->free();


				/**
				 *
				 * 更新用户的基本信息
				 */
				$userBasicInfo = \Rpt\Funcs::getUserBasicInfo($uid);

				// 第一次和第二次充值
				$where = ['userId' => $uid, 'amountFlg' => \Prj\Consts\OrderType::recharges, 'orderStatus' => \Prj\Consts\OrderStatus::done];
				$fields = 'orderTime, amount';
				$recharge_ahead = $db->getRecords($tbname, $fields, $where, 'sort orderTime', 2);
				$userBasicInfo['ymdFirstRecharge'] = substr($recharge_ahead[0]['orderTime'], 0, 8);
				$userBasicInfo['amountFirstRecharge'] = $recharge_ahead[0]['amount'];
				if (isset($recharge_ahead[1])) {
					$userBasicInfo['ymdSecRecharge'] = substr($recharge_ahead[1]['orderTime'], 0, 8);
					$userBasicInfo['amountSecRecharge'] = $recharge_ahead[1]['amount'];
				}

				// 最后一次充值
				$recharge_last = $db->getRecord($tbname, $fields, $where, 'rsort orderTime');
				$userBasicInfo['ymdLastRecharge'] = substr($recharge_last['orderTime'], 0, 8);
				$userBasicInfo['amountLastRecharge'] = $recharge_last['amount'];

				// 最多一次充值
				$recharge_max = $db->getRecord($tbname, $fields, $where, 'rsort amount');
				$userBasicInfo['ymdMaxRecharge'] = substr($recharge_max['orderTime'], 0, 8);
				$userBasicInfo['amountMaxRecharge'] = $recharge_max['amount'];

				unset($userBasicInfo['userId']);
				$upd_keys = array_keys($userBasicInfo);
				$userBasicInfo['userId'] = $uid;
				$this->dbMysql->ensureRecord(\Rpt\Tbname::tb_user_final, $userBasicInfo, $upd_keys);
//error_log('更新用户的信息：'.\Sooh\DB\Broker::lastCmd());
				$db = null;
//var_log($userBasicInfo, $k.' 更新的用户基本信息：');
			}
		}
		$arr_user_id = null;

		/**
		 *
		 * 抓取提现记录
		 */

		$where = ['orderTime]' => $startTime, 'orderTime[' => $endTime, 'amountFlg' => \Prj\Consts\OrderType::withdraw, 'orderStatus!' => [\Prj\Consts\OrderStatus::failed]];
		$arr_withdraw_user = \Prj\Data\Recharges::loopFindRecordsByFields($where, null, 'distinct(userId) as uid', 'getCol');
//error_log('提现用户：'.\Sooh\DB\Broker::lastCmd());
		$arr_withdraw_order = [];
		if (!empty($arr_withdraw_user)) {
			foreach ($arr_withdraw_user as $uid) {
				$o = \Prj\Data\Recharges::getCopy($uid);
				$db = $o->db();
				$tbname = $o->tbname();
				$where['userId'] = $uid;
				$records = $db->getRecords($tbname, \Rpt\Fields::$tb_recharge_produce_fields, $where);
				foreach ($records as $r) {
					$this->ret->total++;
					$arr_withdraw_order[] = $r['ordersId'];
					$tmp = [
							'ordersId' => $r['ordersId'],
							'userId' => $r['userId'],
							'amount' => $r['amount'],
							'poundage' => $r['poundage'],
							'ymd' => substr($r['orderTime'], 0, 8),
							'hhiiss' => substr($r['orderTime'], -6),
							'bankCard' => $r['bankCard'],
							'orderStatus' => $r['orderStatus'],
					];
					if (!empty($r['payTime'])) {
						$tmp['finishYmd'] = substr($r['payTime'], 0, 8);
					}

					try {
						\Sooh\DB\Broker::errorMarkSkip();
						$this->dbMysql->addRecord(\Rpt\Tbname::tb_recharges_final, $tmp);
						$this->ret->newadd++;
					} catch (\ErrorException $e) {
						if (\Sooh\DB\Broker::errorIs($e)) {
							unset($tmp['ordersId']);
							$this->dbMysql->updRecords(\Rpt\Tbname::tb_recharges_final, $tmp, array('ordersId' => $r['ordersId']));
							$this->ret->newupd++;
						} else {
							error_log($e->getMessage() . "\n" . $e->getTraceAsString());
						}
					}
				}
			}
		}

		$arr_withdraw_user = null;

		/**
		 *
		 * 更新报表系统中正在提现过程中的记录
		 */

		$where = ['amount<' => 0, 'orderStatus!' => [\Prj\Consts\OrderStatus::done, \Prj\Consts\OrderStatus::abandon]];
		if(!empty($arr_withdraw_order)){
			$where['ordersId!']=$arr_withdraw_order;
		}
		$arr_withdraw_order = null;
		$arr_withdraw_rpt = $this->dbMysql->getCol(\Rpt\Tbname::tb_recharges_final, 'ordersId', $where);
		if (!empty($arr_withdraw_rpt)) {
			$arr_withdraw_rpt = array_chunk($arr_withdraw_rpt, 500);
			foreach ($arr_withdraw_rpt as $group) {
				$records = \Prj\Data\Recharges::loopFindRecordsByFields(['ordersId' => $group], null, \Rpt\Fields::$tb_recharge_produce_fields, 'getRecords');
				foreach ($records as $r) {
					$this->ret->total++;
					$this->ret->newupd++;
					$tmp = [
							'ordersId' => $r['ordersId'],
							'userId' => $r['userId'],
							'amount' => $r['amount'],
							'poundage' => $r['poundage'],
							'ymd' => substr($r['orderTime'], 0, 8),
							'hhiiss' => substr($r['orderTime'], -6),
							'bankCard' => $r['bankCard'],
							'orderStatus' => $r['orderStatus'],
					];
					if (!empty($r['payTime'])) {
						$tmp['finishYmd'] = substr($r['payTime'], 0, 8);
					}

					unset($tmp['ordersId']);
					$this->dbMysql->updRecords(\Rpt\Tbname::tb_recharges_final, $tmp, array('ordersId' => $r['ordersId']));
					$this->ret->newupd++;
					$this->ret->total++;
				}
			}
		}

		$this->lastMsg = $this->ret->toString();
		error_log('[ Trace ] ### ' . __CLASS__ . ' ### LastMsg:' . $this->lastMsg);
		return true;
	}
}