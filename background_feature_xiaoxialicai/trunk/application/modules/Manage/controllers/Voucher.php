<?php
use Sooh\Base\Form\Item as form_def;
use Prj\Data\Vouchers as Vouchers;

/**
 * Created by PhpStorm.
 * User: LTM <605415184@qq.com>
 * Date: 2015/11/10
 * Time: 17:17
 */
class VoucherController extends \Prj\ManagerCtrl {
	/**
	 * 券总览
	 */
	public function indexAction() {
		$fieldsMapArr = [
			'voucherId'   => ['券ID', '65'],
			'userId'      => ['用户ID', '45'],
			'voucherType' => ['类型', '20'],
			'amount'      => ['金额', '20'],
			'descCreate'  => ['来源', '20'],
			'timeCreate'  => ['获取时间', '45'],
			'dtExpired'   => ['过期时间', '45'],
			'dtUsed'      => ['使用日期', '45'],
			'orderId'     => ['订单号', '60'],
			'statusCode'  => ['状态', '20'],
		];

		$pageId           = $this->_request->get('pageId', 1) - 0;
		$pageSize         = $this->_request->get('pageSize', 10);
		$ids              = $this->_request->get('ids');
		$isDownloadExcel  = $this->_request->get('__EXCEL__') == 1;
		$search           = $this->_request->get('where', []);
		$pkey             = \Prj\Misc\View::decodePkey($this->_request->get('_pkey_val_'));
		$voucherType      = $this->_request->get('voucherType', '');
		$voucherStatusArr = [\Prj\Consts\Voucher::status_unuse => '未使用', \Prj\Consts\Voucher::status_used => '已使用', \Prj\Consts\Voucher::status_abandon => '已废弃',];
		$voucherTypeArr   = [\Prj\Consts\Voucher::type_real => '代金券', \Prj\Consts\Voucher::type_yield => '加息券', \Prj\Consts\Voucher::type_fake => '利息券',];

		//配置搜索项
		$frm = \Sooh\Base\Form\Broker::getCopy('default')->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_s);
		$frm->addItem('_userId_eq', form_def::factory('用户ID', $pkey['userId'] ? : '', form_def::text))
			->addItem('_orderId_eq', form_def::factory('订单号', '', form_def::text))
			->addItem('_voucherType_eq', form_def::factory('类型', $voucherType, form_def::select)->initMore(new \Sooh\Base\Form\Options($voucherTypeArr, '不限')))
			->addItem('_dtUsed_g2', form_def::factory('使用时间', '', form_def::datepicker))
			->addItem('_timeCreate_g2', form_def::factory('领取时间从', '', form_def::datepicker))
			->addItem('_timeCreate_l2', form_def::factory('到', '', form_def::datepicker))
			->addItem('_dtExpired_g2', form_def::factory('过期时间从', '', form_def::datepicker))
			->addItem('_dtExpired_l2', form_def::factory('到', '', form_def::datepicker))
			->addItem('_statusCode_eq', form_def::factory('状态', '', form_def::select)->initMore(new \Sooh\Base\Form\Options($voucherStatusArr, '不限')))
			->addItem('kvobjwhere','')
			->addItem('pageId', $pageId)
			->addItem('pageSize', $pageSize);
		if (!empty($pkey['userId']) || !empty($voucherType)) {
			$inputArr = [
				'_userId_eq' => $pkey['userId'],
			    '_voucherType_eq' => $voucherType,
			];
			foreach($inputArr as $v) {
				if (empty($v)) {
					unset($inputArr[$v]);
				}
			}

			$frm->fillValues(array_merge($_GET, $_POST, $inputArr));
			$frm->flgIsThisForm = true;
		} else {
			$frm->fillValues();
		}

		if ($frm->flgIsThisForm) {
			$where = $frm->getWhere();
		} else {
			$where = [];
		}

		foreach ($where as $k => $v) {
			if (strpos($k, 'timeCreate') !== false || strpos($k, 'dtExpired') !== false) {
				$where[$k] = date('YmdHis', strtotime($v));
			}
		}

