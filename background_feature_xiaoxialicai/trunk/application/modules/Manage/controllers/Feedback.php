<?php
use Sooh\Base\Form\Item as form_def;
use Prj\Data\Vouchers as Vouchers;
use Sooh\DB\Base\Field;

/**
 * 反馈管理
 * Class FeedbackController
 */
class FeedbackController extends \Prj\ManagerCtrl
{

    const Field = phone;

    public function indexAction()
    {
        $fieldsMapArr = [
            'feedbackId' => [
                '反馈ID',
                '50'
            ],
            'userId' => [
                '用户ID',
                '45'
            ],
            'phone' => [
                '手机号',
                '45'
            ],
            'deviceId' => [
                '设备号',
                '100'
            ],
            'content' => [
                '内容',
                '100'
            ],
            'createTime' => [
                '创建时间',
                '70'
            ],
            'status' => [
                '状态',
                '40'
            ],
            'exp' => [
                '备注',
                '100'
            ]
        ];
        
        $pager = new \Sooh\DB\Pager(10, $this->pageSizeEnum, false);
        $pageid = $this->_request->get('pageId', 1) - 0;
        $pager->page_size = $this->_request->get('pageSize', 50);
        $ids = $this->_request->get('ids');
        $isDownloadExcel = $this->_request->get('__EXCEL__') == 1;
        $search = $this->_request->get('where', []);
        
        $frm = \Sooh\Base\Form\Broker::getCopy('default')->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_s);
        
        $frm->addItem('_userId_eq', form_def::factory('用户ID', '', form_def::text))
            ->addItem('_content_lk', form_def::factory('内容', '', form_def::text))
            ->addItem('_status_eq', form_def::factory('状态', '', form_def::select, [
            '' => '全部',
            \Prj\Data\Feedback::status_unread => '未读',
            \Prj\Data\Feedback::status_read => '已读',
            \Prj\Data\Feedback::status_handled => '已处理'
        ]))
            ->
        // ->addItem('_createTime_g2', form_def::factory('创建时间大于', '', form_def::datepicker))
        // ->addItem('_createTime_l2', form_def::factory('创建时间', '', form_def::datepicker))
        addItem('exp', form_def::factory('备注', '', form_def::text))
            ->addItem('pageid', $pageid)
            ->addItem('pagesize', $this->pager->page_size);
        $frm->fillValues();
        if ($frm->flgIsThisForm) {
            $where = $frm->getWhere();
        } else {
            $where = [];
        }
        
        $feedback = new \Prj\Data\Feedback();
        $pager->init(-1, $pageid);
        
        $keys = is_array($ids) ? $ids : explode(',', $ids);
        
        if (! empty($ids)) {
            foreach ($keys as $k => $v) {
                $keys[$k] = \Prj\Misc\View::decodePkey($v)['feedbackId'];
            }
            $where = array(
                'feedbackId' => $keys
            );
        }
        
       // var_log($keys, '>>>>>>>>>>>>>>>>>>>>>>>>>>>>');
        
        if (! empty($records)) {
            foreach ($records as $k => $r) {
                $_pkey_val_ = \Prj\Misc\View::encodePkey(array(
                    'feedbackId' => $r['feedbackId']
                ));
                if (! $isDownloadExcel) {
                    $r['_pkey_val_'] = $_pkey_val_;
                }
                
                $tmp = array(
                    'feedbackId' => $r['feedbackId'],
                    'userId' => $r['userId']
                );
                unset($r['feedbackId']);
                $r = array_merge($tmp, $r);
                $records[$k] = $r;
            }
        }
        
        $tmp = [];
        $tmp = $fieldsMapArr;
        
        $where1=[];
        $pager->total=\Prj\Data\Feedback::loopGetRecordsCount($where1);
    
        unset($tmp['phone']);
        // 全表导出
        
