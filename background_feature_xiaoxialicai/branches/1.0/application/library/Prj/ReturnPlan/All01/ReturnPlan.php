<?php
namespace Prj\ReturnPlan\All01;
use Prj\Consts\ReturnType;

/**
 * 整个标的计息
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/10/23
 * Time: 17:56
 */
class ReturnPlan extends \Prj\ReturnPlan\Base\ReturnCtrl{

    protected $round = 2;  // 取整方法 0：不取整 1:向下取整 2:向上取整
    protected $wares;

    /**
     * 还款计划解析成对象
     * @param $content
     * @param null $wares
     * @return ReturnPlan
     */
    public static function createPlan($content,$wares = null)
    {
        $o = new self($content);
        if(empty($content))return $o;
        $o->encode();
        $o->wares = $wares;
        return $o;
    }

    /**
     * 初始回款日历
     * 返回初始化的对象
     */
    public static function calendar($wares)
    {
        if(is_scalar($wares))$wares = \Prj\Data\Wares::getCopy($wares);
        $wares->load();
        if(!$wares->exists())
        {
            error_log("error>>>标的不存在！".__FILE__);
            throw new \ErrorException('标的不存在');
            return null;
        }
        $o = new ReturnPlan('');
        $o->wares = $wares;
        $o->returnType = $wares->getField('returnType');
        if($o->getDtStart($wares)<=0)
        {
            error_log("error>>>起息时间未确定！".$o->getDtStart($wares).__FILE__);
            throw new \ErrorException('起息时间未确定');
            return null;
        }
        $o->dtStart = $o->getDtStart($wares);  //设置起息时间
        switch ($wares->getField('returnType'))
        {
            case \Prj\Consts\ReturnType::byMonth :
                $o->payByMonth($wares);
                break;
            case \Prj\Consts\ReturnType::single :
                $o->paySingle($wares);
                break;
            default :
                return null;
        }
        //var_log(ReturnPlan::createPlan($content));
        return $o;
    }

    /**
     * 整个标的计息
     * @param \Prj\Data\Wares $wares
     * @return float
     * @throws \ErrorException
     */
    protected function interest(\Prj\Data\Wares $wares)
    {
        $days = $this->getDays($wares);
        $amount = $wares->getField('amount');
        $yield = $wares->getField('yieldStatic')+$wares->getField('yieldStaticAdd'); //全标的起息加了活动计息
        var_log("($yield/360)*$days*$amount",'利息计算>>>>>>>>>');
        return ($yield/360)*$days*$amount;
    }

    /**
     * 获取天息
     * @param \Prj\Data\Wares $wares
     * @return float
     * @throws \ErrorException
     */
    protected function interestPerDay(\Prj\Data\Wares $wares){
        $amount = $wares->getField('amount');
        $yield = $wares->getField('yieldStatic')+$wares->getField('yieldStaticAdd'); //全标的起息加了活动计息
        return ($yield/360)*$amount;
    }

    /**
     * 获取月息
     * @param \Prj\Data\Wares $wares
     * @return float
     * @throws \ErrorException
     */
    protected function interestPerMonth(\Prj\Data\Wares $wares){
        $amount = $wares->getField('amount');
        $yield = $wares->getField('yieldStatic')+$wares->getField('yieldStaticAdd'); //全标的起息加了活动计息
        return ($yield/12)*$amount;
    }

