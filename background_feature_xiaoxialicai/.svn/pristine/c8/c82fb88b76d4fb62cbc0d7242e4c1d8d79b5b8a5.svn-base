<?php
namespace Prj\Oauth;
use Sooh\DB\Error;

/**
 * Class Oauth
 * 继续重构
 * 自动保存token，请求状态码自检查，自动刷新-重新请求
 * @package Prj\Oauth
 * @author  LTM <605415184@qq.com>
 */
class Oauth {
	/**
	 * 临时码code
	 * @var string
	 */
	private $code;

	/**
	 * 回调地址
	 * @var string
	 */
	private $redirectUri;

	/**
	 * accessToken
	 * @var string
	 */
	private $accessToken;

	/**
	 * refreshToken
	 * @var string
	 */
	private $refreshToken;

	/**
	 * Oauth Api 跟路径
	 * @var string
	 */
	private $baseURL;

	/**
	 * 最后一次刷新token的时间
	 * @var
	 */
	private $refreshTime = 0;

	/**
	 * 调用模式
	 * @var string
	 */
	private $mode = 'standard';

	const errServerBusy = '服务器忙';

	/**
	 * 不允许直接访问的方法列表
	 * //TODO 待补充
	 * @var array
	 */
	protected $disableFuns = ['appreg', 'webReg'];

	/**
	 * 构造方法，用于实例化Oauth
	 * @param string $code        临时码
	 * @param string $redirectUri 回调地址
	 * @param string  $mode       应用模式
	 * @throws \ErrorException
	 * @throws \Sooh\Base\ErrException
	 */
	public function __construct($code = '', $redirectUri = '', $mode = 'standard') {
		$this->_set('baseURL', \Sooh\Base\Ini::getInstance()->get('uriBase')['oauth'] . '/index.php?__VIEW__=json&');

		var_log($this->_get('accessToken', false, true), 'accessToken Obj init');
		var_log($this->_get('refreshToken', false, true), 'refreshToken Obj init');

		if ((empty($code) || empty($redirectUri)) && empty($mode)) {
			var_log('oauth set property');
			$this->_set('accessToken', \Sooh\Base\Session\Data::getInstance()->get('accessToken'), true, false);
			$this->_set('refreshToken', \Sooh\Base\Session\Data::getInstance()->get('refreshToken'), true, false);
		} elseif ((empty($code) || empty($redirectUri)) && $mode == 'nonStandardMode') {
			//其他模式
			$this->mode = $mode;
		} else {
			$this->_set('code', $code);
			$this->_set('redirectUri', $redirectUri);

			$this->getToken($code, $redirectUri);
		}
	}

	/**
	 * 其他模式，一般用于不需要accessToken的非标准Oauth模式如appreg、appLogin
	 * @param array $args 其他模式需要的参数:['clientId' => '', 'clientSecret' => '', 'scope' => '', 'func' => 'resetPwd']
	 * @return mixed
	 * @throws \Sooh\Base\ErrException
	 */
	public function invokeMode($args) {
		if ($this->mode != 'nonStandardMode') {
			throw new \Sooh\Base\ErrException(self::errServerBusy);
		}

		if (!isset($args['func'])) {
			throw new \Sooh\Base\ErrException(self::errServerBusy);
		} else {
			$args['__'] = 'oauth/' . $args['func'];
			unset($args['func']);
		}

		if (isset($args['_cmd_'])) {
			$cmd = $args['_cmd_'];
			unset($args['_cmd_']);
//			if (isset($cmd['accessToken'])) {
//				array_merge($args, ['accessToken' => '']);
//			}
			//TODO 安全检查cmd

			$args = array_merge($args, $cmd);
		}

		$ret = $this->http($args);

		return $ret;
	}

