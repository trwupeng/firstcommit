<?php
/**
 * Created by PhpStorm.
 * User: LTM <605415184@qq.com>
 * Date: 2016/3/11
 * Time: 17:49
 */

/**
 * 获取最近领取注册礼包的列表
 * Class GetReceiveListFromReg
 */
class GetReceiveListFromReg
{
	public function run($view, $request, $response = null)
	{
		error_log('======START GetReceiveListFromReg LOG======');
		$amount = \Prj\Data\Config::get('REGISTER_RED_AMOUNT');
		$view->assign('amount', $amount / 100);//出现多个assign中的amount字段重复，修改如下突出唯一识别
		$view->assign('GetReceiveListFromRegamount', $amount / 100);

		$map = [
			'voucherType' => \Prj\Consts\Voucher::type_real,
			'statusCode]' => 0,
		    'codeCreate' => 'register',
		];
		$pager = new \Sooh\DB\Pager(20);
		$counts = \Prj\Data\Vouchers::loopGetRecordsCount($map);
		$pager->init($counts, 1);
		try {
			$ret = \Prj\Data\Vouchers::loopGetRecordsPage(['timeCreate' => 'rsort'], ['where' => $map], $pager);
//			var_log(['where' => $map], 'where by \prj\data\vouchers');
//			var_log($ret, 'loopGetRecordsPage s by \prj\data\vouchers');

			if ($ret['records']) {
				foreach ($ret['records'] as $k => $v) {
					$userIds[] = $v['userId'];
				}
				$_where = ['userId' => $userIds];

				$_pager = new \Sooh\DB\Pager(20);
				$_counts = \Prj\Data\Vouchers::loopGetRecordsCount($_where);
				$_pager->init($_counts, 1);

				$phones = \Prj\Data\User::loopGetRecordsPage(['ymdReg' => 'rsort', 'hisReg' => 'rsort'], ['where' => $_where], $_pager);
//				var_log(['where' => $userIds], 'where by \prj\data\User');
//				var_log($phones, 'loopGetRecordsPage s by \prj\data\User');
				if ($phones['records']) {
					$phoneList = [];
					foreach ($phones['records'] as $k => $v) {
						$phoneList[$v['userId']] = $v['phone'];
					}
				}

				$list = [];
				foreach ($ret['records'] as $k => $v) {
					$list[] = [
						'name' => $phoneList[$v['userId']] ? substr_replace($phoneList[$v['userId']], '****', 3, 4) : '170****8888',
						'amount' => $v['amount'] / 100,
						'time' => date('Y-m-d H:i:s', strtotime($v['timeCreate'])),
					];
				}
				$view->assign('list', $list);//出现多个assign中的list字段重复，修改如下突出唯一识别
				$view->assign('GetReceiveListFromReglist', $list);
				$view->assign('hasList', 1);
				$view->assign('GetReceiveListFromRegOnehasList', 1);

				error_log('======END GetReceiveListFromReg LOG======');
				return;
			}
		} catch (\ErrorException $e) {
			var_log($e->getMessage(), 'e->message');
		}
		$view->assign('hasList', 0);
		$view->assign('GetReceiveListFromRegZerohasList', 1);
	}
}