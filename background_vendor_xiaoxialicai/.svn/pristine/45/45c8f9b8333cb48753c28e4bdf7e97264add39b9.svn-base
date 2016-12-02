<?php
namespace Sooh\DB;
/**
 * Pager，内部pageid从0开始
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class Pager {
	public $page_size=0;
	public $page_count=0;
	public $page_id_zeroBegin=0;
	public $total;
	public $enumPagesize='';
	private $flgZeroBegin=0;
	public $recordsSum=null;
	public function __construct($pagesize,$pagesizes=array(),$zeroBegin=false) {
		$this->flgZeroBegin=$zeroBegin;
		if(empty($pagesizes)){
			$this->enumPagesize = $this->page_size=$pagesize-0;
		}else{
			if(is_array($pagesizes)){
				$this->enumPagesize=implode(',', $pagesizes);
				if(empty($this->page_size)){
					$this->page_size = $pagesize ? : current($pagesizes);
				}
			}else{
				$this->enumPagesize = $pagesizes;
				if(empty($this->page_size)){
					$pagesizes = explode(',', $pagesizes);
					$this->page_size = $pagesize ? : current($pagesizes);
				}
			}
		}
		if(empty($this->page_size)){
			$this->page_size = 10;
		}
	}
	/**
	 * @param int $total
	 * @param int $pageid
	 * @return \Sooh\DB\Pager
	 */
	public function init($total=-1,$pageid=-1)
	{
		if($pageid===-1){
			$pageid = $this->flgZeroBegin?$this->page_id_zeroBegin:$this->page_id_zeroBegin+1;
		}else{
			$this->page_id_zeroBegin = ($this->flgZeroBegin)?$pageid:$pageid-1;
		}
		if($total!==-1){
			$this->total = $total-0;
			if($this->total==0){
				$this->page_count=1;
			}else{
				$this->page_count = ceil($this->total/$this->page_size);
			}
			if($this->flgZeroBegin){
				if($pageid+1>$this->page_count){
					$this->page_id_zeroBegin=0;
				}else{
					$this->page_id_zeroBegin=$pageid-0;
				}
			}else{
				if($pageid>$this->page_count || empty($pageid)){
					$this->page_id_zeroBegin=0;
				}else{
					$this->page_id_zeroBegin=$pageid-1;
				}
			}
		}
		
		return $this;
	}
	
	public function rsFrom()
	{
		return $this->page_id_zeroBegin*$this->page_size;
	}
	
	public function pageid()
	{
		if($this->flgZeroBegin){
			return $this->page_id_zeroBegin;
		}else{
			return $this->page_id_zeroBegin+1;
		}
	}
	
	public function toArray()
	{
		return array(
			'pageId'=>$this->pageid(),
			'total'=>$this->total,
			'pageSize'=>  $this->page_size,
			'pageCount'=>$this->page_count
		);
	}
}