	/**
	 * 换取token
	 * @param string $code        code
	 * @param string $redirectUri redirectUri
	 * @throws \ErrorException
	 * @throws \Sooh\Base\ErrException
	 */
	public function getToken($code, $redirectUri) {
		$params = [
			'__'          => 'oauth/token',
			'code'        => $code,
			'redirectUri' => $redirectUri,
		];

		$ret = $this->http(http_build_query($params));
		$this->_set('accessToken', $ret['accessToken'], false, true, ['expire' => $ret['accessTokenExpiresIn'] - 10]);
		$this->_set('refreshToken', $ret['refreshToken'], false, true, ['expire' => $ret['refreshTokenExpiresIn'] - 10]);
	}

	/**
	 * 获取用户资源
	 * @return array
	 * @throws \Sooh\Base\ErrException
	 */
	public function getResource() {
		error_log('oauth->getResource');
		$params = [
			'__'          => 'oauth/userInfo',
			'accessToken' => '',
		];
		return $this->http($params);
	}

	/**
	 * 刷新token
	 * @param $refreshToken
	 * @return array ['accessToken' => '*****', 'refreshToken' => '*****']
	 * @throws \ErrorException
	 * @throws \Sooh\Base\ErrException
	 */
	public function refreshToken() {
		/**
		 * 规避系统风险，防止异常连续刷新
		 */
		if (\Sooh\Base\Time::getInstance()->timestamp() - $this->refreshTime <= 10) {
			throw new \ErrorException(\Prj\Lang\Broker::getMsg('passport.ban_repeated_refreshes'));
		} else {
			$this->refreshTime = \Sooh\Base\Time::getInstance()->timestamp();
		}

		$_refreshToken = $this->_get('refreshToken', false, true);
		if (empty($_refreshToken)) {
			throw new \ErrorException(\Prj\Lang\Broker::getMsg('passport.login_info_has_expired_please_login_again'));
		}

		$params = [
			'__'           => 'oauth/refresh',
			'refreshToken' => $this->_get('refreshToken', false, true)
		];
		$ret    = $this->http(http_build_query($params));

		$this->_set('accessToken', $ret['accessToken'], false, true, ['expire' => $ret['accessTokenExpiresIn'] - 10]);
		$this->_set('refreshToken', $ret['refreshToken'], false, true, ['expire' => $ret['refreshTokenExpiresIn'] - 10]);
	}

	/**
	 * 执行Oauth的方法
	 * @param string $name Oauth的方法名
	 * @param array  $args 传递的参数
	 * @return array
	 * @throws \ErrorException
	 * @throws \Sooh\Base\ErrException
	 */
	public function invokeOauth($name, $args) {
		if (!is_string($name) || !is_array($args) || in_array($name, $this->disableFuns)) {
			$this->loger->ret   = 'try invoke Oauth';
			$this->loger->sarg1 = json_encode($name);
			$this->loger->sarg2 = json_encode($args);
			throw new \Sooh\Base\ErrException(self::errServerBusy);
		}

		$params = [
			'__'          => 'oauth/' . $name,
			'accessToken' => '',
		];

		$ret = $this->http(array_merge($args, $params));
		return $ret;
	}

	/**
	 * 属性获取器
	 * @param string     $name       属性名
	 * @param bool|false $allowNulls 是否允许为空
	 * @param bool  $sess 是否可以从session中获取
	 * @return mixed
	 * @throws \Sooh\Base\ErrException
	 */
	protected function _get($name, $allowNulls = false, $sess = false) {
		if (!$allowNulls && empty($this->$name)) {
			if ($sess) {
				$value = \Sooh\Base\Session\Data::getInstance()->get($name);
				if ($value) {
					$this->$name = $value;
				}
			} else {
				throw new \Sooh\Base\ErrException(self::errServerBusy);
			}
		}

		return $this->$name;
	}

