<?php
namespace Sooh\DB\Base;
/**
 * @author Simon Wang <sooh_simon@163.com> 
 */
class Where 
	implements \Sooh\DB\Interfaces\Where
{
	public function append($k,$v=null,$ifRealAppend='throwError')
	{
		if($this->dbClass==null)throw new \ErrorException('thisWhere ended already');
		if($ifRealAppend===false)return $this;
		$bakTb = $this->dbClass->_tmpObj($this->forTable);
		if(empty($v) && is_array($v)){
			if($ifRealAppend==='markEmptyArray'){
				$this->_emptyWhere[]=$k;
				return $this;
			}else {
				$err = new \ErrorException('empty Array was Found when build where');
				error_log($err->getMessage()."\n".$err->getTraceAsString());
				throw $err;
			}
		}
		if (is_array($k)){
			foreach($k as $i=>$v){
				if(is_numeric($i))$this->append (null, $v);
				else $this->append ($i, $v);
			}
		}elseif (is_null($k)){
			if(is_scalar($v)){
				$err= new \ErrorException;
				error_log("should avoid:where->append(null,'sql-statement')\n".$err->getTraceAsString());
				$this->r[]=$v;
			}else{
				$tmp = trim($v->end());
				if(!empty($tmp))$tmp = '('.substr($tmp,6).')';
				$this->r[]=$tmp;
			}
		}else{
			$this->r[]=$this->conv($k,$v);
		}
		$this->dbClass->_tmpObj($bakTb);
		return $this;
	}
	protected $_emptyWhere=array();
	public function hasEmptyArray()
	{
		$ret = !empty($this->_emptyWhere);
		$this->_emptyWhere=array();
		return $ret;
	}
	protected function conv($k,$v)
	{
		//TODO: mysql 不论数字还是字符串都可以用''包起来，所以简化了逻辑
		//碰上不能如此的，需要找其他的方式来确定是否要用'', 
		//		a)：是否根据变量名（比如首字母是n还是s）
		//		b)根据数据库表名，查定义？？
		$k0 = substr($k,0,-1);
		switch (substr($k,-1)){
			case "!": 
				if (!is_array($v)){
					if (is_null($v))
						$where = $k0 ." is not null";
					else 
						$where = $k0 ."<>" . $this->dbClass->_safe($k0,$v);
				}else{
					foreach ($v as $i=>$tmp) $v[$i] = $this->dbClass->_safe($k0,$tmp);
					$where = $k0 . " NOT IN (" . implode(',', $v) .")";
				}
				break; 
			case ">":
			case "<": 
				$where = $k .$this->dbClass->_safe($k,$v);
				break;
			case "]":
				$where = $k0 . ">=".$this->dbClass->_safe($k0,$v);
				break;
			case "[": 
				$where = $k0 . "<=".$this->dbClass->_safe($k0,$v);
				break;
			case "*": 
				$where = $k0 . " like " . $this->dbClass->_safe($k0,str_replace('*','%',$v));
				break;
			
			case "=":		
				$k = substr($k,0,-1);
			default:
				if (!is_array($v)){
					if (is_null($v))$where = $k . " is null";
					else $where = $k . "=".$this->dbClass->_safe($k,$v);
				}else{
					foreach ($v as $i=>$tmp) $v[$i] = $this->dbClass->_safe($k,$tmp);
					$where = $k . " IN (" . implode(',', $v) .")";
				}
				break;
		}
		return $where;
	}
	
	protected $forTable;
	/**
	 *
	 * @var tea_db_mysql
	 */
	protected $dbClass;	
	public function _HandleAutoSet($h)
	{
		$this->dbClass=$h;
	}
	public function init($base='AND',$forObj=null){
		$this->method=  strtoupper($base);
		$this->forTable=$forObj;
		return $this;
	}
	

	public function end()
	{
		if($this->dbClass==null)throw new \ErrorException('thisWhere ended already');
		$this->dbClass->_whereDone($this->sn);
		$this->dbClass=null;
		if(empty($this->r))	return '';
		return ' where '.implode(' '.$this->method.' ', $this->r);
	}
	public function abandon()
	{
		if($this->dbClass==null)throw new \ErrorException('thisWhere ended already');
		$this->dbClass->_whereDone($this->sn);
		$this->dbClass=null;
	}
	protected $r;
	protected $method='AND';
	protected $sn;
	public function __construct($sn) 
	{
		$this->sn=$sn;
	}
}