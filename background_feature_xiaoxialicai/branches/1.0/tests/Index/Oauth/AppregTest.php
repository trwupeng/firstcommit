<?php
namespace Tests\Index\Oauth;
include realpath(__DIR__ . '/../../../conf') . '/inc4tests.php';
/**
 * Created by PhpStorm.
 * User: LTM <605415184@qq.com>
 * Date: 2015/10/21
 * Time: 15:38
 */
class AppregTest extends \Sooh\Base\Tests\ApiHttpGetJson {
	const CLIENT_ID = '1104878344';
	const CLIENT_SECRET = 's20vH9emKJ6BmT1Q';
	const REDIRECT_URI = 'https://www.baidu.com/';

	protected $bombsForError = [
		'Oauth_Appreg_returnAuthrizeFlag' => ['"code":200'],
		'Oauth_Appreg_returnAccountFlag' => ['"code":200'],
	];

	public function testDefault() {
		$phone = '1708888' . sprintf('%04d', rand(0, 9999));
		$params = [
			'phone' => $phone,
			'invalidCode' => 123456,
			'password' => 111111,
			'invitationCode' => 'tgtkjgh',
			'contractId' => '110',
			'clientType' => '110',
			'clientId' => self::CLIENT_ID,
			'clientSecret' => self::CLIENT_SECRET,
			'redirectUri' => self::REDIRECT_URI,
			'__VIEW__' => 'json',
		];
		foreach($params as $key => &$val) {
			if (empty($val)) {
				unset($params[$key]);
			}
		}
		$this->apiChk($this->getUrl('oauth/appreg', $params), ['"code":200'], 'oauth appreg for success');
		$this->bombsForError = null;

		//test false phone
		$params['phone'] = '12219829321';
		$this->apiChk($this->getUrl('oauth/appreg', $params), ['"code":400'], 'oauth appreg for false');

		//test false invalidCode
		$params['phone'] = '1708888' . sprintf('%04d', rand(0, 9999));
		$params['invalidCode'] = 'c,siide';
		$this->apiChk($this->getUrl('oauth/appreg', $params), ['"code":400'], 'oauth appreg for false');

		//test false password
		$params['invalidCode'] = 123456;
		$params['password'] = '_,cisje22i30sxkv8893rnfvzs;vlwsdluiwiflxcv)dl2j';
		$this->apiChk($this->getUrl('oauth/appreg', $params), ['"code":400'], 'oauth appreg for false');

		//test false invitationCode
		$params['password'] = 111111;
		$params['invitationCode'] = 'cksssssss9e42lajs;ldv;zlxkjcv09u23_F0p1!@';
		$this->apiChk($this->getUrl('oauth/appreg', $params), ['"code":400'], 'oauth appreg for false');

		//test false contractId
		$params['invitationCode'] = 'tgtkjgh';
		$params['contractId'] = 'kxcivqwef_+@!I$';
		$this->apiChk($this->getUrl('oauth/appreg', $params), ['"code":400'], 'oauth appreg for false');

		//test false clientType
		$params['contractId'] = '110';
		$params['clientType'] = 'KKCJVOQ@R*(!#)CKjvwfoqw';
		$this->apiChk($this->getUrl('oauth/appreg', $params), ['"code":400'], 'oauth appreg for false');

		//test false clientId
		$params['clientType'] = '110';
		$params['clientId'] = 'KKCJVOQ@R*(!#)CKjvwfoqw';
		$this->apiChk($this->getUrl('oauth/appreg', $params), ['"code":400'], 'oauth appreg for false');

		//test false clientSecret
		$params['clientId'] = self::CLIENT_ID;
		$params['clientSecret'] = 'KKCJVOQ@R*(!#)CKjvwfoqw';
		$this->apiChk($this->getUrl('oauth/appreg', $params), ['"code":400'], 'oauth appreg for false');

		//test false redirectUri
//		$params['clientSecret'] = self::CLIENT_SECRET;
//		$params['redirectUri'] = 'KKCJVOQ@R*(!#)CKjvwfoqw';
//		$this->apiChk($this->getUrl('oauth/appreg', $params), ['"code":400'], 'oauth appreg for false');
	}
}