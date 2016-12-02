<?php
namespace Prj\ActivePoints;

/**
 * 活跃值领取记录，封装类
 *
 * @author wang.ning
 */
class APFetchLog {
	//用户 根据分值段领取奖励 入库
	public static function write($userId,$dt,$scoreLevel,$itemName,$itemNum)
	{
		$postIndex = substr($userId,-4);
		for($retry=0;$retry<10;$retry++){
			//随机一下autoid,用userId的后4位做尾4位，为将来分表做准备
			$rnd = rand(1000,9999);
//			error_log("Prj\\ActivePoints\\APFetchLog->write autoid 中间的随机先用了固定的8888");
//			$rnd = 8888;
			$autoid = $dt.$rnd.$postIndex;
			$obj = \Prj\Data\APFetchLog::getCopy($autoid);
			$user= \Prj\Data\User::getCopy($userId);
			$fields=[
				'userId'=>$userId,
				'dt'=>$dt,
				'score'=>$scoreLevel,
				'surname'=>  mb_substr($user->getField('nickname'), 0,1,'utf-8'),
				'phone'=>$user->getField('phone'),
				'itemName'=>$itemName,
				'itemNum'=>$itemNum,
			];
//			$fields['phone'] = substr_replace($fields['phone'],'****',3,4);
			foreach($fields as $k=>$v){
				$obj->setField($k, $v);
			}
			$fields['autoid']=$autoid;
			try{
				//写用户奖励记录 到 奖励记录表
				$obj->update();
				
				//加入ram表
				$db = \Sooh\DB\Broker::getInstance();
				$db->addRecord(self::$tbOfCache, $fields);
				self::removeExpiredCache();
				
				self::updateUserNum(1);				
				
				return true;
			} catch (\ErrorException $ex) {
				
			}
		}
		return false;
	}
	protected static $tbOfCache='db_p2p.tb_apFetchLog_ram';
	/**
	 * 30%几率删除ram表的旧数据，只保留最新的11条
	 */
	protected static function removeExpiredCache()
	{
		$db = \Sooh\DB\Broker::getInstance();
		
		if(rand(1,100)<30){
			$top11 = $db->getCol(self::$tbOfCache,'autoid',null,'rsort autoid',20);
			$db->delRecords(self::$tbOfCache,['autoid!'=>$top11]);
		}
	}
	//用户活跃数领取奖励当天+num，如果num是负数，那么是设置不是增加
	private static function updateUserNum($num=1)
	{
		$retry=3;
		while($retry>0){
			$retry--;
			try{
				$today = \Sooh\Base\Time::getInstance()->YmdFull;
				$data = \Prj\Data\TbConfigItem::getCopy('DayActiveUserNum');
				$data->load();
				$temp = $data->getField('v');
				if(empty($temp) || ($today != $temp['day'])) {//第二天从1开始
					$temp['day'] = $today;
					$temp['user_num'] = abs($num);
				} else {//当天+1
					if($num>0){
						$temp['user_num'] = intval($temp['user_num']) + $num;
					}else{
						$temp['user_num'] = abs($num);
					}
				}
				$data->setField('v', $temp);
				$data->update();
//error_log("##APLOG##add ".json_encode($temp));
//$sqls = \Sooh\DB\Broker::lastCmd(false);
//foreach($sqls as $sql)error_log("##APLOG##addlog ".$sql);	
				return;
			} catch (\ErrorException $ex) {
//error_log("##APLOG##addfailed ".$ex->getMessage().json_encode($temp));
//$sqls = \Sooh\DB\Broker::lastCmd(false);
//foreach($sqls as $sql)error_log("##APLOG##addfailed-sql ".$sql);				
				error_log("update active today to tb_config_ram error ".$ex->getMessage());
			}
		}
	}

	public static function getUserNum()
	{
		$obj = \Prj\Data\TbConfigItem::getCopy('DayActiveUserNum');
		$obj->load();
		$temp = $obj->getField('v',true);
		if(empty($temp)){
			return 0;
		}
//error_log("##APLOG##read ".json_encode($temp));
//$sqls = \Sooh\DB\Broker::lastCmd(false);
//foreach($sqls as $sql)error_log("##APLOG##readlog ".$sql);	

		$today = \Sooh\Base\Time::getInstance()->YmdFull;
		
		if(empty($temp) || ($today != $temp['day'])) {//第二天从1开始
			return 0;
		} else {//当天+1
			return $temp['user_num']-0;
		}
	}
	
