<?php
namespace Prj\ActivePoints;
/**
 * 本周购买金额读写类，需添加字段 ap_BuyAmount
 *
 * @author wang.ning
 */
class BuyAmount extends \Lib\Misc\ActivePoints{
	public function addNum($n = 1) {
		return parent::addNum($n);
	}
}
