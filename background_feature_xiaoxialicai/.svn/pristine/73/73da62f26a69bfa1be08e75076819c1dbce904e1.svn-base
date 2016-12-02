<?php

/**
 * 用户扩展功能接口
 * @author simon.wang
 */
class UserextController extends \Prj\UserCtrl
{
	/**
	 * 获取用户对应的阶段奖励
	 * //返回客户端旧的标志信息
     * @output {code: 200,msg:"success",data:{"registerRedPackage": "100", "bindCardRedPackage": "120", "rechargeRedPackage": "1000", "invesRedPackage": "800"}}
     * //返回客户端新的标志信息
     * @output {code: 200,msg:"success",datagetguidestep:{"registerRedPackage": "100", "bindCardRedPackage": "120", "rechargeRedPackage": "1000", "invesRedPackage": "800"}}
	 */
	public function getGuideStepAction()
	{
		$this->_view->assign('data',[
			"registerRedPackage"=>rand(0,10)>5?100:110,
			"bindCardRedPackage"=>rand(0,10)>5?200:210,
			"rechargeRedPackage"=>rand(0,10)>5?300:310,
			"invesRedPackage"=>rand(0,10)>5?400:410,
		]);//出现多个assign中的data字段重复，修改如下突出唯一识别
		
		$this->_view->assign('datagetguidestep',[
		    "registerRedPackage"=>rand(0,10)>5?100:110,
		    "bindCardRedPackage"=>rand(0,10)>5?200:210,
		    "rechargeRedPackage"=>rand(0,10)>5?300:310,
		    "invesRedPackage"=>rand(0,10)>5?400:410,
		]);
		$this->returnOK('success');
	}
	/**
	 * 获取用户安全级别数据(银行卡要脱敏)
	 * //返回客户端旧的标志信息
     * @output {code: 200,msg:"success",data:{"isSetTradPwd": "1", "cardList":[{ID:"xxxxx",bankId:"cbc","bankCard":"************3863976"}]}}
     * //返回客户端新的标志信息
     * @output {code: 200,msg:"success",datagetsafelevel:{"isSetTradPwd": "1", "cardList":[{ID:"xxxxx",bankId:"cbc","bankCard":"************3863976"}]}}
	 */
	public function getSafeLevelAction()
	{
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $cardList = \Prj\Data\BankCard::loopAll(['userId' => $this->user->userId, 'statusCode' => \Prj\Consts\BankCard::enabled]);
        $this->user->load();
        $tradePwd = $this->user->getField('tradePwd');
        $isSetTradPwd = ($tradePwd) ? 1 : 0;
		$this->_view->assign('data',[
			"cardList"=>$cardList,
			"isSetTradPwd"=> $isSetTradPwd,
		]);//出现多个assign中的data字段重复(user/msgcounts，userext/getsafelevel，user/info)，修改如下突出唯一识别
		
		$this->_view->assign('datagetsafelevel',[
		    "cardList"=>$cardList,
		    "isSetTradPwd"=> $isSetTradPwd,
		]);
		$this->returnOK('success');
	}
}