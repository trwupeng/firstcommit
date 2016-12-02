<?php

namespace Prj\Items;

/**
 * 首购红包
 * @package Prj\Items
 */
class NewFirstBuyRedPacket extends Voucher
{
	protected $buyAmount;
	protected $noInvite;

	public function __construct(array $args = [])
	{
		if (isset($args['amount'])) {
			$this->buyAmount = $args['amount'];
		}
		if (isset($args['noInvite'])) {
			$this->noInvite = $args['noInvite'];
		}
	}

	public function descCreate()
	{
		return '首购红包';
	}

	public function name()
	{
		return 'firstBuy';
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
		return ['end' => \Sooh\Base\Time::getInstance()->timestamp(2)];
	}

	protected function ini_amount()
	{
//		/**
//		 * @var array 红包配置<br />
//		 *            例如：
//		 *            [100,100]<br />
//		 *            [100, [100 => 100, 200000 => 2000]]
//		 */
//		$ruleStr = \Prj\Data\Config::get('ORDER_FIRST_RED_FULE_NO_INVITE');
//		if (empty($ruleStr)) {
//			throw new \ErrorException('无效的普通用户首购红包配置', 999);
//		} else {
//			if (!is_array($ruleStr)) {
//				$rule = json_decode($ruleStr, true);
//			} else {
//				$rule = $ruleStr;
//			}
//
//			if ($this->noInvite) {
//				return $rule[0];
//			} else {
//				$amountConf = $rule[1];
//				if (is_array($amountConf)) {
//					//分阶段（按投资额）奖励
//					krsort($amountConf);
//					foreach ($amountConf as $k => $v) {
//						if ($this->buyAmount < $k) {
//							continue;
//						} else {
//							return $v;
//						}
//					}
//				} else {
//					return $amountConf;
//				}
//			}
//		}

		if ($this->noInvite) {
			$ruleStr = \Prj\Data\Config::get('ORDER_FIRST_RED_FULE_NO_INVITE');
			if (empty($ruleStr)) {
				throw new \ErrorException('无效的普通用户首购红包配置', 999);
			} else {
				$rule = $ruleStr;
				if (is_numeric($rule)) {
					return intval($rule);
				}

				if (is_string($ruleStr)) {
					if (json_decode($rule, true) && json_last_error() === JSON_ERROR_NONE) {
						$rule = json_decode($rule, true);
					}
				}
				if (is_string($rule)) {
					return $rule;
				} else {
					krsort($rule);
					foreach ($rule as $k => $v) {
						if ($this->buyAmount < $k) {
							continue;
						} else {
							return $v;
						}
					}
				}
			}
		} else {
			$ruleStr = \Prj\Data\Config::get('ORDER_FIRST_RED_FULE');
			if (empty($ruleStr)) {
				throw new \ErrorException('无效的首购红包配置', 999);
			} else {
				$rule = $ruleStr;
				if (is_numeric($rule)) {
					return intval($rule);
				}

				if (is_string($ruleStr)) {
					if (json_decode($rule, true) && json_last_error() === JSON_ERROR_NONE) {
						$rule = json_decode($rule, true);
					}
				}
				if (is_string($rule)) {
					return $rule;
				} else {
					krsort($rule);
					foreach ($rule as $k => $v) {
						if ($this->buyAmount < $k) {
							continue;
						} else {
							return $v;
						}
					}
				}
			}
		}
	}

	/**
	 * 解析红包金额配置，获取红包金额<br />
	 * $conf支持的写法：<br />
	 *      '100'
	 *      ['0,100' => 100, '100,200000' => 500, '200000,0' => 1000]
	 * @param mixed $conf 红包金额配置
	 * @param int $amount 投资额（包含红包金额）
	 * @return int
	 */
	private function parseConfigForAmount($conf, $amount)
	{
		if (is_array($conf)) {
			foreach ($conf as $k => $v) {
				if (strpos($k, ',') !== false) {
					list($min, $max) = explode(',', $k);
					if ($min == 0) {
						if ($amount < $max) {
							return $v;
						}
					} else if ($max == 0) {
						if ($amount >= $min) {
							return $v;
						}
					} else {
						if ($amount >= $min && $amount < $max) {
							return $v;
						}
					}
				}
			}
		} else {
			return $conf;
		}
	}
}