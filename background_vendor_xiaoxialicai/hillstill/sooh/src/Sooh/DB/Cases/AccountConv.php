<?php
namespace Sooh\DB\Cases;
/**
 * 账号转化（比如手机号）
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class AccountConv  extends \Sooh\DB\Base\KVObj{
	//put your code here
	/**
	 * 
	 * @param string $account
	 * @param string $camefrom
	 * @return \Sooh\DB\Cases\AccountBase
	 */
	public static function getAccountBase($account,$camefrom='local')
	{
		$me = static::getCopy(array('account'=>$account,'camefrom'=>$camefrom));
		$me->load();
		if($me->getField('uid',true)){
			return \Sooh\DB\Cases\AccountBase::getCopy();
		}else{
			return null;
		}
	}
	/**
	 * 
	 * @param string $new
	 * @param string $camefrom
	 * @param string $old
	 * @param int $intUidForce 强制userid
	 * @return \Sooh\DB\Cases\AccountBase
	 * @throws ErrorException
	 */
	public function createAccount($new,$camefrom,$old=null,$intUidForce=null)
	{
		$me = static::getCopy(array('account'=>$new,'camefrom'=>$camefrom));
		$me->load();
		if($me->getField('status', true)){
			throw new \ErrorException('user exists already');
		}else{
			if($old!==null){
				$tmp = static::getCopy(array('account'=>$old,'camefrom'=>$camefrom));
				$tmp->load();
				if($tmp->exists()){
					try{
						$tmp->setField('status', 0);
						$old = $tmp->getField('changelog',true);
						$msg='';
						$tmp->setField('changelog', ( empty($old) ? $msg : $old."\n".$msg ) );
						$tmp->update();
					} catch (\ErrorException $ex) {
						error_log("update old-user in AccountConv failed:".$ex->getMessage()."\n".$ex->getTraceAsString());
						throw new \ErrorException('user exists already');
					}
				}else{
					error_log("update old-user in AccountConv failed: missing".$ex->getTraceAsString());
				}
			}
			
			if($intUidForce===null){
				$intUidForce = \Sooh\Base\Time::getInstance()->timestamp();
			}
			
			$me->setField('uid', $intUidForce);
			$me->setField('status', 1);
			$me->setField('changelog', '');
			$me->update();
			\Sooh\DB\Cases\AccountBase::getCopy($intUidForce);
		}
	}
	

	public function createTable()
	{
		$this->db()->ensureObj($this->tbname(), array(
			'account'=> \Sooh\DB\Base\Field::str36,
			'camefrom'=> \Sooh\DB\Base\Field::str36,
			
			'uid'=>\Sooh\DB\Base\Field::int64,
			'status'=>\Sooh\DB\Base\Field::int32,   //0:disable, 1:normal
			'changelog'=> \Sooh\DB\Base\Field::str1k,
			'iRecordVerID'=>\Sooh\DB\Base\Field::int64,
		),array('account','camefrom'));
	}
}
