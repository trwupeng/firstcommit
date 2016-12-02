<?php
/**
 *
 *
 * 批量执行Crondsgrab 目录中的命令 此目录中如果增加新的目录则需要手动在代码中添加新目录，以此获取新目录的命令。
 *
 *
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/4/19 0019
 * Time: 下午 7:34
 */

set_time_limit(0);
if (!function_exists('var_log')) {
    function var_log($var, $msg = '')
    {
        error_log($msg . ' ' . var_export($var, true));
    }
}
if (!function_exists('echo_log')) {
    function echo_log($var, $msg = '')
    {
        echo "<pre>" . $msg . ' ' . var_export($var, true) . "</pre>";
    }
}

if(sizeof($argv) != 2) {
    die('参数错误，示例：php runall.php 起始日期'."\n".'起始如期格式：20160606');
};
$ymdFrom = date('Y-m-d', strtotime($argv[1]));


$arr_cmd=[
    'Standalone.CrondNewRegister',
    'Standalone.CrondBindcard',
    'Standalone.CrondProducts',
    'Standalone.CrondNewOrder',
    'Standalone.CrondRecharges',
    'Standalone.CrondVouchers',
    'Standalone.CrondCopartnerWorth',

    'RptDaily.EDAccounts',
    'RptDaily.EDBindCard',
    'RptDaily.EDBindCardByClient',
    'RptDaily.EDBuyers',
    'RptDaily.EDProducts',
    'RptDaily.EDRealnameAuth',
    'RptDaily.EDStocking',
    'RptDaily.EDStockingByClient',
];

foreach($arr_cmd as $cmd) {
    $cmd = 'php loopall2.php '.$cmd.' '.$ymdFrom;
error_log('###########cmd:'.$cmd);
    $lastline = exec($cmd);
error_log('###########lastLline:'.$lastline);

}


// 单独执行扣量 （扣量的数据只能执行到前一天）
$ymdTo = date('Y-m-d', time()-86400);
$cmd = 'php loopall2.php Standalone.CrondTagUserForDisplay '.$ymdFrom.' '.$ymdTo;
error_log('##########扣量cmd:'.$cmd);
$lastline = exec($cmd);
error_log('##########扣量lastline:'.$lastline);



