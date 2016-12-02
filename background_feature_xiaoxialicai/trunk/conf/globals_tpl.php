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

$GLOBALS['CONF']['http'] = 'https'; //http请求/https请求

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
        'dbEnums' => array('default' => 'db_p2p', 'devices' => 'db_devices', 'dbgrpForLog' => 'db_logs')),
		
'redis' => array( 'type' => "redis", 
		'dbEnums' => array('default' => 'db_p2p', 'devices' => 'db_devices', 'dbgrpForLog' => 'db_logs'),
		'pass' => 'ihBnNmFWF07mx^csKusWVrZm*n91cEbX',
		'hosts' => array (
			array( 'host' => '115.28.53.193', 'port' => 3888, ),
			array( 'host' => '115.28.172.53', 'port' => 3888 ),
		),
	),
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
	'chargeback' => [2, 'default'],
	'tongdun' => [2, 'oauth'],
);

$GLOBALS['CONF']['uriBase']=array(
	'www'=>'http://wwwtest.x-licai.com',
	'oauth'=>'http://authtest.x-licai.com',
	'rpt'=>'http://rpttest.x-licai.com',
);

$GLOBALS['CONF']['SMSConf'] = 'ChuangLan';
$GLOBALS['CONF']['PushConf'] = 'JPush';

$GLOBALS['CONF']['TONGDUN'] = [
    'INTERCEPT' => false,//是否拦截注册，false不拦截，true拦截
];

$GLOBALS['CONF']['TongDun_Api_Url'] = 'https://api.tongdun.cn/account/register/v1';//正式环境配置
//$GLOBALS['CONF']['TongDun_Api_Url'] = 'https://apitest.tongdun.cn/account/register/v1';//测试环境配置

$GLOBALS['CONF']['TongDun_Ios_Secret_Key'] = 'acf09c2465b64ecda89fab17ca7ebbd4';
$GLOBALS['CONF']['TongDun_Android_Secret_Key'] = '08983d9a4cf941eda9dcce9cf41f5901';
$GLOBALS['CONF']['TongDun_Web_Secret_Key'] = '96b04ad8c3ea48fdb302da259484bb2e';

//$GLOBALS['CONF']['TongDun_Ios_Secret_Key'] = '73030583de7444598a026681b9947a05';//测试环境的配置
//$GLOBALS['CONF']['TongDun_Android_Secret_Key'] = '8142b6b85fbc4693afa8e1c40993f454';//测试环境的配置
//$GLOBALS['CONF']['TongDun_Web_Secret_Key'] = '48118ad00dab4f0ba98a5e8d12d133f2';//测试环境的配置

/**=======================JPush config========================**/
$GLOBALS['CONF']['JPUSH_ALIAS_RULE'] = 'licai';//极光推送生成别名时的后缀
//$GLOBALS['CONF']['JPUSH'] = [
//    'DEPLOY_ENVIRONMENT' => 'online',
//    'PRO_ON'             => true,//PRO推送开关
//    'KEY'                => 'e1ecdce3c8cdc621cad55079',//默认配置KEY
//    'SECRET'             => '220a11d5b9d5f3f59f92a15d',//默认配置SECRET
//    'KEY_PRO'            => 'e0cd9619fabb5686a4c77057',//IOS PRO KEY
//    'SECRET_PRO'         => '9b6c1098bde0a7fdb523bb61',//IOS PRO SECRET
//];
$GLOBALS['CONF']['JPUSH'] = [
    'DEPLOY_ENVIRONMENT' => 'dev',
    'PRO_ON'             => true,//PRO推送开关
    'KEY'                => '76e0e67dff54dd9b3d414803',//默认配置KEY
    'SECRET'             => '225b1f4241e299f08239391b',//默认配置SECRET
    'KEY_PRO'            => 'e0cd9619fabb5686a4c77057',//IOS PRO KEY
    'SECRET_PRO'         => '9b6c1098bde0a7fdb523bb61',//IOS PRO SECRET
    'KEY_EXT'            => 'e1ecdce3c8cdc621cad55079',//扩展KEY（推送安卓）
    'SECRET_EXT'         => '220a11d5b9d5f3f59f92a15d',//扩展SECRET（推送安卓）
];
//$GLOBALS['CONF']['JPUSH'] = [
//    'DEPLOY_ENVIRONMENT' => 'test',
//    'PRO_ON'             => true,//PRO推送开关
//    'KEY'                => '76e0e67dff54dd9b3d414803',//默认配置KEY
//    'SECRET'             => '225b1f4241e299f08239391b',//默认配置SECRET
//    'KEY_PRO'            => 'e0cd9619fabb5686a4c77057',//IOS PRO KEY
//    'SECRET_PRO'         => '9b6c1098bde0a7fdb523bb61',//IOS PRO SECRET
//    'KEY_EXT'            => 'e1ecdce3c8cdc621cad55079',//扩展KEY（推送安卓）
//    'SECRET_EXT'         => '220a11d5b9d5f3f59f92a15d',//扩展SECRET（推送安卓）
//];

