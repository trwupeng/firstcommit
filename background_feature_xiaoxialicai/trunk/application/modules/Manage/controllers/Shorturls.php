<?php
use Sooh\Base\Form\Item as form_def;

class ShorturlsController extends \Prj\ManagerCtrl {
	public function indexAction() {
		$arrFields = [
			'shortId'       => ['短链ID', '30'],
			'contractId'    => ['contractId', '45'],
			'scale'         => ['比率', '20'],
			'copartnerName' => ['短链位置', '60'],
			'instruction'   => ['说明', '80'],
			'createTime'    => ['创建时间', '45'],
			'status'        => ['状态', '30'],
		];

		$pageId = $this->_request->get('pageId', 1) - 0;
		$pageSize = $this->_request->get('pageSize', 10);
		$ids = $this->_request->get('ids');
		$isDownloadExcel = $this->_request->get('__EXCEL__') == 1;
		$search = $this->_request->get('where', []);
		$pkey = \Prj\Misc\View::decodePkey($this->_request->get('_pkey_val_'));

		$frm = \Sooh\Base\Form\Broker::getCopy('default')->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_s);
		$frm->addItem('_shortId_eq', form_def::factory('短链名', '', form_def::text))
			->addItem('_contractId_eq', form_def::factory('contractId', '', form_def::text))
			->addItem('_copartnerName_lk', form_def::factory('短链位置', '', form_def::text))
			->addItem('pageId', $pageId)
			->addItem('pageSize', $pageSize);
		if (!empty($pkey['contractId']) && !empty($pkey)) {
			$inputArr = [
				'contractId' => $pkey['contractId'],
			];
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

		$pager = new \Sooh\DB\Pager($pageSize, $this->pageSizeEnum, false);
		$pager->init(-1, $pageId);

		$keys = is_array($ids) ? $ids : explode(',', $ids);
		if (!empty($ids)) {
			foreach ($keys as $k => $v) {
				$keys[$k] = \Prj\Misc\View::decodePkey($v)['contractId'];
			}
			$where = ['contractId' => $keys];
		}

		if ($isDownloadExcel) {
			$where = array_merge($where, $search);
			$pager = new \Sooh\DB\Pager(-1);
		}
		$records = \Prj\Data\ShortenedUrl::paged($pager, $where, null, implode(',', array_keys($arrFields)));

		$header = [];
		foreach ($arrFields as $k => $v) {
			$header[$v[0]] = $v[1];
		}

		$temp = [];
		foreach ($records as $v) {
			foreach ($arrFields as $kk => $vv) {
				if ($kk == 'status') {
					switch ($v[$kk]) {
						case 1:
							$temp[$kk] = '正常';
							break;
						case -1:
							$temp[$kk] = '禁用';
							break;
						default:
							$temp[$kk] = '未知';
							break;
					}
				} else {
					$temp[$kk] = $v[$kk];
				}

				if (!$isDownloadExcel) {
					$temp['_pkey_val_'] = \Prj\Misc\View::encodePkey(['shortId' => $v['shortId'], 'contractId' => $v['contractId']]);
				}
			}
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