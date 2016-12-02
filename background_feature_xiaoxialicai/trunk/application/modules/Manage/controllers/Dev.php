<?php
use Sooh\Base\Form\Item as form_def;
include_once __DIR__.'/Devware.php';
class DevController extends DevwareController {
	/**
	 * app访问日志
	 */
	public function apilogAction() {
		$pageId = $this->_request->get('pageId', 1) - 0;
		$pageSize = $this->_request->get('pageSize', 10);

		$frm = \Sooh\Base\Form\Broker::getCopy('default')->init(\Sooh\Base\Tools::uri(), 'get',
			\Sooh\Base\Form\Broker::type_s);
		$frm->addItem('_sessionId_eq', form_def::factory('sessionId', '', form_def::text) )
			->addItem('_accountId_eq', form_def::factory('accountId', '', form_def::text))
			->addItem('_phone_eq', form_def::factory('phone', '', form_def::text))
			->addItem('_apiget_lk', form_def::factory('命令字', '', form_def::text))
			->addItem('_returned_lk', form_def::factory('返回内容包含', '', form_def::text))
			->addItem('pageId', $pageId)
			->addItem('pageSize', $pageSize);
		$frm->fillValues();
		if ($frm->flgIsThisForm) {
			$where = $frm->getWhere();
		} else {
			$where = [];
		}
		if(!empty($where['apiget'])){
			$where['_get']=$where['apiget'];
			unset($where['apiget']);
		}
		
		$pager = new \Sooh\DB\Pager(10, [10], false);
		$pager->init(-1, $pageId);
		$db = \Sooh\DB\Broker::getInstance();
		$records = $db->getRecords('db_logs.tb_a', '*',$where,'rsort ymd rsort his',$pager->page_size);

		$header = [
			'client'=>'65',
			'时间'=>'45',
			'请求'=>100,
			'应答'=>200,
		];
		$new = [];
		foreach ($records as $r) {

			$client = 'phone:'.$r['phone']." <br> u:".$r['accountId']."  <br>  sess:".$r['sessionId'];
			$dt = \Prj\Misc\View::fmtYmd($r['ymd'].sprintf("%06d",$r['his']), 'time');
			$new[] = [
				$client,
				$dt,
				str_replace('__COOKIE__:','<br>Cookie:',htmlspecialchars("GET:".$r['_get']."__COOKIE__:".$r['_cookie'])),
				htmlspecialchars($r['returned'])
			];
		}

		$this->_view->assign('pager', $pager);
		$this->_view->assign('header', $header);
		$this->_view->assign('rs', $new);
	}
	public function sms7Action()
	{//http://apitest.xiaoxialicai.com/dev/smstestuser?phone=70000000024
		$frm = \Sooh\Base\Form\Broker::getCopy('default')->init(\Sooh\Base\Tools::uri(), 'get',
			\Sooh\Base\Form\Broker::type_s);
		$frm->addItem('_phone_eq', form_def::factory('phone', '', form_def::text))
			->addItem('pageId', 1)
			->addItem('pageSize', 10);
		$pager = new \Sooh\DB\Pager(10, [10], false);
		$frm->fillValues();
		if ($frm->flgIsThisForm) {
			$where = $frm->getWhere();
		} else {
			$where = [];
		}
		$header = [
			'时间'=>60,
			'内容'=>400,
		];
		$new = [];
		if(!empty($where)){
			$rs = \Sooh\DB\Broker::getInstance()->getRecords('db_logs.tb_devsms','dt,msg',$where,'rsort dt',$pager->page_size);
			foreach($rs as $r){
				$new[]=[date('m-d H:i:s',$r['dt']),htmlspecialchars($r['msg'])];
			}
		}

		$this->_view->assign('pager', $pager);
		$this->_view->assign('header', $header);
		$this->_view->assign('rs', $new);
	}
	public function dev2testAction()
	{
		$GLOBALS['CONF']['dbConf']['test'] = array(
			'host' => '115.28.172.53', 'user' => 'mytestuser', 'pass' => 'Xx111111', 
			'type' => 'mysql', 'port' => '3306', 'dbEnums' => array('default' => 'db_p2p',));
		$GLOBALS['CONF']['dbConf']['dev'] = $GLOBALS['CONF']['dbConf']['default'];

		$this->ini->initGobal($GLOBALS['CONF']);

		$dbTest = \Sooh\DB\Broker::getInstance('test');
		$dbDev = \Sooh\DB\Broker::getInstance('dev');
		
		$rs  = $dbDev->getRecords('db_p2p.tb_agreement_0', '*');
		$r = $rs[0];
		unset($r['verName']);
		unset($r['verId']);
		$ks = array_keys($r);
		foreach($rs as $r){
			$dbTest->ensureRecord('db_p2p.tb_agreement_0',$r,$ks);
		}
		$ignore = ['dbsql.ver','SHARE_VOUCHER_URL','WECHAT_ACCESSTOKEN','WECHAT_APP_ID','WECHAT_APP_SECRET','CUSTOMER_INVITE_URL'];

		$rs2 = $dbDev->getRecords('db_p2p.tb_config', '*',['k!'=>$ignore]);
		foreach($rs2 as $r){
			$dbTest->ensureRecord('db_p2p.tb_config',$r,['v','intro']);
		}
		$this->_view->assign('dt',date('m-d H:i:s'));
		$this->_view->assign('agreement',  sizeof($rs));
		$this->_view->assign('config',  sizeof($rs2));
		error_log(\Prj\Misc\Cp2cdn::misc('/var/www/licai_php/public/images/logo.png','logo_test.png'));
	}
}