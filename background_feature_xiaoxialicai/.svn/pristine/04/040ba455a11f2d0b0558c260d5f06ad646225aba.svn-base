<?php
/**
 *
 *@author wu.peng
 *
 */
use Sooh\Base\Form\Item as form_def;

class GroupmanagementController extends \Prj\ManagerCtrl {
  
    protected  $tb_activegroup='db_p2prpt.tb_activegroup';
    protected  $tb_activeconfig='db_p2prpt.tb_activeconfig';
    
    protected  function  getDB(){
        return \Sooh\DB\Broker::getInstance();
    }
 
    public function createAction(){
        
        $form = \Sooh\Base\Form\Broker::getCopy('default')
        ->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_c);
        
        $form ->addItem('Code', form_def::factory('分组Code','',form_def::text))
             ->addItem('desc',form_def::factory('描述','', form_def::text));
            
        
        $form->fillValues();
        
        if($form->flgIsThisForm){
            
            $fields = $form->getFields();
            
            if(!empty($fields)){
                
                try{
                    \Sooh\DB\Broker::errorMarkSkip();
                    $r = [
                        //'taskId'=>$fields['taskId'],
                        'Code'=>$fields['Code'],
                        'des'=>$fields['desc'],
                    ];
                    $ret=$this->getDB()->addRecord($this->tb_activegroup,$r);
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
        $ids = $this->_request->get('ids');
        
        $form = \Sooh\Base\Form\Broker::getCopy('default')
        ->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_s);
        $form -> addItem('_Code_eq', form_def::factory('分组Code', '',form_def::text))
        ->addItem('_desc_eq', form_def::factory('描述', '', form_def::text))
        ->addItem('pageid', $pageid)
        ->addItem('pagesize', $pager->page_size);
        $form->fillValues();
        
        if($form->flgIsThisForm) {
            $where = $form->getWhere();
        }else {
            $where = [];
        }
        $pager->init(-1, $pageid);
        
        $keys = is_array($ids) ? $ids : explode(',', $ids);
        
        if (! empty($ids)) {
            foreach ($keys as $k => $v) {
                $keys[$k] = \Prj\Misc\View::decodePkey($v)['Code'];
            }
            $where = array(
                'Code' => $keys
            );
        }
       
        
        $fieldsMapArr = [
           //'taskId'=>['分组ID',50],
            'Code'=>['Code',70],
            'des'=>['描述',120],
        ];
        
        $header = [];
        foreach ($fieldsMapArr as $k => $v) {
            $header[$v[0]] = $v[1];
        }
        
        $tmp = [];
        $rs=[];
        $tmp = $fieldsMapArr;
        $pager->total=$this->getDB()->getOne($this->tb_activegroup,'count(*)',$where);

        if($isDownloadEXCEL){
          $records=$this->getDB()->getRecords($this->tb_activegroup,implode(',', array_keys($tmp)),$where); 
        }else {
            $records=$this->getDB()->getRecords($this->tb_activegroup,implode(',', array_keys($tmp)),$where,null,$pagesize, $pager->rsFrom());
        }
        
        
        $temp=[];
        foreach ($records as $v){
  
            foreach ($fieldsMapArr as $kk=>$vv){
                $temp[$kk]=$v[$kk];
              
            }
            if (! $isDownloadExcel) {
                $temp['_pkey_val_'] = \Prj\Misc\View::encodePkey([
                    'Code' => $v['Code']
                ]);
            }
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

    public  function editAction(){
        
         $where = \Prj\Misc\View::decodePkey($_REQUEST['_pkey_val_']);
         //var_log($where,'>>>>>>>>>>>');
         $where1=$where['Code'];
         
         $formEdit = \Sooh\Base\Form\Broker::getCopy('default')->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_u);

         
        $ret=$this->getDB()->getRecords($this->tb_activeconfig,'*',['groupCode'=>$where1]);
        
        $this->_view->assign('rs',$ret);
        
       
      
    }
    
    public function delAction(){
    
        $where = \Prj\Misc\View::decodePkey($this->_request->get('_pkey_val_'));
        $where=$where['Code'];
        if (empty($where)) {
            return $this->returnError('not found');
        } else {
            try {
                $sql = "DELETE  FROM {$this->tb_activegroup} WHERE Code='$where'";
                $res = $this->getDB()->execCustom(['sql' => $sql]);
    
    
            } catch (\ErrorException $e) {
                return $this->returnError($e->getMessage());
            }
        }
    
        return $this->returnOK('删除成功');
    }
}












