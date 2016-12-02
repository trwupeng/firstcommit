<?php
namespace Prj\Items;
/**
 * 签到赠送的券
 *
 * @author simon.wang
 */
class VoucherCheckin extends Voucher {
	/**
	 * 发放时的参数
	 * @return array [day-expired, amount, type]
	 */
	protected function iniForGiven()
	{
		return [30,0,0];
	}
}
