<?php

namespace Lib\Medal;

/**
 * Description of MedalCheckin
 *
 * @author wu.chen
 */
class MedalCheckin extends \Lib\Medal\Medal {

    public function __construct() {
        parent::__construct();
        $this->num = 1;
    }

    public function logic() {
        $this->setMedal($this->userId, $this->num, \Lib\Medal\MedalConfig::TASK_CHECKIN);
    }

}
