<?php
use Sooh\Base\Form\Item as form_def;

/**
 * 标的
 * By Hand
 */
class WaresController extends \Prj\ManagerCtrl
{
    protected $editor = null;

    protected $fieldsMap = array(
        'waresId' => array('标的ID', 'auto'),
        'waresName' => array('标的名称', '100'),
        'tags' => array('标签', '60'),
        'vipLevel' => array('vip等级', '60'),
        'interestStartType' => array('起息方式', '70'),
        'returnType' => array('还款方式', 'auto'),
        'deadLine' => array('期限', '35'),
        'dlUnit' => array('单位', 'auto'),
        'yieldStatic' => array('利率(%)', '52'),
        'yieldStaticAdd' => array('活动加息', '57'),
        'shelfId' => array('类型', '65'),
        'amount' => array('总额(元)', 'auto'),
        'remain' => array('余额(元)', 'auto'),
        'timeStartPlan' => array('计划上架时间', '160'),
        'timeEndReal' => array('实际满标时间', 'auto'),
        'payYmd' => array('满标转账时间', 'auto'),
        'ymdPayPlan' => array('计划还款时间', 'auto'),
        'statusCode' => array('状态', '70'),
        'payStatus' => array('网关状态', '60'),
        'paySn' => array('网关订单号', 'auto'),
        'exp' => array('备注', 'auto'),
    );
    protected $testFields = array(
        'waresName' => '某知名面粉企业升级改造设备直租项目',
        'waresSN' => 0,
        'deadLine' => 12,
        'dlUnit' => '月',
        'tags' => 1,
        'mainType' => 1,
        'subType' => 1,
        'priceStart' => 10000,
        'priceStep' => 1,
        'amount' => 100000000,
        'remain' => 100000000,
        'yieldStatic' => 0.12,
        'yieldStaticAdd' => 0,
        'yieldFloatFrom' => 0.01,
        'yieldFloatTo' => 0.05,
        'yieldDesc' => '放心不会变更',
        'shelfId' => 2000,
        'timeStartPlan' => '20151019153200',
        'timeEndPlan' => '20151019153200',
        'ymdPayPlan' => '1476806400',
        'interestStartType' => 1,
        'returnType' => 2,
        'introDisplay' => array(
            'a' => '1',
            'b' => '2',
            'instruction' => '3',
            'd' => '4',
        )
    );

    /**
     * 获取图片
     * By Hand
     */
    public function getImageHtmlAction()
    {
        $this->ini->viewRenderType('echo');
        $fileId = $this->_request->get('fileId');
        echo "<div class='bjui-pageContent'>";
        echo "<img src='".\Sooh\Base\Tools::uri(['fileId'=>$fileId],'getImage','public','index')."' />";
        echo "</div>";
    }



