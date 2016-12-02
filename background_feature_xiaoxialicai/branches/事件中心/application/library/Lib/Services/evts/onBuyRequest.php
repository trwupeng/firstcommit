<?php
namespace Lib\Services\evts;
/**
 * 用户发出购买请求 以后做哪些任务
 *
 * @author wang.ning
 */
class onBuyRequest {

    protected $arr;
	/**
	 * 
	 * @param \Sooh\Base\Log\Data $data
	 * @return void
	 */
	public function run($data)
	{
        //兜底活动
        if($data->sarg3 == 'finallyOrder' && $data->ret == 'ok'){
            $this->finallyOrdersAward($data);
            return;
        }
		$this->notifyRptCenter();
		\Lib\Services\Bysms::getInstance(\Prj\BaseCtrl::getRpcDefault('Bysms'))
							->sendCode( '130123456789', __CLASS__.'随文');
	}
	/**
	 * 通知报表中心
	 */
	protected function notifyRptCenter()
	{
		
	}

    /**
     * 兜底活动
     * @param $data
     * @return mixed
     * @throws \ErrorException
     */
    public function finallyOrdersAward($data){
        $orderId = $data->ext;
        error_log('finallyOrdersAward#orderId:'.$orderId.'#开始发放兜底红包...');
        if($data->sarg3 != 'finallyOrder' && $data->ret != 'ok')return $this->_returnError('不满足发放奖励的条件');
        if(!\Prj\Data\Config::get('FINALLY_RED_SWITCH'))return $this->_returnError('活动处于关闭状态');
        if(empty($orderId))return $this->_returnError('orderId_missing');
        error_log('>>>finallyOrdersAward#ordersId:'.$orderId);
        $invest = \Prj\Data\Investment::getCopy($orderId);
        $invest->load();
        if(!$invest->exists())return $this->_returnError('不存在的订单');
        if(!in_array($invest->getField('orderStatus'),\Prj\Consts\OrderStatus::$running))return $this->_returnError('错误的订单状态');
        if($invest->getField('finallyOrdersAward') == 1)return $this->_returnOK('发放成功');
        $userId = $invest->getField('userId');
        $user = \Prj\Data\User::getCopy($userId);
        $user->load();
        if(!$user->exists())return $this->_returnError('不存在的用户');

        try{
            $invest->setField('finallyOrdersAward',1);
            $invest->update();
        }catch (\ErrorException $e){
            return $this->_returnError($e->getMessage());
        }

        //todo 发放兜底红包
        $itemGiver = new \Prj\Items\ItemGiver($userId);
        try{
            $giveResult = $itemGiver->add('NewFinallyOrderRedPacket',1)->give();
            if(empty($giveResult))throw new \ErrorException($itemGiver->getLastError());
        }catch (\ErrorException $e){
            $invest->setField('finallyOrdersAward',4);
            $invest->update();
            return $this->_returnError($e->getMessage());
        }
        try{
            $user->update();
            $itemGiver->onUserUpdated();
        }catch (\ErrorException $e){
            error_log('finallyOrdersAward#ordersId:'.$orderId.'#user update failed ...');
        }
        //todo 发放红包成功给与PUSH及站内信通知（内容由MESSAGE表里配置）梁言庆加一下

        return $this->_returnOK('发放成功');
    }

    protected function _assign($key,$value)
    {
        $this->arr[$key] = $value;
        return $this->arr;
    }

    protected function _returnError($msg='',$str = '',$code=400)
    {
        $this->arr['msg'] = $msg;
        $this->arr['code'] = $code;
        var_log($str,'[error400]'.$msg);
        return $this->arr;
    }

    protected function _returnOK($msg='')
    {
        $this->arr['msg'] = $msg;
        $this->arr['code'] = 200;
        return $this->arr;
    }
}
