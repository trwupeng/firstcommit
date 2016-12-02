<?php
namespace Prj\Data;

use Sooh\Base\ErrException;
use Sooh\DB\Base\KVObj;

/**
 * 意见反馈
 * Class Feedback
 * @package Prj\Data
 * @author  LTM <605415184@qq.com>
 */
class Feedback extends KVObj {
	const errServerBusy = '服务器忙';

	/**
	 * 未读
	 */
	const status_unread = 0;

	/**
	 * 已读
	 */
	const status_read = 1;

	/**
	 * 已处理
	 */
	const status_handled = 2;

	public static function paged($pager, $where = [], $order = null, $fields = '*') {
//		$rs = self::loopGetRecordsPage(array('timeCreate'=>'rsort','subkey'=>'voucherId'),array('where'=>$where,), $pager);

//        if(!empty($where['userId']))
//        {
            $sys = self::getCopy('');
            $db = $sys->db();
            $tb = $sys->tbname();

            $maps = [
//			'voucherType' => [\Prj\Consts\Voucher::type_fake, \Prj\Consts\Voucher::type_yield],
//			'statusCode]' => 0
            ];
            $maps = array_merge($maps, $where);
            $pager->init($db->getRecordCount($tb, $maps), -1);
            if (empty($order)) {
                $order = 'rsort createTime';
            } else {
                $order = str_replace('_', ' ', $order);
            }

            $rs = $db->getRecords($tb, $fields, $maps, $order, $pager->page_size, $pager->rsFrom());
//        }
//        else
//        {
//            $pager->init(self::loopGetRecordsCount($where), -1);
//            $rs = self::loopFindRecords($where);
//        }

		return $rs;
	}

	/**
	 * 创建一条新的意见反馈
	 * @param string $deviceId 唯一设备ID
	 * @param string $content  反馈内容
	 * @param string $userId   创建者ID
	 * @param array  $extends  扩展内容
	 * @return string 反馈ID
	 * @throws ErrException
	 */
	public static function createNew($deviceId, $content, $userId = null, $extends = null) {
		if ($userId) {
			$feedbackId = mt_rand(1000, 9999) . sprintf('%03d', mt_rand(0, 999)) . substr($userId, -4);
		} else {
			$feedbackId = mt_rand(1000, 9999) . sprintf('%03d', mt_rand(0, 999)) . '0000';
		}

		$i = 0;
		while ($i < 10) {
			$dbFeedback = self::getCopy($feedbackId);
			$dbFeedback->load();
			if (!$dbFeedback->exists()) {
				break;
			} else {
				$i++;
			}
		}
		if ($i >= 10) {
			throw new ErrException(self::errServerBusy);
		}

		if (!empty($userId)) {
			//TODO check userId
			$dbFeedback->setField('userId', $userId);
		}
		if (!empty($extends) && is_array($extends)) {
			$dbFeedback->setField('extends', json_encode($extends));
		}
		
	    $dbFeedback->setField('content',json_encode($content));
		$dbFeedback->setField('deviceId', $deviceId);
		//$dbFeedback->setField('content', $content);
		$dbFeedback->setField('createTime', \Sooh\Base\Time::getInstance()->ymdhis());
		$dbFeedback->setField('status', self::status_unread);
		$dbFeedback->update();
		return $feedbackId;
	}

	public static function getCopy($feedbackId) {
		return parent::getCopy(['feedbackId' => $feedbackId]);
	}
	public function getAccountNum($where)
	{
	    return static::loopGetRecordsCount($where);
	}
	
	protected static function splitedTbName($n, $isCache) {
		return 'tb_feedback_' . ($n % static::numToSplit());
	}

	protected static function idFor_dbByObj_InConf($isCache) {
		return 'feedback';
	}
}