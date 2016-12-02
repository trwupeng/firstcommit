<?php
/**
 * 当前用户的周活跃值状态（完成情况，得分，已领取哪些阶段奖励）
 *
 * @author wang.ning
 */
class Weekactive {
	/**
	 * 当前用户的周活跃值状态（完成情况，得分，已领取哪些阶段奖励）

    Weekactive:{
        totalScore: 12,    //本周累计得分
        done:{
            Invited: 12，      //本周累计邀请用户数
            InvitedBuyAmount: 12， //累计邀请注册并购买的用户数
            Checkin：2，       //本周最大连续签到次数
            RechargeTimes：12，//本周充值次数
            RechargeAmount：12,//本周充值金额
            BuyTimes：12，     //本周投资次数
            BuyAmount：12，    //本周投资金额
        }，
        fetched:[               //已经领取的各个积分段的奖励:[多少分的阶段，什么东西，多少个]
            [10,xxxx,1],
            [20,yyy,2]
        ]
    }

	 */
	public static function run($view,$request,$response=null)
	{
		$userId = \Sooh\Base\Session\Data::getInstance()->get('accountId');
		$user = \Prj\Data\User::getCopy($userId);
		$user->load();
		if(!$user->exists()){
			$view->assign('Weekactive', [
				'totalScore'=>-1,//本周累计的积分
				'done'=>[],//每一项的当前值
				'fetched'=>[]
			]);
			return;
		}
		$fetched = \Prj\ActivePoints\APFetched::getByUser($userId);
		$items = \Prj\ActivePoints\APFetched::getAllClasses();
		$done = [];
		foreach($items as $item){
			$done[$item] = \Lib\Misc\ActivePoints::getCopy($userId, '\\Prj\\ActivePoints\\'.$item)->getNum();
		}
		$fetchedThisWeek=[];
		$tmp = $fetched->getFetched();
		foreach($tmp as $score=>$items){
			$itemname =  key($items);
			$itemname = explode("\\", $itemname);
			$fetchedThisWeek[]=[$score,  array_pop($itemname), current($items)];
		}
		$view->assign('Weekactive', [
			'totalScore'=>$fetched->getTotalScore(),//本周累计的积分
			'done'=>$done,//每一项的当前值
			'fetched'=>$fetchedThisWeek,
			'eachScore'=> $fetched->eachScore,
		]);
	}
}
