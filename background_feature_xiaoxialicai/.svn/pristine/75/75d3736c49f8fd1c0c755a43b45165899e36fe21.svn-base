<?php
/**
 * 消息中心处理类
 * User: LTM <605415184@qq.com>
 * Date: 2016/6/20
 */
namespace Prj\Message;

/**
 * 通用的根据模板发送消息
 * @author LTM <605415184@qq.com>
 */
class Message
{
    const MSG_BRAND       = '小虾理财';
    const MSG_TEL_SERVE   = '400-101-8610';
    const MSG_NUM_TIME_15 = 15;
    const MSG_TIME_ALL_12 = 12;

    /**
     * @var string DEFAULT_MSG_CONF_PATH 默认的消息模板配置
     */
    const DEFAULT_MSG_CONF_PATH = './Conf/message.php';

    /**
     * @var array $msgConf 消息模板的配置
     */
    public static $msgConf = [];

    /**
     * @var array $pushType 推送消息种类，excel->class
     */
    public static $pushType = [
        '1'  => 'bid',//投标
        '2'  => 'contractIssued',//合同下发
        '3'  => 'repayment',//项目回款
        '4'  => 'withdrawal',//提现
        '5'  => 'redPacket',//红包
        '6'  => 'rebate',//返利
        '7'  => 'notice',//通知
        '8'  => 'coinbank',//存钱罐
        '9'  => 'bulletin',//公告
        '20' => 'startBid',//标的开售提醒（不在message中）
    ];

    /**
     * @var array $typeMapName = 消息推送名称，class->name
     */
    public static $typeMapName = [
        'bid'            => '投标',
        'contractIssued' => '合同下发',
        'repayment'      => '项目回款',
        'withdrawal'     => '提现',
        'redPacket'      => '红包',
        'rebate'         => '返利',
        'notice'         => '通知',
        'coinbank'       => '存钱罐',
        'bulletin'       => '公告',
        'startBid'       => '标的开售提醒',
    ];

    /**
     * 获取推送设置
     * @return array
     */
    public static function getPushMap()
    {
        self::readConf();

        $pushConf = [];
        foreach (self::$msgConf as $k => $v) {
            if (!empty($v['push'])) {
                if (isset(self::$pushType[$v['class']])) {
                    $pushConf[self::$pushType[$v['class']]] = self::$typeMapName[self::$pushType[$v['class']]];
                }
            }
        }

        return $pushConf;
    }

    /**
     * 第一个参数用于匹配定位替换字符串
     *      ['event' => 'event', ...]
     * 第二个参数用于发送相关的参数
     *      ['params1' => 'v1',...]
     * 发短信中有短信验证码时，需要在params[第二个参数]中指定此短信验证码smsCode,例如：['smsCode' => '666666'...]
     * @return array ['sms' => true, 'push' => false, 'msg' => true]
     */
    public static function run()
    {
        list($args, $params) = func_get_args();
        var_log($args, 'ReadConf::run->args');
        var_log($params, 'ReadConf::run->params');

        $conf = self::readConf();

        $ret = ['sms' => false, 'push' => false, 'msg' => false];
        foreach ($conf as $k => $v) {
            if ($v['event'] == $args['event']) {
                if (preg_match_all('(\{\w+\})', $v['value'], $match) >= 0) {
                    array_shift($args);

                    //调整参数顺序
                    $arrReplace = [];
                    foreach ($match[0] as $matchV) {
                        $replaceV     = substr($matchV, 1, -1);
                        $arrReplace[] = isset($args[$replaceV]) ? $args[$replaceV] : '__';
                    }

                    $arrReplace = self::parseDecimal($arrReplace);

                    $_str = str_replace($match[0], array_values($arrReplace), $v['value']);
                    var_log($_str, '_str');

                    self::send($_str, $v, $params);
                }
                break;
            }
        }
        return $ret;
    }

    public static function load()
    {
        list($args) = func_get_args();
        var_log($args, 'ReadConf::run->args');

        $conf = self::readConf();

        $ret = [];
        foreach ($conf as $k => $v) {
            if ($v['event'] == $args['event']) {
                if (preg_match_all('(\{\w+\})', $v['value'], $match) >= 0) {
                    array_shift($args);

                    //调整参数顺序
                    $arrReplace = [];
                    foreach ($match[0] as $matchV) {
                        $replaceV     = substr($matchV, 1, -1);
                        $arrReplace[] = isset($args[$replaceV]) ? $args[$replaceV] : '__';
                    }

                    $arrReplace = self::parseDecimal($arrReplace);

                    $_str         = str_replace($match[0], array_values($arrReplace), $v['value']);
                    $ret          = $v;
                    $ret['value'] = $_str;
                }
                break;
            }
        }
        return $ret;
    }

