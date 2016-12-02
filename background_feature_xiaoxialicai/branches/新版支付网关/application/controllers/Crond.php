<?php

use Prj\Data\Summary;
/**
 * 定时任务，客户端请跳过
 * 0  *  *  *  *  root /usr/bin/php /var/www/xxxx/public/crond.php "__=crond/hourly" 2>&1
 * 45  *  *  *  *  php /var/www/miao_phpdev/public/crond.php "__=crond/waresque" 2>&1
 * 1  19  *  *  *  php /var/www/miao_phpdev/public/crond.php "__=crond/paygw" 2>&1
 * 1  20  *  *  *  php /var/www/miao_phpdev/public/crond.php "__=crond/sendRebateRedPacket" 2>&1
 * @author Simon Wang <hillstill_simon@163.com>
 */
class CrondController  extends \Prj\BaseCtrl {
	/**
	 * 检查，必须是通过crond.php执行的
	 */
	public function init()
	{
		\Sooh\Base\Rpc\Broker::$_rpcServices = \Lib\Services\Rpcservices::getInstance(null);
		$this->ini = \Sooh\Base\Ini::getInstance();
		if($this->ini->get('released')){
			if(SOOH_INDEX_FILE!=='crond.php'){
				die('not start with crond');
			}
		}
	}
	/**
	 * 手动执行
	 */
	public function runAction()
	{
		$ctrl = new \Sooh\Base\Crond\Ctrl(APP_PATH."/application/Cronds", "__=crond/run", \Lib\Services\CrondLog::getInstance(null));//\Prj\BaseCtrl::getRpcDefault('CrondLog')
		$ctrl->initNamespace('PrjCronds');
		$ctrl->runManually($this->_request->get('task'), $this->_request->get('ymdh'));
	}
	/**
	 * 每小时自动执行
	 */
	public function  hourlyAction()
	{
		$ctrl = new \Sooh\Base\Crond\Ctrl(APP_PATH."/application/Cronds", "__=crond/hourly",\Lib\Services\CrondLog::getInstance(null));//\Prj\BaseCtrl::getRpcDefault('CrondLog')
		$ctrl->initNamespace('PrjCronds');
		$ctrl->runCrond($this->_request->get('task'), $this->_request->get('ymdh'));
	}
	/**
	 * 数据抓取定时任务
	 */
	public function grabAction () {
		$dbConf= $GLOBALS['CONF']['dbConf'];
		$dbConf['default'] = $GLOBALS['CONF']['dbConfMirror']['default'];
		$ini = \Sooh\Base\Ini::getInstance();
		$ini->initGobal(['dbConf'=>$dbConf]);

		$ctrl = new \Sooh\Base\Crond\Ctrl(APP_PATH."/application/Crondsgrab", "__=crond/grab",\Lib\Services\CrondLog::getInstance(null));
		$ctrl->initNamespace('PrjCronds');
		$ctrl->runCrond($this->_request->get('task'), $this->_request->get('ymdh'));
	}

	/**
	 * 手动执行抓取任务
	 */
	public function rungrabAction()
	{
		$dbConf= $GLOBALS['CONF']['dbConf'];
		$dbConf['default'] = $GLOBALS['CONF']['dbConfMirror']['default'];
		$ini = \Sooh\Base\Ini::getInstance();
		$ini->initGobal(['dbConf'=>$dbConf]);

		$ctrl = new \Sooh\Base\Crond\Ctrl(APP_PATH."/application/Crondsgrab", "__=crond/rungrab", \Lib\Services\CrondLog::getInstance(null));
		$ctrl->initNamespace('PrjCronds');
		$ctrl->runManually($this->_request->get('task'), $this->_request->get('ymdh'));
	}

    /**
     * 活动目录定时任务
     * By Hand
     */
    public function activesAction()
    {
        $ctrl = new \Sooh\Base\Crond\Ctrl(APP_PATH."/application/Crondsact", "__=crond/actives",\Lib\Services\CrondLog::getInstance(null));//\Prj\BaseCtrl::getRpcDefault('CrondLog')
        $ctrl->initNamespace('PrjCronds');
        $ctrl->runCrond($this->_request->get('task'), $this->_request->get('ymdh'));
    }

