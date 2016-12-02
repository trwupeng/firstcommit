<?php
/**
 * Created by PhpStorm.
 * User: LTM <605415184@qq.com>
 * Date: 2016/6/2
 * Time: 16:11
 */

use Sooh\Base\Form\Item as FItem;
use Sooh\Base\Form\Broker as FBroker;

/**
 * 红包延期工具
 * Class VoucherdelayController
 */
class VoucherdelayController extends \Prj\ManagerCtrl
{
    public function indexAction()
    {
        $fieldsMapArr = [
            'voucherId'  => ['券ID', '180'],
            'userId'     => ['用户ID', '130'],
            'amount'     => ['金额（元）', '80'],
            'codeCreate' => ['券码', '120'],
            'descCreate' => ['券名', '120'],
            'timeCreate' => ['创建时间', '180'],
            'dtExpired'  => ['过期时间', '180'],
            'exp1'       => ['使用限制', '150'],
            'exp2'       => ['详细限制', '150'],
        ];
        $where = \Prj\Misc\View::decodePkey($this->_request->get('_pkey_val_'));
        $isDownloadExcel = $this->_request->get('__EXCEL__');
        $frm = FBroker::getCopy('default')
            ->init(\Sooh\Base\Tools::uri(), 'get', empty($where) ? FBroker::type_c : FBroker::type_u);

        $frm->addItem('loginName', FItem::factory('手机号', '', FItem::text, []));
        $frm->fillValues();

        $header = [];
        foreach ($fieldsMapArr as $k => $v) {
            $header[$v[0]] = $v[1];
        }

        if ($frm->flgIsThisForm) {
            $fields = $frm->getFields();
            $loginName = $fields['loginName'];

            if (empty($loginName)) {
                $this->_view->assign('data', ['code' => 300, 'msg' => '请填写参数']);
                return $this->returnOK('exception');
            }

            $dbLogin = \Sooh\DB\Cases\AccountAlias::getCopy([$loginName, 'phone']);
            $dbLogin->load();
            if ($dbLogin->exists()) {
                $userId = $dbLogin->getField('accountId');
            } else {
                $this->_view->assign('data', ['code' => 400, 'msg' => '找不到此用户']);
                return $this->returnOK('exception');
            }

            //获取此用户的红包
            $dbVoucher = \Prj\Data\Vouchers::getCopy($userId);
            $time = \Sooh\Base\Time::getInstance();
            $map = [
                'userId' => $userId,
                'voucherType' => \Prj\Consts\Voucher::type_real,
                'statusCode' => \Prj\Consts\Voucher::status_unuse,
                'dtExpired>' => $time->YmdFull,
            ];

            $_db = $dbVoucher->db();
            $_tbName = $dbVoucher->tbname();

            $list = $_db->getRecords($_tbName, '*', $map, 'rsort dtExpired sort voucherId');



            $ids = $this->_request->get('ids');
            $tmp = [];
            if (!empty($ids)) {
                foreach ($ids as $v) {
                    $tmp[] = \Prj\Misc\View::decodePkey($v)['voucherId'];
                }
            }

            $new = [];
            foreach ($list as $v) {
                if (!empty($ids)) {
                    if (!in_array($v['orderId'], $tmp)) {
                        continue;
                    }
                }

                foreach ($fieldsMapArr as $kk => $vv) {
                    if ($kk == 'amount') {
                        $temp[$kk] = sprintf('%.02f', $v[$kk] / 100);
                    } elseif ($kk == 'timeCreate' || $kk == 'dtExpired') {
                        $temp[$kk] = !empty($v[$kk]) ? date('Y-m-d H:i:s', strtotime($v[$kk])) : '';
                    } else {
                        $temp[$kk] = $v[$kk];
                    }

                    if (empty($temp[$kk])) {
                        $temp[$kk] = '';
                    }
                }
                $temp['_pkey_val_'] = \Prj\Misc\View::encodePkey(['voucherId' => $v['voucherId']]);

                $new[] = $temp;
            }
            $rs = $new;

            if ($isDownloadExcel) {
                foreach ($rs as $k => $v) {
                    unset($rs[$k]['_pkey_val_']);
                }
                return $this->downExcel($rs, array_keys($header), null, true);
            }
        }
        $this->_view->assign('header', $header);
        $this->_view->assign('rs', isset($rs) ? $rs : []);
        $this->_view->assign('where', $where);
    }

    public function delayAction()
    {
        $hours = $this->_request->get('hours');

        if (is_numeric($hours) && $hours > 0) {
            $arrPkey = \Prj\Misc\View::decodePkey($this->_request->get('_pkey_val_'));
            if (isset($arrPkey['voucherId'])) {
                $voucherId = $arrPkey['voucherId'];
                $dbVoucher = \Prj\Data\Vouchers::getCopy($voucherId);
                $dbVoucher->load();
                if ($dbVoucher->exists() && $dbVoucher->getField('statusCode') == \Prj\Consts\Voucher::status_unuse) {
                    $dtExpired = strtotime($dbVoucher->getField('dtExpired'));
                    var_log($dtExpired, 'dtExpired');
                    $now = \Sooh\Base\Time::getInstance()->timestamp();
                    var_log($now, 'now');

                    if ($dtExpired > $now) {
                        $dbVoucher->setField('dtExpired', date('YmdHis', $dtExpired + $hours * 3600));
                    } else {
                        $dbVoucher->setField('dtExpired', date('YmdHis', $now + $hours * 3600));
                    }
                    $dbVoucher->update();
                    return $this->returnOK('延期成功');
                }
            }
            return $this->returnError('延期失败');
        } else {
            return $this->returnError('时间参数不正确');
        }
    }

    /**
     * 输入延期时间
     */
    public function delayhoursAction()
    {
        $frm   = FBroker::getCopy('default')->init(\Sooh\Base\Tools::uri(), 'get', FBroker::type_c);
        $frm->addItem('hours', FItem::factory('自定义延期（小时）', '', FItem::text));
        $this->_view->assign('FormOp', $op = '确定');
        $frm->fillValues();
        $frm->flgIsThisForm;
        $frm->getWhere();
        $this->_view->assign('_pkey_val_', $this->_request->get('_pkey_val_'));
    }
}