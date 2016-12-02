<?php
namespace Prj\WaresTpl\Interfaces;
/**
 * 标的的显示逻辑接口
 *
 * @author simon.wang
 */
interface Viewer {
	//put your code here

    public static function getCopy($arr);

    public static function getImgList($content);

    public static function format($data);
}
