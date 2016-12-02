<?php
include realpath(__DIR__.'/../../../conf').'/inc4tests.php';
/**
 * sample
 *
 * @author simon.wang
 */
class DetailTest extends \Sooh\Base\Tests\ApiHttpGetJson {
	protected $bombsForError=[

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
        $db = $this->getDB();
        $sql = " SELECT * FROM db_p2p.tb_wares_0 WHERE statusCode >= 10 LIMIT 1 ";
        $result = $result = $db->execCustom(['sql'=>$sql]);
        $rs = $db->fetchAssocThenFree($result);
        $waresId = $rs[0]['waresId'];
		$this->apiChk($this->getUrl('Financing/detail', ['__VIEW__'=>'json','id'=>$waresId]),['code'=>'200'],'hahaha');
	}
}
