<?php

namespace Lib\Oauth;

/**
 * 定义Oauth的错误返回码和错误提示
 * @package Lib\Oauth
 * @property $invalid_request_response_type 缺少参数responseType或者参数responseType非法
 * @property $invalid_request_client_id 缺少参数clientId或者参数clientId非法
 * @property $invalid_request_client_secret 缺少参数clientSecret或者参数clientSecret非法
 * @property $invalid_request_redirect_uri 缺少参数redirectUri或者参数redirectUri非法
 * @property $invalid_request_scope 缺少参数scope或者参数scope非法
 * @property $invalid_request_state 缺少参数state或者参数state非法
 * @property $invalid_request_code 缺少参数code或者参数code非法
 * @property $invalid_request_grant_type 缺少参数grantType或者参数grantType非法
 * @property $invalid_request_access_token 缺少参数accessToken或者参数accessToken非法
 * @property $invalid_request_token_type 缺少参数tokenType或者参数tokenType非法
 * @property $invalid_request_refresh_token 缺少参数refreshToken或者参数refreshToken非法
 * @property $invalid_request_loginname_password 帐号或密码输入错误
 * @property $failed_get_code 获取临时码code失败
 * @property $failed_get_access_token 获取密钥accessToken失败
 * @property $failed_get_refresh_token 获取刷新refreshToken密钥失败
 * @property $error_code_expired 临时码code已经过期
 * @property $error_access_token_expired 密钥accessToken已经过期
 * @property $error_refresh_token_expired 刷新密钥refreshToken已经过期
 * @property $error_client_illegal 密码不正确或者客户端非法
 * @author LingTM <605415184@qq.com>
 */
class OauthResponse {
	/**
	 * 映射
	 * @var array
	 */
	public $responseMap = [
		'invalid_request_response_type'      => ['code' => 60000, 'error' => '缺少参数responseType或者参数responseType非法'],
		'invalid_request_client_id'          => ['code' => 60001, 'error' => '缺少参数clientId或者参数clientId非法'],
		'invalid_request_client_secret'      => ['code' => 60002, 'error' => '缺少参数clientSecret或者参数clientSecret非法'],
		'invalid_request_redirect_uri'       => ['code' => 60003, 'error' => '缺少参数redirectUri或者参数redirectUri非法'],
		'invalid_request_scope'              => ['code' => 60005, 'error' => '缺少参数scope或者参数scope非法'],
		'invalid_request_state'              => ['code' => 60006, 'error' => '缺少参数state或者参数state非法'],
		'invalid_request_code'               => ['code' => 60007, 'error' => '缺少参数code或者参数code非法'],
		'invalid_request_grant_type'         => ['code' => 60008, 'error' => '缺少参数grantType或者参数grantType非法'],
		'invalid_request_access_token'       => ['code' => 60009, 'error' => '缺少参数accessToken或者参数accessToken非法'],
		'invalid_request_token_type'         => ['code' => 60010, 'error' => '缺少参数tokenType或者参数tokenType非法'],
		'invalid_request_refresh_token'      => ['code' => 60011, 'error' => '缺少参数refreshToken或者参数refreshToken非法'],
		'invalid_request_loginname_password' => ['code' => 60012, 'error' => '帐号或密码输入错误'],
		'failed_get_code'                    => ['code' => 60013, 'error' => '获取临时码code失败'],
		'failed_get_access_token'            => ['code' => 60014, 'error' => '获取密钥accessToken失败'],
		'failed_get_refresh_token'           => ['code' => 60015, 'error' => '获取刷新refreshToken密钥失败'],
		'error_code_expired'                 => ['code' => 60016, 'error' => '临时码code已经过期'],
		'error_access_token_expired'         => ['code' => 60017, 'error' => '密钥accessToken已经过期'],
		'error_refresh_token_expired'        => ['code' => 60018, 'error' => '刷新密钥refreshToken已经过期'],
		'error_client_illegal'               => ['code' => 60019, 'error' => '密码不正确或者客户端非法'],
	];

	/**
	 * @todo !!为了支持旧方法而存在，不建议在使用，将在将来删除
	 * 可选属性：error_rescription, error_uri, state
	 * @var array
	 */
	static $response = [
		'invalid_request_response_type'      => ['code' => 60000, 'error' => '缺少参数responseType或者参数responseType非法'],
		'invalid_request_client_id'          => ['code' => 60001, 'error' => '缺少参数clientId或者参数clientId非法'],
		'invalid_request_client_secret'      => ['code' => 60002, 'error' => '缺少参数clientSecret或者参数clientSecret非法'],
		'invalid_request_redirect_uri'       => ['code' => 60003, 'error' => '缺少参数redirectUri或者参数redirectUri非法'],
		'invalid_request_scope'              => ['code' => 60005, 'error' => '缺少参数scope或者参数scope非法'],
		'invalid_request_state'              => ['code' => 60006, 'error' => '缺少参数state或者参数state非法'],
		'invalid_request_code'               => ['code' => 60007, 'error' => '缺少参数code或者参数code非法'],
		'invalid_request_grant_type'         => ['code' => 60008, 'error' => '缺少参数grantType或者参数grantType非法'],
		'invalid_request_access_token'       => ['code' => 60009, 'error' => '缺少参数accessToken或者参数accessToken非法'],
		'invalid_request_token_type'         => ['code' => 60010, 'error' => '缺少参数tokenType或者参数tokenType非法'],
		'invalid_request_refresh_token'      => ['code' => 60011, 'error' => '缺少参数refreshToken或者参数refreshToken非法'],
		'invalid_request_loginname_password' => ['code' => 60012, 'error' => '帐号或密码输入错误'],
		'failed_get_code'                    => ['code' => 60013, 'error' => '获取临时码code失败'],
		'failed_get_access_token'            => ['code' => 60014, 'error' => '获取密钥accessToken失败'],
		'failed_get_refresh_token'           => ['code' => 60015, 'error' => '获取刷新refreshToken密钥失败'],
		'error_code_expired'                 => ['code' => 60016, 'error' => '临时码code已经过期'],
		'error_access_token_expired'         => ['code' => 60017, 'error' => '密钥accessToken已经过期'],
		'error_refresh_token_expired'        => ['code' => 60018, 'error' => '刷新密钥refreshToken已经过期'],
		'error_client_illegal'               => ['code' => 60019, 'error' => '密码不正确或者客户端非法'],
	];

	/**
	 * 读*器
	 * @param mixed $name
	 * @return mixed
	 * @throws \Sooh\Base\ErrException
	 */
	public function __get($name) {
		if (isset($this->$name)) {
			return $this->$name;
		} elseif (isset($this->responseMap[$name])) {
			return $this->responseMap[$name];
//		} elseif (strpos($name, '.') !== false) {
//			list($parent, $child) = explode('.', $name);
//			if (isset($this->responseMap[$parent][$child])) {
//				return $this->responseMap[$parent][$child];
//			}
		}

		//todo deal exception
	}
}