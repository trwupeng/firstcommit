<?php
use Sooh\Base\Form\Item as form_def;
/**
 * 
 * 
 * @author wupeng
 *
 */
class LogquiryController extends \Prj\ManagerCtrl {
    
    
    protected $evtEnum = [
    ];
    
    protected $pageSizeEnum = array(10, 50, 100);
    public function indexAction () {
        
        $pageId = $this->_request->get('pageId', 1)-0;
        $pageSize = $this->_request->get('pageSize',50) - 0;
// var_log($pageSize, 'pageSize>>>>>>> 1 >>>>>>>>.');        
        $pager = new \Sooh\DB\Pager(50,[50], false);
       // $pager->init(-1, $pageId);
    
        $frm = \Sooh\Base\Form\Broker::getCopy('default')
        ->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_s);
        $frm->addItem('ymd', form_def::factory('日期', date('Y-m-d'), form_def::datepicker, [], ['data-rule' => 'required']))
            ->addItem('deviceId', form_def::factory('设备号', '', form_def::text))
           // ->addItem('phone', form_def::factory('手机号码', '', form_def::text, [], ['data-rule' => 'digits']))
            ->addItem('pageid', $pageId)
           ->addItem('pagesize', $pageSize);
        $frm->fillValues();
        
        if ($frm->flgIsThisForm) {
            $fields = $frm->getFields();
            if (!empty($fields['deviceId'])) {

                $where=['deviceId'=>$fields['deviceId']];
                   
                $headerFields=[
                    'ymd'=>['时间','60'],
                    'ip'=>['ip','60'],
                    'target'=>['管理员','60'],
                    'evt'=>['工作内容','90'],
                ];
                
                foreach ($headerFields as $k => $v) {
                    $headers[$v[0]] =$v[1];
                     
                }
                
               // var_log($where,'>.>>>>>>>>>>>>>>>');
                $deviceId=$where['deviceId'];
               // var_log($deviceId,'1111111111');
                
                $new=[];
                $recd=[];
                $records=[];
                $db = \Sooh\DB\Broker::getInstance('default');
                $recd = $db->getRecords('db_logs' . '.tblog__a_0', 'userId,ymd,ip,target,evt,hhiiss,ext',['deviceId'=>$deviceId],'rsort ymd rsort hhiiss');
//                 foreach ($recd as $v){
//                     $pager->init(count($recd),$pageId);
//                 }
                //var_log($recd,'>>>>>>>>>>>>>>');
               // var_log(\Sooh\DB\Broker::lastCmd(),'sql>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>');
             
                foreach ($recd as $v){
                    $v['ymd']= \Prj\Misc\View::fmtYmd($v['ymd'].sprintf("%06d",$v['hhiiss']), 'time');
                    $userId=$v['userId'];
                    $ext=$v['ext'];
                  if(!empty($ext)&&$userId!='trans'&&$userId!='confirm'&&$userId!='returnFund'&&$userId!='delayConfirm'){
                           $ext=explode('@', $ext);
                       
                            $loginName=$ext[0];
                            $cameForm=$ext[1];
                            $manager=\Prj\Data\Manager::getCopy(['loginname'=>$loginName],['cameForm'=>$cameForm]);
                            $manager->load();
                            $nick=$manager->getField('nickname');
                            $v['target']=$nick;
                        
                    }
                    elseif(!empty($userId)&&$userId!='trans'&&$userId!='confirm'&&$userId!='delayConfirm'&&$userId!='returnFund'&&$userId!='46053455160237'){
                        $manager=\Prj\Data\Manager::getCopy(['loginname'=>$userId],['cameForm'=>'local']);
                        $manager->load();
                        $nickname=$manager->getField('nickname');
                        $v['target']=$nickname;
                    }
                    elseif(!empty($userId)||$userId=='trans'||$userId=='confirm'||$userId=='returnFund'||$userId=='delayConfirm'&&$userId!='46053455160237'){
                        $v['target']=$nickname;
                    }else{
                        $v['target']='管理员登录失败';
                    }
                    unset($v['ext']);
                    unset($v['userId']);
                    unset($v['hhiiss']);
                    $new[]=$v;
                }
                 $records=$new;
                 
                
                $this->_view->assign('records', $records);
                $this->_view->assign('headers', $headers);
            }
            else{
                $fieldsMap=[

                    'ymd'=>['时间','60'],
                  //  'hhiiss'=>['hhiiss','80'],
                    'ip'=>['ip','60'],
                    'target'=>['管理员','60'],
                    'evt'=>['工作内容','90'],
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
                $new = $db->getRecords('db_logs' . '.tblog__a_0' , 'userId,ymd,ip,target,evt,hhiiss,ext',['ymd'=>$where],'rsort ymd rsort hhiiss');
                
                foreach ($new as $v){
                    $pager->init(count($new),$pageId);
                }
                
               // var_log(\Sooh\DB\Broker::lastCmd(),'sql>>>>>>>>>>>>>>>>>>>>>');

                foreach ($new as $v){
                    $v['ymd']= \Prj\Misc\View::fmtYmd($v['ymd'].sprintf("%06d",$v['hhiiss']), 'time');
                    $userId=$v['userId'];
                    $ext=$v['ext'];
                   
                    if(!empty($ext)&&$userId!='trans'&&$userId!='confirm'&&$userId!='returnFund'&&$userId!='delayConfirm'){
                           $ext=explode('@', $ext);
                       
                            $loginName=$ext[0];
                            $cameForm=$ext[1];
                            $manager=\Prj\Data\Manager::getCopy(['loginname'=>$loginName],['cameForm'=>$cameForm]);
                            $manager->load();
                            $nick=$manager->getField('nickname');
                            $v['target']=$nick;
                        
                    }
                    elseif(!empty($userId)&&$userId!='trans'&&$userId!='confirm'&&$userId!='delayConfirm'&&$userId!='returnFund'&&$userId!='46053455160237'){
                        $manager=\Prj\Data\Manager::getCopy(['loginname'=>$userId],['cameForm'=>'local']);
                        $manager->load();
                        $nickname=$manager->getField('nickname');
                        $v['target']=$nickname;
                    }
                    elseif(!empty($userId)||$userId=='trans'||$userId=='confirm'||$userId=='returnFund'||$userId=='delayConfirm'&&$userId!='46053455160237'){
                        $v['target']=$nickname;
                    }else{
                        $v['target']='管理员登录失败';
                    }
                 
                    unset($v['ext']);
                    unset($v['userId']);
                    unset($v['hhiiss']);
                    $old[]=$v;
                }
                
                $records=$old;
                $this->_view->assign('records', $records);
                $this->_view->assign('headers', $headers);
            }
           // var_log($records,'>>>>>>>>>>>>>>>>>>>>>>');
            //var_log(\Sooh\DB\Broker::lastCmd(),'sql>>>>>>>>>>>>>>>>>>>>>>>>>');
           
        }
            $this->_view->assign('pager', $pager);

    }

}