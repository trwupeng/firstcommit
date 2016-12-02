<?php
namespace Prj\Data;
/**
 * Created by PhpStorm.
 * User: tang.gaohang
 * Date: 2016/5/5
 * Time: 15:46
 */

class UserChangeLog {

    const tbname = 'tb_user_change_log';

    const type_unbind = 'unbind';

    protected static function DB() {
        return \Sooh\DB\Broker::getInstance();
    }

    public static function addLog($type,$phone,$data){
        $data = is_array($data)?json_encode($data):$data;
        $managerId = \Sooh\Base\Session\Data::getInstance()->get('managerId');
        if(empty($managerId)){
            $nickname = '';
        }else{
            list($loginName,$cameFrom) = explode('@', $managerId);
            $manager = \Prj\Data\Manager::getCopy($loginName,$cameFrom);
            $manager->load();
            $nickname = $manager->getField('nickname');
        }

        $data = [
            'operator'=>$nickname,
            'updateTime'=>date('YmdHis'),
            'evt'=>$type,
            'data'=>$data,
            'phone'=>$phone,
        ];

        return self::DB()->addRecord(self::tbname,$data);
    }
}