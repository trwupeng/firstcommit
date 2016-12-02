<?php
namespace Prj\ActivePoints;
/**
 * 本周购买次数读写类，需添加字段 ap_BuyTimes
 *
 * @author wang.ning
 */
class BuyTimes extends \Lib\Misc\ActivePoints{
	public function addNum($n = 1) {
		return parent::addNum($n);
	}
}