    /**
     * 标的编辑
     */
    public function editAction()
    {
        $this->closeAndReloadPage($this->tabname('index'));
        // error_reporting(E_ALL);
        $editorTpl = $this->_request->get('viewTPL', 'Std02');  //选择内容展示模板
        $assetId   = $this->_request->get('assetId'); //选择资产ID

        if (!empty($assetId)) {
            $asset = \Prj\Data\Asset::getCopy($assetId);
            $asset->load();
            if (!$asset->exists()) {
                return $this->returnError(\Prj\Lang\Broker::getMsg('asset.asset_unexists'));
            } else {
                $borrowerId = $asset->getField('borrowerId');
                $editorTpl  = $asset->getField('viewTPL');
            }
        }
        switch ($editorTpl) {
            case 'Std02':
                $shelfId = \Prj\Consts\Wares::shelf_static;
                break;
            case 'Std03':
                $shelfId = \Prj\Consts\Wares::shelf_static_float;
                break;
            default:
                $shelfId = \Prj\Consts\Wares::shelf_static;;
        }
        $Editor       = "\\Prj\\WaresTpl\\" . ($editorTpl ? $editorTpl : 'Std02') . '\\Editor';
        $this->editor = $Editor;
        // \Prj\Misc\FormRenderer::$editor = $Editor::getCopy();
        $where = \Prj\Misc\View::decodePkey($this->_request->get('_pkey_val_'));
        $type  = $this->_request->get('_type');
        $frm   = \Sooh\Base\Form\Broker::getCopy('default')
            ->init(\Sooh\Base\Tools::uri(), 'get', empty($where) ? \Sooh\Base\Form\Broker::type_c : \Sooh\Base\Form\Broker::type_u);

        $frm->addItem('viewTPL', form_def::factory('内容模版', $editorTpl ? $editorTpl : 'Std02', form_def::select, \Prj\Consts\Wares::$viewTPL))
            ->addItem('assetId', form_def::factory('资产名称', '', form_def::select, $this->getAssetSelect($editorTpl ? $editorTpl : 'Std02')))
            ->addItem('waresName', form_def::factory('标的名称', '', form_def::text, [], ['data-rule' => 'required,length[~15]']))
            ->addItem('deadLine', form_def::factory('期限', '', form_def::text, [], ['data-rule' => 'required;digits']))
            ->addItem('dlUnit', form_def::factory('期限单位', '月', form_def::select, ['天' => '天', '月' => '月']))
            ->addItem('waresSN', form_def::factory('期数(产品第几期)', '1', form_def::text, [], ['data-rule' => 'required']))
            ->addItem('tags', form_def::factory('标签', '', form_def::chkbox, ['活动' => '活动', '新手' => '新手']))
            ->addItem('vipLevel', form_def::factory('vip等级', '0', form_def::select)->initMore(new \Sooh\Base\Form\Options(array('0' => '不限', '1' => 'VIP1'), '全部')))
            //->addItem('mainType', form_def::factory('大类', '0', form_def::select, ['0' => 'test1', '1' => 'test2']))
            //->addItem('subType', form_def::factory('小类', '0', form_def::select, ['0' => 'test1-test1', '1' => 'test1-test2']))
            ->addItem('amount', form_def::factory('总额(元)', '', form_def::text, [], ['data-rule' => 'required;digits']))
            ->addItem('priceStart', form_def::factory('起投金额(元)', '100', form_def::text, [], ['data-rule' => 'required;digits']))
            ->addItem('priceStep', form_def::factory('递增金额(元)', '0.01', form_def::text, [], ['data-rule' => 'required']))
            ->addItem('yieldStatic', form_def::factory('固定年化收益率', '0.01', form_def::text, [], ['data-rule' => 'required']))
            ->addItem('yieldStaticAdd', form_def::factory('活动加息', '0', form_def::text))
            //->addItem('yieldFloatFrom', form_def::factory('浮动年化收益率下限', '', form_def::text))
            //->addItem('yieldFloatTo', form_def::factory('浮动年化收益率上限', '', form_def::text))
            //->addItem('yieldDesc', form_def::factory('年化率变更详细说明', '', form_def::text))
            ->addItem('shelfId', form_def::factory('产品类型', $shelfId, form_def::select, \Prj\Consts\Wares::$shilfIdName))
            ->addItem('timeStartPlan', form_def::factory('计划上架时间', date('YmdHis'), 'timepicker', [], ['data-rule' => 'required']))
            //->addItem('timeEndPlan', form_def::factory('关闭募集结束时间', '', 'timepicker', [], ['rule' => 'required']))
            ->addItem('ymdPayPlan', form_def::factory('预计还款日期', '', form_def::datepicker, [], ['data-rule' => 'required']))
            ->addItem('interestStartType', form_def::factory('起息方式', \Prj\Consts\InterestStart::whenFull, form_def::select, \Prj\Consts\InterestStart::$enum))
            ->addItem('returnType', form_def::factory('还款方式', \Prj\Consts\ReturnType::byMonth, form_def::select, \Prj\Consts\ReturnType::$enum))
            ->addItem('borrowerId', form_def::factory('借款人ID', $borrowerId, form_def::text, [], ['data-rule' => 'required,number']))
            ->addItem('managementTrans', form_def::factory('服务费(元)', 0, form_def::text, [], ['data-rule' => 'required,number']))
            ->addItem('managementConfirm', form_def::factory('手续费(元)', 0, form_def::text, [], ['data-rule' => 'required,number']))
            ->addItem('autoConfirm', form_def::factory('(借款人)自动还款开关', 0, form_def::select, ['0' => '关', '1' => '开'], []))
            ->addItem('autoReturnFund', form_def::factory('自动回款(给投资人)开关', 0, form_def::select, ['0' => '关', '1' => '开'], []))
            //->addItem('introDisplay', '')
            //->addItem('sortval', form_def::factory('状态位值',0,form_def::text, [],['data-rule' => 'required,number']))
            ->addItem('_type', $type)
            ->addItem('_pkey_val_', '');
        $moneyArr = array('amount', 'priceStart', 'priceStep', 'remain', 'managementTrans', 'managementConfirm');
        //*** 构造表单数据
        /*
        $arr = $this->createWares();
        unset($arr['introDisplay']);
        $keyArr = array_keys($frm->items);
        //var_log($frm->items);

        foreach ($arr as $k => $v) {
            if (!in_array($k, $keyArr)) continue;
            $frm->items[$k]->value = $v;
            //if(array_values($moneyArr['priceStep'])>0)continue;
           // var_log($moneyArr,'#r###########################');
            if (in_array($k, $moneyArr)) $frm->items[$k]->value /= 100;
        }
        */
        //$frm->items['tags[]']->value = $arr['tags'];

        $frm->fillValues();
        $op = '添加标的';
        if ($frm->flgIsThisForm) //submit
        {
            $where = \Prj\Misc\View::decodePkey($_REQUEST['_pkey_val_']);
            if (!empty($where)) {
                $frm->switchType(\Sooh\Base\Form\Broker::type_u);
            } else {
                $frm->switchType(\Sooh\Base\Form\Broker::type_c);
            }
            try {
                $fields = $frm->getFields();

                $Editor = "\\Prj\\WaresTpl\\" . $fields['viewTPL'] . '\\Editor';
                $tags1  = $fields['tags1'];
                //todo 拿到tags1以后 做相关处理
                //...

                unset($fields['tags1']);
                if ($tags = $this->_request->get('tags')) $tags = array_filter($tags);
                if (!empty($tags)) $fields['tags'] = implode(',', $tags);
                // var_log($fields['tags'],'################');

                if($where['waresId']){
                    $ware = \Prj\Data\Wares::getCopy($where['waresId']);
                    $ware->load();
                }else{
                    $ware = null;
                }

                try {
                    //字段过滤
                    if (empty($type)) {
                        $fields = $this->_checkFields($fields , $ware);
                        try {
                            \Prj\Data\Wares::check($fields);
                        } catch (\ErrorException $e) {
                            switch ($e->getMessage()) {
                                case 'error_dlUnit':
                                    $errorMsg = "按月付息期限请以'月'为单位！";
                                    break;
                                case 'error_date':
                                    $errorMsg = "还款日期请勿小于上架日期！";
                                    break;
                                case 'error_days':
                                    $errorMsg = '还款日期不符合期限的设定！';
                                    break;
                                case 'error_priceStep':
                                    $errorMsg = '请输入0.01的整数倍！';
                                    break;
                                default:
                                    $errorMsg = $e->getMessage();
                            }
                            return $this->returnError($errorMsg);
                        }
                    }
                } catch (\ErrorException $e) {
                    return $this->returnError($e->getMessage());
                }

                if (!empty($checkResult)) return $this->returnError($checkResult);

                if ($frm->type() == \Sooh\Base\Form\Broker::type_c) //add
                {
                    $op                   = "新增";
                    $fields['statusCode'] = \Prj\Consts\Wares::status_new;
                    $fields['remain']     = $fields['amount'];
                    //var_log($fields['priceStep'],'添加标的的是：#########################');
                    $phone = $fields['phone'];
                    //var_log($phone,'phone>>>>>>>>>');

                    $borrowerId = $fields['borrowerId'];
                    //var_log($borrowerId,'borrowerId >>>>>>>>>>>>');
                    unset($fields['phone']);

                    $fields['borrowerId'] = $borrowerId;
                    /*
                    $tags=$fields['tags'];
                    $sortval=$fields['sortval'];
                    if(!empty($tags)){
                        if($tags='活动'){
                            $ware->setField($sortval,1000);
                        }elseif($tags='新手'){
                            $ware->setField($sortval,100);
                        }elseif ($tags='活动,新手'){
                            $ware->setField($sortval,1100);
                        }
                    }
                    unset($fields['sortval']);
                     */


                    $ware = \Prj\Data\Wares::getCopy($this->_creatID());
                    $ware->load();


                    $ext = $this->_request->get('ext');
                    //处理扩展数据
                    $introDisplay = $Editor::getDataFromForm($ext);
                    if (empty($introDisplay['d'])) {
                        return $this->returnError('安全审核不能为空!');
                    } else {
                        $extError = true;
                        foreach ($introDisplay['d'] as $v) {
                            if (!empty($v['img'])) {
                                $extError = false;
                            }
                        }
                        if ($extError) {
                            return $this->returnError('安全审核不能为空!');
                        }
                    }
                    foreach ($fields as $k => $v) {

                        $ware->setField($k, $v);
                    }
                    $ware->setField('introDisplay', json_encode($introDisplay));

                    //更新资产余额
                    if (!empty($fields['assetId'])) {
                        $this->setAssetRemain($fields['assetId'], -$fields['amount']);
                    }
                    //$ware->setField('sLockData', null);
                    //return $this->returnError('强行禁止提交！');
                    //\Prj\Wares\Wares::setSortVal($ware);
                    $ware->setField('statusCode1',-1);
                    $ware->update();
                    return $this->returnOK('添加成功，请在标的内容审核进行审核操作');
                } else { // update
                    $op   = '更新';
                    $statusCode = $ware->getField('statusCode');
                    var_log($statusCode,'>>>>>>>>>>>>>>>>>>>>>>>');
                    //审核通过
                    if ($type == 'check') {
                        if ($statusCode != \Prj\Consts\Wares::status_new) return $this->returnError('标的状态不正确');
                        //todo 审核员
                        $this->manager->load();
                        $nickname = $this->manager->getField('nickname');
                        $tpl      = $Editor::getCopy($ware->getField('introDisplay'));
                        try {
                            $ware->setField('introDisplay', $tpl->setAssessor($nickname)->outPut());
                            $ware->update();
                        } catch (\ErrorException $e) {
                            return $this->returnError('标的更新失败');
                        }
                        
                        $this->_updateStatus(\Prj\Consts\Wares::status_csale);
                        $this->closeAndReloadPage($this->tabname('check'));
                        return $this->returnOK('审核通过，请进行标的上架审核操作');
                        var_log($statusCode,'status>>>>>>>>>>>>');
                        //return;
                    }
                     elseif ($type == 'checkSale') { //上架审核

                        if ($statusCode != \Prj\Consts\Wares::status_csale) return $this->returnError('标的状态不正确');
                    
                        $this->_updateStatus(\Prj\Consts\Wares::status_ready);
                        $this->closeAndReloadPage($this->tabname('checkSale'));
                        return $this->returnOK('审核通过');
                        var_log($statusCode,'status>>>>>>>>>>>>');
                      //  return;
                    }

                    //状态下修改权限
                    $wareDump = $ware->dump();
                    if ($wareDump['statusCode'] != \Prj\Consts\Wares::status_abandon && $wareDump['statusCode'] != \Prj\Consts\Wares::status_new) {
                        //$this->returnError("只有未上架的标的才能修改");
                        $this->returnError(\Prj\Lang\Broker::getMsg('wares.wares_not_modify'));
                        return;
                    }

                    //$phone = $fields['phone'];
                    // var_log($phone,'phone>>>>>>>>>');
                    //var_log($borrowerId,'borrowerId >>>>>>>>>>>>');
                    unset($fields['phone']);


                    //==
                    foreach ($fields as $k => $v) {
                        $ware->setField($k, $v);
                    }
                    $ext = $this->_request->get('ext');
                    //处理扩展数据
                    $introDisplay = $Editor::getDataFromForm($ext);
                    $ware->setField('introDisplay', json_encode($introDisplay));
                    $ware->setField('statusCode', \Prj\Consts\Wares::status_new);
                    //更新资产余额
                    if ($ware->getField('assetId') != 0) {
                        $amountAdd = $fields['amount'] - $wareDump['amount'];
                        //var_log($amountAdd,'amountAdd>>>');
                        $this->setAssetRemain($ware->getField('assetId'), -$amountAdd);
                    }
                    \Prj\Wares\Wares::setSortVal($ware);
                    $ware->update();
                    //var_log(\Sooh\DB\Broker::lastCmd(false),'SQL>>>>>>>>>>>>>>');
                }
            } catch (\ErrorException $e) {
                //var_log(\Sooh\DB\Broker::lastCmd(false),'SQL>>>>>>>>>>>>>>');
                // var_log($e->getMessage(),'>>>>>>>>>>>>');
                return $this->returnError($op . '失败：冲突，相关记录已经存在？');
            }

            $this->closeAndReloadPage($this->tabname('index'));
            $this->returnOK($op . '成功');
            return;
        }

        if ($frm->type() == \Sooh\Base\Form\Broker::type_u) //update show
        {

            $op   = '修改';
            $ware = \Prj\Data\Wares::getCopy($where['waresId']);
            $ware->load();
            $fields = $ware->dump();
            /*
                 $tags=$fields['tags'];
                    $sortval=$fields['sortval'];
                    if(!empty($tags)){
                        if($tags='活动'){
                            $ware->setField($sortval,1000);
                        }elseif($tags='新手'){
                            $ware->setField($sortval,100);
                        }elseif ($tags='活动,新手'){
                            $ware->setField($sortval,1100);
                        }
                    }
                    unset($fields['sortval']);

            */
            //var_log($fields,'fields>>>>>>>>>>>>>>>.');
            if (!empty($fields['assetId'])) {
                $asset = \Prj\Data\Asset::getCopy($fields['assetId']);
                $asset->load();
                if (!$asset->exists()) {
                    // return $this->returnError('不存在的资产ID');
                    return $this->returnError(\Prj\Lang\Broker::getMsg('asset.asset_unexists'));
                } else {
                    if ($asset->getField('status') == 0) {
                        return $this->returnError('资产[' . $asset->getField('assetName') . ']未通过审核');
                    }
                }
            }

            if (empty($editorTpl)) {
                $Editor = "\\Prj\\WaresTpl\\" . $fields['viewTPL'] . '\\Editor';
            }

            foreach ($moneyArr as $v) {
                $fields[$v] /= 100;
            }
            //var_log($fields);
            $ks = array_keys($frm->items);
            /*
            $user = \Prj\Data\User::getCopy($fields['borrowerId']);
            $user->load();
            $fields['phone'] = $user->exists()?$user->getField('phone'):'';
            */
            foreach ($ks as $k) {
                if (isset($fields[$k]) && is_object($frm->items[$k])) {
                    $frm->items[$k]->value = $fields[$k];
                }
            }
            //$frm->items['tags[]']->value = $fields['tags'];
            $frm->items['_pkey_val_'] = \Prj\Misc\View::encodePkey(array('waresId' => $fields['waresId']));
            $Editor::$content         = $fields['introDisplay'];
            $this->_view->assign('_pkey_val_', $this->_request->get('_pkey_val_'));
            $this->_view->assign('editor', $Editor::getCopy());
        } else {
            $txtPath = __DIR__ . '/../views/_inc/intro.www.phtml';
            $intro   = [
                'a' => '项目真是好项目啊',
                'b' => [
                    'name' => '张先生',
                    'married' => '已婚',
                    'rootAddr' => '上海市浦东新区',
                    'addr' => '上海市浦东新区',
                    'reason' => '做生意',
                ],
                'c' => [
                    'addr' => '上海市浦东新区',
                    'ymd' => '20140101',
                    'price' => '20000000',
                ],
                'd' => [
                    'id' => [
                        'name' => '',
                        'img' => [],
                    ],
                    'book' => [
                        'name' => '',
                        'img' => [],
                    ],
                    'married' => [
                        'name' => '',
                        'img' => [],
                    ],
                    'credit' => [
                        'name' => '',
                        'img' => [],
                    ],
                    'inspect' => [
                        'name' => '',
                        'img' => [],
                    ],
                    'contract' => [
                        'name' => '',
                        'img' => [],
                    ],
                    'receipt' => [
                        'name' => '',
                        'img' => [],
                    ],
                    'property' => [
                        'name' => '',
                        'img' => [],
                    ]
                ]
            ];
            if (!empty($asset)) {
                $intro = $asset->getField('introDisplay');
            }
            $Editor::$content = $intro;
            $this->_view->assign('editor', $Editor::getCopy());
        }

        //var_dump($fields);
        //die();
        $this->_view->assign('FormOp', $op);
        $this->_view->assign('type', $type);

        $this->_view->assign('_pkey_val_', $this->_request->get('_pkey_val_'));

    }

