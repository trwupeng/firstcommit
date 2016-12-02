<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Lib\Medal;

/**
 * Description of MedalConfig
 *
 * @author wu.chen
 */
class MedalConfig {

    const TASK_INVESTMENT = 'medal_invest';       //任务：累计投资
    const TASK_FRIENDS_REG = 'medal_friendregister';      //任务：邀请好友注册
    const TASK_FRIENDS_INV = 'medal_friendinvest';      //任务：好友累计投资
    const TASK_CHECKIN = 'medal_signin';             //任务：连续签到
    const TASK_USER_REDPACKET = 'medal_useredpacket';         //任务：累计使用红包
    const TASK_SHARE_REDPACKET = 'medal_shareredpacket'; //任务：分享红包
    const REWARD_REDPACKET = 'RedPacketForMedal';       //奖励：红包

    public static function getRewardType() {
        return ['RedPacketForMedal' => '红包'];
    }
    
    public static function getUnit($str) {
        $unit = [
            'xiaoshuyuan' => '元',
            'ge' => '个',
            'tian' => '天',
        ];
        return (isset($unit[$str]) ? $unit[$str] : FALSE);
    }

}
