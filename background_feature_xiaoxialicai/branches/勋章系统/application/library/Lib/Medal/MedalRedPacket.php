<?php

namespace Lib\Medal;

/**
 * Description of MedalRedPacket
 *
 * @author wu.chen
 */
class MedalRedPacket extends \Lib\Medal\Medal {

    protected $voucher;

    public function __construct() {
        parent::__construct();
    }

    public function setVoucher($voucher) {
        $this->voucher = $voucher;
        return $this;
    }

    public function logic() {
        if (!empty($this->voucher)) {
            foreach ($this->voucher as $voucher) {
                $pid = $voucher->getField('pid');
                if (!empty($pid)) {
                    $parVoucher = \Prj\Data\Vouchers::getCopy($pid);
                    $parVoucher->load();
                    if ($parVoucher->exists()) {
                        $this->setMedal($parVoucher->getField('userId'), 1, \Lib\Medal\MedalConfig::TASK_SHARE_REDPACKET);    //好友已使用分享红包个数(子红包)
                    }
                }
                $this->setMedal($this->userId, $voucher->getField('amount'), \Lib\Medal\MedalConfig::TASK_USER_REDPACKET);    //使用红包累计金额
            }
        }
    }
    
    public function logicPro($type) {        
        $this->setMedal($this->userId, $this->num, $type);
    }

}
