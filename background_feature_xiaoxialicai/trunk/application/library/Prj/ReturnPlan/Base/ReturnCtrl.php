<?php
namespace Prj\ReturnPlan\Base;
use Prj\Consts\ReturnType;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/10/23
 * Time: 17:56
 */
class ReturnCtrl {

    protected $content;
    protected $version = 0;
    protected $ordersId;
    public $calendar;
    public $dtStart;
    public $returnType;
    protected $total;
    protected $round = 0;  // 取整方法 0：不取整 1:向下取整 2:向上取整
    protected $formula; //公式

    protected function __construct($content)
    {
        if(is_string($content))
        {
            $this->content = json_decode($content,true);
        }
        else
        {
            $this->content = $content;
        }
        //var_log($content);
    }

    public function getFormula(){
        return $this->formula;
    }

    /**
     * 汇款成功，进入下一轮
     * @input array $ret['interest'] $ret['interestExt'] 当天回款信息
     * @input int $time 时间戳
     */
    public function ymdNext($ret,$time = '')
    {
        if(!is_array($ret))
        {
            $tmp = $ret;
            unset($ret);
            $ret['interest'] = $tmp;
        }
        $time = empty($time)?time():$time;
        $calendar = $this->calendar;
        foreach($calendar as $k=>$v)
        {
            if($v['isPay']==1)continue;
            $calendar[$k]['interest'] = $ret['interest']-0.0;
            if(empty($ret['interest']))return false;
            $calendar[$k]['interestExt'] = $ret['interestExt']-0.0;
            $calendar[$k]['isPay'] = 1;
            $calendar[$k]['realDateYmd'] = date('Ymdhis',$time);
            $this->calendar[$k] = $calendar[$k];
            return $k;
        }
        return false;
    }
    public function decode()
    {
		usort($this->calendar,function($a,$b){
            return $a['id']>=$b['id']?1:-1;
        });
        $arr = array(
            "version"=>$this->creatVersion(),
            "ordersId"=>$this->ordersId,
            "dtStart"=>date('Ymd',$this->dtStart),
            "returnType"=>$this->returnType,
            "total"=>$this->total ,
            "formula"=>$this->formula,
            "calendar"=>$this->calendar,
        );
        return $arr;
    }
    
    public function encode()
    {
        $this->version = $this->content['version'];
        $this->ordersId = $this->content['ordersId'];
        $this->dtStart = strtotime($this->content['dtStart']);
        $this->returnType = $this->content['returnType'];
        $this->total = $this->content['total'];
        $this->formula = $this->content['formula'];
        $this->calendar = $this->content['calendar'];

        $this->calendar = $this->calendar?$this->calendar:[];
        usort($this->calendar,function($a,$b){
            return $a['id']>=$b['id']?1:-1;
        });
       
    }

    /**
     * 下次还款日
     */
    public function getYmdNext()
    {
        $calendar = $this->calendar;
        foreach($calendar as $k=>$v)
        {
            if($v['isPay']==1)continue;
            return $v['planDateYmd'];
        }
        return 0;
    }

    protected function creatVersion()
    {
        return date('ymdhis').rand(1000,9999);
    }

    /**
     * 日期+N个月 输入时间戳 返回时间戳
     */
    protected function getNDate($date,$i=1)
    {
        $dtStart = $this->dtStart;
        if($dtStart<=0)return false;
        $dayInit = date('d',$dtStart); //记录日期中的几号
        $nextDate = strtotime($i." months", $date);
        $nextDateYmd = date('Y-m-d',$nextDate);
        list($year,$month,$day) = explode('-',$nextDateYmd);
        if($day<$dayInit) //如果天数少了  退一个月 日期用最后一天
        {
            $month--;
            $nextDateYmd = date('Y-m-t',mktime(0,0,0,$month,1,$year));
            $nextDate = strtotime($nextDateYmd);
        }
        return $nextDate;
    }

    /**
     * 获取项目总的天数
     * @param \Prj\Data\Wares $wares
     * @return int
     * @throws \ErrorException
     */
    protected function getDays(\Prj\Data\Wares $wares){
        $deadLine = $wares->getField('deadLine');
        if($wares->getField('dlUnit')=='月'){
            $endTime = $this->getNDate($this->dtStart,$deadLine);
            $days = ($endTime-$this->dtStart)/86400;
            return $days;
        }else if($wares->getField('dlUnit')=='天'){
            $days = $deadLine;
            return $days;
        }else{
            return 0;
        }
    }

