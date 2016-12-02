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

        return ($nowYear - $year) + (($nowMd - $md >= 0) ? 0 : -1);
    }
}