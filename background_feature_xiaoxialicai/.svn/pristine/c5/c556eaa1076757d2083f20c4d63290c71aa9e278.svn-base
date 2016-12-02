<?php
/**
 * 接收合作方安装app通知
 *
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/7/1 0001
 * Time: 上午 10:34
 */

class ApicopartnernotifyController extends Yaf_Controller_Abstract {

    protected $oCopartner;

    public function init()
    {
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $copartnerAbs = $this->_request->get('copartnerabs');
        if(empty($copartnerAbs)) {
            $this->_view->assign('success', false);
            $this->_view->assign('message', 'copartnerabs is empty');
            return;
        }else{
            $this->oCopartner = \Lib\Api\Notify\Base::getByCopartnerAbsOrId($copartnerAbs);
            if($this->oCopartner===false) {
                $this->_view->assign('success', false);
                $this->_view->assign('message', 'copartner not found');
                return;
            }
        }
    }

    public function installednotifyAction () {
        $rs = $this->oCopartner->onInstalled($this->_request->getQuery());
        $this->_view->assign($rs);
    }

}