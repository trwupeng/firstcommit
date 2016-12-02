<?php
namespace Prj\Misc;
/**
 * 一些功能函数，将来可能调整位置
 *
 * @author simon.wang
 */
class Funcs {
	/**
	 * 平台产生的各种零头
	 * @param number $num
	 * @param string $desc
	 */
	public static function addOdd($num,$desc)
	{
		\Sooh\DB\Broker::getInstance()->addRecord('db_p2p.tb_odd', ['odd'=>$num,'desc'=>$desc]);
	}
	
	public static function uriReal($arg)
	{
		
	}
	
	public static function copartnerIdWithcopartnerName() {
		$rs = \Sooh\Db\Broker::getInstance('default')
				->getAssoc('db_p2p.tb_copartner_0', 'copartnerId', 'copartnerName, copartnerAbs');
		return $rs;
	}
}
