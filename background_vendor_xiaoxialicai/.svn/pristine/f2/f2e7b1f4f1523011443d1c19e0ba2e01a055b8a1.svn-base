<?php
namespace SoohYaf;
/**
 * yaf 框架 初始化 以及 默认登入检查的 plugin
 * 需要 define('SOOH_ROUTE_VAR','__');
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class SoohPlugin extends \Yaf_Plugin_Abstract{
	/**
	 * @param array $loginChk 登入检查的控制逻辑定义
	 */
	public static function initRightsCheck($loginChk=array())
	{
		
	}
	/**
	 * 
	 * @param \Yaf_Dispatcher $dispatcher
	 * @param  string $jqueryVer 使用的jquery文件，默认值：jquery-1.11.2.min.js
	 * @return view
	 */
	public static function initYafBySooh($dispatcher,$jqueryVer='jquery-1.11.2.min.js')
	{
		$router = $dispatcher->getRouter();
		$router->addRoute("byVar", new \Yaf_Route_Supervar(SOOH_ROUTE_VAR));
		
		\Yaf_Loader::getInstance()->registerLocalNameSpace($GLOBALS['CONF']['localLibs']);
		
		$req = $dispatcher->getRequest();
		$tmp = $req->get('__ONLY__');
		if($tmp=='body'){
			\SoohYaf\Viewext::$bodyonly = true;
		}
		$tmp = trim($req->get('__VIEW__'));//html(default),wap,  json
		define('VIW_INC_PATH', APP_PATH.'/application/views/_inc/');
		\SoohYaf\Viewext::$jqueryVer=$jqueryVer;
		if(!empty($tmp)){
			$tmp=strtolower ($tmp);
			\Sooh\Base\Ini::getInstance()->viewRenderType($tmp);
			if($tmp=='jsonp'){
				\Sooh\Base\Ini::getInstance()->initGobal(array('nameJsonP'=>$req->get('jsonp','jsonp')));
			}
		}

//		$tmp = $dispatcher->getRequest()->get('__GZIP__');
//		if(!empty($tmp)){
//			$tmp = strtolower ($tmp);
//			if($tmp=='gzip')define("ZIP_OUTPUT",$tmp);
//		}
		
		$view =  new \SoohYaf\Viewext( null );
		$dispatcher->setView(  $view );
		
		//$dispatcher->registerPlugin(new SoohPlugin());
		return $view;
	}
	
	public function routerStartup (\Yaf_Request_Abstract $request, \Yaf_Response_Abstract $response)
	{
//TODO: useless code
//		$request->setBaseUri('/V1');
//		$r = explode('?', $_SERVER['REQUEST_URI']);
//		$request->setRequestUri($r[0]);

	}
	public function routerShutdown(\Yaf_Request_Abstract $request, \Yaf_Response_Abstract $response)
	{
//		$router = \Yaf_Dispatcher::getInstance()->getRouter();
//var_log($request,'onShutdone:'.$request->getBaseUri()."##==");		
//TODO: useless code
//		if($_SERVER['REMOTE_ADDR']!='172.25.3.9')
//		error_log("onShutdone##".__CLASS__.'->'.__FUNCTION__.":".$request->getModuleName().'/'.$request->getControllerName()."/".$request->getActionName()."#".$_SERVER['REMOTE_ADDR'].'#'.$_SERVER['REQEUST_URI']);
//		$m = strtolower($request->getModuleName());
//		$c = strtolower($request->getControllerName());
//		if($m!=='index'){
//			$sess = \Sooh\Base\Acl\Acl::getInstance();
//			if(!$sess->isLogined()){
//				$sess->onNeedsLogin($_SERVER['REQUEST_URI']);
//			}
//			if(!$sess->hasRightsTo($m, $c)){
//				$sess->onNeedsRights("$m.$c");
//			}
//		}
		
		/*
		 * //TODO: useless code
		$ini = \Sooh\Base\Ini::getInstance ();
		$tmp=$request->getBaseUri();
		if(substr($tmp,-4)=='.php'){
			$tmp = explode('/',$tmp);
			array_pop($tmp);
			$tmp = implode('/', $tmp);
		}
		if($tmp=='/')$tmp='';
		$ini->initGobal(array('request'=>array('action'=>$request->getActionName(),
												'controller'=>lcfirst($request->getControllerName()),
												'module'=>lcfirst($request->getModuleName()),
												'baseUri'=>$tmp
												)
				));
		*/
		
//		if(\Sooh\Base\Ini::getInstance()->viewRenderType()!=='json'){
//			\Sooh\HTML\Base::$jquery =Viewext::$jqueryVer;
//		}
//		error_log(__CLASS__.'->'.__FUNCTION__);
	}
	public function dispatchLoopStartup(\Yaf_Request_Abstract $request, \Yaf_Response_Abstract $response)
	{
//		error_log(__CLASS__.'->'.__FUNCTION__);
	}
	public function preDispatch(\Yaf_Request_Abstract $request, \Yaf_Response_Abstract $response)
	{
//		error_log(__CLASS__.'->'.__FUNCTION__);
	}
	public function postDispatch(\Yaf_Request_Abstract $request, \Yaf_Response_Abstract $response)
	{
//		error_log(__CLASS__.'->'.__FUNCTION__);
	}
	public function dispatchLoopShutdown(\Yaf_Request_Abstract $request, \Yaf_Response_Abstract $response)
	{
//		error_log(__CLASS__.'->'.__FUNCTION__);
	}
}
