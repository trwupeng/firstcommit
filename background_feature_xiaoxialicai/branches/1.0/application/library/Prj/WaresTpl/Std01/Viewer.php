<?php
namespace Prj\WaresTpl\Std01;
/**
 * Description of Viewer
 *
 * @author simon.wang
 */
class Viewer implements \Prj\WaresTpl\Interfaces\Viewer{
	//put your code here
    private static $o;
    private static $tplPath = ""; //模板路径
    public $content = "";
    public $htmlArr ;
    public $tabs;
    public static function getCopy($content=array())
    {
        if(!self::$o)self::$o = new Viewer($content);
        return self::$o;
    }

    //解析内容
    public static function decode($content=array(),$waresId)
    {
        $arr = [];
        $arr['a'] = 'http://'.$_SERVER['HTTP_HOST'].\Sooh\Base\Tools::uri(array('waresId'=>$waresId),'dec','financing');
        $arr['b'] = is_array($content['b'])?$content['b']:[];
        return $arr;
    }

    private function __construct($content)
    {
        $allTabs = Editor::getAllTabs();
        foreach($content as $k=>$v)
        {
            if(empty($v))
            {
                unset($content[$k]);
                unset($allTabs[$k]);
            }
        }
        $this->content = $content;
        $this->htmlArr = $this->getHtmlArr();
        $this->tabs = $allTabs;
        $this->tabsHtmlArr = $this->getTabsHtmlArr();
    }
    protected function getHtmlArr()
    {
        $arr = array();
        $num = 0;
        foreach($this->content as $k=>$v)
        {
            $num++;
            $html = "<div class='i-d-t-con i-d-t-con-".$num."'>".$v."</div>";
            $arr[$k] = $html;
        }
        return $arr;
    }
    protected function getTabsHtmlArr()
    {
        $arr =array();
        $num = 0;
        foreach($this->tabs as $k=>$v)
        {
            $num++;
            $html = "<li class='i-d-tabs-nav-$num' _index='$num'>$v</li>";
            $arr[$k] = $html;
        }
        return $arr;
    }
    public function getTabsHtml()
    {
        $str = "<div class='i-d-tabs-nav'><ul>".implode('',$this->tabsHtmlArr)."</ul></div>";

        return $str;
    }
    public function getHtml()
    {
        $str = "<div class='i-d-tabs-con'>".implode('',$this->htmlArr)."</div>";
        return $str;
    }


}
