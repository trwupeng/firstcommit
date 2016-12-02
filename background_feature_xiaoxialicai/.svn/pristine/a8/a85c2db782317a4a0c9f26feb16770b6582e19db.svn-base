<?php
/**
 *
 * 此脚本需要在api接口服务器中是用，或者在报表系统中使用。前提是服务器的gloabls.php数据库配置相同。
 *
 * 读取的文件数据格式  idfa  userId
 * php idfatrans.php idfa协议号名单 注册起始日期 注册结束日期
 *
 * 从device表中找到idfa对应的用户ID。 此用户符合此idfa注册的日期范围，原来的协议为0, idfa对应的协议号正确，方更改对应用户的contractId和copartnerId。
 * 转换执行完成后需要，重新跑起始注册日期以来的报表数据。
 * 重跑报表数据使用脚本
 *
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/4/19 0019
 * Time: 下午 7:34
 */
define("APP_PATH",  dirname(__DIR__)); /* 指向public的上一级 */
include dirname(__DIR__).'/conf/globals.php';
include dirname(__DIR__).'/conf/autoload.php';
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

if(sizeof($argv) != 4) {
    die('错误：命令格式错误'."\n".'示例：php idfatrans.php filenameOfInput 20160412 20160413'."\n");
}





$fileNameToSave = './'.date('Y-m-d').'idfa_user.txt';
if(file_exists($fileNameToSave)){
    unlink($fileNameToSave);
}
file_put_contents($fileNameToSave, 'idfa,协议号,用户Id,注册日期,备注'."\n", FILE_APPEND);

/**
 * 读取的idfa对应contractId列表、开始注册日期、结束注册日期
 */
$filename= $argv[1];
$ymdRegFrom = $argv[2];
$ymdRegTo = $argv[3];
if($ymdRegFrom > $ymdRegTo) {
    die('错误：起始注册日期大于结束注册日期'."\n");
}


$handle = fopen($filename, "r");
if ($handle) {
    while (!feof($handle)) {
        $buffer = fgets($handle);
        $match = [];
        preg_match_all('/([0-9a-zA-z]+)/', $buffer, $match);
        $match = $match[0];
        if(!empty($match)){
            $idfa = $match[0];
            $contractId = $match[1];

            $str = $idfa.','.$contractId.',';
            preg_match('/^\d{18}$/', $contractId, $match_contract);
            if(empty($match_contract)) {
                $str .= ''.','.''.','.'协议号不是18位数字'."\n";
                file_put_contents($fileNameToSave, $str, FILE_APPEND);
                continue;
            }

            $copartnerId = substr($contractId, 0, 4);
//var_log($copartnerId, 'copartnerId.>>>>');
            $copartner_obj = \Prj\Data\Copartner::getCopy(['copartnerId'=>$copartnerId]);
            $copartner_obj->load();
            if(!$copartner_obj->exists()){
                $str .= ''.','.''.','.'渠道管理中找不到此协议号对应的渠道'."\n";
                file_put_contents($fileNameToSave, $str, FILE_APPEND);
                continue;
            }

            $device_obj = \Lib\Logs\Device::getCopy('idfa:'.$idfa);
            $device_obj->load();
            if(!$device_obj->exists()){
                $str .= ''.','.''.','.'device表中未找到idfa'."\n";
            }else{
                $userId = $device_obj->getField('userId', true);
                if(!empty($userId)){
                    $user_obj = \Prj\Data\User::getCopy($userId);
                    $user_obj->load();
                    if(!$user_obj->exists()){
                        $str .= ''.','.''.','.'device表中找的userId在生产user表未找到'."\n";
                    }else {
                        $ymdReg = $user_obj->getField('ymdReg');
                        $copartnerIdFromUser = $user_obj->getField('copartnerId');
                        $contractIdFromUser = $user_obj->getField('contractId');
                        $clientType = $user_obj->getField('clientType');
                        if($ymdReg>= $ymdRegFrom && $ymdReg<= $ymdRegTo && $contractIdFromUser == 0 && $clientType == 901 &&$copartnerIdFromUser == 0){
                            $user_obj->setField('contractId', $contractId);
                            $user_obj->setField('copartnerId', substr($contractId, 0, 4));
                            try{
                                $user_obj->update();
                                $str .= $userId.','.$ymdReg.'成功'."\n";
                            }catch(\ErrorException $e) {
                                $str .= ''.','.''.','.$e->getMessage()."\n";
                            }
                        }else {
                            $str .= ''.','.$ymdReg.','.'注册日期不符合搜索日期范围或已经有协议号或clientType不是901[contractId='.$contractIdFromUser.'][clientType='.$clientType.'][copartnerId='.$copartnerIdFromUser.']'."\n";
                        }
                    }
                }else{
                    $str .= ''.','.''.','.'device表中idfa没有对应的userId'."\n";
                }

            }
            file_put_contents($fileNameToSave, $str, FILE_APPEND);
        }

    }
    fclose($handle);
}


//
//$db_rpt = \Sooh\DB\Broker::getInstance('dbForRpt');
//$db_produce = \Sooh\DB\Broker::getInstance('default');






//$contractid_ymdregfrom= $db_rpt->getRecord('db_p2prpt.tb_ios_contract_trans', 'contractId,ymdRegFrom', ['taskId'=>$taskId]);
//$contractId = $contractid_ymdregfrom['contractId'];
//$ymdRegFrom = date('Y-m-d', strtotime($contractid_ymdregfrom['ymdRegFrom']));
//$copartnerId = substr($contractId, 0, 4);
//
//$users= $db_rpt->getCol('db_p2prpt.tb_ios_contract_trans', 'userId', ['taskId'=>$taskId, 'userId>'=>0]);
//
//if(!empty($users)) {
//    $db_rpt->updRecords('db_p2prpt.tb_user_final', ['copartnerId'=>$copartnerId, 'contractId'=>$contractId], ['userId'=>$users]);
//}
//
//
//$arr_cmd = [
//    'Standalone.CrondBindcard',
//    'Standalone.CrondNewOrder',
//    'Standalone.CrondRecharges',
//    'Standalone.CrondVouchers',
//
//];
//
//$cmd = 'php loopall2.php Standalone.CrondBindcard '.$ymdRegFrom;
//error_log('####'.$cmd);
//exec($cmd);
//
//$cmd = 'php loopall2.php Standalone.CrondBindcard '.$ymdRegFrom;
//error_log('####'.$cmd);
//exec($cmd);
//
//$cmd = 'php loopall2.php Standalone.CrondBindcard '.$ymdRegFrom;
//error_log('####'.$cmd);
//exec($cmd);
//
//$cmd = 'php loopall2.php Standalone.CrondBindcard '.$ymdRegFrom;
//error_log('####'.$cmd);
//exec($cmd);





