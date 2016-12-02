<?php
namespace Tests\Index\Oauth;
include realpath(__DIR__ . '/../../../conf') . '/inc4tests.php';
/**
 * Created by PhpStorm.
 * User: LTM <605415184@qq.com>
 * Date: 2015/10/21
 * Time: 15:40
 */
class SendInvalidcodeTest extends \Sooh\Base\Tests\ApiHttpGetJson {
	public function testDefault() {
		$phone = '1708888' . sprintf('%04d', rand(0, 9999));

		$this->apiChk($this->getUrl('oauth/sendInvalidcode', ['phone' => $phone, '__VIEW__' => 'json']), ['"code":200'], 'oauth sendInvalidcode for success');

		$this->apiChk($this->getUrl('oauth/sendInvalidcode', ['phone' => '18616700069', '__VIEW__' => 'json']), ['"code":400'], 'oauth sendInvalidcode for false');
	}
}