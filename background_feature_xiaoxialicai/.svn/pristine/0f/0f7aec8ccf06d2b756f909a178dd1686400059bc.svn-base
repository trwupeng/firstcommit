<?php
/**
 * Created by PhpStorm.
 * User: TGH <693566361@qq.com>
 * Date: 2016/3/8
 * Time: 17:03
 */
class GetShareDesc
{
	/**
	 * 获取分享时的一些信息
	 * @output {code:200,msg:success,inviteDesc:{title:title,content:content,url:url,picUrl:picUrl}}
	 */
	public static function run($view,$request,$response=null)
	{
		$view->assign('inviteDesc', [
			'title' => \Prj\Data\Config::get('SHARE_VOUCHER_TITLE'),
			'content' => \Prj\Data\Config::get('SHARE_VOUCHER_DESC'),
			'url' => \Prj\Data\Config::get('SHARE_VOUCHER_URL'),
			'picUrl' => \Prj\Data\Config::get('SHARE_VOUCHER_PIC'),
		]);
	}
}