<?php
namespace Prj\Misc;

/**
 * 落地页追踪分析用的日志
 *
 * @author wang.ning
 */
class LogOfLandingPage {
	public static function standTrace($logdata,$_request)
	{
		$logdata->sarg1 = 'phoneLen_'.$_request->get('a');//手机输入框
		$logdata->sarg2 = 'passLen_'.$_request->get('b');//密码输入框
		$logdata->sarg3 = 'codeLen_'.$_request->get('c');//短信验证码输入框
		
		$logdata->contractId = $_request->get('source');
		$logdata->target = 'device_'.$_request->get('channel');//前端类型
		$logdata->ext = 'lastinput_'.$_request->get('lt');
		$logdata->mainType = 'codeSend?'.$_request->get('sendcode');
		$logdata->subType = 'regSend?'.$_request->get('reged');
	}
}
