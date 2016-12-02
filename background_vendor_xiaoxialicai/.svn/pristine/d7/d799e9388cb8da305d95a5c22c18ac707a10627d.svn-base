<?php
namespace Sooh\DB\Acl;
/**
 * Menu
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class Menu {
	public $capt;
	public $url;
	public $options;
	/**
	 * 构造方法
	 * @param type $capt
	 * @param type $url
	 * @param array $options
	 * @return \Sooh\DB\Acl\Menu
	 */
	public static function factory($capt,$url=null,$options=null)
	{
		$o = new Menu;
		$o->capt = $capt;
		$o->url=$url;
		$o->options=$options;
		return $o;
	}
	/**
	 * 
	 * @param string|\Sooh\DB\Acl\Menu $capt
	 * @param string $url
	 * @param array $options
	 * @return \Sooh\DB\Acl\Menu currentMenu not the one added
	 */
	public function addChild($capt,$url=null,$options=null)
	{
		if(is_string($capt)){
			$this->children[]=static::factory($capt,$url,$options);
		}else{
			$this->children[]=$capt;
		}
		return $this;
	}
	public $children=array();
}
