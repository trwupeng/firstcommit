<?php
use Sooh\Base\Form\Item as form_def;
use Prj\Data\Asset as Asset;
use Prj\Consts\Wares;

class AssetController extends \Prj\ManagerCtrl {
    
    public function indexAction ($type = '') {
        //\Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $isDownloadEXCEL = $this->_request->get('__EXCEL__');
        $ids = $this->_request->get('ids');
        $idsArr = [];
        if(!empty($ids)){
            foreach($ids as $k=>$v){
                $arr = \Prj\Misc\View::decodePkey($v);
                $idsArr[] = $arr['assetId'];
            }
        }
        //配置表格
        $fieldsMapArr = array(
            'assetId'    => ['资产ID', 'auto'],
            'assetName'    => ['资产名称', 'auto'],
            'viewTPL'    => ['产品类型', 'auto'],
            'amount'    => ['总金额(单位元)', 'auto'],
            'remain'    => ['剩余金额', 'auto'],
            'startYmd'    => ['起始日期', 'auto'],
            'endYmd'    => ['结束日期', 'auto'],
            'days'    => ['天数', '60'],
            'status'    => ['状态', 'auto'],
            'count'    => ['关联标的数', 'auto'],
            'createTime'    => ['创建时间', 'auto'],
        );

        //配置分页
        $pageid = $this->_request->get('pageId', 1) - 0;
        $pager  = new \Sooh\DB\Pager($this->_request->get('pageSize', 10), $this->pageSizeEnum, false);
        $pager->init(-1,$pageid);

        //配置搜索项
        $frm = \Sooh\Base\Form\Broker::getCopy('default')->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_s);
        $frm->addItem('_assetName_lk', form_def::factory('资产名称包含', '', form_def::text))
            ->addItem('pageId', $pageid)
            ->addItem('pageSize', $this->pager->page_size);
        $frm->fillValues();
        if ($frm->flgIsThisForm) {
            $where = $frm->getWhere();
        } else {
            $where = array();
        }
        //合并表单的查询条件
        $search = \Prj\Misc\View::decodePkey($this->_request->get('where'));
        $where = array_merge($search?$search:[],$where);

        if($type == 'check'){
            $where = array_merge($where,['status'=>\Prj\Consts\Asset::status_new]);
        }else{
            // $where['status!'] = \Prj\Consts\Asset::status_new;
        }
        //拉取记录
        if(!empty($idsArr)){
            $where = array_merge($where,['assetId'=>$idsArr]);
        }
      //  var_log($rs,'查询条件>>>>>>>>>>>>>>>>>>');
        $where['status>'] = \Prj\Consts\Asset::status_delete;
        if(!$isDownloadEXCEL){
            $rs = Asset::pager($pager,$where,'rsort createTime');
        }else{
            $rs = Asset::loopFindRecords($where);
        }
         
        //格式配置
        $tempArr = array();
        $newArr  = array();
        foreach ($fieldsMapArr as $kk => $vv) {
            $header[$vv[0]] = $vv[1];
        }
        foreach ($rs as $k => $v) {
            //选中项打印
            if($ids = $this->_request->get('ids')){
                $tmp = [];
                foreach($ids as $vv){
                    $tmp[] = \Prj\Misc\View::decodePkey($vv)['ordersId'];
                }
                //todo 主键匹配
                if(!in_array($v['orderId'],$tmp)){
                    continue;
                }
            }
            foreach ($fieldsMapArr as $kk => $vv) {
                $tempArr[$kk] = $v[$kk];
            }
            //todo 数据格式化
            $str='';
             $view=$tempArr['viewTPL'];
             if($view=='Std02'){
                 $str='房贷';
             }elseif($view=='Std03'){
                 $str='车贷';
             }
            $tempArr['viewTPL']=$str;
            $tempArr['amount']/=100;
            $tempArr['amount'] = sprintf('%.2f',$tempArr['amount']);
            $tempArr['remain']/=100;
            $tempArr['remain'] = sprintf('%.2f',$tempArr['remain']);
            $tempArr['startYmd'] = date('Y-m-d',strtotime($tempArr['startYmd']));
            $tempArr['endYmd'] = date('Y-m-d',strtotime($tempArr['endYmd']));
            $tempArr['_status'] = $tempArr['status'];
            $tempArr['status'] = \Prj\Consts\Asset::$num[$tempArr['status']];
            $tempArr['days'] = (strtotime($tempArr['endYmd'])-strtotime($tempArr['startYmd']))/86400;
            $tempArr['createTime'] = $tempArr['createTime']?date('Y-m-d H:i:s',strtotime($tempArr['createTime'])):'';
            $tempArr['count'] = \Prj\Data\Wares::loopGetRecordsCount(['assetId'=>$tempArr['assetId']]);
            //===
            $newArr[] = $tempArr;
        }
       // var_log($newArr,'############################');
     
        
        $rs = $newArr;
     
      
        
