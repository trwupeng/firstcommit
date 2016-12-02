<?php
use Sooh\Base\Form\Item as form_def;

class AgreementController extends \Prj\ManagerCtrl {
	protected $enumStatus = [
		\Prj\Consts\Agreement::status_enable => '已启用',
		    \Prj\Consts\Agreement::status_disable => '已禁用',
	];

	/**
	 * 协议一览
	 */
	public function indexAction() {
		$fieldsMapArr = [
			'verName' => ['协议标识', '65'],
			'verId' => ['协议版本', '45'],
			//'verType' => ['协议类型', '30'],
			//'verTpl' => ['显示模版', '30'],
			//'title' => ['标题', '150'],
			'createTime' => ['创建时间', '45'],
			'status' => ['状态', '30'],
		];
//		$tplArr = [
//			\Prj\Consts\Agreement::tpl_html => 'HTML',
//		    \Prj\Consts\Agreement::tpl_excel => 'EXCEL',
//		];

		$pageId = $this->_request->get('pageId', 1) - 0;
		$pageSize = $this->_request->get('pageSize', 10);
		//$ids = $this->_request->get('ids');
		//$isDownloadExcel = $this->_request->get('__EXCEL__') == 1;
		//$search = $this->_request->get('where', []);

		$frm = \Sooh\Base\Form\Broker::getCopy('default')->init(\Sooh\Base\Tools::uri(), 'get',
			\Sooh\Base\Form\Broker::type_s);
		$frm->addItem('_verName_eq', form_def::factory('协议标识', '', form_def::select) ->initMore(new \Sooh\Base\Form\Options(\Prj\Consts\Agreement::$enums,'不限')) )
			//->addItem('_verTpl_eq', form_def::factory('显示模板', '', form_def::select)->initMore(new \Sooh\Base\Form\Options($tplArr, '不限')))
			//->addItem('_title_lk', form_def::factory('标题', '', form_def::text))
			->addItem('_status_eq', form_def::factory('状态位', '', form_def::select)->initMore(new \Sooh\Base\Form\Options($this->enumStatus, '不限')))
			->addItem('pageId', $pageId)
			->addItem('pageSize', $pageSize);
		$frm->fillValues();
		if ($frm->flgIsThisForm) {
			$where = $frm->getWhere();
		} else {
			$where = [];
		}

		$pager = new \Sooh\DB\Pager($pageSize, $this->pageSizeEnum, false);
		$pager->init(-1, $pageId);

//		$keys = is_array($ids) ? $ids : explode(',', $ids);
//		if (!empty($ids)) {
//			foreach ($keys as $k => $v) {
//				$keys[$k] = \Prj\Misc\View::decodePkey($v)['verId'];
//			}
//			$where = ['verId' => $keys];
//		}

//		if ($isDownloadExcel) {
//			$where = array_merge($where, $search);
//			$agreement = new \Prj\Data\Agreement();
//			$records = $agreement->db()->getRecords($pager, $where, null, implode(',', array_keys($fieldsMapArr)),
//				$where);
//		} else {
			$records = \Prj\Data\Agreement::paged($pager, $where, null, implode(',', array_keys($fieldsMapArr)));
//		}


		$header = [];
		foreach ($fieldsMapArr as $k => $v) {
			$header[$v[0]] = $v[1];
		}

		foreach ($records as $v) {
			foreach ($fieldsMapArr as $kk => $vv){
				if ($kk == 'verName') {
					$temp[$kk] = \Prj\Consts\Agreement::$enums[$v[$kk]];
//				} elseif ($kk == 'verTpl') {
//					switch($v[$kk]) {
//						case \Prj\Consts\Agreement::tpl_html:
//							$temp[$kk] = 'HTML';
//							break;
//						case \Prj\Consts\Agreement::tpl_excel:
//							$temp[$kk] = 'EXCEL';
//							break;
//						default:
//							break;
//					}
				} elseif ($kk == 'status') {
					$temp[$kk]=$this->enumStatus[   $v[$kk]   ];
				} else {
					$temp[$kk] = $v[$kk];
				}

//				if (!$isDownloadExcel) {
//					$temp['_pkey_val_'] = \Prj\Misc\View::encodePkey(['verId' => $v['verId']]);
//				}
			}
			$temp['_pkey_val_'] = \Prj\Misc\View::encodePkey(['verName'=>$v['verName'],'verId'=>$v['verId']]);
			$new[] = $temp;
		}

//		if ($isDownloadExcel) {
//			return $this->downExcel($new, array_keys($header));
//		} else {
			$this->_view->assign('where', $where);
			$this->_view->assign('pager', $pager);
			$this->_view->assign('header', $header);
			$this->_view->assign('rs', $new);
//		}
	}

