<?php
/**
 * Created by PhpStorm.
 * User: tang.gaohang
 * Date: 2016/7/13
 * Time: 14:27
 */

class GetSinaStatus {
    public $user;
    public $_view;
    public $_request;

    public static function run($view, $request, $response = null){
        try{
            $tmp = new self($view, $request, $response = null);
        }catch (\ErrorException $e){
            return $view->assign(__CLASS__,[]);
        }
        $user = $tmp->user;
        $isSetPwd = $user->getField('isSetPwd')==1?1:0;
        if(!$isSetPwd){
            //去新浪查询
            $ret = $tmp->queryPayPwdStatus();
            if($ret['code'] == 200){
                $isSetPwd = $ret['data']['isSetPwd']?1:0;
                if($isSetPwd){
                    $user->setField('isSetPwd',1);
                    try{
                        $user->update();
                    }catch (\ErrorException $e){
                        error_log('queryPayPwdStatus#userId:'.$tmp->user->userId.'#'.$e->getMessage());
                    }
                }
            }elseif($ret['code'] == 400){
                $isSetPwd = -1;
                $isSetPwdMsg = $ret['msg'];
            }
        }
        if(!$isSetPwd){
            $isSetProxyAuth = 0;
        }else{
            $isSetProxyAuth = $user->getField('isSetProxyAuth')==1?1:0;
            if(!$isSetProxyAuth){
                $ret = $tmp->queryProxyAuth();
                if($ret['code'] == 200){
                    $isSetProxyAuth = $ret['data']['isSetProxyAuth']?1:0;
                    if($isSetProxyAuth){
                        $user->setField('isSetProxyAuth',1);
                        try{
                            $user->update();
                        }catch (\ErrorException $e){
                            error_log('queryProxyAuth#userId:'.$tmp->user->userId.'#'.$e->getMessage());
                        }
                    }
                }elseif($ret['code'] == 400){
                    $isSetProxyAuth = -1;
                    $isSetProxyAuthMsg = $ret['msg'];
                }
            }
        }
        $data['isCertificate'] = $user->getField('idCard')?1:0;
        $data['isSetPwd'] = $isSetPwd;
        $data['isSetPwdMsg'] = $isSetPwdMsg;
        $data['isSetProxyAuth'] = $isSetProxyAuth;
        $data['isSetProxyAuthMsg'] = $isSetProxyAuthMsg;
        $tmp->_view->assign(__CLASS__,$data);
    }

    public function __construct($view, $request, $response = null){
        $this->_view = $view;
        $this->_request = $request;
        $userId = \Sooh\Base\Session\Data::getInstance()->get('accountId');
        if(empty($userId))throw new \ErrorException('请先登录');
        $this->user = \Prj\Data\User::getCopy($userId);
        $this->user->load();
    }

    protected function queryPayPwdStatus(){
        $userId = $this->user->userId;
        try{
            $data = [$userId];
            $ret = \Lib\Services\PayGWCmd::sendToPayGWCmd('queryPayPwdStatus',$data);
        }catch (\ErrorException $e){
            error_log('queryPayPwdStatus#userId:'.$userId.'#'.$e->getMessage());
        }
        return $ret;
    }

    protected function queryProxyAuth(){
        $userId = $this->user->userId;
        try{
            $data = [$userId];
            $ret = \Lib\Services\PayGWCmd::sendToPayGWCmd('proxyAuth_query',$data);
        }catch (\ErrorException $e){
            error_log('proxyAuth_query#userId:'.$userId.'#'.$e->getMessage());
        }
        return $ret;
    }
}