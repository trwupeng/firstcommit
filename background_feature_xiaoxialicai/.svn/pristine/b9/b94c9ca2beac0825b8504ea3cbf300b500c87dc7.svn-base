<?php
use Sooh\Base\Form\Item as form_def;
use Prj\Data\User as User;
use Prj\Consts\OrderStatus as OrderStatus;
use Sooh\DB\Pager;

include_once __DIR__.'/User.php';
/**
 * 用户中心
 */
class UsertallyController extends \Prj\ManagerCtrl
{
    public function tallyAction()
    {
        $isDownloadEXCEL = $this->_request->get('__EXCEL__');
        //配置表格
        $fieldsMapArr = array(
            'tallyId'    => ['资金ID', '20'],
            'tallyType'  => ['类别', '20'],
            'userId'     => ['用户ID', '20'],
            'orderId'    => ['订单ID', '20'],
            'sn'         => ['支付ID', '20'],
            'nOld'       => ['变更前余额(元)', '20'],
            'nAdd'       => ['账户变动(元)', '20'],
            'nNew'       => ['变更后余额(元)', '20'],
            'timeCreate' => ['流水时间', '20'],
            'descCreate' => ['备注', '20'],
        );

        //配置分页
        $pageid = $this->_request->get('pageId', 1) - 0;
        $pager  = new \Sooh\DB\Pager($this->_request->get('pageSize', 50), $this->pageSizeEnum, false);
        $pager->init(-1, $pageid);

        //配置搜索项
        $frm = \Sooh\Base\Form\Broker::getCopy('default')->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_s);
        $frm->addItem('phone', form_def::factory('手机号/用户ID', '', form_def::text))
            ->addItem('_tallyType_eq', form_def::factory('类型', '', form_def::select, \Prj\Consts\OrderType::$enum + ['' => '']))
            ->addItem('pageId', $pageid)
            ->addItem('pageSize', $this->pager->page_size);
        $frm->fillValues();
        if ($frm->flgIsThisForm) {
            $where = $frm->getWhere();
            $form  = $frm->getFields();
            if (!empty($form['phone'])) {
                if (strlen($form['phone']) == 11) {
                    $user = \Prj\Data\User::getByPhone($form['phone']);
                    if ($user) {
                        $where['userId'] = $user->getField('userId');
                    } else {
                        var_log('不存在的手机号>>>');
                    }
                } else {
                    $where['userId'] = $form['phone'];
                }

            }
        } else {
            $where = array();
        }
        //合并表单的查询条件
        $search = \Prj\Misc\View::decodePkey($this->_request->get('where'));
        $where  = array_merge($search ? $search : [], $where);
        //拉取记录
        foreach ($fieldsMapArr as $kk => $vv) {
            $header[$vv[0]] = '';
        }
        $where['statusCode]'] = 0;
        var_log($where, '查询条件>>>>>>>>>>>>>>>>>>');
        if (!empty($where['userId'])) {
            if ($isDownloadEXCEL) {
                $tmp = \Prj\Data\WalletTally::getCopy($where['userId']);
                $tmp->load();
                $db     = $tmp->db();
                $tbname = $tmp->tbname();
                $rs     = $db->getRecords($tbname, '*', $where, 'rsort timeCreate');
            } else {
                $rs = \Prj\Data\WalletTally::pager($where['userId'], $pager, 0, 0, $where, 'rsort timeCreate');
            }
            //格式配置
            $tempArr = array();
            $newArr  = array();

            foreach ($rs as $k => $v) {
                //选中项打印
//                if ($ids == $this->_request->get('ids')) {
//                    $tmp = [];
//                    foreach ($ids as $vv) {
//                        $tmp[] = \Prj\Misc\View::decodePkey($vv)['ordersId'];
//                    }
//                    //todo 主键匹配
//                    if (!in_array($v['orderId'], $tmp)) {
//                        continue;
//                    }
//                }

                foreach ($fieldsMapArr as $kk => $vv) {
                    $tempArr[$kk] = $v[$kk];
                }
                //todo 数据格式化
                isset($tempArr['nOld']) && $tempArr['nOld'] /= 100;
                isset($tempArr['nAdd']) && $tempArr['nAdd'] /= 100;
                isset($tempArr['nNew']) && $tempArr['nNew'] /= 100;
                isset($tempArr['sn']) && $tempArr['sn'] = $tempArr['sn'] ? $tempArr['sn'] : '';
                isset($tempArr['timeCreate']) && $tempArr['timeCreate'] = date('Y-m-d H:i:s', strtotime($tempArr['timeCreate']));
                isset($tempArr['tallyType']) && $tempArr['tallyType'] = \Prj\Consts\OrderType::$enum[$tempArr['tallyType']];

                //===
                $newArr[] = $tempArr;
            }
            $rs = $newArr;
        }
        if ($isDownloadEXCEL) return $this->downEXCEL($rs, array_keys($header), null, true);
        //输出
        $this->_view->assign('rs', $rs);
        $this->_view->assign('header', $header);
        $this->_view->assign('pager', $pager);
        $this->_view->assign('where', \Prj\Misc\View::encodePkey($where));
    }
}