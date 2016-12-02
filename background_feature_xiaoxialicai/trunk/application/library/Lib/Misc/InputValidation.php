<?php
namespace Lib\Misc;

/**
 * 参数验证类
 *
 * @package Lib\Misc
 * @property $phone 手机号
 * @property $smscode 短信验证码
 * @property $accountId 用户ID
 * @property $contractId 渠道ID
 * @property $clientType 客户端类型
 * @property $invitationCode 邀请码
 * @property $clientId Oauth颁发的ID
 * @property $clientSecret Oauth颁发的密钥
 * @property $accessToken Oauth生成的许可accessToken
 * @property $refreshToken Oauth生成的刷新许可refreshToken
 * @property $code Oauth生成的中间临时码
 * @property $cameFrom 注册帐号类型
 * @property $nickname 用户昵称
 * @author LingTM <605415184@qq.com>
 */
class InputValidation
{
	/**
	 * 错误讯息
	 * @var string
	 */
	static $errorMsg = '字段不正确';
	/**
	 * 错误返回码
	 * @var int
	 */
	static $errorCode = 400;

	public $errMsg = '字段不正确';
	public $errCode = 400;

	/**
	 * 常用字段-正则表达式
	 * phone
	 * password
	 * accountId
	 * contractId
	 * clientType
	 * clientId
	 * clientSecret
	 * accessToken
	 * refreshToken
	 * code
	 * cameFrom
	 * @var array
	 */
	static $define = [
		'phone'          => '#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,3,6,7,8]{1}\d{8}$|^18[\d]{9}$#',//手机号
		'smscode'        => '#^\d{6}$#',//短信验证码
		'accountId'      => '#^\d{14}$#',//帐号
		'contractId'     => '#^\w+$#',//渠道ID
		'clientType'     => '#^[1-9]\d{2}$#',
		'invitationCode' => '#^[0-9A-Z]{7}$#',//邀请码
		'clientId'       => '#^\d{10}$#',//clientId
		'clientSecret'   => '#^[a-zA-Z0-9]{16}$#',//clientSecret
		'accessToken'    => '#^\w{32}$#',//accessToken
		'refreshToken'   => '#^[a-z0-9]{32}$#',//refreshToken
		'code'           => '#^\w{32}$#',//code
		'cameFrom'       => '#^[a-zA-Z]{3,10}$#',
		'nickname'       => '/^[\x{4e00}-\x{9fa5}]{1,5}$/u',
	];

	public $ruleMap = [
		'phone'          => '/^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,3,6,7,8]{1}\d{8}$|^18[\d]{9}$/',//手机号
		'smscode'        => '/^\d{6}$/',//短信验证码
		'accountId'      => '/^\d{14}$/',//帐号
		'contractId'     => '/^\w+$/',//渠道ID
		'clientType'     => '/^[1-9]\d{2}$/',
		'invitationCode' => '/^[0-9A-Z]{7}$/',//邀请码
		'clientId'       => '/^\d{10}$/',//clientId
		'clientSecret'   => '/^[a-zA-Z0-9]{16}$/',//clientSecret
		'accessToken'    => '/^\w{32}$/',//accessToken
		'refreshToken'   => '/^[a-z0-9]{32}$/',//refreshToken
		'code'           => '/^\w{32}$/',//code
		'cameFrom'       => '/^[a-zA-Z]{3,10}$/',
		'nickname'       => '/^[\x{4e00}-\x{9fa5}]{1,5}$/u',
	];

