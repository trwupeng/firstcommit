<?php
namespace Lib\SMS;
/**
 * @author wu.peng
 * 
 * Time:2016/07/06 4:49 
 */

class Yimeiruantong {
	//亿美软通发送短信接口URL, 如无必要，该参数可不用修改
	const API_SEND_URL = 'http://hprpt2.eucp.b2m.cn:8080/sdkproxy/sendsms.action';
	//亿美软通余额查询接口URL, 如无必要，该参数可不用修改
	const API_BALANCE_QUERY_URL = 'http://hprpt2.eucp.b2m.cn:8080/sdkproxy/querybalance.action';
	//亿美软通账号
	const API_ACCOUNT = '8SDK-EMY-6699-RIUTQ'; //TDD:正式号
	//亿美软通密码
	const API_PASSWORD = '602744';//TDD:正式密码
    
	private $prefix = '【小虾理财】';//内容前缀
	
	public static $returncode=[
	   '0'=>'成功',
	   '-1'=>'系统异常',
	];
	
	/**
	 * 最简单的XML转数组
	 * @param string $xmlstring XML字符串
	 * @return array XML数组
	 */
	public static function simplest_xml_to_array($obj) {
	    return json_decode(json_encode((array) simplexml_load_string($obj)), true);
	}
	
	/**
	 * 发送短信
	 * @param  string  $mobile      手机号码
	 * @param  string $msg        短信内容
	 * @param  string  $addserial  附加号
	 * @param  string  $seqid 长整型值企业内部必须保持唯一，获取状态报告使用
	 * @param  string  $smspriority  短信优先级1-5
	 * @return string  
	 * @throws \Sooh\Base\ErrException
	 */
	public function send($mobile,$msg,$addserial='',$seqid='',$smspriority=5) {
	    
	    
	    if (strpos($msg, $this->prefix) === false) {
	        $msg = $this->prefix . $msg;
	    }
	    
	    $addserial=mt_rand(1,999999999);
	    
		//亿美软通接口参数
		$postArr = [
			'cdkey' => self::API_ACCOUNT,
			'password' => self::API_PASSWORD,
			'phone' => $mobile,
		    'message' => $msg,
		    'addserial'=>$addserial
		];
        
		var_log($postArr,'resut>>>>>>>>>>>>>>>>>');
		$result = $this->curlPost(self::API_SEND_URL, $postArr);
		
		$result=$result[6];
		$result=self::simplest_xml_to_array($result);
		
		var_log($result,'resut>>>>>>>>>>>>>>>>>');
		if (is_numeric($result['error']) && $result['error'] == 0) {
			return 'success';
		} else {
		    
			throw new \Sooh\Base\ErrException('发送短信失败');
		}
	}

	/**
	 * 查询额度
	 */
	public function queryBalance() {
		$postArr = array('cdkey' => self::API_ACCOUNT, 'password' => self::API_PASSWORD);
		$result  = $this->curlPost(self::API_BALANCE_QUERY_URL, $postArr);
		$resultAll=$this->execResult($result);
		$resultAll*=10;
		return $resultAll;
		//return $this->execResult($result);
	}

