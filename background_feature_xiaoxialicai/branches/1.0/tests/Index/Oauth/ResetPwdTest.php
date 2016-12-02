<?php
namespace Tests\Index\Oauth;
include realpath(__DIR__ . '/../../../conf') . '/inc4tests.php';
/**
 * Created by PhpStorm.
 * User: LTM <605415184@qq.com>
 * Date: 2015/10/21
 * Time: 15:43
 */
class ResetPwdTest extends \Sooh\Base\Tests\ApiHttpGetJson {
	const PHONE = 18616700069;
	const INVALIDCODE = 123456;
	const ACCESS_TOKEN = 'c51fca866a50e0bd90b2f8d154b4ed1f';

	public function testDefault() {
		$params = [
			'phone' => self::PHONE,
			'invalidCode' => 123456,
			'newPwd' => 111111,
			'cameFrom' => 'phone',
			'__VIEW__' => 'json',
		];

		$this->apiChk($this->getUrl('oauth/resetPwd', $params), ['"code":200'], 'oauth resetPwd for success');
		$this->bombsForError = null;

		$params['invalidCode'] = 'cks5ie';
		$expected = ['"code":400'];
		$this->apiChk($this->getUrl('oauth/resetPwd', $params), $expected, 'oauth resetPwd for false');

		$params['cameFrom'] = 'ydke';
		$expected = ['"code":400'];
		$this->apiChk($this->getUrl('oauth/resetPwd', $params), $expected, 'oauth resetPwd for false');
	}
}