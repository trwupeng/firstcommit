<?php
namespace Tests\Index\Oauth;
include realpath(__DIR__ . '/../../../conf') . '/inc4tests.php';
/**
 * Created by PhpStorm.
 * User: LTM <605415184@qq.com>
 * Date: 2015/10/21
 * Time: 15:40
 */
class ApploginTest extends \Sooh\Base\Tests\ApiHttpGetJson {
	const CLIENT_ID = '1104878344';
	const CLIENT_SECRET = 's20vH9emKJ6BmT1Q';
	const REDIRECT_URI = 'https://www.baidu.com/';

	public function testDefault() {
		$params = [
			'phone' => 18616700069,
			'password' => 111111,
			'clientId' => self::CLIENT_ID,
			'clientSecret' => self::CLIENT_SECRET,
			'redirectUri' => self::REDIRECT_URI,
			'__VIEW__' => 'json',
		];

		$this->apiChk($this->getUrl('oauth/applogin', $params), ['"code":200'], 'oauth applogin for success');

		$params['phone'] = 'ckx923rsdp';
		$this->apiChk($this->getUrl('oauth/applogin', $params), ['"code":400'], 'oauth applogin for false');

		$params['phone'] = '17011110000';
		$this->apiChk($this->getUrl('oauth/applogin', $params), ['"code":400'], 'oauth applogin for false');

		$params['phone'] = '18616700069';
		$params['password'] = 'xcviwefazjk)q2';
		$this->apiChk($this->getUrl('oauth/applogin', $params), ['"code":400'], 'oauth applogin for false');

		$params['password'] = '111111';
		$params['clientId'] = 'xcviwefazjk)q2';
		$this->apiChk($this->getUrl('oauth/applogin', $params), ['"code":400'], 'oauth applogin for false');

		$params['clientId'] = self::CLIENT_ID;
		$params['clientSecret'] = 'xcviwefazjk)q2';
		$this->apiChk($this->getUrl('oauth/applogin', $params), ['"code":400'], 'oauth applogin for false');
	}
}