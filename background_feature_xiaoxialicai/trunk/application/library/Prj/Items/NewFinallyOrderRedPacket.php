<?php

namespace Prj\Items;

/**
 * 兜底活动红包
 * @package Prj\Items
 */
class NewFinallyOrderRedPacket extends Voucher
{
	protected $userId;

	public function __construct(array $args)
	{
		if (isset($args['userId'])) {
			$this->userId = $args['userId'];
		}
	}
    

	public function descCreate()
	{
		return '一锤定音活动奖励红包';
	}

	public function name()
	{
		return 'finallyOrder';
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
		//todo 初始化红包金额
        $rule = \Prj\Data\Config::get('FINALLY_RED_AMOUNT');
        if(empty($rule))throw new \ErrorException('无效的兜底红包规则');
        return $this->getRand($rule);
	}

    /**
     * 获取一个随机数
     * @param array $rule 随机规则:['1_100'=>99,'100_900'=>891,'900_1000'=>10]
     * @return int
     */
    protected function getRand($rule) {
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

        unset ($rule);

        $loc = strpos($result, '_');
        if ($loc) {
            $result = mt_rand(substr($result, 0, $loc), substr($result, $loc + 1));
        }
        return $result;
    }

}