<?php
namespace Sooh\DB\Cases;
/**
 * 基础账号
 * (测试环境下，0<deploymentCode<=30, 123456是万能验证码)
 * @author Simon Wang <hillstill_simon@163.com>
 */
class SMSCode extends \Sooh\DB\Base\KVObj{
	public static $maxCounterPerHour = 9999;//每小时内最大发送次数
	public static $maxErrorPerHour=5;//每小时内最大错误次数
	public static $expiredOfCode=900;//每次发送验证码间隔时间-验证码有效期
	protected static function splitedTbName($n,$isCache)
	{
//		if($isCache)return 'tb_test_cache_'.($n % static::numToSplit());
//		else 
		return 'tb_sms_valid_'.($n % static::numToSplit());
	}

	public static function idFor_dbByObj_InConf($isCache) {
		return 'smscode';
	}
	/**
	 * @return SMSCode
	 */
	public static function getCopy($phone)
	{
		$o =  parent::getCopy(array('phone'=>$phone));
		$o->load();
		return $o;
	}
	private $dat;
	/**
	 *
	 * @var \Sooh\Base\Time; 
	 */
	private $dt;
	public function load($fields = '*') {
		parent::load($fields);
		$tmp = parent::getField('dat',true);
		$this->dt = \Sooh\Base\Time::getInstance();
		$cmp = $this->dt->timestamp()-3600;
		if(!empty($tmp)){
			if(is_string($tmp)){
				$this->dat = json_decode($tmp, true);
			}else{
				$this->dat = $tmp;
			}
			//清空1个小时前的错误信息
			if(!empty($this->dat['errors'])) {
				foreach($this->dat['errors'] as $k=>$v){
					if($v<$cmp){
						unset($this->dat['errors'][$k]);
					}
				}
			}
			//清空1个小时前的验证码
			if (!empty($this->dat['codes'])) {
				foreach($this->dat['codes'] as $k=>$v){
					if($v<$cmp){
						unset($this->dat['codes'][$k]);
					}
				}

				//清理第11条code
				if (count($this->dat['codes']) > 10) {
					$this->dat['codes'] = $this->delLastFromArr($this->dat['codes']);
				}
			}
		}else{
			$this->dat = array();
		}
	}
	
	public function chkCode($uInput)
	{
		$tmp = \Sooh\Base\ini::getInstance()->get('deploymentCode');

		if(sizeof($this->dat['errors'])>self::$maxErrorPerHour){
			return false;
		}

		$smscodeExpireDb = \Prj\Data\Config::get('smscodeExpire');
		$smscodeExpire = $smscodeExpireDb ? ($smscodeExpireDb * 60) : self::$expiredOfCode;
		if (isset($this->dat['codes'][$uInput]) && $this->dt->timestamp() - $this->dat['codes'][$uInput] < $smscodeExpire) {
			$this->dat['lastOk'] = $this->dt->timestamp();
			$this->dat['errors'] = 0;
			parent::setField('dat', $this->dat);
			$this->update();
			return true;
		} else {
			$this->dat['errors'][] = $this->dt->timestamp();
			parent::setField('dat', $this->dat);
			$this->update();
			return false;
		}
	}
	/**
	 *
	 * @param string $msgFormat
	 * @param string $code
	 * @throws Exception on update-db failed or send sms failed
	 * @return boolean return false on too many times
	 */
	public function sendCode($msgFormat,$code=null)
	{
		$sentCounterInHour = sizeof($this->dat['codes']);
		if($sentCounterInHour>self::$maxCounterPerHour){
			throw new \Sooh\Base\ErrException('发送次数过多，请稍后再试');
		}
		if($code===null){
			$code = substr(uniqid(),-6);
		}
		$this->dat['codes'][$code]=$this->dt->timestamp();
		parent::setField('dat', $this->dat);
		$this->update();
		return true;
	}

	/**
	 * 删除数组最后一个元素
	 * @param array $arr 源数组
	 * @return array
	 */
	private function delLastFromArr($arr)
	{
		arsort($arr);
		array_pop($arr);//保持索引关系
		return $arr;
	}
}
