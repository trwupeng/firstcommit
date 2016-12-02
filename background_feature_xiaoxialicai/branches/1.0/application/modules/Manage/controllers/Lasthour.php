<?php
use Sooh\Base\Form\Item as form_def;
use Sooh\Base\Form\Options as options_def;
/**
 * 最近一个小时 注册购买人数
 * 
 * @author li.lianqi
 *
 */
class LasthourController extends \Prj\ManagerCtrl {
    public function init() {
        parent::init();  
        for($h=5; $h<=24; $h++) {
            $this->hour[$h] =$h;
        }  
    }
    protected $hour;
    public function indexAction() {
        
        $formObj = \Sooh\Base\Form\Broker::getCopy('default')
            ->init(\Sooh\Base\Tools::uri(),'get',\Sooh\Base\Form\Broker::type_s);
        $formObj->addItem('_hour_eq', form_def::factory('最近几个小时', '5', form_def::select)->initMore(new \Sooh\Base\Form\Options($this->hour)));
        $formObj->fillValues();
        if ($formObj->flgIsThisForm) {
            $where = $formObj->getWhere();
            $hour = $where['hour='];
        }else {
            $hour = 5;
        }
        $time = time();
        $o = \Prj\Data\User::getCopy();
        $b = \Prj\Data\Investment::getCopy();
        $records = [];
        for ($i = 0; $i<= $hour-1; $i++){
            $t = $time-$i*3600;
            $ymdH = date('YmdH', $t);
            $ymd = substr($ymdH, 0, 8);
            $h = substr($ymdH, -2);
            $where = ['ymdReg'=>$ymd, 'hisReg]'=>$h.'0000', 'hisReg['=>$h.'5959'];
            $regCount = $o->loopGetRecordsCount($where);
            
            $where = ['orderTime]'=>$ymdH.'0000','orderTime['=>$ymdH.'5959','orderStatus!'=>\Prj\Consts\OrderStatus::$unsuccessful];
            $arr_userId = $b->loopFindRecordsByFields($where, null, 'userId', 'getCol');
            $numUserBought = sizeof(array_unique($arr_userId));
            $records[] = ['date'=>date('Y年m月d日 H点', $t),'regCount'=>$regCount, 'numUserBought'=>$numUserBought];
        }
        $o->free();
        $b->free();
        $this->_view->assign('records', $records);
        $this->_view->assign('headers', $this->header);
        
        
    }
    
    protected $header = [
        '时间'       =>80,
        '注册人数'     => 40,
        '购买人数'     =>　40,
    ];
    
    
}