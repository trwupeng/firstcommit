<?php
/**
 * @param  查询状态接口
 *
 * @author wupeng
 *
 * */
class ClientFlgs{
    public function run($view,$request,$response=null){
        $userId=\Sooh\Base\Session\Data::getInstance()->get('accountId');
        $user=\Prj\Data\User::getCopy($userId);
        $user->load();
        if(!$user->exists()){
            $view->assign('ClientFlgs',[
                'ever'=>[],
                'daily'=>[],
            ]);
            return;
        }
        $records=\Prj\Misc\ClientFlgs::getCurrent($userId);

        $view->assign('ClientFlgs',
            [
                'ever'=>$records['ever'],
                'daily'=>$records['daily'],
                 
            ]);



    }
}
