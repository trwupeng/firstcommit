<?php
/**
 * 查询今天他人领取情况
 *
 * @author wang.ning
 */
class WeekactiveOther {
	/**
	 * 当前用户的周活跃值状态（完成情况，得分，已领取哪些阶段奖励）

    WeekactiveOthers:{
        pager:[保留未用，将来酌情使用],
        list:[
            {ymd:yyyy-mm-dd,phone:1302****123,name:张*,itemName:xxx,itemNum:1},
            ……
        ]
    }

	 */
	public static function run($view,$request,$response=null)
	{
		$pagesize=11;
		$r = \Prj\ActivePoints\APFetchLog::readVest($pagesize);
		$view->assign('WeekactiveOthers',[
			'pager'=>['pageSize'=>$pagesize,'pageId'=>1],
			'list'=>$r,
			'todayTotal'=>\Prj\ActivePoints\APFetchLog::getUserNum()
		]);
	}
}
