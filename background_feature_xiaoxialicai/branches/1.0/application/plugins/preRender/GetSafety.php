<?php
/**
 * @param  获取安全保障
 *
 * @author wupeng
 *
 * */
class GetSafety{
    public function run($view,$request,$response=null)
    {
        $safety = \Prj\Data\Config::get('safety');
        if(empty($safety))$safety = [];
        $view->assign('safety',$safety);
    }
}
