<?php

namespace Prj;

/**
 * 身份证相关操作类
 * @package Prj
 */
class IdCard
{
	/**
	 * 身份证号
	 * @var idCardNum
	 */
	protected $idCardNum;

//	public function __construct($idCardNum)
//	{
//		$this->idCardNum = $idCardNum;
//	}

	/**
	 * 获取性别
	 * @return int 1：男性；0：女性
	 */
	public static function getGender($idCardNum)
	{
		if (strlen($idCardNum) == 15) {
			return substr($idCardNum, -1) % 2 == 1 ? 1 : 0;
		} else {
			return substr($idCardNum, -2 ,1) % 2 == 1 ? 1 : 0;
		}
	}

    /**
     * 获取称呼
     * @param $idCardNum
     * @param $name
     * @return string
     */
    public static function getCall($idCardNum,$name){
        $firstName = mb_substr($name,0,1,'utf-8');
        $call = self::getGender($idCardNum)?'先生':'女士';
        return $firstName.$call;
    }

    /**
     * 姓名脱敏
     * @param $name
     * @return string
     */
    public static function hideName($name){
        $len = mb_strlen($name,'utf-8');
        $tail = str_pad('',$len-1,'*');
        $name = mb_substr($name,0,1,'utf-8');
        return $name.$tail;
    }

    /**
     * 各种卡号脱敏
     * @param $id
     * @return mixed
     */
    public static function hideId($id){
        $len = strlen($id);
        $start = floor($len/3);
        $xingLen = floor($len/3);
        $xing = str_pad('',$xingLen,'*');
        $newId = substr_replace($id,$xing,$start,$xingLen);
        return $newId;
    }

	/**
	 * 计算年龄
	 * @param string $cardId 身份证号
	 * @return int
	 */
	public static function getAge($cardId) {
		if (strlen($cardId) == 15) {
			$year = '19' . substr($cardId, 6, 2);
			$md = substr($cardId, 8, 4);
		} else {
			$year = substr($cardId, 6, 4);
			$md = substr($cardId, 10, 4);
		}

		$nowYear = date('Y', \Sooh\Base\Time::getInstance()->timestamp());
		$nowMd = date('md', \Sooh\Base\Time::getInstance()->timestamp());

        if ($nowYear >= $year) {
            return (($nowYear - $year) + (($nowMd - $md >= 0) ? 0 : -1));
        } else {
            return -2;
        }
    }

    /**
     * 校验身份证号年月日是否合法
     * @param string $cardId 身份证号
     * @return bool
     */
    public static function verify($cardId) {
        if (strlen($cardId) == 15) {
            $year = '19' . substr($cardId, 6, 2);
            $m    = substr($cardId, 8, 2);
            $d    = substr($cardId, 10, 2);
        } else {
            $year = substr($cardId, 6, 4);
            $m    = substr($cardId, 10, 2);
            $d    = substr($cardId, 12, 2);
        }

        $nowYear = date('Y', \Sooh\Base\Time::getInstance()->timestamp());
        $nowMd   = date('md', \Sooh\Base\Time::getInstance()->timestamp());

        //校验年
        if ($year < 1900 || $year >= $nowYear) {
            return false;
        }

        //校验合法性
        $str = "{$year}-{$m}-{$d}";
        if (date('Y-m-d', strtotime($str)) != $str) {
            return false;
        }
        return true;
    }
}