<?php
namespace Rpt\DataDig;
/**
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/5/26 0026
 * Time: 下午 3:52
 */

class ContractcompareDataDig {

    public function dig($where) {
//    var_log($where, 'where>>>>>>');
        $db = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_p2p_slave);

        // 日期范围内有数据的渠道
         $contracts= $db->getCol(\Rpt\Tbname::tb_user_final, 'distinct(contractId)', $where);
        if(empty($contracts)) {
            return [];
        }

        $contractIdWithName = [];
        $contractsName = \Prj\Data\Contract::loopFindRecordsByFields(['contractId'=>$contracts], null, 'contractId, remarks', 'getRecords');
        if(!empty($contractsName)){
            foreach($contractsName as $k => $r) {
                $contractIdWithName[$r['contractId']] = $r['remarks'];
            }
        }
        $contractsName = null;


        // 注册人数
        $reg = $db->getPair(\Rpt\Tbname::tb_user_final, 'contractId', 'count(*) as n', $where, 'groupby contractId');

        // 绑卡认证人数
        $bind = $db->getPair(\Rpt\Tbname::tb_user_final, 'contractId', 'count(*) as n', array_merge($where,['ymdBindcard>'=>0]), 'groupby contractId');

        // 注册到绑卡转化率
        $transOfReg2Bind = [];
        foreach($reg as $contractId => $num) {
            if(isset($bind[$contractId])) {
                $transOfReg2Bind[$contractId] = sprintf('%0.2f', ($bind[$contractId] / $num)*100).'%';
            }else {
                $transOfReg2Bind[$contractId] = '';
            }
        }

        // 充值人数
        $recharge= $db->getPair(\Rpt\Tbname::tb_user_final, 'contractId', 'count(*) as n', array_merge($where,['ymdFirstRecharge>'=>0]), 'groupby contractId');

        // 充值金额
        $recharegeAmount = $db->getPair(\Rpt\Tbname::tb_user_final, 'contractId', 'sum(rechargeTotalAmount)/100 as totalRecharge', array_merge($where,['ymdFirstRecharge>'=>0]), 'groupby contractId');

        // 绑卡到充值转化率
        $transOfBind2Recharge = [];
        foreach($bind as $contractId => $num) {
            if(isset($recharge[$contractId])) {
                $transOfBind2Recharge[$contractId] = sprintf('%0.2f', ($recharge[$contractId] / $num)*100)."%";
            }else {
                $transOfBind2Recharge[$contractId] = '';
            }
        }

        // 投资人数
        $buyNum = $db->getPair(\Rpt\Tbname::tb_user_final, 'contractId', 'count(*) as n', array_merge($where, ['ymdFirstBuy>'=>0]), 'groupby contractId');
    //var_log($transOfReg2Bind, 'transOfReg2Bind>>>>>>>');


        // 投资金额
        $buyAmount = $db->getPair(\Rpt\Tbname::tb_user_final, 'contractId', 'sum(investTotalAmount)/100 as totalRecharge', array_merge($where, ['ymdFirstBuy>'=>0]), 'groupby contractId');

        // 充值到投资转化率
        $transOfRecharge2Buy= [];
        foreach($recharge as $contractId => $num) {
            if(isset($buyNum[$contractId])) {
                $transOfRecharge2Buy[$contractId] = sprintf('%0.2f', ($buyNum[$contractId] / $num)*100)."%";
            }else {
                $transOfRecharge2Buy[$contractId] = '';
            }
        }


        // 复投人数
        $buyAgainNum = $db->getPair(\Rpt\Tbname::tb_user_final, 'contractId', 'count(*) as n', array_merge($where, ['ymdSecBuy>'=>0]), 'groupby contractId');

        // 复投金额
        $buyAgainAmount = $db->getPair(\Rpt\Tbname::tb_user_final, 'contractId', 'sum(amountSecBuy)/100 as n', array_merge($where, ['ymdSecBuy>'=>0]), 'groupby contractId');

