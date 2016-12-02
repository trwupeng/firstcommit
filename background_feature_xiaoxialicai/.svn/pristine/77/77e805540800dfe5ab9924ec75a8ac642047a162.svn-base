<?php
/**
 * Created by PhpStorm.
 * User: tang.gaohang
 * Date: 2016/4/16
 * Time: 13:42
 */
namespace Prj\WaresTpl;

class Base {

    protected static $class;
    public $htmlArr;
    public static $content;
    private static $o;
    private function __construct($arr = array())
    {
        if(!empty($arr))
        {
            static::$content = is_array($arr) ? $arr : json_decode($arr);
        }
    }

    public static function getAllTabs()
    {
        return static::$_allTabs;
    }

    public static function getCopy($arr = array())
    {
        return new static($arr);
    }

    public function setAssessor($name){
        if(static::$content['d']){
            foreach(static::$content['d'] as $k=>$v){
                if(!empty($v['img']))static::$content['d'][$k]['name'] = $name;
            }
        }
        return $this;
    }

    public function outPut(){
        return static::$content;
    }

    public static $validate = [
        "idCard"=>"ID_card",
        "price"=>"number",
        "meter"=>"number",
    ];

    public static function getValidate($str){
        return static::$validate[$str].',required';
    }

    public function inputShow()
    {
        $this->contentOut();
        $style = "<style>.inputShow{float: left;padding: 0 40px 0 40px;}</style>";
        $str = $style.'<hr/><div style="background-color: deepskyblue"><div class="inputShow">'.$this->echoInput(static::$_allTabs).'</div></div>';
        $js = <<<html
    <script>
        $('.img_close').live('click',function(){
            $(this).parent().remove();
        });
        var doDelete = function(e){
            //$(e).attr();
            return false;
        }
        var lookImg = function(e){
        /*
            var url  = $(e).attr('src');
            window.open(url);*/
            return false;
        }
    </script>
html;
        $str.=$js;


        return $str;
        /*
        static::$content = static::encode(static::$content);
        $arr = array();
        $uploadUrl = \Sooh\Base\Tools::uri(null,'upload');
        foreach (static::$_allTabs as $k => $v) {
            $str = '<label for="j_custom_'.$k.'" class="control-label x85">'.$v.'</label>' .
                '<div style="display: inline-block; vertical-align: middle;">' .
                '<textarea name="introDisplay['.$k.']" id="j_form_'.$k.'" class="j-content"  style="width: 700px;"  data-toggle="kindeditor" data-minheight="200" data-upload-json="'.$uploadUrl.'">'.
                static::$content[$k].
                '</textarea>'.
                '</div>';
            $arr[] = $str;
        }
        $this->htmlArr = $arr;
        $str = implode('<br><br>',$arr);
        return $str;
        */
    }

    public static function getDataFromForm($ext){
        var_log($ext,'ext>>>');
        static::$content =  static::$contentStructure;
        foreach($ext as $k=>$v){
            //if(empty($v))continue;
            $tmp = explode('_',$k);
            if($tmp[2]=='img'){
                if($tmp[3]=='url'){
                    if(!empty($v)){
                        foreach($v as $kk=>$vv){
                            static::$content[$tmp[0]][$tmp[1]][$tmp[2]][$kk][$tmp[3]] = $vv;
                        }
                    }
                }else if($tmp[3]=='desc'){
                    if(!empty($v)){
                        foreach($v as $kk=>$vv){
                            static::$content[$tmp[0]][$tmp[1]][$tmp[2]][$kk][$tmp[3]] = $vv;
                        }
                    }

                }
                continue;
            }
            switch(count($tmp)){
                case 3: static::$content[$tmp[0]][$tmp[1]][$tmp[2]] = $v;break;
                case 2: static::$content[$tmp[0]][$tmp[1]] = $v;break;
                case 1: static::$content[$tmp[0]] = $v;break;
            }
        }
        $edit = new static();
        $edit->contentIn();
        return static::$content;
    }

