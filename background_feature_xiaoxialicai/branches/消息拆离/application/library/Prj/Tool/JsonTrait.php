<?php

//JSON处理的相关小工具

namespace Prj\Tool;

/**
 * Class JsonTrait
 * @package Prj\Tool
 * @author lingtm <605415184@qq.com>
 */
trait JsonTrait
{
    public static $JSON_ERROR_ARRAY = [
        JSON_ERROR_NONE           => 'No error',
        JSON_ERROR_DEPTH          => 'Maximum stack depth exceeded',
        JSON_ERROR_STATE_MISMATCH => 'State mismatch (invalid or malformed JSON)',
        JSON_ERROR_CTRL_CHAR      => 'Control character error, possibly incorrectly encoded',
        JSON_ERROR_SYNTAX         => 'Syntax error',
        JSON_ERROR_UTF8           => 'Malformed UTF-8 characters, possibly incorrectly encoded'
    ];

    /**
     * 将string转换为json
     * @param string $str     待转换的string
     * @param bool   $toArr   当该参数为 TRUE 时，将返回 array 而非 object 。
     * @param int    $depth   User specified recursion depth.
     * @param int    $options Bitmask of JSON decode options. Currently only JSON_BIGINT_AS_STRING is supported
     *                        (default is to cast large integers as floats)
     * @return int|mixed|string
     */
    protected function parseJson($str, $toArr = true, $depth = 512, $options = 0)
    {
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
                    function json_last_error_msg()
                    {
                        $error = json_last_error();
                        return isset(self::$JSON_ERROR_ARRAY[$error]) ? self::$JSON_ERROR_ARRAY[$error] : 'Unknown error';
                    }
                }
                return json_last_error_msg();
            }
        }
    }
}