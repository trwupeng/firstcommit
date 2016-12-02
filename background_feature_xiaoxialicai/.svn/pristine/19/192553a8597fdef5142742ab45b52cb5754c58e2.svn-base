<?php
use Sooh\Base\Form\Item as form_def;
/**
 * 
 * 
 * @author wupeng
 *
 */
class LogqueryController extends \Prj\ManagerCtrl {
    
    
    protected $pageSizeEnum = array(50, 100, 500);
    public function indexAction () {
        
        $pageid = $this->_request->get('pageId', 1)-0;
        $pagesize = $this->_request->get('pageSize', current($this->pageSizeEnum)) - 0;
        $pager = new \Sooh\DB\Pager($pagesize,$this->pageSizeEnum, false);
      
        $frm = \Sooh\Base\Form\Broker::getCopy('default')
        ->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_s);
        $frm->addItem('ymd', form_def::factory('日期', date('Y-m-d'), form_def::datepicker, [], ['data-rule' => 'required']))
            ->addItem('deviceId', form_def::factory('设备号', '', form_def::text))
           // ->addItem('phone', form_def::factory('手机号码', '', form_def::text, [], ['data-rule' => 'digits']))
            ->addItem('pageId', $pageid)
           ->addItem('pageSize',$pagesize);
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
                
             
                $deviceId=$where['deviceId'];
                $ymd = date('Ymd', strtotime($fields['ymd']));
                $where['ymd=']=$ymd;
                $new=[];
                $recd=[];
                $records=[];
                $db = \Sooh\DB\Broker::getInstance('default');
                $redcount=$db->getRecords('db_logs' . '.tblog__a_0' ,'userId,ymd,ip,target,evt,hhiiss,ext', $where);
                $pager->init(count($redcount), $pageid);
                $recd = $db->getRecords('db_logs' . '.tblog__a_0', 'userId,ymd,ip,target,evt,hhiiss,ext',$where,'rsort ymd rsort hhiiss',$pagesize, $pager->rsFrom());
                  
               
               
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
                        $v['target']=$nickname;
                        $v['evt']=$v['evt'].'操作超时';
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
                $ymd = date('Ymd', strtotime($fields['ymd']));
                $where[]=$ymd;
                
                $db = \Sooh\DB\Broker::getInstance('default');
                $redcount=$db->getRecords('db_logs' . '.tblog__a_0' ,'userId,ymd,ip,target,evt,hhiiss,ext', ['ymd'=>$where]);
                $pager->init(count($redcount), $pageid);
               
                $new = $db->getRecords('db_logs' . '.tblog__a_0' , 'userId,ymd,ip,target,evt,hhiiss,ext',['ymd'=>$where],'rsort ymd rsort hhiiss',$pagesize, $pager->rsFrom());
                //var_log(\Sooh\DB\Broker::lastCmd(),'sql>>>>>>>>>>>>>>>>>>>>>');
              
                
                
                
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
                    }
                    else{
                        $v['target']=$nickname;
                        $v['evt']=$v['evt'].'操作超时';
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
          
        }
        $this->_view->assign('pager', $pager);
          
    }

}