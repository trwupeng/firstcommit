<?php
namespace Prj\ActivePoints;

/**
 * 活跃值领取情况，字段封装类(ap_fetched)
 * alter table tb_user_1 add ap_fetched varchar(500) not null default '[]' COMMET '活跃值—领取情况';
 * alter table tb_user_0 add ap_fetched varchar(500) not null default '[]' COMMET '活跃值—领取情况';
 * @author wang.ning
 */
class APFetched {
	/**
	 * 获取当前有哪些事情参与活跃值计算
	 * @return array
	 */
	public static function getAllClasses()
	{
		return ['BuyAmount','UsedShareVoucher','Checkin','Invited','InvitedInvest'];
		//| weekScore_evtList         | 参与统计的事件列表(大小写区分)，格式： {哪天开始使用此列表:[Invited,Checkin],20160501:[Invited,Checkin],....}       |
	}
	public static function enabled()
	{
		return 1;
	}
	/**
	 * 获取当前达到多少分可以获得什么奖励的设置
	 * 奖项中：RedPacketForWeekactive 红包，一个就是1分，要发1元的红包就发100个RedPacketForWeekactive
	 * @return array [达到多少分=>[奖项1=>数量,],达到多少分=>[奖项1=>数量,]]
	 */
	public static function getBonusDefine()
	{
		return [
			20=>['RedPacketForWeekactive'=>50],
			40=>['RedPacketForWeekactive'=>50],
			60=>['RedPacketForWeekactive'=>100],
			80=>['RedPacketForWeekactive'=>200],
			100=>['RedPacketForWeekactive'=>500],
		];
	}
	/**
	 * 获取指定用户的活跃值领取状态
	 * @param type $userId
	 * return \Prj\ActivePoints\APFetched
	 */
	public static function getByUser($userId)
	{
		$obj =  new \Prj\ActivePoints\APFetched();
		$obj->userId = $userId;
		return $obj;
	}
	protected $userId;

	protected function getFromUser()
	{
		//获取用户活跃值
		$user = \Prj\Data\User::getCopy($this->userId);
		$user->load();
		$arr = $user->getField('ap_fetched');
		
		$week = \Lib\Misc\ActivePoints::weekIndex(null);
		if(isset($arr['_'])){
			if($arr['_']!=$week){
				return array('_'=>$week,'got'=>[],'notified'=>0);
			}else{
				return $arr;
			}
		}else{
			return array('_'=>$week,'got'=>[],'notified'=>0);
		}
	}
	/**
	 * 获取已领取列表，【多少分的阶段=>[什么东西=>多少个],多少分的阶段=>[什么东西=>多少个]】
	 */
	public function getFetched()
	{
		$arr = $this->getFromUser();
		return $arr['got'];
	}
	/**
	 * 在用户表自身记录领取情况
	 * @param type $stepid
	 * @param type $itemname
	 * @param type $itemnum
	 */
	public function addFetched($stepid,$itemname,$itemnum)
	{
		$arr = $this->getFromUser();
		$arr['got'][$stepid][$itemname]+=$itemnum;
		$user = \Prj\Data\User::getCopy($this->userId);
		$user->setField('ap_fetched', $arr);
	}
	/**
	 * 获取总分
	 * @return type
	 */
	public function getTotalScore()
	{
		$total = 0;
		$list = self::getAllClasses();
		foreach($list as $evt){
			$obj = \Lib\Misc\ActivePoints::getCopy($this->userId, '\\Prj\\ActivePoints\\'.$evt);//Checkin obj
			$n = $this->getScore($evt, $obj->getNum());
			$total += $n;
			$this->eachScore[$evt]=$n;
		}
		return $total;
	}
	/**
	 * 获取最后通知到用户的达到的级别
	 * @return int
	 */
	public function getLastNotified()
	{
		$arr = $this->getFromUser();
		return $arr['notified'];
	}
	/**
	 * 设置最后通知到用户的达到的级别
	 * @param int $newval
	 */
	public function setLastNotified($newval)
	{
		$arr = $this->getFromUser();
		$arr['notified']=$newval;
		$user = \Prj\Data\User::getCopy($this->userId);
		$user->setField('ap_fetched', $arr);
	}
	
	public $eachScore=[];
	protected static $scopeLoaded=[];
	/**
	 * 获取指定事情完成多少可获多少积分的设置
	 * @param string $evt 
	 * @return array [达到多少=>共可获多少积分，下一高度=>共可获多少积分]
	 */
	public static function getScoreScope($evt)
	{
		if(!isset(self::$scopeLoaded[$evt])){
			$conf = \Prj\Data\Config::get('weekScore_'.$evt);
			$r = json_decode($conf,true);
			if(is_array($r)){
				ksort($r);
				self::$scopeLoaded[$evt]=$r;
			}else{
				$conf = substr($conf,1,-1);
				$tmp = explode(',', $conf);
				$ret = [];
				foreach($tmp as $s){
					list($k,$v) = explode(':',$s);
					$ret[$k]=$v;
				}
				ksort($ret);
				self::$scopeLoaded[$evt]=$ret;
			}
		}
		return self::$scopeLoaded[$evt];
	}
	/**
	 * 从配置中算出evt可得分值
	 * @param type $evt
	 * @param type $val
	 * @return type
	 */
	public function getScore($evt,$val)
	{
		$conf = self::getScoreScope($evt);
		
		$score=0;
		foreach($conf as $cmp=>$n){
			if($val>=$cmp){
				$score = $n;
			}else{
				break;
			}
		}
		//var_log($conf,'socre of '.$evt.' is:'.$score.' val is '.$val.' conf is');
		return $score;
	}
}