	/**
	 * 属性设置器
	 * @param string     $name       属性名
	 * @param    string  $value      属性值
	 * @param bool|false $allowNulls 是否允许为空
	 * @param bool|true  $setExtra   设置额外参数
	 * @param array|[] $extraArgs 额外参数
	 * @throws \Sooh\Base\ErrException
	 */
	protected function _set($name, $value, $allowNulls = false, $setExtra = true, $extraArgs = []) {
		var_log(func_get_args(), 'Prj:Oauth args');
		if (!$allowNulls && empty($value)) {
			throw new \Sooh\Base\ErrException(self::errServerBusy);
		}

		if (in_array($name, ['accessToken', 'refreshToken']) && $setExtra && !empty($extraArgs['expire'])) {
			var_log(\Sooh\Base\Session\Data::getInstance()->get($name), 'before storage ' . $name);
			\Sooh\Base\Session\Data::getInstance()->set($name, $value, $extraArgs['expire']);
			var_log(\Sooh\Base\Session\Data::getInstance()->get($name), 'after storage ' . $name);
		}
		$this->$name = $value;
		var_log($this->_get($name, false, true), 'storage:' . $name);
	}

	/**
	 * 远程请求OauthApi
	 * @param   string  $initUrl     请求URL
	 * @param bool|true $check   是否对返回状态吗进行检查
	 * @param string    $baseURL 根URL
	 * @return mixed
	 * @throws \Sooh\Base\ErrException
	 */
	private function http($initUrl, $check = true, $baseURL = '') {
		var_log($initUrl, '\Prj\Oauth:http->initUrl');
		if (empty($baseURL)) {
			$baseURL = $this->_get('baseURL');
		}

		$url = $initUrl;
		if (is_array($url)) {
			if (isset($url['accessToken']) && empty($url['accessToken'])) {
				$_accessToken = $this->_get('accessToken', false, true);
				if (empty($_accessToken)) {
					var_log($_accessToken, 'start refreshToken because accessToken is NULL');
					$this->refreshToken();
				}
				$url['accessToken'] = $this->_get('accessToken', false, true);
				var_log($url['accessToken'], 'get accessToken from property OR session');
			}
			$url = http_build_query($url);
		}

		$requestUrl = $baseURL . $url;
		var_log($requestUrl, '\Prj\Oauth::http()->requestUrl');
		$response = json_decode(\Sooh\Base\Tools::httpGet($requestUrl), true);
		var_log($response, '\Prj\Oauth:http->ret');
		if ($response['code'] == 200) {
			//快捷模式则刷新accessToken
			if (isset($response['info']['_isQuick_'])) {
				$code = $response['info']['code'];
				$this->getToken($code, $response['info']['redirectUri'] ? : 'https://www.baidu.com/');
			}

			$ret = $response['info'] ? : $response;

			$arrUrl = $this->parseHttpUrl($requestUrl);
			if (in_array(strtoupper($arrUrl['__']), ['OAUTH/TOKEN', 'OAUTH/REFRESH'])) {
				var_log($ret, 'need storage Response Result because ret in_array [oauth/token, oauth/refresh]::');
				//存储accessToken和refreshToken-外层方法保存token有风险
				$this->_set('accessToken', $ret['accessToken'], false, true, ['expire' => $ret['accessTokenExpiresIn'] - 10]);
				$this->_set('refreshToken', $ret['refreshToken'], false, true, ['expire' => $ret['refreshTokenExpiresIn'] - 10]);
			}
			return $ret;
		} else {
			\Sooh\Base\Log\Data::getInstance('c')->ret   = 'get Oauth api';
			\Sooh\Base\Log\Data::getInstance('c')->sarg1 = $url;

			if ($check && $response['code'] == '60017') {
				//accessToken过期，需要refresh
				$this->refreshToken();
				return $this->http($initUrl);
			} else {
				throw new \Sooh\Base\ErrException($response['msg'] ? : self::errServerBusy, $response['code'] ? : 400);
			}
		}
	}

	/**
	 * 解析URL请求的query
	 * @param string $url http request url
	 * @return array
	 */
	private function parseHttpUrl($url)
	{
		$strQuery = parse_url($url, PHP_URL_QUERY);
		parse_str($strQuery, $arrUrl);
		return $arrUrl;
	}
}