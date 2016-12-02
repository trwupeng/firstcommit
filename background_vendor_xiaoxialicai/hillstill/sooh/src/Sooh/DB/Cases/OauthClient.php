<?php
namespace Sooh\DB\Cases;

/**
 * Oauth-客户端表
 * Class OauthClient
 */
class OauthClient extends \Sooh\DB\Base\KVObj {
    protected static function splitedTbName($n,$isCache)
    {
        return 'tb_oauth_client_' . ($n % static::numToSplit());
    }

    /**
     * client getCopy
     * @param string $clientId 客户端ID
     * @param string $clientSecret 客户端密钥
     * @return \Sooh\DB\Base\KVObj
     */
    public static function getCopy ($clientId) {
        return parent::getCopy(['id' => $clientId]);
    }
	
	protected static function idFor_dbByObj_InConf($isCache)
	{
		return 'oauth';
	}
}