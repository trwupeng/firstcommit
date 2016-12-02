<?php

/**
 * 用户封面勋章
 *
 * @author wu.chen
 */
class GetMedalCover {

    public function run($view, $request, $response = null) {
        $userId = \Sooh\Base\Session\Data::getInstance()->get('accountId');
        $userMedal = new \Lib\Medal\UserMedal();
        //$userId = 46755440552778;
        $res = $userMedal->setWhere(['userId' => $userId])->getRecord();
        $res = json_decode($res['medals'], TRUE);
        $rs = [];
        foreach ($res as $k => $v) {
            if (!empty($v['getLevel'])) {
                $rs[$k] = 1;
            }
        }
        $view->assign('userMedalList', $rs);
    }

}
