<?php
namespace Prj\WaresTpl\Interfaces;
/**
 * 标的的编辑逻辑接口
 *
 * @author simon.wang
 */
interface Editor {
	//put your code here
    /**
     * 处理来自表单的参数
     * @param $ext
     * @return mixed
     */
    public static function getDataFromForm($ext);

    public static function getCopy($arr);
}
