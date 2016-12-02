<?php
namespace Sooh\DB\Acl;
/**
 * 访问控制类
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class AclSession {
	const field_loginid='SOOH_loginId';
	/**
	 * 枚举菜单列表
	 * @return array array('manage.managers'=>'系统管理.管理员管理',)
	 */
	public function enumRights()
	{
		return array();
	}
	/**
	 * 获取当前用户有权限的菜单列表
	 * @return \Sooh\DB\Acl\Menu
	 */
	public function menu()
	{
		return Null;
	}
	/**
	 * 查询是否有相应的权限,出于简化够用考虑,目前只用了前两个参数
	 * @param type $module
	 * @param type $ctrl
	 * @param type $action
	 * @param type $res
	 * @param type $opcode
	 * @return boolean
	 */
	public function hasRightsTo($module,$ctrl,$action='ignore',$res=null,$opcode=0)
	{
		return true;
	}
	protected $now = 0;
	protected function init()
	{
		if(empty($_SESSION))session_start ();
		$this->now = \Sooh\Base\Time::getInstance()->timestamp();
	}
	/**
	 * 登入检查：密码，错误次数
	 * @param array $record
	 * @param string $password
	 * @throws ErrorException when password error or too manay retry etc.
	 * @return string identifier for session
	 */
	public function pwdChk($record,$password){	throw new \ErrorException("todo of _login($record,$password)");}
	/**
	 * 
	 * @param string $username
	 * @param string $password
	 * @param int $expired
	 * @param string $cameFrom
	 * @throws ErrorException when password error or too manay retry etc.
	 * @return \Sooh\DB\Acl\AclSession
	 */
	public function login($username,$password,$expired=0,$cameFrom='local')
	{
		$this->init();
		$aclManager = $this->getAclManager();
		$record = $aclManager->getAccount($username,$cameFrom);
		$id = $this->pwdChk($record, $aclManager->pwdEncode($password,$record['salt']));
		$_SESSION[self::field_loginid]=$id;
		if($expired>0)$_SESSION['U_expire']= $this->now+$expired;
		return $this;
	}
	public function getLoginId()
	{
		return $_SESSION[self::field_loginid];
	}
	public function logout()
	{
		session_start();
		session_unset(self::field_loginid);
		session_destroy();
	}
	/**
	 * 当需要登入时做什么（重定向或怎么着，根据具体的框架实现）
	 * @param string $urlReturn 登入成功后返回的地址
	 */
	public function onNeedsLogin($urlReturn)
	{
		throw new \Exception('action of login not defined: '.$urlReturn);
	}
	/**
	 * 当需要登入时做什么（重定向或怎么着，根据具体的框架实现）
	 * @param string $rightsId 所需的权限
	 */
	public function onNeedsRights($rightsId)
	{
		throw new \Exception('action of rights not defined: '.$rightsId);
	}	
	public function setSessionVal($k,$val)
	{
		$this->init();
		$_SESSION[$k]=$val;
		return $this;
	}
	public function getSessionVal($k)
	{
		$this->init();
		return isset($_SESSION[$k])?$_SESSION[$k]:null;
	}
	
	/**
	 * 获取帐号权限管理类
	 * @return \Sooh\DB\Acl\AclManager
	 */
	public function getAclManager()
	{
		error_log('getAclManager form acl-baseclass:');
		return null;
	}

	
	/**
	 * @return boolean
	 */
	public function isLogined()
	{
		$this->init();
		if(!empty($_SESSION[self::field_loginid])){
			if(isset($_SESSION['U_expire']) && $_SESSION['U_expire']>$this->now)
				return true;
		}
		return false;
	}
	/**
	 * @return string
	 */
	public function urlLogin()
	{
		return \Sooh\Base\Tools::uri(null,'login','index');
	}
}