    protected function getAssetSelect($editorTpl = '')
    {
        $where = ['status' => 1];
        if (!empty($editorTpl)) {
            $where['viewTPL'] = $editorTpl;
        }
        $rs = \Prj\Data\Asset::loopFindRecords($where);
        // var_log($rs,'#################');
        if (empty($rs)) {
            $select = [];
        } else {
            $tmp = ['<请选择资产>'];
            foreach ($rs as $v) {
                $tmp[$v['assetId']] = $v['assetName'];
            }
            $select = $tmp;
        }
        return $select + [0 => ''];
    }

    protected function _checkFields($fields , $wares = null)
    {
        $editor     = $this->editor;
        $dateFields = array('timeStartPlan', 'timeEndPlan'); //日期字段过滤

        $moneyArr = array('amount', 'priceStart', 'priceStep', 'managementTrans', 'managementConfirm');
        //if($moneyArr['priceStep']>0)continue;
        $yieldArr  = ['yieldStatic', 'yieldStaticAdd', 'yieldFloatFrom', 'yieldFloatTo'];
        $igNoreArr = ['yieldDesc', 'tags'];
        //表单数据处理以及验证
        $borrowerId = $fields['borrowerId'];
        $borrower   = \Prj\Data\User::getCopy($borrowerId);
        $borrower->load();
        if (!$borrower->exists()) {
            return $this->_error('借款人ID不合法');
        }
        if (!$borrower->getField('ymdBindcard')) {
            return $this->_error('借款人ID不合法');
        }

        if ($fields['viewTPL'] == 'Std02' && $fields['shelfId'] != \Prj\Consts\Wares::shelf_static) {
            $this->_error('模板和产品类型不同步');
        } elseif ($fields['viewTPL'] == 'Std03' && $fields['shelfId'] != \Prj\Consts\Wares::shelf_static_float) {
            $this->_error('模板和产品类型不同步');
        }
        if ($fields['interestStartType'] != \Prj\Consts\InterestStart::whenFull) {
            $this->_error('目前仅支持放款后起息');
        }
        foreach ($fields as $k => $v) {
            if (in_array($k, $igNoreArr)) continue;
            if ($v === '') {
                //var_log($k,'k>>>');
                $this->_error($this->fieldsMap[$k][0] ? $this->fieldsMap[$k][0] : $k . ' 不能为空');
            }
            if (in_array($k, $dateFields)) $fields[$k] = date('YmdHis', strtotime($fields[$k]));//时间格式化
            if (in_array($k, $moneyArr)) $fields[$k] *= 100;//金额转换
        }
        $fields['remain'] = $fields['amount'];
        foreach ($yieldArr as $v) {
            if (!empty($fields[$v]) && $fields[$v] >= 1) $this->_error($this->fieldsMap[$v][0] . ' 必须小于1');
        }
        $fields['tags']       = str_replace('，', ',', $fields['tags']);
        $fields['ymdPayPlan'] = strtotime($fields['ymdPayPlan']);

        //资产验证
        // var_log($fields['assetId'],'assetId>>>');
        if (!empty($fields['assetId'])) {
            $asset = \Prj\Data\Asset::getCopy($fields['assetId']);
            $asset->load();
            if (!$asset->exists()) {
                $this->_error('资产ID不存在');
            } else {
                //计划上架日期
                if (strtotime($fields['timeStartPlan']) < strtotime($asset->getField('startYmd'))) {
                    $this->_error('标的计划上架日期不得早于资产起始日期');
                }
                //计划还款日期
                if ($fields['ymdPayPlan'] > strtotime($asset->getField('endYmd'))) {
                    $this->_error('标的预计还款日期不得晚于资产结束日期');
                }
                //金额
                if($wares){
                    if($fields['amount']-$wares->getField('amount') > $asset->getField('remain')){
                        $this->_error('标的总额不得大于资产剩余金额');
                    }
                }else{
                    if ($fields['amount'] > $asset->getField('remain')) {
                        $this->_error('标的总额不得大于资产剩余金额');
                    }
                }
            }
        }

        return $fields;
    }

    protected function _error($msg)
    {
        throw new \ErrorException($msg);
    }

    //表单过滤

    protected function _creatID()
    {
        return time() . rand(100000, 999999);
    }

    //查看订单

    protected function setAssetRemain($assetId, $amount)
    {
        $asset = \Prj\Data\Asset::getCopy($assetId);
        $asset->load();
        if (!$asset->exists()) {
            $this->_error('资产不存在');
        } else {
            $asset->setField('remain', $asset->getField('remain') + $amount);
            $asset->update();
        }
    }

    //审核页面

    protected function _updateStatus($num)
    {
        $where = \Prj\Misc\View::decodePkey($this->_request->get('_pkey_val_'));
        $ware  = \Prj\Data\Wares::getCopy($where['waresId']);
        $ware->load();
        $fields = $ware->dump();
        try {
            $ware->setField('statusCode', $num);
            $ware->update();
        } catch (\ErrorException $e) {
            return $this->returnError($fields['statusCode'] . '=>' . $num . ':数据库错误 ' . $e->getMessage());
        }
        //  var_log(\Sooh\DB\Broker::lastCmd(false),'SQL>>>>>>>>>');
        return $this->returnOK("操作完成");
    }

    //上线审核

    public function showInvestAction()
    {
        //\Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $where = \Prj\Misc\View::decodePkey($this->_request->get('_pkey_val_'));
        //var_log($where);
        $waresId = $where['waresId'];
        if (empty($waresId)) {
            //  $this->returnError('no_waresId');
            $this->returnError(\Prj\Lang\Broker::getMsg('wares.waresId_error'));
        } else {
            $rs = \Prj\Data\Investment::pagerByWaresId($waresId);
            if (empty($rs)) {
                //  $this->returnError('没有匹配的订单记录');
                $this->returnError(\Prj\Lang\Broker::getMsg('wares.order_unrecord'));
            } else {
                $fieldMap = array(
                    'ordersId' => array('订单号', 20),
                    'userId' => array('用户ID', 20),
                    'amount' => array('金额(元)', 20),
                    'amountExt' => array('红包(元)', 20),
                    'amountFake' => array('券(元)', 20),
                    'extDesc' => array('加成说明', 20),
                    'orderTime' => array('下单时间', 20),
                    'orderStatus' => array('订单状态', 20),
                    'firstTime' => array('是否首次下单', 20),
                );
                foreach ($fieldMap as $k => $v) {
                    $headers[$v[0]] = $v[1];
                    $fields[]       = $k;
                }
                foreach ($rs as $k => $v) {
                    foreach ($fields as $vv) {
                        $new[$k][$vv] = $v[$vv];
                    }
                }
                $rs = $new;
                // var_log($rs,'33333333333333');
                $this->_view->assign('rs', $rs);
                $this->_view->assign('headers', $headers);
            }
        }
    }

