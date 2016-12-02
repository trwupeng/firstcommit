<?php
/**
 *
 *@author wu.peng 
 *
 */

define("APP_PATH",  dirname(__DIR__)); /* 指向public的上一级 */
include dirname(__DIR__).'/conf/globals.php';
include dirname(__DIR__).'/conf/autoload.php';
set_time_limit(0);
if (!function_exists('var_log')) {
    function var_log($var, $msg = '')
    {
        error_log($msg . ' ' . var_export($var, true));
    }
}
if (!function_exists('echo_log')) {
    function echo_log($var, $msg = '')
    {
        echo "<pre>" . $msg . ' ' . var_export($var, true) . "</pre>";
    }
}


$fileNameToSave = './'.date('Y-m-d').'checkin_user_with_voucher.txt';
if(file_exists($fileNameToSave)){
    unlink($fileNameToSave);
}
file_put_contents($fileNameToSave, '用户ID,用户性别,用户年龄,上次登录日期,累计签到天数,领取签到红包总金额,领取签到红包个数,使用签到红包总金额,使用签到红包个数'."\n", FILE_APPEND);



$pager = new \Sooh\DB\Pager((500), [10,500], false);
$where = [
    'checkinBook!'=>'',
];

$usercount = \Prj\Data\User::getCount($where);
$pager->init($usercount);
$pagecount = $pager->page_count;


$lastPage=['where'=>$where];

for ($i = 1; $i <= $pagecount; $i ++) {

    if ($i == 1) {
        $user = \Prj\Data\User::loopGetRecordsPage([
            'userId' => 'sort','ymdReg'=>'sort'
        ], $lastPage, $pager->init($usercount, $i));
    } else {
        $user = \Prj\Data\User::loopGetRecordsPage([
            'userId' => 'sort','ymdReg'=>'sort'
        ], $lastPage, $pager->init($usercount, $i));
    }
    $lastPage = $user['lastPage'];
    $user = $user['records'];
    //  var_log(\Sooh\DB\Broker::lastCmd(),'k1>>>>>>>');
    //var_log($user,'k1>>>>>>>');
    foreach ($user as $r){

        $sexy=(substr($r['idCard'], -2, 1) % 2) ? 'm' : 'f';

        if($sexy=='m'){
            $sexy='男';
        }elseif($sexy=='f'){
            $sexy='女';
        }else{
            $sexy='未实名';
        }
        $date=date('Y',time());
        if(empty($r['idCard'])){
            $age='';
        }
        else{
            $r['idCard']=substr($r['idCard'], 6,4);
            $age=$date-$r['idCard'];
        }
        $tmp=[

            'userId'=>$r['userId'],
            'sexy'=>$sexy,
            'age'=>$age,
            'dtLast'=>\Prj\Misc\View::fmtYmd($r['dtLast'],'time'),
        ];

        $where2=[
            'descCreate'=>'签到奖励',
            'userId'=>$r['userId'],
            'statusCode' => [\Prj\Consts\Voucher::status_unuse,\Prj\Consts\Voucher::status_used],

        ];

        // $voucher=\Prj\Data\Vouchers::loopFindRecordsByFields($where2,null,'count(*) as n,(sum(amount)/100) as a','getRecords');
        $obj = \Prj\Data\Vouchers::getCopy($r['userId']);
        $db = $obj->db();
        $tb = $obj->tbname();
         
        $voucher=$db->getRecords($tb,'count(*) as n,(sum(amount)/100) as a',$where2);
        //  var_log(\Sooh\DB\Broker::lastCmd(),'k1>>>>>>>');
        // var_log($voucher,'new>>>>>>>>');
         
        foreach ($voucher as $k1=>$v1){
             
            if($v1['a']==Null){
                $v1['a']=0;
            }
            $tmp=[
                 
                'userId'=>$r['userId'],
                'sexy'=>$sexy,
                'age'=>$age,
                'dtLast'=>\Prj\Misc\View::fmtYmd($r['dtLast'],'time'),
                'countall'=>$v1['n'],
                'amountall'=>$v1['a']
            ];
        }
         
        $where3=[
            'descCreate'=>'签到奖励',
            'userId'=>$r['userId'],
            'statusCode' => [\Prj\Consts\Voucher::status_used],
        ];
         
        $voucher1=$db->getRecords($tb,'count(*) as n,(sum(amount)/100) as a',$where3);
        //$voucher1=\Prj\Data\Vouchers::loopFindRecordsByFields($where3,null,'count(*) as n,(sum(amount)/100) as a','getRecords');
        // var_log($voucher1,'new>>>>>>>>');
         
        foreach ($voucher1 as $k2=>$v2){
             
            if($v2['a']==Null){
                $v2['a']=0;
            }
            $tmp=[
                 
                'userId'=>$r['userId'],
                'sexy'=>$sexy,
                'age'=>$age,
                'dtLast'=>\Prj\Misc\View::fmtYmd($r['dtLast'],'time'),
                'dayall'=>$v1['n'],
                'countall'=>$v1['n'],
                'amountall'=>sprintf("%.2f",$v1['a']),
                'usedcount'=>$v2['n'],
                'usedamount'=>sprintf("%.2f",$v2['a']),
            ];
            $new[]=$tmp;
             
        }
    }

}

//var_log($new,'new>>>>>>>>>>>>>>');

foreach ($new as $u){
    
    $str=$u['userId'].','.$u['sexy'].','.$u['age'].','.$u['dtLast'].','.$u['dayall']
         .','.$u['amountall'].','.$u['countall'].','.$u['usedamount'].','.$u['usedcount']."\n";
    file_put_contents($fileNameToSave, $str, FILE_APPEND);
}



 
 






