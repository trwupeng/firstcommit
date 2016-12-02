<?php
namespace Sooh\DB\Acl;
/**
 * Description of AclManager
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class AclManager {
	/**
	 * 
	 * @param string $pwd
	 * @param string $salt
	 * @return string
	 */
	public function pwdEncode($pwd,$salt=null)
	{
		return md5($pwd);
	}
	
	/**
	 * 获取指定账号的数据库记录
	 * @param string $accountid 登入账号
	 * @param string $camefrom 账号来源
	 * @return array();
	 */
	public function getAccount($accountid,$camefrom='local')
	{
		return array('_acc_ount_'=>$accountid,'_fr_om_'=>$camefrom);
	}
	/**
	 * 添加账号
	 * @param array $fields
	 * @param string $AutoIncField 如果有自动递增字段的话,请指出
	 * @return boolean
	 */
	public function addAccount($fields,$AutoIncField=null)
	{
		return false && empty($fields) && empty($pkey);
	}
	/**
	 * 更新指定账号的数据库记录
	 * @param array $fields
	 * @param array $pkey
	 * @return boolean
	 */
	public function updAccount($fields,$pkey=null)
	{
		return false && empty($fields) && empty($pkey);
	}

	/**
	 * 获取符合条件的账号的列表
	 * @return array
	 */
	public function getAccouts($where,$pagesize=20,$rsFrom=0)
	{
		return array();
	}
	/**
	 * 获取符合条件的账号的总数
	 * @return array
	 */
	public function getAccoutsCoount($where)
	{
		return array();
	}	
}
