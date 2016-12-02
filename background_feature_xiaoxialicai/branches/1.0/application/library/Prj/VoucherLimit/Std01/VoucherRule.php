<?php
namespace Prj\VoucherLimit\Std01;
/**
 * 券的使用规则
 */
class VoucherRule
{
    public $voucherId = 0;
    public $isStart = 0;//是否顶起步价
    public $isTotal = 0;//是否占用募集总额
    public $isReturn = 0;//是否夹在本金里面回给用户
    public $isYield = 0;//是否加息
    public $isFake = 0;//是否不可提现
    public $error = '';//错误信息


    public static $o;//实例化

    public $amount = 0; //面值
    public $startAmount = 0;//可顶的起步价金额
    public $totalAmount = 0;//占用募集总额
    public $returnAmount = 0;//夹在本金里面
    public $fakeAmount = 0;//不可提现的券金
    public $addYield = 0;//加息

    public $type = 0;
    private function __construct($vouchersId)
    {

    }

    /**
     * @param string vouchersId
     * @return VoucherRule
     * @throws \ErrorException
     */
    public static function createRule($vouchersId)
    {
        $o = new VoucherRule($vouchersId);
        $o->voucherId = $vouchersId;
        if(empty($vouchersId)){
            return $o;
        }
        $vouchers = \Prj\Data\Vouchers::getCopy($vouchersId);
        $vouchers->load();
        if(!$vouchers->exists()){
            error_log('error>>>不存在的券');
            $o->error.=' empty_vocher';
            return $o;
        }
        if(!$amount = $vouchers->getField('amount',true)) {
            error_log('error>>>不存在的面值');
            $o->error.=' empty_amout';
            return $o;
        }
        $o->amount = $amount;
        if(!$o->type = $vouchers->getField('voucherType',true)) {
            error_log('error>>>错误的类型');
            $o->error.=' empty_voucherType';
            return $o;
        }
        $o->typeRule($o->type);
        $o->update($o->type);
        return $o;
    }

    protected function typeRule($type)
    {
        switch($type)
        {
            //用户可领取现金的代金券，80本金+20券，可以当成100元购买额
            case \Prj\Consts\Voucher::type_real:
                $this->isStart = 1;
                $this->isTotal = 1;
                $this->isReturn = 1;
                break;
            //用户不可领取现金的利息券100本金+20券，可以当成100元购买额，收取的是120的利息
            case \Prj\Consts\Voucher::type_fake:
                $this->isFake = 1;
                break;
            //加息券，提高利率
            case \Prj\Consts\Voucher::type_yield:
                $this->isYield = 1;
                break;
            default:
                break;
        }
    }

    protected function update()
    {
        if($this->isStart)$this->startAmount = $this->amount;
        if($this->isTotal)$this->totalAmount = $this->amount;
        if($this->isReturn)$this->returnAmount = $this->amount;
        if($this->isYield)$this->addYield = $this->amount/100;
        if($this->isFake)$this->fakeAmount = $this->amount;
        if($this->addYield>0.1)
        {
            error_log('error>>>过大的加息券(大于10%)');
            $this->error.=' oversize_addYield';
        }
    }
}