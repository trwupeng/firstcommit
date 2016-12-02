<?php
namespace Sooh\DB\Cases;

class OauthToken extends \Sooh\DB\Base\KVObj {
    protected static function splitedTbName($n,$isCache)
    {
        return 'tb_oauth_token_' . ($n % static::numToSplit());
    }

    /**
     * 获取tb_oauth_token
     * @param string $token
     * @return \Sooh\DB\Base\KVObj
     */
    public static function getCopy($token) {
        return parent::getCopy(['accessToken' => $token]);
    }
	
	protected static function idFor_dbByObj_InConf($isCache)
	{
		return 'oauth';
	}
}