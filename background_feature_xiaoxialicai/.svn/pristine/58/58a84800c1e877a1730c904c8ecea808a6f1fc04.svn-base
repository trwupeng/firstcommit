<?php
namespace Lib\Misc;
/**
 * 活跃度字段访问控制类,注意，如果对应的是金额，最大值2146万
 *
 * @author wang.ning
 */
class ActivePoints {
	protected $numDefine=['ymd'=>8,'n'=>10];
	protected $parts;
	protected $todayDone=false;
	public static function weekIndex($dt)
	{
		$dt0 = mktime(0,0,0,3,7,2016)-1;
		if(is_int($dt)){
			if($dt>=20160303 && $dt<1134567890){
				$dur = strtotime($dt)-$dt0;
			}else{
				$dur = $dt-$dt0;
			}
		}else{
			if($dt===null){
				$dur =\Sooh\Base\Time::getInstance()->timestamp()-$dt0;
			}else{
				$dur =$dt->timestamp()-$dt0;
			}
		}
		//var_log($dt,"ActivePoints::weekIndex() dur(".$dur.'=dt-'.$dt0.')/604800='.ceil($dur/604800).' original=');
		return ceil($dur/604800);
	}
	protected function parseField($nStr)
	{
		$dt = \Sooh\Base\Time::getInstance();
		$today = $dt->YmdFull;
		$week = self::weekIndex($dt);//7天
		if($nStr>0){
			$this->parts = \Sooh\Base\NumStr::decode($nStr, $this->numDefine);
			$week2 = self::weekIndex($this->parts['ymd']);
			//var_log("cmp week now=".$week. ' db='.$week2.'(ymd='.$this->parts['ymd'].')');
			if($week!==$week2){
				$this->parts['n']=0;
				$this->parts['ymd'] = $today;
				$this->todayDone=false;
			}else{
				$this->todayDone= $this->parts['ymd']==$today;
			}
		}else{
			$this->parts = ['ymd'=>$today,'n'=>0];
			$this->todayDone=false;
		}
	}
	/**
	 * 获取指定用户对应活跃统计值
	 */
	public function getNum()
	{
		return $this->parts['n'];
	}
	/**
	 * 增加指定用户对应活跃统计值
	 * @param type $uid
	 * @param type $n
	 * @return \Lib\Misc\ActivePoints
	 */
	public function addNum($n=1)
	{
		if($n>2000000000){
			throw new \ErrorException('num over flow');
		}elseif($n+$this->parts['n']>2000000000){
			$this->parts['n'] = 2000000000;
		}elseif($n+$this->parts['n']<$n){
			throw new \ErrorException('num over flow');
		}else{
			$this->parts['n']+=$n;
			return $this;
		}
	}
	/**
	 * 更新到用户对应的KVobj中
	 * @param type $userId
	 * @return arr_notify
	 */
	public function updUser()
	{
		$user = \Prj\Data\User::getCopy($this->belongToUser);
		$user->setField($this->fieldToLoad, \Sooh\Base\NumStr::encode($this->parts, $this->numDefine));
	
		$fetched = \Prj\ActivePoints\APFetched::getByUser($this->belongToUser);
		$lastNotified = $fetched->getLastNotified();
		$totalScore = $fetched->getTotalScore();
		$bonusStep=\Prj\ActivePoints\APFetched::getBonusDefine();
		$bonusStep = array_keys($bonusStep);
		foreach($bonusStep as $k=>$stepScore){
			if($stepScore<=$lastNotified){
				unset($bonusStep[$k]);
			}elseif($stepScore>$totalScore){
				unset($bonusStep[$k]);
			}
		}
		if(!empty($bonusStep)){
			$stepScore=max($bonusStep);
			$fetched->setLastNotified($stepScore);

			//达到了新的奖励阶段-lyq
			try {
				\Prj\ReadConf::run(
					[
						'event' => 'total_got_integrate',
						'much_jifen' => $totalScore,
						'brand' => \Prj\Message\Message::MSG_BRAND,
					],
					[
						'userId' => $this->belongToUser,
					    'phone' => $user->getField('phone')
					]
				);
			} catch (\Exception $e) {
				var_log('give msg/push/sms with new weekactive level error:' . $e->getMessage());
			}
			return ['type'=>'activebonus','step'=>$stepScore,'score'=>$totalScore];
		}else{
			return null;
		}
	}
	
	protected $fieldToLoad;
	protected $belongToUser;
	protected static $_copies=[];
	/**
	 * 获取用户对应事件的完成情况的类
	 * @param string $userId
	 * @return \Lib\Misc\ActivePoints
	 */
	public static function getCopy($userId,$fullname=null)
	{
		if($fullname===null){
			$fullname = get_called_class();
		}
		$classname = explode("\\", $fullname);
		$classname = array_pop($classname);
		if(!isset(self::$_copies[$userId.'@'.$classname])){
			self::$_copies[$userId.'@'.$classname] = $obj = new $fullname;
			$obj->fieldToLoad ="ap_". $classname;
			$obj->belongToUser = $userId;
			$user = \Prj\Data\User::getCopy($userId);
			$user->load();
			$obj->parseField($user->getField($obj->fieldToLoad));
		}
		return self::$_copies[$userId.'@'.$classname];
	}
}
