<?php
use Lib\WX\Wechat;
use Lib\WX\WechatAuth;

class WechatController extends \Prj\BaseCtrl
{
    /**
     * 微信消息接口入口
     * 所有发送到微信的消息都会推送到该操作
     * 所以，微信公众平台后台填写的api地址则为该操作的访问地址
     */
    public function indexAction($id = '')
    {
        try {
            $wechatConf = \Sooh\Base\Ini::getInstance()->get('WECHAT');
            /* 加载微信SDK */
            $wechat = new Wechat($wechatConf['TOKEN'], $wechatConf['APPID'], $wechatConf['AESKEY']);

            /* 获取请求信息 */
            $data = $wechat->request();

            if ($data && is_array($data)) {
                //执行Demo
                $this->run($wechat, $data);
            }
        } catch (Exception $e) {
            var_log($e->getMessage(), 'wechat出错啦');
        }
    }

    public function testAction()
    {
        return false;
        $wechatAuth = new \Lib\WX\WechatAuth(\Prj\Data\Config::get('WECHAT_APP_ID'), \Prj\Data\Config::get('WECHAT_APP_SECRET'));

        // 注意 URL 一定要动态获取，不能 hardcode.
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url      = "{$protocol}{$_SERVER['HTTP_HOST']}{$_SERVER[REQUEST_URI]}";
        //		$url = 'http://tzv6qjwp8m.proxy.qqbrowser.cc/index.php?__=wechat/test';
        $jsapiTicket = $wechatAuth->getJsapiTicket();
        $nonceStr    = $this->createNonceStr();
        $timestamp   = \Sooh\Base\Time::getInstance()->timestamp();
        $string      = "jsapi_ticket={$jsapiTicket}&noncestr={$nonceStr}&timestamp={$timestamp}&url={$url}";
        $signature   = sha1($string);

        $signPacket = [
            'appId'       => \Prj\Data\Config::get('WECHAT_APP_ID'),
            'jsapiTicket' => $jsapiTicket,
            'nonceStr'    => $nonceStr,
            'timestamp'   => $timestamp,
            'url'         => $url,
            'signature'   => $signature,
            'rawString'   => $string,
        ];
        $this->_view->assign('signPacket', $signPacket);
    }

    /**
     * 处理逻辑
     * @param \Lib\WX\Wechat $wechat Wechat对象
     * @param  array         $data   接受到微信推送的消息
     */
    private function run($wechat, $data)
    {
        switch ($data['MsgType']) {
            case Wechat::MSG_TYPE_EVENT:
                $this->parseEvent($wechat, $data);
                break;
            case Wechat::MSG_TYPE_TEXT:
                $this->parseText($wechat, $data);
                break;
            case Wechat::MSG_TYPE_IMAGE:
            case Wechat::MSG_TYPE_VOICE:
            case Wechat::MSG_TYPE_VIDEO:
            case Wechat::MSG_TYPE_SHORTVIDEO:
            case Wechat::MSG_TYPE_LOCATION:
            case Wechat::MSG_TYPE_LINK:
            default:
                $this->parseDefault($wechat, $data);
                break;
        }
    }

