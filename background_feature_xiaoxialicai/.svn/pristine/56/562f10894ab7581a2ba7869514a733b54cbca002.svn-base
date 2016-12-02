<?php

use Sooh\Base\Form\Item as form_def;
use Lib\Medal\Medal;

/**
 * Description of Medal
 *
 * @author wu.chen
 */
class MedalController extends \Prj\ManagerCtrl {

    protected $pageSizeEnum = [30, 50, 100];

    public function indexAction() {
        $pageid = $this->_request->get('pageId', 1);
        $pagesize = $this->_request->get('pageSize', current($this->pageSizeEnum));
        $pager = new \Sooh\DB\Pager($pagesize, $this->pageSizeEnum);
        $frm = \Sooh\Base\Form\Broker::getCopy('default')
                ->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_s);
        $frm->addItem('_key_lk', form_def::factory('勋章英文标识', '', form_def::text))
                ->addItem('pageid', $pageid)
                ->addItem('pagesize', $pager->page_size);
        $frm->fillValues();
        $where = $res = [];
        if ($frm->flgIsThisForm) {
            $where = $frm->getWhere();
        }
        $medal = new Medal();
        $pager->total = $medal->setWhere($where)->getRecordsCount();
        if ($pager->total) {
            $res = $medal->getRecords(($pageid == 1 ? 0 : $pageid - 1), $pageSize);
        }
        $headers = array('英文标识' => 70, '勋章名称' => 70, '序列' => 20, '勋章描述' => 50, '状态' => 30);
        $this->_view->assign('where', $where); //tgh
        $this->_view->assign('headers', $headers);
        $this->_view->assign('res', $res);
        $this->_view->assign('pager', $pager);
    }

    public function editAction() {
        $id = $this->_request->get('_pkey_val_', FALSE);
        $res = FALSE;
        if ($id) {
            $id = Prj\Misc\View::decodePkey($id);
            $medal = new Medal();
            $res = $medal->setWhere(['id' => $id])->getRecord();
        }
        $this->_view->assign('rewardTypes', \Lib\Medal\MedalConfig::getRewardType());
        $this->_view->assign('res', $res);
    }

    public function saveAction() {
        $id = $_POST['id'];
        $arr = [];
        $arr['`key`'] = $_POST['key'];
        $arr['name'] = $_POST['name'];
        $arr['icon'] = $_POST['icon'];
        $arr['description'] = $_POST['description'];
        $arr['serialNumber'] = $_POST['serialNumber'];
        $arr['task'] = $_POST['task'];
        $arr['taskLevel'] = $_POST['taskLevel'];
        $arr['taskUnit'] = $_POST['taskUnit'];
        $arr['reward'] = $_POST['reward'];
        $arr['rewardName'] = $_POST['rewardName'];
        $arr['rewardUnit'] = $_POST['rewardUnit'];
        $arr['rewardNumUnit'] = $_POST['rewardNumUnit'];
        $arr['status'] = $_POST['status'];
        $medal = new Medal();
        if (!$id) {
            $arr['createTime'] = time();
            $res = $medal->save($arr);
        } else {
            $res = $medal->setWhere(['id' => $id])->updateRec($arr);
        }
        $this->closeAndReloadPage($this->tabname('index'));
        $this->returnOK('操作成功');
        return;
    }

}