	/**
	 * 验证方法
	 * 样例：
	 *      $params = [key1 => val1, key2 => val2];
	 *      $rules = [key1 => rule1, key1 => rule2];
	 *      $keyAlias = [key1 => alias1, key2 => alias2]

	 * @param array $params key=>value params
	 * @param array $removeKeys ['key1', 'key2']
	 * @param null $rules key=>['validateExpress', 'errorMsg', 'errorCode'] rules
	 * @param null $keyAlias paramKey=>rulesKey
	 * @param false $returnDesc 是否记录详细信息
	 * @return array|bool
	 */
	public function validate($params, $removeKeys = [], $rules = null, $keyAlias = null, $returnDesc = false)
	{
		if ($rules == null) {
			$rules = $this->initRules(array_keys($params), $keyAlias);
		}
		if (is_array($params) && is_array($rules)) {
			foreach ($rules as $_k => $_v) {
				if (!in_array($_k, $removeKeys)) {
					if (isset($params[$_k])) {
						if (preg_match($_v[0], $params[$_k]) == 0) {
							if (!isset($_v[2]) || $_v[2] === null) {
								if (!empty($_v[1])) {
									$this->errMsg = $_v[1];
									if (!empty($_v[2]) && is_numeric($_v[2])) {
										$this->errCode= $_v[2];
									}
								}
								return false;
							} else {
								$params[$_k] = $_v[2];
							}
						}
					} else {
						$this->errMsg = $_v[1] ? : '参数不合法';
						return false;
					}
				}
			}
			return $params;
		} else {
			$this->errMsg = '待验证参数类型不正确';
			return false;
		}
	}

	public function __construct()
	{
		//在测试环境下，允许以7开头的手机号通过验证
		$tmp = \Sooh\Base\Ini::getInstance()->get('deploymentCode') - 0;
		if ($tmp <= 30) {
			$this->phone = substr($this->phone, 0, -1) . '|^7\d{10}$/';
//			error_log($this->phone);
		}
	}

	/**
	 * 用正则表达式验证表单数据
	 *      $params ['param1' => $a, 'param2' => $b]
	 *      $rules ['param1' => ['regExp1', 'errorMsg', 'errorCode'], 'param2' => ['regExp2', 'errorMsg', 'errorCode']]
	 * @param array $params 表单数据
	 * @param array $rules  规则数组,出现在rules中的字段必须验证
	 * @todo 为了兼容老版本而存在，将在未来删除
	 * @return bool 原样返回或者false
	 */
	public static function validateParams($params, $rules) {
		if (is_array($params) && is_array($rules)) {
			foreach ($rules as $_k => $_v) {
				if (isset($params[$_k])) {
					if (preg_match($_v[0], $params[$_k]) == 0) {
						if ($_v[2] === null || !isset($_v[2])) {
							if (!empty($_v[1])) {
								self::$errorMsg = $_v[1];
								if (!empty($_v[2]) && is_numeric($_v[2])) {
									self::$errorCode = $_v[2];
								}
							}
							return false;
						} else {
							$params[$_k] = $_v[2];
						}
					}
				} else {
					self::$errorMsg = $_v[1] ? : '参数不合法';
					return false;
				}
			}
			return $params;
		} else {
			self::$errorMsg = '待验证参数类型不正确';
			return false;
		}
	}

	/**
	 * 获取验证规则map
	 * @param array $keys 需要验证的key
	 * @param array $keyAlias 别名映射
	 * @example keys:['phone','password'],'phone,password'; return:['phone' => 'validatetion express', 'password' => 'validate express']
	 * @return array
	 */
	protected function initRules($keys, $keyAlias = null)
	{
		$rules = [];
		foreach ($keys as $v) {
			if (isset($keyAlias[$v])) {
				$_v = $keyAlias[$v];
			} else {
				$_v = $v;
			}

			if (isset($this->ruleMap[$_v])) {
				$rules[$v][0] = $this->ruleMap[$_v];
			} else {
				//TODO deal exception
			}
		}
		return $rules;
	}

	/**
	 * 重载__get方法，获取不存在的属性
	 * @param string $var 变量名
	 * @return mixed
	 * @throws \ErrorException
	 */
	function __get($var)
	{
		if (isset($this->$var)) {
			return $this->$var;
		}
		return $this->ruleMap[$var];
	}

	/**
	 * 重载__set方法，设置不存在的属性
	 * @param string $name name
	 * @param string $value value
	 */
	function __set($name, $value)
	{
		if (isset($this->$name)) {
			$this->$name = $value;
		} else {
			$this->ruleMap[$name] = $value;
		}
	}
}

$tmp = \Sooh\Base\Ini::getInstance()->get('deploymentCode')-0;
if ($tmp <= 30) {
	InputValidation::$define['phone']=substr(InputValidation::$define['phone'],0,-1).'|^7\d{10}$#';
	error_log(InputValidation::$define['phone']);
}