	/**
	 * 通过CURL发送HTTP请求
	 * @param string $url        //请求URL
	 * @param array  $postFields //请求参数
	 * @return mixed
	 */
	private function curlPost($url, $postFields, $timeout = 2000, $connection_timeout = 2000) {
		$postFields = http_build_query($postFields);
	
		$ch         = curl_init();
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);

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
		return $this->execResult($result);
	}


	/**
	 * 处理返回值
	 * @param string $result curl result
	 * @return mixed
	 */
	private function execResult($result) {

	    if(is_string($result)){
		$result = preg_split("/[,\r\n]/", $result);
	    }else{
	        $result=$result[6];
	        $result=self::simplest_xml_to_array($result);
	        $result=$result['message'];
	    }
		return $result;
	}



    const MAX_COMMIT_NUM= 200; // 单次最大提交数量

	/**
	 *
     * TODO: 如果以后需要短信的精确发送到用户的状态，需要定义适用各个短信平台各种状态（短信提交状态，用户收到信息状态等等）的通用状态code
     * TODO：将具体的短信平台返回的状态转换成通用状态code
	 * 发送营销短信
	 * @param $mobile
	 * @param $msg
	 */
	public function sendMarket($mobile,$msg,$addserial='',$seqid='',$smspriority=5) {
		$url = 'http://hprpt2.eucp.b2m.cn:8080/sdkproxy/sendsms.action';
		$usr = '8SDK-EMY-6699-RIURL'; //TDD:正式账号
		$pwd = '266237';//TDD:正式密码
	    $end='退订回复TD';
	    
		if (strpos($msg, $this->prefix) === false) {
		    $msg = $this->prefix . $msg.' '.$end;
		}
		
		$addserial=mt_rand(1,9999999999);
		 
        if(is_array($mobile)) {
            if(sizeof($mobile) > self::MAX_COMMIT_NUM){
                throw new \Sooh\Base\ErrException('单次提交最多'.self::MAX_COMMIT_NUM.'个号码');
            }
            $mobile = explode(',', $mobile);
        }
       
        $postData = [
		'cdkey' => self::API_ACCOUNT,
			'password' => self::API_PASSWORD,
			'phone' => $mobile,
		    'message' => $msg,
		    'addserial'=>$addserial
        ];

        $rs = $this->curlPost($url, $postData);
        
        $rs=$rs[6];
        $rs=self::simplest_xml_to_array($rs);
        
        if (is_numeric($rs['error']) && $rs['error'] == 0) {
            return 'success';
        }else {
			throw new \Sooh\Base\ErrException('发送短信失败');
           
        }
	}
	
	/**
	 * 充值接口
	 * 
	 * @param $cdkey 用户序列号
	 * @param $password 用户密码
	 * @param $cardno 充值卡卡号
	 * @param $cardpass 充值卡密码
	 * 
	 */
	public  function  chargeup($cardno,$cardpass){
	    $url='http://hprpt2.eucp.b2m.cn:8080/sdkproxy/chargeup.action';
	    $cdkey='8SDK-EMY-6699-RIUTQ';
	    $password='602744';
	    
	    $postData=['cdkey'=>$cdkey,
	        'password'=>$password,
	        'cardno'=>$cardno,
	        'cardpass'=>$cardpass
	    ];
	    
	    $rs = $this->curlPost($url, $postData);
	    var_log($rs,'rs>>>>>>>>>>>');
	    $rs=$rs[6];
	    $rs=self::simplest_xml_to_array($rs);
	    
	    if (is_numeric($rs['error']) && $rs['error'] == 0) {
	        return 'success';
	    }else {
	        throw new \Sooh\Base\ErrException('发送短信失败');
	        
	    }
	}
	
	/**
	 * 发送定时短信接口
	 * @param $cdkey 用户序列号
	 * @param $password 用户密码
	 * @param $phone  手机号码
	 * @param $message 短信内容
	 * @param $addserial 附加码
	 * @param $sendtime  定时时间  日期格式是20090101101010
	 * @param $seqid 长整型值企业内部必须保持唯一，获取状态报告使用	  
	 * @param $smspriority  短信优先级
	 * 
	 * */
	
	public  function  endtimesms($moblie,$message,$addserial='',$sendtime){
	    $url='http://hprpt2.eucp.b2m.cn:8080/sdkproxy/sendtimesms.action';
	    $cdkey='8SDK-EMY-6699-RIUTQ';
	    $password='602744';
	    
	    if (strpos($message, $this->prefix) === false) {
	        $message = $this->prefix . $message;
	    }
	    
	    $addserial=mt_rand(1,9999999999);
	    	
	    if(is_array($mobile)) {
	        if(sizeof($mobile) > self::MAX_COMMIT_NUM){
	            throw new \Sooh\Base\ErrException('单次提交最多'.self::MAX_COMMIT_NUM.'个号码');
	        }
	        $mobile = explode(',', $mobile);
	    }
	     
	    $postData = [
	        'cdkey' => $cdkey,
	        'password' => $password,
	        'phone' => $moblie,
	        'message' => $message,
	        'addserial'=>$addserial,
	        'sendtime'=>$sendtime,
	    ];
	    $rs = $this->curlPost($url, $postData);
	    var_log($rs,'rs>>>>>>>>>>>>>');
	    $rs=$rs[6];
	    $rs=self::simplest_xml_to_array($rs);
	    
	    if (is_numeric($rs['error']) && $rs['error'] == 0) {
	        return 'success';
	    }else {
	        throw new \Sooh\Base\ErrException('发送短信失败');
	      
	    }
	}
	
	/**
	 * 修改密码接口
	 * @param $cdkey 用户序列号
	 * @param $password 用户密码
	 * 
	 * @param $newPassword 新密码
	 * 
	 * */
	
	public  function newPassword($newPassword){
	    $url='http://hprpt2.eucp.b2m.cn:8080/sdkproxy/changepassword.action';
	    $cdkey='8SDK-EMY-6699-RIUTQ';
	    $password='602744';
	    
	    $postData=['cdkey'=>$cdkey,
	        'password'=>$password,
	        'cardno'=>$cardno,
	        'newPassword'=>$newPassword
	    ];
	    
	    $rs = $this->curlPost($url, $postData);
	    var_log($rs,'rs>>>>>>>>>>>');
	    $rs=$rs[6];
	    $rs=self::simplest_xml_to_array($rs);
	     
	    if (is_numeric($rs['error']) && $rs['error'] == 0) {
	        return 'success';
	    }else {
	        throw new \Sooh\Base\ErrException('发送短信失败');
	        
	    }
	}
	
	
	/**
	 * 序列号注册接口
	 * 
	 * @param   $cdkey   用户序列号
	 * 
	 * @param   $password  用户密码
	 * 
	 * */
	public  function  regist(){
 
	    $url='http://hprpt2.eucp.b2m.cn:8080/sdkproxy/regist.action';
	    $cdkey='8SDK-EMY-6699-RIUTQ';
	    $password='602744';
	    
	    $postData=['cdkey'=>$cdkey,
	        'password'=>$password,
	    ];
	    
	    $rs = $this->curlPost($url, $postData);
	    $result=$result[6];
		$result=self::simplest_xml_to_array($result);
		
		var_log($result,'resut>>>>>>>>>>>>>>>>>');
		if (is_numeric($result['error']) && $result['error'] == 0) {
			return 'success';
		} else {
		    
			throw new \Sooh\Base\ErrException('发送短信失败');
		}
	    
	}
	
}