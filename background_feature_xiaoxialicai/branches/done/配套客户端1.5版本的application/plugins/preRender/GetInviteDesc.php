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
	 * //返回客户端旧的标志信息
	 *  @output {code:200,msg:success,inviteDesc:{title:title,content:content,url:url,picUrl:picUrl}}
	 * //返回客户端新的标志信息
	 *  @output {code:200,msg:success,GetInviteDescinviteDesc:{title:title,content:content,url:url,picUrl:picUrl}}
	 */
	public static function run($view,$request,$response=null)
	{
		$view->assign('inviteDesc', [
			'title' => \Prj\Data\Config::get('CUSTOMER_INVITE_TITLE'),
			'content' => \Prj\Data\Config::get('CUSTOMER_INVITE_CONTENT'),
			'url' => \Prj\Data\Config::get('CUSTOMER_INVITE_URL'),
			'picUrl' => \Prj\Data\Config::get('CUSTOMER_INVITE_PICURL'),
		]);//出现多个assign中的inviteDesc字段重复，修改如下突出唯一识别
		
		$view->assign('GetInviteDescinviteDesc', [
		    'title' => \Prj\Data\Config::get('CUSTOMER_INVITE_TITLE'),
		    'content' => \Prj\Data\Config::get('CUSTOMER_INVITE_CONTENT'),
		    'url' => \Prj\Data\Config::get('CUSTOMER_INVITE_URL'),
		    'picUrl' => \Prj\Data\Config::get('CUSTOMER_INVITE_PICURL'),
		]);
	}
}