	/**
	 * 从内存表中读取最近领取的人的列表，如果人数不足用假人填充
	 * 返回 [ {ymd:yyyy-mm-dd,phone:1302****123,name:张*,itemName:xxx,itemNum:1},……]
	 * @param int $pagesize
	 * @return array
	 */
	public static function readVest($pagesize=10)
	{
		$interval = 5;       //每隔两分钟
		$addVestMax = 3;//最多添加5个
		$db = \Sooh\DB\Broker::getInstance();
		$dt = \Sooh\Base\Time::getInstance();
		if($dt->his>70000){
			$enableAddVest=true;
		}else{
			$enableAddVest=false;
		}
		$dtNow = $dt->timestamp();
		
		$today = strtotime($dt->YmdFull);
		
		//从内存ram表读取最新的10条，
		$records = $db->getRecords(self::$tbOfCache,'*',['autoid]'=>$today.'00000000'],'rsort autoid',$pagesize);
		if(empty($records)) {
			$total = \Prj\Data\APFetchLog::loopGetRecordsCount(['autoid]'=>$today.'00000000']);

			if($total){//cache表空，硬盘表中有真实记录
				$pager = new \Sooh\DB\Pager($pagesize+5);
				$pager->init($total, 1);
				$rs = \Prj\Data\APFetchLog::loopGetRecordsPage(['autoid'=>'rsort'], ['where'=>['autoid]'=>$today.'00000000']], $pager);
				$lastdt =[];
				$totalAdd = self::getUserNum();
				if(empty($totalAdd)){
					self::updateUserNum(sizeof($rs['records']));
				}
				foreach($rs['records'] as $r){
					$db = \Sooh\DB\Broker::getInstance();
					unset($r['iRecordVerID']);
					unset($r['sLockData']);
					$lastdt[] = $r['dt'];
					try{
						\Sooh\DB\Broker::errorMarkSkip(\Sooh\DB\Error::duplicateKey,true);
						$db->addRecord(self::$tbOfCache, $r);
					}catch(\ErrorException $e){
						
					}
					array_unshift($records, $r);
				}
//				$lastdt = max($lastdt);
//				if($dtNow>$lastdt+$interval*60){
//					self::addVests($records, 3, $lastdt, $lastdt+120);
//					$vest = self::addVest($dtNow);
//					array_unshift($records, $vest);
//				}else{
//					$vest = self::addVest($dtNow);
//					array_unshift($records, $vest);
//				}

			}else{//cache表空，硬盘表中也没有真实记录：7点以后的话先加一个假人
				if($enableAddVest){
					$lastdt = $today+(7*3600);
					$dur = $dtNow-$lastdt;
					if($dur>$interval*60){
						$dur = ceil($dur/$interval/60);
						$addVest = 0;
						while($dur>0){
							$addVest += rand(1,$addVestMax);
							$dur--;
						}
						self::addVests($records, $addVest, $lastdt, $dtNow);
					}
				}
			}
		}else{
			$lastdt=[];
			foreach($records as $k=>$v){
				$lastdt[] = $v['dt'];
				$records[$k]['phone'] = substr_replace($v['phone'],'****',3,4);
			}
			$lastdt = max($lastdt);
			$dur = $dtNow-$lastdt;
			if($dur>$interval*60 && $enableAddVest){
				$dur = ceil($dur/$interval/60);
				$addVest = 0;
				while($dur>0){
					$addVest += rand(1,$addVestMax);
					$dur--;
				}
				self::addVests($records, $addVest, $lastdt, $dtNow);
			}
		}
		$pagesize++;
		while(sizeof($records)>$pagesize){
			array_pop($records);
		}
		return $records;
	}
	protected static function addVests(&$records,$addVest, $lastdt, $dtNow)
	{
		$dur = round(($dtNow - $lastdt)/$addVest);
		$addVest--;
		while($addVest>0){
			$addVest--;
			$lastdt+=rand(1, $dur);
			$vest = self::addVest($lastdt);
			array_unshift($records, $vest);
		}
		$vest = self::addVest($dtNow);
		array_unshift($records, $vest);
	}
	protected static function addVest($dt)
	{
		//手机段位
		$tel_first_arr = array('133','153','180','181','189','177','173','130','131','132','155','156','185','186','176','134','135','136','137','138','139','150','151','152','158','159','182','183','184','157','187','188','178','184');
		$surnames = ['张','赵','钱','孙','李','周','吴','郑','王','刘','梁','汤','童','戴'];
		//奖励概率
		$red_packet_arr = array('2',//10%
					'1','1',//20%
					'0.5','0.5','0.5','0.5','0.5','0.5','0.5',//70%
			);
		
		$autoid = $dt.'88'.rand(100000,999999);

		$fields=[
			'autoid'=>$autoid,
			'userId'=>0,
			'dt'=>$dt,
			'score'=>0,
			'surname'=> $surnames[array_rand($surnames)],
			'phone'=>$tel_first_arr[array_rand($tel_first_arr)].rand(10000000,99999999),
			'itemName'=>'RedPacket',
			'itemNum'=>$red_packet_arr[array_rand($red_packet_arr)]*100,
		];

		\Sooh\DB\Broker::getInstance()->addRecord(self::$tbOfCache, $fields);
//error_log("##APLOG##addlog".\Sooh\DB\Broker::lastCmd());
		self::updateUserNum(1);
//$sqls = \Sooh\DB\Broker::lastCmd(false);
//foreach($sqls as $sql)error_log("##APLOG##addnum ".$sql);	
		$fields['phone'] = substr_replace($fields['phone'],'****',3,4);
//		error_log('vest adddd-------------------------------------------vest add');
		return $fields;
	}
}