<?php
namespace Prj\Data;

/**
 * Class SNSWechat
 * @package Prj\Data
 * @author  LTM <605415184@qq.com>
 */
class SNSWechat extends \Sooh\DB\Base\KVObj {

	public static function getByUserid($userid)
	{
		$sys = self::getCopy('');
		$db = $sys->db();
		$tb = $sys->tbname();

		$ret = $db->getRecord($tb, '*', ['userId' => $userid]);
		return $ret;
	}

	//TODO 废弃，存在歧义性
//	public static function bandUser($openid, $userid, $phone = '')
//	{
//		$sys = self::getCopy($openid);
//		$sys->load();
//		$sys->setField('userId', $userid);
//		var_log($userid, '<<<<<userid');
//		if (!empty($phone)) {
//			$sys->setField('loginName', $phone);
//		}
//		$sys->update();
//		return true;
//	}

//	/**
//	 * 获取列表
//	 * @param \Sooh\DB\Pager $pager  分页类
//	 * @param array          $where  查询条件
//	 * @param string         $order  排序条件
//	 * @param string         $fields 字段
//	 * @return array
//	 */
//	public static function paged($pager = null, $where = [], $order = null, $fields = '*') {
//		$model = self::getCopy('');
//		$db    = $model->db();
//		$tb    = $model->tbname();
//
//		$maps = [];
//		$maps = array_merge($maps, $where);
//		if (empty($order)) {
//			$order = 'rsort createTime';
//		} else {
//			$order = str_replace('_', ' ', $order);
//		}
//
//		if (empty($pager)) {
//			$rs = $db->getRecords($tb, $fields, $maps, $order);
//		} else {
//			$rs = $db->getRecords($tb, $fields, $maps ? : null, $order, $pager->page_size, $pager->rsFrom());
//		}
//		return $rs;
//	}
//
//	public static function getAccountNum($where) {
//		return static::loopGetRecordsCount($where);
//	}

	public static function getCopy($openId) {
		return parent::getCopy(['openId' => $openId]);
	}

	protected static function splitedTbName($n, $isCache) {
		return 'tb_sns_wechat_' . ($n % static::numToSplit());
	}

	protected static function idFor_dbByObj_InConf($isCache) {
		return 'default';
	}
}