    public function echoInput($arr,$key = ''){
        $str = '';
        $key = $key?($key.'_'):$key;
        foreach($arr as $k=>$v){
            if(in_array($k,['b','c','d'])){
                $str.='</div><div class="inputShow">';
            }
            $newKey = $key.$k;
            $nameKey = 'ext['.$newKey.']';
            //var_log($newKey,'key>>>');
            if(!is_array($v)){
                //输出
                //var_log($newKey,'key>>>>');
                if($v=='审核员'){
                    if(static::getValueFromStrKey($newKey))$str.= '审核员:<input data-rule="'.static::getValidate($k).'" name="'.$nameKey.'" id="j_form_'.$v.'"  value="'.static::getValueFromStrKey($newKey).'"  readonly /><div style="height:5px"></div>';
                    continue;
                }

                $str.= '<label for="j_custom_'.$v.'" class="control-label x85">'.$v.'</label>';
                if(in_array($k,['a','reason'])) {
                    $str .= '<textarea name="' . $nameKey . '" id="j_form_' . $v . '" >'.static::getValueFromStrKey($newKey).'</textarea><div style="height:5px"></div>';
                }elseif(in_array($k,['img'])) {
                    $imgArr = static::getValueFromStrKey($newKey);
                    $nameKey = 'ext[' . $newKey . '_url][]';
                    $descKey = 'ext[' . $newKey . '_desc][]';
                    //显示图片
                    $editStr = '';
                    if(!empty($imgArr)){
                        foreach($imgArr as $k=>$v){
                            $editStr.= '<div><button class="img_close" onclick="return doDelete(this)">删除</button><img data-url="'.\Sooh\Base\Tools::uri(['fileId'=>$v['url']],'getImageHtml','wares').'" data-title="图片查看" data-id="lookImg" data-toggle="navtab" onclick="return lookImg(this)"  style="cursor: pointer" src="'.\Sooh\Base\Tools::uri(['fileId'=>$v['url']], 'getImage', 'public', 'index').'" /><br/>';
                            $editStr.= '<input style="border:none" name="'.$nameKey.'" value="'.$v['url'].'" readonly /><br/>';
                            $editStr.= '描述:<input name="'.$descKey.'" value="'.$v['desc'].'" /><br/></div>';
                        }
                    }

                    $str .= '<div style="display: inline" data-toggle="upload" data-on-upload-success="uploadCallback_' . $newKey . '" data-uploader="' . \Sooh\Base\Tools::uri([], 'newUpload') . '"></div>';
                    //$str.='<input style="width:500px" name="'.$nameKey.'" readonly />';
                    $str .= '<div class="' . $newKey . '" >';
                    $str .= $editStr;
                    $str.='</div>';
                    $imgUrl = \Sooh\Base\Tools::uri([], 'getImage', 'public', 'index');
                    $js = <<<html
                    <style>.inputShow img{max-width: 400px;}</style>
    <script>
        var uploadCallback_$newKey = function(file, data, element){
            var obj = eval('(' + data + ')');
            if(obj.statusCode!=200){
                alert(obj.message);
                return;
            }
            var str = '<div><button class="img_close" onclick="return doDelete()">删除</button><img src="{$imgUrl}fileId='+obj.fileId+'" /><br/>';
            str+='图片ID:<input style="border:none" name="{$nameKey}" value="'+obj.fileId+'" readonly /><br/>';
            str+='描述:<input name="{$descKey}" /><br/></div>';
           $('.$newKey').append(str);

            return;
        }
    </script>
html;
                    $str .= $js;
                    //var_log($str,'str>>>');
                }elseif(in_array($k,['ymd'])){
                    $str.= '<input name="'.$nameKey.'" id="j_form_'.$v.'" data-toggle="datepicker"  value="'.static::getValueFromStrKey($newKey).'"/><div style="height:5px"></div>';
                }else{
                    $str.= '<input data-rule="'.static::getValidate($k).'" name="'.$nameKey.'" id="j_form_'.$v.'"  value="'.static::getValueFromStrKey($newKey).'"/><div style="height:5px"></div>';
                }

            }else{
                if($v['title'])$str.='<div>'.$v['title'].'</div>';
                $str.=$this->echoInput($v['fields'],$newKey);
            }
        }
        return $str;
    }

    public static function getValueFromStrKey($str){
        $arr = explode('_',$str);
        switch(count($arr)){
            case 1:return static::$content[$arr[0]];
            case 2:return static::$content[$arr[0]][$arr[1]];
            case 3:return static::$content[$arr[0]][$arr[1]][$arr[2]];
        }
        return '';
    }
}