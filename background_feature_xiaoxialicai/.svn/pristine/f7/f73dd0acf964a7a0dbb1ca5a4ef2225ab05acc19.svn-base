<?php
use Sooh\Base\Form\Item as form_def;
use Prj\Data\User as User;
/**
 * Created by PhpStorm.
 * User: LTM <605415184@qq.com>
 * Date: 2015/11/10
 * Time: 17:29
 */
class PointController extends \Prj\ManagerCtrl {
	/**
	 * 积分一览
	 */
	public function indexAction() {
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
		$frm->addItem('_tallyId_eq', form_def::factory('流水ID(精确)', '', form_def::text))
			->addItem('_userId_eq', form_def::factory('用户ID(精确)', $pkey['userId'] ? : '', form_def::text))
			->addItem('_orderId_eq', form_def::factory('订单ID(精确)', '', form_def::text))
			->addItem('_timeCreate_g2', form_def::factory('获得时间大于', '', form_def::datepicker))
			->addItem('_timeCreate_l2', form_def::factory('获得时间小于', '', form_def::datepicker))
//			->addItem('_codeCreate_eq', form_def::factory('codeCreate', '', form_def::text))
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
}