<?php
class Yaf_Request_Abstract
{
	/**
	 * @var Yaf_Request_Abstract
	 */  
    protected $_request;

	/**
	 * @var Yaf_Response_Abstract
	 */
    protected $_response;

	/**
	 * @var Yaf_View_Interface
	 */
    protected $_view;
	/**
	 * @return string ModuleName
	 */
	public function getModuleName(){return 'modulename';}
	/**
	 * @return string ControllerName
	 */
	public function getControllerName(){return 'ctrlname';}
    /**
	 * @return string ActionName
	 */
	public function getActionName(){return 'Action';}
    
	/**
	 * @param string $name module-name
	 * @return boolean
	 */
	public function setModuleName($name){return true;}
    
	/**
	 * @param string $name controller-name
	 * @return boolean
	 */
	public function setControllerName($name){return true;}
	
	/**
	 * @param string $name action-name
	 * @return boolean
	 */
	public function setActionName($name){return true;}

	/**
	 * 初始化
	 */
	public function init(){}
	/**
	 * @return \Exception 
	 */
	public function getException(){return new ErrorException;}
	public function get(string $name,$dafault= NULL){return $default;}
	/**
	 * @return array
	 */
	public function getParams (){return array();}
	/**
	 * @param string $name 变量名
	 * @param mixed $default 当空值的时候用$default替代
	 * @return mixed
	 */
	public function getParam($name,  $dafault= NULL){return $default;}
	/**
	 * @param string $name 变量名
	 * @param mixed $value 当空值的时候用$default替代
	 * @return Yaf_Request_Abstract
	 */
	public function setParam($name,  $value){return $this;}
	/**
	 * @return string 可能的返回值为GET,POST,HEAD,PUT,CLI等
	 */
	public function getMethod( ){return 'GET';}
	
	public function getLanguage(){return '';}
	public function getQuery(string $name= NULL){return '';}
	public function getPost(string $name= NULL){return '';}
	public function getEnv(string $name= NULL){return '';}
	public function getServer(string $name= NULL){return '';}
	public function getCookie(string $name= NULL){return '';}
	public function getFiles(string $name= NULL){return '';}
	public function isGet(){return true;}
	public function isPost(){return true;}
	public function isHead(){return true;}
	public function isXmlHttpRequest(){return true;}
	public function isPut(){return true;}
	public function isDelete(){return true;}
	public function isOption(){return true;}
	public function isCli(){return true;}
	
	public function isDispatched(){return true;}
	public function setDispatched(){return true;}
	public function isRouted(){return true;}
	public function setRouted(){return true;}

}