<?php
namespace Tests\Index\Oauth;
include realpath(__DIR__ . '/../../../conf') . '/inc4tests.php';
/**
 * Created by PhpStorm.
 * User: LTM <605415184@qq.com>
 * Date: 2015/10/21
 * Time: 15:42
 */
class UserInfoTest extends \Sooh\Base\Tests\ApiHttpGetJson {
	const ACCESS_TOKEN = '944c4e2952858af33a87264a13fc1fd3';
	public function testDefault() {
		$params = [
			'accessToken' => self::ACCESS_TOKEN,
			'__VIEW__' => 'json',
		];

		$this->apiChk($this->getUrl('oauth/userInfo', $params), ['"code":200'], 'oauth userInfo for success');

		$params['accessToken'] = 'ckx923rsdp';
		$expected = ['"code":' . \Lib\Oauth\OauthResponse::$response['invalid_request_access_token']['code']];
		$this->apiChk($this->getUrl('oauth/userInfo', $params), $expected, 'oauth userInfo for false');
	}
}