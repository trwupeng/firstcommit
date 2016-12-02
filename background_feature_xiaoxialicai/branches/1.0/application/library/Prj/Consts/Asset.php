<?php
namespace Prj\Consts;
/**
 * 资产
 *
 * @author simon.wang
 */
class Asset {

    const status_delete = -14; //删除

    const status_abandon=-1;

    const status_new = 0;

    const status_ok = 1;

    public static $num = [
        self::status_abandon=>'驳回',
        self::status_new=>'等待审核',
        self::status_ok=>'有效',
    ];
}
