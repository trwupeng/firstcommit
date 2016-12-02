<?php
namespace Prj\Items;
/**
 * 自定义红包，红包金额，描述等自定义
 * @package Prj\Items
 */
class RedPacketOfUserDefined extends Voucher {
	protected $userId;
	protected $amount;
	protected $title='红包提醒';
	protected $content; // 信息内容
	protected $desc; // 红包描述
	protected $minInvestment; // 最低投资多少使用
	protected $days; // 有效期天数

	public function __construct(array $args)
	{
		if(isset($args['userId'])){
			$this->userId = $args['userId'];
		}
		$this->amount = $args['amount']*100;
		$this->content = $args['content'];
		$this->desc = $args['desc'];
		$this->minInvestment = $args['minInvestment']*100;
		$this->days = $args['days'];
	}

	public function descCreate() {
		return $this->desc;
	}

	public function name (){
		return 'redPacketOfUserDefined';
	}
	protected function ini_limit() {
		return ['minInvest'=>$this->minInvestment];
	}
	protected function ini_expire()
	{
		return ['end'=>\Sooh\Base\Time::getInstance()->timestamp($this->ini_days())];
	}

	protected function ini_days() {
		return $this->days;
	}
	protected function ini_more(){
		return ['type' => \Prj\Consts\Voucher::type_real];
	}

	protected function ini_amount() {
		return $this->amount;
	}


	/**
	 * 发送站内信
	 */
	public function sendMsg() {
		try{
			\Lib\Services\Message::getInstance()->add(0, $this->userId, 5, $this->title , $this->content, null, false);
		}catch(\Exception $e ) {
			error_log('send msg error, userId:' . $this->userId . '; title:' . $this->title);
			return false;

		}

		return true;
	}
}