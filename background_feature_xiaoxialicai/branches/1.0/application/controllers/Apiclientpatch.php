<?php
/**
 *
 * 客户端获取更新版本信息接口
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/3/17 0017
 * Time: 下午 8:02
 */

class ApiclientpatchController extends Yaf_Controller_Abstract {
    public  function forclientpatchAction()
    {

        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $clientType = $this->_request->get('clientType');
        $clientVer = $this->_request->get('clientVer');
        $contractId = $this->_request->get('contractId');
        if($contractId == 'LONG_NAME'){
            $contractId = 900120160426110000;
        }


        $db = \Sooh\DB\Broker::getInstance('default');

        $verInfo = [];
        $where = ['clientType'=>$clientType, 'contractId'=>[$contractId, -1]];
        $arr_autoid_ver = $db->getAssoc('tb_clientPatch', 'autoid', 'ver,enforce,info,url,full', $where,
            'rsort ver1 rsort ver2 rsort ver3 rsort ver4');

//error_log(\Sooh\DB\Broker::lastCmd());
//        var_log($arr_autoid_ver, '获取对应的所有版本信息======================');
        if (empty($arr_autoid_ver)) {
            $this->_view->assign('clientPatch', ['code'=>200]);
			return;
        }

        $tmp_request_ver = explode('.', $clientVer);
        $size_request_ver = (sizeof($tmp_request_ver));
        $gotit = false;
        foreach ($arr_autoid_ver as $autoid => $record) {
            $tmp_record_ver = explode('.', $record['ver']);
            $size_record_ver = sizeof($tmp_record_ver);
            $maxSize = ($size_request_ver > $size_record_ver ? $size_request_ver : $size_record_ver);

            foreach($tmp_record_ver as $sk => $sver) {
                if($sver > $tmp_request_ver[$sk]){
                    $verInfo[$autoid] = $record;
                    if($record['full'] == 1){
                        $gotit = true;
                    }
                    break;
                }elseif(($sver == $tmp_request_ver[$sk]) && ($sk != $maxSize-1)){
                    continue;
                }elseif(($sver == $tmp_request_ver[$sk]) && ($sk == $maxSize-1)){
                    $gotit=true;
                    break;
                }else {
                    $gotit = true;
                    break;
                }
            }
            if($gotit){
                break;
            }
        }
        $verInfo = array_reverse($verInfo);
//var_log($verInfo, '最终需要的版本信息=================');
        $this->_view->assign('code', 200);
        $this->_view->assign('clientPatch', $verInfo);
    }
}

