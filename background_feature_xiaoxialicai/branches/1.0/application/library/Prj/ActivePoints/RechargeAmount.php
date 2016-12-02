<?php
namespace Prj\ActivePoints;
/**
 * 本周充值金额读写类，需添加字段 ap_RechargeAmount
 *
 * @author wang.ning
 */
class RechargeAmount extends \Lib\Misc\ActivePoints{
	public function addNum($n = 1) {
		return parent::addNum($n);
	}
}
