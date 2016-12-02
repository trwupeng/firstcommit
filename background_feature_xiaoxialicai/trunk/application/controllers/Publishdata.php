<?php
/**
 * @param 对外提供数据的控制类
 * 
 * @author wupeng
 * 
 * */
class  PublishdataController extends Yaf_Controller_Abstract{

/**
 * @var \Prj\Lib\Api\Base;
 * */

  protected $api;
  public function init(){
      
    
      $this->api=\Lib\Api\Publishdata\Base::factory($this->_request->get('__CLASS__'));
   
      $this->api->initReqView($this->_request,$this->_view);
  
  }  
 

  public function authAction(){
      $this->api->auth();
  }

  public function onedayAction(){
      $this->api->oneday();
  }
  
  public  function errorAction(){
      $this->api->error();
  }
  
  public function  hourlyAction(){
      $this->api->hourly();
  }
}