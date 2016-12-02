<?php
namespace Sooh\Base\Interfaces;
/**
 * Description of ActionDumper
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class ActionDumper {
	public function initLoaderDir($arr_NamespaceRoot_dir)
	{
		$this->arr_NamespaceRoot_dir = $arr_NamespaceRoot_dir;
		spl_autoload_register(array($this,'_loader')); 
		return $this;
	}
	protected $arr_NamespaceRoot_dir;
	public function _loader($class)
	{
		$r = explode('\\', $class);
		if($r[0]=='\\'){
			array_shift($r);
		}
		if(sizeof($r)==1){
			return false;
		}else{
			if(isset($this->arr_NamespaceRoot_dir[ $r[0] ])){
				$dir0 = $this->arr_NamespaceRoot_dir[ $r[0] ];
				include $dir0.'/'.implode('/', $r).'.php';
				return true;
			}else {
				return false;
			}
		}
	}
	public function scandir($dirs,$dumpTo=null)
	{
		return $this;
	}
	
	public function dumpOne($fullname,$moduleName='')
	{
		$fullname = str_replace('\\', '/', $fullname);
		$r = explode('/', $fullname);
		$ctrlName = substr(array_pop($r),0,-4);
		if('Controller'==substr($ctrlName,-10)){
			$ctrlName = substr($ctrlName,0,-10);
		}
		if(!class_exists($ctrlName.'Controller')){
			include $fullname;
		}
		
		
		$rc = new \ReflectionClass($ctrlName.'Controller');
		$methods = $rc->getMethods();
		$docOfClass = $this->parseDoc($rc->getDocComment());
		$docOfClass->module=$moduleName;
		$docOfClass->controller = lcfirst($ctrlName);
		$ret = array($docOfClass);
		
		foreach($methods as $f){
			if($f->class==$ctrlName.'Controller'){
				if(($f->getModifiers() & \ReflectionMethod::IS_PUBLIC)>0){
					if(substr($f->name,-6)=='Action'){
						
						$docOfClass = $f->getDocComment();
						$o = $this->parseDoc($docOfClass);
						$o->module = $moduleName;
						$o->controller = lcfirst($ctrlName);
						$o->action = substr($f->name,0,-6);
						$ret[] = $o;
					}
				}
			}
		}
		
		return $ret;
	}
	
	protected function parseDoc($str)
	{
		$doc = new \Sooh\Base\Interfaces\ActionDoc('doc missing');
		
		$lines = explode("\n", $str);
		array_shift($lines);
		array_pop($lines);
		$lastID = 'doc';
		$lastStr='';
		foreach ($lines as $line){
			$line = trim($line);
			if($line[0]=='*'){
				$line = trim(substr($line,1));
			}

			$cmp=trim($line);
			if($cmp[0]!=='@'){
				$lastStr.=$line."\n";
			}else{
				switch($lastID){
					case 'doc':
						$doc->doc = $lastStr;
						break;
					case 'input':
						$tmp = explode(" ", $lastStr);
						$type = array_shift($tmp);
						$varname = array_shift($tmp);
						$tmp = implode(' ', $tmp);
						$doc->inputs[]=['type'=>$type,'name'=>$varname,'desc'=>$tmp];
						break;
					case 'output':
						$doc->output=$lastStr;
						break;
					case 'errors':
						$doc->errors[]=$lastStr;
						break;
				}
				$line = str_replace("\t", ' ', $line);
				$pos = strpos($line, ' ');
				$lastID=substr($line,1,$pos-1);
				$lastStr=substr($line,$pos+1);
			}
			
		}
		switch($lastID){
			case 'doc':
				$doc->doc = $lastStr;
				break;
			case 'input':
				$tmp = explode(" ", $lastStr);
				$type = array_shift($tmp);
				$varname = array_shift($tmp);
				$tmp = implode(' ', $tmp);
				$doc->inputs[]=['type'=>$type,'name'=>$varname,'desc'=>$tmp];
				break;
			case 'output':
				$doc->output=$lastStr;
				break;
			case 'errors':
				$doc->errors[]=$lastStr;
				break;
		}
		return $doc;
	}
	
	protected function htmlHead()
	{
		return <<<EOT
<!DOCTYPE html>
<html lang="zh-CN">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- 上述3个meta标签*必须*放在最前面，任何其他内容都*必须*跟随其后！ -->
    <title>Bootstrap 101 Template</title>
	<link rel="stylesheet" href="//cdn.bootcss.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <link rel="stylesheet" href="//cdn.bootcss.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">
	<link rel="stylesheet" href="css/bootstrapSwitch.css">

	<!--<script src="http://v3.bootcss.com/assets/js/docs.min.js"></script>-->
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="//cdn.bootcss.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="//cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
	<!--  http://v3.bootcss.com/examples/theme/#  -->
  </head>
  <body>
EOT;
	}

	protected function htmlFoot()
	{
		return <<<EOT
<!-- Include all compiled plugins (below), or include individual files as needed -->
		<script src="http://cdn.bootcss.com/jquery/1.11.3/jquery.min.js"></script>
		<script src="http://cdn.bootcss.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
		<script src="js/bootstrapSwitch.js"></script>
	</body>
</html>
EOT;
	}
	protected function htmlBox($ctrlName,$ctrlDoc,$actions)
	{
		$str = "<div class=\"panel panel-primary\">\n
	<div class=\"panel-heading\">\n
		 <h3 class=\"panel-title\" onclick=\"$(\'#ctrl_$ctrlName\').toggle()\">$ctrlDoc</h3>\n
	</div>\n
	<table id=\"ctrl_$ctrlName\" class=\"table\">\n
		<tbody>\n";

		foreach($actions as $r){
			$str .="<tr><td>POST - ctrl/action<br>intro</td><td>inputs</td><td>return</td></tr>";
		}
		$str.="		</tbody>\n
	</table>\n
</div>\n";

	}
}
