<?php
namespace Prj\ActivePoints;
/**
 * 本周累计的邀请用户的投资额读写类，需添加字段 ap_InvitedInvest
 *
 * @author wang.ning
 */
class InvitedInvest extends \Lib\Misc\ActivePoints{
	public function addNum($n = 1) {
		return parent::addNum($n);
	}
}
