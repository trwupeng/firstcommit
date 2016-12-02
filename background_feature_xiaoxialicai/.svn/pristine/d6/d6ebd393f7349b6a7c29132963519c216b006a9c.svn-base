<?php
//php.ini 设置 yaf.use_spl_autoload=on
$dt000=microtime(true);
if(empty($_GET) && empty($_POST)){
	error_log('attacking??'.$_SERVER['REMOTE_ADDR']);
	exit;
}elseif(isset($_REQUEST['__'])){
	if(explode('/', $_REQUEST['__'])==1){
	        error_log('attacking??'.$_SERVER['REMOTE_ADDR']);
	        exit;
	}
}else{
	$r = explode('/',$_SERVER['REQUEST_URI']);
	error_log('###'.var_export($r,true));
}
define("APP_PATH",  dirname(__DIR__)); /* 指向public的上一级 */
if(!defined('SOOH_INDEX_FILE')){
	define ('SOOH_INDEX_FILE', 'index.php');
}
define('SOOH_ROUTE_VAR','__');
if($_REQUEST['__']=='public/recordRegEvt')exit;


ob_start();
include dirname(__DIR__) .'/conf/globals.php';
$ini = \Sooh\Base\Ini::getInstance();
if($_COOKIE['SoohSessId']=='imei:null'){
	$newCookiePre = "imei";
	$_COOKIE['SoohSessId']='';
}elseif($_COOKIE['SoohSessId']=='idfa:'){
	$newCookiePre = "idfa";
	$_COOKIE['SoohSessId']='';
}
if(empty($_COOKIE['SoohSessId'])){
	function myloader2($class)  
	{  
		$r = explode('\\', $class);
		if($r[0]=='\\')array_shift($r);
		if(sizeof($r)==1){
			if(file_exists($r[0].'.php')) {
				//error_log('try auto-load : '.$r[0].'.php');
				include $r[0].'.php';
				return true;
			} else {
				return false;
			}
		}else{
			if(in_array($r[0], $GLOBALS['CONF']['localLibs'])){
				include APP_PATH.'/application/library/'.implode('/', $r).'.php';
				return true;
			}else{
				return false;
			}
		}
	}
spl_autoload_register('myloader2'); 
	
	$retry=20;
	$rpc = \Prj\BaseCtrl::getRpcDefault('SessionStorage');
	if($rpc==null){
		\Lib\Services\SessionStorage::setStorageIni();
	}
	$storage = \Lib\Services\SessionStorage::getInstance($rpc);
	$dt = time();
	$newCookieReal='md5:db3933fc8172db36b79c4909706a6194';
	while($retry>0){
		$retry--;
		if(empty($newCookieReal)){
			if(!empty($newCookiePre)){
				$newCookieReal = $newCookiePre.':'.md5(microtime(true).getmypid());
			}else{
				$newCookieReal = 'md5:'.md5(microtime(true).getmypid());
			}
		}
		$obj = \Sooh\DB\Cases\SessionStorage::getCopy($newCookieReal);
		$obj->load();
		if($obj->exists()){
			$newCookieReal='';
			continue;
		}
		try{
			$obj->setSessionData([]);
			$obj->setField('lastUpdate', $dt);
			$obj->update();
			if(!empty($newCookiePre)){
				setcookie('SoohSessId',$newCookieReal,time()+86400*7,'/',\Sooh\Base\Ini::getInstance()->get('CookieDomainBase'));
				echo '{"code":400,"msg":"用户系统升级中"}';
				exit;
			}else{
				setcookie('SoohSessId',$newCookieReal,time()+3600,'/',\Sooh\Base\Ini::getInstance()->get('CookieDomainBase'));
				break;
			}
		}catch(\ErrorException $e){
			\Sooh\DB\Cases\SessionStorage::freeAll(['sessionId'=>$newCookieReal]);
			continue;
		}
	}
}
if(40!==\Sooh\Base\Ini::getInstance()->get('deploymentCode')){///////////////////////////////////////////////////////--debug
	error_log("-------------------------------------------------------tart:route=".$_GET['__']." cmd=".(empty($_GET['cmd'])?"":$_GET['cmd'])." pid=".  getmypid());
	error_log('----cookie:' . json_encode($_COOKIE));
	error_log('----get:' . json_encode($_GET));
	error_log('----post:' . json_encode($_POST));
	\Sooh\Base\Tests\Bomb::$flg=empty($_REQUEST['__'])?@array_shift(explode('?',$_SERVER['REQUEST_URI'])):$_REQUEST['__'];
}///////////////////////////////////////////////////////--debug

$app  = new Yaf_Application(APP_PATH . "/conf/application.ini");

$dispatcher = $app->getDispatcher();
if(!empty($reqeustReal)){
	$dispatcher->setRequest( $reqeustReal );
}

$view = \SoohYaf\SoohPlugin::initYafBySooh($dispatcher);
$dispatcher->returnResponse(TRUE);
try{
	$response = $app->run();
}catch(\ErrorException $e){
	$code = $e->getCode();
	$viewType=$ini->viewRenderType();
	if ($viewType === 'html' && $code === 401) {
		header('Location:' . \Sooh\Base\Ini::getInstance()->get('uriBase')['www'] . '/error.html');
	}elseif($code===300 || $code===301){
		if($viewType==='wap'){
			header('Location:' . \Sooh\Base\Ini::getInstance()->get('uriBase')['rpt'] . '/manage/manager/login?__VIEW__=wap&errTrans='.  urlencode($e->getMessage()));
		}else{
			$view->assign('statusCode',$code);
			$view->assign('msg',$e->getMessage());
			if($viewType==='html'){
				$ini->viewRenderType('json');
			}
			$response=new Yaf_Response_Http();
			$req = $dispatcher->getRequest();
			$response->setBody($view->render($req->getControllerName().'/'.$req->getActionName().'.'.$viewType.'.phtml'));
		}
	} else {
		$view->assign('code',$code);
		$view->assign('msg',$e->getMessage());
		error_log("Error Caught at index.php:".$e->getMessage()."\n".\Sooh\DB\Broker::lastCmd()."\n".$e->getTraceAsString()."\n");
		$response=new Yaf_Response_Http();
		$req = $dispatcher->getRequest();
		$response->setBody($view->render($req->getControllerName().'/'.$req->getActionName().'.'.$viewType.'.phtml'));
	}
}

if($ini->viewRenderType()==='json') {
	header('Content-type: application/json');
}
if($response){
	$response->response();
	$end = ob_get_contents();
	ob_flush();
}
\Sooh\Base\Ini::registerShutdown(null, null);
\Sooh\Base\Tests\Bomb::onShutdown();

error_log("====================================================================end:route=".$_GET['__']." cmd=".$_GET['cmd']." pid=".  getmypid()." dur ".sprintf("%.2f",microtime(true)-$dt000));
