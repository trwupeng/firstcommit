<?php
/**
 * 券的使用限制
 * 格式 shelf.tag=起投金额
 * 
 * @author simon.wang
 */
class Broker {
	public $amountMin=0;
	public $shelf=0;
	/**
	 * 解析投资投资限制
	 * @param string $str
	 */
	public function __construct($str) {
		;
	}
	/**
	 * 获取标的的shelfId和tags对应的最低投资额
	 * 
	 * @param string $shelfId 
	 * @param string $tags 英文逗号分隔的标签
	 * @return int 投资额，0表示该标的不能使用此券
	 */
	public function minInvestment($shelfId,$tags)
	{
		
	}
	
	public function toString()
	{
		
	}
}
