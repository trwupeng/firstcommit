<?php

/**
 * 新用户阶段奖励 : 注意购买金额被强制改成1了
 *
 * @author wang.ning
 */
class UserInfo
{
    public $user;
    public $_view;
    public $_request;

    public static function run($view, $request, $response = null)
    {
        $tmp = new self();
        $tmp->_view = $view;
        $tmp->_request = $request;
        $preUserInfo = [];
        $userId = \Sooh\Base\Session\Data::getInstance()->get('accountId');
        $user = \Prj\Data\User::getCopy($userId);
        $user->load();
        $tmp->user = $user;
        if($user->exists()){
            $data = $tmp->_accountInfo($tmp->user);

            $unsentVoucherNum = 0;
            $map = [
                'userId' => $tmp->user->userId,
                'voucherType' => \Prj\Consts\Voucher::type_share,
                'statusCode]' => 0,
                'timeCreate[' => date('YmdHis', strtotime('+30 days')),//30天之内,
            ];
            $pager = new \Sooh\DB\Pager(1000);
            $pager->init(\Prj\Data\Vouchers::loopGetRecordsCount($map), 1);
            $ret = \Prj\Data\Vouchers::loopGetRecordsPage(array('timeCreate' => 'rsort', 'voucherId' => 'sort'), ['where' => $map], $pager);

            //$list = [];
            $keys = [];
            if (is_array($ret['records']) && !empty($ret['records'])) {
                foreach ($ret['records'] as $v) {
                    $pid      = $v['voucherId'];
                    $keys[]   = $pid;
                    $totalNum = \Prj\Data\VouchersInterim::loopGetRecordsCount(['pid' => $pid]);
                    $usedNum = \Prj\Data\VouchersInterim::loopGetRecordsCount(['pid' => $pid, 'isUsed' => 2]);
                    if (strtotime($v['dtExpired']) > \Sooh\Base\Time::getInstance()->timestamp() && $totalNum > $usedNum) {
                        $unsentVoucherNum++;
                    }
                }
            }

            $data['unsentVoucherNum'] = $unsentVoucherNum;  //待使用的红包个数


            $mineInviteTree = \Prj\Data\User::getMineInvitedTree($tmp->user->userId);
            if (empty($mineInviteTree['parent'])) { //是否设置过邀请码
                $data['hasBeenInvited'] = 0;
            } else {
                $data['hasBeenInvited'] = 1;
            }

            $data['canSetInviteCode'] = $tmp->_checkInviteStatus() ? 1 : 0;  //是否可以设置邀请码
            $preUserInfo = $data;
        }
        $view->assign('preUserInfo', $preUserInfo);

    }

    protected function _accountInfo($user)
    {
        $user->load();
        $userId = $user->getField('userId');
        $ret = [];
        $ret['redPacket'] = $this->user->getField('redPacket');
        $ret['wallet'] = $this->user->getField('wallet');
        $ret['interestTotal'] = $this->user->getField('interestTotal'); //累计收益
        $ret['nickname'] = $this->user->getField('nickname');
        $ret['ymdFirstBuy'] = $this->user->getField('ymdFirstBuy');
        $_checkinBook = $this->user->getField('checkinBook');
        if (isset($_checkinBook['ymd']) && !empty($_checkinBook['ymd'])) {
            $ret['isTodayCheckin'] = $_checkinBook['ymd'] == \Sooh\Base\Time::getInstance()->YmdFull ? 1 : 0;
        } else {
            $ret['isTodayCheckin'] = 0;
        }


        $redPacketDtLast = $this->user->getField('redPacketDtLast');
        $voucherSys = \Prj\Data\Vouchers::getCopy('');
        $lastRedPacket = $voucherSys->db()->getRecord($voucherSys->tbname(), 'timeCreate', ['userId' => $userId, 'voucherType' => \Prj\Consts\Voucher::type_real, 'statusCode]' => 0], 'rsort timeCreate');

        $ret['hasNewRedPacket'] = $lastRedPacket['timeCreate'] > $redPacketDtLast ? 1 : 0;//是否有未领取的红包


        $waresId = $this->_request->get('waresId');
        $cmd = $this->_request->get('cmd');
        if (!empty($cmd)) {
            if (strpos($cmd, 'voucher') !== false) {
                foreach ($this->_myVouchers($waresId, [\Prj\Consts\Voucher::type_fake, \Prj\Consts\Voucher::type_yield]) as $k => $v) {
                    $ret[$k] = !empty($v) ? $v : [];
                }
            }
            if (strpos($cmd, 'service') !== false) {
                $isPay = $this->_request->get('isPay', 0);
                $rpAmount = $this->_amountByReturnPlan($isPay);  //从还款计划里搜刮信息
                $ret = array_merge($ret, $rpAmount);
            }
        }
        //是否有待拆的红包
        $where = [
            'userId' => $userId,
            'statusCode' => \Prj\Consts\Voucher::status_wait,
            'voucherType' => \Prj\Consts\Voucher::type_real,
            'dtExpired]' => \Sooh\Base\Time::getInstance()->ymdhis(),
        ];
        $ret['CountRedPacketWait'] = \Prj\Data\Vouchers::loopGetRecordsCount($where);

        if (!empty($waresId)) {
            $ret['uniqueOp'] = \Lib\Misc\UniqueOp::createFor($waresId, 'orders/add');
        }

        $holdingAssets = \Prj\Data\Investment::getHoldingAssetsByUserId($userId);
        //$withdrawing = \Prj\Data\Recharges::getAmountWithdrawingByUserId($userId);
        $ret['freezeAmount'] = $this->freezeAmount();
        $ret['totalAssets'] =  $ret['wallet'] + $holdingAssets + $ret['freezeAmount']; //资产总额 = 钱包+回款中的订单+冻结资产  //$ret['redPacket']
        $ret['holdingAssets'] = $holdingAssets; //持有资产 = 回款中的订单

        //未读消息
        $ret['msgCounts'] = \Lib\Services\Message::getInstance()->getCount(['receiverId' => $userId, 'status' => \Prj\Consts\Message::status_unread]);

        return $ret;
    }

    protected function _checkInviteStatus()
    {
        $this->user->load();
        $inviteByUser = $this->user->getField('inviteByUser');
        if (!empty($inviteByUser)) {
            return false;
        }

        $ymdReg = $this->user->getField('ymdReg');

        $buyTimes = $this->_buyTime(null,$this->user->userId); //投资次数


        if ($ymdReg && strtotime('-15 days') <= strtotime($ymdReg) && $buyTimes<2) {
            return true;
        }
        return false;
    }

    /**
     * 冻结金额
     */
    protected function freezeAmount(){
        $userId = $this->user->userId;
        $tally = \Prj\Data\WalletTally::getCopy($userId);
        $db = $tally->db();
        $tbname = $tally->tbname();
        $rs = $db->getRecord($tbname,'sum(nAdd) as amount,sum(ext) as ext',['freeze'=>1,'statusCode'=>\Prj\Consts\Tally::status_new,'userId'=>$userId]);
        return abs($rs['amount']-0)+abs($rs['ext']);
    }

    protected function _buyTime($waresId = null, $userId)
    {
        $where = [
            'userId' => $userId,
            'orderStatus' => \Prj\Consts\OrderStatus::$running,
        ];
        if(!empty($waresId))$where['waresId'] = $waresId;
        return \Prj\Data\Investment::loopGetRecordsCount($where);
    }
}
