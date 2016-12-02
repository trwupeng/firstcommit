<?php
include_once __DIR__.'/_one.php';

/**
 * Description of _orders
 *
 * @author wang.ning
 */
class _orders extends \_one{
	protected $lastUniqueOpId='';
	protected $lastOrderId='';
	protected function jsonstrByHttpGet($url) {
//echo "\n【URL】".$url."\n";
		$jsonstr = parent::jsonstrByHttpGet($url);
//echo "【ret】".$jsonstr."\n";
		$tmp = json_decode($jsonstr,true);
		if(isset($tmp['financing']['uniqueOpId'])){
			$this->lastUniqueOpId = $tmp['financing']['uniqueOpId'];
		}
		if(isset($tmp['uniqueOpId'])){
			$this->lastUniqueOpId = $tmp['uniqueOpId'];
		}
		if(isset($tmp['OrdersDone']['ordersId'])){
			$this->lastOrderId = $tmp['OrdersDone']['ordersId'];
		}
		if(isset($tmp['ordersId'])){
			$this->lastOrderId = $tmp['ordersId'];
		}
		if(isset($tmp['orderId'])){
			$this->lastOrderId = $tmp['orderId'];
		}
//echo "【pares】opid:{$this->lastUniqueOpId}; orderid:{$this->lastOrderId}\n";
		return $jsonstr;
	}
	protected function prepareOpIdOnBuyWares($waresId)
	{
		$this->jsonstrByHttpGet($this->getUrl('financing/detail', ['id'=>$waresId,'pageId'=>1,'pageSize'=>1]));
	}
	protected function prepareOrdersId($args)
	{
		$args['cmd']='buy';
		$this->jsonstrByHttpGet($this->getUrl('orders/add', $args));
	}
	
	public function testCorrect()
	{
		$correctArg = $this->getCorrectArgs();
		$this->prepareOpIdOnBuyWares($correctArg['waresId']);
		$this->prepareOrdersId($this->getCorrectArgs());
		parent::testCorrect();

	}
	public function testWrong()
	{
		$correctArg = $this->getCorrectArgs();
		$this->prepareOpIdOnBuyWares($correctArg['waresId']);
		$this->prepareOrdersId($this->getCorrectArgs());
		parent::testWrong();

	}	
}
