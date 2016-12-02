<?php
/**
 * 用户成功注册以后做哪些任务
 * @author wang.ning
 */
trait TraitRegister
{
    public function run($data)
    {
        error_log("###EVT###" . __CLASS__);
        $this->notifyRptCenter();
        $this->addNewestPosts($data->userId);
        //		\Lib\Services\Bysms::getInstance(\Prj\BaseCtrl::getRpcDefault('Bysms'))
        //							->sendCode( '130123456789', __CLASS__.'随文');
    }

    /**
     * 抓取最近的公告入库
     */
    protected function addNewestPosts($userId)
    {
        $ret = \Lib\Services\Poststransfer::getRecent();
        //		error_log("URL##".$url);
        //		error_log("RET##".$ret);
        //		error_log("todo: new user  addd newst posts");
        //先初始化一下站内信的控制类
        $rpc = \Prj\BaseCtrl::getRpcDefault('Message');
        \Lib\Services\Message::getInstance($rpc);
        if (!empty($ret)) {
            foreach ($ret as $r) {
                \Lib\Services\Poststransfer::sendToUserOneMsg($userId, $r['title'], $r['content'], $r['id']);
            }
        }
    }

    /**
     * 通知报表中心
     */
    protected function notifyRptCenter()
    {

    }
}