<?php

//include_once realpath(__DIR__ . '/../_inc') . '/_login.php';
include_once __DIR__.'/_login.php';
/**
 * Description of base
 *
 * @author wang.ning
 */
class _one extends \_login{
	const wrongArg_key=0;
	const wrongArg_val=1;
	const wrongArg_find=2;
	const wrongArg_desc=3;
	protected function getCorrectArgs()
	{
		return ['arg'=>'none'];
	}
	protected function getWrongArgs()
	{
		return array(
			//参数名		参数值         返回中应该包含的字符串    unittest报错的提示
			['wareId',	'wrong_waresId',	'"wareId":"wrong_waresId"',	'对 不存在的waresId 的验证失败了'],
			['arg',		'closed_waresId',	'"wareId":"wrong_waresId"',	'对 已关闭的waresId 的验证失败了'],	
		);
	}
	protected function errMsgOnCorrect()
	{
		return '';
	}
	protected function chkOnCorrect()
	{
		return [];
	}
	public function testCorrect()
	{
		$args = $this->getCorrectArgs();
		$mca = $this->moduleController;
		$this->apiChk($this->getUrl($mca, $args),$this->chkOnCorrect(),$this->errMsgOnCorrect());
	}
	public function testWrong()
	{
		$mca = $this->moduleController;
		$wrongs = $this->getWrongArgs();
		foreach($wrongs as $r){
			$args = $this->getCorrectArgs();
			$args[ $r[self::wrongArg_key] ] = $r[self::wrongArg_val];
			$this->apiChk($this->getUrl($mca, $args),[$r[self::wrongArg_find]],$r[self::wrongArg_desc]);
		}
	}
	protected function resetAfterEachApiChk()	{}
}
