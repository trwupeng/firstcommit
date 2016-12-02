<?php
namespace Prj\Consts;
/**
 * Description of Banks
 * @author simon.wang
 */
class BankStop
{
    public static $banks = [
        'boc'   => '中国银行',
        'abc'   => '农业银行',
        'ccb'   => '建设银行',
        'icbc'  => '工商银行',
        'cmb'   => '招商银行',
        'citic' => '中信银行',
        'cmbc'  => '民生银行',
        'gdb'   => '广发银行',
        'cib'   => '兴业银行',
        'ceb'   => '光大银行',
        'bos'   => '上海银行',
        'psbc'  => '邮储银行',
        'hxb'   => '华夏银行',
        'szpab' => '平安银行',
        'spdb'  => '浦发银行',
        'comm'  => '交通银行',
    ];

    public static $plan = [
         ['szpab','2016-10-13 18:00:00','2016-10-16 18:00:00'],
         ['psbc','2016-10-13 1:00:00','2016-10-13 3:00:00'],
         //['cmb','2016-10-12 1:00:00','2016-10-12 17:09:00'],
         //['icbc','2016-10-12 1:00:00','2016-10-12 17:09:00'],
    ];
}