    //驳回操作

    public function checkAction()
    {
//         $fieldsMap = array(
//             'waresId' => array('标的ID', 'auto'),
//             'waresName' => array('标的名称', '100'),
//             'tags' => array('标签', '60'),
//             'vipLevel' => array('vip等级', '60'),
//             'interestStartType' => array('起息方式', '70'),
//             'returnType' => array('还款方式', 'auto'),
//             'deadLine' => array('期限', '35'),
//             'dlUnit' => array('单位', 'auto'),
//             'yieldStatic' => array('利率(%)', '52'),
//             'yieldStaticAdd' => array('活动加息', '57'),
//             'shelfId' => array('类型', '65'),
//             'amount' => array('总额(元)', 'auto'),
//             'remain' => array('余额(元)', 'auto'),
//             'timeStartPlan' => array('计划上架时间', '160'),
//             'timeEndReal'     => array('实际满标时间','auto'),
//             'payYmd'     => array('满标转账时间','auto'),
//             'ymdPayPlan' => array('计划还款时间', 'auto'),
//             'statusCode' => array('状态', '70'),
//             'payStatus' => array('网关状态', '60'),
//             'paySn' => array('网关订单号', 'auto'),
//             'exp' => array('备注', 'auto'),
//         );

        $this->indexAction('check');
    }

    //满标转账操作

    public function indexAction($type = '')
    {
        $pageid           = $this->_request->get('pageId', 1) - 0;
        $isDownloadEXCEL  = $this->_request->get('__EXCEL__');
        $pager            = new \Sooh\DB\Pager(10, $this->pageSizeEnum, false);
        $pager->page_size = $this->_request->get('pageSize', 50);
        //search
        $frm = \Sooh\Base\Form\Broker::getCopy('default')
            ->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_s);
        $frm->addItem('_waresname_lk', form_def::factory('标的名称', '', form_def::text))
            // ->addItem('_interestStartType_eq', form_def::factory('起息方式', '', form_def::select,(\Prj\Consts\InterestStart::$enum+[''=>'全部'])))
            ->addItem('_returnType_eq', form_def::factory('还款方式', '', form_def::select, (\Prj\Consts\ReturnType::$enum + ['' => '全部'])))
            ->addItem('_shelfId_eq', form_def::factory('类型', '', form_def::select, (\Prj\Consts\Wares::$shilfIdName + ['' => '全部'])))
            ->addItem('pageid', $pageid)
            ->addItem('pagesize', $pager->page_size);
        if ($type == 'check') {
            
            //$frm->addItem('_statusCode_eq', form_def::factory('订单状态', '', form_def::select,([\Prj\Consts\Wares::status_new => "等待审核",\Prj\Consts\Wares::status_abandon =>"被驳回",]+[''=>'全部'])));
        } elseif ($type == 'checkSale') {
        } else {
            $frm->addItem('_statusCode_eq', form_def::factory('订单状态', '', form_def::select, (\Prj\Consts\Wares::$enum + ['' => '全部'])));
        }
        $frm->fillValues();
        if ($frm->flgIsThisForm) { //submit
            $where = $frm->getWhere();
            var_log($where,'where >>>');
        } else {
            $where = array();
        }

        $ids = $this->_request->get('ids');
        if (!empty($ids)) {
            if (!is_array($ids)) {
                $ids = explode(',', $ids);
            }
            foreach ($ids as $v) {
                $arr[] = \Prj\Misc\View::decodePkey($v)['waresId'];
            }
            $where['waresId'] = $arr;
        }

        if ($type == "check") //审核页面
        {
            $where['statusCode'] = \Prj\Consts\Wares::status_new;
            
        } elseif ($type == "checkSale") {
            $where['statusCode'] = \Prj\Consts\Wares::status_csale;
        }
        $ware = \Prj\Data\Wares::getCopy('');

        $pager->init($ware->getCount($where), $pageid);
     if($type==''){
        $fieldsMap = array(
            'waresId' => array('标的ID', '140'),
            'waresName' => array('标的名称', '130'),
            'assetId' => array('资产ID', '130'),
            'tags' => array('标签', '60'),
            'vipLevel' => array('vip等级', '60'),
            'interestStartType' => array('起息方式/还款方式', '120'),
            'returnType' => array('还款方式', '113'),
            'deadLine' => array('期限', '35'),
            'dlUnit' => array('单位', '200'),
            'yieldStatic' => array('利率%', '52'),
            'yieldStaticAdd' => array('活动加息', '57'),
            'shelfId' => array('类型', '35'),
            'amount' => array('总额(元)', '100'),
            'remain' => array('余额(元)', '100'),
            'realRaise' => array('实际募集(元)', '100'),
            'timeStartPlan' => array('计划上架时间/计划还款时间', '220'),
            'timeEndReal' => array('实际满标时间/实际转账时间', '220'),
            'payYmd' => array('满标转账时间', '140'),
            'ymdPayPlan' => array('计划还款时间', '92'),
            'statusCode' => array('状态/备注', '165'),
            'payStatus' => array('网关状态/订单号', '140'),
            'paySn' => array('网关订单号', '100'),
            'exp' => array('备注', '120'),
            'confirmGift' => array('逾期垫付(元)', '90'),
            'repay' => array('逾期还款(元)', '90'),
            'managementTrans'=>array('服务费','60'),
            'managementConfirm'=>array('手续费','60'),
        );
     }elseif($type=='check'){
          $fieldsMap = array(
            'waresId' => array('标的ID', 'auto'),
            'waresName' => array('标的名称', '130'),
            'assetId' => array('资产ID', '130'),
            'tags' => array('标签', '60'),
            'vipLevel' => array('vip等级', '60'),
            'interestStartType' => array('起息方式/还款方式', '120'),
           'returnType' => array('还款方式', 'auto'),
            'deadLine' => array('期限', '35'),
            'dlUnit' => array('单位', '0'),
            'yieldStatic' => array('利率%', '52'),
            'yieldStaticAdd' => array('活动加息', '57'),
            'shelfId' => array('类型', '35'),
            'amount' => array('总额(元)', 'auto'),
            'remain' => array('余额(元)', 'auto'),
            'realRaise' => array('实际募集(元)', '100'),
            'timeStartPlan' => array('计划上架时间/计划还款时间', '220'),
            'timeEndReal' => array('实际满标时间/实际转账时间', 'auto'),
            'payYmd' => array('满标转账时间', 'auto'),
            'ymdPayPlan' => array('计划还款时间', 'auto'),
            'statusCode' => array('状态/备注', 'auto'),
            'payStatus' => array('网关状态/订单号', 'auto'),
            'paySn' => array('网关订单号', 'auto'),
            'exp' => array('备注', 'auto'),
            'confirmGift' => array('逾期垫付(元)', 'auto'),
            'repay' => array('逾期还款(元)', 'auto'),
            'managementTrans'=>array('服务费','auto'),
            'managementConfirm'=>array('手续费','auto'),
        );
     }elseif ($type=='checkSale'){
         $fieldsMap = array(
            'waresId' => array('标的ID', 'auto'),
            'waresName' => array('标的名称', '130'),
            'assetId' => array('资产ID', '130'),
            'tags' => array('标签', '60'),
            'vipLevel' => array('vip等级', '60'),
            'interestStartType' => array('起息方式/还款方式', '120'),
           'returnType' => array('还款方式', 'auto'),
            'deadLine' => array('期限', '35'),
            'dlUnit' => array('单位', '0'),
            'yieldStatic' => array('利率%', '52'),
            'yieldStaticAdd' => array('活动加息', '57'),
            'shelfId' => array('类型', '35'),
            'amount' => array('总额(元)', 'auto'),
            'remain' => array('余额(元)', 'auto'),
            'realRaise' => array('实际募集(元)', '100'),
            'timeStartPlan' => array('计划上架时间/计划还款时间', '220'),
            'timeEndReal' => array('实际满标时间/实际转账时间', 'auto'),
            'payYmd' => array('满标转账时间', 'auto'),
            'ymdPayPlan' => array('计划还款时间', 'auto'),
            'statusCode' => array('状态/备注', 'auto'),
            'payStatus' => array('网关状态/订单号', 'auto'),
            'paySn' => array('网关订单号', 'auto'),
            'exp' => array('备注', 'auto'),
            'confirmGift' => array('逾期垫付(元)', 'auto'),
            'repay' => array('逾期还款(元)', 'auto'),
            'managementTrans'=>array('服务费','auto'),
            'managementConfirm'=>array('手续费','auto'),
        );
     }
        foreach ($fieldsMap as $k => $v) {
            $headers[$v[0]] = $v[1];
            $fields[]       = $k;
        }
        $fields[] = 'retryUrl';
        $fields[] = 'retryBtnShow';
        unset($headers['单位']);
        // unset($headers['标的ID']);
        unset($headers['资产ID']);
        unset($headers['还款方式']);
        unset($headers['计划还款时间']);
        unset($headers['满标转账时间']);
        unset($headers['备注']);
        unset($headers['网关订单号']);
        if ($isDownloadEXCEL == 1) //不分页打印
        {
            $search = $this->_request->get('where') ? $this->_request->get('where') : array();
            $where  = array_merge($where, $search);
             var_log($where,'>>>>>>>>>>>>>>>>>>>>>>>');
            $records = $ware->db()->getRecords($ware->tbname(), $fields, $where, ' sort statusCode rsort timeStartPlan');
        } else {
            $records = $ware->db()->getRecords($ware->tbname(), $fields, $where, 'sort statusCode rsort timeStartPlan', $pager->page_size, $pager->rsFrom());
        }
        //var_log(__CLASS__ . '>>>>' . \Sooh\DB\Broker::lastCmd());
        //records 处理
        $new = array();
        // var_log($records,'#r#######################');
        if (!empty($records)) {
            foreach ($records as $k => $v) {

                $_pkey_val_ = \Prj\Misc\View::encodePkey(array('waresId' => $v['waresId']));
                if (!$isDownloadEXCEL) $v['_pkey_val_'] = $_pkey_val_;
                $v['_shelfId']=$v['shelfId'];
                $v['shelfId'] = \Prj\Consts\Wares::$shilfIdName[$v['shelfId']];
                $v['deadLine'] .= $v['dlUnit'];
                $v['managementTrans']            = sprintf("%.2f", $v['managementTrans'] / 100);
                $v['managementConfirm']            = sprintf("%.2f", $v['managementConfirm'] / 100);
                $v['amount']            = sprintf("%.2f", $v['amount'] / 100);
                $v['remain']            = sprintf("%.2f", $v['remain'] / 100);
                $v['realRaise']         = sprintf("%.2f", $v['realRaise'] / 100);
                $v['yieldStatic']       = sprintf("%.2f", $v['yieldStatic'] * 100);
                $v['yieldStaticAdd']    = sprintf("%.2f", $v['yieldStaticAdd'] * 100);
                $v['interestStartType'] = \Prj\Consts\InterestStart::$enum[$v['interestStartType']];
                $v['returnType']        = \Prj\Consts\ReturnType::$enum[$v['returnType']];
                $v['timeStartPlan']     = $v['timeStartPlan'] ? \Prj\Misc\View::fmtYmd($v['timeStartPlan'], 'time') : '';
                $v['payYmd']            = $v['payYmd'] ? \Prj\Misc\View::fmtYmd($v['payYmd'], 'time') : '';
                $v['timeEndReal']       = $v['timeEndReal'] ? \Prj\Misc\View::fmtYmd($v['timeEndReal']) : '';
                $v['ymdPayPlan']        = $v['ymdPayPlan'] ? \Prj\Misc\View::fmtYmd($v['ymdPayPlan'], 'time') : '';
                $v['realStatusCode']    = $v['statusCode'];
                $v['_statusCode']       = $v['statusCode'];
                $v['statusCode']        = \Prj\Consts\Wares::$enum[$v['statusCode']];
                $v['_payStatus']        = $v['payStatus'];
                $v['payStatus']         = \Prj\Consts\PayGW::$status[$v['payStatus']] ? \Prj\Consts\PayGW::$status[$v['payStatus']] : '等待操作';
                var_log($v['retryUrl'].'/'.$v['retryBtnShow'],'重发按钮>>>');
                $v['payStatus'].= ' '.\Prj\Misc\View::retryBtn($v['retryUrl'],$v['retryBtnShow']);
                $v['paySn']             = $v['paySn'] ? $v['paySn'] : '';
                $v['confirmGift'] /= 100;
                $v['repay'] /= 100;
                unset($v['dlUnit']);
                unset($v['retryUrl']);
                unset($v['retryBtnShow']);
               // unset($v['waresId']);
//                unset($v['assetId']);
                unset($v['realStatusCode']);
                $new[$_pkey_val_] = $v;
                //$v['interestStartType/returnType']= '111';
            }
        }
        $records = $new;


