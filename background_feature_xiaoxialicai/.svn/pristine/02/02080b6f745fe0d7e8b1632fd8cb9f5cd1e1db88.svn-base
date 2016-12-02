<?php
namespace Prj\Items;
/**
 * 注册红包
 * @package Prj\Items
 * @version 0.1 废弃
 */
class RedPacketOfKefu extends Voucher {
	protected $userId;

	public function __construct(array $args)
	{
		if(isset($args['userId'])){
			$this->userId = $args['userId'];
		}
	}

	public function descCreate() {
		return '客服奖励红包';
	}

	public function name (){
		return 'redPacketOfKefu';
	}
	protected function ini_limit() {
		return ['minInvest'=>100];
	}
	protected function ini_expire()
	{
		return ['end'=>\Sooh\Base\Time::getInstance()->timestamp($this->ini_days())];
	}

	protected function ini_days() {
		return 2;
	}
	protected function ini_more(){
		return ['type' => \Prj\Consts\Voucher::type_real];
	}

	protected function ini_amount() {
		return 500;
	}


	public function onUserUpdated() {
		\Prj\ReadConf::run(
				[
						'event' => 'redpacket_kefu',
						'brand' => \Prj\Message\Message::MSG_BRAND,
						'num_packet' => 1,//数量
						'private_gift' => sprintf('%.02f', $this->ini_amount() / 100),//金额
						'num_deadline' => $this->ini_days()*24,//有效期
				],
				['userId' => $this->userId]
		);
	}
}