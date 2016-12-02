<?php
namespace Rpt\EvtDaily;
/**
 * 日行为统计管理类：之前注册，当日首购的总人数
 * -- flgext01  prdtType   flgext02  uType
 * 注意：这里的clientType，prdtType始终是0
 * @author Simon Wang <hillstill_simon@163.com>
 */
class BuyNUsrNew1 extends Base{
	protected function actName(){return 'BuyNUsrNew1';}
	public static function displayName(){return '老注册首购人数';}
}
