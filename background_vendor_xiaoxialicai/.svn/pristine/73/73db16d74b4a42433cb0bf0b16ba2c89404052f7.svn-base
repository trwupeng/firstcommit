<?php
namespace Sooh\Base\Tests;
/**
 * 测试炸弹
 *	丢异常的方式触发
 *		\Sooh\Base\Tests::blowup('bomb-identifier',true);
 *	检查返回值的方式
 *		\Sooh\Base\Tests::blowup('bomb-identifier',false);
 * 
 *  收集炸弹的方式：
 *		1)index.php开头加上\Sooh\Base\Tests::$flg='m/c/a';
 *		2)index.php最后加上\Sooh\Base\Tests::onShutdown();
 *		3)访问接口
 *		4)查看php_error.log
 * @author simon.wang
 */
class Bomb {
	/**
	 * 标志位，0：禁用，1，启用，其他：请求的route,用于记录追踪
	 * @var mix 
	 */
	public static $flg=0;
	protected static $list=array();
	/**
	 * 触发炸弹
	 * @param string $identifier 标示
	 * @param bool $throwError 是以抛出异常的方式处理爆炸吗
	 * @return boolean 是否爆炸
	 * @throws \ErrorException
	 */
	public static function blowup($identifier,$throwError=true)
	{
		if(self::$flg===0){
			return false;
		}else{
			
			if(self::$flg!==1){
				self::$list[]=$identifier;
			}

			if(self::$flg && $_REQUEST['__testBomb__']==$identifier){
				if($throwError){
					throw new \ErrorException('testBomb_blowup');
				}else{
					return true;
				}
			}
		}
	}
	
	public static function onShutdown()
	{
		if(is_string(self::$flg)){
			error_log("bomb-list:".self::$flg.":".  implode(',', self::$list));
		}
	}
}