        $replace = array();
        foreach ($records as $k => $v) {
            $v['interestStartType'] = $v['interestStartType'] . '<br>' . $v['returnType'];
            $v['timeStartPlan']     = $v['timeStartPlan'] . '/' . $v['ymdPayPlan'];
            if (!empty($v['timeEndReal']) && !empty($v['payYmd'])) {
                $v['timeEndReal'] = $v['timeEndReal'] . '/' . $v['payYmd'];
            } elseif (!empty($v['timeEndReal']) && empty($v['payYmd'])) {
                $v['timeEndReal'] = $v['timeEndReal'];
            }
            $v['statusCode'] = $v['statusCode'] . '<br>' . $v['exp'];
            $v['payStatus']  = $v['payStatus'] . '<br>' . $v['paySn'];
            unset($v['paySn']);
            unset($v['exp']);
            unset($v['payYmd']);
            unset($v['ymdPayPlan']);
            unset($v['returnType']);
            $replace[] = $v;
        }
        $records = $replace;
       // var_log($records[0]['statusCode'],'records>>>>>>>>>>>>>>>>>>>>>>>>>>>');
        //==
        //var_log($records,'#r#############################');
        if ($isDownloadEXCEL) {
             $new=[];
            foreach ($records as $k => $v) {
                //TODO 过滤
               unset($v['assetId']);
               $new[]=$v;
            }
             $records=$new;
            //$this->_view->assign('records', $records);
            //return;
            //array_shift($headers);
            return $this->downEXCEL($records, array_keys($headers), null, true);
        } else {
            unset($headers['ID']);
            $this->_view->assign('headers', $headers);
            $this->_view->assign('records', $records);
            $this->_view->assign('pager', $pager);
            $this->_view->assign('type', $type);
            $this->_view->assign('where', $where);
        }

    }

    public function checkSaleAction()
    {
//         $fieldsMap = array(
//             'waresId' => array('标的ID', 'auto'),
//             'waresName' => array('标的名称', '100'),
//             'tags' => array('标签', '60'),
//             'vipLevel' => array('vip等级', '60'),
//             'interestStartType' => array('起息方式', '70'),
//             'returnType' => array('还款方式', 'auto'),
//             'deadLine' => array('期限', '35'),
//             'dlUnit' => array('单位', 'auto'),
//             'yieldStatic' => array('利率(%)', '52'),
//             'yieldStaticAdd' => array('活动加息', '57'),
//             'shelfId' => array('类型', '65'),
//             'amount' => array('总额(元)', 'auto'),
//             'remain' => array('余额(元)', 'auto'),
//             'timeStartPlan' => array('计划上架时间', '160'),
//             'timeEndReal' => array('实际满标时间', 'auto'),
//             'payYmd' => array('满标转账时间', 'auto'),
//             'ymdPayPlan' => array('计划还款时间', 'auto'),
//             'statusCode' => array('状态', '70'),
//             'payStatus' => array('网关状态', '60'),
//             'paySn' => array('网关订单号', 'auto'),
//             'exp' => array('备注', 'auto'),
//         );
       
        $this->indexAction('checkSale');
    }

    //状态变更

   public function rejectAction()
    {
        $this->_updateStatus(\Prj\Consts\Wares::status_abandon);
        $type=$this->_request->get('type');
       // var_log($type,'type>>>>>>>>>>>>');
        $this->closeAndReloadPage($this->tabname($type));
    }
    //随机生成ID

    public function transAction()
    {
        $this->loger->userId = "trans";
        //\Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $where = \Prj\Misc\View::decodePkey($this->_request->get('_pkey_val_'));
        //var_log($where,'_pkey_val_>>>>>>>>>');
        $dataMap = [
            'sn' => '订单号',
            'waresId' => '标的ID',
            'amountReal' => '实际募集(元)',
            'amountGift' => '平台垫付(元)',
            'amountTotal' => '借款总额(元)',
            'amountService' => '服务费(元)',
            'borrowerId' => '借款人ID',
            'borrowerName' => '借款人名称',
        ];
        $frm     = \Sooh\Base\Form\Broker::getCopy('default')
            ->init(\Sooh\Base\Tools::uri(), 'get', empty($where) ? \Sooh\Base\Form\Broker::type_c : \Sooh\Base\Form\Broker::type_u);
        $frm->addItem('_pkey_val_', $this->_request->get('_pkey_val_'));
        $frm->fillValues();
        $wares = \Prj\Data\Wares::getCopy($where['waresId']);
        $wares->load();
        if (!$wares->exists()) return $this->returnError(\Prj\Lang\Broker::getMsg('wares.waresId_error'));
        $borrowerId = $wares->getField('borrowerId');
        /*
        $bor = \Prj\Data\User::getCopy($borrowerId);
        $bor->load();
        */
        $introDisplay = $wares->getField('introDisplay');
        $borrowerName = $introDisplay['b']['name'];

        //  if($wares->getField('statusCode')!=\Prj\Consts\Wares::status_go)return $this->returnError('该标的尚未募集结束');
        if ($wares->getField('statusCode') != \Prj\Consts\Wares::status_go) return $this->returnError(\Prj\Lang\Broker::getMsg('wares.amountreal_notend'));
        if (!$frm->flgIsThisForm) {
            // if(!$wares->exists())return $this->returnError('标的不存在');
            $data = [
                'sn' => $this->_creatID(),
                'waresId' => $where['waresId'],
                'amountReal' => $wares->getField('realRaise') / 100,
                'amountGift' => ($wares->getField('amount') - $wares->getField('realRaise')) / 100,
                'amountTotal' => $wares->getField('amount') / 100,
                'amountService' => $wares->getField('managementTrans') / 100,
                'borrowerId' => $borrowerId,
                'borrowerName' => $borrowerName,
            ];
            foreach ($data as $k => $v) {
                $frm->addItem($k, form_def::factory($dataMap[$k], $v, form_def::constval));
            }
        } else {
            $asynchronization = 0; //异步开关

            \Sooh\Base\Ini::getInstance()->viewRenderType('json');
            if (in_array($wares->getField('payStatus'), [\Prj\Consts\PayGW::success])) {
                return $this->returnError(\Prj\Lang\Broker::getMsg('wares.Repeat_submit'));
            }
            try {
                if ($asynchronization) {
                    $url = 'http://' . $_SERVER['HTTP_HOST'] . "/index.php?__=index/crond/trans&__VIEW__=json&waresId={$where['waresId']}";
                    // var_log($url,'url>>>');
                    $ret = \Prj\Tool\Func::curl_post($url, [], 1);
                } else {
                    $ret = $wares->trans();
                }

            } catch (\ErrorException $e) {
                return $this->returnError($e->getMessage());
            }


            $this->closeAndReloadPage();
            if (isset($ret['status'])) {
                if ($ret['status'] == 8 || $ret['status'] == 1) {
                    return $this->returnOK('满标转账成功');
                } else {
                    return $this->returnError('网关处理失败：' . $ret['reason']);
                }
            } else {
                return $this->returnOK('新浪已受理');
            }


        }

    }

    /**
     * 逾期还款
     * @throws ErrorException
     * @throws \Sooh\Base\ErrorException
     */
    public function delayConfirmAction()
    {
        $this->closeAndReloadPage();
        $this->loger->userId = "delayConfirm";
        //\Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $where = \Prj\Misc\View::decodePkey($this->_request->get('_pkey_val_'));
        //var_log($where,'_pkey_val_>>>>>>>>>');
        $dataMap = [
            'sn' => '订单号',
            'waresId' => '标的ID',
            'waresName' => '标的名 ',
            'confirmGift' => '逾期欠款(元)',
            'repay' => '逾期已还(元)',
            'borrowerId' => '借款人ID',
            'borrowerName' => '借款人名称',
        ];
        $frm     = \Sooh\Base\Form\Broker::getCopy('default')
            ->init(\Sooh\Base\Tools::uri(), 'get', empty($where) ? \Sooh\Base\Form\Broker::type_c : \Sooh\Base\Form\Broker::type_u);
        $frm->addItem('_pkey_val_', $this->_request->get('_pkey_val_'));
        $wares = \Prj\Data\Wares::getCopy($where['waresId']);
        $wares->load();
        if (!$wares->exists()) return $this->returnError(\Prj\Lang\Broker::getMsg('wares.waresId_error'));
        $borrowerId = $wares->getField('borrowerId');
        /*
        $bor = \Prj\Data\User::getCopy($borrowerId);
        $bor->load();
        */
        $introDisplay = $wares->getField('introDisplay');
        $borrowerName = $introDisplay['b']['name'];

        //  if($wares->getField('statusCode')!=\Prj\Consts\Wares::status_go)return $this->returnError('该标的尚未募集结束');
        if ($wares->getField('statusCode') != \Prj\Consts\Wares::status_go) return $this->returnError(\Prj\Lang\Broker::getMsg('wares.amountreal_notend'));

        $data = [
            'sn' => $this->_creatID(),
            'waresId' => $where['waresId'],
            'waresName' => $wares->getField('waresName'),
            'confirmGift' => $wares->getField('confirmGift') / 100,
            'repay' => $wares->getField('repay') / 100,
            'borrowerId' => $borrowerId,
            'borrowerName' => $borrowerName,
        ];
        $frm->addItem('realRepay', form_def::factory('逾期还款(元)', ($wares->getField('confirmGift') - $wares->getField('repay')) / 100, form_def::text, [], ['data-rule' => 'required;digits']));
        foreach ($data as $k => $v) {
            $frm->addItem($k, form_def::factory($dataMap[$k], $v, form_def::constval));
        }
        $frm->fillValues();
        if (!$frm->flgIsThisForm) {

        } else {

            $fields  = $frm->getFields();
            $waresId = $where['waresId'];
            $repay   = $fields['realRepay'] * 100;
            $total   = \Prj\Data\Systally::getWaitDelayAmount($waresId);
            if ($total + $repay > $wares->getField('confirmGift') - $wares->getField('repay')) {
                return $this->returnError('还款金额超限');
            }

            //调用网关
            $rpc = \Sooh\Base\Ini::getInstance()->get('noGW') ? self::getRpcDefault('PayGW') : \Sooh\Base\Rpc\Broker::factory('PayGW');
            $sys = \Lib\Services\PayGW::getInstance($rpc);
            //$sn,$waresId,$borrowerId,$repay
            $sn    = $fields['sn'];
            $tally = \Prj\Data\Systally::addTally($sn, $repay, $borrowerId, $waresId);
            $tally->setField('type', \Prj\Consts\PayGW::tally_delayConfirm);
            try {
                $data = [
                    $sn,
                    $waresId,
                    $borrowerId,
                    $repay
                ];
                //var_log($data,'发给网关的参数>>>>>>>>>>>>>>>>>>>>>');
                $ret = call_user_func_array([$sys, 'delayConfirm'], $data);
                //return $this->returnError('中断');
            } catch (\Sooh\Base\ErrException $e) {
                $code = $e->getCode();
                if ($code == 400) {
                    $this->error($e->getMessage());
                } elseif ($code == 500) {
                    $this->error($e->getMessage());
                }
                // return $this->returnError('gw_error');
                $tally->setField('exp', '网关错误:' . $e->getMessage());
                try {
                    $tally->update();
                } catch (\ErrorException $e) {
                    var_log('[error]delayConfirm systally update failed >>>');
                }
                return $this->returnError(\Prj\Lang\Broker::getMsg('system.gw_error'));
            }

            if ($ret['status'] == 1 || $ret['status'] == 8) {
                $tally->setField('statusCode', \Prj\Consts\Systally::wait_status);
            } else {
                $tally->setField('statusCode', \Prj\Consts\Systally::failed_status);
                $tally->setField('exp', $ret['reason']);
            }

            try {
                $tally->update();
            } catch (\ErrorException $e) {
                var_log('[error]delayConfirm systally update failed >>>');
                return $this->returnError('系统错误:流水更新失败');
            }

            return $this->returnOK('操作成功!');
        }
    }

    protected function error($msg, $code = 300)
    {
        throw new \ErrorException($msg, $code);
    }

    public function testChangeStatusAction()
    {
        if (!$this->debug()) return;
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $num  = $this->_request->get('status');
        $ware = \Prj\Data\Wares::getCopy('');
        $ret  = $ware->db()->updRecords($ware->tbname(), array('statusCode' => $num));
        $this->_view->assign('更新记录', $ret);
    }

    public function newUploadAction()
    {
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $fileArr = $_FILES['file'];
        //if(empty($_FILES['file']))return $this->returnError('no_img');
        if (empty($_FILES['file'])) return $this->returnError(\Prj\Lang\Broker::getMsg('wares.img_missing'));
        $data = file_get_contents($fileArr['tmp_name']);
        if ($this->_request->get("type")) {
            $type = $this->_request->get("type");
        } else {
            $type = 'wares';
        }
        $month = date('Ym');
        $date  = $month . date('dHis');
        if ($fileId = \Prj\Data\Files::createNew($data, $type . '_', $date)) {
            $this->_view->assign('fileId', $fileId);
        } else {
            //return $this->returnError('插入DB失败');
            return $this->returnError(\Prj\Lang\Broker::getMsg('system.db_error'));
        }
        $this->returnOK('成功');
    }


    //测试 添加几条记录

    public function uploadAction()
    {
        \Sooh\Base\Ini::getInstance()->viewRenderType('echo');
        $data  = file_get_contents($_FILES['imgFile']['tmp_name']);
        $error = '';
        //var_log($_FILES['imgFile']);
        $maxSize  = 999; //单位K
        $fileName = $_FILES['imgFile']['name'];
        //定义允许上传的文件扩展名
        $ext_arr = array(
            'image' => array('gif', 'jpg', 'jpeg', 'png', 'bmp'),
            'flash' => array('swf', 'flv'),
            'media' => array('swf', 'flv', 'mp3', 'wav', 'wma', 'wmv', 'mid', 'avi', 'mpg', 'asf', 'rm', 'rmvb'),
            'file' => array('doc', 'docx', 'xls', 'xlsx', 'ppt', 'htm', 'html', 'txt', 'zip', 'rar', 'gz', 'bz2'),
        );
        //文件类型
        $tail = strstr($fileName, '.');
        $tail = str_replace('.', '', $tail);
        if (!in_array($tail, $ext_arr['image'])) {
            $error = "不支持的图片格式";
        }
        // var_log($tail);
        //大小限制
        if ($_FILES['imgFile']['size'] / 1000 > $maxSize) {
            $error = "图片大小不能大于$maxSize K";
        }
        //PHP上传失败
        if (!empty($_FILES['imgFile']['error'])) {
            switch ($_FILES['imgFile']['error']) {
                case '1':
                    $error = '超过php.ini允许的大小。';
                    break;
                case '2':
                    $error = '超过表单允许的大小。';
                    break;
                case '3':
                    $error = '图片只有部分被上传。';
                    break;
                case '4':
                    $error = '请选择图片。';
                    break;
                case '6':
                    $error = '找不到临时目录。';
                    break;
                case '7':
                    $error = '写文件到硬盘出错。';
                    break;
                case '8':
                    $error = 'File upload stopped by extension。';
                    break;
                case '999':
                default:
                    $error = '未知错误。';
            }
        }
        $ret = true;
        if (!empty($data) && empty($error)) {
            $ret = \Prj\Data\Files::createNew($data, 'manage_wares_');
        }
        if (!$ret) {
            $error = '插入数据库失败！';
        } else {
            $url     = \Sooh\Base\Tools::uri(array('fileId' => $ret), 'getimage', 'public', 'index');
            $jsonArr = array(
                "error" => 0,
                "url" => $url
            );
        }
        if (!empty($error)) {
            $jsonArr = array(
                "error" => 1,
                "message" => $error
            );
        }
        echo json_encode($jsonArr);
    }

    public function addWaresAction()
    {
        if (!$this->debug()) return;
        $fields = $this->createWares();
        $ware   = \Prj\Data\Wares::getCopy($this->_creatID());
        $ware->load();
        foreach ($fields as $k => $v) {
            $ware->setField($k, $v);
        }
        $ware->setField('sLockData', null);
        $ware->update();
        $fields['ymdPayPlanYmd'] = date('YmdHis', $fields['ymdPayPlan']);
        $this->_view->assign('wares', $fields);
        $this->returnOK('新增标的成功！');
    }

    public function createWares()
    {
        $nameArr             = [
            '新立盈',
            '长兴',
            '车易盈',
            '友居贷',
            'e起赢',
            '易典通',
            '租益盈',
        ];
        $waresSn             = rand(1, 9);
        $nameId              = rand(0, 6);
        $deadLine            = [3, 7, 14, 30, 3, 6, 12];
        $dlUnit              = ['天', '天', '天', '天', '月', '月', '月'];
        $fields              = $this->testFields;
        $fields['waresName'] = $nameArr[$nameId] . rand(1, 9) . '号00' . rand(1, 9) . '-' . $waresSn;
        $fields['waresSN']   = $waresSn;
        $fields['deadLine']  = $deadLine[$nameId];
        $fields['dlUnit']    = $dlUnit[$nameId];
        if (in_array($nameId, [0, 1, 2, 3])) //1：1次   2：按月
        {
            $fields['returnType'] = 1;
            $days                 = $fields['deadLine'];
        } else {
            $fields['returnType'] = 2;
            $days                 = $fields['deadLine'] * 30;
        }
        $tags                        = ['新手', '活动', '', '新手,活动'];
        $tagsId                      = rand(0, 3);
        $fields['tags']              = $tags[$tagsId];
        $fields['waresSN']           = $waresSn;
        $fields['timeStartPlan']     = \Sooh\Base\Time::getInstance()->ymdhis();
        $fields['timeEndReal']       = date('YmdHis', strtotime('+3 days', strtotime($fields['timeStartPlan'])));
        $fields['ymdPayPlan']        = strtotime("+$days days", strtotime($fields['timeStartPlan']));
        $arr                         = [0, 2];
        $fields['interestStartType'] = \Prj\Consts\InterestStart::whenFull;

        if (in_array($tagsId, [1, 2])) {
            $fields['yieldStaticAdd'] = 0.01;
        }
        $shelfId = [2000, 3000, 4000];


        $fields['statusCode'] = \Prj\Consts\Wares::status_open;

        return $fields;
    }

    public function updateReturnPlanAction()
    {
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $_pkey = $this->_request->get('_pkey_val_');
        $where = \Prj\Misc\View::decodePkey($_pkey);
        $ware  = \Prj\Data\Wares::getCopy($where['waresId']);
        $ware->load();
        // if($ware->getField('statusCode')!=\Prj\Consts\Wares::status_go)return $this->returnError("该标的尚未募集结束");
        if ($ware->getField('statusCode') != \Prj\Consts\Wares::status_go) return $this->returnError(\Prj\Lang\Broker::getMsg('wares.amountreal_notend'));
        // if($error = $ware->createReturnPlan())return $this->returnError($error);
        //更新名下所有订单
        $rs  = \Prj\Data\Investment::loopFindRecords(['waresId' => $where['waresId'], 'orderStatus' => [\Prj\Consts\OrderStatus::payed, \Prj\Consts\OrderStatus::going]]);
        $num = 0;
        if (!empty($rs)) {
            foreach ($rs as $k => $v) {
                $tempInvest = \Prj\Data\Investment::getCopy($v['ordersId']);
                $error      = $tempInvest->createReturnPlan();
                if (!$error) {
                    $num++;
                    try {
                        //推送
                        $user = \Prj\Data\User::getCopy($v['userId']);
                        $user->load();
                        $phone = $user->getField('phone');
                        /*
                        $ret = \Prj\ReadConf::run(
	                        [
		                        'event' => 'look_ok',
		                        'pro_name' => $ware->getField('waresName'),
		                        'time_all' => 24,
		                        'brand' => \Prj\Message\Message::MSG_BRAND,
		                        'cont_ok' => '投资记录'
	                        ],
                            ['phone' => $phone]
                        );
                        $ret1 = \Prj\ReadConf::run(
	                        [
		                        'event' => 'money_start',
		                        'pro_name' => $ware->getField('waresName'),
		                        'touzi_money' => ($ware->getField('amount') + $ware->getField('amountExt')) / 100,
		                        'money_back' => '房贷'
	                        ],
                            ['userId' => $v['userId']]
                        );
                        */
                    } catch (\ErrorException $e) {

                    }
                } else {
                    //var_log($v['ordersId'] . ':' . $error, 'createReturnPlan>>>>>>>>>');
                }
            }

        }
        $this->returnOK('更新总计' . $num . '条，其中0条失败，' . $num . '条成功');
        //$this->returnOK('更新了'.$num.'条订单[确保订单支付成功以后更新还款计划]');
    }

    public function flowAction()
    {
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $where = \Prj\Misc\View::decodePkey($this->_request->get('_pkey_val_'));
        if (empty($where)) {
            return $this->returnError('args_error');
        } else {
            try {
                $this->_flow($where['waresId']);
            } catch (\ErrorException $e) {
                return $this->returnError($e->getMessage());
            }
        }

        return $this->returnOK('流标成功,请等待网关处理');
    }

    protected function _flow($waresId)
    { //流标处理
        $ware = \Prj\Data\Wares::getCopy($waresId);
        $ware->load();
        if (!$ware->exists()) {
            throw new \ErrorException('标的不存在');
        } else {
            $where      = ['waresId' => $waresId, 'orderStatus' => [\Prj\Consts\OrderStatus::waiting, \Prj\Consts\OrderStatus::payed, \Prj\Consts\OrderStatus::going]];
            $investList = \Prj\Data\Investment::loopAll($where);
            if (!$ware->lock('流标锁定')) {
                sleep(1);
                if (!$ware->lock('流标锁定')) {
                    $this->error('标的锁定失败,请稍后重试!');
                }
            }
            if (empty($investList)) {
                $ware->setField('statusCode', \Prj\Consts\Wares::status_flow);
                $ware->setField('payStatus', \Prj\Consts\PayGW::success);
                $ware->update();
            } else {
                //调用网关
                $rpc = \Sooh\Base\Ini::getInstance()->get('noGW') ? self::getRpcDefault('PayGW') : \Sooh\Base\Rpc\Broker::factory('PayGW');
                $sys = \Lib\Services\PayGW::getInstance($rpc);
                $sn  = time() . rand(100000, 999999);
                $ware->setField('paySn', $sn);
                $ware->setField('statusCode', \Prj\Consts\Wares::status_flow);
                try {
                    $data = [
                        $sn,
                        $waresId,
                    ];
                    //var_log($data,'发给网关的参数>>>>>>>>>>>>>>>>>>>>>');
                    $ret = call_user_func_array([$sys, 'abort'], $data);
                } catch (\Sooh\Base\ErrException $e) {
                    $ware->setField('exp', "网关错误:" . $e->getMessage());
                    try {
                        $ware->update();
                    } catch (\ErrorException $e) {
                        var_log("[error]流标网关错误#sn:" . $sn . " waresId:" . $waresId . " error:" . $e->getMessage());
                    }
                    $code = $e->getCode();
                    if ($code == 400) {
                        $this->error($e->getMessage());
                    } elseif ($code == 500) {
                        $this->error($e->getMessage());
                    }
                    $this->error(\Prj\Lang\Broker::getMsg('system.gw_error'));
                }

                if ($ret['status'] == 8 || $ret['status'] == 1) {
                    $ware->setField('payStatus', \Prj\Consts\PayGW::accept);
                } else {
                    $ware->setField('payStatus', \Prj\Consts\PayGW::failed);
                    $ware->setField('exp', $ret['reason']);
                }

                //var_log($ware,'ware>>>');
                $ware->update();
            }
        }
    }

    public function aheadFullAction()
    {
        $where = \Prj\Misc\View::decodePkey($this->_request->get('_pkey_val_'));
        if (empty($where['waresId'])) return $this->returnError('系统错误:标的ID为空');
        $waresId = $where['waresId'];
        $ware    = \Prj\Data\Wares::getCopy($waresId);
        $ware->load();
        if (!$ware->exists()) return $this->returnError('不存在的标的');
        if (\Prj\Data\Investment::getCountByWaresId($waresId) < 1) return $this->returnError('该标的尚无人购买');
        $msg      = date('H:i:s') . '#提前满标';
        $maxRetry = 3;
        while ($maxRetry >= 0 && !$ware->lock($msg, 86400)) {
            if ($maxRetry == 0) {
                return $this->returnError('系统正忙,请稍后重试');
            }
            sleep(2);
            $ware->reload();
            $maxRetry--;
        }

        if ($ware->getField('statusCode') != \Prj\Consts\Wares::status_open) {
            $ware->unlock();
            return $this->returnError('不合法的标的状态');
        }

        $ware->setField('statusCode', \Prj\Consts\Wares::status_go);
        $ware->setField('timeEndReal', date('YmdHis'));

        try {
            $ware->update();
        } catch (\ErrorException $e) {
            $ware->unlock();
            return $this->returnError('系统错误:标的更新失败');
        }

        return $this->returnOK('满标成功,等待满标转账');
    }

    function openNowAction(){
        $where = \Prj\Misc\View::decodePkey($this->_request->get('_pkey_val_'));
        var_log($where,'where >>> ');
        $waresId = $where['waresId'];
        $wares = \Prj\Data\Wares::getCopy($waresId);
        $wares->load();
        if(!$wares->exists())return $this->returnError('商品不存在');
        if($wares->getField('statusCode')!=\Prj\Consts\Wares::status_ready)return $this->returnError('商品状态已变更,请刷新重试');

        $wares->setField('statusCode',\Prj\Consts\Wares::status_open);
        $wares->setField('statusCode1',0);
        $wares->setField('timeStartReal',date('YmdHis'));
        $wares->setField('timeStartPlan',date('YmdHis'));

        try{
            $wares->update();
        }catch (\ErrorException $e){
            return $this->returnError('数据库错误'.$e->getMessage());
        }

        return $this->returnOK('更新成功');
    }

    function unlockwaresAction(){
        $where                          = \Prj\Misc\View::decodePkey($this->_request->get('_pkey_val_'));
        $type                           = $this->_request->get('_type');
        $frm                            = \Sooh\Base\Form\Broker::getCopy('default')
            ->init(\Sooh\Base\Tools::uri(), 'get', empty($where) ? \Sooh\Base\Form\Broker::type_c : \Sooh\Base\Form\Broker::type_u);

        //锁定用户列表
        $records = \Prj\Data\Wares::loopFindRecords(['sLockData!'=>'']);
        $header = [
            'waresId'=>['标的ID','100'],
            'waresName'=>['标的名称','130'],
            'amount'=>['标的总额(元)','100'],
            'remain'=>['标的余额(元)','100'],
            'realRaise'=>['实际募集(元)','100'],
            'timeStartReal'=>['上架时间','200'],
            'sLockData'=>['锁定详情','400'],
        ];
        $userList = [];
        if($records){
            $userList = array_map(function($value) use ($header){
                foreach($header as $k=>$v){
                    $tmp[$k] = $value[$k];
                }
                if(isset($tmp['amount']))$tmp['amount']/=100;
                if(isset($tmp['remain']))$tmp['remain']/=100;
                if(isset($tmp['realRaise']))$tmp['realRaise']/=100;
                if(isset($tmp['timeStartReal']))$tmp['timeStartReal']=$tmp['timeStartReal']?date('Y-m-d H:i:s',strtotime($tmp['timeStartReal'])):'';
                return $tmp;
            },$records);
        }
        $this->_view->assign('userList',$userList);
        $this->_view->assign('header',$header);
        //var_log($userList,'userList >>> ');
        //todo 构造表单数据

        $waresId = $this->_request->get('waresId');
        if($waresId){
//=========================================================================
            $investHeader = [
                'ordersId'=>['订单号','160'],
                'waresId'=>['标的号','137'],
                'waresName'=>['标的名称','120'],
                'userId'=>['用户ID','120'],
                'amount'=>['实投金额/红包','90'],
                //'amountExt'=>['红包','35'],
                //'amountFake'=>['券金','35'],
                'yieldStatic'=>['固定年化/活动加息','115'],
                //'yieldStaticAdd'=>['活动加息','60'],
                //'interest'=>['本金收益/奖励收益','115'],
                //'interestExt'=>['奖励收益','60'],
                //'extDesc'=>['赠送说明','105'],
                'orderTime'=>['下单时间/下次还款日','210'],
                'orderStatus'=>['订单状态','120'],
                //'returnType'=>['还款方式','112'],
                //'returnNext'=>['下次还款日','60'],
            ];
            $pager            = new \Sooh\DB\Pager(10, $this->pageSizeEnum, false);
            $pager->page_size = 10;
            $investWhere = ['waresId'=>$waresId];
            $pager->total = \Prj\Data\Investment::loopGetRecordsCount($investWhere);
            $investList = \Prj\Data\Investment::pager(null,$pager,null,null,$investWhere,null)['records'];
            //var_log($investList,'investList >>> ');
                //($invest->tbname(),'*',['userId'=>$userId,'orderStatus!'=>\Prj\Consts\OrderStatus::abandon],'rsort orderTime',10,0);
            array_walk($investList,function(&$v,$k) use ($investHeader){
                $v['orderStatus'] = \Prj\Consts\OrderStatus::$enum[$v['orderStatus']];
                $v['amount'] = ($v['amount']/100).'/'.$v['amountExt']/100;
                $v['yieldStatic'] = ($v['yieldStatic']).'/'.$v['yieldStaticAdd'];
                $v['orderTime'] = date('Y-m-d H:i:s',strtotime($v['orderTime']));
                $v['returnNext'] = date('Y-m-d  ',strtotime($v['returnNext']));
                $v['orderTime'] = $v['orderTime'].'/'.$v['returnNext'];
                foreach($investHeader as $kk=>$vv){
                    $tmp[$kk] = $v[$kk];
                }
                $v = $tmp;
            });
            $this->_view->assign('investHeader',$investHeader);
            $this->_view->assign('investList',$investList);
//=========================================================================
        }


        if ($frm->type() == \Sooh\Base\Form\Broker::type_u) //update show
        {
            //todo 字段展示 设置item的value
            /*
            $??? = \Prj\Data\???::getCopy($where['id']);
            $???->load();
            $arr = $???->dump();
            foreach($frm->items as $k=>$v){
                if(array_key_exists($k,$arr))$frm->items[$k]->value = $arr[$k];
            }
            */
        }

        //var_dump($fields);
        //die();
        $this->_view->assign('FormOp', $op = '添加商品');
        $this->_view->assign('type', $type);
        $this->_view->assign('_pkey_val_', $this->_request->get('_pkey_val_'));
    }

    public function doUnlockwaresAction(){
        $waresId = $this->_request->get('waresId');
        $wares = \Prj\Data\Wares::getCopy($waresId);
        $wares->load();
        if(!$wares->exists())return $this->returnError('不存在的标的');
        $sLockData = $wares->getField('sLockData');
        try{
            $wares->unlock();
        }catch (\ErrorException $e){
            var_log($e->getCode(),'code >>> ');
            if($e->getCode()!=0){
                return $this->returnError($e->getMessage());
            }
        }
        try{
            \Prj\Data\UserChangeLog::addLog('unlockWare',100,json_encode([
                'waresId'=>$waresId,
                'sLockData'=>$sLockData,
            ]));
        }catch (\ErrorException $e){

        }
        return $this->returnOK('解锁成功!');
    }
}