	/**
	 * 新增、修改-尚不可用
	 */
	public function editAction() {
		$pkey = \Prj\Misc\View::decodePkey($this->_request->get('_pkey_val_'));
//		if (empty($verId)) {
//			return $this->returnError('no_verId');
//		}
		$typeArr = [
			\Prj\Consts\Agreement::type_invest => '投资协议',
		    \Prj\Consts\Agreement::type_register => '注册协议',
		];
		$tplArr = [
			//\Prj\Consts\Agreement::tpl_excel => 'EXCEL',
		    \Prj\Consts\Agreement::tpl_html => 'HTML',
		];

		$descChanger =  $this->manager->getField('loginName')."(".$this->manager->getField('nickname',true).")";

		$frm = \Sooh\Base\Form\Broker::getCopy('default')->init(\Sooh\Base\Tools::uri(''), 'get',
			empty($pkey) ? \Sooh\Base\Form\Broker::type_c : \Sooh\Base\Form\Broker::type_u);
		if ($frm->type() == \Sooh\Base\Form\Broker::type_c) {

			$frm->addItem('verName', form_def::factory('协议标识', key(\Prj\Consts\Agreement::$enums), form_def::select) ->initMore(new \Sooh\Base\Form\Options(\Prj\Consts\Agreement::$enums)) )
                ->addItem('verId', form_def::factory('版本号', '', form_def::text)->verifyInteger(1, 1))
				->addItem('status', form_def::factory('状态位', '', form_def::select)->initMore(new \Sooh\Base\Form\Options($this->enumStatus)))
				->addItem('content', form_def::factory('内容', '', form_def::mulit))
				->addItem('_pkey_val_', '');

		} else {
			$obj = \Prj\Data\Agreement::getCopy($pkey);
			$obj->load();
			$frm->addItem('verName', form_def::factory('协议标识', $obj->getField('verName'), form_def::select) ->initMore(new \Sooh\Base\Form\Options(\Prj\Consts\Agreement::$enums)) )
                ->addItem('verId', form_def::factory('版本号', $obj->getField('verId'), form_def::text)->verifyInteger(1, 1))
				->addItem('status', form_def::factory('状态位', $obj->getField('status'), form_def::select) ->initMore(new \Sooh\Base\Form\Options($this->enumStatus)) )
				->addItem('content', form_def::factory('内容', $obj->getField('content'), form_def::mulit))
				->addItem('_pkey_val_', $this->_request->get('_pkey_val_'));
		}
		$frm->fillValues();
		$fields = $frm->getFields();
		if(empty($pkey) && $frm->flgIsThisForm){
			$obj = \Prj\Data\Agreement::getCopy(['verName'=>$fields['verName'],'verId'=>$fields['verId']]);
		}
		
        if ($frm->flgIsThisForm){
			foreach($fields as $k=>$v){
				$obj->setField($k, $v);
			}
			$obj->setField('createTime', \Sooh\Base\Time::getInstance()->timestamp());
			$obj->setField('userId', $this->manager->getField('loginName'));
			$obj->setField('userName', $this->manager->getField('nickame',true));
			try{
				$obj->update();
			}catch (\ErrorException $e){
				return $this->returnError('保存失败:'.$e->getMessage());
			}
			$this->closeAndReloadPage();
            return $this->returnOK('ok');
        }

	}


	/**
	 * 启用
	 */
	public function enableAction() {
		$pkey = \Prj\Misc\View::decodePkey($this->_request->get('_pkey_val_'));
		$verId = $pkey['verId'];
		if (empty($verId)) {
			//return $this->returnError('no_verId');
			return $this->returnError(\Prj\Lang\Broker::getMsg('agreement.verId_misssing'));
		}

		$agreement = new \Prj\Agreement\Agreement();
		try {
			$ret = $agreement->enable($verId);
			return $this->returnOK('成功');
		} catch (\Exception $e) {
			var_log(\Sooh\DB\Broker::lastCmd(false));
			return $this->returnError('失败');
			//return $this->returnError(\Prj\Lang\Broker::getMsg('system.server_busy'));
		}
	}
}