<?php
namespace PrjCronds;
/**
 * 拉取货基收益数据
 * @author Simon Wang <hillstill_simon@163.com>
 */
class GetDayInterest extends \Sooh\Base\Crond\Task{
    protected  $hour = 8; //8点执行
    protected  $title = '>>>拉取货基收益列表';
	public function init() {
		parent::init();
		$this->toBeContinue=true;
		$this->_secondsRunAgain=3000;//每5分钟启动一次
		$this->_iissStartAfter=100;//每小时02分后启动
		$this->ret = new \Sooh\Base\Crond\Ret();

	}
	public function free() {
		parent::free();
	}

	/**
	 * @param \Sooh\Base\Time $dt
	 */
	protected function onRun($dt) {
		$hour = $dt->hour;
        if($this->hour && $hour!=$this->hour){
            error_log($this->title.$this->hour.'点执行>>>#');
            return true;
        }
        var_log($hour , 'hour >>> ');
		if($this->_isManual){
			$m='manual';
		}else{
			$m='auto';
		}
        error_log($this->title.'开始>>>#');

		if($this->_counterCalled==1){
			error_log("[TRace]".__CLASS__.'# first by '.$m.' #'.$this->_counterCalled);
		}else{
			error_log("[TRace]".__CLASS__.'# continue by '.$m.' #'.$this->_counterCalled);
		}

        $ymd = date('Ymd',  $dt->timestamp(-1));
        \Prj\Check\DayInterest::saveData($ymd);
		$this->lastMsg = $this->ret->toString();//要在运行日志中记录的信息
        error_log($this->title.'结束>>>#');
		return true;
	}
}
