<?php
namespace Prj\ReturnPlan\Std01;
use Prj\Consts\ReturnType;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/10/23
 * Time: 17:56
 */
class ReturnPlan extends \Prj\ReturnPlan\Base\ReturnCtrl{

    protected $round = 1;  // 取整方法 0：不取整 1:向下取整 2:向上取整
    protected $yield = [];
    protected $intPerDay = [];
    protected $intPerMonth = [];
    protected $days;
    protected $amountTotal;
    protected $ware;
    protected $invest;
    protected $interestSub;

    /**
     * 还款计划解析成对象
     * @param $content
     * @param null $invest
     * @return ReturnPlan
     * @throws \ErrorException
     */
    public static function createPlan($content,$invest = null)
    {
        $o = new self($content);
        if(empty($content))return $o;
        $o->encode();
        $invest = \Prj\Data\Investment::getCopy($o->ordersId);
        $invest->load();
        $o->invest = $invest;

        $wares = \Prj\Data\Wares::getCopy($invest->getField('waresId'));
        $wares->load();
        $o->ware = $wares;

        $o->amountTotal = $invest->getField('amount')+$invest->getField('amountExt')+$invest->getField('amountFake');

        $o->yield = [
            'static'=>$wares->getField('yieldStatic'),  //基本息
            'add'=>$wares->getField('yieldStaticAdd'), //活动息
            'ext'=>$invest->getField('yieldExt'), //加息券的息
        ];
        foreach($o->yield as $k=>$v)
        {
            $o->intPerDay[$k] = $v * $o->amountTotal / 360;
        }



        return $o;
    }

    public static function calendar($ordersId)
    {
        if(is_scalar($ordersId))
        {
            $invest = \Prj\Data\Investment::getCopy($ordersId);
            $invest->load();
        }
        else
        {
            $invest = $ordersId;
            $invest->load();
        }

        if(!$invest->exists())
        {
            error_log("error>>>订单不存在！".__FILE__);
            return null;
        }
        $wares = \Prj\Data\Wares::getCopy($invest->getField('waresId'));
        $wares->load();
        if(!$wares->exists())
        {
            error_log("error>>>".$invest->getField('waresId')."_标的不存在！".__FILE__);
            return null;
        }
        $o = new ReturnPlan('');
        $o->ware = $wares;
        $o->invest = $invest;
        if($o->getDtStart($wares)<=0)
        {
            //error_log("error>>>起息时间未确定！".__FILE__);
            return null;
        }
        $o->dtStart = $o->getDtStart($wares);  //设置起息时间
        $o->interestSub = $o->getInterestSub();
        $o->days = $o->getDays($wares); //项目总天数
        $o->amountTotal = $invest->getField('amount')+$invest->getField('amountExt')+$invest->getField('amountFake');
        $o->yield = [
            'static'=>$wares->getField('yieldStatic'),
            'add'=>$wares->getField('yieldStaticAdd'),
            'ext'=>$invest->getField('yieldExt'),
        ];
        $o->interestSub = $o->getInterestSub();
        foreach($o->yield as $k=>$v)
        {
            $o->intPerDay[$k] = $v * $o->amountTotal / 360;
        }
        foreach($o->yield as $k=>$v)
        {
            $o->intPerMonth[$k] = $v * $o->amountTotal / 12;
        }

        $o->ordersId = $invest->getField('ordersId');
        $o->returnType = $wares->getField('returnType');
        switch ($wares->getField('returnType'))
        {
            case \Prj\Consts\ReturnType::byMonth :
                $o->payByMonth($invest,$wares);
                break;
            case \Prj\Consts\ReturnType::single :
                $o->paySingle($invest,$wares);
                break;
            default :
                return null;
        }
        $o->total['interestSub'] = $o->round($o->interestSub);
        $o->total['float'] = 0;
        return $o;
    }

    /**
     * 贴息计算
     */
    public function getInterestSub()
    {
        $orderDay = substr($this->invest->getField('orderTime'),0,8);
        $orderDayInt = strtotime($orderDay);
        $days = floor(($this->dtStart-$orderDayInt)/86400)-1;

        //$days = floor((strtotime('20160404')-strtotime('20160401'))/86400)-1 ;
        var_log($days,'贴息天数 days >>> ');
        if($days<=0)return 0 ;
        $interestSub = $this->amountTotal * (($this->yield['static'])/360) * $days;
		var_log($this->yield,'$this->yield >>>');
        return $this->round($interestSub);
    }

    /**
     * 按月付息
     * By Hand
     */
    protected function payByMonth($invest, $wares)
    {
        $deadLine = $wares->getField('deadLine'); //总共期数
        $returnList = array();

        for($i=1;$i<=$deadLine;$i++)
        {
            $dateArr[$i] = $this->getNDate($this->dtStart,$i);  //获取所有的付息日期
            //if($i==$deadLine)$dateArr[$i]=$wares->getField('ymdPayPlan');
        }
        $dateArr[0] = $this->dtStart;

        foreach($dateArr as $k=>$v)
        {
            $v = date('Ymd',$v);
            if($k==0)continue;
            $daysTemp = floor(($dateArr[$k]-$dateArr[$k-1])/86400);

            $tempList = array(
                'id'=>$k,
                'days'=>$daysTemp,
                'realDateYmd'=>0,
                'planDateYmd'=>$v,
                'isPay'=>0,
                'ordersId'=>$invest->getField('ordersId'),
                'waresId'=>$wares->getField('waresId'),
                'waresName'=>$wares->getField('waresName'),
            );
            /*
            foreach($this->intPerDay as $kk=>$vv)
            {
                $interest = $this->round($daysTemp*$vv)>=0?$this->round($daysTemp*$vv):0; //算出利息
                $tempList['interest'.ucfirst($kk)] = $interest;
                $this->total[$kk]+=$interest;
            }
            */
            $formula = [];
            foreach($this->intPerMonth as $kk=>$vv)
            {
                $interest = $this->round($vv)>=0?$this->round($vv):0; //算出利息
                if($this->yield[$kk]!=0)$formula[] = $this->yield[$kk].'*'.$this->amountTotal.'/12';
                $tempList['interest'.ucfirst($kk)] = $interest;
                $this->total[$kk]+=$interest;
            }
            $this->formula = implode('+',$formula);
            $tempList['interestFloat'] = 0;
            if($k==$deadLine)
            {
                $tempList['amount']+=$invest->getField('amount');
                $tempList['amountExt']+=$invest->getField('amountExt');
                $tempList['interestSub'] = $this->round($this->interestSub);
            }
            $returnList[] = $tempList;
        }
        $this->calendar = $returnList;
        return $this;
    }

