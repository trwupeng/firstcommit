<?php
namespace Sooh\Base;

use \Sooh\Base\Time as sooh_time;
/**
 * CD 封装类
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class CD {
	protected $r;
	protected $add;
	protected $max;
	protected $now;
	protected $define=array('start'=>10,'red'=>1,'dur'=>8);
	/**
	 * CD 构造函数
	 * 
	 * @param string $numString 存取的字符串
	 * @param int $add 一次增加多少秒
	 * @param int $max 累计多少之后变红
	 */
	public function __construct($numString,$add,$max) {
		$this->add=$add;
		$this->max=$max;
		$this->now = sooh_time::getInstance()->timestamp();
		$this->r =  \Sooh\Base\NumStr::decode($numString, $this->define);
		if($this->cdLeft()<=0){
			$this->r=array('start'=>0,'red'=>0,'dur'=>0);
		}
	}
	/**
	 * 是否已经红了
	 * @return boolean 
	 */
	public function isRed()
	{
		return $this->r['red']==1;
	}
	/**
	 * 加几次cd (构造时设置的$add)
	 * @param int $times default=1
	 * @return CD
	 */
	public function add($times=1)
	{
		if($this->r['red']==1)throw new \ErrorException('cd is red');
		if(empty($this->r['start']))$this->r['start']=$this->now;
		$this->r['dur']+=$this->add*$times;
		$this->r['red'] = $this->cdLeft()>=$this->max?1:0;
		return $this;
	}
	/**
	 * 累计加了几次cd了
	 * @return int
	 */
	public function timesCount()
	{
		if($this->r['dur']>0){
			return round($this->r['dur']/$this->add);
		}else return 0;
	}
	/**
	 * 剩余cd秒数
	 * @return boolean 
	 */
	public function cdLeft()
	{
		if($this->r['dur']>0){
			return $this->r['start']+$this->r['dur']-$this->now;
		}else return 0;
	}
	/**
	 * @return string
	 */
	public function toString()
	{
		return \Sooh\Base\NumStr::encode($this->r, $this->define);
	}
}
