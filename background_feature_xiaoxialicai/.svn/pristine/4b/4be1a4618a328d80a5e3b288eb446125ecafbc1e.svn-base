<?php
/**
 * 标的模板
 * By Hand
 */
class BankController extends \Prj\ManagerCtrl
{
    /**
     * @var \Sooh\DB\Pager $_pager
     */
    protected $_pager;
    protected static $_model = '\Prj\Data\Bank';
    protected static $_pk = ['bank',true];
    protected static $_head = [];
    protected static $_format = [
        'money' => [
            'chargeFirst','chargeSingle','chargeDay','withdawSingle','withdawDay','chargeAtLeast'
        ],
        'require' => [
            'bank','name'
        ]
    ];

    protected static function _getHead(){
        return [
            'bank' => ['bank','银行ID','','text'],
            'name' => ['name','名称','','text'],
            'icon' => ['icon','图标','','upload'],
            'chargeFirst' => ['chargeFirst','首充限额(元)',0,'text'],
            'chargeSingle' => ['chargeSingle','单笔充值限额(元)',0,'text'],
            'chargeDay' => ['chargeDay','日充值限额(元)',0,'text'],
            'withdawSingle' => ['withdawSingle','单笔提现限额(元)',0,'text'],
            'withdawDay' => ['withdawDay','日提现限额(元)',0,'text'],
            'intercept' => ['intercept','客户端是否拦截用户绑卡',0,'select',[0=>'否',1=>'是']],
            'chargeAtLeast' => ['chargeAtLeast','最低充值额度(元)',0,'text'],
        ];
    }

    public function init(){
        parent::init();
        $this->_pager = new \Sooh\DB\Pager($this->_request->get('pageSize', 10), $this->pageSizeEnum, false);
    }

    protected function _dataCheck($record){
        foreach($record as $k => $v){
            if(in_array($k,self::$_format['money']) && $v < 0){
                throw new \ErrorException('金额不能为负值',400);
            }
            if(in_array($k,self::$_format['require']) && empty($v)){
                throw new \ErrorException(self::_getHead()[$k][1].'不能为空',400);
            }
        }
    }

    protected function _dataFormat($record , $type = 'SELECT'){
        $type = strtoupper($type);
        switch($type){
            case 'SELECT' : {
                foreach($record as $key => $value){
                    if(in_array($key,self::$_format['money'])){
                        $record[$key] /= 100;
                    }
                }
                break;
            }
            case 'UPDATE' : {
                foreach($record as $key => $value){
                    if(in_array($key,self::$_format['money'])){
                        $record[$key] *= 100;
                    }
                }
                break;
            }
            default : throw new \ErrorException(__METHOD__.' error type');
        }
        return $record;
    }

    public function indexAction () {
        /**
         * @var \Prj\Data\BaseFK $model
         */
        $model = self::$_model;
        $view = new \Prj\Misc\ViewFK();
        //配置分页
        $pageid = $this->_request->get('pageId', 1) - 0;
        $this->_pager->init(-1,$pageid);
        $data = $model::paged($this->_pager,[]);
        foreach($data as $k=>$v){
            $data[$k] = $this->_dataFormat($v);
        }
        $view->setPk(self::$_pk[0],self::$_pk[1])->setData($data)->setPager($this->_pager)->setAction(\Sooh\Base\Tools::uri(['__VIEW__'=>'json'],'update'),\Sooh\Base\Tools::uri(['__VIEW__'=>'json'],'delete'));

        $head = self::$_head?self::$_head:(self::_getHead());
        foreach($head as $v){
            $view->addRow($v[0],$v[1],$v[2],$v[3],$v[4]);
        }

        $this->_view->assign('view',$view);
        $this->_view->assign('_type',$this->_request->get('_type'));
    }

    public function updateAction(){
        $model = self::$_model;
        $pk = self::$_pk[0];
        $input = $this->_request->get('values')[0];
        $id = $this->_request->get($pk);
        if(empty($id)){
            //this is add
            if(empty($input[$pk])){
                return $this->returnError('银行ID不能为空');
                $input[$pk] = time().rand(1000,9999);
            }
            $dataObj = $model::getCopy($input[$pk]);
            $dataObj->load();
            if($dataObj->exists())return $this->returnError('该记录已存在');
        }else{
            //this is update
            $dataObj = $model::getCopy($id);
            $dataObj->load();
            if(!$dataObj->exists())return $this->returnError('不存在的记录');
        }

        $input = $this->_dataFormat($input , 'UPDATE');
        try{
            $this->_dataCheck($input);
        }catch (\ErrorException $e){
            return $this->returnError($e->getMessage());
        }

        foreach($input as $k => $v){
            if($k == self::$_pk[0] && !$input[$pk]){

            }else{
                $dataObj->setField($k,$v);
            }
        }

        try{
            $dataObj->update();
        }catch (\ErrException $e){
            return $this->returnError($e->getMessage());
        }
        $this->_view->assign('_id',$input[$pk]?$input[$pk]:$id);
        return $this->returnOK();
    }

    public function deleteAction(){
        /**
         * @var \Prj\Data\BaseFK $model
         */
        $model = self::$_model;
        $id = $this->_request->get('_id');
        if($this->_request->get('_type') == 'new'){
            return $this->returnOK();
        }
        if(empty($id))return $this->returnError('主键不能为空');
        $right =$model::getCopy($id);
        $right->load();
        if(!$right->exists())return $this->returnError('该记录已经被删除');
        try{
            $right->delete();
        }catch (\ErrException $e){
            return $this->returnError($e->getMessage());
        }
        return $this->returnOK();
    }

    public function uploadAction(){
        $fileArr = $_FILES['file'];
        try{
            $ret = \Prj\Wares\Img::uploadImg($fileArr,'bank_');
        }catch (\ErrorException $e){
            return $this->returnError($e->getMessage());
        }
        $this->_view->assign('filename','');
        $this->_view->assign('ret',$ret);
        return $this->returnOK();
    }
}