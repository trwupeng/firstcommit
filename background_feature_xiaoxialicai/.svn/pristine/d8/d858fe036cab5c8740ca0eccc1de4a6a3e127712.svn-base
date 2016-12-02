<?php
/**
 *
 * 过期红包提醒配置
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/5/9 0009
 * Time: 下午 3:47
 */

namespace Rpt\Configs;

class VoucherOverdue {

    /**
     * 提醒时间点，过期时间段
     * @var array
     */

    public static $overdue =  [
        845=>[0, 85959],        // 00:00 - 09:00
        1145=>[90000, 145959],  // 09:00 - 15:00
        1445=>[150000, 185959], // 15:00 - 19:00
        1745=>[190000, 205959], // 19:00 - 21:00
        2045=>[210000, 235959], // 21:00 - 24:00
    ];

    /**
     * 可以累积amount的券类型
     * @var array
     */
    public static $addup_type = [
        \Prj\Consts\Voucher::type_real,
    ];

    /**
     * 不可以累计amount的券类型
     * @var array
     */
    public static $no_addup_type = [
        \Prj\Consts\Voucher::type_yield,
    ];


    /**
     * 要抓取的券类型
     * 目前只有红包，以后可能还有加息券和其他券
     * @var array
     */
    public static $grabVoucherType = [
        // 红包
        \Prj\Consts\Voucher::type_real => [
            '首次登录红包',
            '认证红包',
            '注册红包',
            '首充红包',
            '首购红包',
            '邀请红包',
            '赏金红包',
            '活动红包',
            '分享红包',
            '流标补偿红包',
        ],
    ];

    public static $msg = [
        \Prj\Consts\Voucher::type_real => '{num}个共计{amount}元的红包',
        \Prj\Consts\Voucher::type_yield => '{num}张{amount}%加息券',
    ];

    /**
     * 短信模板
     */
    const sms_tpl = '尊敬的用户，您有{replace}将在明天过期，请您尽快登录小虾理财平台投资使用吧！每天签到还有无限抵用的现金红包可以领哦！';
    /**
     * 站内信模板
     */
    const msg_tpl = '尊敬的用户，您有{replace}将在明天过期，小虾提醒您不要忘记使用哦！';

    /**
     * push信息模板
     */
    const push_tpl = '小虾提醒您，您有{replace}将在明天过期，不要忘记使用哦!';

    /**
     * 站内信标题
     */
    const msg_title = '红包过期提醒';

    /**
     *
     * 分页给用户发短信和站内信
     *
     */

    const pagesize = 500;

    /**
     * 使用了哪些消息发送方式 1 短信， 2 站内信， 3 推送
     * @var array
     */
    public static $sendMsgType = [2, 3];

}


