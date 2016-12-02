<?php
namespace Tests\Index\Oauth;

use \Prj\Consts\MsgDefine as msgDefine;
use \Lib\Oauth\OauthResponse as oauthResponse;

include realpath(__DIR__ . '/../../../conf') . '/inc4tests.php';

/**
 * Created by PhpStorm.
 * User: LTM <605415184@qq.com>
 * Date: 2015/10/21
 * Time: 9:18
 */
class AuthrizeTest extends \Sooh\Base\Tests\ApiHttpGetJson {
	const CLIENT_ID = '1104878344';
	const CLIENT_SECRET = 's20vH9emKJ6BmT1Q';
	const REDIRECT_URI = 'https://www.baidu.com/';
	const SCOPE = 'basic,photo,info,admin';

	public function testDefault() {
		$params = [
			'responseType' => 'code',
			'clientId' => self::CLIENT_ID,
			'clientSecret' => self::CLIENT_SECRET,
			'redirectUri' => self::REDIRECT_URI,
			'scope' => self::SCOPE,
			'state' => '',
		    '__VIEW__' => 'json',
		];
		foreach ($params as $_key => &$_val) {
			if (empty($_val)) {
				unset($params[$_key]);
			}
		}
		//success
		$expected = ['"code":200'];
		$this->apiChk($this->getUrl('oauth/authrize', $params), $expected, '[oauth authrize for right params]:');

		//error for clientId
		$params['clientId'] = 'sdvcksd.xlclls';
		$expected = ['"code":' . oauthResponse::$response['invalid_request_client_id']['code']];
		$this->apiChk($this->getUrl('oauth/authrize', $params), $expected, '[oauth authrize for false clientId]:');

		//error for clientSecret
		$params['clientId'] = self::CLIENT_ID;
		$params['clientSecret'] = 'sdfcksd,mekx1230sl;';
		$expected = ['"code":' . oauthResponse::$response['invalid_request_client_secret']['code']];
		$this->apiChk($this->getUrl('oauth/authrize', $params), $expected, '[oauth authrize for false clientSecret]:');

		//error for responseType
		$params['clientSecret'] = self::CLIENT_SECRET;
		$params['responseType'] = 'sdcj';
		$expected = ['"code":' . oauthResponse::$response['invalid_request_response_type']['code']];
		$this->apiChk($this->getUrl('oauth/authrize', $params), $expected, '[oauth authrize for false responseType]');

		//error for redirectUri
		$params['responseType'] = 'code';
		$params['redirectUri'] = 'http://www.lingtm.com';
		$expected = ['"code":' . oauthResponse::$response['invalid_request_redirect_uri']['code']];
		$this->apiChk($this->getUrl('oauth/authrize', $params), $expected, '[oauth authrize for false redirectUri]');

		//error for scope
		$params['redirectUri'] = self::REDIRECT_URI;
		$params['scope'] = 'dks,ckv,admin';
		$expected = ['"code":' . oauthResponse::$response['invalid_request_scope']['code']];
		$this->apiChk($this->getUrl('oauth/authrize', $params), $expected, '[oauth authrize for false scope]');
	}
}