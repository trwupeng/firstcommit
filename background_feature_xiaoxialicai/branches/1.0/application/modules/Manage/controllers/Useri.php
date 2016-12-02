<?php
include_once  __DIR__.'/User.php';
use Sooh\Base\Form\Item as form_def;
use Prj\Data\Investment as Investment;
use Prj\Data\Vouchers as Vouchers;
/**
 * 用户
 * By Hand
 */
class UseriController extends UserController
{
	public function myinvestAction()
    {
        $pageid = $this->_request->get('pageId', 1) - 0;
        $isDownloadEXCEL = $this->_request->get('__EXCEL__');
        $pager = new \Sooh\DB\Pager(10, $this->pageSizeEnum, false);
        $pager->page_size = $this->_request->get('pageSize', 10);
        $pager->init(-1,$pageid);
        //>>>search
        $frm = \Sooh\Base\Form\Broker::getCopy('default')
            ->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_s);
        $frm->addItem('_userId_eq', form_def::factory('用户ID(精确)', '', form_def::text))
            ->addItem('_waresId_eq', form_def::factory('标的ID(精确)', '', form_def::text))
            ->addItem('pageid', $pageid)
            ->addItem('pagesize', $this->pager->page_size);
        $frm->fillValues();
        if ($frm->flgIsThisForm) { //submit
            $where = $frm->getWhere();
            if($_pkey=$this->_request->get('_pkey_val_'))
            {
                var_log($_pkey,'pkey>>>>>>>>');
                $where['waresId'] = \Prj\Misc\View::decodePkey($_pkey)['waresId'];
            }
        } else {
            $where = array();
        }

        //>>>

        $fieldsMapArr = array(
            'ordersId'=>['订单号','160'],
            'waresId'=>['标的号','137'],
            'waresName'=>['标的名称','150'],
            'userId'=>['用户ID','120'],
            'amount'=>['实投金额','100'],
            'amountExt'=>['红包','35'],
            'amountFake'=>['券金','35'],
            'yieldStatic'=>['固定年化','60'],
            'yieldStaticAdd'=>['上浮年化','60'],
            'interest'=>['本金收益','60'],
            'interestExt'=>['奖励收益','60'],
            'extDesc'=>['赠送说明','105'],
            'orderTime'=>['下单时间','150'],
            'orderStatus'=>['订单状态','120'],
            'returnType'=>['还款方式','112'],
            'returnNext'=>['下次还款日','92'],
        );
        $search = $this->_request->get('where');
        if(!empty($search))$where = array_merge($where,$search);
        $invet = \Prj\Data\Investment::getCopy($where['userId=']);
        $db = $invet->db();
        $tbname = $invet->tbname();
        $pager->total = $db->getRecordCount($tbname,$where);
        $rs = $db->getRecords($tbname,'*',$where,'rsort orderTime',$pager->page_size,$pager->rsFrom());
        //$rs = Investment::loopAll($where);
        $header = array();
        foreach($fieldsMapArr as $k=>$v)
        {
            $header[$v[0]] = $v[1];
        }