        if($isDownloadEXCEL){
            $records = \Prj\Data\Feedback::loopAll($where);
             
        }else{
            if($pager->pageid()==1){
        
                $ret = \Prj\Data\Feedback::loopGetRecordsPage(['createTime'=>'rsort'],['where'=>$where1],$pager);
            }else{
              
                $lastPage = \Sooh\Base\Session\Data::getInstance()->get('wp_lastPage');
              
                $ret = \Prj\Data\Feedback::loopGetRecordsPage(['createTime'=>'rsort'],$lastPage,$pager);
            }
            \Sooh\Base\Session\Data::getInstance()->set('wp_lastPage',$ret['lastPage']);
            $records = $ret['records'];
        }
        
//         if ($isDownloadExcel) {
//             $where = array_merge($where, $search);
            
//             $records = $feedback->db()->getRecords($pager,$feedback->tbname(), implode(',', array_keys($tmp)), $where, 'rsort createTime');
//         } else {
//             $records = \Prj\Data\Feedback::paged($pager, $where, null, implode(',', array_keys($tmp)));
//         }
        
        $header = [];
        foreach ($fieldsMapArr as $k => $v) {
            $header[$v[0]] = $v[1];
        }
        
        $temp = [];
        foreach ($records as $v) {
            
            $userId = $v['userId'];
            if (! empty($userId)) {
                $user = \Prj\Data\User::getCopy($userId);
                $user->load();
                $phone = $user->getField(self::Field);
            }else{
                $phone='';
            }
            
            foreach ($fieldsMapArr as $kk => $vv) {
                if ($kk == 'status') {
                    switch ($v[$kk]) {
                        case \Prj\Data\Feedback::status_read:
                            $temp[$kk] = '已读';
                            break;
                        case \Prj\Data\Feedback::status_unread:
                            $temp[$kk] = '未读';
                            break;
                        case \Prj\Data\Feedback::status_handled:
                            $temp[$kk] = '已处理';
                            break;
                        default:
                            $temp[$kk] = '其他';
                            break;
                    }
                }elseif($kk=='content') {
                    if(json_decode($v[$kk]) !== null) {
                        $temp[$kk] = htmlspecialchars(json_decode($v[$kk], true));
                    }else {
                        $temp[$kk] = htmlspecialchars($v[$kk]);
                    }
                }else {
                    $temp[$kk] = $v[$kk];
                }
                
                if (! $isDownloadExcel) {
                    $temp['_pkey_val_'] = \Prj\Misc\View::encodePkey([
                        'feedbackId' => $v['feedbackId']
                    ]);
                }
            }
            foreach ($temp as $k => $v) {
                if ($k == 'phone') {
                    $temp[$k] = $phone;
                }elseif ($k=='createTime'){
                    $createTime=$temp[$k];
                    $createTime=\Prj\Misc\View::fmtYmd($createTime,'time');
                    $temp[$k]=$createTime;
                }
            }
            $new[] = $temp;
        }
        $records = $new;
        
        foreach ($records as $k => $v) {
            $records[$k]['exp'] = json_decode($records[$k]['exp'], true);
            $exp = $records[$k]['exp'];
            is_array($exp) ? null : $exp = array();
            
            $str = '';
            foreach ($exp as $ks => $v1) {
                
                $str .= implode(' ', $v1) . '<br>';
            }
            $records[$k]['exp'] = $str;
        }
        
