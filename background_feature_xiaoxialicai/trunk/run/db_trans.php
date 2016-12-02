<?php
//测试环境用的，从开发环境更新协议和配置到测试服
include '/var/www/vendor/autoload.php';
$GLOBALS=[];
$GLOBALS['CONF']['dbConf'] = array(//rpt那台机器，用192.168.1.144（从库）
'test' => array('host' => '115.28.172.53', 'user' => 'mytestuser', 'pass' => 'Xx111111', 'type' => 'mysql', 'port' => '3306',
    'dbEnums' => array('default' => 'db_p2p',)),
	//开发库
'dev' => array('host' => '115.28.138.127', 'user' => 'mydpuser', 'pass' => 'Mm123456', 'type' => 'mysql', 'port' => '3306',
    'dbEnums' => array('default' => 'db_p2p',)),
);
$dbTest = \Sooh\DB\Broker::getInstance('test');
$dbDev = \Sooh\DB\Broker::getInstance('dev');
$rs  = $dbDev->getRecords('db_p2p.tb_agreement_0', '*');
$r = $rs[0];
unset($r['verName']);
unset($r['verId']);
$ks = array_keys($r);
foreach($rs as $r){
	$dbTest->ensureRecord('db_p2p.tb_agreement_0',$r,$ks);
}
$ignore = ['dbsql.ver','SHARE_VOUCHER_PIC','SHARE_VOUCHER_URL','WECHAT_ACCESSTOKEN','CUSTOMER_INVITE_PICURL','CUSTOMER_INVITE_URL'];

$rs = $dbDev->getRecords('db_p2p.tb_config', '*',['k!'=>$ignore]);
foreach($rs as $r){
	$dbTest->ensureRecord('db_p2p.tb_config',$r,['v','intro']);
}
