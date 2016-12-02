<?php
namespace Tests\Index\Passport;

include realpath(__DIR__ . '/../../../conf') . '/inc4tests.php';
/**
 * Created by PhpStorm.
 * User: LTM <605415184@qq.com>
 * Date: 2015/10/20
 * Time: 18:25
 */
class LoginTest extends \Sooh\Base\Tests\ApiHttpGetJson {
	protected $bombsForError = [
		'Passport_Login_Return_AccountId' => ["81568478941117"],
	    'Passport_Login_Return_InvitationCode' => 'bombsCallback',
	    'Passport_Onlogin_returnAccountInfo' => 'bombsCallback',
	];

	protected function resetAfterEachApiChk($stepId = '') {
		switch ($stepId) {
			case 'Passport_Login_Return_InvitationCode':

				break;
			case 'Passport_Onlogin_returnAccountInfo':
				$result = json_decode($this->bombsJson, true);
				$this->assertArrayHasKey('accountId', $result['data']);
				$this->assertArrayHasKey('nickname', $result['data']);
				break;
		}
	}

	public function testDefault() {
		$params = [
			'code' => '8727b912af1da1a4ef36fa28c0212b2c',//万能code，对应的手机号为18616700069
			'redirectUri' => 'https://www.baidu.com/',
			'__VIEW__' => 'json',
		];
		$result = ['"code":200', '"info":{"accountId":"'];

		$this->apiChk($this->getUrl('passport/login', $params), $result, 'passport/login for code:' . $params['code']);
		$this->bombsForError = null;
	}
}