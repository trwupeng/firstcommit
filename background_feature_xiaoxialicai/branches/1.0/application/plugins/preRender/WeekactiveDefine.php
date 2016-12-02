<?php
/**
 * 当前用户的周活跃系统设置
 *
 * @author wang.ning
 */
class WeekactiveDefine {
	/**
	 * 当前用户的周活跃值状态（完成情况，得分，已领取哪些阶段奖励）

    WeekactiveDefine:{
			Bonus: [   //达到多少分可领取什么奖励，后面描述中
				{over:10,itemName:xxxx,itemNum:1},//达到10分可领取1个xxxx
				{over:20,itemName:yyyy,itemNum:2},//达到20分可领取2个xxxx
			],
			ActivePoints:{
				Invited: [
					{over:10,AP:1}//邀请用户数达到多少可获多少积分
				],
				InvitedBuyAmount: [……(同上)],   //邀请用户数达到多少可获多少积分
				Checkin: [……(同上)]，       //连续签到次数达到多少可获多少积分
				RechargeTimes: [……(同上)]， //充值次数达到多少可获多少积分
				RechargeAmount: [……(同上)], //充值金额达到多少可获多少积分
				BuyTimes: [……(同上)]，      //投资次数达到多少可获多少积分
				BuyAmount: [……(同上)]，     //投资金额达到多少可获多少积分
			}
    }

	 */
	public static function run($view,$request,$response=null)
	{
		$ret = ['Enable'=> \Prj\ActivePoints\APFetched::enabled(),'Bonus'=>[],'ActivePoints'=>[]];
		$classes = \Prj\ActivePoints\APFetched::getAllClasses();
		foreach($classes as $c){
			$ret['ActivePoints'][$c]=[];
			$tmp = \Prj\ActivePoints\APFetched::getScoreScope($c);
			foreach($tmp as $k=>$v){
				if($k>5000){
					$k = $k/100;
				}
				$ret['ActivePoints'][$c][]=['over'=>$k,'AP'=>$v];
			}
		}
		$ret['Actived']=array_keys($ret['ActivePoints']);
		$bonus = \Prj\ActivePoints\APFetched::getBonusDefine();
		foreach($bonus as $score=>$items){
			$ret['Bonus'][] = ['over'=>$score,'itemName'=>key($items),'itemNum'=>  current($items)];
		}
		$view->assign('WeekactiveDefine',$ret);
	}
}
