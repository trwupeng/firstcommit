<?php
/**
 *
 * 渠道和协议添加接口
 *
 * 用户app打包时调用接口自动添加渠道和协议
 * 只接受渠道和协议的关键信息录入
 * 渠道扣量、是否展示数据、authcode的配置采用建表的默认值，扣量、展示渠道数据、authcode配置需要在后台手动配置
 *
 */

class ApiaddnewcontractController extends Yaf_Controller_Abstract {
    // xiaoxia20160503
    const token = 'd0df230674978cf7f834638c0830d07d';

    /**
     * 添加渠道
     */
    public  function addcopartnerAction()
    {
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $copartnerId = $this->_request->get('copartnerId')-0; // 渠道号
        $copartnerName = $this->_request->get('copartnerName'); // 渠道名称
        $copartnerAbs = strtolower($this->_request->get('copartnerAbs')); // 渠道简称
        $authCode     = $this->_request->get('authCode');
        $contractorBiz = $this->_request->get('contractorBiz'); // 业务
        $contractorDev = $this->_request->get('contractorDev'); // 技术
        $dt = $this->_request->get('dt'); // 时间戳
        $sign = $this->_request->get('sign');
        $md5Str = 'dt='.$dt
            .'&token='.self::token;
//var_log($md5Str, 'md5str>>>>>>');
        $tmp_sign = md5($md5Str);
//var_log($tmp_sign, 'tmp_sign>>>>>>');
//var_log($sign, 'sign>>>>>>>>>>>');
        if($sign != $tmp_sign) {
            $code = 201;
            $msg ='验证失败';
        }elseif(!is_numeric($copartnerId) || strlen($copartnerId) !== 4){
            $code = 201;
            $msg = '参数错误：渠道号格式错误';
        }elseif(!ctype_alnum($copartnerAbs)){
            $code = 201;
            $msg = '参数错误：渠道简称格式错误';
        }elseif(empty($copartnerName)){
            $code = 201;
            $msg = '参数错误：渠道名称不能为空';
        }else {
            $n = \Prj\Data\Copartner::loopGetRecordsCount(['copartnerId'=>$copartnerId]);
            if(!$n) {
                $record = [
                    'copartnerId'       => $copartnerId,
                    'copartnerName'     => $copartnerName,
                    'copartnerAbs'      => $copartnerAbs,
                ];

                if(!empty($contractorBiz)){
                    $record['contractorBiz'] = $contractorBiz;
                }
                if(!empty($contractorDev)){
                    $record['contractorDev'] = $contractorDev;
                }
                if(!empty($authCode)) {
                    $record['authCode'] = $authCode;
                }

                $copartner_obj = \Prj\Data\Copartner::getCopy();
                try{
                    \Sooh\DB\Broker::errorMarkSkip();
                    $copartner_obj->db()->addRecord($copartner_obj->tbname(), $record);
                    $code = 200;
                    $msg = '渠道添加成功';
                }catch (\ErrorException $e){
                    if(\Sooh\DB\Broker::errorIs($e)){
                        $code = 200;
                        $msg = '渠道已经存在';
                    }else{
                        $code = 201;
                        $msg = $e->getMessage();
                    }
                }
                $copartner_obj->free();
            }else {
                $code = 200;
                $msg = '渠道已经存在';
            }
        }

        $this->_view->assign('resultcode', $code);
        $this->_view->assign('message', $msg);
    }

