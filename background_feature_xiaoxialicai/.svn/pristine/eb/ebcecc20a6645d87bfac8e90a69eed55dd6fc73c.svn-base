<?php
use Sooh\Base\Form\Item as form_def;
use Sooh\Base\Form\Options as options_def;
use Sooh\Base\ErrException;
use Sooh\Base\Form\Error;

/**
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/3/22 0022
 * Time: 下午 4:55
 */

class ExcodeController extends \Prj\ManagerCtrl {


    protected $pageSizeEnum =[30, 50, 100];
    protected $grpIdWithBatchId;
    public function init() {
        parent::init();
        $this->grpIdWithBatchId = \Prj\Data\ExchangeCode::grpIdWithBatchId();
    }

    public function indexAction (){

        $isRevoke = $this->_request->get('revoke');

        $pageid = $this->_request->get('pageId', 1) - 0;
        $pagesize = $this->_request->get('pageSize', current($this->pageSizeEnum)) - 0;
        $pager = new \Sooh\DB\Pager($pagesize, $this->pageSizeEnum, false);
        if(empty($this->grpIdWithBatchId)) {
            $arr_grp_id = [''=>'无数据'];
            $grpId = '';
        }else{
            $tmp = array_keys($this->grpIdWithBatchId);
            $arr_grp_id = array_combine($tmp, $tmp);
            $grpId = key($arr_grp_id);
        }

        $form = \Sooh\Base\Form\Broker::getCopy('default')
            ->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_s);
        $form -> addItem('_grpId_eq', form_def::factory('分组', $grpId, form_def::select)->initMore(new options_def($arr_grp_id)))
            ->addItem('_batchId_eq', form_def::factory('批次', '', form_def::text))
            ->addItem('pageid', $pageid)
            ->addItem('pagesize', $pager->page_size);
        $form->fillValues();
        if($form->flgIsThisForm) {
            $where = $form->getWhere();
            if($where['batchId='] == 'all') {
                unset($where['batchId=']);
            }
        }

        $pkey = $this->_request->get('_pkey');
        if(!empty($pkey)) {
            $where = \Prj\Misc\View::decodePkey($pkey);
        }


        if(!empty($where)) {
            $pager->init(\Prj\Data\ExchangeCode::totalBatch($where), $pageid);
            $isDownloadExcel = $this->_request->get('__EXCEL__');
            if(!empty($isDownloadExcel)) {
                $batchInfo = \Prj\Data\ExchangeCode::batchInfo($where['grpId'],$where['batchId']);
                if(empty($batchInfo)) {
                    //$this->returnError('未找到此批次信息的记录');
                    $this->returnError(\Prj\Lang\Broker::getMsg('excode.batch_unfound'));
                }else {
                    if($isDownloadExcel == 'notexchanged'){
                        $records = \Prj\Data\ExchangeCode::getExchangeCode($where['grpId'], $where['batchId'], false);
                    }elseif($isDownloadExcel == 'exchanged'){
                        $records = \Prj\Data\ExchangeCode::getExchangeCode($where['grpId'], $where['batchId'], true);
                    }

                    if(!empty($records)) {
                        foreach($records as $k => $v) {
                            $tmp = [];
                            foreach($this->downloadExcelFields as $column => $value) {
                                if($column == 'grpId'|| $column == 'batchId' || $column == 'intro' || $column == 'bonusini') {
                                    $tmp[$column] = $batchInfo[$column];
                                }

                                if($column == 'dtExpire') {
                                    $tmp[$column] = date('Y-m-d H:i:s', $v[$column]);
                                }

                                if($column == 'excode') {
                                    $tmp[$column] = $v[$column];
                                }
                                if($column == 'dtFetch') {
                                    if($v[$column] > 0) {
                                        $tmp[$column] = date('Y-m-d H:i:s', $v[$column]);
                                    }else {
                                        $tmp[$column] = '未兑换';
                                    }
                                }

                            }
                            $records[$k] = $tmp;
                        }
                    }
                    return $this->downExcel($records, $this->downloadExcelFields);
                }

            }else {

                $records = \Prj\Data\ExchangeCode::usageByBachId($where, $pager->page_size, $pager->rsFrom());
                foreach($records as $k => $v) {
                    $_pkey = \Prj\Misc\View::encodePkey(['grpId'=>$v['grpId'], 'batchId'=>$v['batchId']]);
                    $records[$k]['_pkey'] = $_pkey;
                    $records[$k]['dtExpire'] = date('Y-m-d H:i:s', $v['dtExpire']);
                }
                $this->_view->assign('records', $records);
            }

            $this->_view->assign('where', $where);
        }



