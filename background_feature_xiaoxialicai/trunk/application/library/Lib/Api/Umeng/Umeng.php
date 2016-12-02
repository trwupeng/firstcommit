<?php
namespace Lib\Api\Umeng;

/**
 * @package Lib\Api
 * @author  wu.peng
 */
class Umeng
{
    //umeng 获取用户密钥的接口
    const API_SEND_URL = 'http://api.umeng.com/authorize';
   
    const API_ACCOUNT = 'zhaoyuguang@kkdai.com.cn';
   
    const API_PASSWORD = 'zyg315';
    
    public   $headers;
    
    
    
       // $this->headers =array("Authorization: Basic emhhb3l1Z3VhbmdAa2tkYWkuY29tLmNuOnp5ZzMxNQ=",'Content-Type:  data/gzencode and rsa public encrypt;charset=UTF-8');
  
    
    /**
     * @return string
     * @throws \Sooh\Base\ErrException
     */
    public function get_token()
    {
      
        $postArr = [
            'email'    => self::API_ACCOUNT,
            'password'       => self::API_PASSWORD,
  
        ];

        $result = $this->curlPost(self::API_SEND_URL, $postArr);
        if(!empty($result)){
            return  $result;
        }
    }
    
    
    /**
     * @return string
     * @throws \Sooh\Base\ErrException
     */
    public function auth_token($per_page,$page)
    {
        $url='http://api.umeng.com/apps';
        $auth_token='6yZ9WBSsChOJiirf4ahx';
        
        $postArr = [
            'per_page'    => $per_page,
            'page'       => $page,
             'auth_token'=>$auth_token
    
        ];
    
        $result = $this->curlGet($url, $postArr);
        if(!empty($result)){
            return  $result;
        }
    }
    
    public  function new_users(){
        
        $url="http://api.umeng.com/new_users";
        $appkey="55c9806de0f55a9ae6000b8e";
        $start_date="2016-08-01";
        $end_date="2016-08-07";
        $period_type="weekly";
       // $channels="100020150101100001";
        $auth_token="emhhb3l1Z3VhbmdAa2tkYWkuY29tLmNuOnp5ZzMxNQ=";
        $postArr = [
           'appkey'=>$appkey,
            'start_date'=>$start_date,
            'end_date'=>$end_date,
            'period_type'=>$period_type,
            //'channels'=>$channels
            'auth_token'=>$auth_token
        
        ];
        
        $result = $this->curlPost($url, $postArr);
        if(!empty($result)){
            return  $result;
        }
        
    }
    
    /**
     * 通过CURL发送HTTP请求  (get请求)
     * @param string $url        //请求URL
     * @param array  $postFields //请求参数
     * @return mixed
     */
    private function curlGet($url, $postFields, $timeout = 2000, $connection_timeout = 2000)
    {
        $postFields = http_build_query($postFields);
        $url=$url.'?'.$postFields;
        $ch         = curl_init();
       // curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        //curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);

        if (defined("CURLOPT_TIMEOUT_MS")) {
            curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT_MS, $timeout);
        } else {
            curl_setopt($ch, CURLOPT_TIMEOUT, ceil($timeout / 1000));
        }
        if (defined("CURLOPT_CONNECTTIMEOUT_MS")) {
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, $connection_timeout);
        } else {
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, ceil($connection_timeout / 1000));
        }

        $result = curl_exec($ch);
        var_log($result,'result《《《《《《《《《《');
        curl_close($ch);
        return $this->execResult($result);
    }

    /**
     * 通过CURL发送HTTP请求  (post请求)
     * @param string $url        //请求URL
     * @param array  $postFields //请求参数
     * @return mixed
     */
    private function curlPost($url, $postFields, $timeout = 2000, $connection_timeout = 2000)
    {    
       
        $postFields = http_build_query($postFields);
        var_log($postFields,'var>>>>>>>>>');
//         $this->headers=array("Authorization: Basic emhhb3l1Z3VhbmdAa2tkYWkuY29tLmNuOnp5ZzMxNQ==");
//         var_log($this->headers,'herads>>>>>>>>>');
        $ch         = curl_init();
        //curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER,0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
           
      
//         $ch = curl_init();
//         curl_setopt($ch, CURLOPT_URL, $url);
//         curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
//         curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
//         curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
//         curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
//         curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
//         curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
//         curl_setopt($ch, CURLOPT_POST, 1);
//         curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
//         curl_setopt($ch, CURLOPT_TIMEOUT, 30);
//         curl_setopt($ch, CURLOPT_HEADER, 0);
//         curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        
        
        if (defined("CURLOPT_TIMEOUT_MS")) {
            curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT_MS, $timeout);
        } else {
            curl_setopt($ch, CURLOPT_TIMEOUT, ceil($timeout / 1000));
        }
        if (defined("CURLOPT_CONNECTTIMEOUT_MS")) {
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, $connection_timeout);
        } else {
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, ceil($connection_timeout / 1000));
        }
         
         
        $result = curl_exec($ch);
     
        curl_close($ch);
        
        var_log($result,'ch<<<<<<<<<<<<<');
        return $this->execResult($result);
    }
    
    /**
     *
     */
    /**
     * 处理返回值
     * @param string $result curl result
     * @return mixed
     */
    private function execResult($result)
    {
        //$result = preg_split("/[,\r\n]/", $result);
        return $result;
    }


    const MAX_COMMIT_NUM = 20000; // 单次最大提交数量

    /**
     * 如果以后需要短信的精确发送到用户的状态，需要定义适用各个短信平台各种状态（短信提交状态，用户收到信息状态等等）的通用状态code
     * 将具体的短信平台返回的状态转换成通用状态code
     * 发送营销短信
     * @param string $phone 手机号
     * @param string $msg   发送内容
     * @return string
     * @throws \Sooh\Base\ErrException
     */
    public function sendMarket($phone, $msg)
    {
        $url     = 'http://222.73.117.169/msg/HttpBatchSendSM?';
        $account = 'xiaoxia_yx';
        $pwd     = 'Tch931947';
        $product = 2347;
        $extno   = 1793;

        if (is_array($phone)) {
            if (sizeof($phone) > self::MAX_COMMIT_NUM) {
                throw new \Sooh\Base\ErrException('单次提交最多' . self::MAX_COMMIT_NUM . '个号码');
            }
            $phone = explode(',', $phone);
        }

        $postData = [
            'account'    => $account,
            'pswd'       => $pwd,
            'msg'        => $msg,
            'mobile'     => $phone,
            'needstatus' => true,
        ];

        $rs = $this->curlPost($url, $postData);
        if (is_numeric($rs[1]) && $rs[1] == 0) {
            return 'success';
        } else {
            throw new \Sooh\Base\ErrException('提交短信到创蓝平台失败');
        }
    }
}