    /**
     * 手动执行活动目录
     */
    public function runactivesAction()
    {
        $ctrl = new \Sooh\Base\Crond\Ctrl(APP_PATH."/application/Crondsact", "__=crond/run", \Lib\Services\CrondLog::getInstance(null));//\Prj\BaseCtrl::getRpcDefault('CrondLog')
        $ctrl->initNamespace('PrjCronds');
        $ctrl->runManually($this->_request->get('task'), $this->_request->get('ymdh'));
    }


	/**
	 * 每个小时59分启动一次，用于更新标的队列情况
	 */
	public function waresqueAction()
	{
		$wares = \Prj\Data\Wares::getCopy('');
		$db = $wares->db();
		$tb = $wares->tbname();
		$step=12;
		$dt = \Sooh\Base\Time::getInstance();
        $num = 0;
		while($step>0){
			$step--;
            $num++;
			//TIPS: 外面写个封装类，这里调用；
			//TIPS: 每日每个类型可显示的等待上架的和可购买的标的的数量放在tb_config里
			//TIPS: 从等待变成可购买的条件是时间和数量限制都符合，从最早的里面挑一个
			//code here : 检查是否有需要新上的标的
			//code here : 检查是否有需要结标的标的
            var_log('[warning]标的上架检查定时器>>>');
			error_log($num.'[ Trace ] ### '.__CLASS__.' ### Task start YmdHis:'.date('Y-m-d H:i:s',$dt->timestamp()));
			
			$where = [
				'statusCode'=>\Prj\Consts\Wares::status_ready,
				'timeStartPlan['=>$dt->ymdhis(),
			];
			$rs = $db->getRecords($tb,'waresId',$where);
			if(!empty($rs))
			{
                $tmp = '';
				foreach($rs as $v)
				{
                    $tmp.=($v['waresId'].' ');
					$ware = \Prj\Data\Wares::getCopy($v['waresId']);
					$ware->load();
					$ware->setField('statusCode',\Prj\Consts\Wares::status_open);
					$ware->setField('timeStartReal',$dt->ymdhis());
					$ware->setField('statusCode1',0);
					$ware->update();
				}
                error_log('上架标的:'.$tmp);
			}
			error_log($num.'[ Trace ] ### '.__CLASS__.' ### on '.date('Y-m-d H:i:s',$dt->timestamp()).' Change:'.  json_encode($rs));
			sleep(300);
			$dt->reset();
		}
	}
	/**
	 * 每天7点从支付网关获取昨日数据（对账，货基收益等等）
	 */
	public function paygwAction()
	{
		\Sooh\Base\Rpc\Broker::$_rpcServices = \Lib\Services\Rpcservices::getInstance(null);
        $ymd = $this->_request->get('ymd',date('Ymd',  \Sooh\Base\Time::getInstance()->timestamp(-1)));
        var_log('[warning]对账定时器开始>>>');
        $ctrlName = ['DayRecharges','DayWithdraw','DayBuy','DayLoan','DayPayback','DayPaysplit','DayInterest','DayManage'];
        $cmd = $this->_request->get('cmd');
        if($cmd)$ctrlName = [$cmd];
        foreach($ctrlName as $v){
            $className = '\Prj\Check\\'.$v;
            $className::saveData($ymd);
            if($v!='DayInterest'){
                $className::check($ymd);
            }
        }
        var_log('[warning]对账定时器结束>>>');
	}

    /**
     * 每天24点前 发放前一天的邀请红包 挑选金额最高的三个发放
     */
    public function sendRebateRedPacketAction(){
        $ymd = $this->_request->get('ymd',date('Ymd',  \Sooh\Base\Time::getInstance()->timestamp(-1)));
        var_log('[warning]发放邀请红包定时器>>>');

        $tmp = \Prj\Data\Vouchers::getCopy('');
        //$ymd = '20160304';
        $where = ['codeCreate'=>'firstBuyInvite','LEFT(timeCreate,8)'=>$ymd,'statusCode'=>\Prj\Consts\Voucher::status_freeze];
        $rs = $tmp->loopFindRecords($where);
        $users = [];
        if(!empty($rs)){
	        array_map(function ($v) use (&$users) {
		        $users[] = $v['userId'];
	        }, $rs);
            $users = array_unique($users);
            foreach($users as $v){
                try{
//                    $ret = \Prj\Items\RedPacketForFirstBuyForInvite::sendRebateRedPacket($v,$ymd);
	                $ret = \Prj\Items\NewFirstBuyForInviteRedPacket::sendRebateRedPacket($v, $ymd);
                }catch (\ErrorException $e){
                    var_log($e->getMessage());
                    sleep(20);
                    try{
//                        $ret = \Prj\Items\RedPacketForFirstBuyForInvite::sendRebateRedPacket($v,$ymd);
	                    $ret = \Prj\Items\NewFirstBuyForInviteRedPacket::sendRebateRedPacket($v, $ymd);
                    }catch (\ErrorException $e){
                        var_log($e->getMessage());
                    }
                }
                var_log('[warning]邀请红包发放 userId:'.$v.' ymd:'.$ymd);
                var_log($ret,'发放结果>>>');
            }
        }else{
            var_log('[warning]没有需要发放邀请红包的用户');
        }

    }