//微信公众号的配置-开发服-lyq
$GLOBALS['CONF']['WECHAT'] = [
    'APPID'     => 'wxeb292725a9b34381',//AppID(应用ID)
    'APPSECRET' => '87c8e5ce89d7cdbc76bc2accc3034259',//AppSecret(应用密钥)
    'TOKEN'     => 'GOgHOz4yTuiRNYu8Scf8z3NuPr87nv',//Token(令牌)
    'AESKEY'    => 'wrCWZErlSNNJhqzieHHNTCbM2r2guFFuUsJIMWq6',//EncodingAESKey(消息加解密密钥)
];
////微信公众号的配置-测试服-tgh
//$GLOBALS['CONF']['WECHAT'] = [
//    'APPID'     => 'wxbed418d617b9e7c2',//AppID(应用ID)
//    'APPSECRET' => 'df235cae97eddfa1d4320313a58459f9',//AppSecret(应用密钥)
//    'TOKEN'     => 'Ww0savV62olCUi9RiB0G3YJEHzCzzO',//Token(令牌)
//    'AESKEY'    => 'VgteMnh47nfjm2SikaaSYCXbVSlsabxxrrthYFrH',//EncodingAESKey(消息加解密密钥)
//];
////微信公众号的配置-正式服-lyw
//$GLOBALS['CONF']['WECHAT'] = [
//    'APPID'     => 'wxa723c4283d0c114b',//AppID(应用ID)
//    'APPSECRET' => '98d9a220c26dd8d55d18d712b1bc4173',//AppSecret(应用密钥)
//    'TOKEN'     => '8QR3G7ZInmQJCkrV9nduRY78kSeD4w',//Token(令牌)
//    'AESKEY'    => 'lp58FYTe54P6cR0QQ4E8vMwvMaju91H1ibeU3KNyDII',//EncodingAESKey(消息加解密密钥)
//];

$GLOBALS['CONF']['OAUTH'] = [
    'CLIENT_ID' => '1104878344',
    'CLIENT_SECRET' => 's20vH9emKJ6BmT1Q',
];

$GLOBALS['CONF']['TestKey'] = 'O2eWC5ExmwL47Ku8MRBcq35kvhAGRVDiZQvak8z';

$GLOBALS['CONF']['debug'] = 1;

$GLOBALS['CONF']['localLibs']=array('Lib','Prj');
// // 配置cms域名  域名根据开发还是线上环境进行配置
$GLOBALS['CONF']['cmsurl']='wwwtest.xiaoxialicai.com';

//redis服务器列表
$GLOBALS['CONF']['redis']=array(
	array(
		'host' => '115.28.53.193',
		'port' => 3888,
	),
	array(
		'host' => '115.28.172.53',
		'port' => 3888,
	)
);

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