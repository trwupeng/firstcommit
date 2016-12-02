<?php
include realpath(__DIR__.'/../../../conf').'/inc4tests.php';
/**
 * sample
 *
 * @author simon.wang
 */
class ByshelfTest extends \Sooh\Base\Tests\ApiHttpGetJson {
	protected $bombsForError=[
        'wares_is_empty'=>['msg'=>'content_empty']
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
        /*
        $wares = \Prj\Data\Wares::getCopy('tgh');
        $arr = $wares->db()->getRecord($wares->tbname(),array('waresId','statusCode'),array());
        //var_log($arr);
        $waresId = $arr['waresId'];
        $statusCode = $arr['statusCode'];
        $ret = $wares->db()->updRecords($wares->tbname(),array('statusCode'=>11),array('waresId'=>$waresId));
        */
		$this->apiChk($this->getUrl('Financing/byshelf', ['__VIEW__'=>'json']),['"code":200','order'],'error');
        /*
        $rett = $wares->db()->updRecords($wares->tbname(),array('statusCode'=>$statusCode),array('waresId'=>$waresId));
        */
	}
}
