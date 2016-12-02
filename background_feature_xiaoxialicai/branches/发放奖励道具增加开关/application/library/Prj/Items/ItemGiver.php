<?php

namespace Prj\Items;

/**
 * 给予用户道具奖励的包装类
 * 
 * 发放道具到指定用户身上，如果发生错误，通过getLastError()获取最后的错误
 *
 * @author wang.ning
 */
class ItemGiver {
    protected $userId;
    protected $rs=[];
    protected $lst=[];
    protected $switch = [];//开关

//    //example-首次打开某个开关时，最好经过严格的测试
//    protected $switchExample = [
//        'NewFirstLoginAppRedPacket' => 1,
//        'NewRegisterRedPacket' => 1,
//        'NewFirstBindRedPacket' => 1,
//        'NewFirstBuyRedPacket' => 1,
//        'NewFirstBuyForInviteRedPacket' => 1,
//        'NewFirstChargeRedPacket' => 1,
//        'NewFinallyOrderRedPacket' => 1,
//    ];//开关
    public function __construct($userId) {
		$this->userId = $userId;
		\Sooh\Base\Ini::registerShutdown([$this,'freeOnShutdown'], 'itemPack_'.$userId);
        $this->switch = \Prj\Data\Config::get('ITEM_GIVE_SWITCH');
	}
	/**
	 * 添加准备发放的道具
	 * @param string $itemName
	 * @param int $itemNum
	 * @return \Prj\Items\ItemGiver
	 */
	public function add($itemName,$itemNum)
	{
        if (empty($this->switch) || !isset($this->switch[$itemName]) || $this->switch[$itemName] == 1) {
            $this->lst[$itemName] = $itemNum;
        }
        return $this;
	}

	/**
	 * 尝试发放（不更新user，主要是批处理所有道具，完成各自的检查和周边设置），
	 * 出错返回null，通过getLastError()获取最后的错误信息
	 * 成功返回实际给予的道具及数量的列表，格式：[ [itemName,itemNum], [itemName,itemNum], .... ]
	 * @param array $args args
	 * @return array|null [<br />
	 * [bonus名称, 金额、数量，到期时间时间戳，券ID]，<br />
	 * [bonus名称, 金额、数量，到期时间时间戳，券ID]，<br />
	 * [bonus名称, 金额、数量，到期时间时间戳，券ID]<br />
	 * ]
	 * @throws \Sooh\Base\ErrException
	 */
	public function give(array $args = [])
	{
        if (empty($this->lst)) {
            return null;
        }

		$user = \Prj\Data\User::getCopy($this->userId);
		$user->load();
		$gived = [];
		//var_log($this->lst,'plan to give');
		foreach($this->lst as $bonusItem=>$bonusNum){
			$bonusItemClass = '\\Prj\\Items\\'.$bonusItem;
			//error_log(">>>>>>>>>$bonusItemClass");
			if (!class_exists($bonusItemClass)) {
				throw new \Sooh\Base\ErrException('unknown bonusItem found:'.$bonusItem, 500);
			}

			/**
			 * @var \Prj\Items\Voucher $bonusObj
			 */
			$bonusObj = new $bonusItemClass($args);
			//error_log(">>>>>>>>>$bonusItemClass created");
			\Prj\Misc\OrdersVar::$introForUser  = $bonusObj->descCreate();
			\Prj\Misc\OrdersVar::$introForCoder = $bonusObj->name();
			//error_log(">>>>>>>>>$bonusItemClass start try");
			try{

				$errmsg = $bonusObj->give_prepare($user, $bonusNum);
			}  catch (\ErrorException $e){
				error_log('give bonus to user('.$this->userId.') failed: '.$e->getMessage()."\n".$e->getTraceAsString());
				$errmsg = \Prj\Lang\Broker::getMsg('system.server_busy');
			}
			//error_log(">>>>>>>>>$bonusItemClass returned-msg=".$errmsg);
			if(!empty($errmsg)){
				//error_log("############-------b:".$errmsg);
				$this->lastError = $errmsg;
				break;
			}else{
				$gived[] = $bonusObj;
				if(method_exists($bonusObj, 'onUserUpdated')){
					$this->funcUserUpdated[$bonusItem] = [$bonusObj,'onUserUpdated'];
				}
			}
		}
		
		if(!empty($this->lastError)){
			//error_log("gived faled");
			foreach($gived as $bonusObj){
				$bonusObj->give_rollback($user);
			}
			return null;
		}else{
			//error_log("gived ok");
			$finalItems=[];
			foreach($gived as $bonusObj){
				$bonusObj->give_confirm($user);
				$finalItems = array_merge($finalItems,$bonusObj->realGived());
			}
			return $finalItems;
		}
	}
	protected $lastError = '';
	public function getLastError()
	{
		return $this->lastError;
	}
	protected $funcUserUpdated=[];
	/**
	 * user->update()成功后调用
	 */
	public function onUserUpdated()
	{
		foreach($this->funcUserUpdated as $k=>$f){
			try{
				if(is_callable($f)){
					$f();
				}else{
					call_user_func($f);
				}
			}  catch (\ErrorException $ex){
				error_log("error found when call $k.onUserUpdated to user:{$this->userId} : ".$ex->getMessage()."\n".$ex->getTraceAsString());
			}
		}
	}
	/**
	 * 最后确保释放用的，基于框架会自动调用
	 */
	public function freeOnShutdown()
	{
		$ks = array_keys($this->funcUserUpdated);
		foreach($ks as $k){
			unset($this->funcUserUpdated[$k]);
		}
	}
	
	/**
	 * 奖励红包转为文字说明
	 * @param array $bonus_arr 奖励数组
	 * @return string  奖励文字说明
	 */
	public function bonusToDesc($bonus_arr)
	{
		if(empty($bonus_arr) || empty($bonus_arr[0])) {
			return false;
		} else {
			$bonus_str = '';
			$bonus_str_arr = array();
			foreach($bonus_arr as $val){
				if(!empty($val['itemName'])) {
					$bonus_str0 = \Prj\Lang\Broker::getMsg('RedPacket.'.$val['itemName'].'.0');
					$bonus_str1 = \Prj\Lang\Broker::getMsg('RedPacket.'.$val['itemName'].'.1');	
					if($bonus_str1=='元'){
						$bonus_str_arr[] = sprintf("%.2f",$val['itemNum']/100).$bonus_str1.$bonus_str0;
					}else{
						$bonus_str_arr[] = $val['itemNum'].$bonus_str1.$bonus_str0;
					}
				}
			}
			
			$bonus_str = join(',',$bonus_str_arr);
			return $bonus_str;
		}
	}
}