    /**
     * 处理微信事件
     * @param \Lib\WX\Wechat $wechat Wechat对象
     * @param  array         $data   接受到微信推送的消息
     */
    private function parseEvent($wechat, $data)
    {
        switch ($data['Event']) {
            case Wechat::MSG_EVENT_SUBSCRIBE:
                var_log($data, 'wechat user subscribe, this is data content');
                break;
            case Wechat::MSG_EVENT_UNSUBSCRIBE:
                var_log($data, 'wechat user unsubscribe, this is data content');
                break;
            case Wechat::MSG_EVENT_CLICK:
                switch ($data['EventKey']) {
                    case 'MENU_CUSTOM':
                        $wechat->replyText('客服电话：400-101-8610' . "\n" . '服务时间：早9：00-晚21:00（工作日）');
                        break;
//                    case 11:
//                        //绑定帐号
//                        $wechat->replyText('绑定帐号将分两步完成，首先请按照如下格式输入：tel#123456789');
//                        break;
//                    case 12:
//                        //账户余额
//                        try {
//                            $openId    = $data['FromUserName'];
//                            $snsWechat = \Prj\Data\SNSWechat::getCopy($openId);
//                            $snsWechat->load();
//                            if ($snsWechat->exists()) {
//                                $userId = $snsWechat->getField('userId');
//                                $user   = \Prj\Data\User::getCopy($userId);
//                                $user->load();
//                                $wechat->replyNews([
//                                    '我的余额',
//                                    '钱包余额：' . $user->getField('wallet') / 100 . "元\n红包余额：" . $user->getField('redPacket') / 100 . "元\n累计收益：" . $user->getField('interestTotal') / 100 . "元\n已用红包：" . $user->getField('redPacketUsed') / 100 . '元',
//                                    '',
//                                    ''
//                                ]);
//                            } else {
//                                $wechat->replyText('对不起，您还没有绑定帐号，请先绑定帐号');
//                            }
//                        } catch (Exception $e) {
//                            var_log('<<<<<<<<<<<<<<<<<<<<<<<');
//                        }
//                        break;
//                    case 13:
//                        //我的订单
//                        try {
//                            $openId    = $data['FromUserName'];
//                            $snsWechat = \Prj\Data\SNSWechat::getCopy($openId);
//                            $snsWechat->load();
//                            if ($snsWechat->exists()) {
//                                $userId = $snsWechat->getField('userId');
//
//                                $pager = new \Sooh\DB\Pager(10);
//                                $pager->init(-1, 1);
//                                $_timeStamp = \Sooh\Base\Time::getInstance()->timestamp();
//                                $ymdStart   = date('YmdHis', ($_timeStamp - 86400 * 30));
//                                $ymdEnd     = date('YmdHis', $_timeStamp);
//                                $where      = [
//                                    'orderStatus' => \Prj\Consts\OrderStatus::$running,
//                                ];
//                                $rs         = \Prj\Data\Investment::pager($userId, $pager, $ymdStart, $ymdEnd, $where, null);
//
//                                $news = ['我的订单', '', '', ''];
//                                foreach ($rs as $v) {
//                                    $_amount = $v['amount'] / 100;
//                                    $news[1] .= "产品名：$v[waresName]；订单ID：$v[ordersId]；投资金额：{$_amount}元；\n";
//                                }
//                                $wechat->replyNews($news);
//                                return;
//                            } else {
//                                $wechat->replyText('对不起，您还没有绑定帐号，请先绑定帐号');
//                            }
//                        } catch (Exception $e) {
//                            var_log('<<<<<<<<<<<<<<<<<<<<<<<');
//                        }
//                        break;
//                    case 14:
//                        //回款计划
//                        try {
//                            $openId    = $data['FromUserName'];
//                            $snsWechat = \Prj\Data\SNSWechat::getCopy($openId);
//                            $snsWechat->load();
//                            if ($snsWechat->exists()) {
//                                $userId = $snsWechat->getField('userId');
//                                $user   = \Prj\Data\User::getCopy($userId);
//                                $user->load();
//
//                                $invest  = \Prj\Data\Investment::getCopy($userId);
//                                $db      = $invest->db();
//                                $tb      = $invest->tbname();
//                                $where   = [
//                                    'userId'      => $userId,
//                                    'orderStatus' => \Prj\Consts\OrderStatus::$running,
//                                    'returnPlan!' => '',
//                                ];
//                                $rs      = $db->getRecords($tb, [
//                                    'waresId',
//                                    'waresName',
//                                    'returnPlan'
//                                ], $where, ' rsort orderTime ');
//                                $newList = $this->_getReturnPlan($rs, 0);
//                                foreach ($newList as $v) {
//                                    $news[] = [
//                                        '标的名：' . $v['waresName'],
//                                        '天数：' . $v['days'] . '；实际支付日：' . $v['realDateYmd'] . '；是否支付：' . ($v['isPay'] ? '是' : '否') . '；固定收益：' . $v['interestStatic'],
//                                        '',
//                                        '',
//                                    ];
//                                }
//                                if (count($news) > 5) {
//                                    $wechat->replyNews($news[0], $news[1], $news[2], $news[3], $news[4]);
//                                } else {
//                                    $wechat->replyNews($news[0]);
//                                }
//                            } else {
//                                $wechat->replyText('对不起，您还没有绑定帐号，请先绑定帐号');
//                            }
//                        } catch (Exception $e) {
//                            var_log('<<<<<<<<<<<<<<<<<<<<<<<');
//                        }
//                        break;
//                    case 15:
//                        $wechat->replyText('OAuth2.0网页授权演示<a href="http://xiaoxia.zzwwsfks.com/index.php?__=public/share">点击这里体验</a>');
//                        //                        //我的消息
//                        //                        try {
//                        //                            $openId    = $data['FromUserName'];
//                        //                            $snsWechat = \Prj\Data\SNSWechat::getCopy($openId);
//                        //                            $snsWechat->load();
//                        //                            if ($snsWechat->exists()) {
//                        //                                $userId = $snsWechat->getField('userId');
//                        //                                $user   = \Prj\Data\User::getCopy($userId);
//                        //                                $user->load();
//                        //
//                        //                                $ret  = \Lib\Services\Message::getInstance()->getList($userId, null, null);
//                        //                                $news = ['我的消息', '', '', ''];
//                        //                                foreach ($ret as $v) {
//                        //                                    $news[1] .= "【{$v[title]}】:$v[content]\n";
//                        //                                }
//                        //                                $wechat->replyNews($news);
//                        //                                return;
//                        //                            } else {
//                        //                                $wechat->replyText('对不起，您还没有绑定帐号，请先绑定帐号');
//                        //                            }
//                        //                        } catch (Exception $e) {
//                        //                            var_log('<<<<<<<<<<<<<<<<<<<<<<<');
//                        //                        }
//                        break;
                    default:
                        $this->transferCustomer($wechat, $data);
                        break;
                }
                break;
            default:
//                $wechat->replyText("欢迎访问理财测试公众平台！您的事件类型：{$data['Event']}，EventKey：{$data['EventKey']}");
                $this->transferCustomer($wechat, $data);
                break;
        }
    }

