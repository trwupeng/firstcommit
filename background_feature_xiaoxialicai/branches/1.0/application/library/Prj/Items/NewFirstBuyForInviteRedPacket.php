<?php

namespace Prj\Items;

/**
 * 邀请红包
 * @package Prj\Items
 */
class NewFirstBuyForInviteRedPacket extends Voucher
{
	/**
	 * @var int 给受邀人的红包额
	 */
	protected $redAmount;

	protected $setStatus;

	public function __construct(array $args)
	{
		$this->setStatus = \Prj\Consts\Voucher::status_freeze;
		if (isset($args['redAmount'])) {
			$this->redAmount = $args['redAmount'];
		}
	}

	public function descCreate()
	{
		return '邀请红包';
	}

	public function name()
	{
		return 'firstBuyInvite';
	}

	protected function ini_limit()
	{
		return ['minInvest' => 10000];
	}

	protected function ini_more()
	{
		return ['type' => \Prj\Consts\Voucher::type_real];
	}

	protected function ini_expire()
	{
		$remainingTime = (strtotime((\Sooh\Base\Time::getInstance()->YmdFull) . '235959') - \Sooh\Base\Time::getInstance()->timestamp());
		return ['end' => \Sooh\Base\Time::getInstance()->timestamp(2) + $remainingTime];
	}

	protected function ini_amount()
	{
		//受邀人首次投资获得的红包金额的一半
		return round($this->redAmount / 2);
//		$ruleStr = \Prj\Data\Config::get('ORDER_FIRST_RED_FULE');
//		if (empty($ruleStr)) {
//			throw new \ErrorException('无效的首购红包配置', 999);
//		} else {
//			if (!is_array($ruleStr)) {
//				$rule = json_decode($ruleStr, true);
//			} else {
//				$rule = $ruleStr;
//			}
//			krsort($rule);
//			//var_log($rule,'rule>>>>>>>>>>>>');
//			foreach ($rule as $k => $v) {
//				if ($this->buyAmount < $k) {
//					continue;
//				} else {
//					return $v[1];
//				}
//			}
//		}
	}

	/**
	 * 原有方法移植过来，未做任何修改
	 * @param $userId
	 * @param $ymd
	 * @return array
	 * @throws \ErrorException
	 * @throws \Exception
	 */
	public static function sendRebateRedPacket($userId, $ymd) {
		$errorMsg = "[error]激活邀请红包 userId:" . $userId . ' ymd:' . $ymd;
		$ret      = \Prj\Data\Vouchers::sendRebateRedPacket($userId, $ymd);
		if (!empty($ret['amountTotal'])) {
			$user = \Prj\Data\User::getCopy($userId);
			$user->load();
			if (!$user->exists()) {
				var_log($errorMsg . ' 用户不存在,回滚');
				\Prj\Data\Vouchers::rebateRedPacketRollBack($userId, $ymd);
				throw new \ErrorException($errorMsg . ' 用户不存在,回滚');
			} else {
				$user->setField('redPacket', $user->getField('redPacket') + $ret['amountTotal']);
				try {
					$user->update();
					try {
						//邀请人
						\Prj\ReadConf::run(
							[
								'event' => 'red_log_packet',
								'brand' => \Prj\Message\Message::MSG_BRAND,
								'num_packet' => 1,
								'private_gift' => sprintf('%.2f', $ret['amountTotal'] / 100),
								'num_deadline' => 48,
							],
							['phone' => $user->getField('phone'), 'userId' => $userId]
						);
					} catch(\Exception $e) {
						var_log($e->getMessage(), 'Send NewFirstBuyForInviteRedPacket::sendRebateRedPacket Message Error');
					}
				} catch (\ErrorException $e) {
					var_log($errorMsg . ' 更新红包账户失败,回滚');
					\Prj\Data\Vouchers::rebateRedPacketRollBack($userId, $ymd);
					throw new \ErrorException($errorMsg . ' 更新红包账户失败,回滚 ' . $e->getMessage());
				}
				return $ret;
			}
		} else {
			return [];
		}
	}
}