        $tmp = \Prj\Data\ExchangeCode::$grp_info;
        foreach($tmp as $v) {
            $headers [$v[0]] = $v[1];
        }
        $this->_view->assign('headers', $headers);
        $this->_view->assign('pager', $pager);
//        var_log($where, 'where>>>>>>>>>');

    }


    protected $downloadExcelFields = [
        'grpId'=>'分组',
        'batchId'=>'批次',
        'intro'=>'描述',
        'bonusini'=>'奖励',
        'dtExpire'=>'过期日期',
        'excode'=>'兑换码',
        'dtFetch'=>'兑换时间',
    ];

    public function addnewAction () {
       
    
        if(!empty($this->grpIdWithBatchId)){
            $arr_grp_id = array_keys($this->grpIdWithBatchId);
            $arr_grp_id = array_combine($arr_grp_id, $arr_grp_id);
        }else {
            $arr_grp_id = ['暂无分组'=>'暂无分组'];
        }
        $grpId = key($arr_grp_id);
        $form = \Sooh\Base\Form\Broker::getCopy('default')
            ->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_c);
        $form->addItem('grpId', form_def::factory('分组', $grpId, form_def::select)->initMore(new options_def($arr_grp_id)))
            ->addItem('addGrp', form_def::factory('或添加新分组', '', form_def::text, [], ['data-rule' => 'length[~10]']))
            ->addItem('batchId', form_def::factory('批次', '', form_def::text, [],['data-rule' => 'required,length[~4]']))
            ->addItem('bonusini', form_def::factory('奖励{"奖项":数量}', '', form_def::text, [], ['data-rule' => 'required']))
            ->addItem('dtExpire', form_def::factory('过期日期', '', 'timepicker'))
            ->addItem('batchNum', form_def::factory('兑换码数量', '', form_def::text, [],  [], ['data-rule' => 'Broker;digits']))
            ->addItem('intro', form_def::factory('说明', '', form_def::text))
            ->addItem('exp', form_def::factory('红包类', '', form_def::constval));


        $form->fillValues();
        if($form->flgIsThisForm){
            
                $fields = $form->getFields();
                
//                 $bonusini=$fields['bonusini'];
//                 var_log($bonusini,'111111111111111111111111');
                
                function RedPacket($userInput,$redPackFor2){
                     
                    if(preg_match_all('/^{\"RedPacketForExcode":\d+}$/',$userInput)==1){
                          $redPackFor1='活动红包'; 
                          $redPackFor=$userInput;
                        //  $redPackFor=$redPackFor1;
                          return $redPackFor;
                         
                
                    }elseif(preg_match_all('/^{\"RedPacket":\d+}$/',$userInput)==1){
                         $redPackFor1='普通红包';
                         $redPackFor=$userInput;
                        // $redPackFor=$redPackFor1;
                         return $redPackFor;
                       
                    }elseif (preg_match_all('/^{\"RedPacketForAward":\d+}$/',$userInput)==1){
                         $redPackFor1='手动发放奖励红包';
                           $redPackFor=$userInput;
                        // $redPackFor=$redPackFor1;
                          return $redPackFor;
                    }elseif (preg_match_all('/^{\"RedPacketForCheckin":\d+}$/',$userInput)==1){
                         $redPackFor1='签到红包';                  
                         $redPackFor=$userInput;
                        // $redPackFor=$redPackFor1;
                          return $redPackFor;
                    }elseif (preg_match_all('/^{\"RedPacketForFirstBind":\d+}$/',$userInput)==1){
                           $redPackFor1='首次绑卡的红包';
                          $redPackFor=$userInput;
                         //$redPackFor=$redPackFor1;
                          return $redPackFor;
                    }elseif (preg_match_all('/^{\"RedPacketForFirstBuy":\d+}$/',$userInput)==1){
                         $redPackFor1='首次购买的红包';
                        $redPackFor=$userInput;
                       //  $redPackFor=$redPackFor1;
                          return $redPackFor;
                    }elseif (preg_match_all('/^{\"RedPacketForFirstBuyForInvite":\d+}$/',$userInput)==1){
                         $redPackFor1='邀请红包';
                         $redPackFor=$userInput;
                        // $redPackFor=$redPackFor1;
                          return $redPackFor;;
                    }elseif (preg_match_all('/^{\"RedPacketForFirstcharge":\d+}$/',$userInput)==1){
                         $redPackFor1='首充红包';
                         $redPackFor=$userInput;
                        // $redPackFor=$redPackFor1;
                          return $redPackFor;;
                    }elseif (preg_match_all('/^{\"RedPacketForOrder":\d+}$/',$userInput)==1){
                         $redPackFor1='首购红包';
                         $redPackFor=$userInput;
                        // $redPackFor=$redPackFor1;
                          return $redPackFor;
                    }elseif (preg_match_all('/^{\"RedPacketForRebate":\d+}$/',$userInput)==1){
                         $redPackFor1='返利红包';
                       $redPackFor=$userInput;
                        // $redPackFor=$redPackFor1;
                          return $redPackFor;;
                    }elseif (preg_match_all('/^{\"RedPacketForReBuy":\d+}$/',$userInput)==1){
                         $redPackFor1='购买（非首购）红包';
                     $redPackFor=$userInput;
                        // $redPackFor=$redPackFor1;
                          return $redPackFor;
                    }elseif (preg_match_all('/^{\"RedPacketForRecharge":\d+}$/',$userInput)==1){
                         $redPackFor1='充值（非首冲）奖励红包';
                         $redPackFor=$userInput;
                        // $redPackFor=$redPackFor1;
                          return $redPackFor;;
                    }elseif (preg_match_all('/^{\"RedPacketForRegister":\d+}$/',$userInput)==1){
                         $redPackFor1='注册红包';
                         $redPackFor=$userInput;
                        // $redPackFor=$redPackFor1;
                          return $redPackFor;
                    }elseif (preg_match_all('/^{\"RedPacketForShare":\d+}$/',$userInput)==1){
                         $redPackFor1='分享红包';
                         $redPackFor=$userInput;
                       //  $redPackFor=$redPackFor1;
                          return $redPackFor;
                   }elseif (preg_match_all('/^{\"RedPacketForWeekactive":\d+}$/',$userInput)==1){
                         $redPackFor1='赏金红包';
                         $redPackFor=$userInput;
                        // $redPackFor=$redPackFor1;
                          return $redPackFor;;
                    }elseif (preg_match_all('/^{\"NewFirstBuyRedPacket":\d+}$/',$userInput)==1){
                         $redPackFor1='首购红包';
                          $redPackFor=$userInput;
                       //  $redPackFor=$redPackFor1;
                          return $redPackFor;
                    }elseif (preg_match_all('/^{\"NewFirstBuyForInviteRedPacket":\d+}$/',$userInput)==1){
                         $redPackFor1='邀请红包';
                         $redPackFor=$userInput;
                       //  $redPackFor=$redPackFor1;
                          return $redPackFor;
                    }elseif (preg_match_all('/^{\"NewRegisterRedPacket":\d+}$/',$userInput)==1){
                          $redPackFor1='注册红包';
                         $redPackFor=$userInput;
                        // $redPackFor=$redPackFor1;
                          return $redPackFor;
                    }elseif (preg_match_all('/^{\"NewFirstLoginAppRedPacket":\d+}$/',$userInput)==1){
                         $redPackFor1='首次登录红包';
                         $redPackFor=$userInput;
                        // $redPackFor=$redPackFor1;
                          return $redPackFor;
                    }elseif (preg_match_all('/^{\"NewFirstBindRedPacket":\d+}$/',$userInput)==1){
                         $redPackFor1='认证红包';
                         $redPackFor=$userInput;
                       //  $redPackFor=$redPackFor1;
                          return $redPackFor;
                    }elseif (preg_match_all('/^{\"NewFirstChargeRedPacket":\d+}$/',$userInput)==1){
                        $redPackFor1='首充红包';
                        $redPackFor=$userInput;
                       //  $redPackFor=$redPackFor1;
                          return $redPackFor;
                    }else{
                       return -1;
                }
            }
               
            
            function RedPacketFor($userInput,$redPackFor2){
                 
                if(preg_match_all('/^{\"RedPacketForExcode":\d+}$/',$userInput)==1){
                    $redPackFor1='活动红包';
                    $redPackFor=$userInput;
                     $redPackFor=$redPackFor1;
                    return $redPackFor;
                     
            
                }elseif(preg_match_all('/^{\"RedPacket":\d+}$/',$userInput)==1){
                    $redPackFor1='普通红包';
                    $redPackFor=$userInput;
                     $redPackFor=$redPackFor1;
                    return $redPackFor;
                     
                }elseif (preg_match_all('/^{\"RedPacketForAward":\d+}$/',$userInput)==1){
                    $redPackFor1='手动发放奖励红包';
                    $redPackFor=$userInput;
                    $redPackFor=$redPackFor1;
                    return $redPackFor;
                }elseif (preg_match_all('/^{\"RedPacketForCheckin":\d+}$/',$userInput)==1){
                    $redPackFor1='签到红包';
                    $redPackFor=$userInput;
                    $redPackFor=$redPackFor1;
                    return $redPackFor;
                }elseif (preg_match_all('/^{\"RedPacketForFirstBind":\d+}$/',$userInput)==1){
                    $redPackFor1='首次绑卡的红包';
                    $redPackFor=$userInput;
                    $redPackFor=$redPackFor1;
                    return $redPackFor;
                }elseif (preg_match_all('/^{\"RedPacketForFirstBuy":\d+}$/',$userInput)==1){
                    $redPackFor1='首次购买的红包';
                    $redPackFor=$userInput;
                    $redPackFor=$redPackFor1;
                    return $redPackFor;
                }elseif (preg_match_all('/^{\"RedPacketForFirstBuyForInvite":\d+}$/',$userInput)==1){
                    $redPackFor1='邀请红包';
                    $redPackFor=$userInput;
                    $redPackFor=$redPackFor1;
                    return $redPackFor;;
                }elseif (preg_match_all('/^{\"RedPacketForFirstcharge":\d+}$/',$userInput)==1){
                    $redPackFor1='首充红包';
                    $redPackFor=$userInput;
                    $redPackFor=$redPackFor1;
                    return $redPackFor;;
                }elseif (preg_match_all('/^{\"RedPacketForOrder":\d+}$/',$userInput)==1){
                    $redPackFor1='首购红包';
                    $redPackFor=$userInput;
                    $redPackFor=$redPackFor1;
                    return $redPackFor;
                }elseif (preg_match_all('/^{\"RedPacketForRebate":\d+}$/',$userInput)==1){
                    $redPackFor1='返利红包';
                    $redPackFor=$userInput;
                    $redPackFor=$redPackFor1;
                    return $redPackFor;;
                }elseif (preg_match_all('/^{\"RedPacketForReBuy":\d+}$/',$userInput)==1){
                    $redPackFor1='购买（非首购）红包';
                    $redPackFor=$userInput;
                    $redPackFor=$redPackFor1;
                    return $redPackFor;
                }elseif (preg_match_all('/^{\"RedPacketForRecharge":\d+}$/',$userInput)==1){
                    $redPackFor1='充值（非首冲）奖励红包';
                    $redPackFor=$userInput;
                    $redPackFor=$redPackFor1;
                    return $redPackFor;;
                }elseif (preg_match_all('/^{\"RedPacketForRegister":\d+}$/',$userInput)==1){
                    $redPackFor1='注册红包';
                    $redPackFor=$userInput;
                    $redPackFor=$redPackFor1;
                    return $redPackFor;
                }elseif (preg_match_all('/^{\"RedPacketForShare":\d+}$/',$userInput)==1){
                    $redPackFor1='分享红包';
                    $redPackFor=$userInput;
                    $redPackFor=$redPackFor1;
                    return $redPackFor;
                }elseif (preg_match_all('/^{\"RedPacketForWeekactive":\d+}$/',$userInput)==1){
                    $redPackFor1='赏金红包';
                    $redPackFor=$userInput;
                    $redPackFor=$redPackFor1;
                    return $redPackFor;;
                }elseif (preg_match_all('/^{\"NewFirstBuyRedPacket":\d+}$/',$userInput)==1){
                    $redPackFor1='首购红包';
                    $redPackFor=$userInput;
                    $redPackFor=$redPackFor1;
                    return $redPackFor;
                }elseif (preg_match_all('/^{\"NewFirstBuyForInviteRedPacket":\d+}$/',$userInput)==1){
                    $redPackFor1='邀请红包';
                    $redPackFor=$userInput;
                    $redPackFor=$redPackFor1;
                    return $redPackFor;
                }elseif (preg_match_all('/^{\"NewRegisterRedPacket":\d+}$/',$userInput)==1){
                    $redPackFor1='注册红包';
                    $redPackFor=$userInput;
                    $redPackFor=$redPackFor1;
                    return $redPackFor;
                }elseif (preg_match_all('/^{\"NewFirstLoginAppRedPacket":\d+}$/',$userInput)==1){
                    $redPackFor1='首次登录红包';
                    $redPackFor=$userInput;
                    $redPackFor=$redPackFor1;
                    return $redPackFor;
                }elseif (preg_match_all('/^{\"NewFirstBindRedPacket":\d+}$/',$userInput)==1){
                    $redPackFor1='认证红包';
                    $redPackFor=$userInput;
                    $redPackFor=$redPackFor1;
                    return $redPackFor;
                }elseif (preg_match_all('/^{\"NewFirstChargeRedPacket":\d+}$/',$userInput)==1){
                    $redPackFor1='首充红包';
                    $redPackFor=$userInput;
                    $redPackFor=$redPackFor1;
                    return $redPackFor;
                }else{
                    return -1;
                }
            }
 
                 $bonusin=$fields['bonusini'];
                 $fields['exp']=RedPacketFor($bonusin);
               
                if(!empty($fields['addGrp'])){
                    $fields['grpId'] = $fields['addGrp'];
                }
                unset($fields['addGrp']);
                $fields['dtExpire'] = strtotime($fields['dtExpire']);
                // TODO:应该再加个奖励格式的检查
               // $match_n = preg_match_all("/^{\"\w+\":\d+}$/", $fields['bonusini']);
               // $match_n = preg_match_all($userInput, $fields['bonusini']);
               
              
                
                $bonusini=$fields['bonusini'];
               // var_log($bonusini,'>>>>>>>>>>>>>>>>>>>');
                $bonusin=$fields['bonusini'];
                
                $fields['bonusini']=RedPacket($bonusini);
                
               
               $match_n = preg_match_all("/^{\"\w+\":\d+}$/", $fields['bonusini']);
              //  $match_n = preg_match_all('活动红包', $fields['bonusini']);
               // var_log($match_n,'1111111111111111111');
                if($match_n !== 1){
                    //return $this->returnError('奖励格式不对');
                    return $this->returnError(\Prj\Lang\Broker::getMsg('excode.reward_format_wrong'));
                }

                $obj = \Prj\Data\ExchangeCode::getCopy();
                $db= $obj->db();
                $dtAddBatch = \Sooh\Base\Time::getInstance()->timestamp();
                $fields['dtAddBatch'] = $dtAddBatch;
                $dtAddGrp = $db->getOne(\Prj\Data\ExchangeCode::tbGrp, 'dtAddGrp');

                if(empty($dtAddGrp)) {
                    $fields['dtAddGrp'] = $dtAddBatch;
                }else {
                    $fields['dtAddGrp'] = $dtAddGrp;
                }
            try{
                \Sooh\DB\Broker::errorMarkSkip();
                $db->addRecord(\Prj\Data\ExchangeCode::tbGrp, $fields);

            }catch(\ErrorException $e){
                if (\Sooh\DB\Broker::errorIs($e)){
                  //  return $this->returnError('此分组的批次用已经存在');
                    return $this->returnError(\Prj\Lang\Broker::getMsg('excode.group_batch_error'));
                }else {
                    error_log($e->getMessage()."\n".$e->getTraceAsString());
                    return $this->returnError('添加失败：未知错误，请联系系统管理员');
                }
            }
            $candidate  = '2346789abcdefghjkmnpqrtuvwxyz';
            $n = 0;

            while($n < $fields['batchNum']) {
                $code = '';
                for ($i = 1; $i <= 6; $i++) {
                    //从字符库中随机一个位置[数字]
                    $indexStr = rand(0, strlen($candidate) - 1);
                    $code .= $candidate[$indexStr];
                }
                $code = strtoupper($code);
                $excode= \Prj\Data\ExchangeCode::getCopy($code);
                $db = $excode->db();
                $tbname = $excode->tbname();
                
                try{
                    \Sooh\DB\Broker::errorMarkSkip();
                    $r = [
                        'excode' => $code,
                        'grpId' => $fields['grpId'],
                        'batchId' => $fields['batchId'],
                        'dtExPire' => $fields['dtExpire'],
                    ];
                    $db->addRecord($tbname, $r);
                    $n++;
                  
                }catch(\ErrorException $e) {

                }
              
                $this->closeAndReloadPage($this->tabname('index'));
            }

            if($n == $fields['batchNum']) {
                
                return $this->returnOK('添加成功');
            }else {
                return $this->returnOK('成功添加'.$n.'个兑换码');
            }
         

        }

    }


    // 作废
    public function revokeAction () {
        $pkey = $this->_request->get('_pkey');
        if(empty($pkey)) {
            //return $this->returnError('参数错误');
            return $this->returnError(\Prj\Lang\Broker::getMsg('excode.prem_error'));
        }

        $pkey = \Prj\Misc\View::decodePkey($pkey);
        $obj = \Prj\Data\ExchangeCode::getCopy();
        $db = $obj->db();
        $dtExpire = $db->getOne(\Prj\Data\ExchangeCode::tbGrp, 'dtExpire', $pkey);
        $dtNow = \Sooh\Base\Time::getInstance()->timestamp();
        if(empty($dtExpire)) {
            //return $this->returnError('未找到此行记录');
            return $this->returnError(\Prj\Lang\Broker::getMsg('excode.record_unfound'));
        }elseif($dtExpire < $dtNow){
            //$this->closeAndReloadPage($this->tabname('index'));
            //return $this->returnError('此批兑换码已经被作废了111');
            return $this->returnOk(\Prj\Lang\Broker::getMsg('excode.excode_missing_expired'));
        }else {
            $dtExpire = \Sooh\Base\Time::getInstance()->timestamp(-1);
            $n = $db->updRecords(\Prj\Data\ExchangeCode::tbGrp, ['dtExpire'=>$dtExpire], $pkey);
            if($n) {
                $this->returnOK('操作成功');
                //$this->closeAndReloadPage($this->tabname('index'));
            }else{
                return $this->returnError('操作失败');
            }
        }


    }


    public function getbatchidsAction (){
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $grpId = $this->_request->get('grpId');
        if(empty($grpId)) {
            return [];
        }
        $r = [];
        $tmp = $this->grpIdWithBatchId[$grpId];
        if(!empty($tmp)) {
            $r = ['all'=>'所有批次'];
            $tmp = array_combine($tmp, $tmp);
            $r += $tmp;
        }
        $this->_view->assign($r);
    }
}