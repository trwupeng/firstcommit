<?php
namespace Rpt\Misc;

/**
 * Class DataCrondGather
 *  0点之后统计0点之前3个小时的数据
 *  1点之后统计前面第2天的数据
 *  2点之后统计前面第1天的数据
 *  3点之后统计前面第4天的数据
 *  4点之后统计前面第10天的数据
 *  5点统计当天0点-5点的数据
 *  6点之后开始统计前3个小时的数据
 */
class DataCrondGather extends \Sooh\Base\Crond\Task {
    public function init(){
        $this->_secondsRunAgain = 600;
        $this->toBeContinue = true;
        $this->ret = new \Sooh\Base\Crond\Ret;
        $this->ret->newadd = 0;
        $this->ret->newupd = 0;
    }
    protected $dt;
    protected $dtFrom;
    protected $dtTo;
    protected $ymd;
    protected function onRun($dt){
        $this->ymd = date('Ymd',$dt->timestamp());
        $this->dt = $dt;
        if($this->_isManual) {
//        手动的时候抓去一整天的数据
            $this->dtFrom = strtotime($this->ymd);
            $this->dtTo = $this->dtFrom + 86400-1;
            $ret = $this->gather();
        }else{
            $this->toBeContinue= false;
            switch($dt->hour){
            	case 0:
                    $this->ymd = date('Ymd', $dt->timestamp(-2));
            	    $this->dtFrom = strtotime($this->ymd);
            	    $this->dtTo = $this->dtFrom + 86400-1;
            	    $ret=$this->gather();
            	    break;
                case 1:
                    $this->ymd = date('Ymd', $dt->timestamp(-4));
            	    $this->dtFrom = strtotime($this->ymd);
            	    $this->dtTo = $this->dtFrom + 86400-1;
                    $ret=$this->gather();
                    break;
                case 2:
                    $this->ymd = date('Ymd',$dt->timestamp(-10));
            	    $this->dtFrom = strtotime($this->ymd);
            	    $this->dtTo = $this->dtFrom + 86400-1;
                    $ret = $this->gather();
                    break;
                case 3:
                    $this->ymd = date('Ymd', $dt->timestamp(-1));
            	    $this->dtFrom = strtotime($this->ymd);
            	    $this->dtTo = $this->dtFrom + 86400-1;
                    $ret=$this->gather();
                    break;
                case 4:
					$this->lastMsg = 'skip';
                    $ret = true;
                    break;
                default:
                    $this->toBeContinue = true;
                    if($dt->hour==5){
                        $this->dtFrom = strtotime($this->ymd);
                        $this->dtTo = $dt->timestamp();
                    }else{
                        $this->dtFrom = $dt->timestamp()-7200;
                        $this->dtTo = $dt->timestamp();
                    }
                    $ret=$this->gather();
                    break;
            }
        }
        $this->lastMsg = $this->ret->toString();
        return $ret;
    }

    protected function gather(){}

    // 命令执行的时间范围 日志
    protected function printLogOfTimeRang() {
        $classname = get_class($this);
        error_log('[ Trace ] ### '.$classname.' ### Time range:['.date('Y-m-d H:i:s', $this->dtFrom).', '.date('Y-m-d H:i:s', $this->dtTo).']');
    }
}