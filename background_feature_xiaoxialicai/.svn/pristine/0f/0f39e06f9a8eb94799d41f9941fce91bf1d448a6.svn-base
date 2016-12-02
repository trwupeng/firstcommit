<?php
namespace Rpt;
/**
 * 
 * 从生产库获取用户与生俱来的基本信息
 */
class Funcs {
    public  static  function getUserBasicInfo ($userId) {
        $fields = ['ymdReg', 'hisReg', 'ipReg', 'phone', 'nickname', 'copartnerId',
            'clientType', 'contractId', 'inviteByUser', 'inviteByParent', 'inviteByRoot',
            'myInviteCode', 'isBorrower', 'isSuperUser', 'idCard'];
        $o = \Prj\Data\User::getCopy($userId);
        $db = $o->db();
        $record = $db->getRecord($o->tbname(), $fields, ['userId'=>$userId]);

        $o->free(true);
        $db = null;
        $tmp=[];
        if(!empty($record)) {
            $promotionWay = \Sooh\DB\Broker::getInstance('default')->getPair('db_p2p.tb_contract_0', 'contractId', 'promotionWay');
            $tmp = [
                'userId'             => $userId,
                'phone'              => $record['phone'],
                'nickname'           => $record['nickname'],
                'ymdReg'             => $record['ymdReg'],
                'hisReg'             => $record['hisReg'],
                'clientType'         => $record['clientType'],
                'copartnerId'        => $record['copartnerId'],
                'contractId'         => $record['contractId'],
                'promotionWay'       => $promotionWay[$record['contractId']],
                'isBorrower'         => $record['isBorrower'],
                'flagUser'           => $record['isSuperUser'],
                'inviteByUser'       => $record['inviteByUser'],
                'inviteByParent'     => $record['inviteByParent'],
                'inviteByRoot'       => $record['inviteByRoot'],
                'myInviteCode'       => $record['myInviteCode'],
            ];
            if(!empty($record['idCard'])) {
                $tmp['realname'] = $record['nickname'];
                $tmp['idCard'] = $record['idCard'];
                $tmp['ymdBirthday'] = substr($tmp['idCard'], -12, 8);
                $tmp['gender'] = (substr($tmp['idCard'], -2, 1) % 2) ? 'm' : 'f';
            }
        }
        return $tmp;
    }

    /**
     * 根据配置中的手机号码获取这些手机号码对应的用户id 用于在抓取数据中排除掉这些用户
     * @return array
     * @throws \ErrorException
     */
    public static function getexcludedUser (){
//        $phones = \Prj\Data\Config::get('EXCLUDE_PHONES');

        $oConfg = \Prj\Data\TbConfigItem::getCopy();
        $db = $oConfg->db();
        $tbname = $oConfg->tbname();
        $phones = $db->getOne($tbname, 'v', ['k'=>EXCLUDE_PHONES]);
        $ret =[];
        if(!empty($phones)) {
            $phones = array_unique(explode('|', $phones));
            foreach($phones as $phone) {
                $o= \Prj\Data\User::getByPhone($phone);
                if($o!==null) {
                    $ret[$phone] = $o->getField('userId');
                    $o->free();
                }
            }
        }
        return $ret;
    }
}