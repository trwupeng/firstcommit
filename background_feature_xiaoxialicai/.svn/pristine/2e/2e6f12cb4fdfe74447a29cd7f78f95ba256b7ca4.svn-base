<?php
/**
 *
 *@author wu.peng
 *
 */
use Sooh\Base\Form\Item as form_def;

class ConfigmanagementController extends \Prj\ManagerCtrl {
  
    protected  $tb_activeconfig='db_p2prpt.tb_activeconfig';
    protected  $tb_activegroup='db_p2prpt.tb_activegroup';
    
    protected  function  getDB(){
        return \Sooh\DB\Broker::getInstance();
    }
    
    
    public function createAction(){
        
        function array_multi2single($v){
            static $result_array=array();
            foreach($v as $value){
                if(is_array($value)){
                     
                    array_multi2single($value);
                     
                }
                else
                    $result_array[]=$value;
            }
            return $result_array;
        }
        
       $where=[];
       $arr_Code=$this->getDB()->getRecords($this->tb_activegroup,'Code',$where);
       var_log($arr_Code,'11111111111>>>>>>>>>');
       var_log(\Sooh\DB\Broker::lastCmd(),'sql>>>>>>>>>>>>>>>');
       
       $arr_Code=array_multi2single($arr_Code);
       //var_log($arr_Code,'arr>>>>>>>>>');
       $arr_Code=array_reverse($arr_Code);
     
        $form = \Sooh\Base\Form\Broker::getCopy('default')
        ->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_c);
        
        $form->addItem('code', form_def::factory('code','',form_def::text))
             ->addItem('value',form_def::factory('起止时间值','',form_def::datepicker))
             ->addItem('value1',form_def::factory('值[单位，分](与起止时间值二填一)','', form_def::text))
             ->addItem('groupCode',form_def::factory('分组Code','', form_def::select)->initMore(new \Sooh\Base\Form\Options($arr_Code)))
             ->addItem('des',form_def::factory('描述','', form_def::text));
        
        $form->fillValues();
        
