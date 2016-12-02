<?php
namespace Sooh\DB\Acl;
/**
 * 获取当前访问控制类（可以通过定义常量 SOOH_ACL_CLASS 来指定）
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class Acl {
	private static $_instance=null;
	/**
	 * @return \Sooh\DB\Acl\AclSession
	 */
	public static function getInstance()
	{
		if(self::$_instance===null){
			//error_log("defined(SOOH_ACL_CLASS)=".SOOH_ACL_CLASS);
			if(defined('SOOH_ACL_CLASS')){
				$classname = SOOH_ACL_CLASS;
				self::$_instance = new	$classname;
			}else{
				self::$_instance = new \Sooh\DB\Acl\AclSession();
			}
		}
		return self::$_instance;
	}
}
