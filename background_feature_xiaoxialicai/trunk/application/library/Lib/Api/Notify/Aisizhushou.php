<?php
namespace Lib\Api\Notify;
use Lib\Api\Notify\Base;

/**
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/7/1 0001
 * Time: 上午 11:58
 */

class Aisizhushou extends Base{


    const APPID = '1097441868';
    const tbname = 'tb_copartner_notify';

    public function onInstalled ($args) {
        $appid = $args['appid'];
        $idfa = str_replace('-', '', $args['idfa']);
        if($appid !== self::APPID || empty($idfa)){
            return $this->resultReturn(false, 'parameter error');
        }

        $record = [
            'deviceId'          => 'idfa:'.$idfa,
            'appId'             => $appid,
            'copartnerId'       => parent::getCopartnerIdByAbs(strtolower($args['copartnerabs'])),
            'dtInstallNotify'   => date('YmdHis', \Sooh\Base\Time::getInstance()->timestamp()),
        ];
        if(!empty($args['mac'])) {
            $record['mac'] = $args['mac'];
        }
        if(!empty($args['openudid'])){
            $record['openUDID'] = $args['openudid'];
        }
        if(!empty($args['os'])) {
            $record['OSVer'] = $args['os'];
        }
        if(!empty($args['callback'])) {
            $record['callback'] = urldecode($args['callback']);
        }
//var_log($record, 'record####');
        $db_rpt = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_p2prpt);
        try{
            \Sooh\DB\Broker::errorMarkSkip();
            $db_rpt->addRecord(\Rpt\Tbname::tb_copartner_notify, $record);
        }catch(\ErrorException $e){
            if (\Sooh\DB\Broker::errorIs($e)){
                return $this->resultReturn(false, 'repeat notification');
            }else{
                return $this->resultReturn(false, 'save record failed');
            }
        }

        return $this->resultReturn(true, 'save record success');
    }

    public function onStartUpToCallBack($args) {
        $db_rpt = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_p2prpt);
        $deviceId = $args['deviceId'];
        $dtActivated = $args['ymd'].sprintf('%06d', $args['hhiiss']);
        $record = $db_rpt->getRecord(\Rpt\Tbname::tb_copartner_notify, '*', ['deviceId'=>$deviceId]);

//var_log($args, '回调传参：');
//var_log($record, '根据参数idfa查询的此设备号的信息');
        if(empty($record) || $record['flagActivatedNotfy'] == 1) {
            error_log('不满足条件，结束执行');
            return;
        }


        if ($record['dtInstallNotify'] > $dtActivated) {
            // 安装通知时间 比激活通知时间晚60秒之内，也算的。
            if(strtotime($record['dtInstallNotify']) - strtotime($dtActivated) > 60) {
                error_log('不满足条件，结束执行');
                return;
            }
        }

        if(!empty($record['callback'])) {
            $updrecord = [
                'isActivated'=>1,
                'dtActivated'=>$dtActivated,
            ];

            $url = $record['callback'];
            $ret = \Sooh\Base\Tools::httpGet($url);

//var_log($ret, '激活后回调结果#######');

            $ret = json_decode($ret, true);
            if($ret['success'] === 'true'){
                $updrecord['flagActivatedNotfy'] = 1;
            }elseif($ret['success'] === 'false') {
                $updrecord['flagActivatedNotfy'] = 0; // 回调返回失败
            }else {
                $updrecord['flagActivatedNotfy'] = -1;  // 其他错误
                error_log('ErrorOnAisizhushou onStartUpToCallBack###'.$deviceId.' receive result:'.$ret);
            }

            $r =  $db_rpt->updRecords(\Rpt\Tbname::tb_copartner_notify, $updrecord, ['deviceId'=>$args['deviceId'], 'flagActivatedNotfy!'=>1]);
//error_log('激活结果更新####'.\Sooh\DB\Broker::lastCmd());

            if($r!==true && is_numeric($r)){
                error_log($args['deviceId'].'激活通知结果更新成功');
            }else {
                error_log($args['deviceId'].'激活通知结果更新失败');
            }
        }

    }


    protected function resultReturn($result, $msg) {
        return [
            'success'=>$result,
            'message'=>$msg,
        ];
    }
}