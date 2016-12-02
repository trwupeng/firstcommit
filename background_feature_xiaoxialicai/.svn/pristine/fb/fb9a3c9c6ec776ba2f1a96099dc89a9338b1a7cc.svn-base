<?php
namespace Prj\Data;
/**
 * Created by PhpStorm.
 * User: JDP <mutor@126.com>
 * Date: 2016/05/12
 * Time: 16:59
 */
class Sqlmanage extends \Sooh\DB\Base\KVObj {

	/**
	 * 分页
	 * @param \Sooh\DB\Pager $pager 分页类
	 * @param array  $where 查询条件
	 * @param null   $order 排序条件
	 * @param string $fields 要查询的字段
	 * @return mixed array
	 */
	public static function paged($pager, $where = [], $order = null, $fields = '*') {
		$sys = self::getCopy(['sqlId'=>0]);
		$db = $sys->db();
		$tb = $sys->tbname();

		$maps = [];

		$maps = array_merge($maps, $where);
		$pager->init($db->getRecordCount($tb, $maps), -1);

		if (empty($order)) {
			$order = 'rsort createTime';
		} else {
			$order = str_replace('_', ' ', $order);
		}

		$rs = $db->getRecords($tb, $fields, $maps, $order, $pager->page_size, $pager->rsFrom());
		return $rs;
	}


	protected static function splitedTbName($n, $isCache) {
		return 'tb_sql_manage';
	}

	protected static function idFor_dbByObj_InConf($isCache) {
		return 'default';
	}

}