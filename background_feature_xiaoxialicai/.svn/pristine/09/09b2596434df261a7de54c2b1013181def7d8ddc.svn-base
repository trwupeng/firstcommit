<?php

/**
 * Description of Licence2
 *
 * @author wu.chen
 */
class Licence2Controller extends LicenceController {

    /**
     * 注册协议审核版
     */
    public function registerAction() {
        echo \Prj\Misc\Licence2::register();
    }

    /**
     * 绑卡协议审核版
     */
    public function bindingAction() {
        echo \Prj\Misc\Licence2::binding();
    }

    /**
     * 购买理财协议审核版
     */
    public function newinvestAction() {
        $type = $this->_request->get('type');
        $amount = $this->_request->get('amount');
        $ordersId = $this->_request->get('ordersId');
        $waresId = $this->_request->get('waresId');

        echo \Prj\Misc\Licence2::getInvest($type, $amount, $ordersId, $waresId);
    }

    /**
     * 充值许可协议审核版
     */
    public function newrechargesAction() {
        $type = $this->_request->get('type');
        $amount = $this->_request->get('amount');
        $bankCard = $this->_request->get('bankCard');
        $uid = \Sooh\Base\Session\Data::getInstance()->get('accountId');
        echo \Prj\Misc\Licence2::getCharges($type, $amount, $bankCard, $uid);
    }

}
