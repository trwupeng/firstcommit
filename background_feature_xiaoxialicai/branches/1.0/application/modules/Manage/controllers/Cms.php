<?php
use Sooh\Base\Form\Item as form_def;
use Prj\Data\Investment as Investment;

/**
 * cms
 * By Hand
 */
class CmsController extends \Prj\ManagerCtrl
{
    public function indexAction()
    {
        \Sooh\Base\Ini::getInstance()->viewRenderType('html');
        $dt = time();
        $sign = md5($dt.'tgh');
        $url = 'http://'.\Sooh\Base\Ini::getInstance()->get('cmsurl').'/comein/index1.php?dt='.$dt.'&sign='.$sign;
        var_log($url,'url>>>>>>>>>>>>>');
        $this->_view->assign('url',$url);
    }

    public function cmsLoginAction(){
        \Sooh\Base\Ini::getInstance()->viewRenderType('html');
        $url = 'http://'.Sooh\Base\Ini::getInstance()->get('cmsurl').'/index.php?m=admin&c=index&a=login&dosubmit=1';
        $key = md5('miaoji');
        $form = [
            'username'=>'cmsAdmin',
            'password'=>'miaoji2015',
            'key'=>$key,
        ];
        //\Prj\Tool\Func::curl_post($url,$form);

        $this->_view->assign('form',$form);
        $this->_view->assign('url',$url);
    }
}