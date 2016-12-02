<?php
namespace Sooh\Base\Tests;

/**
 * 测试时的带中断炸弹
 *	 子类
 *		module/controller/IndexTest.php 测试 module/controller/index 接口
		class IndexTest extends \Sooh\Base\Tests\ApiHttpGetJson {
			protected $bombsForError=[
				//'bombId1'=>['error_should_found'],
				//'bombId2'=>'onBomb_bombId2'
			];
			protected function initSetMC($act='index'){parent::initSetMC(__DIR__.'/'.$act);}
			protected function getUrl($mca,$args){return trim(\Sooh\Base\Ini::getInstance()->get('uriBase.www'),'/').'/'.trim($mca).'?'.http_build_query($args);}
			public function testDefault()
			{
				$this->apiChk($this->getUrl('dev/test', ['sdf'=>123]),['123'],'hahaha');
				$r = $this->getRecord('db_p2p.tb_wares_0', ['waresId'=>'1233456437856']);
				$this->assertEquals($r['waresName'],'wares001');
				$this->assertFalse(empty($r));
				$r = $this->getRecord('\Prj\Data\Investment', ['ordersId'=>'1014439500292497856']);
				$this->assertEquals($r['ordersId'],'1014439500292497856');

				$this->assertContains('$cmp',$this->jsonstrByHttpGet($this->getUrl('dev/test', ['sdf'=>123])),'error-tip');
			}
			protected function onBomb_bombId2($strJson,$errDescPrefix)
			{
				$this->assertContains('$cmp',$strJson,'error-tip');
			}
			protected function resetAfterEachApiChk()
			{
			}
		}
 *   主要用法：
 *		$this->getRecord($tbname_or_KVObj,$where);
 *		$this->jsonstrByHttpGet($url);
 *		$this->apiChk($url,$checkList,$errorDesc)
 * @author simon.wang
 */
class ApiHttpGetJson extends \PHPUnit_Framework_TestCase {
	//const firstStep=1;
	protected $bombsForError=[
		//'bombId'=>['error_should_found'],
	];

	protected $bombsJson = '';
	protected $taskData = null;

	/**
	 * 
	 * @return \Sooh\DB\Interfaces\All;
	 */
	protected function getDB()
	{
		return null;
	}
	protected function getRecord($tb,$where)
	{
		if($tb[0]!=='\\'){
			$r = $this->getDB()->getRecord($tb,'*',$where);
		}else{
			$rs = call_user_func($tb.'::loopFindRecords', $where);
			if(!empty($rs)){
				$r = current($rs);
			}else{
				$r = null;
			}
		}
		return $r;
	}
	protected function initSetMC($dir=null)
	{
		if(empty($this->moduleController)){
			$r = explode(DIRECTORY_SEPARATOR, $dir);
			while(sizeof($r)>3){
				array_shift($r);
			}
			$this->moduleController = implode('/', $r);
		}
	}

	/**
	 * 拼装测试URL
	 * @param string $mca 模块/控制器/Action
	 * @param array $args 参数
	 * @return string url
	 */
	protected function getUrl($mca, $args) {
		return trim(\Sooh\Base\Ini::getInstance()->get('uriBase.www'), '/') . '/' . trim($mca) . '?' . http_build_query($args);
	}

	/**
	 * 接口检查
	 * @param string $url
	 * @param array $checkList 应该包含的字符串的数组
	 * @param string $errorDesc 
	 */
	protected function apiChk($url,$checkList,$errorDesc)
	{
		$this->initSetMC();
		if(empty($errorDesc)){
			$errorDesc = 'on check missing';
		}
		$errorDesc = "[$errorDesc]";
		$json = $this->jsonstrByHttpGet($url);
		if (is_array($checkList)) {
			$this->checkJson($json, $checkList, $errorDesc);
		} else {
			$this->$checkList($json, $errorDesc);
		}
		$this->resetAfterEachApiChk();

		if(!empty($this->bombsForError)){
			foreach($this->bombsForError as $stepId=>$checkList)
			{
				$json = $this->jsonstrByHttpGet($url.'&__testBomb__='.$stepId);
				if(is_array($checkList)){
					$this->checkJson($json, $checkList, "[on bomb {$stepId}  {$errorDesc}]");
				}else{
					$this->$checkList($json,$errorDesc); 
				}
				$this->resetAfterEachApiChk($stepId);
			}
		}
	}
	/**
	 * 在apiChk里每次检查完一次调用结果后，调用此函数重置相关资源，手动调用检查不会触发此函数
	 */
	protected function resetAfterEachApiChk($stepId = '')
	{
		
	}
	protected $moduleController;
	protected function checkJson($json,$shouldHas,$errorDesc)
	{
		if(!is_string($json)){
			$json = json_encode($json);
		}
		if(!is_array($shouldHas)){
			$shouldHas = [$shouldHas];
		}
		$errorDesc = $this->moduleController.' '.$errorDesc;
		foreach($shouldHas as $cmp){
			$this->assertContains($cmp,$json,$errorDesc);
//			if(false===strpos($json, $cmp)){
//				$this->errorFound($errorDesc.' '.$cmp.' [RESPONSE]'.$json);
//			}
		}
	}
//	protected function errorFound($err)
//	{
//		$this->assertTrue(false,'◆◆◆◆◆◆'.  substr(get_called_class(),0,-4).' '.$err);
//	}
	protected $arrHeaders;
	protected $arrCookies=['SoohSessId'=>'session_phpunit'];

	/**
	 * 通过httpGet获取json
	 * @param type $url
	 * @return string
	 */
	protected function jsonstrByHttpGet($url)
	{
		$ch = curl_init();
		if($ch){
			curl_setopt($ch, CURLOPT_URL, $url);
			if(is_array($this->arrHeaders) && !empty($this->arrHeaders)){
				curl_setopt($ch, CURLOPT_HTTPHEADER, $this->arrHeaders);
			}
			if(is_array($this->arrCookies) && !empty($this->arrCookies)){
				curl_setopt($ch, CURLOPT_COOKIE,str_replace('&', '; ', http_build_query($this->arrCookies)));
			}
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			$output = curl_exec($ch);
			$err=curl_error($ch);
			if(!empty($err)){
				curl_close($ch);
				$str= false;
			}else {
				curl_close($ch);
				$str= $output.$err;
			}
			//$httpCodeLast = curl_getinfo($ch,CURLINFO_HTTP_CODE);
		}else{
			$str= "curl init failed";
		}
		
		$test = json_decode($str);
		if(!empty($test)){
			return $str;
		}else{
			$this->assertTrue(false,'◆◆◆◆◆◆'. $this->moduleController.' httpGet failed:'.$url);
		}
	}

	protected function bombsCallback($json, $errorDesc) {
		$this->bombsJson = $json;
		error_log('=-=-==-=-==-=-==-=-==-=-==-=-==-=-==-=-==-=-==-=-==-=-==-=-==-=-==-=-=');
		var_log($json, 'json:');
		var_log($errorDesc, 'errorDesc');
	}
}
