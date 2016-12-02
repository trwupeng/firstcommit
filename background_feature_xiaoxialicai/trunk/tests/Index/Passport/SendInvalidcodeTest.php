<?php
namespace Tests\Index\Passport;
include realpath(__DIR__ . '/../../../conf') . '/inc4tests.php';
/**
 * Created by PhpStorm.
 * User: LTM <605415184@qq.com>
 * Date: 2015/10/19
 * Time: 16:15
 */

class SendInvalidcodeTest extends \Sooh\Base\Tests\ApiHttpGetJson {
	protected $bombsForError=[
		'Passport_SendInvalidcode_Return_Code' => 'bombsCallback',
	];

	/**
	 * 每一次触发炸弹后的回调方法
	 * @param array $arr $this->apiChe()第四个参数原样返回
	 * @param string $stepId bombID
	 * @throws ErrorException
	 */
	protected function resetAfterEachApiChk($stepId = '') {
		if ($stepId == 'Passport_SendInvalidcode_Return_Code') {
//			$phone = $arr['phone'];
//			$returnVal = json_decode($this->bombsJson, true);
//			$smsCode = \Sooh\DB\Cases\SMSCode::getCopy($phone);
//			$smsCode->load();
//			$this->assertTrue($smsCode->exists());
//			$dat = $smsCode->getField('dat');
//			$this->assertEquals($returnVal['data']['invalidCode'], array_pop($dat['codes']));
		} else {

		}
	}

	public function testDefault() {
		$phone = '1708888' . sprintf('%04d', rand(0, 9999));
		$params = [
			'phone' => $phone,
			'__VIEW__' => 'json',
		];
		$this->taskData = ['phone' => $phone];
		$this->apiChk($this->getUrl('passport/sendInvalidcode', $params), ['"code":200'], 'test send invalidCode for phone:' . $phone);
		$this->bombsForError = null;

		$smsCode = \Sooh\DB\Cases\SMSCode::getCopy($phone);
		$smsCode->load();
		$this->assertTrue($smsCode->exists());
		$dat = $smsCode->getField('dat');
		$this->assertArrayHasKey('codes', $dat);
	}
}