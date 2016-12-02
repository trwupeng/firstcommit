<?php
namespace Prj\Items;
/**
 * 周活跃奖励红包（基本分）
 * User: LTM <605415184@qq.com>
 * Date: 2015/11/4
 * Time: 13:50
 */
class RedPacketForExcode extends \Prj\Items\Voucher
{

	protected function ini_amount()
	{
		//return parent::realAmoundByWeight(['1_100'=>99,'100_900'=>891]);
		return 1;
	}

	protected function ini_limit()
	{
		//minInvest: 最低投资额，单位元
		return ['minInvest' => 0, 'shelf' => 0];
	}

	protected function ini_more()
	{
		return ['type' => \Prj\Consts\Voucher::type_real];
	}

	protected function ini_expire()
	{
		//start end 开始结束的时间戳
		$dt = strtotime(\Sooh\Base\Time::getInstance()->YmdFull);
		return ['type'  => 'd',
		        'dur'   => 2,
		        'start' => $dt,
		        'end'   => \Sooh\Base\Time::getInstance()->timestamp(2)
		];//48小时有效期

		//		$dt=  \Sooh\Base\Time::getInstance()->timestamp();
		//		return ['type'=>'s','dur'=>86400, 'start'=>$dt,'end'=>$dt+86400];//24小时
	}

	public function descCreate()
	{
		return '活动红包';
	}
}