<?php
namespace Prj\WaresTpl\Std05;
/**
 * Description of Viewer
 *  车贷模板
 * @author simon.wang
 */
class Viewer extends Editor{
	//put your code here
    private static $o;
    private static $tplPath = ""; //模板路径
    public static $content = "";
    public $htmlArr ;
    public $tabs;
    public static function getCopy($content=array())
    {
        if(!self::$o)self::$o = new Viewer($content);
        return self::$o;
    }

    public static function format($data){
        if(is_array($data['c']['ymd'])){
            foreach($data['c']['ymd'] as $k=>$v){
                if(!empty($data['c']['ymd'][$k]))$data['c']['ymd'][$k] = date('Y年m月d日',strtotime($v));
            }
        }else{
            if(!empty($data['c']['ymd']))$data['c']['ymd'] = date('Y年m月d日',strtotime($data['c']['ymd']));
        }


        $data['b']['idCard'] = substr_replace($data['b']['idCard'],'********',6,8);

        if(is_array($data['c']['price'])){
            foreach($data['c']['price'] as $k=>$v){
                $data['c']['price'][$k] = ($data['c']['price'][$k]/1000000).'万';
            }
        }else{
            $data['c']['price'] = ($data['c']['price']/1000000).'万';
        }

        if(is_array($data['c']['buy'])){
            foreach($data['c']['buy'] as $k=>$v){
                $data['c']['buy'][$k] = ($data['c']['buy'][$k]/1000000).'万';
            }
        }else{
            $data['c']['buy'] = ($data['c']['buy']/1000000).'万';
        }

        if(is_array($data['c']['meter'])){
            foreach($data['c']['meter'] as $k=>$v){
                $data['c']['meter'][$k] = ($data['c']['meter'][$k]).'km';
            }
        }else{
            $data['c']['meter'] = ($data['c']['meter']).'km';
        }

        if(!empty($data['d'])){
            foreach($data['d'] as $k=>$v){
                if(empty($v['name'])){
                   // $data['d'][$k]['name'] = '小周';
                }
            }
        }
        return $data;
    }

    //解析内容
    public static function decode($content=array(),$waresId)
    {
        $arr = [];
        $arr['a'] = 'http://'.$_SERVER['HTTP_HOST'].\Sooh\Base\Tools::uri(array('waresId'=>$waresId),'dec','financing');
        $arr['b'] = is_array($content['b'])?$content['b']:[];
        return $arr;
    }

    public static function getImgList($content){
        $num = 0;
       if(!empty($content['d'])){
           foreach($content['d'] as $k=>$v){
               //unset($content['d'][$k]['name']);
               if(!empty($content['d'][$k]['img'])){
                   foreach($content['d'][$k]['img'] as $kk=>$vv){
                       $content['d'][$k]['img'][$kk]['index'] = $num;
                       $content['d'][$k]['img'][$kk]['type'] = $k;
                       $content['d'][$k]['img'][$kk]['urlFull'] = \Prj\Wares\Img::getImgUrl($content['d'][$k]['img'][$kk]['url']);
                       $num++;
                   }
               }
               $content['d'][$k] = $content['d'][$k]['img'];
           }
           return $content['d'];
       }else{
           $tmp = Editor::$contentStructure['d'];
           array_walk($tmp,function(&$v,$k){
               $v = [];
           });
           return $tmp;
       }

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
