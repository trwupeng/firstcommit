<?php
namespace PrjCronds;
 /**
  * php /var/www/licai_php/run/crond.php "__=crond/rungrab&task=Standalone.CrondNewOrder&ymdh=20160309"
  *
  * 状态2， 3 是用户购买成功，但是还没有从新浪扣款的。 2和3可以认为用户购买成功了。
  * 抓取指定时间段内成功的订单
  * 检查并更新每个用户tb_user_final中的ymdFirstBuy, numFirstBuy
  * 更新每个用户tb_user_final中的ymdLastBuy,numLastBuy，这两个数据是获取的指定日期内用户的最后成功订单。
  * 更新每个用户tb_user_final中的maxBoughtAmount
  *
  */

class CrondNewOrder extends \Rpt\Misc\DataCrondGather
{
    public function init()
    {
        parent::init();
        $this->_iissStartAfter = 500;
        $this->_secondsRunAgain = 300;
        $this->db_rpt = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_p2prpt);
        $this->excludeUser = \Rpt\Funcs::getexcludedUser();
    }

    public function free()
    {
        parent::free();
        $this->db_rpt = null;
    }

    protected $db_rpt;
    protected $excludeUser;
    protected function gather()
    {
        $this->printLogOfTimeRang();
        $startTime = date('YmdHis', $this->dtFrom);
        $endTime = date('YmdHis', $this->dtTo);
        $failed_order_status = [
            \Prj\Consts\OrderStatus::created,
            \Prj\Consts\OrderStatus::abandon,
            \Prj\Consts\OrderStatus::unusual,
        ];


        $where = ['orderStatus!' =>$failed_order_status, 'orderTime]' => $startTime, 'orderTime[' => $endTime];
        $arr_user = \Prj\Data\Investment::loopFindRecordsByFields($where, null, 'distinct(userId)', 'getCol');
        $arr_order_all = [];
        if(!empty($arr_user)) {
            foreach($arr_user as $userId) {
                if(in_array($userId, $this->excludeUser)) {
                    continue;
                }
                $o = \Prj\Data\Investment::getCopy($userId);
                $db = $o->db();
                $tbname = $o->tbname();
                $where['userId'] = $userId;
                $rs = $db->getRecords($tbname, \Rpt\Fields::$tb_orders_produce_fields, $where);
//var_log(\Sooh\DB\Broker::lastCmd());
                foreach ($rs as $record) {
                    $this->ret->total++;
                    $arr_order_all[] = $record['ordersId'];
                    $tmp = [
                        'ordersId' => $record['ordersId'],
                        'waresId' => $record['waresId'],
                        'waresName' => $record['waresName'],
                        'shelfId' => $record['shelfId'],
                        'userId' => $record['userId'],
                        'realname' => $record['nickname'],
                        'amount' => $record['amount'],
                        'amountExt' => $record['amountExt'],
                        'amountFake' => $record['amountFake'],
                        'yieldStaticAdd' => $record['yieldStaticAdd'],
                        'yieldStatic' => $record['yieldStatic'],
                        'yieldExt' => $record['yieldExt'],
                        'interest' => $record['interest'],
                        'interestStatic' => $record['interestStatic'],
                        'interestAdd' => $record['interestAdd'],
                        'interestFloat' => $record['interestFloat'],
                        'interestExt' => $record['interestExt'],
                        'interestSub' => $record['interestSub'],
                        'returnAmount' => $record['returnAmount'],
                        'returnInterest' => $record['returnInterest'],
                        'ymd' => date('Ymd', strtotime($record['orderTime'])),
                        'hhiiss' => date('His', strtotime($record['orderTime'])),
                        'orderStatus' => $record['orderStatus'],
                        'vouchers' => $record['vouchers'],
                        'firstTimeInAll' => $record['firstTime'],
                        'returnType' => $record['returnType'],
                        'lastReturnFundYmd' => $record['lastReturnFundYmd'],
                        'returnNext' => $record['returnNext'],
                    ];
                    if ($record['transTime'] > 0) {
                        $tmp['ymdTrans'] = date('Ymd', strtotime($record['transTime']));
                        $tmp['hisTrans'] = date('His', strtotime($record['transTime']));
                    }
                    $objWare = \Prj\Data\Wares::getCopy($record['waresId']);
                    $objWare->load(['mainType', 'subType']);
                    $type = $objWare->dump();
                    $tmp['mainType'] = $type['mainType'];
                    $tmp['subType'] = $type['subType'];
                    $objWare->free();
                    $type = null;

                    try {
                        \Sooh\DB\Broker::errorMarkSkip();
                        $this->db_rpt->addRecord(\Rpt\Tbname::tb_orders_final, $tmp);
                        $this->ret->newadd++;
                    } catch (\ErrorException $e) {
                        unset($tmp['ordersId']);
                        $this->db_rpt->updRecords(\Rpt\Tbname::tb_orders_final, $tmp, ['ordersId' => $record['ordersId']]);
                        $this->ret->newupd++;
                    }
                }
                $o->free();


                /**
                 *
                 * 更新用户的基本信息
                 */

                $user_basic_info = \Rpt\Funcs::getUserBasicInfo($userId);
                // 第一次、第二次、第三次购买
                $fields = 'orderTime, amount,shelfId';
                $failed_order_status[] = \Prj\Consts\OrderStatus::flow;
                $where = ['userId'=>$userId, 'orderStatus!'=>$failed_order_status];
                $order_ahead = $db->getRecords($tbname, $fields, $where, 'sort orderTime', 3);
                if(isset($order_ahead[0])){
                    $user_basic_info['ymdFirstBuy'] = substr($order_ahead[0]['orderTime'], 0, 8);
                    $user_basic_info['amountFirstBuy'] = $order_ahead[0]['amount'];
                    $user_basic_info['shelfIdFirstBuy'] = $order_ahead[0]['shelfId'];
                }else {
                    $user_basic_info['ymdFirstBuy'] = 0;
                    $user_basic_info['amountFirstBuy'] = 0;
                    $user_basic_info['shelfIdFirstBuy'] = 0;
                }
                if(isset($order_ahead[1])) {
                    $user_basic_info['ymdSecBuy'] = substr($order_ahead[1]['orderTime'], 0, 8);
                    $user_basic_info['amountSecBuy'] = $order_ahead[1]['amount'];
                    $user_basic_info['shelfIdSecBuy'] = $order_ahead[1]['shelfId'];
                }else {
                    $user_basic_info['ymdSecBuy'] = 0;
                    $user_basic_info['amountSecBuy'] = 0;
                    $user_basic_info['shelfIdSecBuy'] = 0;
                }
                if(isset($order_ahead[2])) {
                    $user_basic_info['ymdThirdBuy'] = substr($order_ahead[2]['orderTime'], 0, 8);
                    $user_basic_info['shelfIdThirdBuy'] = $order_ahead[2]['shelfId'];
                    $user_basic_info['amountThirdBuy'] = $order_ahead[2]['amount'];
                }else {
                    $user_basic_info['ymdThirdBuy'] = 0;
                    $user_basic_info['shelfIdThirdBuy'] = 0;
                    $user_basic_info['amountThirdBuy'] = 0;
                }

                // 最后一次购买
                $last = $db->getRecord($tbname, $fields, $where, 'rsort orderTime');
                if(!empty($last)) {
                    $user_basic_info['ymdLastBuy'] = substr($last['orderTime'], 0, 8);
                    $user_basic_info['amountLastBuy'] = $last['amount'];
                    $user_basic_info['shelfIdLastBuy'] = $last['shelfId'];
                }else {
                    $user_basic_info['ymdLastBuy']=$user_basic_info['amountLastBuy']=$user_basic_info['shelfIdLastBuy']=0;
                }
                // 最大一次购买
                $max = $db->getRecord($tbname, $fields, $where, 'rsort amount rsort orderTime');
                if(!empty($max)) {
                    $user_basic_info['ymdMaxBuy'] = substr($max['orderTime'], 0, 8);
                    $user_basic_info['amountMaxBuy'] = $max['amount'];
                    $user_basic_info['shelfIdMaxBuy'] = $max['shelfId'];
                }else {
                    $user_basic_info['ymdMaxBuy'] =$user_basic_info['amountMaxBuy'] =$user_basic_info['shelfIdMaxBuy'] =0;
                }

                // 用户总投资金额
                $total = $db->getOne($tbname, 'sum(amount)', $where);
                $user_basic_info['investTotalAmount'] = $total;

                unset($user_basic_info['userId']);
                $upd_keys = array_keys($user_basic_info);
                $user_basic_info['userId'] = $userId;
                $this->db_rpt->ensureRecord(\Rpt\Tbname::tb_user_final, $user_basic_info, $upd_keys);
            }

            $arr_user = null;
        }

        /**
         * 更新没有还款结束的订单状态
         */
        $where = ['orderStatus[' => \Prj\Consts\OrderStatus::advanced, 'orderStatus!'=>\Prj\Consts\OrderStatus::flow];
        if(!empty($arr_order_all)){
        $where['ordersId!'] = $arr_order_all;
    }
        $arr_order_all = null;
        $arr_orders = $this->db_rpt->getCol(\Rpt\Tbname::tb_orders_final, 'ordersId', $where);
//error_log('更新rpt订单sql:'.\Sooh\DB\Broker::lastCmd());
        if (!empty($arr_orders)) {
            $fields = ['ordersId,interestSub', 'interest', 'interestAdd', 'interestFloat', 'interestExt', 'returnAmount',
                'returnInterest', 'transTime', 'orderStatus', 'lastReturnFundYmd', 'returnNext'];
            $arr_orders = array_chunk($arr_orders, 200);
            foreach ($arr_orders as $group) {
                $records = \Prj\Data\Investment::loopFindRecordsByFields(['ordersId'=>$group], null, $fields, 'getRecords');
                foreach($records as $r) {
                    $tmp = [
                        'interestSub' => $r['interestSub'],
                        'interest' => $r['interest'],
                        'interestAdd' => $r['interestAdd'],
                        'interestFloat' => $r['interestFloat'],
                        'interestExt' => $r['interestExt'],
                        'returnAmount' => $r['returnAmount'],
                        'returnInterest' => $r['returnInterest'],
                        'orderStatus' => $r['orderStatus'],
                        'lastReturnFundYmd' => $r['lastReturnFundYmd'],
                        'returnNext' => $r['returnNext'],
                    ];

                    if ($r['transTime'] > 0) {
                        $tmp['ymdTrans'] = date('Ymd', strtotime($r['transTime']));
                        $tmp['hisTrans'] = date('His', strtotime($r['transTime']));
                    }
                    if(in_array($r['orderStatus'], $failed_order_status)) {
                        $this->db_rpt->delRecords(\Rpt\Tbname::tb_orders_final, ['ordersId'=>$r['ordersId']]);
                    }else {
                        $this->db_rpt->updRecords(\Rpt\Tbname::tb_orders_final, $tmp, ['ordersId' => $r['ordersId']]);
                        $this->ret->total++;
                        $this->ret->newupd++;
                    }


                }
            }
        }


        $this->lastMsg = $this->ret->toString();
        error_log('[ Trace ] ### ' . __CLASS__ . ' ### LastMsg:' . $this->lastMsg);
        return true;
    }

}
