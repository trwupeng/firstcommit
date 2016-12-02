<?php
namespace Prj\Misc;

/**
 * 复制图片到cdn
 * http://photo.xiaoxialicai.com/app/【wares,banners,misc】/xxx.png
 * @author wang.ning
 */
class Cp2cdn {
	/**
	 * 复制banner图片
	 * @param string $srcFile 完整的路径名
	 * @param string $dstName [可选] 目标文件名（app/banners/之后的部分），null的话，就使用原文件名
	 * @return string 使用的完整的图片地址，http协议的地址
	 */
	public static function banner($srcFile,$dstName=null)
	{
		if($dstName===null){
			$dstName = '/var/www/cms/uploadfile/app/banners/'.basename($srcFile);
		}else{
			$dstName = '/var/www/cms/uploadfile/app/banners/'.$dstName;
		}
		self::scp($srcFile, $dstName);
		$f = basename($dstName);
		return 'http://res.xiaoxialicai.com/app/banners/'.$f;
	}
	/**
	 * 复制其他未分类图片
	 * @param string $srcFile 完整的路径名
	 * @param string $dstName [可选] 目标文件名（app/misc/之后的部分），null的话，就使用原文件名
	 * @return string 使用的完整的图片地址，http协议的地址
	 */	
	public static function misc($srcFile,$dstName=null)
	{
		if($dstName===null){
			$dstName = '/var/www/cms/uploadfile/app/misc/'.basename($srcFile);
		}else{
			$dstName = '/var/www/cms/uploadfile/app/misc/'.$dstName;
		}
		self::scp($srcFile, $dstName);
		$f = basename($dstName);
		return 'http://res.xiaoxialicai.com/app/misc/'.$f;
	}
	/**
	 * 复制标的用的图片
	 * @param string $srcFile 完整的路径名
	 * @param string $dstName [可选] 目标文件名（app/wares/之后的部分），null的话，就使用原文件名
	 * @return string 使用的完整的图片地址，http协议的地址
	 */		
	public static function wares($srcFile,$dstName=null)
	{
		if($dstName===null){
			$dstName = '/var/www/cms/uploadfile/app/wares/'.basename($srcFile);
		}else{
			$dstName = '/var/www/cms/uploadfile/app/wares/'.$dstName;
		}
		self::scp($srcFile, $dstName);
		$f = basename($dstName);
		return 'http://res.xiaoxialicai.com/app/wares/'.$f;
	}

	protected static function scp($src,  $targetName)
	{
		if(\Sooh\Base\Ini::getInstance()->get('deploymentCode')<40){
			$host = '10.251.54.88';
		}else{
			$host = '121.42.166.81';
		}
		$cmd = "scp $src cmsuser@{$host}:$targetName";
		error_log($cmd);
		$lastline = exec($cmd);
		return $lastline;
	}
}
