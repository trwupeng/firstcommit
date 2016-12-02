<?php
use Sooh\Base\Form\Item as form_def;
use Sooh\Base\Form\Options as options_def;
use Sooh\Base\ErrException;
use Sooh\Base\Form\Error;

/**
 * 支付网关命令执行
 */

class PaygwcmdsController extends \Prj\ManagerCtrl {
	protected $cmds = [
		'Manage/Paygwcmds/unbindBankCard'=>'解绑银行卡'
	];
	public function init() {
		parent::init();
		
		\Sooh\Base\Rpc\Broker::$_rpcServices = \Lib\Services\Rpcservices::getInstance(null);
		$rpc = \Sooh\Base\Rpc\Broker::factory('PayGWCmd');
		//var_log($rpc,'[rpc]');
		$this->gw = \Lib\Services\PayGWCmd::getInstance($rpc);
	}
	/**
	 *
	 * @var \Lib\Services\PayGWCmd 
	 */
	protected $gw;
	/**
	 * 根据手机号获取userid
	 * @param string $phone
	 * @param string $_ignore_
	 */
	public function getUserIdByPhone($phone,$_ignore_=null)
	{
		$tmp = \Sooh\DB\Cases\AccountAlias::getCopy([$phone,'phone']);
		$tmp->load();
		if($tmp->exists()){
			return $tmp->getField('accountId');
		}else{
			return 0;
		}
	}
    public function cmdsintroAction (){}
	
	public function unbindBankCardAction()
	{
		$frm = \Sooh\Base\Form\Broker::getCopy('default')
        ->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_s);
        $frm->addItem('phone', form_def::factory('登入手机号', '', form_def::text))
           ->addItem('idcardsn', form_def::factory('身份证号', '', form_def::text))
           ->addItem('bankcardlast4', form_def::factory('银行卡号尾4位', '', form_def::text));
        $frm->fillValues();
		
		if($frm->flgIsThisForm){
			//['phone' => '123', 'idcardsn' => '345', 'bankcardlast4' => '567',]
			$fields = $frm->getFields();
			$this->loger->ret = 'failed:error-param';
			if(empty($fields['phone']) || empty($fields['idcardsn']) || empty($fields['bankcardlast4'])){
				return $this->returnError('参数错误，三项都必须提供');
			}
			$userId = $this->getUserIdByPhone($fields['phone']);
			if(empty($userId)){
				return $this->returnError('用户没找到');
			}
			$tmp = \Prj\Data\BankCard::getCopy($userId);
			$rs = $tmp->db()->getRecords($tmp->tbname(), '*',['userId'=>$userId,'statusCode'=>16]);
			if(empty($rs)){
				return $this->returnError('没找到成功的绑卡记录');
			}
			$this->loger->target = "u:$userId & bankcard:".$fields['bankcardlast4']." &idcard:".$fields['idcardsn'];
			foreach($rs as $r){
				if(substr($r['bankCard'],-4)===$fields['bankcardlast4'] && $r['idCardSN']==$fields['idcardsn']){
					
					$err = $this->gw->unbindBankCard($userId, $r['cardId']);
					if(empty($err)){
						$this->loger->ret = 'success';
						$this->closeAndReloadPage();
						return $this->returnOK('成功解绑');
					}else{
						$this->loger->ret = 'failed:'.$err;
						return $this->returnError('解绑失败：'.$err);
					}
				}
			}
			$this->loger->ret = 'failed:没找到同时匹配银行卡和身份证的绑卡记录';
			return $this->returnError('没找到同时匹配银行卡和身份证的绑卡记录');
		}
	}

}