        if ($isDownloadExcel) {
            return $this->downExcel($records, array_keys($header));
        } else {
            
            $this->_view->assign('where', $where);
            $this->_view->assign('pager', $pager);
            $this->_view->assign('header', $header);
            $this->_view->assign('rs', $records);
        }
    }

    public function updAction()
    {
        $where = \Prj\Misc\View::decodePkey($_REQUEST['_pkey_val_']);
        $where = $where['feedbackId'];
//        var_log($where, __FUNCTION__ . '>>>$where>>>>>>>');
        $formEdit = \Sooh\Base\Form\Broker::getCopy('default')->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_u);
        $formEdit->addItem('feedbackId', form_def::factory('反馈Id', '', form_def::constval))
            ->addItem('userId', form_def::factory('用户Id', '', form_def::constval)/*->initMore(new options_def($this->copartners), form_def::constval)*/)
            ->addItem('content', form_def::factory('内容', '', form_def::constval))
            ->addItem('status', form_def::factory('状态', 'cpc', form_def::select)->initMore(new \Sooh\Base\Form\Options(array(
            '0' => '未读',
            '1' => '已读',
            '2' => '已处理'
        ), '全部')))
            ->addItem('exp', form_def::factory('备注', '', form_def::constval))
            ->addItem('message', form_def::factory('追加备注', '', form_def::text));
        $loginName = $this->manager->getField('loginName');
        $lastYmd = $this->manager->getField('lastYmd');
        $lastYmd = date('Y-m-d', $lastYmd);
        $this->_view->assign('FormOp', $op = '更新');
        $formEdit->fillValues();
        
        if ($formEdit->flgIsThisForm) {
            try {
                $fields = $formEdit->getFields();
                
                $fields['exp'] = explode(' ', $fields['exp']);
//                var_log($fields['exp'], 'fields>>>>>');
                $fields['exp'] = array(
                    $fields['exp']
                );
                $mix = array(
                    'lastYmd' => '(' . $lastYmd . ')',
                    
                    'loginName' => $loginName . '&nbsp' . ':' . '<br>',
                    
                    'message' => $fields['message']
                );
                
                unset($fields['message']);
                
                $fields['exp'][

                ] = $mix;
                
                $fields['exp'] = json_encode($fields['exp']);
                
                $feedbackId = $fields['feedbackId'];
                
                $feedback = \Prj\Data\Feedback::getCopy($feedbackId);
                
                $pkey = $feedback->load();
                
                if ($pkey === null) {
                    // $this->returnError('不能更新，无相关记录');
                    $this->returnError(\Prj\Lang\Broker::getMsg('feedback.upd_not_record'));
                    return;
                }
                foreach ($fields as $k => $v) {
                    $feedback->setField($k, $v);
                }
                $feedback->update();
                $this->returnOK($op . '成功');
                $this->closeAndReloadPage($this->tabname('index'));
            } catch (\ErrorException $e) {
                if (\Sooh\DB\Broker::errorIs($e, \Sooh\DB\Error::duplicateKey)) {
                    $this->returnError($op . '失败：冲突，相关记录已经存在？');
                } else {
                    $this->returnError($op . '失败：' . $e->getMessage());
                }
            }
        } else {
            $feedback = \Prj\Data\Feedback::getCopy($where);
            
            $pkey = $feedback->load();
            
            if ($pkey === null) {
                // $this->returnError('记录找不到');
                $this->returnError(\Prj\Lang\Broker::getMsg('feedback.record_unfound'));
            } else {
                $ks = array_keys($formEdit->items);
                foreach ($ks as $k) {
                    if ($feedback->exists($k)) {
                        if($k=='content'){
                            $content=$feedback->getField($k);
                            if(json_decode($content)!==null){
                                $formEdit->item($k)->value=htmlspecialchars(json_decode($content));
                            }else{
                                $formEdit->item($k)->value=htmlspecialchars($content);
                            }
                        }
                        else{
                        $formEdit->item($k)->value = $feedback->getField($k);
                        }
                        if ($k == 'exp') {
                            
                            $exp = $formEdit->item($k)->value;
                            
                            foreach ($exp as $kk => $v) {
                                $exp = implode(' ', $v) . '<br>';
                                
                                $ext[] = $exp;
                            }
                            $exp = $ext;
                            $exp = implode('', $exp);
                            
                            $formEdit->item($k)->value = $exp;
                        }
	                     
	                }
	            }
	        }
	    }
	     
	}
}