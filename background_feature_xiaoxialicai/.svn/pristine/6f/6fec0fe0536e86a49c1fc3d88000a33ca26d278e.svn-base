<?php
namespace PrjCronds;
/**
 *
 *
 * php /var/www/licai_php/run/crond.php "__=crond/runactives&task=Actives.Activityspider&ymdh=20160427"
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/4/23 0023
 * Time: 下午 1:58
 */

class Activityspider extends \Sooh\Base\Crond\Task {

    public function init() {
        parent::init();
        $this->toBeContinue = true;
        $this->_secondsRunAgain = 180;  // 3分钟跑一次
        $this->_iissStartAfter = 0;
        $this->ret = new \Sooh\Base\Crond\Ret();
        $this->ret->newadd = 0;
        $this->ret->newupd = 0;
        $this->db_rpt = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_p2prpt);
        $this->db_produce = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_p2p_slave);
    }

    protected $db_rpt;
    protected $db_produce;
    protected $arr_admin = ['王宁' => 17717555734,'沈涛' => 18616626758, '张姝俪'=>15800862286, '程兆林'=>13818911475];
    protected $limitValue = [50, 100, 200, 300, 400, 600, 800];  // 票池剩余数量预警
    protected $msg2customer = '感谢您参加“2016小虾理财请您看电影”活动。您已获得蜘蛛网电影通兑券一张，通兑券电子码：{num}。您可进入蜘蛛网电影频道在快速购票频道选择电影和座位，并在支付页面选择使用蜘蛛系列卡券中的蜘蛛网通兑券即可完成换购！';
    protected $msg2admin = '运管：{admin}，现小虾平台剩余{remain}个未发放蜘蛛电影票兑换码，请注意补仓。';

    protected $failedOrderStatus = [
        \Prj\Consts\OrderStatus::created, // 0
        \Prj\Consts\OrderStatus::abandon, // -1
        \Prj\Consts\OrderStatus::unusual, // -4
    ];

    public function free() {
        parent::free();
        $this->db_rpt=null;
        $this->db_produce=null;
    }
    public function onRun($dt)
    {
        error_log('##########################################################');
//  TODO:
        $spider_active_from = '20160506000000';
        $spider_active_to = '20160605235959';
        $spider_ymd_active_from = substr($spider_active_from, 0, 8);
        $spider_ymd_active_to = substr($spider_active_to, 0, 8);

        $spider_amount_min = 100000;   // 分
        $spider_contract = [101020160426010000,101020160426110000,101020160426210000,]; // TODO:

        if($this->_isManual) {
            $ymd = $dt->YmdFull;
            if ($ymd<$spider_ymd_active_from || $ymd>$spider_ymd_active_to) {
                error_log('###Manual抓取订单时间范围['.date('Y-m-d', strtotime($ymd)).']不在活动时间！');
                return true;
            }else {
                error_log('###Manual抓取订单时间范围['.date('Y-m-d', strtotime($ymd)).']');
            }

            // 搜索时间段内购买订单的where条件
            $where = ['orderTime*'=>$ymd.'%'];

        }else {
            $dtFrom = date('YmdHis', $dt->timestamp() - 5400);  // 抓取订单的订单时间范围 一个半小时
            $dtTo = date('YmdHis', $dt->timestamp());

            if($dtFrom > $spider_active_to || $dtTo < $spider_active_from){
                error_log('###Auto抓取订单时间范围：['.date('Y-m-d H:i:s', strtotime($dtFrom)).','.date('Y-m-d H:i:s', strtotime($dtTo)).']不在活动时间');
                return true;
            }

            if($dtFrom <= $spider_active_from) {
                $dtFrom = $spider_active_from;
            }

            if($dtTo >= $spider_active_to){
                $dtTo = $spider_active_to;
            }
            error_log('###Auto抓取订单时间范围：['.date('Y-m-d H:i:s', strtotime($dtFrom)).','.date('Y-m-d H:i:s', strtotime($dtTo)).']');

            // 搜索时间段内购买用户的where条件
            $where = ['orderTime]'=>$dtFrom, 'orderTime['=>$dtTo];
        }

        $where['orderStatus!'] = $this->failedOrderStatus;
        $where['amount+amountExt]'] = $spider_amount_min;
        /**
         * 以用户的角度看，以后产品流标了，但也是购买成功了。
         *
         */
        // where 获取时间段内 购买>=1000元 非超级用户 （此时包含购买新手标的用户）
        $users = \Prj\Data\Investment::loopFindRecordsByFields($where, null, 'distinct(userId)', 'getCol');
//        error_log(\Sooh\DB\Broker::lastCmd());
//        var_log($users, '时间段内 购买>=1000元 用户 （包含购买新手标的用户）');

        if(empty($users)) {
            error_log('时间段内无成功购买>=1000元的订单（包含新手标订单）');
            return true;
        }

//         获取时间段内购买 >= 1000元的蜘蛛用户是在活动时间内注册并且不是超级用户的用户
//        $users = $this->db_rpt->getCol(\Rpt\Tbname::tb_user_final, 'userId',
//            ['ymdReg]'=>$spider_ymd_active_from, 'ymdReg['=>$spider_ymd_active_to, 'flagUser!'=>1, 'userId'=>$users, 'contractId'=>$spider_contract]);
        $users = \Prj\Data\User::loopFindRecordsByFields(['ymdReg]'=>$spider_ymd_active_from, 'ymdReg['=>$spider_ymd_active_to, 'isSuperUser!'=>1, 'userId'=>$users, 'contractId'=>$spider_contract],
            null, 'userId', 'getCol');
        if(empty($users)) {
            error_log('无符合条件的用户：时间段内购买的用户无蜘蛛网用户');
            return true;
        }
//var_log($users, 'users>>>>>>');
        // 遍历每个用户活动时间段内的订单
        foreach($users as $userId) {
            // 检查用户是否已经发送过电影票
            $n = $this->db_rpt->getRecordCount(\Rpt\Tbname::tb_activity_spider, ['userId'=>$userId]);
            if(!$n){
                $obj = \Prj\Data\Investment::getCopy($userId);
                $orders = $obj->db()->getRecords($obj->tbname(), 'ordersId, waresId, userId, orderTime, amount+amountExt as totalAmount',
                    ['orderStatus!'=>$this->failedOrderStatus, 'orderTime]'=>$spider_active_from, 'orderTime['=>$spider_active_to, 'userId'=>$userId],
                    'sort orderTime');

                foreach($orders as $order) {
                    $obj_wares = \Prj\Data\Wares::getCopy($order['waresId']);
                    $isNewBieProduct = $obj_wares->db()->getRecordCount($obj_wares->tbname(), ['waresId'=>$order['waresId'], 'tags*'=>'%新手%']);
                    if($isNewBieProduct) {
                        continue;
                    }else {
                        $obj_user = \Prj\Data\User::getCopy($userId);
                        $obj_user->load('userId,phone,nickname');
                        $userInfo = $obj_user->dump();
                        $obj_user->free();
                        if($order['totalAmount'] >= $spider_amount_min) {
                            // 发电影票
                            error_log('蜘蛛用户'.$userId.' '.$userInfo['nickname'].' 首次非新手标订单'.$order['ordersId'].' '.number_format($order['totalAmount']/100, 0, '.', '').'元可以发票');
                            $this->sendTicketMsg($userId, $userInfo['nickname'], $userInfo['phone']);
                        }else{
                            error_log('蜘蛛用户'.$userId.' '.$userInfo['nickname'].' 首次非新手标订单'.$order['ordersId'].' '.number_format($order['totalAmount']/100, 0, '.', '').'元不能发票');
                        }
                        break;
                    }

                    $obj_wares->free();
                }
                $obj->free();
            }else{
                error_log('蜘蛛用户：'.$userId.' 已经发过电影票');
            }

        }

        return true;
    }


    protected function sendTicketMsg ($userId, $name, $phone){
        $record = [
            'userId'        =>$userId,
            'realname'      =>$name,
            'phone'         =>$phone,
            'createTime'    =>  date('Y-m-d H:i:s'),
            'flagGranted'   =>1,
        ];
        $retry = 5;
        while ($retry>0) {
            $ticket_info = $this->db_rpt->getRecords(\Rpt\Tbname::tb_activity_spider, 'ticketSerialNo,userId', ['flagGranted'=>0,'flagMsg' => 0], null, 10);
            if (empty($ticket_info)) {
                error_log('票池已经没票了');
                return;
            }
            $k = array_rand($ticket_info);
            $ticket_info = $ticket_info[$k];
            try{
                \Sooh\DB\Broker::errorMarkSkip();
                $r = $this->db_rpt->updRecords(\Rpt\Tbname::tb_activity_spider, $record, ['ticketSerialNo'=>$ticket_info['ticketSerialNo'],'flagGranted'=>0, 'flagMsg'=>0]);
                if($r!==true){
                    break;
                }

            }catch(\ErrorException $e) {
                if(\Sooh\DB\Broker::errorIs($e)){
                    error_log('蜘蛛用户：'.$userId.' 此用户的票已经发过了');
                    return;
                }
            }

            $retry--;
        }

        if($retry > 0) {

            // 给用户发短信
            $retry = 5;
            $msg = str_replace('{num}',$ticket_info['ticketSerialNo'], $this->msg2customer);
            while($retry>0){
                $result = $this->sendMsg($phone, $msg);
                if($result){
                    $this->db_rpt->updRecords(\Rpt\Tbname::tb_activity_spider, ['flagMsg'=>1], ['ticketSerialNo'=>$ticket_info['ticketSerialNo'],'flagMsg'=>0]);
                    break;
                }else{
                    $retry--;
                }
            }

            if($retry > 0) {
                error_log('蜘蛛用户'.$userId.' '.$name.' 发票成功 发短信成功');
            }else{
                error_log('蜘蛛用户'.$userId.' '.$name.' 发票成功 发短信失败');
            }

            // 给管理员发短信
            $nLeft = $this->db_rpt->getRecordCount(\Rpt\Tbname::tb_activity_spider, ['flagGranted'=>0, 'flagMsg'=>0]);
            if(in_array($nLeft, $this->limitValue)) {
                foreach($this->arr_admin as $adminName => $phone) {
                    $msg = str_replace(['{admin}', '{remain}'], [$adminName,$nLeft], $this->msg2admin);
                    $retry =  5;
                    while($retry > 0){
                        $result = $this->sendMsg($phone,$msg);
                        if($result){
                            error_log('票池剩余票数:'.$nLeft. ' 给管理员'.$adminName.'发送短信成功');
                            break;
                        }
                        $retry--;
                    }
                    if(!$retry){
                        error_log('票池剩余票数:'.$nLeft. ' 给管理员'.$adminName.'发送短信失败');
                    }
                }
            }
        }else {
            error_log('蜘蛛用户'.$userId.' '.$name.' 发票失败');
        }
    }

    // 发短信
    protected function sendMsg($phone, $msg) {
        try {
            \Lib\Services\SMS::getInstance()->sendNotice($phone, $msg);
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }

}