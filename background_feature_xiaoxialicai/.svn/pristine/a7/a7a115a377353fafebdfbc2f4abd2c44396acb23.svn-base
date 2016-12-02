<?php

/**
 * 用户扩展功能接口
 * 
 * 
 * 
 * @author simon.wang
 */
class WeekactiveController extends \Prj\UserCtrl
{
//	/*
//	public function init()
//	{	
//		error_log('>>TODO: 删掉weekactiveController里的init()');
//		parent::init();
//		$userId='75722292896094';
//		$sess = \Sooh\Base\Session\Data::getInstance();
//		$sess->set('accountId',$userId);
//	}
//	*/
//	
//	public function testAction()
//	{
//		$sess = \Sooh\Base\Session\Data::getInstance();
//
//		$user = \Prj\Data\User::getCopy($userId=$sess->get('accountId'));
//		$user->load();
//		\Prj\ActivePoints\Checkin::getCopy($userId)->addNum(1)->updUser();
//		$user->update();
//	}
	/**
	 * 获取用户对应的阶段奖励
	 * @input int scorestep 要领取的是哪个阶段的奖励
     * @output {code: 200,WeekactiveBonus:{forScore:30,items:[{itemName:xxxx,itemNum:1}]}}
	 */
	public function fetchAction()
	{
		$this->_view->assign('WeekactiveBonus',['itemName'=>'xxxx','itemNum'=>1]);
		$bonusDefine = \Prj\ActivePoints\APFetched::getBonusDefine();
		$step = $this->_request->get('scorestep')-0;//如：2=>Array ( [RedPacketForWeekactive] => 123 ) 
		if(!isset($bonusDefine[$step])){
			$this->returnError(\Prj\Lang\Broker::getMsg('weekactive.bonusstep_dismatch'));
		}else{
			$userId = \Sooh\Base\Session\Data::getInstance()->get('accountId');
			$user = \Prj\Data\User::getCopy($userId);
			$user->load();
			$fetched = \Prj\ActivePoints\APFetched::getByUser($userId);// 根据userid，产生 活跃值领取情况，字段封装类 
			if($fetched->getTotalScore()<$step){
				return $this->returnError(\Prj\Lang\Broker::getMsg('weekactive.ap_not_enough'));
			}
			$fetchedAlready = $fetched->getFetched();
			if (isset($fetchedAlready[$step])){
				return $this->returnError(\Prj\Lang\Broker::getMsg('weekactive.bonus_fetched_already'));
			}
			
			try{
				$bonusReal = $bonusDefine[$step];
				$bonusTask = new \Prj\Items\ItemGiver($userId);
				foreach($bonusReal as $bonusItem=>$bonusNum){
					$bonusTask->add($bonusItem, $bonusNum);
				}
				$realGived = $bonusTask->give();
				if($realGived!==null){
					foreach($realGived as $k=>$r){
						$tmp = $r[0];
						$tmp = explode("\\", $tmp);
						$realGived[$k][0] = $r[0] = array_pop($tmp);
						$fetched->addFetched($step, $r[0], $r[1]);
					}
					$user->update();
					$forClients=[];
					foreach($realGived as $r){
						$forClients[]=['itemName'=>$r[0],'itemNum'=>$r[1]];
					}
					$this->_view->assign('WeekactiveBonus',[
						'forScore'=>$step,'items'=>$forClients
					]);
					$bonusTask->onUserUpdated();
					foreach($realGived as $r){
						\Prj\ActivePoints\APFetchLog::write($userId, \Sooh\Base\Time::getInstance()->timestamp(), $step, $r[0], $r[1]);
					}

                    $this->loger->sarg1 = sprintf('%.02f', $forClients[0]['itemNum'] / 100);
                    $this->loger->sarg2 = \Sooh\Base\Session\Data::getInstance()->get('accountId');

//					try {
//						\Prj\ReadConf::run(
//							[
//								'event' => 'red_admire_packet',
//								'num_packet' => 1,
//								'private_gift' => sprintf('%.02f', $forClients[0]['itemNum'] / 100),
//								'num_deadline' => 48,
//								'brand' => \Prj\Message\Message::MSG_BRAND,
//							],
//							[
//								'userId' => \Sooh\Base\Session\Data::getInstance()->get('accountId'),
//							]
//						);
//					} catch (\Exception $e) {
//						var_log('give weekactive bonus error:' . $e->getMessage());
//					}

					$this->returnOK('success');
				}else{
					return $this->returnError($bonusTask->getLastError(),508);
				}
			} catch (Exception $ex) {
				error_log($ex->getMessage()."\n".$ex->getTraceAsString());
				$this->returnError(\Prj\Lang\Broker::getMsg('system.db_error'),509);
			}
		}
	}	
}