<?php
namespace Prj\Data;

/**
 * Class ShortenedUrl
 * @package Prj\Data
 * @author LTM <605415184@qq.com>
 */
class ShortenedUrl extends \Sooh\DB\Base\KVObj {

	public static function paged($pager, $where = [], $order = null, $fields = '*') {
		$sys = self::getCopy('');
		$db = $sys->db();
		$tb = $sys->tbname();

		$maps = [];
		$maps = array_merge($maps, $where);
		$pager->init($db->getRecordCount($tb, $maps), -1);

		if (empty($order)) {
			$order = 'rsort createTime';
		}

		$rs = $db->getRecords($tb, $fields, $maps, $order, $pager->page_size, $pager->rsFrom());
		return $rs;
	}

	/**
	 * 根据短链ID获取contractId[包含随机]
	 * @param string $shortId 短链ID
	 * @return mixed
	 */
	public static function getContractId($shortId) {
		$sys = self::getCopy('', '');
		$db = $sys->db();
		$tb = $sys->tbname();

		$ret = $db->getRecords($tb, 'contractId, scale', ['shortId' => $shortId, 'status' => 1], 'rsort createTime');
		foreach ($ret as $v) {
			$arr[$v['contractId']] = $v['scale'];
		}
		return self::getRand($arr);
	}

	public static function getCopy($shortId, $contractId) {
		return parent::getCopy(['shortId' => $shortId, 'contractId' => $contractId]);
	}

	protected static function splitedTbName($n, $isCache) {
		return 'tb_shortened_url_' . ($n % static::numToSplit());
	}

	protected static function idFor_dbByObj_InConf($isCache) {
		return 'default';
	}

	/**
	 * 获取一个随机值
	 * @param array $rule 随机规则:['1_100'=>99,'100_900'=>891,'900_1000'=>10]
	 * @return int
	 */
	private function getRand($rule) {
		$result  = '';
		$ruleSum = array_sum($rule);

		//概率数组循环
		foreach ($rule as $key => $val) {
			$randNum = mt_rand(1, $ruleSum);
			if ($randNum <= $val) {
				$result = $key;
				break;
			} else {
				$ruleSum -= $val;
			}
		}
		unset ($rule);

		$loc = strpos($result, '_');
		if ($loc) {
			$result = mt_rand(substr($result, 0, $loc), substr($result, $loc + 1));
		}
		return $result;
	}
}