    /**
     * 按月付息
     */
    protected function payByMonth($wares)
    {
        $waresSN = $wares->getField('dlUnit')=='月'?$wares->getField('deadLine'):1;
        $returnList = array();
        $ret['interest'] = $this->interest($wares);
        $amount = $wares->getField('amount');
        $ret['days'] = $this->getDays($wares);
        $ret['interestPer'] = $ret['interest']/$ret['days'];

        for($i=1;$i<=$waresSN;$i++)
        {
            $dateArr[$i] = $this->getNDate($this->dtStart,$i);
        }
        $dateArr[0] = $this->dtStart;
        $totalAll = 0;

        $tmpInterest = $this->interestPerMonth($this->wares);
        $this->formula = ($wares->getField('yieldStatic')+$wares->getField('yieldStaticAdd')).'*'.$wares->getField('amount').'/12';
        foreach($dateArr as $k=>$v)
        {
            $v = date('Ymd',$v);
            if($k==0)continue;
            $days = ($dateArr[$k]-$dateArr[$k-1])/86400;

            $temp = array(
                'id'=>$k,
                'days'=>floor($days),
                'interest'=>$this->round($tmpInterest),
                'amount'=>0,
                'realDateYmd'=>0,
                'planDateYmd'=>$v,
                'waresId'=>$wares->getField('waresId'),
                'waresName'=>$wares->getField('waresName'),
                'isPay'=>0,
            );
            if($k==$waresSN)
            {
                $temp['amount']=$amount;
            }
            $totalAll+=($temp['amount']+$temp['interest']);
            $returnList[] = $temp;
        }
        $this->version = $this->creatVersion();
        $this->ordersId = 0;
        $this->calendar = $returnList;
        $this->total = $totalAll;
        return $this;
    }
    /**
     * 一次性付息
     */
    protected function paySingle($wares)
    {
        $date = $this->getEndDay($wares);
        if($wares->getField('dlUnit')=='月'){ //按月计息
            $this->formula = $this->interestPerMonth($wares).'*'.$wares->getField('deadLine');
           $interest = $this->interestPerMonth($wares)*$wares->getField('deadLine');
        }else{ //按天算息
            $this->formula = $this->interestPerDay($wares) .'*'. $this->getDays($wares);
            $interest = $this->interestPerDay($wares) * $this->getDays($wares);
        }
        $returnList[] = array(
            'id'=>1,
            'days'=>$this->getDays($wares),
            'interest'=>$this->round($interest),
            'amount'=>$wares->getField('amount'),
            'realDateYmd'=>0,
            'planDateYmd'=>$date,
            'waresId'=>$wares->getField('waresId'),
            'waresName'=>$wares->getField('waresName'),
            'isPay'=>0,
        );

        $this->version = $this->creatVersion();
        $this->ordersId = 0;
        $this->calendar = $returnList;
        $this->dtStart = $this->getDtStart($wares);
        $this->total = $returnList[0]['amount']+$returnList[0]['interest'];
        //var_log($this->dtStart);
        return $this;
    }

    /**
     * 获取提前还款的应付收益
     * @param $id
     * @param int $date
     * @return float
     * @throws \ErrorException
     */
    public function getAheadInterest($id,$date = 0){
        if(empty($date))$date = strtotime(date('Ymd'));
        $debtInterest = 0; //欠息

        foreach($this->calendar as $k=>$v){
            if($v['id']>=$id){
                $debtInterest+=$v['interest'];
            }
        }

        $rDays = (strtotime($this->getEndDay($this->wares))-$date)/86400; //提前了多少天
        $rDays = $rDays>0?$rDays:0;
        $rDays = floor($rDays);

        $tmp['formula'] = $debtInterest .'-'. $this->round($this->interestPerDay($this->wares)) .'*'. $rDays ;
        $tmp['ahead'] = $debtInterest - $this->round($this->interestPerDay($this->wares) * $rDays) ;
        $tmp['ahead'] = $tmp['ahead']>0?$tmp['ahead']:0;
        $tmp['ahead'] = $this->round($tmp['ahead']);
        return $tmp;








        /*
        if($id==1){
            $days = ($date-$this->dtStart)/86400;
            $days = $days>0?$days:0;
            return $this->round($days*$this->interestPerDay($this->wares));
        }else{
            $lastDate = $this->getPlanById($id-1)['planDateYmd'];
            if(empty($lastDate)){
                throw new \ErrorException('获取上次还款日期失败');
            }else{
                $days = ($date-strtotime($lastDate))/86400;
                $days = $days>0?$days:0;
                return $this->round($days*$this->interestPerDay($this->wares));
            }
        }
        */

    }

}