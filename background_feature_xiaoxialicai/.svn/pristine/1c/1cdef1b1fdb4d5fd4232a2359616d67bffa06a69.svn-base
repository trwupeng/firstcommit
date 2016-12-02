<?php
/**
 * @param 提供设置状态位接口
 * 
 * @author wupeng
 * 
 * */
class ClientflgsController extends \Prj\UserCtrl{

//    public function init()
// 	{
// 		error_log('>>TODO: 删掉ClientflgsController里的init()');
// 		parent::init();
// 		$userId='11909963963636';
// 		$sess = \Sooh\Base\Session\Data::getInstance();
// 		$sess->set('accountId',$userId);
// 	}
   
// 	function  donothingAction()
// 	{
	
// 	}

    
    public function  seteverflgAction(){
     $userId=\Sooh\Base\Session\Data::getInstance()->get('accountId');
     $user=\Prj\Data\User::getCopy($userId);
     $user->load();
     $records = \Prj\Misc\ClientFlgs::getCurrent($userId);
     $records['ever'][$this->_request->get('k')]=$this->_request->get('v');
     $user->setField(\Prj\Misc\ClientFlgs::field, $records);
     $user->update();
    }
    
    public function  setdailyflgAction(){
        $userId=\Sooh\Base\Session\Data::getInstance()->get('accountId');
        $user=\Prj\Data\User::getCopy($userId);
        $user->load();
        $records=\Prj\Misc\ClientFlgs::getCurrent($userId);
        $records['daily'][$this->_request->get('k')]=$this->_request->get('v');
       $user->setField(\Prj\Misc\ClientFlgs::field, $records);
       $user->update();
    }
   
}