    /**
     * 添加协议
     */
    public function addcontractAction()
    {
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $contractId = $this->_request->get('contractId') - 0;
        $ymdStart = $this->_request->get('ymdStart');
        $ymdEnd = $this->_request->get('ymdEnd');
        $remarks = $this->_request->get('remarks');
        $promotionWay = $this->_request->get('promotionWay');
        $notes = $this->_request->get('notes');
        $sign = $this->_request->get('sign');
        $dt = $this->_request->get('dt'); // 时间戳
        $md5str = 'dt='.$dt
            . '&token='.self::token;
        $tmp_sign = md5($md5str);

//        var_log($md5str, 'md5str>>>>>>');
//        var_log($tmp_sign, 'tmp_sign>>>>>>');
//        var_log($sign, 'sign>>>>>>>>>>>');

        if ($sign != $tmp_sign) {
            $code = 201;
            $msg = '验证失败';
        } elseif (!is_numeric($contractId) || strlen($contractId) != 18) {
            $code = 201;
            $msg = '参数错误：协议号格式错误';
        } elseif (!$this->_checkdate($ymdStart) || !$this->_checkdate($ymdEnd) || $ymdStart > $ymdEnd) {
            $code = 201;
            $msg = '参数错误：日期错误';
        } elseif (empty($promotionWay)) {
            $code = 201;
            $msg = '参数错误：推广方式不能为空';
        } else {
            $copartnerId = substr($contractId, 0, 4);
            $copartner_obj = \Prj\Data\Copartner::getCopy(['copartnerId'=>$copartnerId]);
            $copartner_obj->load();
            if (!$copartner_obj->exists()) {
                $code = 201;
                $msg = '渠道不存在';
            } else {
                $n = \Prj\Data\Contract::loopGetRecordsCount(['contractId' => $contractId]);
                $promotionWays = \Prj\Data\Contract::$promotionWay;
                if ($n) {
                    $code = 200;
                    $msg = '协议号已经存在';
                } elseif(!in_array($promotionWay, $promotionWays)) {
                    $code = 201;
                    $msg = '后台不存在此推广方式';
                } else {
                    $copartnerAbs = $copartner_obj->getField('copartnerAbs');
                    $record = [
                        'contractId' => $contractId,
                        'copartnerAbs' => $copartnerAbs,
                        'ymdStart' => $ymdStart,
                        'ymdEnd' => $ymdEnd,
                        'promotionWay' => $promotionWay,
                        'remarks' => $remarks,
                    ];
                    if (!empty($notes)) {
                        $record['notes'] = $notes;
                    }

                    $contract_obj = \Prj\Data\Contract::getCopy();
                    try {
                        \Sooh\DB\Broker::errorMarkSkip();
                        $contract_obj->db()->addRecord($contract_obj->tbname(), $record);
                        $code = 200;
                        $msg = '协议添加成功';
                    } catch (\ErrorException $e) {
                        if (\Sooh\DB\Broker::errorIs($e)) {
                            $code = 200;
                            $msg = '协议号已经存在';
                        } else {
                            $code = 201;
                            $msg = $e->getMessage();
                        }
                    }
                }

                $copartner_obj->free;
            }
        }
        $this->_view->assign('resultcode', $code);
        $this->_view->assign('message', $msg);
    }

    private function _checkdate ($ymd) {
        $y = date('Y', strtotime($ymd))-0;
        $m = date('m', strtotime($ymd))-0;
        $d = date('d', strtotime($ymd))-0;
        return checkdate($m, $d, $y);
    }

    public function inquerycopartnerAction() {
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $copartnerId = $this->_request->get('copartnerId');
        if(empty($copartnerId)) {
            $this->_view->assign('resultcode', 201);
            $this->_view->assign('message', '参数错误');
            return;
        }

        $sign = $this->_request->get('sign');
        $dt = $this->_request->get('dt'); // 时间戳
        $md5str = 'dt='.$dt.'&token='.self::token;
        $tmp_sign = md5($md5str);

        if($sign!== $tmp_sign){
            $code=201;
            $msg = '验证失败';

        }else{
            $n = \Prj\Data\Copartner::loopGetRecordsCount(['copartnerId'=>$copartnerId]);
            if($n) {
                $code=200;
                $msg = '渠道存在';
            }else{
                $code = 202;
                $msg = '渠道不存在';
            }
        }
        $this->_view->assign('resultcode', $code);
        $this->_view->assign('message', $msg);
    }

    public function inquerycontractAction (){
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $contractId = $this->_request->get('contractId');
        if(empty($contractId)) {
            $this->_view->assign('resultcode', 201);
            $this->_view->assign('message', '参数错误');
            return;
        }

        $sign = $this->_request->get('sign');
        $dt = $this->_request->get('dt'); // 时间戳
        $md5str = 'dt='.$dt.'&token='.self::token;
        $tmp_sign = md5($md5str);
        if($sign !== $tmp_sign) {
            $code= 201;
            $msg='验证失败';
        }else{
            $n = \Prj\Data\Contract::loopGetRecordsCount(['contractId'=>$contractId]);
            if($n){
                $code=200;
                $msg='协议存在';
            }else{
                $code=202;
                $msg='协议不存在';
            }
        }

        $this->_view->assign('resultcode', $code);
        $this->_view->assign('message', $msg);
    }
}


