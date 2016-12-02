<?php
namespace Tests\Index\Passport;

include realpath(__DIR__ . '/../../../conf') . '/inc4tests.php';

/**
 * Created by PhpStorm.
 * User: LTM <605415184@qq.com>
 * Date: 2015/11/9
 * Time: 9:50
 */
class checkinTest extends \Sooh\Base\Tests\ApiHttpGetJson {
	const PHONE = 18616700069;
	const PASSWORD = '111111';
	const CLIENT_ID = '1104878344';
	const CLIENT_SECRET = 's20vH9emKJ6BmT1Q';
	const REDIRECT_URI = 'https://www.baidu.com/';
	const ACCOUNT_ID = '81568478941117';

	private $callbackValue = null;

	protected $bombsForError = [];

	protected function resetAfterEachApiChk($stepId = '') {
		switch ($stepId) {
			case '':

				break;
		}
	}

	/**
	 * passport login
	 */
	public function testLogin() {
		$oauthParams = [
			'phone' => self::PHONE,
		    'password' => self::PASSWORD,
		    'clientId' => self::CLIENT_ID,
		    'clientSecret' => self::CLIENT_SECRET,
		    'redirectUri' => self::REDIRECT_URI,
		    '__VIEW__' => 'json',
		];
		//oauth login
		$this->apiChk($this->getUrl('oauth/applogin', $oauthParams), 'loginBack', 'oauth applogin for checkinTest');
		$code = $this->callbackValue['oauth_applogin']['info']['code'];

		//passport login
		$passportParams = [
			'code' => $code,
		    'redirectUri' => self::REDIRECT_URI,
			'__VIEW__' => 'json',
		];
		$this->apiChk($this->getUrl('passport/login', $passportParams), ['"code":200'], 'passport login for checkinTest');
	}

	/**
	 * @depends testLogin
	 */
	public function testDefault() {
		$checkinParams = [
			'dowhat' => 'checkin',
		    'withBonus' => 1,
			'__VIEW__' => 'json',
		];
		//clear checkin records
		$clearParams = [
			'phone' => self::PHONE,
		    'cameFrom' => 'phone',
		    'clearBalance' => 0,
			'__VIEW__' => 'json',
		];
		$this->apiChk($this->getUrl('dev/clearCheckin', $clearParams), ['"code":200'], 'clear checkin records');

		//the first checkin of the day
		$this->apiChk($this->getUrl('actives/checkin', $checkinParams), 'checkinBack', 'first checkin');

		//the second check in of the day
		$this->apiChk($this->getUrl('actives/checkin', $checkinParams), ['"code":400'], 'second checkin');

		//test bombs
//		$this->bombsForError = [
//			'CheckinBook_Decode_Return' => ['bombsForRecords'],
//			'CheckinBook_GiveBinus_Return_BonusPlan' => ['bombsForBonusPlan'],
//		];
//		$this->apiChk($this->getUrl('actives/checkin', $checkinParams), ['"code":400'], 'third checkin');

	}

	/**
	 * callback function for oauth/applogin
	 * @param string $json callback value
	 * @param string $errDesc err message
	 */
	public function loginBack($json, $errDesc) {
		$ret = json_decode($json, true);
		if ($ret['code'] == 200) {
			$this->callbackValue['oauth_applogin'] = $ret;
		} else {
			$this->assertTrue(false, 'loginBack:' . $json);
		}
	}

	/**
	 * callback function for actives/checkin
	 * @param string $json callback value
	 * @param string $errDesc err message
	 */
	public function checkinBack($json, $errDesc) {
		$ret = json_decode($json, true);
		if ($ret['code'] == 200) {
			$this->assertEquals($ret['data']['ymd'], \Sooh\Base\Time::getInstance()->YmdFull);
			$this->assertEquals($ret['data']['checked'][count($ret['data']['checked']) - 1]['ymd'], \Sooh\Base\Time::getInstance()->YmdFull);
			$this->assertEquals($ret['data']['todaychked'], 1);
		} else {
			$this->assertTrue(false, 'checkinBack:' . $json);
		}
	}

	/**
	 * callback function for bombs return today records
	 * @param string $json callback value
	 * @param string $errDesc err msg
	 */
	public function bombsForRecords($json, $errDesc) {
		$ret = json_decode($json, true);
		if ($ret['code'] == 200) {
			$this->assertEquals($ret['data']['ymd'], \Sooh\Base\Time::getInstance()->YmdFull);
			$this->assertEquals($ret['data'][count($ret['data']) - 1]['ymd'], \Sooh\Base\Time::getInstance()->YmdFull);
		} else {
			$this->assertTrue(false, 'bombsForRecords:' . $json);
		}
	}

	/**
	 * callbact function for bombs return bonus plan
	 * @param string $json callback value
	 * @param string $errDesc error msg
	 */
	public function bombsForBonusPlan($json, $errDesc) {
		$ret = json_decode($json, true);
		if ($ret['code'] == 200) {

		} else {
			$this->assertTrue(false, 'bombsForBonusPlan:' . $json);
		}
	}
}