<?php

use Lib\Medal\Medal;
use Lib\Medal\UserMedal;

/**
 * 勋章系统接口
 *
 * @author wu.chen
 */
class MedalController extends \Prj\BaseCtrl {

    /**
     * 获得所有勋章(勋章墙)
     * 添加参数 __VIEW__=json 返回json数据格式
     * 列表项字段：
     * {
     * 		id: 勋章ID,
     * 		key：勋章英文字段,
     * 		name：勋章名称,
     * 		icon:图标，如果有，没有的话，空串""，
     * 		serialNumber:排序，
     * 		description:勋章描述
     * 		task:任务描述
     * 		taskLevel:任务等级
     * 		taskUnit:任务数字对应的单位
     * 		reward:奖励等级
     * 		rewardName：奖励名称
     * 		rewardUnit:奖励金额单位
     * 		rewardNumUnit:奖励数量单位
     * 		status:状态1:启用
     * 		progress:当前任务完成度
     *          getLevel:完成等级 {"1":{"getTime":"获得勋章时间", "getReward":"获得奖励时间"}}
     * }
     */
    public function getAllMedalAction() {
        $userMedal = new UserMedal();
        var_log("########勋章userId:" . \Sooh\Base\Session\Data::getInstance()->get('accountId') . '###########');
        $userId = \Sooh\Base\Session\Data::getInstance()->get('accountId');
        $user = $userMedal->setWhere(['userId' => $userId])->getRecord();
        $userMedals = FALSE;
        if ($user) {
            $userMedals = json_decode($user['medals'], TRUE);
        }
        $medal = new Medal();
        $medalList = $medal->setSelectFields(['id', '`key`'])->getAllRecords();

        $recommendList = $newList = $oldList = [];
        foreach ($medalList as $index => $medal) {
            $medalList[$index]['getLevel'] = $medalList[$index]['progress'] = 0;
            if (isset($userMedals[$medal['key']])) {
                if (!empty($userMedals[$medal['key']]['getLevel'])) {
                    $medalList[$index]['getLevel'] = $userMedals[$medal['key']]['getLevel'];
                }
                $medalList[$index]['progress'] = $userMedals[$medal['key']]['progress'];
            }
            unset($medalList[$index]['createTime']);
        }
        $this->_view->assign('medalList', $medalList);
        $this->_view->assign('medalShare', ['medalShareTitle' => \Prj\Data\Config::get('SHARE_MEDAL_TITLE'),
            'medalShareDesc' => \Prj\Data\Config::get('SHARE_MEDAL_DESC'),
            'medalShareUrl' => \Prj\Data\Config::get('SHARE_MEDAL_URL'),
            'medalSharePic' => \Prj\Data\Config::get('SHARE_MEDAL_PIC'),]);
        $this->returnOK();
    }

}