    /**
     * 定时同步用户的红包金额
     * 半夜1点执行一次
     * @throws ErrorException
     */
    public function syncUserRedPacketAction(){
        var_log("####[warning]红包同步定时任务开始###");
        $where = [];//['redPacket>'=>0,];
        $users = \Prj\Data\User::loopFindRecords($where);
        if(!empty($users)){
            foreach($users as $arr){
                $userId = $arr['userId'];

                $user = \Prj\Data\User::getCopy($userId);
                $user->load();
                if(!$user->exists()){
                    var_log('[error]用户不存在');
                    continue;
                }else{
                    $total = \Prj\Data\Vouchers::getTotalByUserId($userId);
                    if($total==$user->getField('redPacket')){
                        continue;
                    }else{
                        $redPacket = $user->getField('redPacket');
                        $user->setField('redPacket',$total);
                        var_log('[warning]开始刷新用户:'.$userId.' 的红包总额...');
                        try{
                            $user->update();
                        }catch (\ErrorException $e){
                            var_log('[warning]更新失败!');
                            var_log($e->getTraceAsString());
                        }
                        var_log('[warning]更新成功 '.$redPacket.' => '.$total);
                    }
                }
            }
        }
        var_log("####[warning]红包同步定时任务结束###");
    }

    /**
     * 自动满标转账 建议一小时执行一次
     */
    public function transAction(){
		\Sooh\Base\Rpc\Broker::$_rpcServices = \Lib\Services\Rpcservices::getInstance(null);
        var_log("###[warning]满标转账定时任务开始###");
        if(date('Hi') < '0930' || date('Hi')>'2330'){
            error_log('trans#9点30之前不执行>>>');
            error_log('trans#23点30之后不执行>>>');
            goto over;
        }
        $waresId = $this->_request->get('waresId');
        if(!empty($waresId)){ //单体
            $ware = \Prj\Data\Wares::getCopy($waresId);
            $ware->load();
            if(!$ware->exists()){
                var_log('[error]标的不存在 wares:'.$waresId);
            }else{
                try{
                    $ret = $ware->trans();
                    var_log("转账结果:".json_encode($ret));
                }catch (\ErrorException $e){
                    var_log('[error]转账失败 waresId:'.$waresId.' error:'.$e->getMessage());
                }
            }
        }else{ //AOE
            //符合满标转账的条件
            if(!\Prj\Data\Config::get('AUTO_TRANS')){
                var_log("###[warning]满标转账定时任务开关关闭###");
                goto over;
            }
            
            $where = [
                'statusCode'=>\Prj\Consts\Wares::status_go,
                'payStatus'=>0,
            ];
            $tmp = \Prj\Data\Wares::getCopy('');
            $waresList = $tmp::loopFindRecords($where);
            if(empty($waresList)){

            }else{
                foreach($waresList as $v){
                    try{
                        $ware = \Prj\Data\Wares::getCopy($v['waresId']);
                        $ware->load();
                        if(!$ware->exists()){
                            var_log('标的不存在 waresId:'.$v['waresId']);
                            continue;
                        }else{
                            $ret = $ware->trans();
                        }

                    }catch (\ErrorException $e){
                        var_log('满标转账处理失败 waresId:'.$v['waresId'].' error:'.$e->getMessage());
                        continue;
                    }
                    var_log('满标转账处理成功 waresId:'.$v['waresId'].' ret:'.json_encode($ret));
                }
            }
        }
        over:
        var_log("###[warning]满标转账定时任务结束###");
    }

