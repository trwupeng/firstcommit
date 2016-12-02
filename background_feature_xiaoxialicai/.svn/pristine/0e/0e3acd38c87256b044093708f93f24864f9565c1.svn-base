<?php
namespace Prj\Items;

/**
 * 道具类基类
 *
 * @author wang.ning
 */
abstract class Base {
	/**
	 * @var array [<br />
	 * [bonus名称, 金额、数量，到期时间时间戳，券ID]，<br />
	 * [bonus名称, 金额、数量，到期时间时间戳，券ID]，<br />
	 * [bonus名称, 金额、数量，到期时间时间戳，券ID]<br />
	 * ]
	 */
	protected $dispatchedReal=[];

	/**
	 * 获得名称
	 */
	abstract public function name();
	/**
	 * 发放时：每份的数量，可以是数字，可以是权重数组
	 * @return mixed
	 */
	protected function ini_amount() {
		return 1;
	}
	/**
	 * 发放时：有效期设置
	 * @return mixed
	 */
	protected function ini_expire() {
		return null;
	}
	/**
	 * 发放时：道具使用限制
	 * @return mixed
	 */
	protected function ini_limit() {
		return null;
	}
	/**
	 * 发放时：其他设置
	 * @return mixed
	 */
	protected function ini_more() {
		return null;
	}
	/**
	 * 根据权重数组
	 * @param array $rule 随机规则:['1_100'=>99,'100_900'=>891,'900_1000'=>10]
	 * @return int
	 */
	protected function realAmoundByWeight($rule) {
		if (count($rule) == 1) {
			$result = key($rule);
		} else {
			$result = '';
			$ruleSum = array_sum($rule);

			//概率数组循环
			$vSum = 0;
			$randNum = mt_rand(1, $ruleSum);
			foreach ($rule as $k => $v) {
				$vSum += $v;
				if ($randNum <= $vSum) {
					$result = $k;
					break;
				}
			}
		}

		$loc = strpos($result, '_');
		if ($loc) {
			$result = mt_rand(substr($result, 0, $loc), substr($result, $loc + 1));
		}
		return $result;
	}

	/**
	 * 保存bonus列表
	 * @param string $itemName itemName，bonus名称
	 * @param int $itemNum 金额，数量
	 * @param int $expire 到期时间，时间戳
	 * @param string $voucherId voucherId，券ID
	 */
	protected function addGived($itemName, $itemNum = 0, $expire = 0, $voucherId = '') {

		if (is_array($expire)) {
			$expired = $expire['end'];
		} else {
			$expired = strtotime(date('Ymd', \Sooh\Base\Time::getInstance()->timestamp($expire)) . '235959');
		}
		$this->dispatchedReal[] = [$itemName, $itemNum, $expired, $voucherId];
	}
	/**
	 * 获取真实发放的道具列表
	 * @return array [<br />
	 * [bonus名称, 金额、数量，到期时间时间戳，券ID]，<br />
	 * [bonus名称, 金额、数量，到期时间时间戳，券ID]，<br />
	 * [bonus名称, 金额、数量，到期时间时间戳，券ID]<br />
	 * ]
	 */
	public function realGived()
	{
		return $this->dispatchedReal;
	}
}
