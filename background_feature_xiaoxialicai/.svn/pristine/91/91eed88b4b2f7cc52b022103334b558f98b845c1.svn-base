<?php
namespace Prj\Misc;
/**
 * Description of ClientPatch
 *
 * @author simon.wang
 */
class ClientPatch {
	/**
	 * 系统启动时索要后续可更新的版本的列表
	 * @return array [ver:1.0.2,url:xxxx,info:xxxxx,enforce:0]
	 */
	public static function forStartup($clientType,$clientVer, $contractId)
	{
		if($contractId == 'LONG_NAME'){
			$contractId = 900120160426110000;
		}
$log = [
    'clientType'=>$clientType,
    'clientVer'=>$clientVer,
    'contractId'=>$contractId
];    
//var_log($log, '请求数据  ======================================================');
		$db = \Sooh\DB\Broker::getInstance();
		$verInfo = [];
		$where = ['clientType'=>$clientType, 'contractId'=>[$contractId, -1]];
		$arr_autoid_ver = $db->getAssoc('db_p2p.tb_clientPatch', 'autoid', 'ver,enforce,info,url,full', $where,
				'rsort ver1 rsort ver2 rsort ver3 rsort ver4');

//var_log($arr_autoid_ver, '获取对应的所有版本信息======================');
		if (empty($arr_autoid_ver)) {
		    return $verInfo;
		}

		$tmp_request_ver = explode('.', $clientVer);
		$size_request_ver = (sizeof($tmp_request_ver));
		$gotit = false;
		foreach ($arr_autoid_ver as $autoid => $record) {
			$tmp_record_ver = explode('.', $record['ver']);
			$size_record_ver = sizeof($tmp_record_ver);
			if($size_record_ver != $size_request_ver) {
				continue;
			}
			$maxSize = ($size_request_ver > $size_record_ver ? $size_request_ver : $size_record_ver);

			foreach($tmp_record_ver as $sk => $sver) {
				if($sver > $tmp_request_ver[$sk]){
					$verInfo[$autoid] = $record;
					if($record['full'] == 1){
						$gotit = true;
					}
					break;
				}elseif(($sver == $tmp_request_ver[$sk]) && ($sk != $maxSize-1)){
					continue;
				}elseif(($sver == $tmp_request_ver[$sk]) && ($sk == $maxSize-1)){
					$gotit=true;
					break;
				}else {
					$gotit = true;
					break;
				}
			}
			if($gotit){
				break;
			}
		}

		$verInfo = array_reverse($verInfo);
//var_log($verInfo, '最终需要的版本信息=================');

		return $verInfo;
	}
}
