<?php
/**
 * Created by PhpStorm.
 * User: LTM <605415184@qq.com>
 * Date: 2016/5/24
 * Time: 10:12
 */

use Sooh\Base\Form\Item as FItem;
use Sooh\Base\Form\Broker as FBroker;

class ChargeBackController extends \Prj\ManagerCtrl
{
    /**
     * 检查是否可以退款
     * @throws ErrorException
     * @throws \Sooh\Base\ErrorException
     */
    public function createAction()
    {
        $where = \Prj\Misc\View::decodePkey($this->_request->get('_pkey_val_'));
        $frm   = FBroker::getCopy('default')
            ->init(\Sooh\Base\Tools::uri(), 'get', empty($where) ? FBroker::type_c : FBroker::type_u);

        $frm->addItem('userId', FItem::factory('用户ID', '', FItem::text, [], ['data-rule' => 'required,length[~14]']))
            ->addItem('orderId', FItem::factory('订单号', '', FItem::text, [], ['data-rule' => 'required']))
//            ->addItem('amount', FItem::factory('退款金额(单位元)', '', FItem::text, [], ['data-rule' => 'required']))
            ->addItem('reason', FItem::factory('退款原因', '', FItem::text, [], ['data-rule' => 'required']));
        $frm->fillValues();

        if ($frm->flgIsThisForm) {
            $fields  = $frm->getFields();
            $userId  = $fields['userId'];
            $orderId = $fields['orderId'];
            $reason  = $fields['reason'];
//            $amount  = round(($fields['amount'] + 0) * 100);
            if (empty($userId) || empty($orderId) || empty($reason)) {
                return $this->returnError('必要参数不能为空');
            }

            $dbUser = \Prj\Data\User::getCopy($userId);
            $dbUser->load();
            if ($dbUser->exists() == false) {
                return $this->returnError('用户不存在');
            }

            $dbOrder = \Prj\Data\Investment::getCopy($orderId);
            $dbOrder->load();
            if ($dbOrder->exists() == false || !in_array($dbOrder->getField('orderStatus'), [-1, 4, -4])) {
                return $this->returnError('订单不存在或不允许退款');
            } else {
                if (!($dbOrder->getField('chargeBackStatus') == 0 || $dbOrder->getField('chargeBackStatus') == 4)) {
                    return $this->returnError('订单正在退款中或者退款成功');
                }
                if ($dbOrder->getField('userId') != $userId) {
                    return $this->returnError('订单与用户不匹配');
                }
            }

//            if ($amount > $dbOrder->getField('amount')) {
//                return $this->returnError('退款金额太大了');
//            }

            $arrPkey = [
                'userId'  => $userId,
                'orderId' => $orderId,
//                'amount'  => $amount,
                'reason'  => $reason,
                'order'   => 'chargeback',
            ];
            $this->_view->assign('_pkey_val_', \Prj\Misc\View::encodePkey($arrPkey));
        } else {
            $this->_view->assign('ret', []);
        }
    }

    /**
     * 立即退款
     * @throws ErrorException
     */
    public function chargebackAction()
    {
        $where = \Prj\Misc\View::decodePkey($this->_request->get('_pkey_val_'));
        if (empty($where)) {
            return $this->returnError('参数错误');
        }

        $userId  = $where['userId'];
        $orderId = $where['orderId'];
        $reason  = $where['reason'];
//        $amount  = $where['amount'] + 0;
        if (empty($userId) || empty($orderId) || empty($reason)) {
            return $this->returnError('必要参数不能为空');
        }

        $dbUser = \Prj\Data\User::getCopy($userId);
        $dbUser->load();
        if ($dbUser->exists() == false) {
            return $this->returnError('用户不存在');
        }

        $dbOrder = \Prj\Data\Investment::getCopy($orderId);
        $dbOrder->load();
        if ($dbOrder->exists() == false || !in_array($dbOrder->getField('orderStatus'), [-1, 4, -4])) {
            return $this->returnError('订单不存在或不允许退款');
        } else {
            if (!($dbOrder->getField('chargeBackStatus') == 0 || $dbOrder->getField('chargeBackStatus') == 4)) {
                return $this->returnError('订单正在退款中或者退款成功');
            }
            if ($dbOrder->getField('userId') != $userId) {
                return $this->returnError('订单与用户不匹配');
            }
        }

//        if ($amount > $dbOrder->getField('amount')) {
//            return $this->returnError('退款金额太大了');
//        }

        $ret = \Prj\Data\Chargeback::createNew($orderId, $userId, $reason);
        \Prj\Data\UserChangeLog::addLog('chargeBack', $orderId, json_encode(['orderId' => $orderId, 'userId' => $userId, 'reason' => $reason]));
        if (json_decode($ret, true)) {
            $ret = json_decode($ret, true);
        }
        if ($ret == '') {
            return $this->returnOK('请求已受理，等待网关处理中！');
        } else if (is_string($ret)) {
            return $this->returnError($ret);
        } else {
            return $this->returnError(is_array($ret) ? $ret['msg'] : '失败');
        }
    }

