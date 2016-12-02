<?php
namespace Tests\Index\Passport;
include realpath(__DIR__ . '/../../../conf') . '/inc4tests.php';
/**
 * Created by PhpStorm.
 * User: LTM <605415184@qq.com>
 * Date: 2015/10/20
 * Time: 17:29
 */
class GetInvitationCodeTest extends \Sooh\Base\Tests\ApiHttpGetJson {
	public function testDefault() {
		$myInvitationCode = 'tgtkjgh';//18616700069的优惠码
		$params = [
			'code' => '8727b912af1da1a4ef36fa28c0212b2c',//万能code，对应的手机号为18616700069
			'redirectUri' => 'https://www.baidu.com/',
			'__VIEW__' => 'json',
		];

		$this->apiChk($this->getUrl('passport/getInvitationCode', $params), ['"code":200'], 'get invitationCode for code:' . $params['code']);

		$ret = $this->jsonstrByHttpGet($this->getUrl('passport/getInvitationCode', $params));
		$ret = json_decode($ret, true);

		$this->assertEquals($myInvitationCode, $ret['info']['invitationCode']);
	}
}