		$keys = is_array($ids) ? $ids : explode(',', $ids);
		if (!empty($ids)) {
			foreach ($keys as $k => $v) {
				$keys[$k] = \Prj\Misc\View::decodePkey($v)['voucherId'];
			}
			$where = ['voucherId' => $keys];
		}

		if ($isDownloadExcel) {
			$where = array_merge($where, $search);
			$pager = new \Sooh\DB\Pager(-1);
			$pager->init(-1, -1);
		}else{
			$pager = new \Sooh\DB\Pager($pageSize, $this->pageSizeEnum, false);
			$pager->init(\Prj\Data\Vouchers::loopGetRecordsCount($where), $pageId);
		}

		$kvobjWhere = $frm->getFields()['kvobjwhere'];
		if(!empty($kvobjWhere)){
			$kvobjWhere = json_decode($kvobjWhere,true);
			$allmatch=true;//检查
			foreach($where as $k=>$v){
				if($kvobjWhere['where'][$k]!=$v){
					$allmatch=false;
				}
			}
			if($allmatch){
				$ret = \Prj\Data\Vouchers::loopGetRecordsPage(array('timeCreate'=>'rsort','voucherId'=>'sort'),  $kvobjWhere, $pager);
			}else{
				$ret = \Prj\Data\Vouchers::loopGetRecordsPage(array('timeCreate'=>'rsort','voucherId'=>'sort'),['where'=>$where], $pager);
			}
		}else{
			if ($isDownloadExcel) {
				unset($pager);
				$pager = new \Sooh\DB\Pager(1000);
				$pager->init(-1, -1);
//				$ret = \Prj\Data\Vouchers::loopFindRecords($where);
			}
			$allRet = \Prj\Data\Vouchers::loopGetRecordsPage(array('timeCreate'=>'rsort','voucherId'=>'sort'),['where'=>$where], $pager);
			$ret = $allRet;
		}
		$frm->resetValue('kvobjwhere', json_encode($ret['lastPage']));
		$header = [];
		foreach ($fieldsMapArr as $k => $v) {
			$header[$v[0]] = $v[1];
		}

		$temp = [];
		foreach ($ret['records'] as $v) {
			foreach ($fieldsMapArr as $kk => $vv) {
				if ($kk == 'voucherType') {
					$temp[$kk] = \Prj\Consts\Voucher::getName($v[$kk]);
				} elseif ($kk == 'statusCode') {
					$temp['status'] = $v[$kk];
					$temp[$kk] = \Prj\Consts\Voucher::getStatus($v[$kk]);
				} elseif ($kk == 'amount') {
					$temp[$kk] = sprintf('%.2f', $v[$kk] / 100);
				} elseif ($kk == 'descCreate') {
					if (!empty($v[$kk]) && !is_numeric($v[$kk])) {
						$temp[$kk] = $v[$kk];
					} else {
						$temp[$kk] = '其他';
					}
				} else {
					$temp[$kk] = $v[$kk];
				}
				if (!$isDownloadExcel) {
					$temp['_pkey_val_'] = \Prj\Misc\View::encodePkey(['voucherId' => $v['voucherId'], 'userId' => $v['userId']]);
				}
			}
			
			$temp['timeCreate'] = \Prj\Misc\View::fmtYmd($temp['timeCreate'],'time');
			$temp['dtExpired'] =  \Prj\Misc\View::fmtYmd($temp['dtExpired'],'time');
			$new[] = $temp;
		}
		$records = $new;

