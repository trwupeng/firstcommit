<?php
use Sooh\Base\Form\Item as form_def;
/**
 * 
 * 
 * @author li.lianqi
 *
 */
class LoginquiryController extends \Prj\ManagerCtrl {
    
    
    protected $evtEnum = [
    ];
    
    protected $pageSizeEnum = array(10, 50, 100);
    public function indexAction () {
        
        $pageId = $this->_request->get('pageId', 1)-0;
        $pageSize = $this->_request->get('pageSize',10) - 0;
// var_log($pageSize, 'pageSize>>>>>>> 1 >>>>>>>>.');        
        $pager = new \Sooh\DB\Pager(10, [10], false);
        $pager->init(-1, $pageId);
        
        $frm = \Sooh\Base\Form\Broker::getCopy('default')
        ->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_s);
        $frm->addItem('ymd', form_def::factory('日期', date('Y-m-d'), form_def::datepicker, [], ['data-rule' => 'required']))
            ->addItem('deviceId', form_def::factory('设备号', '', form_def::text))
            ->addItem('phone', form_def::factory('手机号码', '', form_def::text, [], ['data-rule' => 'digits']))
            ->addItem('pageid', $pageId)
           ->addItem('pagesize', $pageSize);
        $frm->fillValues();
        if ($frm->flgIsThisForm) {
            $fields = $frm->getFields();
            if (!empty($fields['deviceId']) || !empty($fields['phone'])) {


                $ymd = date('Ymd', strtotime($fields['ymd']));
               // $where['ymd'] = $ymd;
                if (!empty($fields['phone'])) {
                    $user = \Prj\Data\User::getCopy();
                    $r = $user->loopFindRecords(['phone' => $fields['phone']]);
                    if (!empty($r)) {
                        $userId = $r[0]['userId'];
                    }
                    $user->free();
                }

                if (!empty($fields['deviceId']) && !empty($userId)) {
					$where = $db->newWhereBuilder();
					$where->init('OR');
					$where->append('deviceId',$fields['deviceId']);
					$where->append('userId',$userId);
                } else if (!empty($fields['deviceId'])) {
                    $where = ['deviceId' => $fields['deviceId']];
                }
                
                 else {
                    $where = ['userId' => $userId];
                }
                   
                $headerFields=[
                    'ymd'=>['时间','60'],
                    'ip'=>['ip','60'],
                    'target'=>['管理员','60'],
                    'evt'=>['处理内容','90'],
                ];
                
                foreach ($headerFields as $k => $v) {
                    $headers[$v[0]] =$v[1];
                     
                }
                
                var_log($where,'>.>>>>>>>>>>>>>>>');
                $deviceId=$where['deviceId'];
                var_log($deviceId,'1111111111');
                
                $new=[];
                $recd=[];
                $records=[];
                $db = \Sooh\DB\Broker::getInstance('default');
                $recd = $db->getRecords('db_logs' . '.tblog__a_0', 'ymd,ip,target,evt,hhiiss',['deviceId'=>$deviceId]);
              
                var_log(\Sooh\DB\Broker::lastCmd(),'sql>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>');
                if (!empty($recd['clientType'])) {
                    $recd['clientType'] = \Prj\Consts\ClientType::clientTypes($recd['clientType']);
                }
                foreach ($recd as $v){
                    $v['ymd']= \Prj\Misc\View::fmtYmd($v['ymd'].sprintf("%06d",$v['hhiiss']), 'time');
                    unset($v['hhiiss']);
                    $new[]=$v;
                }
                 $records=$new;
                 
                
//                 $dbname = 'db_logs';
//                 $tbs = $db->getTables($dbname, 'tblog_' . $ymd . '%_a_%');

//                 $logGuid = [];
//                 $records = [];
              
//                 if (!empty($tbs)) {
//                     foreach ($tbs as $tb) {
//                         $tmp = $db->getPair($dbname . '.' . $tb, 'logGuid', 'hhiiss', $where);
//                         $logGuid += $tmp;
//                     }
//                     arsort($logGuid);


//                     $tmp = $db->execCustom(array('sql' => 'SHOW COLUMNS FROM ' . $dbname . '.' . $tb));
//                     $tmp = $db->fetchAssocThenFree($tmp);
//                     foreach ($tmp as $value) {
//                         $this->headerFields[$value['Field']] = strlen($value['Field']) * 6;
//                     }

//                 }
                
             
//                 if (!empty($logGuid)) {
//                    // $pager->init(sizeof($logGuid), $pageId);
//                     $logGuids = array_keys($logGuid);
//                    // var_log($logGuids,'>11111111111111111111111111>');
//                     $logGuids = array_slice($logGuids, ($pageId - 1) * $pageSize, $pageSize);
//                   //  var_log($logGuids,'>22222222222222222222222222222>');
                    
//                     foreach ($logGuids as $id) {
//                       // var_log($id,'1111111111111111111111111');
//                        // $n = ($id % 2 == 0) ? 0 : 1 ;
                     
//                        $recd = $db->getRecord('db_logs' . '.tblog__a_0', 'ymd,ip,userId,evt', array('logGuid'=>$id),$pager->page_size);
                
//                        var_log($recd,'>>>>>>>>>>>>>>>>>>>>>>>');
//                       // var_log(\Sooh\DB\Broker::lastCmd(),'>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>');
//                         if (!empty($recd['clientType'])) {
//                             $recd['clientType'] = \Prj\Consts\ClientType::clientTypes($recd['clientType']);
//                         }
//                         $records[] = $recd;
//                     }
                    
//                 }
                
//                 $new=[];
//                 if(empty($fields['deviceId'])&&empty($fields['phone'])){
//                     	$db = \Sooh\DB\Broker::getInstance();
// 	                 	$new = $db->getRecords('db_logs.tb_a', '*',$ymd,'rsort ymd rsort his',$pager->page_siz);
// 	                 	if (!empty($new['clientType'])) {
// 	                 	    $new['clientType'] = \Prj\Consts\ClientType::clientTypes($new['clientType']);
// 	                 	}
// 	                 	$records[] = $new;
//                 }
                
                $this->_view->assign('records', $records);
                $this->_view->assign('headers', $headers);
            }
            else{
                $fieldsMap=[
//                    'logGuid'=>array('logGuid','80'),
//                     'deviceId' =>array('设备号','80'),
//                     'userId'=>array('用户id','80'),
//                     'isLogined'=>array('isLogined','80'),
//                     'opcount'=>array('opcount','80'),
//                     'clientType'=>array('clientType','50'),
//                     'clientVer'=>array('clientVer','80'),
//                     'contractId'=>array('contractId','80'),                   
//                     'evt'=>array('evt','50'),
//                     'mainType'=>array('mainType','40'),
//                     'subType'=>array('subType','50'),
//                     'target'=>array('target','60'),
//                     'num'=>array('num','80'),
//                     'ext'=>array('ext','80'),
//                     'ret'=>array('ret','40'),
//                     'narg1'=>array('narg1','80'),
//                     'narg2'=>array('narg2','80'),
//                     'narg3'=>array('narg3','80'),
//                     'sarg1'=>array('sarg1','80'),
//                     'sarg2'=>array('sarg2','80'),
//                     'sarg3'=>array('sarg3','80'),
//                     'ip'=>array('ip','50'),
//                     'ymd'=>array('日期','50'),
//                     'hhiiss'=>array('hhiiss','50'),
//                     'iRecordVerID'=>array('iRecordVerID','iRecordVerID')
                    'ymd'=>['时间','60'],
                  //  'hhiiss'=>['hhiiss','80'],
                    'ip'=>['ip','60'],
                    'target'=>['管理员','60'],
                    'evt'=>['处理内容','90'],
                ];
                
                foreach ($fieldsMap as $k => $v) {
                    $headers[$v[0]] =$v[1];
                 
                }
               // unset($headers['hhiiss']);
                $old=[];
                $new=[];
                $records=[];
               // $ymd = date('Ymd', strtotime($fields['ymd']));
                $ymd = date('Ymd', strtotime($fields['ymd']));
                $where[]=$ymd;
                $db = \Sooh\DB\Broker::getInstance('default');
                $new = $db->getRecords('db_logs' . '.tblog__a_0' , 'ymd,ip,target,evt,hhiiss',['ymd'=>$where],'',$pager->page_size);
                var_log($new,'>>>>>>>>>>>>>>>>>>>>>>>>>>>');
              
                if (!empty($new['clientType'])) {
                    $new['clientType'] = \Prj\Consts\ClientType::clientTypes($new['clientType']);
                }
                foreach ($new as $v){
                    $v['ymd']= \Prj\Misc\View::fmtYmd($v['ymd'].sprintf("%06d",$v['hhiiss']), 'time');
                    unset($v['hhiiss']);
                    $old[]=$v;
                }
                
                $records=$old;
                $this->_view->assign('records', $records);
                $this->_view->assign('headers', $headers);
            }
           // var_log($records,'>>>>>>>>>>>>>>>>>>>>>>');
            var_log(\Sooh\DB\Broker::lastCmd(),'sql>>>>>>>>>>>>>>>>>>>>>>>>>');
           
        }
            $this->_view->assign('pager', $pager);

    }
//     protected $headerFields=[
//         'ymd'=>['时间','80'],
//         'ip'=>['ip','60'],
//         'target'=>['管理员','80'],
//         'evt'=>['内容','90'],
//     ];
}