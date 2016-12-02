<?php

namespace Prj\Tool;

/**
 * Excel工具类
 *
 * @author wu.chen
 */
class ExcelTool {

    /**
     * 导出EXCEL文件
     * @param $records 导出数据
     * @param $title 标题
     * @param $filename 导出文件名
     * @param $scientificFlg 标示
     * @paran ext
     */
    public static function outExcel($records, $title = null, $filename = 'page_', $ext = '.xls') {
        $filename .= date('Y_m_d');
        $ini = \Sooh\Base\Ini::getInstance();
        $ini->viewRenderType('echo');
        header("Pragma:public");
        header("Expires:0");
        header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type:application/vnd.ms-execl;charset=gb2312");
        header("Content-Type:application/octet-stream");
        header("Content-Type:application/download");
        header('Content-Disposition:attachment;filename="' . $filename . $ext . '"');
        header("Content-Transfer-Encoding:binary");
        if (empty($title)) {
            $title = array_keys(current($records));
        }
        echo iconv('utf-8', 'gbk', implode("\t", $title)) . "\n";
        foreach ($records as $r) {
            $r = array_map(function($v) {
                if (is_numeric($v) && strlen($v) > 11) {
                    return 'ID:' . $v;
                } else {
                    return $v;
                }
            }, $r);
            echo iconv('utf-8', 'gbk//TRANSLIT', implode("\t", $r)) . "\n";
        }
    }

}