    /**
     * 获取结束日期
     * @param \Prj\Data\Wares $wares
     * @return bool|int|string
     * @throws \ErrorException
     */
    public function getEndDay(\Prj\Data\Wares $wares){
        $deadLine = $wares->getField('deadLine');
        if($wares->getField('dlUnit')=='月'){
            $endTime = $this->getNDate($this->dtStart,$deadLine);
            return date("Ymd",$endTime);
        }else if($wares->getField('dlUnit')=='天'){
            $days = $deadLine;
            return date("Ymd",strtotime('+ '.$days.' days',$this->dtStart));
        }else{
            return 0;
        }
    }

    public function getEndDayByDeadLine($deadLine,$dlUnit){
        if($dlUnit=='月'){
            $endTime = $this->getNDate($this->dtStart,$deadLine);
            return date("Ymd",$endTime);
        }else if($dlUnit){
            $days = $deadLine;
            return date("Ymd",strtotime('+ '.$days.' days',$this->dtStart));
        }else{
            return 0;
        }
    }

    //取整方法
    protected function round($float)
    {
        switch($this->round)
        {
            case 0:
                return $float;
            case 1:
                return floor($float);
            case 2:
                return ceil($float);
            default:
                return $float;
        }
    }

    //获得某个月的计划
    public function getPlanByMonth($ymd)
    {
        $temp = array_filter($this->calendar,function($a) use ($ymd){
            return $a['planDateYmd']==$ymd;
        });
        return current($temp)?current($temp):[];
    }

    //
    public function getPlanById($id)
    {
        $temp = array_filter($this->calendar,function($a) use ($id){
            return $a['id']==$id;
        });
        return current($temp)?current($temp):[];
    }

    //修改计划里的字段
    public function updatePlanByMonth($key,$value,$ymd)
    {
        $temp = array_filter($this->calendar,function($a) use ($ymd){
            return $a['planDateYmd']==$ymd;
        });
        if(empty($temp))return false;
        $num = 0;
        foreach($temp as $k=>$v)
        {
            $this->calendar[$k][$key] = $value;
            $num++;
        }
        return $num;
    }

    //修改计划里的字段
    public function updatePlanById($key,$value,$id)
    {
        $temp = array_filter($this->calendar,function($a) use ($id){
            return $a['id']==$id;
        });
        if(empty($temp))return false;
        $num = 0;
        foreach($temp as $k=>$v)
        {
            $this->calendar[$k][$key] = $value;
            $num++;
        }
        return $num;
    }

    //修改计划里的字段
    public function updatePlan($key,$value,$where = [])
    {
        $temp = array_filter($this->calendar,function($a) use ($where){
            if(empty($where))return true;
            $num = 1;
            foreach($where as $k=>$v){
                if($a[$k]!=$v){
                    $num = 0;
                    break;
                }
            }
            return $num;
        });
        if(empty($temp))return false;
        $num = 0;
        foreach($temp as $k=>$v)
        {
            $this->calendar[$k][$key] = $value;
            $num++;
        }
        return $num;
    }

    public function getPlan($where){
        $temp = array_filter($this->calendar,function($a) use ($where){
            if(empty($where))return true;
            $num = 1;
            foreach($where as $k=>$v){
                if($a[$k]!=$v){
                    $num = 0;
                    break;
                }
            }
            return $num;
        });
        return $temp;
    }

    /**
     *  提前还款以后清空后续的计划
     */
    public function clearPlanWhenAhead($id){
        foreach($this->calendar as $k=>$v){
            if($v['id']>$id){
                var_log($v,'提前还款取消还款计划>>>');
                $this->calendar[$k]['status'] = \Prj\Consts\PayGW::abondon;
                $this->calendar[$k]['isPay'] = 1;
                $this->calendar[$k]['exp'] = '因提前还款而取消';
                $this->calendar[$k]['realDateYmd'] = date('Ymd');
            }
        }
    }

    /**
     * 起息日期计算
     */
    public function getDtStart($wares)
    {
        $timeEndReal = $wares->getField('timeEndReal');
        if(empty($timeEndReal)){
            var_log('[warning]实际募集结束时间为空');
            $this->dtStart = 0;
        }
        else
        {
            //放款后起息
            $startTime = $wares->getField('payYmd')?$wares->getField('payYmd'):date('YmdHis');
            $this->dtStart = strtotime(substr($startTime,0,8));
            $this->dtStartYmd = substr($startTime,0,8);
        }

        return $this->dtStart ;
    }
}