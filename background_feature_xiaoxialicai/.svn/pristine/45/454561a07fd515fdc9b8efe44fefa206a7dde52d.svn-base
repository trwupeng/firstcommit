<?php
namespace Rpt\DataDig;
/**
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/5/26 0026
 * Time: 下午 3:52
 */

class CopartnercompareDataDig {

    public function dig($where) {
//    var_log($where, 'where>>>>>>');
        $db = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_p2p_slave);

        // 日期范围内有数据的渠道
        $copartners = $db->getCol(\Rpt\Tbname::tb_user_final, 'distinct(copartnerId)', $where);
        if(empty($copartners)) {
            return [];
        }

        $copartnerIdWithName = [];
        $copartnerNames = \Prj\Data\Copartner::loopFindRecordsByFields(['copartnerId'=>$copartners], null, 'copartnerId, copartnerName', 'getRecords');
        if(!empty($copartnerNames)){
            foreach($copartnerNames as $k => $r) {
                $copartnerIdWithName[$r['copartnerId']] = $r['copartnerName'];
            }
        }
        $copartnerNames = null;
    // var_log($copartnerIdWithName, '有注册的渠道>>>');


        // 注册人数
        $reg = $db->getPair(\Rpt\Tbname::tb_user_final, 'copartnerId', 'count(*) as n', $where, 'groupby copartnerId');

        // 绑卡认证人数
        $bind = $db->getPair(\Rpt\Tbname::tb_user_final, 'copartnerId', 'count(*) as n', array_merge($where,['ymdBindcard>'=>0]), 'groupby copartnerId');

        // 注册到绑卡转化率
        $transOfReg2Bind = [];
        foreach($reg as $copartnerId => $num) {
            if(isset($bind[$copartnerId])) {
                $transOfReg2Bind[$copartnerId] = sprintf('%0.2f', ($bind[$copartnerId] / $num)*100).'%';
            }else {
                $transOfReg2Bind[$copartnerId] = '';
            }
        }

        // 充值人数
        $recharge= $db->getPair(\Rpt\Tbname::tb_user_final, 'copartnerId', 'count(*) as n', array_merge($where,['ymdFirstRecharge>'=>0]), 'groupby copartnerId');

        // 充值金额
        $recharegeAmount = $db->getPair(\Rpt\Tbname::tb_user_final, 'copartnerId', 'sum(rechargeTotalAmount)/100 as totalRecharge', array_merge($where,['ymdFirstRecharge>'=>0]), 'groupby copartnerId');

        // 绑卡到充值转化率
        $transOfBind2Recharge = [];
        foreach($bind as $copartnerId => $num) {
            if(isset($recharge[$copartnerId])) {
                $transOfBind2Recharge[$copartnerId] = sprintf('%0.2f', ($recharge[$copartnerId] / $num)*100)."%";
            }else {
                $transOfBind2Recharge[$copartnerId] = '';
            }
        }

        // 投资人数
        $buyNum = $db->getPair(\Rpt\Tbname::tb_user_final, 'copartnerId', 'count(*) as n', array_merge($where, ['ymdFirstBuy>'=>0]), 'groupby copartnerId');
    //var_log($transOfReg2Bind, 'transOfReg2Bind>>>>>>>');


        // 投资金额
        $buyAmount = $db->getPair(\Rpt\Tbname::tb_user_final, 'copartnerId', 'sum(investTotalAmount)/100 as totalRecharge', array_merge($where, ['ymdFirstBuy>'=>0]), 'groupby copartnerId');

        // 充值到投资转化率
        $transOfRecharge2Buy= [];
        foreach($recharge as $copartnerId => $num) {
            if(isset($buyNum[$copartnerId])) {
                $transOfRecharge2Buy[$copartnerId] = sprintf('%0.2f', ($buyNum[$copartnerId] / $num)*100)."%";
            }else {
                $transOfRecharge2Buy[$copartnerId] = '';
            }
        }


        // 复投人数
        $buyAgainNum = $db->getPair(\Rpt\Tbname::tb_user_final, 'copartnerId', 'count(*) as n', array_merge($where, ['ymdSecBuy>'=>0]), 'groupby copartnerId');

        // 复投金额
        $buyAgainAmount = $db->getPair(\Rpt\Tbname::tb_user_final, 'copartnerId', 'sum(amountSecBuy)/100 as n', array_merge($where, ['ymdSecBuy>'=>0]), 'groupby copartnerId');

        // 复投比例
        $transOfBuyAgain = [];
        foreach($buyNum as $copartnerId => $num) {
            if(isset($buyAgainNum[$copartnerId])){
                $transOfBuyAgain[$copartnerId] = sprintf('%0.2f', ($buyAgainNum[$copartnerId] / $num)*100)."%";
            }else {
                $transOfBuyAgain[$copartnerId] = '';
            }
        }

        // 三投人数
        $thirdBuyNum = $db->getPair(\Rpt\Tbname::tb_user_final, 'copartnerId', 'count(*) as n', array_merge($where, ['ymdThirdBuy>'=>0]), 'groupby copartnerId');


