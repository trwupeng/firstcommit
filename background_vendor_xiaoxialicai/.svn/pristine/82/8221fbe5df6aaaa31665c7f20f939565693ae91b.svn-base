<?php
namespace Sooh\Base;
/**
 * 隔一段时间重试若干次的控制类，存储成bigint，时间戳放在开头的部分
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class Retrylater {
	const dtDone = '9999999999999999999';
	protected $define=array('dt'=>12,'kept'=>4,'step'=>3);
	public $step=0;
	public $isDone=false;
	protected $dtNextTry;
	public $maxForDone;
	protected $arrSteps;
	protected $stepSize;
	protected $dtNow;
	/**
	 *
	 * @var bigint 小于此值的是需要重试的
	 */
	public $cmpVal;
	public function __construct($arrSteps,$step=1,$maxForDone=self::dtDone) 
	{
		$this->arrSteps=$arrSteps;
		$this->maxForDone=$maxForDone;
		$this->stepSize=$step;
		$this->dtNow = \Sooh\Base\Time::getInstance()->timestamp();
		$this->cmpVal = $this->dtNow.str_repeat('0', 7);
	}
	public function parse($val)
	{
		if($val==$this->maxForDone){
			$this->isDone=true;
		}else{
			$this->isDone=false;
		}
		if($val>0){
			$r = \Sooh\Base\NumStr::decode($val, $this->define);
			if($r['dt']<=$this->dtNow)$this->dtNextTry=$this->dtNow;
			else $this->dtNextTry=$r['dt'];
			$this->step=$r['step'];
		}else{
			$this->dtNextTry=$this->dtNow;
			$this->step=0;
		}
	}
	/**
	 * 仍然需要重试，
	 * @return bool 是否还有重试的机会
	 */
	public function needsRetry()
	{
		$this->step++;
		return $this->step<  sizeof($this->arrSteps);
	}
	public function done()
	{
		$this->isDone=true;
	}
	public function toNumStr()
	{
		if($this->isDone){
			return $this->maxForDone;
		}else{
			$this->dtNextTry = $this->dtNextTry + ($this->arrSteps[$this->step]*$this->stepSize);
			return \Sooh\Base\NumStr::encode(array('dt'=>$this->dtNextTry,'step'=> $this->step), $this->define);
		}
	}
}
