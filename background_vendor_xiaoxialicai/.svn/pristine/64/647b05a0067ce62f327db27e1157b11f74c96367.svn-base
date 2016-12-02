<?php

namespace Sooh\DB\Cases;

class OauthRefresh extends \Sooh\DB\Base\KVObj {
    protected static function splitedTbName($n,$isCache)
    {
        return 'tb_oauth_refresh_' . ($n % static::numToSplit());
    }

    public static function getCopy($refreshToken)
    {
        return parent::getCopy(['refreshToken' => $refreshToken]);
    }

	protected static function idFor_dbByObj_InConf($isCache)
	{
		return 'oauth';
	}

	public function update()
	{
		error_log('====================start update oauth=====================');
		var_log(func_get_args(), 'func_get_args');
		$err = new \ErrorException();
		error_log( 'trace string:'.$err->getTraceAsString());
		return parent::update();
	}
}