<?php
namespace PrjCronds;
/**
 * 检查等待上架的标的,决定哪些显示,哪些不显示
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class CheckWaitWares extends \Sooh\Base\Crond\Task{
	public function init() {
		parent::init();
		$this->toBeContinue=true;
		$this->_secondsRunAgain=300;//每5分钟启动一次
		$this->_iissStartAfter=200;//每小时02分后启动

		$this->ret = new \Sooh\Base\Crond\Ret();

	}
	public function free() {
		parent::free();
	}

	/**
	 * @param \Sooh\Base\Time $dt
	 */
	protected function onRun($dt) {
		
		if($this->_isManual){
			$m='manual';
		}else{
			$m='auto';
		}
        error_log('#>>>待上架标的队列检查开始>>>#');
        $this->checkWaitWares();
		
		if($this->_counterCalled==1){
			error_log("[TRace]".__CLASS__.'# first by '.$m.' #'.$this->_counterCalled);
		}else{
			error_log("[TRace]".__CLASS__.'# continue by '.$m.' #'.$this->_counterCalled);
		}
		$this->lastMsg = $this->ret->toString();//要在运行日志中记录的信息
        error_log('#>>>待上架标的队列检查结束>>>#');
		return true;
	}

    protected function checkWaitWares(){
        $op = '#待上架标的队列检查#';
        $wares = \Prj\Data\Wares::getCopy('');
        $wares->load();
        $waresWait = $wares->db()->getRecords($wares->tbname(),'*',['statusCode'=>\Prj\Consts\Wares::status_ready,'shelfId'=>\Prj\Consts\Wares::shelf_house],'sort timeStartPlan');
        $this->refreshWares($waresWait);
        $waresWait = $wares->db()->getRecords($wares->tbname(),'*',['statusCode'=>\Prj\Consts\Wares::status_ready,'shelfId'=>\Prj\Consts\Wares::shelf_car],'sort timeStartPlan');
        $this->refreshWares($waresWait);

    }

    protected function upStatusCode1($waresId,$oldStatus,$newStatus){
        if($newStatus==0){
            if($oldStatus!=0){
                //todo 将状态置为显示 0
                return $this->changeStatus($waresId,$newStatus);
            }
        }else{
            if($oldStatus==0){
                //todo 将状态置为隐藏 -1
                return $this->changeStatus($waresId,$newStatus);
            }
        }
    }

    protected function changeStatus($waresId,$newStatus){
        $op = '#待上架标的队列检查#';
        $ware = \Prj\Data\Wares::getCopy($waresId);
        $ware->load();
        if(!$ware->exists())return '商品不存在';
        $ware->setField('statusCode1',$newStatus);
        if($newStatus == 0){
            error_log($op.$waresId.'#'.$ware->getField('waresName').'#将状态置为显示>>>');
        }else{
            error_log($op.$waresId.'#'.$ware->getField('waresName').'#将状态置为隐藏>>>');
        }

        try{
            $ware->update();
        }catch (\ErrorException $e){
            error_log($op.$waresId.'#'.$ware->getField('waresName').'#[warning]状态更新失败>>>'.$e->getMessage());
            return $e->getMessage();
        }
        return true;
    }

    /**
     * @param $waresWait
     * @param $tmp
     */
    protected function refreshWares($waresWait = null)
    {
        $tagCheck = []; //根据tag匹配
        if ($waresWait) {
            $newList = [];
            foreach ($waresWait as $k => $v) {
                $tagArr = explode(',', $v['tags']);
                if (!empty($tagArr)) {
                    $tagSort = 0;
                    foreach ($tagArr as $vv) {
                        if (array_key_exists($vv, $tagCheck)) {
                            $tagSort += $tagCheck[$vv];
                        } else {
                            $value = rand(1000, 9999);
                            $tagSort += $value;
                            $tagCheck[$vv] = $value;
                        }
                    }
                    $waresWait[$k]['tagSort'] = $tagSort;
                } else {
                    $waresWait[$k]['tagSort'] = 0;
                }

                $tmp['waresId']       = $v['waresId'];
                $tmp['tags']          = $v['tags'];
                $tmp['waresName']     = $v['waresName'];
                $tmp['statusCode']    = $v['statusCode'];
                $tmp['statusCode1']   = $v['statusCode1'];
                $tmp['timeStartPlan'] = $v['timeStartPlan'];
                $tmp['tagSort']       = $tagSort;
                $newList[]            = $tmp;
            }

            usort($newList, function ($a, $b) {
                if ($a['tagSort'] > $b['tagSort']) {
                    return 1;
                } elseif ($a['tagSort'] < $b['tagSort']) {
                    return -1;
                } else {
                    if ($a['timeStartPlan'] > $b['timeStartPlan']) {
                        return 1;
                    } else {
                        return -1;
                    }
                }
            });

            $maxNum  = \Prj\Data\Config::get('WARES_PLAN_NUM') - 0;
            $tmpSort = 0;
            $tmpNum  = 0;
            foreach ($newList as $v) {
                if ($tmpSort != $v['tagSort']) {
                    $tmpSort = $v['tagSort'];
                    $tmpNum  = 1;
                    //todo 将状态置为显示 0
                    $this->upStatusCode1($v['waresId'], $v['statusCode1'], 0);
                } else {
                    $tmpNum++;
                    if ($tmpNum > $maxNum) {
                        //todo 将状态置为隐藏 -1
                        $this->upStatusCode1($v['waresId'], $v['statusCode1'], -1);
                    } else {
                        //todo 将状态置为显示 0
                        $this->upStatusCode1($v['waresId'], $v['statusCode1'], 0);
                    }
                }
            }
        }
    }
}
