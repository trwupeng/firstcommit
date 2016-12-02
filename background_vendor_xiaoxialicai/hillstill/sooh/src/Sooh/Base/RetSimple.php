<?php
namespace Sooh\Base;
/**
 * 执行结果
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class RetSimple {
	const ok=0;
	const errDefault=-1;
	public function __construct($code=0,$msg='',$data=null) {
		$this->code=$code-0;
		$this->msg=$msg;
		$this->data=$data;
	}
	public $code=0;
	public $msg='';
	public $data=null;
	public function toArray()
	{
		$r=array('code'=>$this->code);
		if(!empty($this->msg)){
			$r['msg']=  $this->msg;
		}
		if(!empty($this->data)){
			$r['data']=  $this->data;
		}
		return $r;
	}
}
