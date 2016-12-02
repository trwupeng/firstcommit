<?php
/**
 * Created by PhpStorm.
 * User: LTM <605415184@qq.com>
 * Date: 2016/3/8
 * Time: 17:03
 */
class GetInviteDesc
{
	/**
	 * 获取邀请时的一些信息
	 * @output {code:200,msg:success,inviteDesc:{title:title,content:content,url:url,picUrl:picUrl}}
	 */
	public static function run($view,$request,$response=null)
	{
		$view->assign('inviteDesc', [
			'title' => \Prj\Data\Config::get('CUSTOMER_INVITE_TITLE'),
			'content' => \Prj\Data\Config::get('CUSTOMER_INVITE_CONTENT'),
			'url' => \Prj\Data\Config::get('CUSTOMER_INVITE_URL'),
			'picUrl' => \Prj\Data\Config::get('CUSTOMER_INVITE_PICURL'),
		]);
	}
}