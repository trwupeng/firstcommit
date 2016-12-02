<?php
namespace Prj\Data;

/**
 * Class Message
 * @package Prj\Data
 * @author  LTM <605415184@qq.com>
 */
class Message extends \Sooh\DB\Base\KVObj {

	/**
	 * 获取列表
	 * 仅后台用
	 * @param \Sooh\DB\Pager $pager  分页类
	 * @param array          $where  查询条件
	 * @param string         $order  排序条件
	 * @param string         $fields 字段
	 * @return array
	 */
	public static function paged( $pager = null, $where = [], $order = null, $fields = '*') {
		$model = self::getCopy('');
		$db    = $model->db();
		$tb    = $model->tbname();

		$maps = [];
		$maps = array_merge($maps, $where);
		if (empty($order)) {
			$order = 'rsort createTime';
		} else {
			$order = str_replace('_', ' ', $order);
		}

		if (empty($pager)) {
			$rs = $db->getRecords($tb, $fields, $maps, $order);
		} else {
			$rs = $db->getRecords($tb, $fields, $maps ? : null, $order, $pager->page_size, $pager->rsFrom());
		}
		return $rs;
	}

	/**
	 * 全部设为已读
	 * @param int $userId 用户ID
	 * @return bool
	 */
	public static function readAll($userId)
	{
		$model = self::getCopy($userId);
		$db = $model->db();
		$tb = $model->tbname();

		$maps = [
			'receiverId' => $userId,
		    'status' => \Prj\Consts\Message::status_unread,
		];

		$rs = $db->getRecords($tb, 'msgId', $maps);
		if ($rs) {
			foreach ($rs as $k => $v) {
				$_dbMsg = self::getCopy($v['msgId']);
				$_dbMsg->load();
				if ($_dbMsg->exists()) {
					$_dbMsg->setField('status', \Prj\Consts\Message::status_read);
					$_dbMsg->update();
				}
			}
		}
		return true;
	}

	public static function getAccountNum($where) {
		return static::loopGetRecordsCount($where);
	}

	/**
	 * @param int $msgId
	 * @return \Sooh\DB\Base\KVObj
	 */
	public static function getCopy($msgId) {
		return parent::getCopy(['msgId' => $msgId]);
	}

	protected static function splitedTbName($n, $isCache) {
		return 'tb_message_' . ($n % static::numToSplit());
	}

	protected static function idFor_dbByObj_InConf($isCache) {
		return 'messages';
	}
}