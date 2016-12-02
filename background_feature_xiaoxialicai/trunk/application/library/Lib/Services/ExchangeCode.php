<?php
namespace Lib\Services;
/**
 * rpc 服务器管理
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class ExchangeCode
{
	protected static $_instance = null;

	/**
	 *
	 * @param \Sooh\Base\Rpc\Broker $rpcOnNew
	 * @return \Lib\Services\ExchangeCode
	 */
	public static function getInstance($rpcOnNew = null)
	{
		if (self::$_instance === null) {
			$c = get_called_class();
			self::$_instance = new $c;
			self::$_instance->rpc = $rpcOnNew;
		}
		return self::$_instance;
	}
	
	/**
	 *
	 * @var \Sooh\Base\Rpc\Broker
	 */
	protected $rpc;

	/**
	 * 用户使用兑换码获得奖品
	 * @param string $excode 手机号
	 * @param string $userId userid
	 * @param string $fetched _分隔的已经领取的分组
	 * @param string $ordersId 可选的订单号
	 * @return string  {code:200,err:"",groupid:'1111',excode:"xxxxxx",bonus:{bonusItem1:num1}}
	 *			出错的时候 {code:400,err:"兑换码已过期|验证码已领取",excode:"xxxxxx",}
	 */
	public function useCode($excode,$userId,$fetched,$ordersId)
	{
		if ($this->rpc !== null) {
			return $this->rpc->initArgs(array('excode' => $excode, 'userId' => $userId, 'ordersId' => $ordersId))->send(__FUNCTION__);
		} else {
			$obj = \Prj\Data\ExchangeCode::getCopy(strtoupper($excode));
			$obj->load();
			if($obj->exists()){
				$dtNow = \Sooh\Base\Time::getInstance()->timestamp();	
				$expire = $obj->getField('dtExpire');
				
				if($obj->getField('dtFetch')){
					return array("code"=> 400,"err"=>\Prj\Lang\Broker::getMsg('excode.excode_used'),"status"=>1);
				}elseif(!empty($expire) && ($expire < $dtNow)){
					return array("code"=> 400,"err"=>\Prj\Lang\Broker::getMsg('excode.excode_missing_expired'),"status"=>1);
				}elseif($obj->getField('userId') && $obj->getField('userId')!=$userId){
					return array("code"=> 400,"err"=>\Prj\Lang\Broker::getMsg('excode.try_fetch_other'),"status"=>1);
				
				}else{
					$excode_grp = $obj->getField('grpId');
					if(strpos($fetched, '_'.$excode_grp.'_')!==false){
						return array("code"=> 400,"err"=>\Prj\Lang\Broker::getMsg('excode.try_fetch_samegrp'),"status"=>1);
					}
					$obj->setField('userId', $userId);
					$obj->setField('dtFetch', $dtNow);
					$obj->setField('ordersId', $ordersId);
					try{
						$obj->update();
						
						$excode_batchId = $obj->getField('batchId');
						$db = \Sooh\DB\Broker::getInstance();
						$bonus = $db->getOne(\Prj\Data\ExchangeCode::tbGrp, 'bonusini',['grpId'=>$excode_grp,'batchId'=>$excode_batchId]);
						
						$tmp = json_decode($bonus,true);//[ItemName=>ItemNum]
						
						if($tmp){
							return array("code"=> 200,"groupid"=>$excode_grp,"bonus"=>$tmp,"excode"=>$excode);								
						}else{
							return array("code"=> 400,"err"=>\Prj\Lang\Broker::getMsg('excode.excode_dberror'),"status"=>1);
								
						}
					} catch (\ErrorException $ex) {
						error_log($ex->getMessage()."\n".$ex->getTraceAsString());
						return array("code"=> 400,"err"=>\Prj\Lang\Broker::getMsg('excode.excode_dberror'),"status"=>1);
						
					}
				}
				
			}else{
				return array("code"=> 400,"err"=>\Prj\Lang\Broker::getMsg('excode.excode_error'),"status"=>0);
			}
		}
	}

	/**
	 * 获取一个可用兑换码并给于指定用户
	 * @param string $grpid 手机号
	 * @param string $batch 消息内容
	 * @param string $userId 消息内容
	 * @return string {code:200,excode:"xxxxxx"}或{code:400,err:"已经领取光了"}
	 */
	public function markone($grpid, $batch,$userId)
	{
		if ($this->rpc !== null) {
			return $this->rpc->initArgs(array('grpid' => $grpid, 'batch' => $batch,'userId'=>$userId))->send(__FUNCTION__);
		} else {
			//只要10个
			$pager = new \Sooh\DB\Pager(10);
			$pager->init($pager->page_size, 1);
			if(empty($batch)){
				$ret = \Prj\Data\ExchangeCode::loopGetRecordsPage(['excode'=>'sort'], ['where'=>['grpId'=>$grpid,'userId'=>0]],$pager);
			}else{
				$ret = \Prj\Data\ExchangeCode::loopGetRecordsPage(['excode'=>'sort'], ['where'=>['grpId'=>$grpid,'batchId'=>$batch,'userId'=>0]],$pager);
			}
			$rs = $ret['records'];
			$rand=[];
			foreach($rs as $r){
				$rand[$r['excode']]=$r['excode'];
			}
			if(empty($userId)){//不用限定给某人 
				return '{"code:200","excode":"'.array_rand($rand).'"}';
			}
			
			while(sizeof($rand)){
				$excode = array_rand($rand);
				$sys = \Prj\Data\ExchangeCode::getCopy($excode);
				$u = $sys->getField('userId');
				if(empty($u)){
					$sys->setField('userId', $userId);
					try{
						$sys->update();
						return '{"code:200","excode":"'.array_rand($rand).'"}';
					} catch (Exception $ex) {
						
					}
				}
				\Prj\Data\ExchangeCode::freeAll();
			}
		}
	}
		
	/**
	 * 获取用户的兑换码
	 * @param string $grp 分组字符串
	 * @param string $batch 批字符串
	 * @param int $userId 用户id
	 * @return string {code:200,excode:"xxxxxx"}或{code:400,err:"已经领取光了"}
	 */
	public function getUserCode($grp, $batch,$userId)
	{		
		throw new \ErrorException("todo");
//		$map = array('grpId'=>$grp,'batchId'=>$batch,'userId'=>$userId);
//		$ret = \Prj\Data\ExchangeCode::loopFindRecords($map);
//		if(!empty($ret)){
//			return array("code"=> 400,"msg"=>\Prj\Lang\Broker::getMsg('excode.only_fetch_one'));
//		}
//		
//		//只要10个
//		$pager = new \Sooh\DB\Pager(10);
//		$pager->init($pager->page_size, 1);
//		if(empty($batch)){
//			$ret = \Prj\Data\ExchangeCode::loopGetRecordsPage(['excode'=>'sort'], ['where'=>['grpId'=>$grp,'userId'=>0]],$pager);
//		}else{
//			$ret = \Prj\Data\ExchangeCode::loopGetRecordsPage(['excode'=>'sort'], ['where'=>['grpId'=>$grp,'batchId'=>$batch,'userId'=>0]],$pager);
//		}
//		$rs = $ret['records'];
//		$rand=[];
//		foreach($rs as $r){
//			$rand[$r['excode']]=$r['excode'];
//		}
//		if(empty($userId)){//不用限定给某人 
//			return array("code"=> 200,"excode"=>array_rand($rand));
//		}
//		
//		while(sizeof($rand)){
//			$excode = array_rand($rand);
//			$sys = \Prj\Data\ExchangeCode::getCopy($excode);
//			$sys->load();
//			//var_log($sys);
//			$u = $sys->getField('userId');
//			if(empty($u)){
//				$sys->setField('userId', $userId);
//				try{
//					$sys->update();
//					
//					$db = \Sooh\DB\Broker::getInstance('default');
//					$record = $db->getRecord('db_p2p.tb_exchangecodes_grp','*', array('grpId'=>$grp,'batchId'=>$batch));
//					$new_use_num = intval($record['useNum'])+1;
//					$db->updRecords('db_p2p.tb_exchangecodes_grp',array('useNum'=>$new_use_num),array('grpId'=>$grp,'batchId'=>$batch));
//					
//					
//					return array("code"=> 200,"excode"=>$excode);
//				} catch (Exception $ex) {
//					
//				}
//			}
//			\Prj\Data\ExchangeCode::freeAll();
//		}			
		
	}

}
