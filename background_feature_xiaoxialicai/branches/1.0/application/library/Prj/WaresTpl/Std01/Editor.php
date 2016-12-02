<?php
namespace Prj\WaresTpl\Std01;
/**
 * Description of Editor
 *
 * @author simon.wang
 */
class Editor
{
    protected static $_allTabs = array(
        'a' => '投资产品详情',
        'b' => '安全保障',
    );
    public $htmlArr;
    public static $content;
    private static $o;
    private function __construct($arr = array())
    {
        if(!empty($arr))
        {
            self::$content = is_array($arr) ? $arr : json_decode($arr);
        }
    }
    public static function getAllTabs()
    {
        return self::$_allTabs;
    }

    public static function getCopy($arr = array())
    {
        if(!self::$o)self::$o = new Editor($arr);
        return self::$o;
    }

    public function inputShow()
    {
        self::$content = self::encode(self::$content);
        $arr = array();
        $uploadUrl = \Sooh\Base\Tools::uri(null,'upload');
        foreach (self::$_allTabs as $k => $v) {
            $str = '<label for="j_custom_'.$k.'" class="control-label x85">'.$v.'</label>' .
                '<div style="display: inline-block; vertical-align: middle;">' .
                '<textarea name="introDisplay['.$k.']" id="j_form_'.$k.'" class="j-content"  style="width: 700px;"  data-toggle="kindeditor" data-minheight="200" data-upload-json="'.$uploadUrl.'">'.
                self::$content[$k].
                '</textarea>'.
                '</div>';
            $arr[] = $str;
        }
        $this->htmlArr = $arr;
        $str = implode('<br><br>',$arr);
        return $str;
    }

    public static function decode($arr=[])
    {
        preg_match_all('/<p>[\s\S]*?<\/p>/',$arr['b'],$temp);
        foreach($temp[0] as $v)
        {
            $v = strip_tags($v);
            $v = preg_replace('/[\s]*/','',$v);
            $newB[] = $v;
        }
        if(!empty($newB))
        {
            foreach($newB as $k=>$v)
            {
                if($k%2==0)
                {
                    $newBB[] = [
                        'title'=>$v,
                        'content'=>$newB[$k+1]?$newB[$k+1]:'',
                    ];
                }
            }
        }
        $arr['b'] = $newBB;
        return $arr;
    }

    public static function encode($arr=[])
    {
        $new = '';
        if(!empty($arr['b']))
        {
            foreach($arr['b'] as $v)
            {
                $new.=('<p>'.$v['title'].'</p><p>'.$v['content'].'</p>');
            }
        }
        $arr['b'] = $new;
        return $arr;
    }
}