    /**
     * 发送储钱罐利息
     */
    public function sendDayInterestAction(){
		set_time_limit(3600);
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        var_log("####[warning]发送储钱罐利息任务开始###");
        $go = \Prj\Data\Config::get('sendDayInterest');
        if($go){
            $records = \Prj\Data\DayInterest::loopFindRecords(['statusCode'=>0,'amount>'=>0]);
            if(!empty($records)){
                foreach($records as $v){
                    try{
                        \Prj\Wares\Wares::getCopy()->sendDayInterest($v['sn']);
                    }catch (\ErrorException $e){
                        var_log($e->getMessage(),'[warning]'.$v['sn']);
                        continue;
                    }
                    var_log('处理成功>>>',$v['sn']);
                }
            }
        }else{
            var_log("####[warning]任务开关关闭###");
        }
        var_log("####[warning]发送储钱罐利息任务结束###");
    }

    public function updateMarketingSecondAction(){
        var_log("####[warning]二次营销定时任务开始###");
        $ymd = $this->_request->get('ymd',date('Ymd',  \Sooh\Base\Time::getInstance()->timestamp()));
        $ret = [];
        for($i=0;$i<7;$i++){
            $newYmd = date('Ymd',strtotime('-'.$i.' days',strtotime($ymd)));
            //var_log($newYmd);
            $users = \Prj\Data\User::loopFindRecords(['ymdReg'=>$newYmd]);
            if(empty($users)){
                continue;
            }else{
                foreach($users as $v){
                    $ret[] = \Prj\Data\MarketingSecond::updateData($v,'crond');
                }
            }
        }
        //var_log($ret);
        $this->_view->assign('ret',$ret);
        var_log("####[warning]二次营销定时任务结束###");
    }

    /**
     * 借款人自动回款
     */
    public function autoConfirmAction(){
        error_log("####[warning]自动还款定时任务开始###");
        $lazy = 1; //延缓执行
        if($lazy)error_log("autoConfirm#延缓执行开启>>>");
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $ymd = $this->_request->get('ymd');
        if(empty($ymd))$ymd = date('Ymd');

        $where = [
            'statusCode'=>\Prj\Consts\Wares::status_return,
            'payStatus'=>\Prj\Consts\PayGW::success,
            'LEFT(nextConfirmYmd,8)'=>$ymd,
            'autoConfirm'=>1,
        ];
        $result = [];
        $list = \Prj\Data\Wares::loopFindRecords($where);
        if($list){
            foreach($list as $v){
                error_log('autoConfirm#'.$v['waresId'].'#'.$v['waresName'].'开始自动还款>>>');
                $wares = \Prj\Data\Wares::getCopy($v['waresId']);
                $wares->load();
                if(!is_array($v['returnPlan'])){
                    $v['returnPlan'] = json_decode($v['returnPlan'],true);
                }
                $rp = \Prj\ReturnPlan\All01\ReturnPlan::createPlan($v['returnPlan']);
                $plan = current($rp->getPlan(['planDateYmd'=>$ymd]));
                var_log($plan,$v['waresId'].'还款计划>>>');
                if(empty($plan)){
                    var_log('[error]'.$v['waresName'].'#'.$v['waresId'].'#系统异常,找不到的计划>>>');
                    continue;
                }
                if($plan['remitStatus']!=\Prj\Consts\PayGW::success){
                    /* todo 
                    var_log('[warning]'.$v['waresName'].'#'.$v['waresId'].'#尚未给借款人打款 >>>');
                    continue;
                    */
                }
                error_log('autoConfirm#'.$v['waresName'].'#'.$v['waresId'].'#该笔处理状态#'.$plan['status']);
                if($plan['status']==\Prj\Consts\PayGW::success){
                    continue;
                }
                if($plan['status']==\Prj\Consts\PayGW::accept){
                    continue;
                }
                $newApi = 1;
                //if($wares->getField('timeStartReal')>'20160818000000')$newApi = 1;
                try{
                    if($newApi){
                        error_log('autoConfirm#执行新的支付网关...');
                        $ret = \Prj\Wares\Wares::doConfirm(0,$plan['interest']+$plan['amount'],0,$wares->getField('managementConfirm'),$plan['planDateYmd'],$plan['ahead'],0,$wares);
                    }else{
                        error_log('autoConfirm#执行旧的支付网关...');
                        $ret = \Prj\Wares\Wares::getCopy()->confirm($wares,$plan);
                    }
                }catch (\ErrorException $e){
                    var_log('[warning]'.$v['waresName'].'#'.$v['waresId'].'#'.$e->getMessage());
                    continue;
                }
                $result[] = '[success]>>>'.$v['waresName'].'#'.$v['waresId'];
                var_log('[success]>>>'.$v['waresName'].'#'.$v['waresId']);
                if($lazy)break;
            }
        }
        $this->_view->assign('ret',$result);
        over:
        error_log("####[warning]自动还款定时任务结束###");
    }

