<?php
$GLOBALS['CONF']['path_php'] = 'php';
$GLOBALS['CONF']['path_console'] = APP_PATH.'/run/crond.php';
//$GLOBALS['CONF']['released'] = false;
$GLOBALS['CONF']['ServerId']=3;
$GLOBALS['CONF']['version'] = '1.0';
$GLOBALS['CONF']['deploymentCode'] = '10';//10:dev | 20:test | 30:pre | 40:online
$GLOBALS['CONF']['CookieDomainBase']='.x-licai.com';
//$GLOBALS['CONF']['SignKeyForService'] = 'asgdfw4872hfhjksdhr8732trsj';
//$GLOBALS['CONF']['hostsOfMssqlAPI'] = array(
//	'default'=>array('http://192.168.56.130/API001/index.php?__=service/call',)
//);
$GLOBALS['CONF']['RpcConfig'] = array(
	'force'=>0,
	'key'=>'asgdfw4872hfhjksdhr8732trsj','protocol'=>'HttpGet',
	'urls'=>array('http://wwwtest.x-licai.com/index.php?__=service/call',)
);
//By Hand
$GLOBALS['CONF']['payGW'] = array(
   //'http://192.168.1.177:8888/SpringMVC_Spring_mybatis/service/service',
   'http://139.129.29.52:8080/backgrount_payment/service/service',
);

$GLOBALS['CONF']['payCK'] = array(
    'http://192.168.1.112:8080/backgrount_payment/docheck/docheck',
);

$GLOBALS['CONF']['noGW'] = 0;

$GLOBALS['CONF']['logerror'] = array(
	1=>['/var/www/logs/prj_error.txt'],
	2=>['/var/www/logs/prj_trace.txt']
);
$GLOBALS['CONF']['maintainTime'] = array(mktime(23,59,59,1970,12,30)-60,mktime(23,59,59,1971,12,30),);

$GLOBALS['CONF']['dbConf'] = array(//rpt那台机器，用192.168.1.144（从库）
'default' => array('host' => '192.168.1.143', 'user' => 'p2p', 'pass' => 'Pp123456', 'type' => 'mysql', 'port' => '3306',
    'dbEnums' => array('default' => 'db_p2p', 'devices' => 'db_devices', 'dbgrpForLog' => 'db_logs')),//根据模块选择的具体的数据库名
'manage' => array('host' => '192.168.1.143', 'user' => 'p2p', 'pass' => 'Pp123456', 'type' => 'mysql', 'port' => '3306',
    'dbEnums' => array('default' => 'db_p2p',)),
'oauth' => array('host' => '192.168.1.143', 'user' => 'p2p', 'pass' => 'Pp123456', 'type' => 'mysql', 'port' => '3306',
    'dbEnums' => array('default' => 'db_oauth',)),

'dbForRpt'=>array('host'=>'localhost','user'=>'p2p','pass'=>'123456','type'=>'mysql','port'=>'3306',
    'dbEnums'=>array('default'=>'db_p2prpt',)),
// 主从库分离时要用， crond定时任务的日志要写到主库中，
'crondForLog' =>array('host'=>'192.168.1.143','user'=>'p2p','pass'=>'Pp123456','type'=>'mysql','
port'=>'3306','dbEnums'=>array('default'=>'db_logs',)),

// 指向从库		
'slaveCatches' => array('host' => '10.164.23.167', 'user' => 'mytestuser', 'pass' => 'Xx111111', 'type' => 'mysql', 'port' => '3306',
        'dbEnums' => array('default' => 'db_p2p', 'devices' => 'db_devices', 'dbgrpForLog' => 'db_logs'))
);

$GLOBALS['CONF']['dbConfMirror'] = array(//镜像库
'default' => array('host' => '10.164.23.167', 'user' => 'mytestuser', 'pass' => 'Xx111111', 'type' => 'mysql', 'port' => '3306',
    'dbEnums' => array('default' => 'db_p2p', 'devices' => 'db_devices', 'dbgrpForLog' => 'db_logs')),//根据模块选>择的具体的数据库名
);
$GLOBALS['CONF']['dbByObj']=array(
	'default'=>array(1,'default'),
	'dbgrpForLog'=>array(2,'default',),
	'session'=>array(2,'default'),
	//'account'=>array(1,'default'),
	//'monitor'=>array(1,'default'),
	'smscode'=>array(2,'default'),
	'devices'=>array(2,'default'),
	'manage'=>array(1,'manage'),
	'modCheckin'=>array(2,'default'),
	'investment'=>array(2,'default'),
	'messages'>array(2,'default'),
	'pointsTally'=>array(2,'default'),
	'recharges'=>array(2,'default'),
	'redpacket'=>array(2,'default'),
	'user'=>array(2,'default'),
	'userbank'=>array(2,'default'),
	'vouchers'=>array(2,'default'),
	'wallyTally'=>array(2,'default'),
	'returnlog'=>array(2,'TODO'),
	'oauth'=>array(2,'oauth'),
    'reportManagers'=>array(1, 'dbForRpt'),
);

$GLOBALS['CONF']['uriBase']=array(
	'www'=>'http://wwwtest.x-licai.com',
	'oauth'=>'http://authtest.x-licai.com',
	'rpt'=>'http://rpttest.x-licai.com',
);

$GLOBALS['CONF']['SMSConf'] = 'ChuangLan';
$GLOBALS['CONF']['PushConf'] = 'JPush';

$GLOBALS['CONF']['TestKey'] = 'O2eWC5ExmwL47Ku8MRBcq35kvhAGRVDiZQvak8z';

$GLOBALS['CONF']['debug'] = 1;

$GLOBALS['CONF']['localLibs']=array('Lib','Prj');
// // 配置cms域名  域名根据开发还是线上环境进行配置
$GLOBALS['CONF']['cmsurl']='wwwtest.xiaoxialicai.com';

function var_log($var,$prefix=''){
	if(is_a($var, "\Exception")){
		$s = $var->__toString();
		if(strpos($s,'[Sooh_Base_Error]')){
			if(class_exists('\Sooh\DB\Broker',false)){
				$sql = "\n".\Sooh\DB\Broker::lastCmd()."\n";
			}else{
				$sql = "\n";
			}
			error_log(str_replace('[Sooh_Base_Error]',$sql,$s));
		}else{
			error_log($prefix.$var->getMessage()."\n".$s);
		}
	}else{
		error_log($prefix."\n".var_export($var,true));
	}
}

include "/var/www/vendor/autoload.php";