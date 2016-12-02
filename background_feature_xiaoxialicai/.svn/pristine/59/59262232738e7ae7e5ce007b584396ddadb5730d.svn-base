<?php
namespace PrjCronds;
/**
 * 绑卡信息
 *
 * TODO:每获得一个成功绑卡的用户添加或者更新记录后，就获取这个用户此次绑卡之前正在使用的应行卡，将这些银行卡状态改为非默认和禁用
 * TODO:　第一次绑卡是从本地数据库获取的。 不是从线上。
 * php /var/www/licai_php/run/crond.php "__=crond/rungrab&task=Standalone.CrondBindcard&ymdh=20150819"
 * @author yixiu
 *
 */
class CrondBindcard extends \Rpt\Misc\DataCrondGather {
	public function init() {
		parent::init();
		$this->db_rpt = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_p2prpt);
		$this->_iissStartAfter = 600;
		$this->_secondsRunAgain = 300;
	}
	public function free(){
		$this->db_rpt = null;
		parent::free();
	}
	protected $db_rpt;
	protected function gather(){
		$this->printLogOfTimeRang();
		$startTime = date('YmdHis', $this->dtFrom);
		$endTime = date('YmdHis', $this->dtTo);

		$where = array('timeCreate]'=>$startTime,'timeCreate['=>$endTime,
				'statusCode'=>[\Prj\Consts\BankCard::disabled, \Prj\Consts\BankCard::enabled]);
		$array_user = \Prj\Data\BankCard::loopFindRecordsByFields($where,null,'distinct(userId)','getCol');

		if(!empty($array_user)) {
			foreach($array_user as $userId) {

				$o = \Prj\Data\BankCard::getCopy($userId);
				$db = $o->db();
				$tbname = $o->tbname();
				$o->free();
				$where['userId'] = $userId;
				$records = $db->getRecords($tbname, \Rpt\Fields::$tb_bankcard_produce_fields, $where, 'sort timeCreate');
				foreach($records as $r) {
					$this->ret->total++;
					$tmp = [
						'orderId'=>$r['orderId'],
						'userId'=>$r['userId'],
						'bankId'=>$r['bankId'],
						'bankCard'=>$r['bankCard'],
						'isDefault'=>$r['isDefault'],
						'statusCode'=>$r['statusCode'],
						'createYmd'=>date('Ymd', strtotime($r['timeCreate'])),
						'createHis'=>date('His', strtotime($r['timeCreate'])),
						'resultMsg'=>$r['resultMsg'],
						'idCardType'=>$r['idCardType'],
						'idCardSN'=>$r['idCardSN'],
						'realname'=>$r['realName'],
						'phone'=>$r['phone'],
						'cardId'=>$r['cardId'],
					];

					if(!empty($r['resultTime'])) {
						$tmp['resultYmd'] = date('Ymd', strtotime($r['resultTime']));
						$tmp['resultHis'] = date('His', strtotime($r['resultTime']));
					}
//var_log($tmp, '抓取的绑卡记录：');
					try{
						\Sooh\DB\Broker::errorMarkSkip();
						$this->db_rpt->addRecord(\Rpt\Tbname::tb_bankcard_final, $tmp);
						$this->ret->newadd++;
					}catch(\ErrorException $e){
						unset($tmp['orderId']);
						$this->db_rpt->updRecords(\Rpt\Tbname::tb_bankcard_final, $tmp, ['orderId'=>$r['orderId']]);
						$this->ret->newupd++;
					}
					// 更新用户信息
					$user_basic_info = \Rpt\Funcs::getUserBasicInfo($userId);
					$firstTimeBindcard = $db->getOne($tbname, 'timeCreate',
							['userId'=>$userId, 'statusCode'=>[\Prj\Consts\BankCard::disabled, \Prj\Consts\BankCard::enabled]],
							'sort timeCreate');
					$user_basic_info['ymdRealnameAuth'] = $user_basic_info['ymdBindcard'] = substr($firstTimeBindcard, 0, 8);
					unset($user_basic_info['userId']);
					$upd_keys = array_keys($user_basic_info);
					$user_basic_info['userId'] = $userId;
//var_log($user_basic_info,'更新的用户信息：');
					$this->db_rpt->ensureRecord(\Rpt\Tbname::tb_user_final, $user_basic_info, $upd_keys);


				}
			}

		}


		// 解绑记录 后续平台会加解绑功能
//				$where = ['unBindTime]'=>$startTime, 'unBindTime['=>$endTime];
//				$arr_unbind_order  = \Prj\Data\BankCard::loopFindRecordsByFields($where,null,'orderId','getCol');
//				if(!empty($arr_unbind_order)) {
//					foreach($arr_unbind_order as $orderId) {
//						$o = \Prj\Data\BankCard::getCopy(array('orderId'=>$orderId));
//						$o->load(['isDefault','statusCode']);
//						$record = $o->dump();
//						$this->db_rpt->updRecords(\Rpt\Tbname::tb_bankcard_final, $record, ['orderId'=>orderId]);
//					}
//				}


		// 抓取绑卡失败的记录

		$where = ['timeCreate]'=>$startTime,'timeCreate['=>$endTime,
				'statusCode'=>\Prj\Consts\BankCard::checking,'resultMsg!'=>''];
		$array_user = \Prj\Data\BankCard::loopFindRecordsByFields($where,null,'distinct(userId)','getCol');
		if(!empty($array_user)) {
			foreach($array_user as $userId) {
				$o = \Prj\Data\BankCard::getCopy($userId);
				$db = $o->db();
				$tbname = $o->tbname();
				$o->free();
				$where['userId'] = $userId;
				$records = $db->getRecords($tbname, \Rpt\Fields::$tb_bankcard_produce_fields, $where);
				foreach($records as $r) {
					$this->ret->total++;
					$tmp = [
							'orderId'=>$r['orderId'],
							'userId'=>$r['userId'],
							'bankId'=>$r['bankId'],
							'bankCard'=>$r['bankCard'],
							'isDefault'=>$r['isDefault'],
							'statusCode'=>$r['statusCode'],
							'createYmd'=>date('Ymd', strtotime($r['timeCreate'])),
							'createHis'=>date('His', strtotime($r['timeCreate'])),
							'resultMsg'=>$r['resultMsg'],
							'idCardType'=>$r['idCardType'],
							'idCardSN'=>$r['idCardSN'],
							'realname'=>$r['realName'],
							'phone'=>$r['phone'],
							'cardId'=>$r['cardId'],
					];

					if(!empty($r['resultTime'])) {
						$tmp['resultYmd'] = date('Ymd', strtotime($r['resultTime']));
						$tmp['resultHis'] = date('His', strtotime($r['resultTime']));
					}
//var_log($tmp, '抓取的绑卡记录：');
					try{
						\Sooh\DB\Broker::errorMarkSkip();
						$this->db_rpt->addRecord(\Rpt\Tbname::tb_bankcard_final, $tmp);
						$this->ret->newadd++;
					}catch(\ErrorException $e){
						unset($tmp['orderId']);
						$this->db_rpt->updRecords(\Rpt\Tbname::tb_bankcard_final, $tmp, ['orderId'=>$r['orderId']]);
						$this->ret->newupd++;
					}
				}
			}
		}



		$this->lastMsg = $this->ret->toString();
		error_log('[ Trace ] ### ' . __CLASS__ . ' ### LastMsg:' . $this->lastMsg);
		return true;
	}
}