<?php
//开发环境用的，从几个使用测试环境的地方，收集支付网关的流水记录
include 'E:\works\background_vendor\autoload.php';
$GLOBALS=[];
$GLOBALS['CONF']['dbConf'] = array(//rpt那台机器，用192.168.1.144（从库）
'write' => array('host' => '115.28.138.127', 'user' => 'mydpuser', 'pass' => 'Mm123456', 'type' => 'mysql', 'port' => '3306',
    'dbEnums' => array('default' => 'db_p2p',)),//根据模块选择的具体的数据库名
	//测试库
'test' => array('host' => '115.28.172.53', 'user' => 'mytestuser', 'pass' => 'Xx111111', 'type' => 'mysql', 'port' => '3306',
    'dbEnums' => array('default' => 'db_p2p',)),
	//开发库
'dev' => array('host' => '115.28.138.127', 'user' => 'mydpuser', 'pass' => 'Mm123456', 'type' => 'mysql', 'port' => '3306',
    'dbEnums' => array('default' => 'db_p2p',)),
);
$dbs = array_keys($GLOBALS['CONF']['dbConf']);
unset($dbs[0]);
$db2 = \Sooh\DB\Broker::getInstance('write');
foreach($dbs as $baseid=>$reader){
	$baseid = $baseid*10000*10000;
	$maxid = ($baseid+1)*10000*10000;
	$db = \Sooh\DB\Broker::getInstance($reader);
	
	$lastid = $db2->getOne('test.db_record', 'max(id)',['id>'=>$baseid,'id<'=>$maxid]);
	if($lastid){
		$lastid = $lastid - $baseid;
		$where = ['id>'=>$lastid];
	}else{
		$where = null;
	}
	$records = $db->getRecords('db_p2ppay.db_record', '*',$where);
	foreach($records as $r){
		$r['id']+=$baseid;
		$db2->addRecord('test.db_record', $r);
	}
}
echo "total now:".$db2->getRecordCount('test.db_record');