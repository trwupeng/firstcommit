<?php
namespace Tests\Index\Oauth;
include realpath(__DIR__ . '/../../../conf') . '/inc4tests.php';
/**
 * Created by PhpStorm.
 * User: LTM <605415184@qq.com>
 * Date: 2015/10/21
 * Time: 15:43
 */
class RefreshTest extends \Sooh\Base\Tests\ApiHttpGetJson {
	const REFRESH_TOKEN = '18d4b22a80f3a0bfffd2a86c4ac60786';
	const ACCESS_TOKEN = 'c51fca866a50e0bd90b2f8d154b4ed1f';
	public function testDefault() {
		$params = [
			'refreshToken' => self::REFRESH_TOKEN,
			'__VIEW__' => 'json',
		];

		$this->apiChk($this->getUrl('oauth/refresh', $params), ['"code":200'], 'oauth refresh for success');

		$params['refreshToken'] = 'ckx923rsdp';
		$expected = ['"code":' . \Lib\Oauth\OauthResponse::$response['invalid_request_refresh_token']['code']];
		$this->apiChk($this->getUrl('oauth/refresh', $params), $expected, 'oauth refresh for false');
	}
}