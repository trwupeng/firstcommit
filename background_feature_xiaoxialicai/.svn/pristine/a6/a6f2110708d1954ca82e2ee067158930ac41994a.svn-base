<?php
namespace Lib\Interfaces;
/**
 * 道具类的发放和使用的接口定义
 * @author simon.wang
 */
interface Item {
	/**
	 * 发放道具-准备
	 * @param \Prj\Data\User $user
	 * @param int $num
	 * @return string 错误说明，没错误返回空串
	 */
	public function give_prepare($user,$num=1);
	/**
	 * 碰上错误，回滚发放行为
	 * （要求内部捕获错误，写报警日志,不要抛出异常，外部调用时，不额外处理错误细节）
	 * @param \Prj\Data\User $user
	 * @return string 错误说明，没错误返回空串
	 */
	public function give_rollback($user);
	/**
	 * 正常结束，确认发放
	 * （要求内部捕获错误，写报警日志,不要抛出异常，外部调用时，不额外处理错误细节）
	 * @param \Prj\Data\User $user
	 * @return string 错误说明，没错误返回空串
	 */
	public function give_confirm($user);
	/**
	 * 使用道具
	 * @param array $args
	 * @param int $num
	 * @return string 错误说明，没错误返回空串
	 */
	public function useit($args,$num);
	/**
	 * 碰上错误，回滚使用行为
	 * @param array $args
	 */
	public function rollbackUse($args);
	/**
	 * 剩余数量
	 * @param \Prj\Data\User $user
	 * @return int
	 */
	public function numLeft($user);
}
