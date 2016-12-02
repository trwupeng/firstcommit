<?php
include_once realpath( __DIR__.'/../_inc').'/_orders.php';;

/**
 * 模板文件
 * 其他附加说明：
 * 订单流程中需要的uniqueOpId和autoid，getXXXXXArg的函数定义中，value使用$this->lastUniqueOpId和$this->lastOrderId即可
 *
 * @author wang.ning
 */
class addstdTest extends \_orders {
	//------------------------炸
	//有没有相关的炸弹要检查
	protected $bombsForError=[
		//'bombId1'=>['error_should_found'], //该炸弹触发后应该给出的错误信息
		//'bombId2'=>'onBomb_bombId2'        //该炸弹触发后检查的函数($jsonstr,$errmsgDefinedWhenCallApiChk)
	];
	//protected function onBomb_bombId2($strJson,$errDescPrefix)	{$this->assertContains('$cmp',$strJson,'error-tip');}
	
	//如果不使用 _inc/login.php里设置的默认的帐号密码，可以在这里指定新的
	//protected function getNameAndPassword(){	return ['17717555734','qq123456'];}
	
	//测试的哪个接口 : 比如测试passport/login ： passport 是目录名，这个函数里面设置成login
	protected function initSetMC($act='add'){parent::initSetMC(__DIR__.'/'.$act);}//说明实际测试的是哪个action

	//本测试使用的正确的接口参数
	protected function getCorrectArgs()
	{
		return ['waresId'=>'1458964467467471',
			'inviteCode'=>'',
			'amount'=>10000,
			'voucherId'=>'',
			'paypwd'=>'123456',
			'orderId'=>$this->lastOrderId,
			'cmd'=>'buypaypwd',
			'smscode'=>'',//保留未用
			'clientType'=>'901',
			'uniqueOpId'=>$this->lastUniqueOpId,
			];
		
	}
	//注释掉下面一行开启正确参数的测试
	//public function testCorrect(){}
	//单项错误测试的时候，单项调整参数的情况
	protected function getWrongArgs()
	{
		return array(
			//参数名			参数值         返回中应该包含的字符串    unittest报错的提示
			['waresId',		'wrong_waresId',	'"msg":"no_wares"',	'对 不存在的waresId 的验证失败了'],
			['inviteCode',	'wrong_inviteCode',	'"msg":"error_code"',	'对 不存在的邀请码 的验证失败了'],
			['amount',		'1',				'"msg":"amount_change0"',	'对 低于起投金额 的验证失败了'],
			['amount',		'1000000000',		'"msg":"order_syn"',	'对 中途改金额 的验证失败了'],
			['voucherId',	'wrong_voucherId',	'"msg":"void_voucherId"',	'对 券有效性 的验证失败了'],
			['paypwd',		'wrong_paypwd',		'"msg":"error_paypwd"',	'对 错误支付密码 的验证失败了'],
			['orderId',		'wrong_orderId',	'"msg":"order_syn"',	'对 错误订单号 的验证失败了'],
			['orderId',		'',					'"msg":"no_orderId"',	'对 空订单号 的验证失败了'],
			['cmd',			'wrong_cmd',		'"msg":"unknown_cmd"',	'对 错误命令代码 的验证失败了'],
			['uniqueOpId',	'wrong_uniqueOpId',	'"msg":"page_expired"',	'对 错误OPCODE 的验证失败了'],
		);
	}	
	//用正确的参数提交请求后，在返回的json字符串里检查的字符串列表
	protected function chkOnCorrect()
	{
		return ['"code":200','"orderStatus":2'];
	}
	//参数都正确的时候，如果发生错误，提示的信息文字是什么
	protected function errMsgOnCorrect(){	return '正确参数也报错了？？';}

}
