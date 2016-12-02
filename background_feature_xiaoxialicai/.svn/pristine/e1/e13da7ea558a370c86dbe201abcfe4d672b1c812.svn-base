<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Lib\Medal;

/**
 * Description of UserMedal
 *
 * @author wu.chen
 */
class UserMedal extends \Lib\Medal\MedalBase {

    public function __construct() {
        parent::__construct();
        $this->tbname = 'db_p2p.tb_user_medal';
    }

    public function getUserMedalByIds($ids) {
        $result = FALSE;
        if ($ids) {
            $ids = implode(',', $ids);
            $sql = "select * from {$this->tbname} where userId in ({$ids})";
            $result = $this->db->execCustom(['sql' => $sql]);
            $result = $this->db->fetchAssocThenFree($result);            
        }
        return $result;
    }

}