    /**
     * 退款列表
     * @throws \Sooh\Base\ErrException
     * @throws \Sooh\Base\ErrorException
     */
    public function listAction()
    {
        $pageId           = $this->_request->get('pageId', 1) - 0;
        $isDownloadExcel  = $this->_request->get('__EXCEL__');
        $pager            = new \Sooh\DB\Pager(10, $this->pageSizeEnum, false);
        $pager->page_size = $this->_request->get('pageSize', 50);
        $pager->init(-1, $pageId);
        $frm = FBroker::getCopy('default')->init(\Sooh\Base\Tools::uri(), 'get', FBroker::type_s);
        $frm->addItem('_orderId_eq', FItem::factory('订单号', '', FItem::text))
            ->addItem('_userId_eq', FItem::factory('用户ID', '', FItem::text))
            ->addItem('pageid', $pageId)
            ->addItem('pagesize', $this->pager->page_size);
        $frm->fillValues();
        if ($frm->flgIsThisForm) {
            $where = $frm->getWhere();
            if ($_pkey = $this->_request->get('_pkey_val_')) {

            }
        } else {
            $where = [];
        }

        $fieldsMapArr = [
            'orderId'     => ['订单ID', '150'],
            'userId'      => ['用户ID', '120'],
//            'amount'      => ['金额(单位元)', '70'],
            'reason'      => ['退款理由', '150'],
            'createTime'  => ['创建时间', '130'],
            'transTime'   => ['网关处理时间', '130'],
            'sn'          => ['网关SN', '150'],
            'serviceRet'  => ['网关消息', '250'],
            'serviceCode' => ['网关处理状态', '80'],
            'status'      => ['最终状态', '100'],
        ];
        $search       = $this->_request->get('where');
        if (!empty($search)) {
            $where = array_merge($where, $search);
        }

        $total        = \Prj\Data\Chargeback::loopGetRecordsCount($where);
        $pager->total = $total;

        if ($isDownloadExcel) {
            $rs = \Prj\Data\Chargeback::loopAll($where);
        } else {
            if ($pager->pageid() == 1) {
                $ret = \Prj\Data\Chargeback::loopGetRecordsPage(['createTime' => 'rsort'], ['where' => $where], $pager);
            } else {
                $lastPage = \Sooh\Base\Session\Data::getInstance()->get('lyq_lastPage');
                $ret      = \Prj\Data\Chargeback::loopGetRecordsPage(['createTime' => 'rsort'], $lastPage, $pager);
            }
            \Sooh\Base\Session\Data::getInstance()->set('lyq_lastPage', $ret['lastPage']);
            $rs = $ret['records'];
        }

        $header = [];
        foreach ($fieldsMapArr as $k => $v) {
            $header[$v[0]] = $v[1];
        }

        $ids = $this->_request->get('ids');
        $tmp = [];
        if (!empty($ids)) {
            foreach ($ids as $v) {
                $tmp[] = \Prj\Misc\View::decodePkey($v)['orderId'];
            }
        }

        $new = [];
        foreach ($rs as $v) {
            if (!empty($ids)) {
                if (!in_array($v['orderId'], $tmp)) {
                    continue;
                }
            }

            foreach ($fieldsMapArr as $kk => $vv) {
                if($kk == 'createTime' || $kk == 'transTime') {
                    $temp[$kk] = !empty($v[$kk]) ? date('Y-m-d H:i:s', strtotime($v[$kk])) : '0';
                } else if($kk == 'status') {
                    switch ($v[$kk]) {
                        case 1: $temp[$kk] = '创建成功'; break;
                        case 4: $temp[$kk] = '已提交网关'; break;
                        case 8: $temp[$kk] = '网关处理完成'; break;
                        default: $temp[$kk] = '其他'; break;
                    }
                } else {
                    $temp[$kk] = $v[$kk];
                }
                if (empty($temp[$kk])) {
                    $temp[$kk] = '';
                }
            }

            $temp['_pkey_val_'] = \Prj\Misc\View::encodePkey(['orderId' => $v['orderId']]);

            $new[] = $temp;
        }
        $rs = $new;

        if ($isDownloadExcel) {
            foreach ($rs as $k => $v) {
                unset($rs[$k]['_pkey_val_']);
            }
            return $this->downExcel($rs, array_keys($header), null, true);
        }
        $this->_view->assign('header', $header);
        $this->_view->assign('rs', $rs);
        $this->_view->assign('where', $where);
        $this->_view->assign('pager', $pager);
    }

    /**
     * 根据手机号查找UserID
     * @throws \Sooh\Base\ErrorException
     */
    public function phoneAction()
    {
        $phone = $this->_request->get('phone');
        $frm   = FBroker::getCopy('default')->init(\Sooh\Base\Tools::uri(), 'get', FBroker::type_c);
        $frm->addItem('userId', FItem::factory('用户id', '', FItem::text));
        $this->_view->assign('FormOp', $op = '查找');
        $frm->fillValues();
        $frm->flgIsThisForm;
        $frm->getWhere();
        $fields = $frm->getFields();
        $userid = $fields['userId'];
        if (!empty($phone)) {
            $userId = \Prj\Data\User::loopFindRecordsByFields(['phone' => $phone], 'userId');
        }
        $records = [];

        foreach ($userId as $r) {
            $userid  = $r['userId'];
            $records = $userid;

        }
        $this->_view->assign('rs', $records);
        $this->_view->assign('phone', $phone);
    }
}