        $ids = $this->_request->get('ids');
        if(!empty($ids)){
            $tmp = [];
            foreach($ids as $v){
                $tmp[] = \Prj\Misc\View::decodePkey($v)['ordersId'];
            }
        }
        var_log($tmp,'tmp>>>>>>>>>>>>>>>');
        foreach($rs as $v)
        {
            if(!empty($ids)){
                if(!in_array($v['ordersId'],$tmp))continue;
            }
            foreach($fieldsMapArr as $kk=>$vv)
            {
                $temp[$kk] = $v[$kk];
                if(empty($temp[$kk]))$temp[$kk] = '';
            }
            $temp['amount']/=100;
            $temp['amountExt']/=100;
            $temp['amountFake']/=100;
            $temp['interest']/=100;
            $temp['interestExt']/=100;
            $temp['orderTime'] = \Prj\Misc\View::fmtYmd( $temp['orderTime'],'time');
            $temp['yieldStatic'] = (string)($temp['yieldStatic']*100).'%';
            $temp['yieldStaticAdd'] = $temp['yieldStaticAdd']!='0.00'?(string)($temp['yieldStaticAdd']*100).'%':'';
            $temp['orderStatus'] = \Prj\Consts\OrderStatus::$enum[$temp['orderStatus']];
            $temp['returnType'] = \Prj\Consts\ReturnType::$enum[$temp['returnType']];
            $temp['_pkey_val_'] = \Prj\Misc\View::encodePkey(['ordersId'=>$v['ordersId']]);
            if(!empty($temp['returnNext']))$temp['returnNext'] = date('Y-m-d',strtotime($temp['returnNext']));
            $new[] = $temp;
        }
        $rs = $new;
        if(!empty($rs))
        {
            usort($rs,function($a,$b){
                if($a['orderStatus']==$b['orderStatus'])return 0;
                return $a['orderStatus']>$b['orderStatus']?-1:1;
            });
        }
        if($isDownloadEXCEL){
            foreach($rs as $k=>$v){
                unset($rs[$k]['_pkey_val_']);
            }
            return $this->downEXCEL($rs, array_keys($header),null,false);
        }
        $this->_view->assign('header',$header);
        $this->_view->assign('pager',$pager);
        $this->_view->assign('rs',$rs);
        $this->_view->assign('where',$where);

    }
	
	/**
	 * 积分一览
	 */
	public function mypointsAction() {
		$fieldsMapArr = [
			'tallyId' => ['流水ID', '60'],
		    'userId' => ['用户ID', '45'],
		    'orderId' => ['订单ID', '60'],
		    'timeCreate' => ['获得时间', '40'],
// 		    'codeCreate' => ['codeCreate', '40'],
		    'descCreate' => ['描述', '40'],
		    'nOld' => ['旧积分', '30'],
		    'nAdd' => ['新增积分', '30'],
		    'nNew' => ['新积分', '30'],
		];

		$pageId          = $this->_request->get('pageId', 1) - 0;
		$pageSize        = $this->_request->get('pageSize', 10);
		$ids             = $this->_request->get('ids');
		$isDownloadExcel = $this->_request->get('__EXCEL__') == 1;
		$search          = $this->_request->get('where', []);
		$pkey            = \Prj\Misc\View::decodePkey($this->_request->get('_pkey_val_'));

		$frm = \Sooh\Base\Form\Broker::getCopy('default')->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_s);
		$frm->addItem('_tallyId_eq', form_def::factory('流水ID', '', form_def::text))
			->addItem('_userId_eq', form_def::factory('用户ID', $pkey['userId'] ? : '', form_def::text))
			->addItem('_orderId_eq', form_def::factory('订单ID', '', form_def::text))
			->addItem('_timeCreate_g2', form_def::factory('获得时间大于', '', form_def::datepicker))
			->addItem('_timeCreate_l2', form_def::factory('获得时间小于', '', form_def::datepicker))
			->addItem('_codeCreate_eq', form_def::factory('codeCreate', '', form_def::text))
			->addItem('_descCreate_lk', form_def::factory('描述', '', form_def::text))
			->addItem('pageId', $pageId)
			->addItem('pageSize', $pageSize);

		if (!empty($pkey['userId'])) {
			$frm->fillValues(array_merge($_GET, $_POST, ['_userId_eq' => $pkey['userId']]));
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
			if (strpos($k, 'timeCreate') !== false) {
				$where[$k] = date('YmdHis', strtotime($v));
			}
		}

		$pager = new \Sooh\DB\Pager($pageSize, $this->pageSizeEnum, false);
		$pager->init(-1, $pageId);

		$keys = is_array($ids) ? $ids : explode(',', $ids);
		if (!empty($ids)) {
			foreach($keys as $k => $v) {
				$keys[$v] = \Prj\Misc\View::decodePkey($v)['tallyId'];
			}
			$where = ['tallyId' => $keys];
		}

		if ($isDownloadExcel) {
			$where = array_merge($where, $search);
			$pager = new \Sooh\DB\Pager(-1);
		}
		$records = \Prj\Data\ShopPoints::paged($pager, $where, null, implode(',', array_keys($fieldsMapArr)));

		$header = [];
		foreach ($fieldsMapArr as $k => $v) {
			$header[$v[0]] = $v[1];
		}

		$temp = [];
		foreach ($records as $v) {
			foreach ($fieldsMapArr as $kk => $vv) {
				$temp[$kk] = $v[$kk];

				if (!$isDownloadExcel) {
					$temp['_pkey_val_'] = \Prj\Misc\View::encodePkey(['tallyId' => $v['tallyId'], 'userId' => $v['userId']]);
				}
			}
			$temp['timeCreate'] = \Prj\Misc\View::fmtYmd($temp['timeCreate'],'time');
			$new[] = $temp;
		}
		$records = $new;

		if ($isDownloadExcel) {
			return $this->downExcel($records, array_keys($header));
		} else {
			$this->_view->assign('where', $where);
			$this->_view->assign('pager', $pager);
			$this->_view->assign('header', $header);
			$this->_view->assign('rs', $records);
		}
	}
	
	/**
	 * 券总览
	 */
	public function voucherindexAction() {
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
		$voucherType      = $this->_request->get('_voucherType_eq', '');
		$voucherStatusArr = [\Prj\Consts\Voucher::status_unuse => '未使用', \Prj\Consts\Voucher::status_used => '已使用', \Prj\Consts\Voucher::status_abandon => '已废弃',];
		$voucherTypeArr   = [\Prj\Consts\Voucher::type_real => '红包', 99=>'券'];

		//配置搜索项
		$frm = \Sooh\Base\Form\Broker::getCopy('default')->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_s);
		$frm->addItem('_userId_eq', form_def::factory('用户ID', $pkey['userId'] ? : '', form_def::text))
			->addItem('_orderId_eq', form_def::factory('订单号', '', form_def::text))
			->addItem('_dtUsed_g2', form_def::factory('使用时间', '', form_def::datepicker))
			->addItem('_timeCreate_g2', form_def::factory('领取时间从', '', form_def::datepicker))
			->addItem('_timeCreate_l2', form_def::factory('到', '', form_def::datepicker))
			->addItem('_dtExpired_g2', form_def::factory('过期时间从', '', form_def::datepicker))
			->addItem('_dtExpired_l2', form_def::factory('到', '', form_def::datepicker))
				->addItem('_statusCode_eq', form_def::factory('状态', '', form_def::select)->initMore(new \Sooh\Base\Form\Options($voucherStatusArr, '不限')))
			->addItem('kvobjwhere','')
			
			->addItem('pageId', $pageId)
			->addItem('pageSize', $pageSize);
		if ($voucherType != 99) {
			$frm->addItem('_voucherType_eq', form_def::factory('类型', $voucherType, form_def::select)->initMore(new \Sooh\Base\Form\Options($voucherTypeArr, '不限')));
		}

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
		}else{
			$pager = new \Sooh\DB\Pager($pageSize, $this->pageSizeEnum, false);
			$pager->init(\Prj\Data\Vouchers::loopGetRecordsCount($where), $pageId);
		}
		
		if(99==$where['voucherType']){
			unset($where['voucherType']);
			$where['voucherType!']=\Prj\Consts\Voucher::type_real;
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
			$ret = \Prj\Data\Vouchers::loopGetRecordsPage(array('timeCreate'=>'rsort','voucherId'=>'sort'),['where'=>$where], $pager);
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
			return $this->downExcel($records, array_keys($header),null,false);
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
	public function voucherunuseAction() {
		$pkey = \Prj\Misc\View::decodePkey($this->_request->get('_pkey_val_'));
		$voucherId = $pkey['voucherId'];
		if (empty($voucherId)) {
			//return $this->returnError('no_voucherId');
			return $this->returnError(\Prj\Lang\Broker::getMsg('useri.voucherId_error'));
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
				return $this->returnError(\Prj\Lang\Broker::getMsg('useri.voucher_unfound'));
			}
		} catch (\Exception $e) {
			return $this->returnError($e->getMessage(), $e->getCode());
		}
	}

	/**
	 * 禁用券
	 */
	public function voucherabandonAction() {
		$pkey = \Prj\Misc\View::decodePkey($this->_request->get('_pkey_val_'));
		$voucherId = $pkey['voucherId'];
		if (empty($voucherId)) {
			//return $this->returnError('no_voucherId');
			return $this->returnError(\Prj\Lang\Broker::getMsg('useri.voucherId_error'));
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
				return $this->returnError(\Prj\Lang\Broker::getMsg('useri.voucher_unfound'));
			}
		} catch (\Exception $e) {
			return $this->returnError($e->getMessage(), $e->getCode());
		}
	}
}
