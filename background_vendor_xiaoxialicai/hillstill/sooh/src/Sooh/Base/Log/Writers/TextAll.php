<?php
namespace Sooh\Base\Log\Writers;
/**
 * 默认的写文本的log writer (一个文件中)
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class TextAll {
	private $fullname;
	public function __construct($path='',$filename='') {
		$this->fullname = $path.'/'.$filename;
	}
	/**
	 * 
	 * @param \Sooh\Base\Log\Data $logData
	 */
	public function write($logData)
	{
		$arr = $logData->toArray();
		$ret = array('ret'=>$arr['ret']);
		unset($arr['ret']);
		$arr = array_merge($ret,$arr);
		if($this->fullname==='/'){
			error_log(json_encode($arr));
		}else{
			file_put_contents($this->fullname, '['.date('m-d H:i:s').']'.json_encode($arr), FILE_APPEND);
		}
	}
	public function free()
	{
		
	}	
}