     var_log($rs,'############################');
       // if($isDownloadEXCEL)return $this->downEXCEL($rs, array_keys($header),null,true);
        if($isDownloadEXCEL){
            var_log($rs,'Excel_rs >>> ');
            return $this->downEXCEL($rs, array_keys($header),null,true);
        }
        //输出
        $this->_view->assign('rs', $rs);
        $this->_view->assign('header', $header);
        $this->_view->assign('pager', $pager);
        $this->_view->assign('type', $type);
        $this->_view->assign('where', \Prj\Misc\View::encodePkey($where));
    }

    public function checkAction(){
        $this->indexAction('check');
    }

    public function editAction()
    {
        // error_reporting(E_ALL);
        $editorTpl                      = $this->_request->get('viewTPL');  //选择内容展示模板
        $Editor                         = "\\Prj\\WaresTpl\\" . ($editorTpl?$editorTpl:'Std02') . '\\Editor';
        $this->editor = $Editor;
        // \Prj\Misc\FormRenderer::$editor = $Editor::getCopy();
        $where                          = \Prj\Misc\View::decodePkey($this->_request->get('_pkey_val_'));
        $type                           = $this->_request->get('_type');
        $frm                            = \Sooh\Base\Form\Broker::getCopy('default')
            ->init(\Sooh\Base\Tools::uri(), 'get', empty($where) ? \Sooh\Base\Form\Broker::type_c : \Sooh\Base\Form\Broker::type_u);

        $frm->addItem('viewTPL', form_def::factory('内容模版', 'Std02', form_def::select, \Prj\Consts\Wares::$viewTPL))
            ->addItem('assetName', form_def::factory('资产名称', '中国好资产'.rand(1000,9999), form_def::text, [], ['data-rule' => 'required,length[~15]']))
            ->addItem('amount', form_def::factory('总金额(元)', '', form_def::text, [], ['data-rule' => 'required;digits']));
        $borrowerArr = \Prj\Data\Config::get('borrower');
        if($borrowerArr){
            var_log($borrowerArr);
            $borrowerArr+=[''=>''];
            $borrowerType = form_def::select;
        }else{
            $borrowerType = form_def::text;
        }
        $frm->addItem('borrowerId', form_def::factory('借款人ID', '', $borrowerType, $borrowerArr, ['data-rule' => 'required;digits']));
        $frm->addItem('startYmd', form_def::factory('开始日期', '', form_def::datepicker, [], ['data-rule' => 'required']))
            ->addItem('endYmd', form_def::factory('结束日期', '', form_def::datepicker, [], ['data-rule' => 'required']))
            ->addItem('_type', $type)
            ->addItem('_pkey_val_', $this->_request->get('_pkey_val_'));

        $frm->fillValues();
        $op = '添加资产';
        if ($frm->flgIsThisForm) //submit
        {
            //审核通过
            if ($type == 'check') {
                $asset  = \Prj\Data\Asset::getCopy($where['assetId']);
                $asset->load();
                if($asset->getField('status')!=\Prj\Consts\Asset::status_new)return $this->returnError('该资产已经审核过了');
                $this->_updateStatus(\Prj\Consts\Asset::status_ok);
                $this->closeAndReloadPage($this->tabname('check','assetc','manage'));
                return;
            }elseif($type == 'checkSale'){
                //return;
                return \Prj\Lang\Broker::getMsg('asset.check_error');
            }
            $where = \Prj\Misc\View::decodePkey($_REQUEST['_pkey_val_']);
            if (!empty($where)) {
                $frm->switchType(\Sooh\Base\Form\Broker::type_u);
            } else {
                $frm->switchType(\Sooh\Base\Form\Broker::type_c);
            }
            try {
                $fields = $frm->getFields();
                $Editor                         = "\\Prj\\WaresTpl\\" . $fields['viewTPL'] . '\\Editor';

                try{
                    //todo 字段过滤
                    $fields = $this->_makeFields($fields);
                    try{
                        //todo 数据合法性检查
                        if($fields['startYmd']>=$fields['endYmd']){
                           // return $this->returnError('结束日期不合法');
                            return $this->returnError(\Prj\Lang\Broker::getMsg('asset.startYmd_over_endYmd'));
                        }
                    }catch (\ErrorException $e){

                    }
                }catch (\ErrorException $e){
                    return $this->returnError($e->getMessage());
                }

                if ($frm->type() == \Sooh\Base\Form\Broker::type_c) //add
                {
                    $op                   = "新增";
                    $fields['remain']     = $fields['amount'];

                    $asset = \Prj\Data\Asset::add($fields);
                    if(empty($asset)){
                       // return $this->returnError('系统错误:添加资产失败');
                       return $this->returnError(\Prj\Lang\Broker::getMsg('asset.add_asset_error'));
                    }
                    $ext = $this->_request->get('ext');
                    //处理扩展数据
                    $introDisplay = $Editor::getDataFromForm($ext);
                    $asset->setField('introDisplay',json_encode($introDisplay));
                    $asset->update();
                } else { // update
                    $op   = '更新';
                    var_log($where,'where>>>>>>>');
                    $asset = \Prj\Data\Asset::getCopy($where['assetId']);
                    $asset->load();
                    //状态下修改权限
                    $assetDump = $asset->dump();
                    if ($assetDump['status'] == \Prj\Consts\Asset::status_ok) {
                        //$this->returnError("只有未生效的资产才能修改");
                        $this->returnError(\Prj\Lang\Broker::getMsg('asset.asset_not_effect_modify'));
                        return;
                    }

                    foreach ($fields as $k => $v) {
                        $asset->setField($k, $v);
                    }
                    $ext = $this->_request->get('ext');
                    //处理扩展数据
                    $introDisplay = $Editor::getDataFromForm($ext);
                    $asset->setField('introDisplay',json_encode($introDisplay));
                    $asset->setField('status', \Prj\Consts\Wares::status_new);

                    $asset->update();
                }
            } catch (\ErrorException $e) {
                $this->returnError($op . '失败：冲突，相关记录已经存在？');
            }

            $this->closeAndReloadPage($this->tabname('index'));
            $this->returnOK($op . '成功');
            return;
        }

        if ($frm->type() == \Sooh\Base\Form\Broker::type_u) //update show
        {

            $op = '修改';
            $asset = \Prj\Data\Asset::getCopy($where['assetId']);
            $asset->load();
            $fields = $asset->dump();
            $fields['amount']/=100;
            if(empty($editorTpl)){
                $Editor                         = "\\Prj\\WaresTpl\\" . $fields['viewTPL'] . '\\Editor';
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
            $Editor::$content         = $fields['introDisplay'];
            $this->_view->assign('_pkey_val_',$this->_request->get('_pkey_val_'));
            $this->_view->assign('editor',$Editor::getCopy());
        }else{
            $txtPath = __DIR__.'/../views/_inc/intro.www.phtml';
            $intro = [
                'a'=>'项目真是好项目啊',
                'b'=>[
                    'name'=>'张先生',
                    'married'=>'已婚',
                    'rootAddr'=>'上海市浦东新区',
                    'addr'=>'上海市浦东新区',
                    'reason'=>'做生意',
                ],
                'c'=>[
                    'addr'=>'上海市浦东新区',
                    'ymd'=>'20140101',
                    'price'=>'20000000',
                ],
                'd'=>[
                    'id'=>[
                        'name'=>'',
                        'img'=>[],
                    ],
                    'book'=>[
                        'name'=>'',
                        'img'=>[],
                    ],
                    'married'=>[
                        'name'=>'',
                        'img'=>[],
                    ],
                    'credit'=>[
                        'name'=>'',
                        'img'=>[],
                    ],
                    'inspect'=>[
                        'name'=>'',
                        'img'=>[],
                    ],
                    'contract'=>[
                        'name'=>'',
                        'img'=>[],
                    ],
                    'receipt'=>[
                        'name'=>'',
                        'img'=>[],
                    ],
                    'property'=>[
                        'name'=>'',
                        'img'=>[],
                    ]
                ]
            ];
            $Editor::$content = $intro;
            $this->_view->assign('editor',$Editor::getCopy());
        }

        //var_dump($fields);
        //die();
        $this->_view->assign('FormOp', $op);
        $this->_view->assign('type', $type);

        $this->_view->assign('_pkey_val_', $this->_request->get('_pkey_val_'));

    }

    public function deleteAction(){
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $where                          = \Prj\Misc\View::decodePkey($this->_request->get('_pkey_val_'));
        $asset  = \Prj\Data\Asset::getCopy($where['assetId']);
        $asset->load();
        if(!$asset->exists())return $this->returnError('资产不存在');
        if($asset->getField('status')==\Prj\Consts\Asset::status_ok)return $this->returnError('上线的资产无法删除');
        $asset->setField('status',\Prj\Consts\Asset::status_delete);
        try{
            $asset->update();
            return $this->returnOK('操作成功');
        }catch (\ErrorException $e){
            return $this->returnError('操作失败'.$e->getMessage());
        }
    }

    //驳回
    public function rejectAction(){
        $this->_updateStatus(\Prj\Consts\Asset::status_abandon);
        $this->closeAndReloadPage($this->tabname('check','assetc','manage'));
        $this->returnOK("操作完成");
    }

    //状态变更
    protected function _updateStatus($num)
    {
        $where = \Prj\Misc\View::decodePkey($this->_request->get('_pkey_val_'));
        $asset  = \Prj\Data\Asset::getCopy($where['assetId']);
        $asset->load();
        $fields = $asset->dump();
        try {
            $asset->setField('status', $num);
            $asset->update();
        } catch (\ErrorException $e) {
           // $this->returnError($fields['statusCode'] . '=>' . $num . ':数据库错误');
            $this->returnError($fields['statusCode'] . '=>' . $num .\Prj\Lang\Broker::getMsg('system.db_error'));
        }
        $this->returnOK("操作完成");
    }

    protected function _makeFields($fields){
        $fields['amount']*=100;
        $fields['startYmd'] and $fields['startYmd'] = date('Ymd',strtotime($fields['startYmd']));
        $fields['endYmd'] and $fields['endYmd'] = date('Ymd',strtotime($fields['endYmd']));
        return $fields;
    }
    
    /*
    public function edit1Action(){
        $where                          = \Prj\Misc\View::decodePkey($this->_request->get('_pkey_val_'));
        $type                           = $this->_request->get('_type');
        $frm                            = \Sooh\Base\Form\Broker::getCopy('default')
            ->init(\Sooh\Base\Tools::uri(), 'get', empty($where) ? \Sooh\Base\Form\Broker::type_c : \Sooh\Base\Form\Broker::type_u);

        $frm->addItem('assetName', form_def::factory('资产名称', '', form_def::text, [], ['data-rule' => 'required']))
            ->addItem('amount', form_def::factory('金额', '', form_def::text, [], ['data-rule' => 'required']))
            ->addItem('assetDesc', form_def::factory('描述', '', form_def::mulit, [], ['data-rule' => 'required']))
            ->addItem('amount', form_def::factory('金额', '', form_def::text, [], ['data-rule' => 'required']))
            ->addItem('startYmd', form_def::factory('开始日期', '', form_def::datepicker, [], ['data-rule' => 'required']))
            ->addItem('endYmd', form_def::factory('结束日期', '', form_def::datepicker, [], ['data-rule' => 'required']))
            ->addItem('_type', $type)
            ->addItem('_pkey_val_', $this->_request->get('_pkey_val_'));

        //todo 构造表单数据

        $frm->fillValues();
        //表单提交
        if ($frm->flgIsThisForm)
        {

            $where = \Prj\Misc\View::decodePkey($_REQUEST['_pkey_val_']);
            if (!empty($where)) {
                $frm->switchType(\Sooh\Base\Form\Broker::type_u);
            } else {
                $frm->switchType(\Sooh\Base\Form\Broker::type_c);
            }
            $op = '';
            try {
                $fields = $frm->getFields();
                try{
                    //todo 字段过滤
                    $fields['startYmd'] = date('Ymd',strtotime($fields['startYmd']));
                    $fields['endYmd'] = date('Ymd',strtotime($fields['endYmd']));
                    $fields['amount']*=100;
                    $fields['remain'] = $fields['amount'];
                    if(empty($fields['amount']))return $this->returnError('金额不能为空');
                    if($fields['startYmd']>$fields['endYmd'])return $this->returnError('结束日期不能小于起始日期');
                }catch (\ErrorException $e){
                    return $this->returnError($e->getMessage());
                }

                if ($frm->type() == \Sooh\Base\Form\Broker::type_c) //add
                {
                    $op                   = "新增";
                    if($asset = Asset::add($fields)){
                        try{
                            $asset->update();
                        }catch (\ErrorException $e){
                            return $this->returnError('新增失败'.$e->getMessage());
                        }
                    }else{
                        return $this->returnError('新增失败');
                    }
                    //todo 插入数据库
                } else { // update
                    $op   = '更新';
                    //todo 更新数据库
                }
            } catch (\ErrorException $e) {
                $this->returnError($op . '失败：冲突，相关记录已经存在？');
            }

            $this->closeAndReloadPage($this->tabname('index'));
            $this->returnOK($op . '成功');
            return;
        }

        if ($frm->type() == \Sooh\Base\Form\Broker::type_u) //update show
        {
            //todo 字段展示 设置item的value
            $asset = Asset::getCopy($where['assetId']);
            $asset->load();
            $arr = $asset->dump();
            $arr['amount']/=100;
            $arr['amount'] = sprintf("%.2f",$arr['amount']);
            $arr['startYmd'] = date('Y-m-d',strtotime($arr['startYmd'] ));
            $arr['endYmd'] = date('Y-m-d',strtotime($arr['endYmd'] ));
            foreach($frm->items as $k=>$v){
                if(in_array($k,['_type','_pkey_val_']))continue;
                $frm->items[$k]->value = $arr[$k];
                if($this->_request->get('_type')=='check')$frm->items[$k]->setinputDefault('constval');
            }
        }

        //var_dump($fields);
        //die();
        $this->_view->assign('FormOp', $op);
        $this->_view->assign('type', $type);
        $this->_view->assign('_pkey_val_', $this->_request->get('_pkey_val_'));
    }
    */
    
    
 /*
  * 
  * 详细
  * 
  * */
    public function detailAction(){
        $where = \Prj\Misc\View::decodePkey($this->_request->get('_pkey_val_'));
        var_log($where,'>>>>>>>>>>>>>>>>');
        $assetId=$where['assetId'];
        if(empty($assetId)){
            return $this->returnError(\Prj\Lang\Broker::getMsg('asset.asset_unexists'));
        }
        
        $formView = \Sooh\Base\Form\Broker::getCopy('default')->init(\Sooh\Base\Tools::uri('nullAction'), 'get',
            \Sooh\Base\Form\Broker::type_u);
        $formView->addItem('assetId', form_def::factory('资产ID', '', form_def::constval))
        ->addItem('assetName', form_def::factory('资产名称', '', form_def::constval))
        ->addItem('viewTPL', form_def::factory('产品类型', '', form_def::constval))
        ->addItem('amount', form_def::factory('总金额(单位元)', '', form_def::constval))
        ->addItem('remain', form_def::factory('剩余金额', '', form_def::constval))
        ->addItem('startYmd', form_def::factory('起始日期', '', form_def::constval))
        ->addItem('endYmd', form_def::factory('结束日期', '', form_def::constval))
        ->addItem('days', form_def::factory('天数', '', form_def::constval))
        ->addItem('status', form_def::factory('状态', '', form_def::constval))
        ->addItem('count', form_def::factory('关联标的数', '', form_def::constval))
        ->addItem('createTime', form_def::factory('创建时间', '', form_def::constval));
        $formView->fillValues();
        
        $asset=Asset::getCopy($assetId);
        $pkey=$asset->load();
        if($pkey=null){
            return $this->returnError(\Prj\Lang\Broker::getMsg('asset.asset_unexists'));
        }else{
            $ks=array_keys($formView->items);
            var_log($ks,'>>>>>>>>>>>>>>>>>>>>>>>>>>');
            foreach ($ks as $k){
                if($asset->exists($k)){
                    $assetId=$asset->getField('assetId');
                    $count=\Prj\Data\Wares::loopGetRecordsCount(['assetId'=>$assetId]);
                    //var_log($count,'>>>>>>>>>>>>>>>>>');
                    $viewTPL=$asset->getField('viewTPL');
                    $startYmd=strtotime(\Prj\Misc\View::fmtYmd($asset->getField('startYmd')));
                    //var_log($startYmd,'111111111111111111');
                    $endYmd=strtotime(\Prj\Misc\View::fmtYmd($asset->getField('endYmd')));
                   // var_log($endYmd,'2222222222222222222222222');
                    $status=$asset->getField('status');
                    if($k=='startYmd'||$k=='endYmd'){
                        $formView->item($k)->value = \Prj\Misc\View::fmtYmd($asset->getField($k));
                    }elseif($k=='createTime'){
                        $formView->item($k)->value = \Prj\Misc\View::fmtYmd($asset->getField($k),'time');
                    }elseif($k=='status'){
                        if($status=='1'){
                        $formView->item($k)->value='有效';
                        }elseif($status=='-1'){
                            $formView->item($k)->value='无效';
                        }elseif($status=='0'){
                            $formView->item($k)->value='等待审核';
                        }
                    }elseif($k=='viewTPL'){
                        if($viewTPL=='Std02'){
                        $formView->item($k)->value='房贷';
                        }elseif($viewTPL=='Std03'){
                            $formView->item($k)->value='车贷';
                        }
                    }
                    else{
                  $formView->item($k)->value=$asset->getField($k); 
                    } 
                }
                if($k=='days'){
                    $formView->item($k)->value=($endYmd-$startYmd)/86400;
                }elseif($k=='count'){
                    $formView->item($k)->value=$count;
                }
            }
        }
    }
    
    /**
     * 查看相关标的
     */
    public function checkViewAction(){
        
       $fieldsMap = array(
            'waresId'           => array('标的ID', '300'),
            'waresName'         => array('标的名称', '130'),
            'assetId'           =>array('资产ID','130'),
            'tags'         => array('标签', '60'),
            'vipLevel'         => array('vip等级', '60'),
            'interestStartType' => array('起息方式/还款方式','120'),
            'returnType'        => array('还款方式', '113'),
            'deadLine'          => array('期限', '35'),
            'dlUnit'            => array('单位', '200'),
            'yieldStatic'       => array('利率%', '52'),
            'yieldStaticAdd'    => array('活动加息', '57'),
            'shelfId'           => array('类型', '35'),
            'amount'            => array('总额(元)', '100'),
            'remain'            => array('余额(元)', '100'),
            'realRaise'            => array('实际募集(元)', '100'),
            'timeStartPlan'     => array('计划上架时间/计划还款时间', '220'),
            'timeEndReal'     => array('实际满标时间/实际转账时间','220'),
            'payYmd'     => array('满标转账时间','140'),
            'ymdPayPlan'        => array('计划还款时间', '92'),
            'statusCode'        => array('状态/备注', '165'),
            'payStatus'        => array('网关状态/订单号', '140'),
            'paySn'        => array('网关订单号', '100'),
            'exp'        => array('备注', '120'),
           'managementTrans'=>array('服务费','60'),
           'managementConfirm'=>array('手续费','60'),
        );
        
        $where= \Prj\Misc\View::decodePkey($this->_request->get('_pkey_val_'));
        $asset=Asset::getCopy($where['assetId']);
        $asset->load();
        $assetId=$asset->getField('assetId');
        
//        $wares= \Prj\Data\Wares::getCopy();
//       $ware=$wares->loopFindRecords(['assetId'=>$where['assetId']]);
   //   var_log(\Sooh\DB\Broker::lastCmd(),'sql>>>>>>>>>>>>>>>>>>>>>');
        
      $pageid           = $this->_request->get('pageId', 1) - 0;
      $isDownloadEXCEL  = $this->_request->get('__EXCEL__');
      $pager            = new \Sooh\DB\Pager(10, $this->pageSizeEnum, false);
      $pager->page_size = $this->_request->get('pageSize', 50);
      $ids = $this->_request->get('ids');
      
      $frm = \Sooh\Base\Form\Broker::getCopy('default')
      ->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_s);
      $frm->addItem('_waresname_lk', form_def::factory('标的名称', '', form_def::text))
          ->addItem('_assetId_eq', form_def::factory('资产Id','', form_def::text))
          ->addItem('pageid', $pageid)
          ->addItem('pagesize', $pager->page_size);
      
       $frm->fillValues();
        if ($frm->flgIsThisForm) {
            $where = $frm->getWhere();
        } else {
            $where = array();
        }
       
     
        
        foreach ($fieldsMap as $k => $v) {
            $headers[$v[0]] =$v[1];
            $fields[]       = $k;
        }
        
        unset($headers['单位']);
        unset($headers['标的ID']);
        unset($headers['还款方式']);
        unset($headers['计划还款时间']);
        unset($headers['满标转账时间']);
        unset($headers['备注']);
        unset($headers['网关订单号']);
        
        $ware= \Prj\Data\Wares::getCopy('');
      //  $ware=$ware->loopFindRecords(['assetId'=>$where['assetId']]);
        $records = $ware->db()->getRecords($ware->tbname(), $fields, ['assetId'=>$assetId], 'sort statusCode rsort timeStartPlan', $pager->page_size, $pager->rsFrom());
        var_log(\Sooh\DB\Broker::lastCmd(),'sql>>>>>>>>>>>>>>>>>>>>>');
        //var_log($records,'>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>');
        $new        = array();
        
        if (!empty($records)) {
            foreach ($records as $k => $v) {
                 
                $_pkey_val_ = \Prj\Misc\View::encodePkey(array('waresId' => $v['waresId']));
               // if (!$isDownloadEXCEL) $v['_pkey_val_'] = $_pkey_val_;
                $v['shelfId'] = \Prj\Consts\Wares::$shilfIdName[$v['shelfId']];
                $v['deadLine'] .= $v['dlUnit'];
                $v['amount'] = sprintf("%.2f", $v['amount']/100);
                $v['remain'] = sprintf("%.2f", $v['remain']/100);
                $v['realRaise'] = sprintf("%.2f", $v['realRaise']/100);
                $v['yieldStatic']=sprintf("%.2f", $v['yieldStatic']*100);
                $v['yieldStaticAdd']=sprintf("%.2f", $v['yieldStaticAdd']*100);
                $v['interestStartType'] = \Prj\Consts\InterestStart::$enum[$v['interestStartType']];
                $v['returnType'] = \Prj\Consts\ReturnType::$enum[$v['returnType']];
                $v['timeStartPlan'] = $v['timeStartPlan']?\Prj\Misc\View::fmtYmd($v['timeStartPlan'],'time'):'';
                $v['payYmd'] = $v['payYmd']?\Prj\Misc\View::fmtYmd($v['payYmd'],'time'):'';
                $v['timeEndReal'] = $v['timeEndReal']?\Prj\Misc\View::fmtYmd($v['timeEndReal']):'';
                $v['ymdPayPlan'] = $v['ymdPayPlan']?\Prj\Misc\View::fmtYmd($v['ymdPayPlan'],'time'):'';
                $v['realStatusCode'] = $v['statusCode'];
                $v['_statusCode'] = $v['statusCode'];
                $v['statusCode'] = \Prj\Consts\Wares::$enum[$v['statusCode']];
                $v['_payStatus'] = $v['payStatus'];
                $v['payStatus'] = \Prj\Consts\PayGW::$status[$v['payStatus']]?\Prj\Consts\PayGW::$status[$v['payStatus']]:'等待操作';
                $v['paySn'] = $v['paySn']?$v['paySn']:'';
                unset($v['dlUnit']);
                unset($v['waresId']);
                unset($v['realStatusCode']);
                $new[$_pkey_val_]=$v;
               // var_log($new,'>>>>>>>>>>>>>>>>>>>>>');
               // $new[$_pkey_val_] = $v;
                //$v['interestStartType/returnType']= '111';
            }
           
        }
       // var_log($new,'>>>>>11111111111');
        $records = $new;
       // var_log($records,'>>>>>>>>>>>>>>>>>>>');
        
       $replace=array();
         foreach ($records as $k=>$v){
             $v['interestStartType']=$v['interestStartType'].'<br>'.$v['returnType'];
             $v['timeStartPlan']=$v['timeStartPlan'].'/'.$v['ymdPayPlan'];
             if(!empty($v['timeEndReal'])&&!empty($v['payYmd'])){
             $v['timeEndReal']=$v['timeEndReal'].'/'.$v['payYmd'];
             }elseif(!empty($v['timeEndReal'])&&empty($v['payYmd'])){
                 $v['timeEndReal']=$v['timeEndReal'];
             }
             $v['statusCode']=$v['statusCode'].'<br>'.$v['exp'];
             $v['payStatus']=$v['payStatus'].'<br>'.$v['paySn'];
             unset($v['paySn']);
             unset($v['exp']);
             unset($v['payYmd']);
             unset($v['ymdPayPlan']);
             unset($v['returnType']);
             $replace[]=$v;
         }
         $records=$replace;
        
       // var_log($records,'>>>>>>>>>>>>>>>');
        $this->_view->assign('headers', $headers);
        $this->_view->assign('records', $records);
        $this->_view->assign('pager', $pager);
        
    }
    
    public function splitAction(){
        $this->closeAndReloadPage();
        $where                          = \Prj\Misc\View::decodePkey($this->_request->get('_pkey_val_'));
        $type                           = $this->_request->get('_type');
        $asset = Asset::getCopy($where['assetId']);
        $asset->load();
        $r = $asset->dump();
        $r['amount']/=100;
        $r['amount'] = sprintf('%.2f',$r['amount']);
        $r['remain']/=100;
        $r['remain'] = sprintf('%.2f',$r['remain']);
        $r['startYmd'] = date('Y-m-d',strtotime($r['startYmd']));
        $r['endYmd'] = date('Y-m-d',strtotime($r['endYmd']));
        $days = (strtotime($r['endYmd'])-strtotime($r['startYmd']))/86400;
        $frm                            = \Sooh\Base\Form\Broker::getCopy('default')
            ->init(\Sooh\Base\Tools::uri(), 'get', empty($where) ? \Sooh\Base\Form\Broker::type_c : \Sooh\Base\Form\Broker::type_u);
        $frm->addItem('assetId', form_def::factory('资产ID', $r['assetId'], form_def::constval, [], ['data-rule' => 'required']))
            ->addItem('assetName', form_def::factory('资产名称', $r['assetName'], form_def::constval, [], ['data-rule' => 'required']))
            ->addItem('borrowerId', form_def::factory('借款人ID', $r['borrowerId'], form_def::constval, [], ['data-rule' => 'required']))
            ->addItem('amount', form_def::factory('金额', $r['remain'], form_def::constval, [], ['data-rule' => 'required']))
            ->addItem('assetDesc', form_def::factory('描述', $r['introDisplay']['a'], form_def::constval, [], ['data-rule' => 'required']))
            ->addItem('startYmd', form_def::factory('开始日期', $r['startYmd'], form_def::constval, [], ['data-rule' => 'required']))
            ->addItem('endYmd', form_def::factory('结束日期', $r['endYmd'], form_def::constval, [], ['data-rule' => 'required']))
            ->addItem('days', form_def::factory('天数', $days, form_def::constval, [], ['data-rule' => 'required']))
            ->addItem('waresName', form_def::factory('标的名称',  $r['assetName'], form_def::text, [], ['data-rule' => 'required']))
            ->addItem('waresSN', form_def::factory('第几期', '24', form_def::text, [], ['data-rule' => 'required']))
            ->addItem('pers', form_def::factory('时间分段', '3', form_def::text, [], ['data-rule' => 'required']))
            ->addItem('perAmount', form_def::factory('单个金额(单位元)', '100000', form_def::text, [], ['data-rule' => 'required']))

            ->addItem('introDisplay', '')
            ->addItem('viewTPL', '')
            ->addItem('_type', $type)
            ->addItem('_pkey_val_', $this->_request->get('_pkey_val_'));
        $txtPath = __DIR__.'/../views/_inc/intro.www.phtml';
        $Editor = "\\Prj\\WaresTpl\\Std02\\Editor";

        $Editor::$content = $r['introDisplay'];
        //todo 构造表单数据

        $frm->fillValues();
        //表单提交
        if ($frm->flgIsThisForm)
        {
            $where = \Prj\Misc\View::decodePkey($_REQUEST['_pkey_val_']);
            if (!empty($where)) {
                $frm->switchType(\Sooh\Base\Form\Broker::type_u);
            } else {
                $frm->switchType(\Sooh\Base\Form\Broker::type_c);
            }
            $op = '';
            $fields = $frm->getFields();

            //表单验证
            if($fields['pers']>$days){
                //return $this->returnError('分段不能小于天数');
               return  $this->returnError(\Prj\Lang\Broker::getMsg('asset.pers_error'));
            }
            if($fields['perAmount']>$r['remain']){
               // return $this->returnError('每份金额不能大于总金额');
                return  $this->returnError(\Prj\Lang\Broker::getMsg('asset.perAmount_error'));
            }
            //
            $fields['shelfId'] = $fields['viewTPL']=='Std02'?\Prj\Consts\Wares::shelf_static:\Prj\Consts\Wares::shelf_static_float;
            $fields['viewTPL'] = $r['viewTPL'];
            $fields['introDisplay'] = $r['introDisplay'];
            $fields['amount']*=100;
            $fields['perAmount']*=100;
            $this->closeAndReloadPage();
            $this->_view->assign('data',$fields);
            $this->returnOK($op . '成功');
            return;
        }

        //var_dump($fields);
        //die();
        $this->_view->assign('FormOp', $op);
        $this->_view->assign('type', $type);
        $this->_view->assign('editor', $Editor::getCopy());
        $this->_view->assign('_pkey_val_', $this->_request->get('_pkey_val_'));
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

    public function splitResultAction(){
        $data = $this->_request->get('data');
        $wares = $this->_split($data);
        $headers = [
            'waresName'=>'标的名称',
            'waresSN'=>'期数',
            'deadLine'=>'期限',
            'dlUnit'=>'单位',
            'amount'=>'金额',
            'timeStartPlan'=>'计划上架日期',
            'ymdPayPlan'=>'计划还款日期',
            'assetId'=>'资产ID'
        ];
        $newWares = [];

        foreach($wares as $kk=>$vv){
            foreach($headers as $k=>$v){
                $tmp[$k] = $vv[$k];
            }
            //过滤
            $tmp['amount']/=100;
            $tmp['amount']=sprintf('%.2f',$tmp['amount']);
            $tmp['timeStartPlan']=date('Y-m-d H:i:s',strtotime($tmp['timeStartPlan']));
            $tmp['ymdPayPlan']=date('Y-m-d',$tmp['ymdPayPlan']);
            $newWares[] = $tmp;
        }

        $this->_view->assign('wares',$newWares);
        $this->_view->assign('headers',$headers);
        $this->_view->assign('data',$data);
    }

    /**
     * 拆分入库
     */
    public function doSplitAction(){
        $this->closeAndReloadPage();
        $data = $this->_request->get('data');
        $assetId = $data['assetId'];
        //$borrowerId = $data['borrowerId'];
        $count = \Prj\Data\Wares::loopGetRecordsCount(['assetId'=>$assetId]);
        var_log($count,'已经拆分了>>>>>>>>');
        if(!empty($count))return $this->returnError('已经拆分成'.$count.'个标,请勿重复操作！');
        $wares = $this->_split($data);
        foreach($wares as $k=>$v){
            unset($v['days']);
            //$v['borrowerId'] = $borrowerId;
            $ware = \Prj\Data\Wares::createWare($v);
            try{
                $ware->update();
            }catch (\ErrorException $e){
                return $this->returnError($e->getMessage());
            }
        }
        return $this->returnOK('ok');
    }

    protected function _split($data){
        $wares = [];
        $days = floor($data['days']/$data['pers']);
        $waresSn = $data['waresSN'];
        $startYmd = 0;
        $endYmd = 0;
        $amountCut = floor($data['amount']/$data['perAmount'])?floor($data['amount']/$data['perAmount']):1;

        for($i=0;$i<$data['pers'];$i++){
            $startYmd=empty($startYmd)?strtotime($data['startYmd']):$endYmd;
            $endYmd = $i==$data['pers']-1?strtotime($data['endYmd']):strtotime('+'.$days.'days',$startYmd);

            for($k=0;$k<$amountCut;$k++){
                $temp['waresName'] = $data['waresName'];
                $temp['waresSN'] = $waresSn;
                $temp['dlUnit'] = '天';
                $temp['amount'] = $k==$amountCut-1?$data['amount']-$data['perAmount']*($amountCut-1):$data['perAmount'];
                $temp['timeStartPlan'] = date('Ymd100000',$startYmd);
                $temp['ymdPayPlan'] = date('Ymd',$endYmd);
                $temp['days'] = floor((strtotime($temp['ymdPayPlan'])-strtotime($temp['timeStartPlan']))/86400);
                $temp['deadLine'] = $temp['days'];
                $temp['assetId'] = $data['assetId'];
                $temp['ymdPayPlan'] = strtotime($temp['ymdPayPlan']);
                $temp['viewTPL'] = $data['viewTPL'];
                $temp['introDisplay'] = $data['introDisplay'];
                $temp['borrowerId'] = $data['borrowerId'];
                $temp['shelfId'] = $data['shelfId'];
                $temp['interestStartType'] = key(\Prj\Consts\InterestStart::$enum);
                $wares[] = $temp;
                $waresSn++;
            }
        }
        return $wares;
    }
}