<?php

namespace Prj;

/**
 * 
 */
class ManagerCtrl extends \Prj\BaseCtrl {

//	protected function getFromRaw()
//	{
//		$s = file_get_contents('php://input');
//		if(!empty($s)){
//			parse_str($s,$inputs);
//			return $inputs;
//		}else{
//			return $inputs=array();
//		}
//	}
    public function init() {
        define('SOOH_USE_REWRITE', 1);
        parent::init();
        $this->onInit_chkLogin();
        \Sooh\Base\Log\Data::addWriter(new \Sooh\Base\Log\Writers\Database('dbgrpForLog', 1, false), 'evt', 0);
        //$render = $this->ini->viewRenderType();
    }

    protected function tabname($act = null, $ctrl = null, $mod = null) {
        if ($act === null) {
            $act = $this->_request->getActionName();
        }
        if ($ctrl === null) {
            $ctrl = $this->_request->getControllerName();
        }
        if ($mod === null) {
            $mod = $this->_request->getModuleName();
        }
        return strtolower("{$mod}_{$ctrl}_{$act}");
//		$ret = $this->manager->acl->getMenuPath($act,$ctrl,$mod);
//		if($ret){
//			$tmp=explode('.',$ret);
//			return 'page_'.array_pop($tmp);
//		}else{
//			throw new \ErrorException("unknown tabname for $mod/$ctrl/$act");
//		}
    }

    protected $pageSizeEnum = [10, 50, 100];

    protected function useJsonIfNotSet() {
        $tmp = $this->ini->viewRenderType();
        if ($tmp !== 'json' && $tmp !== 'jsonp') {
            $this->ini->viewRenderType('json');
        }
    }

    protected function returnError($msg = '', $code = 300) {
        var_log('[error]' . $msg);
        $this->useJsonIfNotSet();
        $this->_view->assign('statusCode', $code);
        if (!empty($msg)) {
            $this->_view->assign('message', $msg);
        }
    }

    protected function returnOK($msg = '', $code = 200) {
        $this->useJsonIfNotSet();
        $this->_view->assign('statusCode', $code);
        if (!empty($msg)) {
            $this->_view->assign('message', $msg);
        }
    }

    /**
     * 关闭当前页面或窗口，如果指定了$tabPageId，则刷新对应的tab页
     * @param string $tabPageId
     */
    protected function closeAndReloadPage($tabPageId = null) {
        //$this->_view->assign ('callbackType', 'closeCurrent');
        $this->_view->assign('closeCurrent', true);
        if ($tabPageId) {
            //$this->_view->assign ('navTabId', $tabPageId);
            $this->_view->assign('tabid', $tabPageId);
            //$this->_view->assign ('tabid', '');
        }
    }

    protected function downExcel($records, $title = null, $filename = null, $scientificFlg = true) {
        if ($filename === null) {
            $filename = str_replace('page_', '', $this->tabname()) . '_' . date('Y_m_d');
        }
        $this->ini->viewRenderType('echo');
        header("Pragma:public");
        header("Expires:0");
        header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type:application/vnd.ms-execl;charset=gb2312");
        header("Content-Type:application/octet-stream");
        header("Content-Type:application/download");
        header('Content-Disposition:attachment;filename="' . $filename . '.xls"');
        header("Content-Transfer-Encoding:binary");
        reset($records);
        if (empty($title)) {
            $title = array_keys(current($records));
        }
        echo iconv('utf-8', 'gbk', implode("\t", $title)) . "\n";
        foreach ($records as $r) {
            foreach ($r as $k => $v) {
                if ($scientificFlg && is_numeric($v) && $v > 99999999999) {
                    $r[$k] = 'ID:' . $v;
                } else {
                    $r[$k] = $v;
                }
                if (substr($k, 0, 1) == '_')
                    unset($r[$k]);
            }
            echo iconv('utf-8', 'gbk//TRANSLIT', implode("\t", $r)) . "\n";
        }
    }

    protected function getInputs() {
        return array_merge($this->_request->getQuery(), $this->_request->getPost(), $this->_request->getParams());
    }

    protected function getManagerLog($managerId, $pageid) {
        return array(
            array('managerId' => '191704475514345770', 'evt' => 'login'),
            array('managerId' => '502774624898912753', 'evt' => 'logout'),
        );
    }

