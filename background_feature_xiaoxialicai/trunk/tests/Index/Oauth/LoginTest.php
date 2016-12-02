<?php
namespace Tests\Index\Oauth;

include realpath(__DIR__ . '/../../../conf') . '/inc4tests.php';
/**
 * Created by PhpStorm.
 * User: LTM <605415184@qq.com>
 * Date: 2015/10/21
 * Time: 13:32
 */
class LoginTest extends \Sooh\Base\Tests\ApiHttpGetJson {

	public function testDefault() {
		$params = [
			'loginName' => 18616700069,
		    'password' => 111111,
		    'cameFrom' => 'phone',
		    'scope' => 'admin',
		    '__VIEW__' => 'json',
		];
		foreach($params as $key => &$val) {
			if (empty($val)) {
				unset($params[$key]);
			}
		}
		$this->apiChk($this->getUrl('oauth/login', $params), ['"code":200'], 'oauth login for success');

		$params['loginName'] = 'dkcskew';
		$this->apiChk($this->getUrl('oauth/login', $params), ['"code":400'], 'oauth login for false loginName');

		$params['loginName'] = 18616700069;
		$params['password'] = 'clsdm,xcj';
		$this->apiChk($this->getUrl('oauth/login', $params), ['"code":400'], 'oauth login for false password');

		$params['password'] = 111111;
		$params['cameFrom'] = 'dclsl';
		$this->apiChk($this->getUrl('oauth/login', $params), ['"code":' . \Lib\Oauth\OauthResponse::$response['invalid_request_loginname_password']['code']], 'oauth login for false cameForm');

		$params['cameFrom'] = 'phone';
		$params['scope'] = 'clsjdck,jhdh';
		$this->apiChk($this->getUrl('oauth/login', $params), ['"code":' . \Lib\Oauth\OauthResponse::$response['failed_get_code']['code']], 'oauth login for false scope');
	}
}