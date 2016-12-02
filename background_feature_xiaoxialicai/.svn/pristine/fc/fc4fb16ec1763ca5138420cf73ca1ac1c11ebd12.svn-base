<?php
namespace Tests\Index\Passport;

include realpath(__DIR__ . '/../../../conf') . '/inc4tests.php';
/**
 * Created by PhpStorm.
 * User: LTM <605415184@qq.com>
 * Date: 2015/10/20
 * Time: 16:54
 */
class CheckInvalidcodeTest extends \Sooh\Base\Tests\ApiHttpGetJson {
	/**
	 * 正式测试前的依赖，并返回手机号和验证码
	 * @return ['phone' => '', 'code' => '', 'expiress' => '']
	 */
	public function testSend() {
		$phone = '1708888' . sprintf('%04d', rand(0, 9999));
		$params = [
			'phone' => $phone,
			'__VIEW__' => 'json',
		];
		$this->apiChk($this->getUrl('passport/sendInvalidcode', $params), ['"code":200'], 'test send invalidCode for phone:' . $phone);

		$smsCode = \Sooh\DB\Cases\SMSCode::getCopy($phone);
		$smsCode->load();
		$this->assertTrue($smsCode->exists());
		$dat = $smsCode->getField('dat');
		$this->assertArrayHasKey('codes', $dat);
		end($dat['codes']);
		return ['phone' => $phone, 'code' => key($dat['codes']), 'expiress' => current($dat['codes'])];
	}

	/**
	 * @depends testSend
	 */
	public function testDefault(array $stack) {
		$params = [
			'phone' => $stack['phone'],
			'invalidCode' => $stack['code'],
			'__VIEW__' => 'json',
		];

		$this->apiChk($this->getUrl('passport/checkInvalidcode', $params), ['"code":200'], 'test checkInvalidcode for params:' . serialize($params));
	}
}