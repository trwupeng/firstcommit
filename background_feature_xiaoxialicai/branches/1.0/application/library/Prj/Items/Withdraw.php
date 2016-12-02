<?php
namespace Prj\Items;
/**
 * 提现相关赠送
 * Created by PhpStorm.
 * User: tang.gaohang
 * Date: 2016/3/17
 * Time: 11:23
 */

class Withdraw extends  \Prj\Items\Base implements \Lib\Interfaces\ItemV2{

    protected function getFreeNum(){
        return \Prj\Data\Config::get('WITHDRAW_MONTH_TIMES')-0;
    }

	public function name()
	{
		return '提现券';
	}
	/**
	 * 剩余数量
	 * @param \Prj\Data\User $user 用户对象
	 * @return null
	 * @throws \ErrorException
	 */
	public function numLeft($user) {
		$r = $this->parseUser($user);
		return $r['free']+$r['buyed']-$r['used'];
	}
	protected $fieldName = 'withdrawLeft';
	protected $define = ['Ym'=>6,'free'=>2,'used'=>3,'buyed'=>2];
	/**
	 * 从user中取出实际的数量
	 * @param \Prj\Data\User $user 用户对象
	 * @return null
	 */
	protected function parseUser($user)
	{
		$r=$user->getField($this->fieldName);
		$r = \Sooh\Base\NumStr::decode($r, $this->define);
		$thisMonth = \Sooh\Base\Time::getInstance()->YmdFull;
		$thisMonth = substr($thisMonth,0,6);
		if($r['Ym']!=$thisMonth){
			$r['Ym']=$thisMonth;
			$r['free']=$this->getFreeNum();//默认值
			$r['used']=0;
			$r['buyed']=0;//如果额外获得的要保留，这里要额外计算处理了
		}
		return $r;
	}

    public function getUsed($user){
        $r = $this->parseUser($user);
        return $r['used'];
    }
	
	/**
     * 准备发放红包
     * @param \Prj\Data\User $user 用户对象
     * @param int            $num  一次发放的数量
     * @return string
     * @throws \ErrorException
     */
    public function give_prepare($user, $num = 1) {
		$this->rForRollback = $r = $this->parseUser($user);
		$r['buyed']+=$num;
        var_log($r['buyed'],'buyed>>>>>>>>>>>');
		$user->setField($this->fieldName, \Sooh\Base\NumStr::encode($r,$this->define));
        return '';
    }
	protected $rForRollback;
	public function give_rollback($user){
		$user->setField($this->fieldName, \Sooh\Base\NumStr::encode($this->rForRollback,$this->define));
	}
	public function give_confirm($user){
		
	}
//	public function onUserUpdated()
//	{
//		
//	}

	public function useit($user,$num=1){
		$r = $this->parseUser($user);
		$r['used']+=$num;
		$user->setField($this->fieldName, \Sooh\Base\NumStr::encode($r,$this->define));
	}


    public function descCreate(){
        return '';
    }
    
	/////////////////////////////////////////////////////////////////////
}