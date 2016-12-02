<?php
include realpath(__DIR__.'/../../../conf').'/inc4tests.php';
/**
 * sample
 *
 * @author simon.wang
 */
class IndexTest extends \Sooh\Base\Tests\ApiHttpGetJson {
	protected $bombsForError=[
		'devTest'=>['arg_error'],
	];
	protected function getDB() {
		return \Sooh\DB\Broker::getInstance();
	}
	protected function initSetMC($act='index'){
		parent::initSetMC(__DIR__.'/'.$act);
	}
	protected function getUrl($mca,$args)
	{
		return trim(\Sooh\Base\Ini::getInstance()->get('uriBase.www'),'/').'/'.trim($mca).'?'.http_build_query($args);
	}
	public function testDefault()
	{
		$this->apiChk($this->getUrl('dev/test', ['sdf'=>123]),['123','"_":"index.php"'],'hahaha');
		//$this->apiChk('http://192.168.56.140/dev/test?sdf=123',['1234'],'hahaha');
		$r = $this->getRecord('db_p2p.tb_wares_0', ['waresId'=>'1233456437856']);
		$this->assertEquals($r['waresName'],'wares001');
		$this->assertFalse(empty($r));
		$r = $this->getRecord('\Prj\Data\Investment', ['ordersId'=>'1014439500292497856']);
		$this->assertEquals($r['ordersId'],'1014439500292497856');
		
		$this->assertContains('$cmp',$this->jsonstrByHttpGet($this->getUrl('dev/test', ['sdf'=>123])),'error-tip');
	}
}