        // 复投比例
        $transOfBuyAgain = [];
        foreach($buyNum as $contractId => $num) {
            if(isset($buyAgainNum[$contractId])){
                $transOfBuyAgain[$contractId] = sprintf('%0.2f', ($buyAgainNum[$contractId] / $num)*100)."%";
            }else {
                $transOfBuyAgain[$contractId] = '';
            }
        }

        // 三投人数
        $thirdBuyNum = $db->getPair(\Rpt\Tbname::tb_user_final, 'contractId', 'count(*) as n', array_merge($where, ['ymdThirdBuy>'=>0]), 'groupby contractId');

        // 三投金额
        $thirdBuyAmount = $db->getPair(\Rpt\Tbname::tb_user_final, 'contractId', 'sum(amountThirdBuy)/100 as n', array_merge($where, ['ymdThirdBuy>'=>0]), 'groupby contractId');

        // 三投比例
        $transOfThirdAgain = [];
        foreach($buyAgainNum  as $contractId => $num) {
            if(isset($thirdBuyNum[$contractId])){
                $transOfThirdAgain[$contractId] = sprintf('%0.2f', ($thirdBuyNum[$contractId]/$num)*100)."%";
            }else{
                $transOfThirdAgain[$contractId] = '';
            }
        }

    //var_log($copartners, 'copartners>>>>>>>>');
        $header = $this->header;
        $data = $this->data;
        foreach($contracts as $contractId) {

            // 标题
            if(!isset($contractIdWithName[$contractId])) {
                $header[$contractId] = '未找到渠道名称['.$contractId.']';
            }else {
                $header[$contractId] = $contractIdWithName[$contractId].'['.$contractId.']';
            }

            // 注册数目
            $data['regNum'][$contractId] = $reg[$contractId];

            // 绑卡数目
            $data['bindNum'][$contractId] = isset($bind[$contractId]) ? $bind[$contractId] : '';

            // 注册到绑卡转化率
            $data['transOfReg2Bind'][$contractId] = $transOfReg2Bind[$contractId];

            // 充值人数
            $data['rechargeNum'][$contractId] = isset($recharge[$contractId]) ? $recharge[$contractId] : '';

            // 充值金额
            $data['rechargeAmount'][$contractId] = isset($recharegeAmount[$contractId]) ? number_format($recharegeAmount[$contractId], 2) : '';

            // 绑卡到充值转化率
            $data['transOfBind2Recharge'][$contractId] = isset($transOfBind2Recharge[$contractId]) ? $transOfBind2Recharge[$contractId] : '';

            // 投资人数
            $data['buyNum'][$contractId] = isset($buyNum[$contractId]) ? $buyNum[$contractId] :  '';

            // 投资金额
            $data['buyAmount'][$contractId] = isset($buyAmount[$contractId]) ? number_format($buyAmount[$contractId], 2) : '';

            // $contractId
            $data['transOfRecharge2Buy'][$contractId] = isset($transOfRecharge2Buy[$contractId]) ? $transOfRecharge2Buy[$contractId] : '';

            // 复投人数
            $data['buyAgainNum'][$contractId] = isset($buyAgainNum[$contractId]) ? $buyAgainNum[$contractId] : '';

            // 复投金额
            $data['buyAgainAmount'][$contractId] = isset($buyAgainAmount[$contractId]) ? number_format($buyAgainAmount[$contractId], 2) : '';

            // 复投比例
            $data['transOfBuyAgain'][$contractId] = isset($transOfBuyAgain[$contractId]) ? $transOfBuyAgain[$contractId] : '';

            // 三投人数
            $data['thirdBuyNum'][$contractId] = isset($thirdBuyNum[$contractId]) ? $thirdBuyNum[$contractId] : '';

            // 三投金额
            $data['thirdBuyAmount'][$contractId] = isset($thirdBuyAmount[$contractId]) ? number_format($thirdBuyAmount[$contractId], 2) : '';

            // 三投比例
            $data['transOfThirdBuy'][$contractId] = isset($transOfThirdAgain[$contractId]) ? $transOfThirdAgain[$contractId] : '';
        }
//    var_log($header, 'header>>>>>>');
//    var_log($data, 'data>>>>>>');
        return ['header'=>$header, 'data'=>$data];
    }

    protected $header = ['协议名称'];

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