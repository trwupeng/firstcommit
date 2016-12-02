<?php
/**
 * 个人账户接口
 * @author hand
 */
class AccountController extends \Prj\UserCtrl{
    public function testAction()
    {
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
    }
    protected function onInit_chkLogin()
    {
        @parent::onInit_chkLogin();
        $this->loger->isLogined = 1;
    }





}