    protected function onInit_chkLogin() {
        $this->session = \Sooh\Base\Session\Data::getInstance();
        if ($this->session) {
            $userId = $this->session->get('managerId');
            if ($userId) {
                list($loginName, $cameFrom) = explode('@', $userId);
                $this->manager = \Prj\Data\Manager::getCopy($loginName, $cameFrom);
                $this->manager->load();
                $this->loger->userId = $loginName;
                if ($this->manager->getField('dtForbidden') > \Sooh\Base\Time::getInstance()->timestamp()) {
                    $this->ini->viewRenderType('json');
                    throw new \ErrorException(\Prj\ErrCode::errNoRights, 300);
                }
                $this->manager->rights = $this->getRights();
                $wapMenu = [];
                $m = strtolower($this->_request->getModuleName());
                $c = strtolower($this->_request->getControllerName());
                $curView = 'wap';
                $wapMenu['__'] = '个人中心';
                if ($this->manager->acl->hasRightsFor('report', 'rptdailybasic')) {
                    $wapMenu['日报(整合)'] = \Sooh\Base\Tools::uri(['__VIEW__' => $curView], 'recent', 'rptdailybasic', 'report');
                    if ($m == 'report' && $c == 'rptdailybasic') {
                        $wapMenu['__'] = '日报';
                    }
                    $wapMenu['日报(数字)'] = \Sooh\Base\Tools::uri(['__VIEW__' => $curView], 'recent2', 'rptdailybasic', 'report');
                    if ($m == 'report' && $c == 'rptdailybasic') {
                        $wapMenu['__'] = '日报';
                    }
                }
                error_log("手机版月报菜单暂时关闭");
//				if($this->manager->acl->hasRightsFor('report', 'monthreport')){
//                    $wapMenu['月报'] = \Sooh\Base\Tools::uri(['__VIEW__'=>$curView], 'recent', 'monthreport', 'report');
//					if($m=='report' && $c=='monthreport'){
//						$wapMenu['__']='月报';
//					}
//				}
//				if($this->manager->acl->hasRightsFor('report', 'rptconf')){
//                    $wapMenu['报表权限'] = \Sooh\Base\Tools::uri(['__VIEW__'=>$curView], 'conf', 'rptconf', 'report');
//					if($m=='report' && $c=='rptconf'){
//						$wapMenu['__']='报表权限';
//					}
//				}
                $wapMenu['个人中心'] = \Sooh\Base\Tools::uri(['__VIEW__' => $curView], 'welcome', 'manager', 'manage');
                if ($this->ini->viewRenderType() == 'wap') {
                    $this->_view->assign('wapMenuList', $wapMenu);
                }
//				if(!$this->manager->acl->hasRightsFor($this->_request->getModuleName(), $this->_request->getControllerName())){
                if (!$this->manager->acl->hasRightsFor($m, $c)) {
//				$this->manager->rights = \Sooh\Base\Session\Data::getInstance()->get('rights')?\Sooh\Base\Session\Data::getInstance()->get('rights'):[];
                    //$this->returnError(\Prj\ErrCode::errNoRights,300);
                    throw new \ErrorException(\Prj\ErrCode::errNoRights, 300);
                }
            } else {
                //$this->returnError(\Prj\ErrCode::errNotLogin,301);
                throw new \ErrorException(\Prj\ErrCode::errNotLogin, 301);
            }
        }
    }

    //tgh
    public function getImageAction() {
        $this->ini->viewRenderType('echo');
        $fileId = $this->_request->get('fileId');
        header('Content-type: image/jpg');
        echo \Prj\Data\Files::getDataById($fileId);
    }

    /**
     * 要求的权限
     * hand 160113
     */
    protected function needRights($str) {
        if (in_array('*', $this->manager->rights))
            return;
        if (is_array($str)) {
            if (!count(array_intersect($str, $this->manager->rights))) {
                \Sooh\Base\Ini::getInstance()->viewRenderType('echo');
                echo "没有权限！";
            }
        } else {
            if (!in_array($str, $this->manager->rights)) {
                \Sooh\Base\Ini::getInstance()->viewRenderType('echo');
                echo "没有权限！";
            }
        }
    }

    /**
     * 数据库权限表完整度检查
     */
    /*
      protected function checkRightsDB(){
      var_log($this->rights,'this->rights>>>>');
      if(empty($this->rights))return;
      foreach($this->rights as $k=>$v){
      $conf = \Prj\Data\Rights::getCopy($k);
      $conf->load();
      if(!$conf->exists()){
      $conf->setField('rightsType',explode('_',$k)[0]);
      $conf->setField('rightsName',$v);
      try{
      $conf->update();
      }catch (\ErrorException $e){
      var_log($e->getMessage());
      }
      }
      }
      }
     */

    protected function getRights() {
        if (empty($this->modelName))
            return [];
        $loginName = $this->manager->getField('loginName');
        return \Prj\Data\ManagerRight::getRightsByType($loginName, $this->modelName);
    }

    /**
     *
     * @var \Sooh\Base\Session\Data 
     */
    protected $session = null;

    /**
     *
     * @var \Prj\Data\Manager
     */
    protected $manager = null;

//	protected function getUriBase()
//	{
//		return '/manage';
//	}

    /**
     * 导出EXCEL文件
     * @param $records 导出数据
     * @param $title 标题
     * @param $filename 导出文件名
     * @param $scientificFlg 标示
     * @paran ext
     */
    protected function downOutExcel($records, $title = null, $filename = null, $scientificFlg = true, $ext = '.xls') {
        if ($filename === null) {
            $filename = str_replace('page_', '', $this->tabname()) . '_' . date('Y_m_d');
        }
        $this->ini->viewRenderType('echo');
        header("Pragma:public");
        header("Expires:0");
        header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type:application/vnd.ms-execl;charset=gb2312");
        header("Content-Type:application/octet-stream");
        header("Content-Type:application/download");
        header('Content-Disposition:attachment;filename="' . $filename . $ext . '"');
        header("Content-Transfer-Encoding:binary");
        reset($records);
        if (empty($title)) {
            $title = array_keys(current($records));
        }
        echo iconv('utf-8', 'gbk', implode("\t", $title)) . "\n";
        foreach ($records as $r) {
            foreach ($r as $k => $v) {
                if ($scientificFlg && is_numeric($v) && $v > 99999999999) {
                    $r[$k] = 'ID:' . $v;
                } else {
                    $r[$k] = $v;
                }
                if (substr($k, 0, 1) == '_')
                    unset($r[$k]);
            }
            echo iconv('utf-8', 'gbk//TRANSLIT', implode("\t", $r)) . "\n";
        }
    }

    /**
     * 通过手机查询用户ID的action
     */
    public function phoneAction() {
        $phone = $this->_request->get('phone', FALSE);
        if ($phone) {            
            /**ajax查询返回**/
            $res = \Prj\Data\User::getByPhone($phone);
            if ($res) {
                $res = $res->userId;
            }
            echo $res;
            exit;
        }
        \Prj\Misc\View::phoneBox(\Sooh\Base\Tools::uri('phone')); //导入页面html
        exit;
    }

}
