<?php
/**
 * 管理员登入，登出
 * 
 */
class IndexController extends \Prj\ManagerCtrl{
    /**
     * 改造：没登入的情况下不丢异常
     */
    protected function onInit_chkLogin()
    {
        $this->session = \Sooh\Base\Session\Data::getInstance();
        if($this->session){
            $userId = $this->session->get('managerId');
            if (!empty($userId)){
                \Sooh\Base\Log\Data::getInstance()->userId = $userId;
                $this->manager = \Prj\Data\RptManagers::getCopyByManagerId($userId);
            }else{
                $this->manager = null;
            }
        }
    }
    
    public function indexAction() {
//        $this->manager = \Prj\Data\RptManagers::getCopyByManagerId('root@local');
       if($this->_request->get('dowhat')=='logout'){
           $this->_view->assign('useTpl','logout');
           \Sooh\Base\Session\Data::getInstance()->set('managerId', '');
       }elseif ($this->manager){
           $this->manager->load();
           $this->menu();
           $this->_view->assign('leftmenus', $this->manager->acl->getMenuMine());
           $this->_view->assign('useTpl', 'homepage');
       }else {
           $this->loginAction();
       }
    }
    
    // 初始化菜单
    protected function menu() {
        $menus = $this->manager->acl->getMenuMine();
        $this->_view->assign('menus', $menus);
    }
    
    public function loginAction () {
        $u = $this->_request->get('u');
        $p = $this->_request->get('p');
        $f = $this->_request->get('from', 'local');
        if (!empty($u) && !empty($p)) {
            $this->ini->viewRenderType('json');
            $acc = \Prj\Data\RptManagers::getCopy($u, $f);
            $acc->load();
            if($acc->exists() && $acc->getField('passwd')==$p){
                $sessionData=\Sooh\Base\Session\Data::getInstance();
                $sessionData->set('managerId',$u.'@'.$f);
                $this->loger->ret = 'login ok';
                $this->loger->ext = $u.'@'.$f;
                $this->returnOK();
            }else {
                $this->returnError(\Prj\Lang\Broker::getMsg('index.password_error'));
            }
        }else {
            $acc = \Prj\Data\RptManagers::getCopy('fdg');
            $n = $acc->db()->getRecordCount($acc->tbname());
            if ($n==0){
                $acc->db()->addRecord($acc->tbname(),['cameFrom'=>'local', 'loginName'=>'root', 'passwd'=>'123456']);
            }
            $this->_view->assign('useTpl', 'login');
        }
    }
    
    public function welcomeAction()
    {
    
    }
    
    public function resetpwdAction()
    {
    
    }
    
}
