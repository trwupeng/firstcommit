<?php
namespace Prj\Agreement;
/**
 * Class Agreement
 * @package Prj\Agreement
 * @author LTM
 */
class Agreement2 {
	const errAgreementNotExists = '';
	const errServerBusy = '';

	/**
	 * 创建一个新的协议版本
	 * @param integer $verType 协议类型
	 * @param string $userId 创建者ID
	 * @param string $title 标题
	 * @param string $content 内容
	 * @param string $verTpl 协议模版
	 */
	public function createNew($verType, $userId, $title, $content, $verTpl) {
		$dbAgreement = \Prj\Data\Agreement2::getCopy('');

		$dbAgreement->setField('verType', $verType);
		$dbAgreement->setField('userId', $userId);
		$dbAgreement->setField('title', $title);
		$dbAgreement->setField('content', $content);
		$dbAgreement->setField('verTpl', $verTpl);
		$dbAgreement->setField('createTime', \Sooh\Base\Time::getInstance()->ymdhis());
		$dbAgreement->setField('status', \Prj\Consts\Agreement::status_disable);
		$dbAgreement->update();
	}

	/**
	 * 启用一个协议版本，同类型的其他协议都改为废弃
	 * 想要禁用一个协议版本，必须提供一个新的协议
	 * @param string $verId 协议版本ID
	 * @throws \Sooh\Base\ErrException
	 * @return bool true;
	 */
	public function enable($verId) {
		$dbAgreement = \Prj\Data\Agreement2::getCopy($verId);
		$dbAgreement->load();
		try {
			if ($dbAgreement->exists()) {
				$verType = $dbAgreement->getField('verType');
				$maps = ['verType' => $verType, 'status' => \Prj\Consts\Agreement::status_enable];
				$ret = $dbAgreement->db()->updRecords($dbAgreement->tbname(), ['status' => \Prj\Consts\Agreement::status_disable], $maps);
				if ($ret) {
					$dbAgreement->setField('status', \Prj\Consts\Agreement::status_enable);
					$dbAgreement->update();
					return true;
				} else {
					throw new \Sooh\Base\ErrException(self::errServerBusy);
				}
			} else {
				throw new \Sooh\Base\ErrException(self::errAgreementNotExists);
			}
		} catch (\ErrException $e) {
			throw new \Sooh\Base\ErrException($e->getMessage(), $e->getCode());
		}
	}

	/**
	 * 生成一个协议
	 */
	public function produce($verId, $userId) {

	}

	protected function getAgreement() {
		return ;
	}
}