<?php
include_once realpath(__DIR__ . '/../../../conf') . '/inc4tests.php';
class _login extends \Sooh\Base\Tests\ApiHttpGetJson{
	/**
	 * 当前测试的使用的登入用户名和密码
	 * @return array ['user','password'];
	 */
	protected function getNameAndPassword(){	return ['17717555734','qq123456'];}
	protected $arrCookies = [];
	public function setUp() {
		parent::setUp();
		$this->initSetMC();
		$this->onSetUP_login();
	}

	protected function onSetUP_login()
	{
		//每次登入
		$this->arrCookies['SoohSessId']='test:1234567890';
		list($phone,$pwd) = $this->getNameAndPassword();
		$clientId = '1104878344';
		$clientSecret = 's20vH9emKJ6BmT1Q';
		$redirectUri = 'https://www.baidu.com/';
		$jsonstr = $this->jsonstrByHttpGet($this->getUrl('oauth/applogin',[
														'phone'=>$phone,
														'password'=>$pwd,
														'clientId'=>$clientId,
														'clientSecret'=>$clientSecret,
														'redirectUri'=>$redirectUri]));
		$r = json_decode($jsonstr,true);
		//{"code":200,"info":{"code":"*****","redirectUri":"*****","accountId":"accountId"},"msg":"成功"}
		//{"code":400,"msg":"error"}
		if($r['code']==200){
			$code = $r['info']['code'];
		}else{
			throw new \ErrorException('login failed 1:'.$this->outstring($jsonstr));
		}
		$jsonstr = $this->jsonstrByHttpGet($this->getUrl('passport/login', ['code'=>$code,'redirectUri'=>$redirectUri,'clientType'=>901]));
		//{"code":200,"info":{"accountId":"****","nickname":"*****"},"msg":"成功"}
		//{"code":400,"msg":"帐号不存在"}
		$r = json_decode($jsonstr,true);
		if($r['code']==200){
			
		}else{
			throw new \ErrorException('login failed 2:'.$this->outstring($jsonstr));
		}
	}
	protected function outstring($strOrArr){
		if(is_string($strOrArr)){
			$r = json_decode($strOrArr,true);
			if($r){
				$s = '';
				foreach($r as $k=>$v){
					$s.= "【{$k}: {$v}】";
				}
				return $s;
			}else{
				return $strOrArr;
			}
		}else{
			$s = '';
			foreach($strOrArr as $k=>$v){
				$s.= "【{$k}: {$v}】";
			}
			return $s;
		}
	}
	protected function getUrl($mca,$args){return trim(\Sooh\Base\Ini::getInstance()->get('uriBase.www'),'/').'/'.trim($mca).'?'.http_build_query($args).'&__VIEW__=json';}
	/**
	 * @return \Sooh\DB\Interfaces\All
	 */
	protected function db_p2p(){return \Sooh\DB\Broker::getInstance();}
	public function tearDown() {
		parent::tearDown();
	}
}
