<?php
namespace Prj\Consts;

/**
 * 标的的状态码
 *
 * @author simon.wang
 */
class Wares {

    /**
     * 流标
     */
    const status_flow=-3;
	/**
	 * 正式上架后撤销的
	 */
	const status_break=-2;
	/**
	 * 没正式上架的撤销的
	 */
	const status_abandon=-1;
	/**
	 * 新建
	 */
	const status_new=0;


    /**
     * 等待上架审核
     */
    const status_csale=5;



	/**
	 * 等待上架
	 */
	const status_ready=10;
	/**
	 * 上架募集中
	 */
	const status_open=11;
	/**
	 * 募集结束，等待还款
	 */
	const status_go=12;

    /**
     * 还款中
     */
    const status_return=13;

    /**
     * 提前还款结束
     */
    const status_ahead=19;
	/**
	 * 还款结束
	 */
	const status_close=20;
    /**
     * 网关错误
     */
    const gw_error = -2;
    /**
     * 网关已受理
     */
    const gw_wait = 2;
    /**
     * 网关处理成功
     */
    const gw_success = 1;
    /**
     * 网关处理失败
     */
    const gw_failed = 4;

    /**
     * 房贷
     */
    const shelf_static = 2000;
    /**
     * 车贷
     */
    const shelf_static_float = 3000;
    /**
     * 浮动
     */
    const shelf_float = 4000;
    /*
    public static $typeNameArr = array(
        '省心计划',
        '新手专享'
    );
    */

    public static $enum = array(
        self::status_open => "上架募集中",
        self::status_new => "等待内容审核",
        self::status_csale=> "等待上架审核",
        self::status_ready => "等待上架",
        self::status_return=>'还款中',
        self::status_abandon =>"被驳回",
        self::status_go => "募集结束",
        self::status_ahead =>'提前还款',
        self::status_close=>'还款结束',
        self::status_flow=>'流标',
    );

    public static $shilfIdName = array(
        self::shelf_static => '房X',
        self::shelf_static_float => '车X',
        //self::shelf_float => ' ',  //可能导致就数据报错
    );

    public static $gwEnum = [
        self::gw_failed=>'网关错误',
    ];

    public static $viewTPL = [
        'Std02'=>'房贷模板',
        'Std03'=>'车贷模板',
    ];
}
Wares::$shilfIdName[Wares::shelf_static]=\Prj\Lang\Broker::getMsg('wares.name_of_house');
Wares::$shilfIdName[Wares::shelf_static_float]=\Prj\Lang\Broker::getMsg('wares.name_of_car');
