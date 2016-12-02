<?php

/**
 * Class ContractController
 * @author LTM <605415184@qq.com>
 */
class ContractController extends \Prj\BaseCtrl {

	/**
	 * 渠道1
	 */
	public function channel1Action() {
		$shortId = $this->_request->get('shortId');
		if (\Lib\Misc\InputValidation::validateParams(['shortId' => $shortId], ['shortId' => ['#^[a-zA-Z]{5,7}$#', 'server busy']]) === false) {
			return $this->returnError(\Lib\Misc\InputValidation::$errorMsg);
		}

		$contractId = \Prj\Data\ShortenedUrl::getContractId($shortId);

		$this->_view->assign('contractId', $contractId);
		return $this->returnOK('success');
	}

	/**
	 * 获取M版注册需要的参数-可以废弃的
	 */
	public function getParamsAction() {
		$clientId     = base64_encode('1104878344' . 'clientId');
		$clientSecret = base64_encode('s20vH9emKJ6BmT1Q' . 'clientSecret');

		$data = [
			'clientId'     => $clientId,
			'clientSecret' => $clientSecret,
		];

		$this->_view->assign('data', $data);
		return $this->returnOK('success');
	}

	/**
	 * 获取一个随机值
	 * @param array $rule 随机规则:['1_100'=>99,'100_900'=>891,'900_1000'=>10]
	 * @return int
	 */
	private function getRand($rule) {
		$result  = '';
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
}