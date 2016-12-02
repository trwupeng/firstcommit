<?php
namespace PrjCronds;
use Lib\Services\PayGWCmd;

/**
 * 检查失败的订单，回复本金券状态
 * php /var/www/licai_php/run/crond.php "__=crond/run&task=Standalone.CheckBorrowerWallet" 2>&1
 * @author Simon Wang <hillstill_simon@163.com>
 */
class CheckBorrowerWallet extends \Sooh\Base\Crond\Task{
	public function init() {
		parent::init();
		$this->toBeContinue=true;
		$this->_secondsRunAgain=3600;//每5分钟启动一次
		$this->_iissStartAfter=100;//每小时01分后启动

		$this->ret = new \Sooh\Base\Crond\Ret();

	}
	public function free() {
		parent::free();
	}

    protected $hours = [1];

	protected function onRun($dt) {
        if(!empty($this->hours))if(!in_array($dt->hour,$this->hours)){
            var_log($this->hours,'指定的时间>>>');
            return true;
        }
        error_log('###[warning]同步借款人的余额开始###');
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
        $this->checkBorrowerWallet();
        error_log('###[warning]同步借款人的余额结束###');
		return true;
	}

    protected function checkBorrowerWallet(){
        $borrowerConfig = \Prj\Data\Config::get('borrower');
        if(!is_array($borrowerConfig)){
            return error_log('[error]错误的借款人配置');
        }else{
            $borrowers = array_keys($borrowerConfig);
        }
        $records = \Prj\Data\User::loopFindRecords(['userId'=>$borrowers]);
        //var_log($records , 'records >>> ');
        if($records){
            foreach($records as $record){
                $userId = $record['userId'];
                try{
                    $data = [$userId];
                    $ret = \Lib\Services\PayGWCmd::sendToPayGWCmd('queryBalance',$data);
                }catch (\ErrorException $e){
                    error_log('[error]'.__METHOD__.'#userId:'.$userId.'#'.$e->getMessage());
                    continue;
                }
                if($ret['code']==400){
                    error_log('[error]'.__METHOD__.'#userId:'.$userId.'#'.$ret['msg']);
                    continue;
                }else{
                    //var_log($ret['data']);
                    if($ret['data']['availableBalance']!==null){
                        error_log('availableBalance:'.$ret['data']['availableBalance']);
                        $walletSina = round($ret['data']['availableBalance']*100);
                        $user = \Prj\Data\User::getCopy($userId);
                        $user->load();
                        if(!$user->exists()){
                            error_log('[error]'.__METHOD__.'#userId:'.$userId.'#不存在的用户');
                            continue;
                        }
                        $wallet = $user->getField('wallet');
                        if($walletSina == $wallet){
                            error_log('[warning]'.__METHOD__.'#userId:'.$userId.'#正常的金额,无需更新');
                            continue;
                        }
                        $user->setField('wallet',$walletSina);
                        try{
                            $user->update();
                            error_log('>>>[success]>>>'.__METHOD__.'#userId:'.$userId.'#金额同步成功#old:'.$wallet.'#new:'.$walletSina);
                        }catch (\ErrorException $e){
                            error_log('[error]'.__METHOD__.'#userId:'.$userId.'#'.$e->getMessage());
                        }
                    }else{
                        error_log('[error]'.__METHOD__.'#userId:'.$userId.'#');
                        continue;
                    }
                }
            }
        }
    }
}
