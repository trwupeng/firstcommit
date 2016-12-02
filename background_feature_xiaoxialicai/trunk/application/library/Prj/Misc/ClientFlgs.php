<?php
namespace Prj\Misc;
/**
 * @param 状态位通用接口模块
 *
 * @author wupeng
 *
 * */
class ClientFlgs{
    const field = 'clientFlgs';
    /**
     * 通过UserId,从用户身上取出ClientFlgs的值，检查如果跨天了，清空daily里面的记录，
     * 返回所有的客户端的标记 [ever=>[....],daily=>[....],lastday=>20160324]
     */

    public static function  getCurrent($userId){
        
        $userId=\Sooh\Base\Session\Data::getInstance()->get('accountId');
        $user=\Prj\Data\User::getCopy($userId);
        $user->load();
        $records=$user->getField(self::field);
	    if (is_string($records)) {
		    $records = json_decode($records, true);
	    }
	    if (!is_array($records)) {
		    return [];
	    }
        $today=date('Ymd');
        $lastday=$records['lastday'];
        if($today!=$lastday){
            $records['daily']=[];
        }
        $records['lastday']=$today;
        return $records;

    }
}