        // 三投金额
        $thirdBuyAmount = $db->getPair(\Rpt\Tbname::tb_user_final, 'copartnerId', 'sum(amountThirdBuy)/100 as n', array_merge($where, ['ymdThirdBuy>'=>0]), 'groupby copartnerId');

        // 三投比例
        $transOfThirdBuy = [];
        foreach($buyAgainNum as $copartnerId => $num) {
            if(isset($thirdBuyNum[$copartnerId])) {
                $transOfThirdBuy[$copartnerId] = sprintf('%0.2f', ($thirdBuyNum[$copartnerId]/$num)*100)."%";
            }else {
                $transOfThirdBuy[$copartnerId] = '';
            }
        }


    //var_log($copartners, 'copartners>>>>>>>>');
        $header = $this->header;
        $data = $this->data;
        foreach($copartners as $copartnerId) {

            // 标题
            if(!isset($copartnerIdWithName[$copartnerId])) {
                $header[$copartnerId] = '未找到渠道名称['.$copartnerId.']';
            }else {
                $header[$copartnerId] = $copartnerIdWithName[$copartnerId];
            }

            // 注册数目
            $data['regNum'][$copartnerId] = $reg[$copartnerId];

            // 绑卡数目
            $data['bindNum'][$copartnerId] = isset($bind[$copartnerId]) ? $bind[$copartnerId] : '';

            // 注册到绑卡转化率
            $data['transOfReg2Bind'][$copartnerId] = $transOfReg2Bind[$copartnerId];

            // 充值人数
            $data['rechargeNum'][$copartnerId] = isset($recharge[$copartnerId]) ? $recharge[$copartnerId] : '';

            // 充值金额
            $data['rechargeAmount'][$copartnerId] = isset($recharegeAmount[$copartnerId]) ? number_format($recharegeAmount[$copartnerId], 2) : '';

            // 绑卡到充值转化率
            $data['transOfBind2Recharge'][$copartnerId] = isset($transOfBind2Recharge[$copartnerId]) ? $transOfBind2Recharge[$copartnerId] : '';

            // 投资人数
            $data['buyNum'][$copartnerId] = isset($buyNum[$copartnerId]) ? $buyNum[$copartnerId] :  '';

            // 投资金额
            $data['buyAmount'][$copartnerId] = isset($buyAmount[$copartnerId]) ? number_format($buyAmount[$copartnerId], 2) : '';

            // 充值到投资转化率
            $data['transOfRecharge2Buy'][$copartnerId] = isset($transOfRecharge2Buy[$copartnerId]) ? $transOfRecharge2Buy[$copartnerId] : '';

            // 复投人数
            $data['buyAgainNum'][$copartnerId] = isset($buyAgainNum[$copartnerId]) ? $buyAgainNum[$copartnerId] : '';

            // 复投金额
            $data['buyAgainAmount'][$copartnerId] = isset($buyAgainAmount[$copartnerId]) ? number_format($buyAgainAmount[$copartnerId], 2) : '';

            // 复投比例
            $data['transOfBuyAgain'][$copartnerId] = isset($transOfBuyAgain[$copartnerId]) ? $transOfBuyAgain[$copartnerId] : '';

            // 三投人数
            $data['thirdBuyNum'][$copartnerId] = isset($thirdBuyNum[$copartnerId]) ? $thirdBuyNum[$copartnerId] : '';

            // 三投金额
            $data['thirdBuyAmount'][$copartnerId] = isset($thirdBuyAmount[$copartnerId]) ?number_format($thirdBuyAmount[$copartnerId], 2) : '';

            // 三投比例
            $data['transOfThirdBuy'][$copartnerId] = isset($transOfThirdBuy) ? $transOfThirdBuy[$copartnerId] : '';

        }
//    var_log($header, 'header>>>>>>');
//    var_log($data, 'data>>>>>>');
        return ['header'=>$header, 'data'=>$data];
    }

    protected $header = ['渠道名称'=>'渠道名称'];

    protected $data = [
        'regNum'                    => ['注册人数'=>'注册人数'],
        'bindNum'                   => ['认证绑卡人数'=>'认证绑卡人数'],
        'transOfReg2Bind'           => ['注册到认证转化率'=>'注册到认证转化率'],
        'rechargeNum'               => ['充值人数'=>'充值人数'],
        'rechargeAmount'            => ['充值金额'=>'充值金额'],
        'transOfBind2Recharge'      => ['认证绑卡到充值转化率'=>'认证绑卡到充值转化率'],
        'buyNum'                    => ['投资人数'=>'投资人数'],
        'buyAmount'                 => ['投资金额'=>'投资金额'],
        'transOfRecharge2Buy'       => ['充值到投资转化率'=>'充值到投资转化率'],
        'buyAgainNum'               => ['复投人数'=>'复投人数'],
        'buyAgainAmount'            => ['复投金额'=>'复投金额'],
        'transOfBuyAgain'           => ['再次投资比例'=>'再次投资比例'],
        'thirdBuyNum'               => ['三投人数'=>'三投人数'],
        'thirdBuyAmount'            => ['三投金额'=>'三投金额'],
        'transOfThirdBuy'           => ['三投比例'=>'三投比例'],
    ];

}