        if($form->flgIsThisForm){
            
            $fields = $form->getFields();
            $Code=$fields['groupCode'];
            //var_log($Code,'arr>>>>>>>>>');
            foreach ($arr_Code as $k=>$v){
                if($k==$Code){
                    $fields['groupCode']=$v;
                }
            }
            
            if(!empty($fields)){
                
                try{
                    \Sooh\DB\Broker::errorMarkSkip();
                    if(!empty($fields['value'])){
                    $r = [
                        'code'=>$fields['code'],
                        'value'=>strtotime($fields['value']),
                        'groupCode'=>$fields['groupCode'],
                        'des'=>$fields['des'],
                      
                    ];
                    }else{
                        $r = [
                            'code'=>$fields['code'],
                            'value'=>$fields['value1'],
                            'groupCode'=>$fields['groupCode'],
                            'des'=>$fields['des'],
                        
                        ];
                    }
                    $ret=$this->getDB()->addRecord($this->tb_activeconfig,$r);
                    $this->closeAndReloadPage($this->tabname('index'));
                    return $this->returnOK('添加成功');
                
                }catch(\ErrorException $e) {
                    return $this->returnError('添加失败');
                }
                
             
             
            }
        }
              
    }
    
    
    public  function indexAction(){
        
        $pageid = $this->_request->get('pageId', 1) - 0;
        $pagesize = $this->_request->get('pageSize', current($this->pageSizeEnum)) - 0;
        $pager = new \Sooh\DB\Pager($pagesize, $this->pageSizeEnum, false);
        $isDownloadExcel = $this->_request->get('__EXCEL__') == 1;
        
        $form = \Sooh\Base\Form\Broker::getCopy('default')
        ->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_s);
        $form -> addItem('_code_eq', form_def::factory('code', '',form_def::text))
        ->addItem('_value_eq', form_def::factory('值', '', form_def::text))
        ->addItem('_groupCode_eq', form_def::factory('分组Code', '', form_def::text))
        ->addItem('_des_eq', form_def::factory('描述', '', form_def::text))
        ->addItem('pageid', $pageid)
        ->addItem('pagesize', $pager->page_size);
        $form->fillValues();
        
        if($form->flgIsThisForm) {
            $where = $form->getWhere();
        }else {
            $where = [];
        }
        $pager->init(-1, $pageid);
        
        $fieldsMapArr = [
           'code'=>['code',50],
           'value'=>['值',50],
            'groupCode'=>['分组Code',50],
            'des'=>['描述',120],
        ];
        
        $header = [];
        foreach ($fieldsMapArr as $k => $v) {
            $header[$v[0]] = $v[1];
        }
        
        $tmp = [];
        $rs=[];
        $tmp = $fieldsMapArr;
        $pager->total=$this->getDB()->getOne($this->tb_activeconfig,'count(*)',$where);

        if($isDownloadEXCEL){
          $records=$this->getDB()->getRecords($this->tb_activeconfig,implode(',', array_keys($tmp)),$where); 
        }else {
            $records=$this->getDB()->getRecords($this->tb_activeconfig,implode(',', array_keys($tmp)),$where,null,$pagesize, $pager->rsFrom());
        }
        
        
        $temp=[];
        foreach ($records as $v){
            foreach ($fieldsMapArr as $kk=>$vv){
                if($kk=='value'){
                    if(strlen($v[$kk])<10 && strlen($v[$kk])>4){
                        $temp[$kk]=$v[$kk]/100;
                    }elseif(strlen($v[$kk])==4){
                        $temp[$kk]=$v[$kk];
                    }
                     elseif(strlen($v[$kk])==1){
                         $temp[$kk]=$v[$kk];
                     }   
                    else{
                     $temp[$kk]=\Prj\Misc\View::fmtYmd($v[$kk]);
                    }
                }else{
                $temp[$kk]=$v[$kk];
                } 
            }
            if (! $isDownloadExcel) {
                $temp['_pkey_val_'] = \Prj\Misc\View::encodePkey([
                    'code' => $v['code']
                ]);
            $new[]=$temp;
        }

       $records=$new;
       
      if($isDownloadExcel){
           return $this->downExcel($records,array($header));
       }else{
           $this->_view->assign('where',$where);
           $this->_view->assign('pager',$pager);
           $this->_view->assign('header',$header);
           $this->_view->assign('rs',$records);
            
       }
    }
 }
    
    public function delAction(){
        
        $where = \Prj\Misc\View::decodePkey($this->_request->get('_pkey_val_'));
        $where=$where['code'];
        if (empty($where)) {
            return $this->returnError('not found');
        } else {
            try {
                $sql = "DELETE  FROM {$this->tb_activeconfig} WHERE code='$where'";
                $res = $this->getDB()->execCustom(['sql' => $sql]);
                
                
            } catch (\ErrorException $e) {
                return $this->returnError($e->getMessage());
            }
        }
        
        return $this->returnOK('删除成功');
    }
    
    public function  editAction(){
     $where = \Prj\Misc\View::decodePkey($this->_request->get('_pkey_val_'));
     $where=$where['code'];

      
     $formEdit = \Sooh\Base\Form\Broker::getCopy('default')->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_u);
     
     $formEdit
       ->addItem('code',form_def::factory('code', '', form_def::text))
       ->addItem('value',form_def::factory('值', '', form_def::text))
       ->addItem('groupCode',form_def::factory('分组Code', '', form_def::text))
       ->addItem('des',form_def::factory('描述', '', form_def::text));
     
     $this->_view->assign('FormOp', $op = '更新');
     $formEdit->fillValues();
     
     if($formEdit->flgIsThisForm) {
         $fields = $formEdit->getFields();
         $code=$fields['code'];
         if(strlen($fields['value'])==10){
             $fields['value']=strtotime($fields['value']);
         }elseif(strlen($fields['value'])>3){
             $fields['value']=$fields['value']*100;
         }
         if(!empty($fields) && !empty($code)){
             $ret=$this->getDB()->updRecords($this->tb_activeconfig,$fields,['code'=>$code]);
         }
        // var_log(\Sooh\DB\Broker::lastCmd(),'sql>>>>>>>>>>>');
         $this->returnOK($op . '成功');
         $this->closeAndReloadPage($this->tabname('index'));
         
         
        }else {
            $ret=$this->getDB()->getRecords($this->tb_activeconfig,'*',['code'=>$where]);
             
            if(!empty($ret)){
                if(!empty($ret)){
                    $ks=array_keys($formEdit->items);
                    foreach ($ks as $k){
                        foreach ($ret as $v){
            
                            if($k=='code'){
                                $formEdit->item($k)->value=$v[$k];
                            }elseif($k=='groupCode'){
                                $formEdit->item($k)->value=$v[$k];
                            }elseif($k=='value'){
                                if($v[$k]=='1'){
                                    $formEdit->item($k)->value=$v[$k];
                                }elseif($v[$k]=='0'){
                                    $formEdit->item($k)->value=$v[$k];
                                }elseif (strlen($v[$k])<10 && strlen($v[$k])>1){
                                    $formEdit->item($k)->value=($v[$k]/100);
                                }elseif(strlen($v[$k])=='10'){
                                    $formEdit->item($k)->value=\Prj\Misc\View::fmtYmd($v[$k]);
                                }
                            }elseif ($k=='des'){
                                $formEdit->item($k)->value=$v[$k];
                            }
            
                        }
                    }
                }
        }
       
     }
     
    
        
    }
}
         









