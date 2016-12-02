<?php

namespace Prj\Items;

/**
 * 返利红包
 * Class RedPacketForRebate
 * @package Prj\Items
 * @author  LTM <605415184@qq.com>
 */
class RedPacketForRebate extends RedPacket {
	static $bonusName = 'RedPacketForRebate';

	const errAccountNotExist = '用户不存在';

	const errSendError = '发送返利失败';

	/**
	 * 发放返利红包
	 * @param string  $userId 用户ID
	 * @param integer $amount 净投资额
	 * @param integer $days   投资天数
	 * @return array
	 * @throws \ErrorException
	 * @throws \Sooh\Base\ErrException
	 */
	public function give($userId, $amount, $days) {
		//获取邀请人
		$inviteTree = \Prj\Data\InviteCode::find('', $userId);
		if (empty($inviteTree['parent'])) {
			return null;
		} else {
			$parentId = $inviteTree['parent'];
		}

		$parent = \Prj\Data\User::getCopy($parentId);
		$parent->load();
		if ($parent->exists() === false) {
			throw new \Sooh\Base\ErrException(self::errAccountNotExist);
		}
		$ret = $this->give_prepare($parent, $amount, $days, $userId);
		if ($ret !== '') {
			$this->give_rollback($parent);
			throw new \Sooh\Base\ErrException(self::errSendError);
		} else {
			$this->give_confirm($parent);
			return ['type' => self::$bonusName, 'amount' => $this->getAmountLast()[0]];
		}
	}

	/**
	 * 准备发放红包
	 * @param \Prj\Data\User $user 用户对象
	 * @param integer        $num  净投资额
	 * @param integer        $days 投资天数
	 * @return string
	 * @throws \ErrorException
	 */
	public function give_prepare($parent, $num = 1, $days = 30, $userId = '') {
		$loger     = \Sooh\Base\Log\Data::getInstance();
		$cur       = $parent->getField('redPacket');
		$curRebate = $parent->getField('rebate');
		$parentId  = $parent->userId;
		list($expire, $amount, $limits, $type) = $this->iniForGiven($num, $days);
		$tmp = $this->newVoucher($parentId, $type, $amount, $expire, $userId);
		if ($tmp != null) {
			try {
				$tmp->update();
				$this->vouchersLast[] = $tmp;
				$this->amountLast[]   = $amount;
			} catch (\Exception $e) {
				$loger->error("error on give voucher to user:{$parentId} on " . \Prj\Misc\OrdersVar::$introForCoder . " : " . $e->getMessage());
				return $e->getMessage();
			}
		}

		$parent->setField('redPacket', $cur + array_sum($this->amountLast));
		$parent->setField('rebate', $curRebate + array_sum($this->amountLast));
		$parent->update();

		return '';
	}

	/**
	 * 创建一条新的券
	 * @param string  $parentId 邀请人ID
	 * @param integer $type     券类型
	 * @param integer $amount   金额
	 * @param integer $expired  有效期
	 * @param string  $userId   被邀请人ID
	 * @return null|\Prj\Data\Vouchers
	 * @throws \ErrorException
	 * @throws \Exception
	 */
	private function newVoucher($parentId, $type, $amount, $expired, $userId) {
		$ordersIdBase = substr($parentId, -4);
		$dt           = \Sooh\Base\Time::getInstance();
		for ($retry = 0; $retry < 10; $retry++) {
			list($sec, $ms) = explode('.', microtime(true));
			$ordersId = \Prj\Consts\OrderType::vouchers . $sec . substr($ms, 0, 3) . $ordersIdBase;
			$tmp      = \Prj\Data\Vouchers::getCopy($ordersId);
			$tmp->load();
			if (!$tmp->exists()) {
				$counts = $tmp->db()->getRecordCount($tmp->tbname(), [
					'userId'      => $parentId,
					'voucherType' => $type,
					'descCreate'  => $userId
				]);
				$user   = \Prj\Data\User::getCopy($userId);
				$user->load();
				if ($user->exists() === false) {
					$nickname = '';
					$zhName   = '';
				} else {
					$phone    = $user->getField('phone');
					$name     = $user->getField('nickname');
					$idCard   = $user->getField('idCard');
					$nickname = substr($phone, 0, 4) . '****' . substr($phone, -3);
					$zhName   = $this->msubstr($name, 0, 1) . ((strlen($idCard) == 15 ? substr($idCard, -1) : substr($idCard, -2, 1)) % 2 ? '先生' : '女士');
				}

				$tmp->setField('userId', $parentId);
				$tmp->setField('voucherType', $type);
				$tmp->setField('amount', $amount);
				$tmp->setField('timeCreate', $dt->ymdhis());
				$tmp->setField('dtExpired', date('Ymd', $dt->timestamp($expired)) . '235959');
				$tmp->setField('dtUsed', 0);
				$tmp->setField('orderId', 0);
				$tmp->setField('descCreate', $userId);//被邀请人的ID
				$tmp->setField('codeCreate', [
					'counts'   => $counts + 1,
					'nickname' => $nickname,
					'zhName'   => $zhName
				]);//其他参数
				$tmp->setField('exp1', \Prj\Misc\OrdersVar::$explain1);
				$tmp->setField('exp2', \Prj\Misc\OrdersVar::$explain2);
				$tmp->setField('statusCode', \Prj\Consts\Voucher::status_unuse);  //发一张可用的券
				$tmp->update();
				return $tmp;
			}
			\Prj\Data\Vouchers::freeAll($tmp->getPKey());
		}
		return null;
	}


	/**
	 * 计算返利金额
	 * @param integer $amount 净投资额
	 * @param integer $days   投资天数
	 * @return float
	 */
	protected function getAmount($amount, $days) {
		$ret = $amount * $days;
		if ($ret < 360000) {
			return 0;
		} else {
			return floor($ret / 360000);
		}
	}

	protected function iniForGiven($amount, $days) {
		if ($this->registerOn) {
			return [2, $this->getAmount($amount, $days), '', \Prj\Consts\Voucher::type_rebate];
		}
		throw new \Sooh\Base\ErrException('server busy');
	}

	/**
	 * 字符串截取，支持中文和其他编码
	 * @static
	 * @access public
	 * @param string $str     需要转换的字符串
	 * @param string $start   开始位置
	 * @param string $length  截取长度
	 * @param string $charset 编码格式
	 * @param string $suffix  截断显示字符
	 * @return string
	 */
	private function msubstr($str, $start = 0, $length, $charset = "utf-8", $suffix = false) {
		if (function_exists("mb_substr")) {
			$slice = mb_substr($str, $start, $length, $charset);
		} elseif (function_exists('iconv_substr')) {
			$slice = iconv_substr($str, $start, $length, $charset);
			if (false === $slice) {
				$slice = '';
			}
		} else {
			$re['utf-8']  = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
			$re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
			$re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
			$re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
			preg_match_all($re[$charset], $str, $match);
			$slice = join("", array_slice($match[0], $start, $length));
		}
		return $suffix ? $slice . '...' : $slice;
	}
}