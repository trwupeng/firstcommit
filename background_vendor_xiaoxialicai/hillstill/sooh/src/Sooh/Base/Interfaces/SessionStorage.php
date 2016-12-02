<?php
namespace Sooh\Base\Interfaces;
interface SessionStorage
{
	/**
	 * @param string $sessionId
	 * @param array $arrConfig
	 * @return \Sooh\Base\Interfaces\SessionStorage
	 */
	public static function getInstance($sessionId,$arrConfig);
	/**
	 * 获取sessionData
	 */
	public function getSessionData();
	/**
	 * 设置设置会话数据
	 * @param string $strValue
	 * @param int $dtExpired 过期的时间点
	 */
	public function setSessionData($strValue,$dtExpired);
	/**
	 * 额外设置用户信息，可用于实时分析在线用户
	 * @param type $userid
	 * @param type $ip
	 * @param type $camefrom
	 */
	public function tellMeUser($userid,$ip,$camefrom=null);
	/**
	 * 更新存储
	 */
	public function update();
	/**
	 * 程序执行结束后释放相关资源
	 */
	public function freeOnShutdown();
	/**
	 * 如果使用的是系统默认的session机制，调用此函数更改系统的存储机制
	 */
	public static function hookSessionHandle();
}