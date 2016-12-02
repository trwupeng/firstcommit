<?php
namespace Lib\Medal;

/**
 * Description of MedalInvestment
 *
 * @author wu.chen
 */
class MedalInvestment extends \Lib\Medal\Medal {

    protected $amount;
    protected $type;
    public function __construct() {
        parent::__construct();
    }
    
    public function setAmount($amount) {
        $this->amount = $amount;
        return $this;
    }
    
    public function setType($type) {
        $this->type = $type;
        return $this;
    }

    public function logic() {
        $this->setMedal($this->userId, $this->amount, $this->type);// \Lib\Medal\MedalConfig::TASK_INVESTMENT);
    }

}
