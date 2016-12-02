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
        //$userId = 10168531462454;
        $res = $userMedal->setWhere(['userId' => $userId])->getRecord();
        $res = json_decode($res['medals'], TRUE);
        $rs = array_map(function($value) {
            if (!empty($value['getLevel'])) {
                $m = 1;
            }
            return $m;
        }, $res);
        $view->assign('userMedalList', $rs);
    }

}
