<?php
/**
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/6/28 0028
 * Time: 下午 3:44
 */
function echo_log($var, $msg=''){
    echo "<pre>".$msg." ".var_export($var,true)."</pre>";
}
function var_log($var, $msg = '')
{
    error_log($msg . ' ' . var_export($var, true));
}

$basedir = '/var/www/logs/';
$ymd = $argv[1];
var_log($ymd, 'ymd####');
if(!empty($ymd)) {
    $tmp_arr = explode('-', $ymd);
    if(sizeof($tmp_arr)!= 3) {
        die('日期格式：xxxx-xx-xx'."\n");
    }

    if(!checkdate($tmp_arr[1], $tmp_arr[2], $tmp_arr[0])){
        die('日期不正确'."\n");
    }

    $ymd = date('Y-m-d', strtotime($ymd));

    $filename2save = $basedir.'clientver_'.$ymd.'.txt';
    if(file_exists($filename2save)) {
        unlink($filename2save);
    }

    if(false!= ($handle=opendir($basedir))) {
        while(false!==($file=readdir($handle))){
            if(strpos($file, 'php_errors_'.$ymd) !==false) {
                save2File($basedir.$file, $filename2save);
            }
        }
        closedir($handle);
    }
}

if(empty($ymd)) {
    $filename2save = $basedir.'clientver_'.date('Y-m-d').'.txt';
    if(file_exists($filename2save)) {
        unlink($filename2save);
    }
    save2File($basedir.'php_errors.log', $filename2save);
}
error_log('over');

function save2File ($filename2read, $filename2save) {
error_log('###检索文件:'.$filename2read);
error_log('###保存文件:'.$filename2save);
    $log = new SplFileObject($filename2read);
    foreach($log as $line) {
        if((strpos($line, '"evt":"start_up"' )!==false || strpos($line, '"evt":"wake_up"'))!==false && strpos($line, 'loger\\/applog')!==false) {
            $data =  json_decode(substr($line, strpos($line, '{')), true);
            if(isset($data['evt']) && isset($data['clientVer']) && isset($data['contractId'])) {
                $str = $data['sarg1'].','.$data['contractId'].','.$data['clientVer']."\n";
                file_put_contents($filename2save, $str, FILE_APPEND);
            }
		}
    }
}