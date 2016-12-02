<?php
namespace Prj;
/**
 * Description of SMS
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class SMS {
	public static $formats= array(
		'register'=> '您正在注册小虾理财,验证码为{code},请在15分钟内填写,如非本人操作,请忽略此短信。',
	    'regSuccess' => '恭喜您成功注册小虾理财,如您在操作过程中有任何疑问,可拨打电话400-8888-8888,祝您生活愉快!',
	    'rechargeOK' => '恭喜您成功充值{code}元!如需详情请在平台内查看',
//		'register'=>'注册账号的验证码为：{code}',
		'resetPwd' => '您正在修改小虾理财平台的登录密码，此次验证码为{code}，请在15分钟内填写,如非本人操作,请忽略此短信',
		'resetTradePwd' => '您正在重置交易密码，短信验证码为：{code}',
		'redPacket' => '您收到了{num_packet}个红包,共{num_money}元,有效期为{num_deadline}天,可用于p2p投资和网信钱包消费,请您尽快使用。',
	);
}
