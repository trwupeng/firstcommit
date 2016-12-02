<?php
namespace Lib\Api\Publishdata;

/**
 *
 * @author wu.peng
 *        
 */
class Base 
{

    /**
     *
     * @param string $classname            
     * @return \Lib\Api\Lib\Api\Publishdata\Base;
     *
     *
     */
    public function factory($classname)
    {   
     
        $classname = '\\Lib\\Api\\Publishdata\\'.$classname;
        $o = new $classname();
        return $o;
    }

    /**
     *
     * @var Yaf_Request_Abstract;
     *
     *
     */
    protected $_request;

    /**
     *
     * @var Yaf_View_Simple
     *
     */
    protected $_view;

    public function initReqView($request, $view)
    {
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $this->_request = $request;
        $this->_view = $view;
    }

    public function auth()
    {   
    
    }
    
   
    
    public function oneday()
    {
        
    }

    public function error()
    {
        
    }

    public function hourly()
    {
        
    }
    
    /**
     *
     * return \Sooh\DB\Interfaces\All.php
     * 
     * */
    protected  function  getDB(){
        return \Sooh\DB\Broker::getInstance();
    }
    
  protected  $tb_apiuser='db_p2prpt.tb_apiaccounts'; 

  protected  function  _login($token){
      $r=$this->getDB()->getRecord($this->tb_apiuser,'*',array('token'=>$token));
      return array('u'=>$r['u'],'p'=>$r['pass'],'expired'=>$r['expired']);
  }
  
  protected  function  _auth($type='wdzj',$u='xiaoxialicai',$p='xiaoxia123456'){
      if(empty($u)||empty($p))return null;
      $r=$this->getDB()->getRecord($this->tb_apiuser,'*',array('utype'=>$type,'u'=>$u));
      //var_log($r,'r>>>>>>>>>>');
      //var_log(\Sooh\DB\Broker::lastCmd(),'sql>>>>>>>>>>');
      $ip=$this->_request->get('HTTP_X_FORWARDED_FOR');
      if($p!=$r['pass'])return null;
      if(!empty($r['ips']))$ips=explode(',', $r['ips']);
      if(!empty($ips)&&!in_array($ip, $ips)){
          $this->_view->assign('your_ip',$ip);
          return null;
      }
      $dt=\Sooh\Base\Time::getInstance()->timestamp();
      $token=md5($u.$dt);
      $ret=$this->getDB()->updRecords($this->tb_apiuser,array('token'=>$token,'expired'=>$dt+3600,'iplast'=>$ip),array('u'=>$u));
      if($ret)return $token;
      else return null;
  }
}