    /**
     * 处理微信文本消息
     * @param \Lib\WX\Wechat $wechat Wechat对象
     * @param array          $data   接受到的微信推送消息
     */
    private function parseText($wechat, $data)
    {
        $this->transferCustomer($wechat, $data);
        if (strpos($data['Content'], 'tel#') !== false) {
            $loginName = substr($data['Content'], 4);
            if (is_numeric($loginName) && strlen($loginName) == 11) {
                $openId = $data['FromUserName'];
                try {
                    $snsWechat = \Prj\Data\SNSWechat::getCopy($openId);
                    $snsWechat->load();
                    $snsWechat->setField('loginName', $loginName);
                    $snsWechat->setField('expiresIn', \Sooh\Base\Time::getInstance()->timestamp() + 300);//5分钟内绑定有效
                    $snsWechat->update();
                    $wechat->replyText('请输入您的密码，格式为：pwd#123456');
                } catch (Exception $e) {
                    var_log('bind username error');
                }
            } else {
                $wechat->replyText('您输入的用户名不正确，请重新绑定');
            }
            return;
        }
        if (strpos($data['Content'], 'pwd#') !== false) {
            $pwd = substr($data['Content'], 4);
            if (preg_match('#^\w{6,20}$#', $pwd)) {
                $openId = $data['FromUserName'];
                try {
                    $snsWechat = \Prj\Data\SNSWechat::getCopy($openId);
                    $snsWechat->load();
                    if ($snsWechat->exists()) {
                        $loginName = $snsWechat->getField('loginName');
                        $userId    = $snsWechat->getField('userId');
                        if (!empty($loginName) && empty($userId) && $snsWechat->getField('expiresIn') > \Sooh\Base\Time::getInstance()->timestamp()) {
                            $account     = \Lib\Services\Account::getInstance(\Prj\BaseCtrl::getRpcDefault('Account'));
                            $accountInfo = $account->login($loginName, 'phone', $pwd);

                            $snsWechat->setField('userId', $accountInfo['accountId']);
                            $snsWechat->update();
                            $wechat->replyText('恭喜您绑定成功^_^');
                        } else {
                            $wechat->replyText('您上次输入已经失效，请您重新绑定');
                        }
                    } else {
                        $wechat->replyText('您还没有输入用户名，请您重新绑定');
                    }
                } catch (Exception $e) {
                    $wechat->replyText('用户名或者密码错误');
                }
            } else {
                $wechat->replyText('您输入的密码不正确，请重新绑定');
            }
            return;
        } else {
            $wechat->replyText('您输入的是：' . $data['Content']);
        }

//        switch ($data['Content']) {
//            case '文本':
//                $wechat->replyText('欢迎访问理财测试公众平台，这是文本回复的内容！');
//                break;
//
//            case '图片':
//                //$media_id = $this->upload('image');
//                $media_id = '1J03FqvqN_jWX6xe8F-VJr7QHVTQsJBS6x4uwKuzyLE';
//                $wechat->replyImage($media_id);
//                break;
//
//            case '语音':
//                //$media_id = $this->upload('voice');
//                $media_id = '1J03FqvqN_jWX6xe8F-VJgisW3vE28MpNljNnUeD3Pc';
//                $wechat->replyVoice($media_id);
//                break;
//
//            case '视频':
//                //$media_id = $this->upload('video');
//                $media_id = '1J03FqvqN_jWX6xe8F-VJn9Qv0O96rcQgITYPxEIXiQ';
//                $wechat->replyVideo($media_id, '视频标题', '视频描述信息。。。');
//                break;
//
//            case '音乐':
//                //$thumb_media_id = $this->upload('thumb');
//                $thumb_media_id = '1J03FqvqN_jWX6xe8F-VJrjYzcBAhhglm48EhwNoBLA';
//                $wechat->replyMusic(
//                    'Wakawaka!',
//                    'Shakira - Waka Waka, MaxRNB - Your first R/Hiphop source',
//                    'http://wechat.zjzit.cn/Public/music.mp3',
//                    'http://wechat.zjzit.cn/Public/music.mp3',
//                    $thumb_media_id
//                ); //回复音乐消息
//                break;
//
//            case '图文':
//                $wechat->replyNewsOnce(
//                    "全民创业蒙的就是你，来一盆冷水吧！",
//                    "全民创业已经如火如荼，然而创业是一个非常自我的过程，它是一种生活方式的选择。从外部的推动有助于提高创业的存活率，但是未必能够提高创新的成功率。第一次创业的人，至少90%以上都会以失败而告终。创业成功者大部分年龄在30岁到38岁之间，而且创业成功最高的概率是第三次创业。",
//                    "http://www.topthink.com/topic/11991.html",
//                    "http://yun.topthink.com/Uploads/Editor/2015-07-30/55b991cad4c48.jpg"
//                ); //回复单条图文消息
//                break;
//
//            case '多图文':
//                $news = array(
//                    "全民创业蒙的就是你，来一盆冷水吧！",
//                    "全民创业已经如火如荼，然而创业是一个非常自我的过程，它是一种生活方式的选择。从外部的推动有助于提高创业的存活率，但是未必能够提高创新的成功率。第一次创业的人，至少90%以上都会以失败而告终。创业成功者大部分年龄在30岁到38岁之间，而且创业成功最高的概率是第三次创业。",
//                    "http://www.topthink.com/topic/11991.html",
//                    "http://yun.topthink.com/Uploads/Editor/2015-07-30/55b991cad4c48.jpg"
//                ); //回复单条图文消息
//
//                $wechat->replyNews($news, $news, $news, $news, $news);
//                break;
//            case 'test':
//                $wechat->replyText('http://tzv6qjwp8m.proxy.qqbrowser.cc/index.php?__=wechat/test');
//                break;
//            case 'token':
//                $wechat->replyText('token');
//                break;
//            default:
//                $wechat->replyText("欢迎访问理财测试公众平台！您输入的内容是：{$data['Content']}");
//                break;
//        }
    }

