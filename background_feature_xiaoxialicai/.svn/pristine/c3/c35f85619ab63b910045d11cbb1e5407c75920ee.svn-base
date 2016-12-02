<?php

namespace Lib\Medal;

/**
 * Description of MedalFrindesReg
 *
 * @author wu.chen
 */
class MedalFriendsReg extends \Lib\Medal\Medal {

    protected $invitationCode;       //邀请码

    public function __construct() {
        parent::__construct();
        $this->num = 1;
    }

    public function setInvitationCode($invitationCode) {
        $this->invitationCode = $invitationCode;
        return $this;
    }

    public function logic() {
        $this->setMedal($this->userId, $this->num, \Lib\Medal\MedalConfig::TASK_FRIENDS_REG);
    }

    public function logicByInvCode() {
        $res = $this->getUserIdByInviteCode();
        if (!empty($res)) {
            $this->setMedal($res['userId'], 1, \Lib\Medal\MedalConfig::TASK_FRIENDS_REG);
        } else {
            $res = $this->getUserIdByInviteCode('db_p2p.tb_invitecodes_1');
            !empty($res) && $this->setMedal($res, 1, \Lib\Medal\MedalConfig::TASK_FRIENDS_REG);
        }
    }

    private function getUserIdByInviteCode($tbname = 'db_p2p.tb_invitecodes_0') {
        $this->tbname = $tbname;
        return $this->setWhere(['inviteCode' => $this->invitationCode])->getRecord();
    }

}
