<?php
namespace SoohYaf;
/**
 * 扩展的支持模板选择的view
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class Viewext extends \Yaf_View_Simple{
	/**
	 * 
	 */
	public static $jqueryVer='jquery-1.11.2.min.js';
	/**
	 * 输出数据使用的模板类型
	 * @var string
	 */
	public static $renderType='html';
	/**
	 * 是否禁用默认的HTML头和尾
	 * @var boolean
	 */
	public static $bodyonly=false;
	private $renderStart=false;
	private function preRender()
	{
		if($this->renderStart==false){
			$request = \Yaf_Application::app()->getDispatcher()->getRequest();
			$this->renderStart=true;
			$cs = $request->get('__preRender__');
			if(!empty($cs)){
				$cs = explode(',',$cs);
				foreach($cs as $c){
					if(!class_exists($c,false)){
						include APP_PATH.'/application/plugins/preRender/'.ucfirst($c).'.php';
					}
					$c::run($this,$request,null);
				}
			}
		}
	}
	public function render ( $strTpl , $arrTplVars=null)
	{
		$this->preRender();
		$renderType=\Sooh\Base\Ini::getInstance()->viewRenderType();
		if($renderType=='json'){
			$ret = $this->toJson();
		}elseif($renderType=='jsonp'){
			$ret = $this->toJson();
			$ret = \Sooh\Base\Ini::getInstance()->get('nameJsonP').'('.$ret.')';
		}elseif($renderType=='echo'){
			$ret='';
		}else{
			$ret = parent::render($this->fixTplPath($strTpl) , $arrTplVars);
		}
		return $ret;
	}
	protected function toJson()
	{
		$tmp = array();
		foreach($this->_tpl_vars as $k1=>$v1){
			if(is_array($v1)){
				foreach($v1 as $k2=>$v2){
					if(is_array($v2)){
						foreach($v2 as $k3=>$v3){
							if(is_array($v3)){
								$tmp[$k1][$k2][$k3]=$v3;
							}else{
								if(is_object($v3) && method_exists($v3, 'toArray')){
									$tmp[$k1][$k2][$k3]=$v3->toArray();
								}else{
									$tmp[$k1][$k2][$k3]=$v3;
								}
							}
						}
					}else{
						if(is_object($v2) && method_exists($v2, 'toArray')){
							$tmp[$k1][$k2]=$v2->toArray();
						}else{
							$tmp[$k1][$k2]=$v2;
						}
					}
				}
			}else{
				if(is_object($v1) && method_exists($v1, 'toArray')){
					$tmp[$k1]=$v1->toArray();
				}else{
					$tmp[$k1]=$v1;
				}
			}
		}
		return json_encode($tmp);
	}
	protected function fixTplPath($strTpl)
	{
		$renderType=\Sooh\Base\Ini::getInstance()->viewRenderType();
		switch ($renderType){
			case 'html':return str_replace('.phtml', '.www.phtml', $strTpl);
			case 'wap':return str_replace('.phtml', '.wap.phtml', $strTpl);
			case 'echo':return;
			case 'json':return ;
		}
	}
	public function display (  $strTpl , $tpl_vars =array() )
	{
		return parent::display($this->fixTplPath($strTpl),$tpl_vars);
	}
	
	public function getScriptPath()
	{
		return $this->fixTplPath(parent::getScriptPath());
	}
	
	public function setScriptPath ( $strTpl )
	{
		return parent::setScriptPath($this->fixTplPath($strTpl));
	}
	/**
	 * 渲染输出inc目录下的指定文件
	 * @param string $part
	 * @return string
	 */
	public function renderInc($part)
	{
		return $this->render(VIW_INC_PATH.$part.'.phtml');
	}
	protected $headParts=array();
	/**
	 * 追加html的head部分的内容
	 * @param string $str
	 * @return string
	 */
	public function htmlHeadPart($str=null)
	{
		if($str==null){
			return $this->headParts;
		}else{
			$this->headParts[]=$str;
		}
	}
}
