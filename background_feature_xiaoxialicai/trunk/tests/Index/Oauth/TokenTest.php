<?php
namespace Tests\Index\Oauth;
include realpath(__DIR__ . '/../../../conf') . '/inc4tests.php';
/**
 * Created by PhpStorm.
 * User: LTM <605415184@qq.com>
 * Date: 2015/10/21
 * Time: 15:41
 */
class TokenTest extends \Sooh\Base\Tests\ApiHttpGetJson {
	const CODE = '8727b912af1da1a4ef36fa28c0212b2c';
	const REDIREDT_URI = 'https://www.baidu.com/';

	public function testDefault() {
		$params = [
			'code' => self::CODE,
		    'redirectUri' => self::REDIREDT_URI,
		    '__VIEW__' => 'json',
		];

		$this->apiChk($this->getUrl('oauth/token', $params), ['"code":200'], 'oauth token for success');

		$params['code'] = 'ckx923rsdp';
		$expected = ['"code":' . \Lib\Oauth\OauthResponse::$response['invalid_request_code']['code']];
		$this->apiChk($this->getUrl('oauth/token', $params), $expected, 'oauth token for false');

		/**
		 * 这步中，redirectUri并没有参与校验
		 */
//		$params['code'] = self::CODE;
//		$params['redirectUri'] = 'http:c.sjdfa;';
//		$expected = ['"code":' . \Lib\Oauth\OauthResponse::$response['invalid_request_redirect_uri']['code']];
//		$this->apiChk($this->getUrl('oauth/token', $params), $expected, 'oauth token for false');
	}
}