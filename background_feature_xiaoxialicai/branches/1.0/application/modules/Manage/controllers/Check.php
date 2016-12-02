<?php

/**
 * Created by PhpStorm.
 * User: tang.gaohang
 * Date: 2016/2/16
 * Time: 17:48
 */
use Sooh\Base\Form\Item as form_def;

/**
 * 对账
 */
class CheckController extends \Prj\ManagerCtrl {

    protected function DB(){
        return \Sooh\DB\Broker::getInstance();
    }

    protected $payName = '新浪';

    protected $dateInit = '20160114';

    protected $header;

    public function init(){
        parent::init();
        $this->dateInit = date('Ymd',strtotime("-1 days"));
    }

    public function dayCheckOverviewAction(){
        //\Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $ymd = $this->_request->get('ymd',$this->dateInit);
        $this->header = [
            'ymd'=>['核对日期','100'],
            'payCorp'=>['名称','100'],
            'recharges'=>['充值','100'],
            'withdraw'=>['提现','100'],
            'buy'=>['投标','100'],
            'loan'=>['放款（借款人）','100'],
            'paysplit'=>['回款（投资人）','100'],
            'payback'=>['还款（借款人）','100'],
            //'giftpay'=>['优惠券','100'],
            'manage'=>['管理费','100'],
        ];
        //$result = $this->db->execCustom(['sql'=>$sql]);
        //$rs = $this->db->fetchAssocThenFree($result);
        //$dayBuySql = 'select sum(amount) as amount ,sum(amountLocal) as amountLocal,ymd from tb_dayBuy GROUP BY ymd ORDER BY ymd desc';

        //配置搜索项
        $frm = \Sooh\Base\Form\Broker::getCopy('default')->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_s);
        $frm->addItem('_ymd_eq', form_def::factory('日期', $ymd, form_def::datepicker));
            //->addItem('pageId', $pageid)
            //->addItem('pageSize', $this->pager->page_size);
        $frm->fillValues();
        if ($frm->flgIsThisForm) {
            $where = $frm->getWhere();
            if(!empty($where['ymd=']))$where['ymd='] = date('Ymd',strtotime($where['ymd=']));
        } else {
            $where = array();
        }
        if($where['ymd='])$ymd = $where['ymd='];
        $data['buy'] = $dayBuy = $this->getSum('Buy');
        $data['loan'] = $dayLoan = $this->getSum('Loan');
        $data['payback'] = $dayLoan = $this->getSum('Payback');
        $data['paysplit'] = $dayLoan = $this->getSum('Paysplit','interest');
        $data['recharges'] = $dayLoan = $this->getSum('Recharges');
        $data['withdraw'] = $dayLoan = $this->getSum('Withdraw');
        $data['manage'] = $dayLoan = $this->getSum('Manage');
        $rs = $this->dataEncode($data,$ymd);
        var_log($data['paysplit'],'rs >>> ');
        foreach($this->header as $k=>$v){
            $header[$v[0]] = $v[1];
        }
        $this->_view->assign('header',$header);
        //$this->_view->assign('data',$data);
        $this->_view->assign('rs',$rs);
    }

    protected function dataEncode($data,$ymd){
        $rs = [];
        for($i=0;$i<10;$i++){
            $tmpYmd = date('Ymd',strtotime('- '.$i.' days',strtotime($ymd)));
            $header = $this->header;
            unset($header['ymd']);
            unset($header['payCorp']);
            $rs[$i]['ymd'] = $tmpYmd;
            $rs[$i]['payCorp'] = [$this->payName,'平台'];
            foreach($header as $k=>$v){
                $rs[$i][$k] = [$data[$k][$tmpYmd]['amount']/100+$data[$k][$tmpYmd]['interest']/100,$data[$k][$tmpYmd]['amountLocal']/100+$data[$k][$tmpYmd]['interestLocal']/100];
                $rs[$i][$k]['url'] = \Sooh\Base\Tools::uri(['ymd'=>$tmpYmd],'day'.ucfirst($k));
                $rs[$i][$k]['notice'] = $this->header[$k][0];
                if($k=='paysplit'){
                    var_log($data[$k][$tmpYmd],'data >>> ');
                }
            }
        }
        return $rs;
    }

    protected function getSum($name , $arg1 = '' ){
        $tbname = 'tb_day'.$name;
        $db = \Sooh\DB\Broker::getInstance();
        $ext = $arg1?'sum('.$arg1.') as '.$arg1.' ,sum('.$arg1.'Local) as '.$arg1.'Local,':'';
        $rs = $db->getRecords($tbname,' "'.$name.'" as type, sum(amount) as amount ,sum(amountLocal) as amountLocal,'.$ext.'ymd',[],'group ymd rsort ymd');
        var_log(\Sooh\DB\Broker::lastCmd(),'sql >>> ');
        $tmp = [];
        if(empty($rs)){
            return [];
        }else{
            foreach($rs as $v){
                $tmp[$v['ymd']] = $v;
            }
        }
        return $tmp;
    }
    /**
     * 充值流水（对账）
     */
    public function dayRechargesAction(){
        //\Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $isDownloadEXCEL = $this->_request->get('__EXCEL__');
        $ymd = $this->_request->get('ymd',$this->dateInit);
        $sum = [];//统计
        //配置表格
        $fieldsMapArr = array(
            'sn'    => ['流水号', 'auto'],
            'ymd'    => ['日期', 'auto'],
            'paycorp'    => ['来源', 'auto'],
            'userId'    => ['用户ID['.$this->payName.']', 'auto'],
            'userIdLocal'    => ['用户ID[平台]', 'auto'],
            'amount'    => ['金额['.$this->payName.']', 'auto'],
            'amountLocal'    => ['金额[平台]', 'auto'],
            'diff'    => ['差异', 'auto'],
            'checkk'    => ['审核', 'auto'],
            'exp'    => ['备注', 'auto'],
        );

        //配置分页
        $pageid = $this->_request->get('pageId', 1) - 0;
        $pager  = new \Sooh\DB\Pager($this->_request->get('pageSize', 100), $this->pageSizeEnum, false);
        $pager->init(-1,$pageid);

        //配置搜索项
        $frm = \Sooh\Base\Form\Broker::getCopy('default')->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_s);
        $frm->addItem('_sn_eq', form_def::factory('流水号', '', form_def::text))
            ->addItem('_ymd_eq', form_def::factory('日期', $ymd, form_def::datepicker))
            ->addItem('pageId', $pageid)
            ->addItem('pageSize', $this->pager->page_size);
        $frm->fillValues();
        if ($frm->flgIsThisForm) {
            $where = $frm->getWhere();
            if(!empty($where['ymd=']))$where['ymd='] = date('Ymd',strtotime($where['ymd=']));
        } else {
            $where = array();
        }
        if(empty($where['ymd=']))$where['ymd='] = $ymd;
        //合并表单的查询条件
        $search = \Prj\Misc\View::decodePkey($this->_request->get('where'));
        $where = array_merge($search?$search:[],$where);
        //拉取记录
        var_log($where,'查询条件>>>>>>>>>>>>>>>>>>');
        $pager->total = $this->DB()->getRecordCount('tb_dayRecharges',$where);
        $total = $this->DB()->getRecord('tb_dayRecharges','sum(amount) as amount,sum(amountLocal) as amountLocal',$where,'rsort diff');
        $rs = $this->DB()->getRecords('tb_dayRecharges','*',$where,'rsort diff',$pager->page_size,$pager->rsFrom());

        //格式配置
        $tempArr = array();
        $newArr  = array();
        foreach ($fieldsMapArr as $kk => $vv) {
            $header[$vv[0]] = '';
            $sum[$kk] = '';
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
            isset($tempArr['amount']) && $tempArr['amount']/=100;
            isset($tempArr['amountLocal']) && $tempArr['amountLocal']/=100;
            isset($tempArr['ymd']) && $tempArr['ymd'] = date('Y-m-d',strtotime($tempArr['ymd']));
            isset($tempArr['paycorp']) && $tempArr['paycorp'] = \Prj\Consts\PayGW::$payCorp[$tempArr['paycorp']];
            isset($tempArr['checkk']) && $tempArr['checkk'] = $tempArr['checkk']?'已确认':'未处理';
            $tempArr['diff'] = \Prj\Misc\View::checkDiff($tempArr['diff']);
            //===

            $newArr[] = $tempArr;
        }

        //todo 数据统计
        $sum['amount']=$total['amount']/100;
        $sum['amountLocal']=$total['amountLocal']/100;
        //===

        $rs = $newArr;
        if($isDownloadEXCEL)return $this->downEXCEL($rs, array_keys($header),null,true);
        //输出
        $this->_view->assign('sum',$sum);
        $this->_view->assign('rs', $rs);
        $this->_view->assign('header', $header);
        $this->_view->assign('pager', $pager);
        $this->_view->assign('where', \Prj\Misc\View::encodePkey($where));
    }

    public function doCheckAction(){
        //\Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $map = [
            \Prj\Consts\OrderType::investment=>'DayBuy',
            \Prj\Consts\OrderType::loan=>'DayLoan',
            \Prj\Consts\OrderType::payback=>'DayPayback',
            \Prj\Consts\OrderType::paysplit=>'DayPaysplit',
            \Prj\Consts\OrderType::recharges=>'DayRecharges',
            \Prj\Consts\OrderType::withdraw=>'DayWithdraw',
            'DayManage'=>'DayManage',
        ];
        $where                          = \Prj\Misc\View::decodePkey($this->_request->get('_pkey_val_'));
        var_log($where,'where >>> ');
        $type                           = $this->_request->get('_type');
        $frm                            = \Sooh\Base\Form\Broker::getCopy('default')
            ->init(\Sooh\Base\Tools::uri(), 'get', empty($where) ? \Sooh\Base\Form\Broker::type_c : \Sooh\Base\Form\Broker::type_u);

        $frm->addItem('sn', form_def::factory('流水ID', '', form_def::constval, [], []))
            ->addItem('checkResult',form_def::factory('审核结果',0,form_def::select,['0'=>'未通过','1'=>'通过']))
            ->addItem('exp',form_def::factory('说明','',form_def::mulit,[]))
            ->addItem('_type', $type)
            ->addItem('_pkey_val_', $this->_request->get('_pkey_val_'));

        //todo 构造表单数据

        $frm->fillValues();
        //表单提交
        $class = '\Prj\Check\\'.$map[$where['ordersType']];
        var_log($class,'class>>>');
        $rs = $class::getDataFromDB(['sn'=>$where['id']])[0];
        var_log($rs,'rs>>>>>>>>>>>');
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
                }catch (\ErrorException $e){
                    return $this->returnError($e->getMessage());
                }

                if ($frm->type() == \Sooh\Base\Form\Broker::type_c) //add
                {
                    $op                   = "新增";
                    //todo 插入数据库
                } else { // update
                    $op   = '更新';
                    //todo 更新数据库
                    try{
                        $ret = $class::doCheck($where['id'],$fields['checkResult'],$fields['exp']);
                    }catch (\ErrorException $e){
                        return $this->returnError($e->getMessage());
                    }
                    if(!$ret){
                        return $this->returnError('操作失败');
                    }
                }
            } catch (\ErrorException $e) {
                return $this->returnError($op . '失败：冲突，相关记录已经存在？');
            }

            $this->closeAndReloadPage($this->tabname('index'));
            $this->returnOK($op . '成功');
            return;
        }

        if ($frm->type() == \Sooh\Base\Form\Broker::type_u) //update show
        {
            //todo 字段展示 设置item的value
            if(!$rs)$rs = [];
            foreach($frm->items as $k=>$v){
                if(array_key_exists($k,$rs))$frm->items[$k]->value = $rs[$k];
            }
        }

        //var_dump($fields);
        //die();
        $this->_view->assign('FormOp', $op = '添加标的');
        $this->_view->assign('type', $type);
        $this->_view->assign('_pkey_val_', $this->_request->get('_pkey_val_'));
    }

    /**
     * 提现流水（对账）
     */
    public function dayWithdrawAction(){
        //\Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $isDownloadEXCEL = $this->_request->get('__EXCEL__');
        $ymd = $this->_request->get('ymd',$this->dateInit);
        $sum = [];//统计
        //配置表格
        $fieldsMapArr = array(
            'sn'    => ['流水号', 'auto'],
            'ymd'    => ['日期', 'auto'],
            'paycorp'    => ['来源', 'auto'],
            'userId'    => ['用户ID['.$this->payName.']', 'auto'],
            'userIdLocal'    => ['用户ID[平台]', 'auto'],
            'amount'    => ['金额['.$this->payName.']', 'auto'],
            'amountLocal'    => ['金额[平台]', 'auto'],
            'poundage'    => ['手续费['.$this->payName.']', 'auto'],
            'poundageLocal'    => ['手续费[平台]', 'auto'],
            'diff'    => ['差异', 'auto'],
            'checkk'    => ['审核', 'auto'],
            'exp'    => ['备注', 'auto'],
        );

        //配置分页
        $pageid = $this->_request->get('pageId', 1) - 0;
        $pager  = new \Sooh\DB\Pager($this->_request->get('pageSize', 50), $this->pageSizeEnum, false);
        $pager->init(-1,$pageid);

        //配置搜索项
        $frm = \Sooh\Base\Form\Broker::getCopy('default')->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_s);
        $frm->addItem('_sn_eq', form_def::factory('流水号', '', form_def::text))
            ->addItem('_ymd_eq', form_def::factory('日期', $ymd, form_def::datepicker))
            ->addItem('pageId', $pageid)
            ->addItem('pageSize', $this->pager->page_size);
        $frm->fillValues();
        if ($frm->flgIsThisForm) {
            $where = $frm->getWhere();
            if(!empty($where['ymd=']))$where['ymd='] = date('Ymd',strtotime($where['ymd=']));
        } else {
            $where = array();
        }
        if(empty($where['ymd=']))$where['ymd='] = $ymd;
        //合并表单的查询条件
        $search = \Prj\Misc\View::decodePkey($this->_request->get('where'));
        $where = array_merge($search?$search:[],$where);
        //拉取记录
        var_log($where,'查询条件>>>>>>>>>>>>>>>>>>');
        $pager->total = $this->DB()->getRecordCount('tb_dayWithdraw',$where);
        $total = $this->DB()->getRecord('tb_dayWithdraw','sum(amount) as amount,sum(amountLocal) as amountLocal,sum(poundage) as poundage,sum(poundageLocal) as poundageLocal',$where,'rsort diff');
        $rs = $this->DB()->getRecords('tb_dayWithdraw','*',$where,'rsort diff',$pager->page_size,$pager->rsFrom());

        //格式配置
        $tempArr = array();
        $newArr  = array();
        foreach ($fieldsMapArr as $kk => $vv) {
            $header[$vv[0]] = '';
            $sum[$kk] = '';//统计
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
            isset($tempArr['amount']) && $tempArr['amount']/=100;
            isset($tempArr['amountLocal']) && $tempArr['amountLocal']/=100;
            isset($tempArr['poundage']) && $tempArr['poundage']/=100;
            isset($tempArr['poundageLocal']) && $tempArr['poundageLocal']/=100;
            isset($tempArr['paycorp']) && $tempArr['paycorp'] = \Prj\Consts\PayGW::$payCorp[$tempArr['paycorp']];
            isset($tempArr['ymd']) && $tempArr['ymd'] = date('Y-m-d',strtotime($tempArr['ymd']));
            isset($tempArr['checkk']) && $tempArr['checkk'] = $tempArr['checkk']?'已确认':'未处理';
            $tempArr['diff'] = \Prj\Misc\View::checkDiff($tempArr['diff']);
            //===

            $newArr[] = $tempArr;
        }

        //todo 数据统计
        $sum['amount']=$total['amount']/100;
        $sum['amountLocal']=$total['amountLocal']/100;
        $sum['poundage']=$total['poundage']/100;
        $sum['poundageLocal']=$total['poundageLocal']/100;
        //===

        $rs = $newArr;
        if($isDownloadEXCEL)return $this->downEXCEL($rs, array_keys($header),null,true);
        //输出
        $this->_view->assign('sum',$sum);
        $this->_view->assign('rs', $rs);
        $this->_view->assign('header', $header);
        $this->_view->assign('pager', $pager);
        $this->_view->assign('where', \Prj\Misc\View::encodePkey($where));
    }
    /**
     * 购买（对账）
     */
    public function dayBuyAction(){
        //\Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $isDownloadEXCEL = $this->_request->get('__EXCEL__');
        $ymd = $this->_request->get('ymd',$this->dateInit);
        $sum = [];//统计
        //配置表格
        $fieldsMapArr = array(
            'sn'    => ['流水号', 'auto'],
            'ymd'    => ['日期', 'auto'],
            'paycorp'    => ['来源', 'auto'],
            'waresId'    => ['标的ID['.$this->payName.']', 'auto'],
            'waresIdLocal'    => ['标的ID[平台]', 'auto'],
            'userId'    => ['用户ID['.$this->payName.']', 'auto'],
            'userIdLocal'    => ['用户ID[平台]', 'auto'],
            'amount'    => ['金额['.$this->payName.']', 'auto'],
            'amountLocal'    => ['金额[平台]', 'auto'],
            'amountExtra'    => ['优惠券['.$this->payName.']', 'auto'],
            'amountExtraLocal'    => ['优惠券[平台]', 'auto'],
            'diff'    => ['差异', 'auto'],
            'checkk'    => ['审核', 'auto'],
            'exp'    => ['备注', 'auto'],
        );

        //配置分页
        $pageid = $this->_request->get('pageId', 1) - 0;
        $pager  = new \Sooh\DB\Pager($this->_request->get('pageSize', 100), $this->pageSizeEnum, false);
        $pager->init(-1,$pageid);

        //配置搜索项
        $frm = \Sooh\Base\Form\Broker::getCopy('default')->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_s);
        $frm->addItem('_sn_eq', form_def::factory('流水号', '', form_def::text))
            ->addItem('_ymd_eq', form_def::factory('日期', $ymd, form_def::datepicker))
            ->addItem('pageId', $pageid)
            ->addItem('pageSize', $this->pager->page_size);
        $frm->fillValues();
        if ($frm->flgIsThisForm) {
            $where = $frm->getWhere();
            if(!empty($where['ymd=']))$where['ymd='] = date('Ymd',strtotime($where['ymd=']));
        } else {
            $where = array();
        }
        if(empty($where['ymd=']))$where['ymd='] = $ymd;
        //合并表单的查询条件
        $search = \Prj\Misc\View::decodePkey($this->_request->get('where'));
        $where = array_merge($search?$search:[],$where);
        //拉取记录
        var_log($where,'查询条件>>>>>>>>>>>>>>>>>>');
        $pager->total = $this->DB()->getRecordCount('tb_dayBuy',$where);
        $total = $this->DB()->getRecord('tb_dayBuy','sum(amount) as amount,sum(amountLocal) as amountLocal,sum(amountExtra) as amountExtra,sum(amountExtraLocal) as amountExtraLocal',$where,'rsort diff');
        $rs = $this->DB()->getRecords('tb_dayBuy','*',$where,'rsort diff',$pager->page_size,$pager->rsFrom());

        //格式配置
        $tempArr = array();
        $newArr  = array();
        foreach ($fieldsMapArr as $kk => $vv) {
            $header[$vv[0]] = '';
            $sum[$kk] = '';
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
            isset($tempArr['amount']) && $tempArr['amount']/=100;
            isset($tempArr['amountLocal']) && $tempArr['amountLocal']/=100;
            isset($tempArr['amountExtra']) && $tempArr['amountExtra']/=100;
            isset($tempArr['amountExtraLocal']) && $tempArr['amountExtraLocal']/=100;
            isset($tempArr['paycorp']) && $tempArr['paycorp'] = \Prj\Consts\PayGW::$payCorp[$tempArr['paycorp']];
            isset($tempArr['ymd']) && $tempArr['ymd'] = date('Y-m-d',strtotime($tempArr['ymd']));
            isset($tempArr['checkk']) && $tempArr['checkk'] = $tempArr['checkk']?'已确认':'未处理';
            $tempArr['diff'] = \Prj\Misc\View::checkDiff($tempArr['diff']);
            //===

            $newArr[] = $tempArr;
        }

        //todo 数据统计

        $sum['amount']=$total['amount']/100;
        $sum['amountLocal']=$total['amountLocal']/100;
        $sum['amountExtra']=$total['amountExtra']/100;
        $sum['amountExtraLocal']=$total['amountExtraLocal']/100;

        //===

        $rs = $newArr;
        if($isDownloadEXCEL)return $this->downEXCEL($rs, array_keys($header),null,true);
        //输出
        $this->_view->assign('sum',$sum);
        $this->_view->assign('rs', $rs);
        $this->_view->assign('header', $header);
        $this->_view->assign('pager', $pager);
        $this->_view->assign('where', \Prj\Misc\View::encodePkey($where));
    }
    /**
     * 放款（对账）
     */
    public function dayLoanAction(){
        //\Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $isDownloadEXCEL = $this->_request->get('__EXCEL__');
        $ymd = $this->_request->get('ymd',$this->dateInit);
        $sum = [];//统计
        //配置表格
        $fieldsMapArr = array(
            'sn'    => ['流水号', 'auto'],
            'ymd'    => ['日期', 'auto'],
            'paycorp'    => ['来源', 'auto'],
            'waresId'    => ['标的ID['.$this->payName.']', 'auto'],
            'waresIdLocal'    => ['标的ID[平台]', 'auto'],
            'borrowerId'    => ['借款人ID['.$this->payName.']', 'auto'],
            'borrowerIdLocal'    => ['借款人ID[平台]', 'auto'],
            'amount'    => ['金额['.$this->payName.']', 'auto'],
            'amountLocal'    => ['金额[平台]', 'auto'],
            'diff'    => ['差异', 'auto'],
            'checkk'    => ['审核', 'auto'],
            'exp'    => ['备注', 'auto'],
        );

        //配置分页
        $pageid = $this->_request->get('pageId', 1) - 0;
        $pager  = new \Sooh\DB\Pager($this->_request->get('pageSize', 10), $this->pageSizeEnum, false);
        $pager->init(-1,$pageid);

        //配置搜索项
        $frm = \Sooh\Base\Form\Broker::getCopy('default')->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_s);
        $frm->addItem('_sn_eq', form_def::factory('流水号', '', form_def::text))
            ->addItem('_ymd_eq', form_def::factory('日期', $ymd, form_def::datepicker))
            ->addItem('pageId', $pageid)
            ->addItem('pageSize', $this->pager->page_size);
        $frm->fillValues();
        if ($frm->flgIsThisForm) {
            $where = $frm->getWhere();
            if(!empty($where['ymd=']))$where['ymd='] = date('Ymd',strtotime($where['ymd=']));
        } else {
            $where = array();
        }
        if(empty($where['ymd=']))$where['ymd='] = $ymd;
        //合并表单的查询条件
        $search = \Prj\Misc\View::decodePkey($this->_request->get('where'));
        $where = array_merge($search?$search:[],$where);
        //拉取记录
        var_log($where,'查询条件>>>>>>>>>>>>>>>>>>');
        $pager->total = $this->DB()->getRecordCount('tb_dayLoan',$where);
        $total = $this->DB()->getRecord('tb_dayLoan','sum(amount) as amount,sum(amountLocal) as amountLocal',$where,'rsort diff');

        $rs = $this->DB()->getRecords('tb_dayLoan','*',$where);

        //格式配置
        $tempArr = array();
        $newArr  = array();
        foreach ($fieldsMapArr as $kk => $vv) {
            $header[$vv[0]] = '';
            $sum[$kk] = '';//统计
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
            isset($tempArr['amount']) && $tempArr['amount']/=100;
            isset($tempArr['amountLocal']) && $tempArr['amountLocal']/=100;
            isset($tempArr['paycorp']) && $tempArr['paycorp'] = \Prj\Consts\PayGW::$payCorp[$tempArr['paycorp']];
            isset($tempArr['ymd']) && $tempArr['ymd'] = date('Y-m-d',strtotime($tempArr['ymd']));
            isset($tempArr['checkk']) && $tempArr['checkk'] = $tempArr['checkk']?'已确认':'未处理';
            $tempArr['diff'] = \Prj\Misc\View::checkDiff($tempArr['diff']);
            //===

            $newArr[] = $tempArr;
        }

        //todo 数据统计
        $sum['amount']=$total['amount']/100;
        $sum['amountLocal']=$total['amountLocal']/100;
        //===

        $rs = $newArr;
        if($isDownloadEXCEL)return $this->downEXCEL($rs, array_keys($header),null,true);
        //输出
        $this->_view->assign('sum',$sum);
        $this->_view->assign('rs', $rs);
        $this->_view->assign('header', $header);
        $this->_view->assign('pager', $pager);
        $this->_view->assign('where', \Prj\Misc\View::encodePkey($where));
    }

    /**
     * 还款（对账）
     */
    public function dayPaybackAction(){
        //\Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $isDownloadEXCEL = $this->_request->get('__EXCEL__');
        $ymd = $this->_request->get('ymd',$this->dateInit);
        $sum = [];//统计
        //配置表格
        $fieldsMapArr = array(
            'sn'    => ['流水号', 'auto'],
            'ymd'    => ['日期', 'auto'],
            'paycorp'    => ['来源', 'auto'],
            'waresId'    => ['标的ID['.$this->payName.']', 'auto'],
            'waresIdLocal'    => ['标的ID[平台]', 'auto'],
            'borrowerId'    => ['借款人ID['.$this->payName.']', 'auto'],
            'borrowerIdLocal'    => ['借款人ID[平台]', 'auto'],
            'amount'    => ['金额['.$this->payName.']', 'auto'],
            'amountLocal'    => ['金额[平台]', 'auto'],
            'diff'    => ['差异', 'auto'],
            'checkk'    => ['审核', 'auto'],
            'exp'    => ['备注', 'auto'],
        );

        //配置分页
        $pageid = $this->_request->get('pageId', 1) - 0;
        $pager  = new \Sooh\DB\Pager($this->_request->get('pageSize', 100), $this->pageSizeEnum, false);
        $pager->init(-1,$pageid);

        //配置搜索项
        $frm = \Sooh\Base\Form\Broker::getCopy('default')->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_s);
        $frm->addItem('_sn_eq', form_def::factory('流水号', '', form_def::text))
            ->addItem('_ymd_eq', form_def::factory('日期', $ymd, form_def::datepicker))
            ->addItem('pageId', $pageid)
            ->addItem('pageSize', $this->pager->page_size);
        $frm->fillValues();
        if ($frm->flgIsThisForm) {
            $where = $frm->getWhere();
            if(!empty($where['ymd=']))$where['ymd='] = date('Ymd',strtotime($where['ymd=']));
        } else {
            $where = array();
        }
        if(empty($where['ymd=']))$where['ymd='] = $ymd;
        //合并表单的查询条件
        $search = \Prj\Misc\View::decodePkey($this->_request->get('where'));
        $where = array_merge($search?$search:[],$where);
        //拉取记录
        var_log($where,'查询条件>>>>>>>>>>>>>>>>>>');
        $pager->total = $this->DB()->getRecordCount('tb_dayPayback',$where);
        $total = $this->DB()->getRecord('tb_dayPayback','sum(amount) as amount,sum(amountLocal) as amountLocal',$where,'rsort diff');

        $rs = $this->DB()->getRecords('tb_dayPayback','*',$where,'rsort diff',$pager->page_size,$pager->rsFrom());

        //格式配置
        $tempArr = array();
        $newArr  = array();
        foreach ($fieldsMapArr as $kk => $vv) {
            $header[$vv[0]] = '';
            $sum[$kk] = '';//统计
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
            isset($tempArr['amount']) && $tempArr['amount']/=100;
            isset($tempArr['amountLocal']) && $tempArr['amountLocal']/=100;
            isset($tempArr['paycorp']) && $tempArr['paycorp'] = \Prj\Consts\PayGW::$payCorp[$tempArr['paycorp']];
            isset($tempArr['ymd']) && $tempArr['ymd'] = date('Y-m-d',strtotime($tempArr['ymd']));
            isset($tempArr['checkk']) && $tempArr['checkk'] = $tempArr['checkk']?'已确认':'未处理';
            $tempArr['diff'] = \Prj\Misc\View::checkDiff($tempArr['diff']);
            //===

            $newArr[] = $tempArr;
        }
        //todo 数据统计
        $sum['amount']=$total['amount']/100;
        $sum['amountLocal']=$total['amountLocal']/100;
        //===
        $rs = $newArr;
        if($isDownloadEXCEL)return $this->downEXCEL($rs, array_keys($header),null,true);
        //输出
        $this->_view->assign('sum',$sum);
        $this->_view->assign('rs', $rs);
        $this->_view->assign('header', $header);
        $this->_view->assign('pager', $pager);
        $this->_view->assign('where', \Prj\Misc\View::encodePkey($where));
    }

    /**
     * 返用户本息对账
     */
    public function dayPaysplitAction(){
        //\Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $isDownloadEXCEL = $this->_request->get('__EXCEL__');
        $ymd = $this->_request->get('ymd',$this->dateInit);
        $sum = [];//统计
        //配置表格
        $fieldsMapArr = array(
            'sn'    => ['流水号', 'auto'],
            'ymd'    => ['日期', 'auto'],
            'paycorp'    => ['来源', 'auto'],
            'waresId'    => ['标的ID['.$this->payName.']', 'auto'],
            'waresIdLocal'    => ['标的ID[平台]', 'auto'],
            'userId'    => ['用户ID['.$this->payName.']', 'auto'],
            'userIdLocal'    => ['用户ID[平台]', 'auto'],
            'amount'    => ['本金['.$this->payName.']', 'auto'],
            'amountLocal'    => ['本金[平台]', 'auto'],
            'interest'    => ['利息['.$this->payName.']', 'auto'],
            'interestLocal'    => ['利息[平台]', 'auto'],
            'diff'    => ['差异', 'auto'],
            'checkk'    => ['审核', 'auto'],
            'exp'    => ['备注', 'auto'],
        );

        //配置分页
        $pageid = $this->_request->get('pageId', 1) - 0;
        $pager  = new \Sooh\DB\Pager($this->_request->get('pageSize', 100), $this->pageSizeEnum, false);
        $pager->init(-1,$pageid);

        //配置搜索项
        $frm = \Sooh\Base\Form\Broker::getCopy('default')->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_s);
        $frm->addItem('_sn_eq', form_def::factory('流水号', '', form_def::text))
            ->addItem('_ymd_eq', form_def::factory('日期', $ymd, form_def::datepicker))
            ->addItem('pageId', $pageid)
            ->addItem('pageSize', $this->pager->page_size);
        $frm->fillValues();
        if ($frm->flgIsThisForm) {
            $where = $frm->getWhere();
            if(!empty($where['ymd=']))$where['ymd='] = date('Ymd',strtotime($where['ymd=']));
        } else {
            $where = array();
        }
        if(empty($where['ymd=']))$where['ymd='] = $ymd;
        //合并表单的查询条件
        $search = \Prj\Misc\View::decodePkey($this->_request->get('where'));
        $where = array_merge($search?$search:[],$where);
        //拉取记录
        var_log($where,'查询条件>>>>>>>>>>>>>>>>>>');
        $pager->total = $this->DB()->getRecordCount('tb_dayPaysplit',$where);
        $total = $this->DB()->getRecord('tb_dayPaysplit','sum(amount) as amount,sum(amountLocal) as amountLocal,sum(interest) as interest,sum(interestLocal) as interestLocal',$where,'rsort diff');

        $rs = $this->DB()->getRecords('tb_dayPaysplit','*',$where,'rsort diff',$pager->page_size,$pager->rsFrom());

        //格式配置
        $tempArr = array();
        $newArr  = array();
        foreach ($fieldsMapArr as $kk => $vv) {
            $header[$vv[0]] = '';
            $sum[$kk] = '';//统计
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
            isset($tempArr['amount']) && $tempArr['amount']/=100;
            isset($tempArr['amountLocal']) && $tempArr['amountLocal']/=100;
            isset($tempArr['interest']) && $tempArr['interest']/=100;
            isset($tempArr['interestLocal']) && $tempArr['interestLocal']/=100;
            isset($tempArr['paycorp']) && $tempArr['paycorp'] = \Prj\Consts\PayGW::$payCorp[$tempArr['paycorp']];
            isset($tempArr['ymd']) && $tempArr['ymd'] = date('Y-m-d',strtotime($tempArr['ymd']));
            isset($tempArr['checkk']) && $tempArr['checkk'] = $tempArr['checkk']?'已确认':'未处理';
            //===

            $tempArr['diff'] = \Prj\Misc\View::checkDiff($tempArr['diff']);
            //===
            $newArr[] = $tempArr;
        }

        //todo 数据统计
        $sum['amount']=$total['amount']/100;
        $sum['amountLocal']=$total['amountLocal']/100;
        $sum['interest']=$total['interest']/100;
        $sum['interestLocal']=$total['interestLocal']/100;

        $rs = $newArr;
        if($isDownloadEXCEL)return $this->downEXCEL($rs, array_keys($header),null,true);
        //输出
        $this->_view->assign('sum',$sum);
        $this->_view->assign('rs', $rs);
        $this->_view->assign('header', $header);
        $this->_view->assign('pager', $pager);
        $this->_view->assign('where', \Prj\Misc\View::encodePkey($where));
    }

    /**
     * 充值流水（对账）
     */
    public function dayManageAction(){
        //\Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $isDownloadEXCEL = $this->_request->get('__EXCEL__');
        $ymd = $this->_request->get('ymd',$this->dateInit);
        $sum = [];//统计
        //配置表格
        $fieldsMapArr = array(
            'sn'    => ['流水号', 'auto'],
            'ymd'    => ['日期', 'auto'],
            'type'    => ['类型', 'auto'],
            'paycorp'    => ['来源', 'auto'],
            'waresId'    => ['标的ID['.$this->payName.']', 'auto'],
            'waresIdLocal'    => ['标的ID[平台]', 'auto'],
            'amount'    => ['金额['.$this->payName.']', 'auto'],
            'amountLocal'    => ['金额[平台]', 'auto'],
            'diff'    => ['差异', 'auto'],
            'checkk'    => ['审核', 'auto'],
            'exp'    => ['备注', 'auto'],
        );

        //配置分页
        $pageid = $this->_request->get('pageId', 1) - 0;
        $pager  = new \Sooh\DB\Pager($this->_request->get('pageSize', 50), $this->pageSizeEnum, false);
        $pager->init(-1,$pageid);
        $typeArr = [
            \Prj\Consts\PayGW::tally_managementTrans=>\Prj\Consts\PayGW::$tallyMap[\Prj\Consts\PayGW::tally_managementTrans],
            \Prj\Consts\PayGW::tally_managementConfirm=>\Prj\Consts\PayGW::$tallyMap[\Prj\Consts\PayGW::tally_managementConfirm],
            ''=>'全部',
        ];
        //配置搜索项
        $frm = \Sooh\Base\Form\Broker::getCopy('default')->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_s);
        $frm->addItem('_sn_eq', form_def::factory('流水号', '', form_def::text))
            ->addItem('_type_eq', form_def::factory('类型', '', form_def::select,$typeArr))
            ->addItem('_ymd_eq', form_def::factory('日期', $ymd, form_def::datepicker))
            ->addItem('pageId', $pageid)
            ->addItem('pageSize', $this->pager->page_size);
        $frm->fillValues();
        if ($frm->flgIsThisForm) {
            $where = $frm->getWhere();
            if(!empty($where['ymd=']))$where['ymd='] = date('Ymd',strtotime($where['ymd=']));
        } else {
            $where = array();
        }
        if(empty($where['ymd=']))$where['ymd='] = $ymd;
        //合并表单的查询条件
        $search = \Prj\Misc\View::decodePkey($this->_request->get('where'));
        $where = array_merge($search?$search:[],$where);
        //拉取记录
        var_log($where,'查询条件>>>>>>>>>>>>>>>>>>');

        $pager->total = $this->DB()->getRecordCount('tb_dayManage',$where);
        $total = $this->DB()->getRecord('tb_dayManage','sum(amount) as amount,sum(amountLocal) as amountLocal',$where,'rsort diff');

        $rs = $this->DB()->getRecords('tb_dayManage','*',$where,'rsort diff',$pager->page_size,$pager->rsFrom());

        //格式配置
        $tempArr = array();
        $newArr  = array();
        foreach ($fieldsMapArr as $kk => $vv) {
            $header[$vv[0]] = '';
            $sum[$kk] = '';
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
            isset($tempArr['amount']) && $tempArr['amount']/=100;
            isset($tempArr['amountLocal']) && $tempArr['amountLocal']/=100;
            isset($tempArr['ymd']) && $tempArr['ymd'] = date('Y-m-d',strtotime($tempArr['ymd']));
            isset($tempArr['type']) && $tempArr['type'] = $tempArr['type']?\Prj\Consts\PayGW::$tallyMap[$tempArr['type']]:'未知';
            isset($tempArr['paycorp']) && $tempArr['paycorp'] = \Prj\Consts\PayGW::$payCorp[$tempArr['paycorp']];
            isset($tempArr['checkk']) && $tempArr['checkk'] = $tempArr['checkk']?'已确认':'未处理';
            $tempArr['diff'] = \Prj\Misc\View::checkDiff($tempArr['diff']);
            //===

            $newArr[] = $tempArr;
        }

        //todo 数据统计
        $sum['amount']=$total['amount']/100;
        $sum['amountLocal']=$total['amountLocal']/100;
        //===

        $rs = $newArr;
        if($isDownloadEXCEL)return $this->downEXCEL($rs, array_keys($header),null,true);
        //输出
        $this->_view->assign('sum',$sum);
        $this->_view->assign('rs', $rs);
        $this->_view->assign('header', $header);
        $this->_view->assign('pager', $pager);
        $this->_view->assign('where', \Prj\Misc\View::encodePkey($where));
    }

    /**
     * 公司账户运营资金流水（包括分润）
     */
    public function dayCompanyAction(){
        $isDownloadEXCEL = $this->_request->get('__EXCEL__');
        //配置表格
        $fieldsMapArr = array(
            'sn'    => ['流水号', '20'],
            'type'    => ['类型', '20'],
            'amount'=>['金额','20'],
            'userId'=>['用户ID','20'],
            'waresId'=>['标的ID','20'],
            'rebateId'=>['返利ID','20'],
            'statusCode'=>['状态','20'],
            'tallyYmd'=>['流水时间','20'],
            'payYmd'=>['支付时间','20'],
            'exp'=>['备注','20'],
        );

        //配置分页
        $pageid = $this->_request->get('pageId', 1) - 0;
        $pager  = new \Sooh\DB\Pager($this->_request->get('pageSize', 50), $this->pageSizeEnum, false);
        $pager->init(-1,$pageid);

        //配置搜索项
        $frm = \Sooh\Base\Form\Broker::getCopy('default')->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_s);
        $frm->addItem('_sn_eq', form_def::factory('流水号', '', form_def::text))
            ->addItem('_type_eq', form_def::factory('类型', '', form_def::select , \Prj\Consts\PayGW::$tallyMap+[''=>'全部']))
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
        //拉取记录
        var_log($where,'查询条件>>>>>>>>>>>>>>>>>>');
        $rs = \Prj\Data\Systally::paged($pager,$where,'rsort tallyYmd');

        //格式配置
        $tempArr = array();
        $newArr  = array();
        foreach ($fieldsMapArr as $kk => $vv) {
            $header[$vv[0]] = '';
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
            $tempArr['type'] = \Prj\Consts\PayGW::$tallyMap[$tempArr['type']];
            $tempArr['amount']/=100;
            $tempArr['tallyYmd'] = date('Y-m-d H:i:s',strtotime($tempArr['tallyYmd']));
            $tempArr['payYmd'] = $tempArr['payYmd']?date('Y-m-d H:i:s',strtotime($tempArr['payYmd'])):'';
            $tempArr['userId'] = $tempArr['userId']?$tempArr['userId']:'';
            $tempArr['waresId'] = $tempArr['waresId']?$tempArr['waresId']:'';
            $tempArr['rebateId'] = $tempArr['rebateId']?$tempArr['rebateId']:'';
            $tempArr['statusCode'] = \Prj\Consts\Systally::$exp[$tempArr['statusCode']];
            //===
            $newArr[] = $tempArr;
        }
        $rs = $newArr;
        if($isDownloadEXCEL)return $this->downEXCEL($rs, array_keys($header),null,true);
        //输出
        $this->_view->assign('rs', $rs);
        $this->_view->assign('header', $header);
        $this->_view->assign('pager', $pager);
        $this->_view->assign('where', \Prj\Misc\View::encodePkey($where));
    }

    /**
     * 存钱罐账户日收益
     */
    public function dayInterestAction(){
        $isDownloadEXCEL = $this->_request->get('__EXCEL__');
        $ymd = $this->_request->get('ymd',$this->dateInit);
        $sum = [];//统计
        //配置表格
        $fieldsMapArr = array(
            'sn'    => ['流水号', 'auto'],
            'ymd'    => ['日期', 'auto'],
            'paycorp'    => ['来源', 'auto'],
            'userId'    => ['用户ID['.$this->payName.']', 'auto'],
            //'userIdLocal'    => ['用户ID[平台]', 'auto'],
            'amount'    => ['金额['.$this->payName.']', 'auto'],
            //'amountLocal'    => ['金额[平台]', 'auto'],
            //'diff'    => ['差异', 'auto'],
            'statusCode'=>['状态','auto'],
            'tallyId'=>['资金流水号','auto'],
            //'checkk'    => ['审核', 'auto'],
            'exp'    => ['备注', 'auto'],
        );

        //配置分页
        $pageid = $this->_request->get('pageId', 1) - 0;
        $pager  = new \Sooh\DB\Pager($this->_request->get('pageSize', 100), $this->pageSizeEnum, false);
        $pager->init(-1,$pageid);

        //配置搜索项
        $frm = \Sooh\Base\Form\Broker::getCopy('default')->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_s);
        $frm->addItem('_sn_eq', form_def::factory('流水号', '', form_def::text))
            ->addItem('_ymd_eq', form_def::factory('日期', $ymd, form_def::datepicker))
            ->addItem('pageId', $pageid)
            ->addItem('pageSize', $this->pager->page_size);
        $frm->fillValues();
        if ($frm->flgIsThisForm) {
            $where = $frm->getWhere();
            if(!empty($where['ymd=']))$where['ymd='] = date('Ymd',strtotime($where['ymd=']));
        } else {
            $where = array();
        }
        if(empty($where['ymd=']))$where['ymd='] = $ymd;
        //合并表单的查询条件
        $search = \Prj\Misc\View::decodePkey($this->_request->get('where'));
        $where = array_merge($search?$search:[],$where);
        //拉取记录
        var_log($where,'查询条件>>>>>>>>>>>>>>>>>>');
        $pager->total = $this->DB()->getRecordCount('tb_dayInterest_0',$where);
        $total = $this->DB()->getRecord('tb_dayInterest_0','sum(amount) as amount',$where,'rsort diff');

        $rs = $this->DB()->getRecords('tb_dayInterest_0','*',$where,'rsort diff',$pager->page_size,$pager->rsFrom());

        //格式配置
        $tempArr = array();
        $newArr  = array();
        foreach ($fieldsMapArr as $kk => $vv) {
            $header[$vv[0]] = '';
            $sum[$kk] = '';
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
            isset($tempArr['amount']) && $tempArr['amount']/=100;
            isset($tempArr['amountLocal']) && $tempArr['amountLocal']/=100;
            isset($tempArr['ymd']) && $tempArr['ymd'] = date('Y-m-d',strtotime($tempArr['ymd']));
            isset($tempArr['diff']) && $tempArr['diff'] = \Prj\Misc\View::checkDiff($tempArr['diff']);
            isset($tempArr['paycorp']) && $tempArr['paycorp'] = \Prj\Consts\PayGW::$payCorp[$tempArr['paycorp']];
            //===

            //===
            $newArr[] = $tempArr;
        }

        //todo 数据统计
        $sum['amount']=$total['amount']/100;
        //$sum['amountLocal']+=$tempArr['amountLocal'];

        $rs = $newArr;
        if($isDownloadEXCEL)return $this->downEXCEL($rs, array_keys($header),null,true);
        //输出
        $this->_view->assign('sum',$sum);
        $this->_view->assign('rs', $rs);
        $this->_view->assign('header', $header);
        $this->_view->assign('pager', $pager);
        $this->_view->assign('where', \Prj\Misc\View::encodePkey($where));
    }

    public function sendDayInterestAction(){
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $where                          = \Prj\Misc\View::decodePkey($this->_request->get('_pkey_val_'));
        var_log($where,'_pkey_val_');
        $tbname = 'tb_dayInterest_0';
        $dayWhere = ['sn'=>$where['id']];
        $dayInter = $this->DB()->getRecord($tbname,'*',$dayWhere);
        if(empty($dayInter)){
           // return $this->returnError('系统错误:流水不存在');
            return $this->returnError(\Prj\Lang\Broker::getMsg('check.sn_missing'));
        }else{
            $userId = $dayInter['userId'];
            $amount = $dayInter['amount'];
            $sn = $dayInter['sn'];
            if($dayInter['statusCode']==1){
               // return $this->returnError('已经支付过');
                return $this->returnError(\Prj\Lang\Broker::getMsg('check.check_over'));
            }else{
                $user = \Prj\Data\User::getCopy($userId);
                $user->load();
                if(!$user->exists()){
                   // return $this->returnError('系统错误:用户不存在');
                    return $this->returnError(\Prj\Lang\Broker::getMsg('check.user_notfound'));
                }else{
                    if(!$user->lock(__CLASS__.' '.__METHOD__.' SN:'.$sn)){
                        var_log('[warning]第一次锁定失败');
                        $user->reload();
                        if(!$user->lock(__CLASS__.' '.__METHOD__.' SN:'.$sn)){
                           // return $this->returnError('系统错误:锁定用户失败');
                            return $this->returnError(\Prj\Lang\Broker::getMsg('check.user_lock_fail'));
                           
                        }
                    }
                    \Prj\Misc\OrdersVar::$introForUser = date('Y年m月d日',strtotime($dayInter['ymd'])).'存钱罐利息';
                    \Prj\Misc\OrdersVar::$introForCoder = 'dayInterest_'.$sn.'_'.$dayInter['ymd'];
                    $tally = \Prj\Data\WalletTally::addTally($userId,$user->getField('wallet'),$amount,0,$sn,\Prj\Consts\OrderType::dayInterest);
                    if(empty($tally)){
                       // return $this->returnError('系统错误:添加流水失败');
                        return $this->returnError(\Prj\Lang\Broker::getMsg('check.sn_add_fail'));
                    }else{
                        $tally->setField('statusCode',\Prj\Consts\Tally::status_new);
                        $user->setField('wallet',$user->getField('wallet')+$amount);
                        try{
                            $tally->update();
                        }catch (\ErrorException $e){
                            var_log('[error]'.__CLASS__.' '.__METHOD__.' '.$e->getMessage());
                            $user->unlock();
                           // return $this->returnError('系统错误:更新流水表失败');
                            return $this->returnError(\Prj\Lang\Broker::getMsg('check.sn_upd_fail'));
                        }

                        try{
                            $ret = $this->DB()->updRecords($tbname,['statusCode'=>1,'tallyId'=>$tally->getPKey()['tallyId']],$dayWhere);
                        }catch (\ErrorException $e){
                            var_log('[error]'.__CLASS__.' '.__METHOD__.' '.$e->getMessage());
                            $user->unlock();
                            $tally->setField('statusCode',\Prj\Consts\Tally::status_abandon);
                            $tally->update();
                           // return $this->returnError('系统错误:更新存钱罐表失败');
                            return $this->returnError(\Prj\Lang\Broker::getMsg('check.saving_pot_upd_fail'));
                        }

                        try{
                            $user->update();
                        }catch (\ErrorException $e){
                            var_log('[error]'.__CLASS__.' '.__METHOD__.' '.$e->getMessage());
                            $user->unlock();
                            $tally->setField('statusCode',\Prj\Consts\Tally::status_abandon);
                            $tally->update();
                            $ret = $this->DB()->updRecords($tbname,['statusCode'=>0,'tallyId'=>0],$dayWhere);
                           // return $this->returnError('系统错误:更新用户表失败');
                            return $this->returnError(\Prj\Lang\Broker::getMsg('check.userform_upd_fail'));
                        }

	                    \Prj\ReadConf::run(
		                    [
			                    'event' => 'saving_money',
			                    'saving_pot_money' => $amount / 100,
			                    'brand' => \Prj\Message\Message::MSG_BRAND,
		                    ],
		                    ['userId' => $userId]
	                    );
                        return $this->returnOK('操作成功');
                    }
                }
            }
        }

    }
}