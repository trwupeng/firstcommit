<?php
use Sooh\Base\Form\Item as form_def;

/**
 * 标的模板
 * By Hand
 */
class WarestplController extends \Prj\ManagerCtrl
{
    protected $editor    = null;
    protected $class     = '\Prj\Data\Warestpl';
    protected $fieldsMap = array(
        'tplId'              => array('模板ID', '160'),
        'tplName'            => array('模板名称', '100'),
        'waresNameSim'       => array('标的简称', '60'),
        'shelfId'            => array('产品类型', '60'),
        'deadLine'           => array('期限', '40'),
        'dlUnit'             => array('单位', '40'),
        'tags'               => array('标签', 'auto'),
        'vipLevel'           => array('vip等级', '60'),
        'amount'             => array('总额(元)', '60'),
        'priceStart'         => array('起投金额(元)', '90'),
        'priceStep'          => array('递增金额(元)', '90'),
        'yieldStatic'        => array('固定年化收益率', '100'),
        'yieldStaticAdd'     => array('活动加息', '60'),
        'interestStartType'  => array('起息方式', 'auto'),
        'returnType'         => array('还款方式', '160'),
        'autoConfirm'        => array('自动还款', 'auto'),
        'autoReturnFund'     => array('自动回款', 'auto'),
    );

