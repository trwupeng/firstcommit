<?php
namespace PrjCronds;
/**
 * 检查失败的订单，解冻相关金额
 *  php /var/www/miao_php/run/crond.php "__=crond/run&task=Standalone.CheckNeedUnfreeze" 2>&1
 * @author Simon Wang <hillstill_simon@163.com>
 */
class CheckNeedUnfreeze extends \Sooh\Base\Crond\Task{
	public function init() {
		parent::init();
		$this->toBeContinue=true;
		$this->_secondsRunAgain=600;//每10分钟启动一次
		$this->_iissStartAfter=100;//每小时02分后启动

		$this->ret = new \Sooh\Base\Crond\Ret();

	}
	public function free() {
		parent::free();
	}


	protected function onRun($dt) {
		if($this->_isManual){
			$m='manual';
		}else{
			$m='auto';
		}
		if($this->_counterCalled==1){
			error_log("[TRace]".__CLASS__.'# first by '.$m.' #'.$this->_counterCalled);
		}else{
			error_log("[TRace]".__CLASS__.'# continue by '.$m.' #'.$this->_counterCalled);
		}
		$this->lastMsg = $this->ret->toString();//要在运行日志中记录的信息
        $this->checkNeedUnfreeze();
		return true;
	}

    protected function checkNeedUnfreeze(){
        error_log('###[warning]扫描购买失败需要解冻的订单开始###');
        $where = ['descCreate'=>'#网关错误回滚#','orderStatus<'=>0,'unfreeze'=>[0,4]];
        $list = \Prj\Data\Investment::loopFindRecords($where);
        $waresStatus = [];
        if($list){
            foreach($list as $v){
                $ordersId = $v['ordersId'];
                $userId = $v['userId'];
                $waresId = $v['waresId'];
                $invest = \Prj\Data\Investment::getCopy($ordersId);
                $invest->load();
                if(!$invest->exists()){
                    error_log('[warning]'.__METHOD__.'#'.__LINE__.'#'.$ordersId.'#订单不存在!');
                    continue;
                }
                if($waresStatus[$waresId]===null){
                    $wares = \Prj\Data\Wares::getCopy($waresId);
                    $wares->load();
                    if(!$wares->exists()){
                        error_log('[warning]'.__METHOD__.'#'.__LINE__.'#'.$ordersId.'#标的不存在!');
                        continue;
                    }
                    $waresStatus[$waresId] = $wares->getField('statusCode');
                }
                if(!in_array($waresStatus[$waresId],[\Prj\Consts\Wares::status_open,\Prj\Consts\Wares::status_go])){
                    continue;
                }
                //todo 通知网关
                $data = [
                    $ordersId,
                    $userId
                ];
                //\Sooh\Base\Ini::getInstance()->get('noGW') ? \Prj\Wares\Wares::getRpcDefault('PayGWCmd') :
                $rpc = \Sooh\Base\Rpc\Broker::factory('PayGWCmd');
                $sys = \Lib\Services\PayGWCmd::getInstance($rpc);
                try{
                    //$ret = call_user_func_array([$sys, 'unfreezeBalance'], $data);
                    $ret = \Lib\Services\PayGWCmd::sendToPayGWCmd('trade_unfreeze',['',$ordersId]);
                }catch (\ErrorException $e){
                    error_log('[error]'.__METHOD__.'#'.__LINE__.'#'.$ordersId.'#'.$e->getMessage());
                   // var_log($e->getTraceAsString(),'trace >>> ');
                    continue;
                }

                if($ret['code']==200){
                    $invest->setField('unfreeze',8);
                    try{
                        $invest->update();
                        error_log('[warning]'.__METHOD__.'#'.__LINE__.'#'.$ordersId.'#解冻成功>>>');
                    }catch (\ErrorException $e){
                        error_log('[warning]'.__METHOD__.'#'.__LINE__.'#'.$ordersId.'#'.$e->getMessage());
                        continue;
                    }
                }else if($ret['code']==400){
                    if(strpos($ret['msg'],'非成功订单')!==false){
                        error_log('[warning]'.__METHOD__.'#'.__LINE__.'#'.$ordersId.'#无需解冻');
                        $invest->setField('unfreeze',3);
                    }else{
                        $invest->setField('unfreeze',4);
                    }
                    try{
                        $invest->update();
                        error_log('[warning]'.__METHOD__.'#'.__LINE__.'#'.$ordersId.'#解冻失败#'.$ret['msg']);
                    }catch (\ErrorException $e){
                        error_log('[warning]'.__METHOD__.'#'.__LINE__.'#'.$ordersId.'#'.$e->getMessage());
                        continue;
                    }
                }else{
                    error_log('[warning]'.__METHOD__.'#'.__LINE__.'#'.$ordersId.'#网关返回了未知的CODE');
                }

            }
        }
        error_log('###[warning]扫描购买失败需要解冻的订单结束###');
    }
}
