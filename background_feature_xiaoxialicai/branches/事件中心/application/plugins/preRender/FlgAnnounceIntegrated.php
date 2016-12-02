<?php
/**
 * @param  公告系统是否集成到平台的消息系统中
 *
 * @author wupeng
 *
 * */
class FlgAnnounceIntegrated{
    public function run($view,$request,$response=null){
        $view->assign('FlgAnnounceIntegrated',1);
    }
}