    public function editAction()
    {
        $inputConfig = [
            'tplName'           => ['模板名称', '房贷宝模板' . rand(1000, 9999), form_def::text, [], 'required,length[~15]'],
            'waresNameSim'      => ['标的简称', '房贷宝', form_def::text, [], 'required,length[~15]'],
            'shelfId'           => ['产品类型', \Prj\Consts\Wares::shelf_house, form_def::select, \Prj\Consts\Wares::$shilfIdName, 'required'],
            'deadLine'          => ['期限', '30', form_def::text, [], 'required'],
            'dlUnit'            => ['单位', '天', form_def::select, ['天' => '天', '月' => '月'], 'required'],
            'tags'              => ['标签', '活动', form_def::chkbox, ['活动' => '活动', '新手' => '新手'], 'required'],
            'vipLevel'          => ['vip等级', '0', form_def::select, ['0' => '不限', '1' => 'VIP1'], 'required'],
            'amount'            => ['总额(元)', '100000', form_def::text, [], 'required;digits'],
            'priceStart'        => ['起投金额(元)', '200', form_def::text, [], 'required;digits'],
            'priceStep'         => ['递增金额(元)', '0.01', form_def::text, [], 'required;number'],
            'yieldStatic'       => ['固定年化收益率', '0.08', form_def::text, [], 'required;number'],
            'yieldStaticAdd'    => ['活动加息', '0.008', form_def::text, [], 'required;number'],
            'interestStartType' => ['起息方式', \Prj\Consts\InterestStart::whenFull, form_def::select, \Prj\Consts\InterestStart::$enum, 'required'],
            'returnType'        => ['还款方式', \Prj\Consts\ReturnType::single, form_def::select, \Prj\Consts\ReturnType::$enum, 'required'],
            'autoConfirm'       => ['(借款人)自动还款开关', 1, form_def::select, ['0' => '关', '1' => '开'], 'required'],
            'autoReturnFund'    => ['自动回款(给投资人)开关', 1, form_def::select, ['0' => '关', '1' => '开'], 'required'],
        ];
        $actionName  = '标的模板';

        $class = $this->class;
        if (!class_exists($class)) return $this->returnError('系统错误:不存在的模板类');
        $where = \Prj\Misc\View::decodePkey($this->_request->get('_pkey_val_'));
        $type  = $this->_request->get('_type');
        $frm   = \Sooh\Base\Form\Broker::getCopy('default')
            ->init(\Sooh\Base\Tools::uri(), 'get', empty($where) ? \Sooh\Base\Form\Broker::type_c : \Sooh\Base\Form\Broker::type_u);

        $frm->addItem('_type', $type)
            ->addItem('_pkey_val_', $this->_request->get('_pkey_val_'));

        if ($inputConfig) {
            foreach ($inputConfig as $k => $v) {
                $frm->addItem($k, form_def::factory($v[0], $v[1], $v[2], $v[3], ['data-rule' => $v[4]]));
            }
        }

        //todo 构造表单数据
        $frm->fillValues();
        //表单提交
        if ($frm->flgIsThisForm) {
            //审核通过
            if ($type == 'check') {

            }
            $where = \Prj\Misc\View::decodePkey($this->_request->get('_pkey_val_'));
            if (!empty($where)) {
                $frm->switchType(\Sooh\Base\Form\Broker::type_u);
            } else {
                $frm->switchType(\Sooh\Base\Form\Broker::type_c);
            }
            $op = '';
            try {
                $fields = $frm->getFields();
                $fields = $this->inFormat($fields);
                try {
                    //todo 字段过滤
                } catch (\ErrorException $e) {
                    return $this->returnError($e->getMessage());
                }

                if ($frm->type() == \Sooh\Base\Form\Broker::type_c) //add
                {
                    $op  = "新增" . $actionName;
                    $tmp = $class::add($fields);
                    try {
                        $tmp->update();
                    } catch (\ErrorException $e) {
                        return $this->returnError($e->getMessage());
                    }
                    //todo 插入数据库
                } else { // update
                    $op = '更新' . $actionName;
                    //todo 更新数据库
                    $fields[$class::getPkeyName()] = $where['id'];
                    $tmp                         = $class::dataUpdate($fields);
                    if (empty($tmp)) return $this->returnError('找不到记录');
                    try {
                        $tmp->update();
                    } catch (\ErrorException $e) {
                        return $this->returnError($e->getMessage());
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
            $tmp = $class::getCopy($where['id']);
            $tmp->load();
            if(!$tmp->exists()){
                return $this->returnError('不存在的模板');
            }
            $fields = $tmp->dump();
            $fields = $this->outFormatEdit($fields);
            foreach($inputConfig as $k=>$v){
                $frm->items[$k]->value = $fields[$k];
            }
        }

        $this->_view->assign('type', $type);
        $this->_view->assign('_pkey_val_', $this->_request->get('_pkey_val_'));
    }

    protected function inFormat($fields)
    {
        $moneyArr = ['amount', 'priceStart', 'priceStep'];
        foreach ($moneyArr as $v) {
            if (isset($fields[$v])) {
                $fields[$v] *= 100;
            }
        }
        return $fields;
    }

    protected function outFormatIndex($fields)
    {
        $moneyArr = ['amount', 'priceStart', 'priceStep'];
        foreach ($moneyArr as $v) {
            if (isset($fields[$v])) {
                $fields[$v] /= 100;
            }
        }
        if(isset($fields['tags']))$fields['tags'] = implode(',',json_decode($fields['tags'],true));
        if(isset($fields['interestStartType']))$fields['interestStartType'] = \Prj\Consts\InterestStart::$enum[$fields['interestStartType']];
        if(isset($fields['returnType']))$fields['returnType'] = \Prj\Consts\ReturnType::$enum[$fields['returnType']];
        if(isset($fields['shelfId']))$fields['shelfId'] =  \Prj\Consts\Wares::$shilfIdName[$fields['shelfId']];
        if(isset($fields['autoConfirm']))$fields['autoConfirm'] = $fields['autoConfirm']?'开':'关';
        if(isset($fields['autoReturnFund']))$fields['autoReturnFund'] = $fields['autoReturnFund']?'开':'关';
        return $fields;
    }

    protected function outFormatEdit($fields){
        $moneyArr = ['amount', 'priceStart', 'priceStep'];
        foreach ($moneyArr as $v) {
            if (isset($fields[$v])) {
                $fields[$v] /= 100;
            }
        }
        if(isset($fields['tags']))$fields['tags'] = implode(',',$fields['tags']);
        return $fields;
    }

    public function indexAction()
    {
        $isDownloadEXCEL = $this->_request->get('__EXCEL__');
        //配置表格
        $fieldsMapArr = $this->fieldsMap;
        $class = $this->class;
        //配置分页
        $pageid = $this->_request->get('pageId', 1) - 0;
        $pager  = new \Sooh\DB\Pager($this->_request->get('pageSize', 10), $this->pageSizeEnum, false);
        $pager->init(-1, $pageid);

        //配置搜索项
        $frm = \Sooh\Base\Form\Broker::getCopy('default')->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_s);
        $frm->addItem('_'.$class::getPkeyName().'_eq', form_def::factory($this->fieldsMap[$class::getPkeyName()][0], '', form_def::text))
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
        $where  = array_merge($search ? $search : [], $where);
        //拉取记录
        $where+=['statusCode!'=>-1];
        var_log($where, '查询条件>>>>>>>>>>>>>>>>>>');
        $rs = \Prj\Data\Warestpl::paged($pager,$where);
        //格式配置
        $tempArr = array();
        $newArr  = array();
        foreach ($fieldsMapArr as $kk => $vv) {
            $header[$vv[0]] = $vv[1];
        }
        foreach ($rs as $v) {
            foreach ($fieldsMapArr as $kk => $vv) {
                $tempArr[$kk] = $v[$kk];
            }
            $tempArr = $this->outFormatIndex($tempArr);
            $newArr[] = $tempArr;
        }
        $rs = $newArr;
        if ($isDownloadEXCEL) return $this->downEXCEL($rs, array_keys($header), null, true);
        //输出
        $this->_view->assign('rs', $rs);
        $this->_view->assign('headers', $header);
        $this->_view->assign('pager', $pager);
        $this->_view->assign('where', \Prj\Misc\View::encodePkey($where));
    }

    public function deleteAction(){
        $where = \Prj\Misc\View::decodePkey($this->_request->get('_pkey_val_'));
        $class = $this->class;
        $tmp = $class::getCopy($where['id']);
        $tmp->load();
        if(!$tmp->exists()){
            return $this->returnError('不存在的模板');
        }
        $tmp->setField('statusCode',-1);
        try{
            $tmp->update();
            return $this->returnOK('删除成功');
        }catch (\ErrorException $e){
            return $this->returnError('操作失败');
        }
    }
}