    /**
     * 一次性付息
     */
    protected function paySingle($invest,$wares)
    {
        $date = $this->getEndDay($wares);

        $returnList[0] = array(
            'id'=>1,
            'days'=>$this->days,
            'realDateYmd'=>0,
            'planDateYmd'=>$date,
            'isPay'=>0,
            'ordersId'=>$invest->getField('ordersId'),
            'waresId'=>$wares->getField('waresId'),
            'waresName'=>$wares->getField('waresName'),
        );
        $formula = [];
        if($wares->getField('dlUnit')=='月'){
            foreach($this->intPerMonth as $kk=>$vv)
            {
                $months = $wares->getField('deadLine');
                $interest = $this->round($months * $vv)>=0?$this->round($months * $vv):0;
                if($this->yield[$kk]!=0)$formula[] = $this->amountTotal.'*'.$this->yield[$kk].'/12*'.$months;
                $returnList[0]['interest'.ucfirst($kk)] = $interest;
                $this->total[$kk] = $interest;
            }
        }else{
            foreach($this->intPerDay as $kk=>$vv)
            {
                $interest = $this->round($this->days * $vv)>=0?$this->round($this->days * $vv):0;
                if($this->yield[$kk]!=0)$formula[] = $this->amountTotal.'*'.$this->yield[$kk].'/360*'.$this->days;
                $returnList[0]['interest'.ucfirst($kk)] = $interest;
                $this->total[$kk] = $interest;
            }
        }
        $this->formula = implode('+',$formula);

        $returnList[0]['interestFloat'] = 0;
        $returnList[0]['amount'] = $invest->getField('amount');
        $returnList[0]['amountExt'] = $invest->getField('amountExt');
        $returnList[0]['interestSub'] = $this->round($this->interestSub);
        $this->calendar = $returnList;
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
        $debtInterest = []; //欠息
        $formulaArr = [];
        foreach($this->calendar as $v){
            if($v['id']>=$id){
                $debtInterest['interestStatic']+=$v['interestStatic'];
                $debtInterest['interestAdd']+=$v['interestAdd'];
                $debtInterest['interestExt']+=$v['interestExt'];
            }
        }
        $rDays = (strtotime($this->getEndDay($this->ware))-$date)/86400; //提前了多少天
        $rDays = $rDays>0?$rDays:0;
        $rDays = floor($rDays);

        foreach($this->intPerDay as $k=>$v){
            $formulaArr['interest'.ucfirst($k)] = '('.$debtInterest['interest'.ucfirst($k)] .'-'.$rDays.'*'.$v.')';
            $tmp['interest'.ucfirst($k)] = $debtInterest['interest'.ucfirst($k)] - $this->round($rDays*$v);
            $tmp['interest'.ucfirst($k)] = $tmp['interest'.ucfirst($k)]>0?$tmp['interest'.ucfirst($k)]:0;
            $tmp['interest'.ucfirst($k)] = $this->round($tmp['interest'.ucfirst($k)]);
        }
        $formula = implode('+',$formulaArr);
        $newTmp['ahead'] = $tmp;
        $newTmp['formula'] = $formula;
        $newTmp['interestSub'] = $this->getInterestSub();
        return $newTmp;






        /*
        if($id==1){
            $days = ($date-$this->dtStart)/86400;
            $days = $days>0?$days:0;
        }else{
            $lastDate = $this->getPlanById($id-1)['planDateYmd'];
            if(empty($lastDate)){
                throw new \ErrorException('获取上次还款日期失败');
            }else{
                $days = ($date-strtotime($lastDate))/86400;
                $days = $days>0?$days:0;
            }
        }
        var_log($this->yield);
        foreach($this->yield as $k=>$v){
            $tmp['interest'.ucfirst($k)] = $this->round($v*$this->intPerDay[$k]*$days);
        }
        return $tmp;
        */
    }

    /**
     * 提前还款拆分
     * @param $id
     */
    public function splitAhead($id){
        $plan = $this->getPlan(['id'=>$id,'ahead'=>1]);
        if(empty($plan))return;
        $tmp = $this->getPlanById($id);
        $this->updatePlanById('realPayAmount',0,$id);
        $this->updatePlanById('realPayInterest',0,$id);
        $this->updatePlanById('realPayinterestSub',0,$id);
        $this->updatePlanById('realDateYmd',0,$id);
        $this->updatePlanById('ahead',null,$id);
        $this->updatePlanById('status',-1,$id);
        $this->updatePlanById('exp','因提前还款而取消',$id);
        $this->updatePlanById('sn','',$id);
        $tmp['id'] = $id-0.5;
        //$this->updatePlan()
        $this->calendar[] = $tmp;
        var_log($plan);
    }
}