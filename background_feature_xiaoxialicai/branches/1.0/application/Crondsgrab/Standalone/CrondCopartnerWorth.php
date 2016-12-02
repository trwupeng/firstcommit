<?php
namespace PrjCronds;
/**
 * php /var/www/licai_php/run/crond.php "__=crond/rungrab&task=Standalone.CrondCopartnerWorth&ymdh=20150819"
 *
 */
class CrondCopartnerWorth extends \Sooh\Base\Crond\Task{
	public function init() {
		parent::init();
		$this->toBeContinue=true;
		$this->_iissStartAfter=4000;//每小时03分后启动
		$this->ret = new \Sooh\Base\Crond\Ret();

	}
	public function free() {
		parent::free();
	}
	/**
	 * 
	 * @param \Sooh\Base\Time $dt
	 * @param \Sooh\Base\RetSimple $__ignore__ Description
	 */

	protected function onRun($dt)
	{
		if(!$this->_isManual && $dt->hour<=6){
			$dt0 = strtotime($dt->YmdFull);
			switch ($dt->hour){
				case 1: $this->oneday(date('Ymd',$dt0-86400*10));break;
				case 2: $this->oneday(date('Ymd',$dt0-86400*7));break;
				case 3: $this->oneday(date('Ymd',$dt0-86400*1));break;
				case 4: $this->oneday(date('Ymd',$dt0-86400*3));break;
				case 5: $this->oneday(date('Ymd',$dt0-86400*2));break;
				case 6: $this->oneday(date('Ymd',$dt0-86400*4));break;
				default: $this->oneday($dt->YmdFull); break;
			}
		}else{
			$this->oneday($dt->YmdFull);
		}
		$this->toBeContinue=false;
		return true;
	}

	protected function oneday($ymd) {
		$o = new \Rpt\DataDig\CopartnerWorthDig();
		$o->importData($ymd);
	}
}
