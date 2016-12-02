<?php
namespace Sooh\Base\Tests;
/**
 * 测试用的短信接口
 */
class SMS{
	protected $tb;
	/**
	 *
	 * @var \Sooh\DB\Interfaces\All 
	 */
	protected $db;
	/**
	 * 构建函数（数据库）
	 * @param \Sooh\DB\Interfaces\All $db
	 * @param string $tb
	 */
	public function __construct($db,$tb) {
		$this->db=$db;
		$this->tb=$tb;
	}
	/**
	 * 给指定用户发短信
	 * @param type $phone
	 * @param type $msg
	 * @throws \ErrorException
	 */
	public function send($phone,$msg,$channel=0)
	{
		try{
			\Sooh\DB\Broker::errorMarkSkip(\Sooh\DB\Error::tableNotExists);
			$this->db->addRecord($this->tb, array('phone'=>$phone,'msg'=>$msg,'channel'=>$channel));
		} catch (\ErrorException $e){
			if(\Sooh\DB\Broker::errorIs($e,\Sooh\DB\Error::tableNotExists)){
				$this->db->ensureObj($this->tb, ['autoid'=>'int not null auto_increment','phone'=>'varchar(32)','channel'=>'varchar(16)','msg'=>'varchar(255)'],'autoid',['phone']);
				$this->db->addRecord($this->tb, array('phone'=>$phone,'msg'=>$msg,'channel'=>$channel));
			}else{
				throw $e;
			}
		}
	}
	/**
	 * 获取最新的消息
	 * @param string $phone
	 * @return array 
	 */
	public function recent($phone)
	{
		return $this->db->getRecords($this->tb, 'channel,phone,msg', ['phone'=>$phone],'rsort autoid',10);
	}
}