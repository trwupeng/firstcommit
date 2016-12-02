<?php
namespace Prj\WaresTpl\Std02;
use Prj\WaresTpl\Std02\Editor;

/**
 * Description of Viewer
 *  房贷模板
 * @author simon.wang
 */
class Viewer extends Editor implements \Prj\WaresTpl\Interfaces\Viewer{
	//put your code here
    private static $o;
    private static $tplPath = ""; //模板路径
   // public $content = "";
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
                if(!empty($v)){
                    $data['c']['ymd'][$k] = date('Y年m月d日',strtotime($v));
                }
            }
        }else{
            if(!empty($data['c']['ymd']))$data['c']['ymd'] = date('Y年m月d日',strtotime($data['c']['ymd']));
        }
        if(is_array($data['c']['price'])){
            foreach($data['c']['price'] as $k=>$v){
                $data['c']['price'][$k] = ($data['c']['price'][$k]/1000000).'万';
            }
        }else{
            $data['c']['price'] = ($data['c']['price']/1000000).'万';
        }
        //姓名脱敏
        $data['b']['name'] = \Prj\IdCard::getCall($data['b']['idCard'],$data['b']['name']);
        if(!empty($data['d'])){
            foreach($data['d'] as $k=>$v){
                if(empty($v['name'])){
                    //$data['d'][$k]['name'] = '小周';
                }
            }
        }
        return $data;
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

}
