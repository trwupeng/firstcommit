<?php

namespace Prj\Secure;

class TongDun
{
	/**
	 * 调用同盾安全保护接口获得风险结果建议
	 * @param array $params 请求参数
	 * @param int   $timeout 超时时间
	 * @param int   $connection_timeout 连接超时时间
	 * @return string
	 */
	public static function invoke_fraud_api(array $params, $timeout = 5000, $connection_timeout = 5000) {
		$api_url = \Sooh\Base\Ini::getInstance()->get('TongDun_Api_Url');

		$options = array(
			CURLOPT_POST => 1,            // 请求方式为POST
			CURLOPT_URL => $api_url,      // 请求URL
			CURLOPT_RETURNTRANSFER => 1,  // 获取请求结果
			// -----------请确保启用以下两行配置------------
			CURLOPT_SSL_VERIFYPEER => 1,  // 验证证书
			CURLOPT_SSL_VERIFYHOST => 2,  // 验证主机名
			// -----------否则会存在被窃听的风险------------
			CURLOPT_POSTFIELDS => http_build_query($params) // 注入接口参数
		);
		if (defined("CURLOPT_TIMEOUT_MS")) {
			$options[CURLOPT_NOSIGNAL] = 1;
			$options[CURLOPT_TIMEOUT_MS] = $timeout;
		} else {
			$options[CURLOPT_TIMEOUT] = ceil($timeout / 1000);
		}
		if (defined("CURLOPT_CONNECTTIMEOUT_MS")) {
			$options[CURLOPT_CONNECTTIMEOUT_MS] = $connection_timeout;
		} else {
			$options[CURLOPT_CONNECTTIMEOUT] = ceil($connection_timeout / 1000);
		}
		$ch = curl_init();
		curl_setopt_array($ch, $options);
		if(!($response = curl_exec($ch))) {
			// 错误处理，按照同盾接口格式fake调用结果
			$_msg =  array(
				"success" => false,
				"reason_code" => "000:调用API时发生错误[".curl_error($ch)."]"
			);
			var_log($_msg, 'tongdun Ret Error');
			return '';
		}
		curl_close($ch);
		return $response;
//		return json_decode($response, true);
	}
}