    /**
     * 每天中午12:00 给绑卡没有签到的用户发推送和站内信
     * 定时任务
     */
    public function checkintaskAction()
    {
        error_log("####[warning]自动给绑卡没有签到的用户发送短信任务开始###");
        $pager = new \Sooh\DB\Pager($this->_request->get('pageSize', 300), $this->pageSizeEnum, false);
        $where = [];
        $usercount = \Prj\Data\User::getCount($where);
        $pager->init($usercount);
        $pagecount = $pager->page_count;
        
        for ($i = 1; $i <= $pagecount; $i ++) {
            
            if ($i == 1) {
                $user = \Prj\Data\User::loopGetRecordsPage([
                    'userId' => 'sort','ymdReg'=>'sort'
                ], null, $pager->init($usercount, $i));
            } else {
                $user = \Prj\Data\User::loopGetRecordsPage([
                    'userId' => 'sort','ymdReg'=>'sort'
                ], $lastPage, $pager->init($usercount, $i));
            }
            $lastPage = $user['lastPage'];
            $user = $user['records'];
           
            foreach ($user as $v) {
                $userId = $v['userId'];
                $ymdBindcard = $v['ymdBindcard'];
                
                if (empty($ymdBindcard))
                    continue;
                $checkinbook = $v['checkinBook'];
                
                $checkinbook = json_decode($checkinbook, true);
                $date = date('Ymd', time());
                
                if ($date == $checkinbook['ymd']) {
                    continue;
                } else {
                   
                    \Prj\Message\Message::run([
                        'event' => 'sign_in_remind',
                        'brand' => \Prj\Message\Message::MSG_BRAND
                    ]
                    , [
                        'userId' => $userId
                    ]);
                }
            }
            
        sleep(1);
        }
        error_log("####[warning]自动给绑卡没有签到的用户发送短信任务结束###");
    }
    
    /**
     * 在2016-07-05到2016-07-15期间
     * 
     * 当天签到，没有使用签到奖励的用户发送推送和站内信的定时任务
     * 
     * */
    
    public  function checkinVoucherUserTaskAction(){
         
        error_log("####[warning]自动给当天签到但是没有使用签到奖励的用户发送推送任务开始###");
        $timeForm='20160705000000';
        $timeEnd='20160715235959';
         
        $push_msg='亲，您还有1.88元的签到红包没有使用，点我马上花掉TA';
        $msg_title = '签到红包未使用提醒';
    
        $dateTime=date('YmdHis',time());
    
        $dayForm=date('Ymd',time());
        $dayFormtrue=$dayForm.'000000';
        $dayEnd=$dayForm.'235959';
         
        if($dateTime>=$timeForm && $dateTime<=$timeEnd){
    
            $db_p2p=\Sooh\DB\Broker::getInstance();
            $where=[
                'timeCreate]' => $dayFormtrue,
                'timeCreate[' => $dayEnd,
                'codeCreate'=>'actives.checkin',
                'statusCode'=>\Prj\Consts\Voucher::status_unuse,
            ];
    
            $arr_user_id = \Prj\Data\Vouchers::loopFindRecordsByFields($where, null, 'distinct(userId) as uid', 'getCol');
            var_log($arr_user_id,'>>>>>>>>>>>>>');
            if(!empty($arr_user_id)){
                foreach ($arr_user_id as $uid){
                     
                    try{
                        \Lib\Services\Message::getInstance()->push($uid, $push_msg);
                    }catch(\ErrorException $e) {
                        error_log('### Trace vouchers overdue remind: ### '. $dateTime.' push　failed! errMsg:'.$e->getMessage());
                    }
    
                    try {
    
                        \Lib\Services\Message::getInstance()->add(0, $uid, 5,  $msg_title, $push_msg, null, false);
    
                    }catch(\ErrorException $e){
                        error_log('### Trace vouchers overdue remind: ### '. $dateTime.' msg　failed! errMsg:'.$e->getMessage());
                    }
                }
            }
        }
        error_log("####[warning]自动给当天签到但是没有使用签到奖励的用户发送推送任务结束###");
         
    }
} 