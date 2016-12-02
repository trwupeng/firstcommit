<?php
namespace Lib\SMS;

/**
 * 创蓝发送短信
 * @package Lib\SMS
 * @author  LTM <605415184@qq.com>
 */
class ChuangLan
{
    //创蓝发送短信接口URL, 如无必要，该参数可不用修改
    const API_SEND_URL = 'http://222.73.117.156/msg/HttpBatchSendSM';
    //创蓝短信余额查询接口URL, 如无必要，该参数可不用修改
    const API_BALANCE_QUERY_URL = 'http://222.73.117.156/msg/QueryBalance';
    //创蓝账号
    const API_ACCOUNT = 'xiaoxiawangluo';
    //创蓝密码
    const API_PASSWORD = 'Aa123456';

    /**
     * 发送短信
     * @param  string  $phone      手机号码
     * @param   string $msg        短信内容
     * @param string   $product    产品id,可选
     * @param string   $needstatus 是否需要状态报告
     * @param string   $extno      扩展码,可选
     * @return string
     * @throws \Sooh\Base\ErrException
     */
    public function send($phone, $msg, $product = '349312826', $needstatus = 'true', $extno = '044485')
    {
        //创蓝接口参数
        $postArr = [
            'account'    => self::API_ACCOUNT,
            'pswd'       => self::API_PASSWORD,
            'msg'        => $msg,
            'mobile'     => $phone,
            //			'product' => $product,
            'needstatus' => $needstatus,
            //			'extno' => $extno,
        ];

        $result = $this->curlPost(self::API_SEND_URL, $postArr);
        if (is_numeric($result[1]) && $result[1] == 0) {
            return 'success';
        } else {
            throw new \Sooh\Base\ErrException('发送失败');
        }
    }

    /**
     * 查询额度
     */
    public function queryBalance()
    {
        $postArr = array('account' => self::API_ACCOUNT, 'pswd' => self::API_PASSWORD);
        $result  = $this->curlPost(self::API_BALANCE_QUERY_URL, $postArr);
        return $this->execResult($result);
    }

    /**
     * 通过CURL发送HTTP请求
     * @param string $url        //请求URL
     * @param array  $postFields //请求参数
     * @return mixed
     */
    private function curlPost($url, $postFields, $timeout = 2000, $connection_timeout = 2000)
    {
        $postFields = http_build_query($postFields);
        $ch         = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);

        if (defined("CURLOPT_TIMEOUT_MS")) {
            curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT_MS, $timeout);
        } else {
            curl_setopt($ch, CURLOPT_TIMEOUT, ceil($timeout / 1000));
        }
        if (defined("CURLOPT_CONNECTTIMEOUT_MS")) {
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, $connection_timeout);
        } else {
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, ceil($connection_timeout / 1000));
        }

        $result = curl_exec($ch);
        curl_close($ch);
        return $this->execResult($result);
    }

    /**
     *
     */
    /**
     * 处理返回值
     * @param string $result curl result
     * @return mixed
     */
    private function execResult($result)
    {
        $result = preg_split("/[,\r\n]/", $result);
        return $result;
    }


    const MAX_COMMIT_NUM = 20000; // 单次最大提交数量

    /**
     * 如果以后需要短信的精确发送到用户的状态，需要定义适用各个短信平台各种状态（短信提交状态，用户收到信息状态等等）的通用状态code
     * 将具体的短信平台返回的状态转换成通用状态code
     * 发送营销短信
     * @param string $phone 手机号
     * @param string $msg 发送内容
     * @return string
     * @throws \Sooh\Base\ErrException
     */
    public function sendMarket($phone, $msg)
    {
        $url     = 'http://222.73.117.169/msg/HttpBatchSendSM?';
        $account = 'xiaoxia_yx';
        $pwd     = 'Tch931947';
        $product = 2347;
        $extno   = 1793;

        if (is_array($phone)) {
            if (sizeof($phone) > self::MAX_COMMIT_NUM) {
                throw new \Sooh\Base\ErrException('单次提交最多' . self::MAX_COMMIT_NUM . '个号码');
            }
            $phone = explode(',', $phone);
        }

        $postData = [
            'account'    => $account,
            'pswd'       => $pwd,
            'msg'        => $msg,
            'mobile'     => $phone,
            'needstatus' => true,
        ];

        $rs = $this->curlPost($url, $postData);
        if (is_numeric($rs[1]) && $rs[1] == 0) {
            return 'success';
        } else {
            throw new \Sooh\Base\ErrException('提交短信到创蓝平台失败');
        }
    }
}