    /**
     * 检查是否打开了推送开关
     * @param string  $userId  用户ID
     * @param integer $classId 分类ID
     * @return bool true可以推送，false关闭了推送
     * @throws \ErrorException
     */
    public static function checkPush($userId, $classId)
    {
        $user = \Prj\Data\User::getCopy($userId);
        $user->load();
        if ($user->exists()) {
            $pushSetting = $user->getField('pushSetting');
            if (empty($pushSetting)) {
                return true;
            }

            if (is_string($pushSetting) && json_decode($pushSetting, true)) {
                $pushSetting = json_decode($pushSetting, true);
            }

            if ((isset($pushSetting['all']) && $pushSetting['all'] == 1) || (!isset($pushSetting['all']))) {
                if (!isset($pushSetting[self::$pushType[$classId]]) || $pushSetting[self::$pushType[$classId]] == 1) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * 读取消息的模板配置
     * @param string $fileName 模板文件名/地址
     * @return array
     */
    private static function readConf($fileName = '')
    {
        if (empty(self::$msgConf)) {
            $file = $fileName ? : self::DEFAULT_MSG_CONF_PATH;
            $_msg = include_once($file);
            $_msg = str_replace([' ', "\t", "\n"], '', $_msg);
            $conf = self::$msgConf = json_decode($_msg, true);
        } else {
            $conf = self::$msgConf;
        }
        return $conf;
    }

    /**
     * 过滤、格式化
     * 过滤小数保留两位，如果为0则不显示
     * 为了应对计算机处理浮点数时，会出现.000000423这种情况，之所以没有对第3位进行四舍五入，是因为这种情况出现的小数位相当靠后（000000001）
     * @param array $arrReplace 待处理的数组数据
     * @return array
     */
    private static function parseDecimal($arrReplace)
    {
        foreach ($arrReplace as $fmtK => $fmtV) {
            if (is_numeric($fmtV)) {
                if (strpos($fmtV, '.') !== false) {
                    list($vInt, $vDecimal) = explode('.', $fmtV);
                    if (strlen($vDecimal) == 1) {
                        if ($vDecimal == 0) {
                            $arrReplace[$fmtK] = $vInt;
                        } else {
                            $arrReplace[$fmtK] = $vInt . '.' . $vDecimal;
                        }
                    }
                    if (substr($vDecimal, 0, 2) == '00') {
                        $arrReplace[$fmtK] = $vInt;
                    } else {
                        if (substr($vDecimal, 1, 1) == 0) {
                            $arrReplace[$fmtK] = $vInt . '.' . substr($vDecimal, 0, 1);
                        } else {
                            $arrReplace[$fmtK] = $vInt . '.' . substr($vDecimal, 0, 2);
                        }
                    }
                }
            }
        }

        return $arrReplace;
    }

    /**
     * 执行发送命令
     * @param string $string 消息内容
     * @param array  $value  消息模板
     * @param array  $params 替换模板中变量的值数组
     * @return void
     */
    public static function send($string, $value, $params)
    {
        if ($value['sms'] == 1) {
            try {
                $code   = $params['smsCode'];
                $smsReg = \Sooh\DB\Cases\SMSCode::getCopy($params['phone']);
                if (empty($code)) {
                    $smsReg->sendCode($string);
                } else {
                    $smsReg->sendCode($string, $code);
                }
                \Lib\Services\SMS::getInstance()->sendCode($params['phone'], empty($code) ? '' : $code, $string);
                $ret['sms'] = true;
                var_log('send sms success, phone:' . $params['phone'] . '; code:' . $code ? : 'null');
            } catch (\Exception $e) {
                var_log('send sms error, phone:' . $params['phone'] . '; code:' . $code ? : 'null');
            }
        }
        if ($value['push'] == 1) {
            //检查是否关闭了推送
            if (self::checkPush($params['userId'], $value['class'])) {
                try {
                    \Lib\Services\Message::getInstance()->push($params['userId'], $string, 'all');
                    $ret['push'] = true;
                    var_log('send push success, userId:' . $params['userId'] . '; classId:' . $value['class']);
                } catch (\Exception $e) {
                    var_log('send push error, userId:' . $params['userId'] . '; classId:' . $value['class']);
                }
            } else {
                var_log('user close push');
            }
        }
        if ($value['msg'] == 1) {
            try {
                $_msgType = $value['class'];
//                if (is_numeric($_msgType) && $_msgType >= 1 && $_msgType <= 7) {
//                    //other
//                } else {
//                    $_msgType = 7;
//                }

                \Lib\Services\Message::getInstance()->add(0, $params['userId'], $_msgType, empty($value['title']) ? '其他' : $value['title'], $string, null, false);
                $ret['msg'] = true;
                var_log('send msg success, userId:' . $params['userId'] . '; title:' . $value['title']);
            } catch (\Exception $e) {
                var_log('send msg error, userId:' . $params['userId'] . '; title:' . $value['title']);
            }
        }
    }
}