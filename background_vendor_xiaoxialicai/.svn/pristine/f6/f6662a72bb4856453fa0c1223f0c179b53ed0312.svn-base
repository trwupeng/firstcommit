<?php
namespace Sooh\DB\Cases;

class OauthCode extends \Sooh\DB\Base\KVObj {
    protected static function splitedTbName($n,$isCache)
    {
        return 'tb_oauth_code_' . ($n % static::numToSplit());
    }

    public static function getCopy($code) {
        return parent::getCopy(['code' => $code]);
    }
	
	protected static function idFor_dbByObj_InConf($isCache)
	{
		return 'oauth';
	}
}