		if ($isDownloadExcel) {
			foreach ($records as $k => $v) {
				unset($records[$k]['status']);
			}
			return $this->downExcel($records, array_keys($header),null,true);
		} else {
			$this->_view->assign('where', $where);
			$this->_view->assign('pager', $pager);
			$this->_view->assign('header', $header);
			$this->_view->assign('rs', $records);
		}
	}

	/**
	 * 初始化券
	 */
	public function unuseAction() {
		$pkey = \Prj\Misc\View::decodePkey($this->_request->get('_pkey_val_'));
		$voucherId = $pkey['voucherId'];
		if (empty($voucherId)) {
			//return $this->returnError('no_voucherId');
			return $this->returnError(\Prj\Lang\Broker::getMsg('voucher.voucherId_error'));
		}

		$voucher = \Prj\Data\Vouchers::getCopy($voucherId);
		$voucher->load();
		try {
			if ($voucher->exists()) {
				$voucher->setField('statusCode', \Prj\Consts\Voucher::status_unuse);
				$voucher->update();
				return $this->returnOK('更改成功');
			} else {
				//return $this->returnError('not found voucher');
				return $this->returnError(\Prj\Lang\Broker::getMsg('voucher.voucher_unfound'));
			}
		} catch (\Exception $e) {
			return $this->returnError($e->getMessage(), $e->getCode());
		}
	}

	/**
	 * 禁用券
	 */
	public function abandonAction() {
		$pkey = \Prj\Misc\View::decodePkey($this->_request->get('_pkey_val_'));
		$voucherId = $pkey['voucherId'];
		if (empty($voucherId)) {
			//return $this->returnError('no_voucherId');
		    return $this->returnError(\Prj\Lang\Broker::getMsg('voucher.voucherId_error'));
		}

		$voucher = \Prj\Data\Vouchers::getCopy($voucherId);
		$voucher->load();
		try {
			if ($voucher->exists()) {
				$voucher->setField('statusCode', \Prj\Consts\Voucher::status_abandon);
				$voucher->update();
				return $this->returnOK('更改成功');
			} else {
				//return $this->returnError('not found voucher');
				return $this->returnError(\Prj\Lang\Broker::getMsg('voucher.voucher_unfound'));
			}
		} catch (\Exception $e) {
			return $this->returnError($e->getMessage(), $e->getCode());
		}
	}

    public function addVoucherAction(){
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $userId = $this->_request->get('userId');
        $voucherType = $this->_request->get('voucherType');
        $amount = $this->_request->get('amount');
        $expired = $this->_request->get('expired',3);
        $limitsShelf = $this->_request->get('limitsShelf','');
        $limitsTag = $this->_request->get('limitsTag','');
        $limitsAmount = $this->_request->get('limitsAmount',0);
        $limitsDeadline = $this->_request->get('limitsDeadline',0);
       // if(empty($userId))return $this->returnError('用户名为空');
        if(empty($userId))return $this->returnError(\Prj\Lang\Broker::getMsg('voucher.user_notfound'));
        if($tmp = \Prj\Data\Vouchers::newForUser($userId,$voucherType,$amount,$expired)){
            $tmp->setField('limitsShelf',$limitsShelf);
            $tmp->setField('limitsTag',$limitsTag);
            $tmp->setField('limitsAmount',$limitsAmount);
            $tmp->setField('limitsDeadline',$limitsDeadline);
            try{
                $tmp->update();
                try{
                    if($voucherType==\Prj\Consts\Voucher::type_real){
                        $user = \Prj\Data\User::getCopy($userId);
                        $user->load();
                        if(!$user->lock('')){
                            return $this->returnError(\Prj\Lang\Broker::getMsg('voucher.user_notfound'));
                        }
                        $user->setField('redPacket',$user->getField('redPacket')+$amount);
                        $user->update();
                    }
                }catch (\ErrorException $e){
                    $tmp->setField('statusCode',\Prj\Consts\Voucher::status_abandon);
                    $tmp->update();
                    return $this->returnError(\Prj\Lang\Broker::getMsg('voucher.getvoucher_closed'));
                }
            }catch (\ErrorException $e){
                return $this->returnError(\Prj\Lang\Broker::getMsg('system.server_busy'));
            }
        }else{
            return $this->returnError(\Prj\Lang\Broker::getMsg('voucher.getvoucher_notfound'));
        }
        $tmp->load();
        $this->_view->assign('voucher',$tmp->dump());
        return $this->returnOK('ok');
    }
}