<?php
/**
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/4/8 0008
 * Time: 上午 9:27
 */
use Sooh\Base\Form\Item as form_def;
use Sooh\Base\Form\Options as options_def;
class SecondmarketsearchController extends \Prj\ManagerCtrl{
    public function init() {
        parent::init();
    }

    protected $pageSizeEnum = [50,100, 150, 300, 500, 1000];
    protected $hesitateType =['注册未绑卡', '绑卡未购买'];

    protected $headers = [
        'userId'=>['用户ID', null],
        'phone'=>['手机号', null],
        'realname'=>['姓名', null],
        'ymdReg'=>['注册日期', null],
        'ymdBindCard'=>['认证绑卡日期', null],
        'ymdFirstBuy'=>['首投日期', null],
        'hesitateDaysBind'=>['认证绑卡犹豫期（天）', null],
        'hesitateDaysFistBuy'=>['首投犹豫期（天）', null],
    ];

    public function indexAction() {
        $pageid = $this->_request->get('pageId',1)-0;
        $pagesize = $this->_request->get('pageSize',current($this->pageSizeEnum))-0;
        $pager = new \Sooh\DB\Pager($pagesize,$this->pageSizeEnum,false);
        $ymdDefault = date('Y-m-d', \Sooh\Base\Time::getInstance()->timestamp(-4));
        $form = \Sooh\Base\Form\Broker::getCopy('default')
            ->init(\Sooh\Base\Tools::uri(),'get',\Sooh\Base\Form\Broker::type_s);
        $form->addItem('hesitateType', form_def::factory('营销类型', 0, form_def::select, $this->hesitateType))
            ->addItem('ymd', form_def::factory('日期', $ymdDefault, form_def::datepicker))
            ->addItem('pageid', $pageid)
            ->addItem('pagesize', $pager->page_size);

        $form->fillValues();
        $fields = $form->getFields();


        $where = [];
        if(!empty($fields)){
            $fields['ymd'] = date('Ymd', strtotime($fields['ymd']));
            $db = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_p2prpt);
            $n = $db->getRecordCount(\Rpt\Tbname::tb_secondmarket_list, $fields);
            if($n) {
                $this->returnError('此条件的记录已经存在');
            }else {
// 展示出没有添加到营销列表里的记录
                if($fields['hesitateType'] == 0) {
                    $where['ymdReg'] = $fields['ymd'];
                    $where['ymdBindCard'] = 0;
                    $where['ymdFirstBuy'] = 0;
                    $where['flagUser!'] = 1;
                }elseif($fields['hesitateType'] == 1){
                    $where['ymdBindCard'] = $fields['ymd'];
                    $where['ymdFirstBuy'] = 0;
                    $where['flagUser!'] = 1;
                }
                $count = $db->getRecordCount(\Rpt\Tbname::tb_user_final, $where);
                $pager->init($count, $pageid);
                $records = $db->getRecords(\Rpt\Tbname::tb_user_final, 'userId,phone,realname,ymdReg,ymdBindCard,ymdFirstBuy', $where, null, $pagesize, $pager->rsFrom());
                $dt = \Sooh\Base\Time::getInstance();
                if(!empty($records)) {
                    foreach($records as $k => $r) {
                        $tmp = array_combine(array_keys($this->headers), array_fill(0, sizeof($this->headers), ''));
                        $tmp['userId'] = $r['userId'];
                        $tmp['phone'] = $r['phone'];
                        $tmp['realname'] = $r['realname'];
                        $tmp['ymdReg'] = date('Y-m-d', strtotime($r['ymdReg']));
                        if($fields['hesitateType'] == 0) {
                            $tmp['hesitateDaysBind']=$tmp['hesitateDaysFistBuy'] = floor(($dt->timestamp() - strtotime($r['ymdReg']))/86400);
                        }elseif($fields['hesitateType'] == 1) {
                            $tmp['ymdBindCard'] = date('Y-m-d', strtotime($r['ymdBindCard']));
                            $tmp['hesitateDaysBind'] = floor((strtotime($r['ymdBindCard']) - strtotime($r['ymdReg']))/86400);
                            $tmp['hesitateDaysFistBuy'] = floor(($dt->timestamp() - strtotime($r['ymdBindCard']))/86400);
                        }
                        $records[$k] = $tmp;
                    }
                }
            }
        }

        foreach($this->headers as $v) {
            $headers[$v[0]] = $v[1];
        }
        $this->_view->assign('headers', $headers);
        $this->_view->assign('records', $records);
        $this->_view->assign('where', $fields);
        $this->_view->assign('pager', $pager);
    }

    public function saverecordsAction() {
        $pkey = $this->_request->get('_pkey_val_');
        $pkey = \Prj\Misc\View::decodePkey($pkey);
        if(empty($pkey)) {
            return $this->returnError('参数错误');
        }
        if($pkey['hesitateType'] == 0) {
            $where['ymdReg'] = $pkey['ymd'];
            $where['ymdBindCard'] = 0;
        }elseif($pkey['hesitateType'] == 1) {
            $where['ymdBindCard'] = $pkey['ymd'];
            $where['ymdFirstBuy'] = 0;
        }
        $where['flagUser!'] = 1;
        $db = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_p2prpt);
        $records = $db->getCol(\Rpt\Tbname::tb_user_final, 'userId', $where);
        $num = 0;
        if(!empty($records)) {
            foreach($records as $userId) {
                $tmp = [
                    'ymd'=>$pkey['ymd'],
                    'hesitateType'=>$pkey['hesitateType'],
                    'userId'=>$userId,
                ];
                try{
                    \Sooh\DB\Broker::errorMarkSkip();
                    $db->addRecord(\Rpt\Tbname::tb_secondmarket_list, $tmp);
                    $num++;
                }catch(\ErrorException $e) {
                    error_log("###营销类型:".$this->hesitateType[$where['hesitateType']]." 用户ID:".$userId."保存失败");
                }

            }
        }

        return $this->returnOK("总共".sizeof($records)."条，保存成功".$num."条");
    }
}