    /**
     * 默认的消息处理方法
     * @param \Lib\WX\Wechat $wechat Wechat对象
     * @param array          $data   接受到的微信推送消息
     */
    private function parseDefault($wechat, $data) {
        $this->transferCustomer($wechat, $data);
    }

    /**
     * 转发到客服-不做其他处理
     * @param \Lib\WX\Wechat $wechat Wechat对象
     * @param array          $data   接受到的微信推送消息
     */
    private function transferCustomer($wechat, $data) {
        $wechat->response('transfer_customer_service', $wechat::MSG_TYPE_TRANSFER_CUSTOMER_SERVICE);
    }

    /**
     * 资源文件上传方法
     * @param  string $type 上传的资源类型
     * @return string       媒体资源ID
     */
    private function upload($type)
    {
        $token = \Sooh\Base\Session\Data::getInstance()->get('token');

        if ($token) {
            $auth = new WechatAuth(\Prj\Data\Config::get('WECHAT_APP_ID'), \Prj\Data\Config::get('WECHAT_APP_SECRET'), $token);
        } else {
            $auth  = new WechatAuth(\Prj\Data\Config::get('WECHAT_APP_ID'), \Prj\Data\Config::get('WECHAT_APP_SECRET'));
            $token = $auth->getAccessToken();

            \Sooh\Base\Session\Data::getInstance()->set('token', $token['access_token'], $token['expires_in']);
        }

        switch ($type) {
            case 'image':
                $filename = './Public/image.jpg';
                $media    = $auth->materialAddMaterial($filename, $type);
                break;

            case 'voice':
                $filename = './Public/voice.mp3';
                $media    = $auth->materialAddMaterial($filename, $type);
                break;

            case 'video':
                $filename    = './Public/video.mp4';
                $discription = array('title' => '视频标题', 'introduction' => '视频描述');
                $media       = $auth->materialAddMaterial($filename, $type, $discription);
                break;

            case 'thumb':
                $filename = './Public/music.jpg';
                $media    = $auth->materialAddMaterial($filename, $type);
                break;

            default:
                return '';
        }

        if ($media["errcode"] == 42001) { //access_token expired
            session("token", null);
            $this->upload($type);
        }

        return $media['media_id'];
    }

    private function createNonceStr($length = 16)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str   = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    /**
     * 从订单列表里抽出还款计划
     * By Hand
     */
    protected function _getReturnPlan($rs, $isPay = 0)
    {
        $tinyReturnPlan = [];
        if (!empty($rs)) {
            foreach ($rs as $k => $v) {
                $returnPlan = json_decode($v['returnPlan'], true);
                if (!empty($returnPlan)) {
                    foreach ($returnPlan['calendar'] as $kk => $vv) {
                        $vv['waresId']   = $v['waresId'];
                        $vv['waresName'] = $v['waresName'];
                        //$vv['month']      = (int)substr($kk, 0, 6);
                        //$vv['date']       = $kk;
                        $tinyReturnPlan[] = $vv;
                    }
                }
            }
        }
        usort($tinyReturnPlan, function ($a, $b) {
            if ($a['planDateYmd'] == $b['planDateYmd'])
                return 0;
            return $a['planDateYmd'] > $b['planDateYmd'] ? 1 : -1;
        });
        foreach ($tinyReturnPlan as $k => $v) {
            if ($isPay !== 'all') {
                if ($v['isPay'] != $isPay)
                    continue;
            }
            $newList[] = $v;
        }
        return $newList;
    }
}
