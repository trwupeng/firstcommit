<?php
/**
 * Created by PhpStorm.
 * User: LTM <605415184@qq.com>
 * Date: 2016/2/24
 * Time: 18:09
 */

namespace Prj\Data;

class Config
{

	/**
	 * 获取系统配置，支持单个字段与分组获取
	 * @param string $name 配置名/组名
	 * @param bool $parseJson 是否解析JSON
	 * @return mixed
	 */
	public static function get($name, $parseJson = false)
	{
		$config = \Prj\Data\TbConfigItem::getCopy($name);
		$config->load();
		if($config->exists()===false){
			return null;
		}
		$chk = $config->getField('extlimit');
		if(empty($chk)){
			$ret = $config->getField('v');
			if (is_string($ret) && $parseJson) {
				if (json_decode($ret, true)) {
					$ret = json_decode($ret, true);
				}
			}
			return $ret;
		}else{
			$r = explode(',',$config->getField('v'));
			return $r[\Prj\Data\TbConfigLimit::parseStr($chk)->getIndex()];
		}
	}
	/**
	 * 
	 * @param string $k
	 * @param string $v 逗号分隔的数据
	 * @param \Prj\Data\TbConfigLimit $limit
	 */
	public static function set($k,$v,$limit)
	{
		
	}
	
	public static function getLists($kLike=null)
	{
		
	}

	/**
	 * 完全解析json
	 * json解析数字型字符串的时候会不抱错而直接返回整数值，所以做了此函数
	 * @param string     $str 待解析的字符串
	 * @param bool|false $toArr 是否转换为数组
	 * @param int        $depth 深度
	 * @param int        $options options
	 * @return int|mixed|string 当为string时代表解析失败，string为错误原因
	 */
	private static function parseJson($str, $toArr = false, $depth = 512, $options = 0) {
		if (is_numeric($str)) {
			return intval($str);
		} elseif (!is_string($str)) {
			return 'The string is not a valid';
		} else {
			if (json_decode($str, $toArr, $depth, $options) && json_last_error() === JSON_ERROR_NONE) {
				return json_decode($str, $toArr, $depth, $options);
			} else {
				//解析出错
				if (!function_exists('json_last_error_msg')) {
					function json_last_error_msg() {
						static $ERRORS = array(
							JSON_ERROR_NONE => 'No error',
							JSON_ERROR_DEPTH => 'Maximum stack depth exceeded',
							JSON_ERROR_STATE_MISMATCH => 'State mismatch (invalid or malformed JSON)',
							JSON_ERROR_CTRL_CHAR => 'Control character error, possibly incorrectly encoded',
							JSON_ERROR_SYNTAX => 'Syntax error',
							JSON_ERROR_UTF8 => 'Malformed UTF-8 characters, possibly incorrectly encoded'
						);

						$error = json_last_error();
						return isset($ERRORS[$error]) ? $ERRORS[$error] : 'Unknown error';
					}
				} else {
					return json_last_error_msg();
				}
			}
		}
	}
}