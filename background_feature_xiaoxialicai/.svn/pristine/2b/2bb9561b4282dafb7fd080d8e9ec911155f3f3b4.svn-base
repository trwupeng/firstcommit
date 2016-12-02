<?php
namespace Prj\Items;
/**
 * Description of ShopPoint
 *
 * @author simon.wang
 */
class ShopPoint implements \Lib\Interfaces\Item {
	public function realGived(){
		$s = get_called_class();
		$r = explode('\\', $s);
		return [
			[array_pop($r),$this->getAmount()]
		];
	}
	protected $pointRule = ['1_10'=>1000,'10_20'=>8000,'20_30'=>1000];
	protected $pointLast = [];

	/**
	 * 发放时的参数
	 * @return array [day-expired, amount, limits, type]
	 */
	protected function iniForGiven() {
		return [30, $this->getRand($this->pointRule), '', 0];
	}

	/**
	 * 获取一个随机数
	 * @param array $rule 随机规则:['1_100'=>99,'100_900'=>891,'900_1000'=>10]
	 * @return int
	 */
	protected function getRand($rule) {
		$result = '';
		$ruleSum = array_sum($rule);

		//概率数组循环
		foreach ($rule as $key => $val) {
			$randNum = mt_rand(1, $ruleSum);
			if ($randNum <= $val) {
				$result = $key;
				break;
			} else {
				$ruleSum -= $val;
			}
		}
		unset ($rule);

		$loc = strpos($result, '_');
		if ($loc) {
			$result = mt_rand(substr($result, 0, $loc), substr($result, $loc + 1));
		}
		return $result;
	}

	public function getAmountLast() {
		return $this->pointLast;
	}

	private function tryAddTally($userId, $cur, $num)
	{
		for ($retry = 0; $retry < 10; $retry++) {
			$tally = \Prj\Data\ShopPoints::addTally($userId, $cur, $num, 0, 0);
			if ($tally === null) {
				continue;
			} else {
				try {
					\Sooh\DB\Broker::errorMarkSkip(\Sooh\DB\Error::duplicateKey);
					$tally->update();
					return $tally;
				} catch (\ErrorException $e) {
					if (\Sooh\DB\Broker::errorIs($e, \Sooh\DB\Error::duplicateKey)) {
						continue;
					} else {
						return null;
					}
				}
			}
		}
		return null;
	}
	/**
	 * 发放道具
	 * @param \Prj\Data\User $user
	 * @param int $num
	 * @return string 错误说明，没错误返回空串
	 */
	public function give_prepare($user,$num=1)
	{
		$cur = $user->getField('points');
		$userId = $user->userId;
		list($expire, $amount, $limits, $type) = $this->iniForGiven();
		$tally = $this->tryAddTally($userId, $cur, $amount);
		if($tally===null){
			return 'add_shoppoints_tally_failed';
		}else{
			$this->tallyLast=$tally;
		}
		$this->numLast=$amount;
		$this->pointLast[] = $amount;
		$user->setField('points', $cur+$amount);
		return '';
	}
	private $numLast;
	/**
	 *
	 * @var \Prj\Data\ShopPoints
	 */
	private $tallyLast;
	/**
	 * 碰上错误，回滚发放行为
	 * @param \Prj\Data\User $user
	 * @return string 错误说明，没错误返回空串
	 */
	public function give_rollback($user)
	{
		$user->setField('points', $user->getField('points')-$this->numLast);
		try{
			$this->tallyLast->setField('statusCode', \Prj\Consts\Tally::status_abandon);
			$this->tallyLast->update();
		} catch (\Exception $ex) {
			\Sooh\Base\Log\Data::getInstance()->error('[rollback ShopPoint tally failed id:'.current($this->tallyLast->getPKey()).' of user '.$user->userId.' ]'.$ex->getMessage());
			return $ex->getMessage();
		}
		return '';
	}
	/**
	 * 正常结束，确认发放
	 * （要求内部捕获错误，写报警日志,不要抛出异常，外部调用时，不额外处理错误细节）
	 * @param \Prj\Data\User $user
	 * @return string 错误说明，没错误返回空串
	 */
	public function give_confirm($user)
	{
		return '';
	}
	/**
	 * 使用道具
	 * @param array $args
	 * @param int $num
	 * @return string 错误说明，没错误返回空串
	 */
	public function useit($args,$num)
	{
		$cur = $args['user']->getField('points');
		if($cur<$num){
			return \Sooh\Base\ErrException::msgNotEnough;
		}
		$userId=  current($args['user']->userId);
		$tally = $this->tryAddTally($userId, $cur, -$num);
		if($tally===null){
			return 'add_shoppoints_tally_failed';
		}else{
			$this->tallyLast=$tally;
		}
		$this->numLast=$num;
		$args['user']->setField('points', $cur-$num);
		return '';
	}
	/**
	 * 碰上错误，回滚使用行为
	 * @param array $args
	 */
	public function rollbackUse($args)
	{
		$args['user']->setField('points', $args['user']->getField('points')+$this->numLast);
		try{
			$this->tallyLast->setField('statusCode', \Prj\Consts\Tally::status_abandon);
			$this->tallyLast->update();
		} catch (\Exception $ex) {
			\Sooh\Base\Log\Data::getInstance()->error('[rollback ShopPoint tally failed id:'.current($this->tallyLast->getPKey()).' of user '.$args['user']->userId.' ]'.$ex->getMessage());
		}
		return '';
	}
	/**
	 * 剩余数量
	 * @param \Prj\Data\User $user
	 * @return int
	 */
	public function numLeft($user)
	{
		return $user->getField('points');
	}
}
