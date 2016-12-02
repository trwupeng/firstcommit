<?php
namespace Rpt\DataDig;
/**
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/5/25 0025
 * Time: 上午 9:43
 */

class CopartnertransfunnelchartDataDig {


    /**
     *
     * @param $where 起始和结束日期、渠道或渠道号码
     */
    public static function createFunnelData($where) {

        $db_rpt = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_p2p_slave);

        // 本周日期内注册的人数
        $regnum = $db_rpt->getRecordCount(\Rpt\Tbname::tb_user_final, $where);

        // 本周日期内注册的人认证绑卡人数
        $bindcardnum = $db_rpt->getRecordCount(\Rpt\Tbname::tb_user_final, array_merge($where, ['ymdBindcard>'=>0]));

        // 本周日期内注册的人充值人数
        $rechargenum = $db_rpt->getRecordCount(\Rpt\Tbname::tb_user_final, array_merge($where, ['ymdFirstRecharge>'=>0]));

        // 本周日期内注册的人投资人数
        $buynum = $db_rpt->getRecordCount(\Rpt\Tbname::tb_user_final, array_merge($where, ['ymdFirstBuy>'=>0]));

        // 本周日期内注册的人再次投资人数
        $buyagain = $db_rpt->getRecordCount(\Rpt\Tbname::tb_user_final, array_merge($where, ['ymdSecBuy>'=>0]));

        // 本周日期内注册的人三投的人数
        $thirdBuy = $db_rpt->getRecordCount(\Rpt\Tbname::tb_user_final, array_merge($where, ['ymdThirdBuy>'=>0]));
        
        $baseNum = ($regnum > 0 ? $regnum : 1);
        $data_got = [
            '注册' => $regnum,
            '认证' => $bindcardnum,
            '充值' => $rechargenum,
            '投资' => $buynum,
            '复投' => $buyagain,
            '三投' => $thirdBuy
        ];

        $data = [];
        $serias_data = [];
//        $i = 0;
//        $pre = 0;
//        foreach($data_got as $name => $value) {
//            if($i ==0 ) {
//                $pre = $value;
//            }
//            $pre = ($pre > 0 ? $pre : 1);
//            $name = $name.'('.$value.'人)';
//            $data[] = $name;
//            $rate = sprintf('%0.0f', ($value/$pre)*100);
//            $serias_data[] = ['value'=>$rate, 'name'=>$name];
//            $pre = $value;
//            $i++;
//        }

        foreach($data_got as $name => $value) {
            $name = $name.'('.$value.'人)';
            $data[] = $name;
//            $rate = sprintf('%0.0f', ($value/$baseNum)*100);
            $serias_data[] = ['value'=>$value, 'name'=>$name];
        }
        return ['data' => $data, 'serias_data' => $serias_data];
    }
}
