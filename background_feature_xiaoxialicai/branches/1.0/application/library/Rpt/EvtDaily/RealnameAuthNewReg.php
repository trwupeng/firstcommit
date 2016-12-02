<?php
namespace Rpt\EvtDaily;
/**
 *
 * 资金流向提现
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/1/27 0027
 * Time: 下午 4:05
 */

class RealnameAuthNewReg extends \Rpt\EvtDaily\Base {
    protected function actName() {return 'RealnameAuthNewReg';}
    public static function displayName(){return '当日新注册实名认证数';}
}