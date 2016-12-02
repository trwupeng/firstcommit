<?php
namespace Prj\ActivePoints;
/**
 * 本周累计签到次数读写类，需添加字段 ap_Checkin
 *
 * @author wang.ning
 */
class Checkin extends \Lib\Misc\ActivePoints{
	public function addNum($n = 1) {
		if($this->todayDone){
			throw New \ErrorException('should not goes here:'.__CLASS__.'::'.__FUNCTION__);
		}
		return parent::addNum($n);
	}
	
}
