<?php
/**
 * 开发测试环境使用的，客户端请跳过
 */
class DevController extends \Lib\Dev\ApidocCtrl
{

	public function init() {
		parent::init();
		if(\Sooh\Base\Ini::getInstance()->get('deploymentCode')>30){
			throw new \ErrorException('ctrl not found');
		}
	}
	public function smstestuserAction()
	{
		$db = \Sooh\DB\Broker::getInstance();
		$r = $db->getCol('db_logs.tb_devsms', 'msg',['phone'=>$this->_request->get('phone')],'rsort dt',10);
		$this->ini->viewRenderType('echo');
		echo '<!Doctype html><html xmlns=http://www.w3.org/1999/xhtml><head><meta http-equiv=Content-Type content="text/html;charset=utf-8">';
		foreach($r as $s){
			echo $s."<br><br>";
		}
	}

	public function rpcAction()
	{
		$this->ini->viewRenderType('json');
		$rpc = self::getRpcDefault('PayGW');
		$rpc = \Sooh\Base\Rpc\Broker::factory('PayGW');
		$sys = \Lib\Services\PayGW::getInstance($rpc);
		try{
			$ret = $sys->recharge('sn', '$cardRecordId', '$userId', '$bankId', '$bankCard', 10000, '$userIP');
			$this->_view->assign('retAll',['ret'=>'ok','got'=>$ret]);
		}catch(\Sooh\Base\ErrException $e){
			$code = $e->getCode();
			if($code==400){
				$this->_view->assign('retAll',['ret'=>$e->getMessage(),'got'=>$e->customData]);
			}elseif($code==500){
				$this->_view->assign('retAll',['ret'=>$e->getMessage(),'got'=>$e->customData]);
			}
		}

	}

	public function indexAction()
	{
		\Lib\Services\SessionStorage::setStorageIni();
		\Sooh\Base\Session\Data::getInstance( \Lib\Services\SessionStorage::getInstance(\Prj\BaseCtrl::getRpcDefault('SessionStorage')));
		\Sooh\Base\Ini::getInstance()->viewRenderType('json');
		$this->_view->assign('normal',true);
		$this->_view->assign('sessionId', \Sooh\Base\Session\Data::getSessId());
		$this->_view->assign('accountId', \Sooh\Base\Session\Data::getInstance()->get('accountId'));
		$today = \Sooh\Base\Time::getInstance()->YmdFull;
		$uri0 = 'http://'.$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].''.$_SERVER["SCRIPT_NAME"];
		$this->_view->assign('urls',  array(
			'【接口文档】'=>$uri0.'?__=dev/apidoc',
			'【数据库表】'=>$uri0.'?__=dev/showTable',
			'【日志字段】'=>$uri0.'?__=dev/logdefine',
			'checkin-checkin'=>$uri0.'?__=actives/checkin&dowhat=checkin',//&with_bonus=1
			'checkin-today'=>$uri0.'?__=actives/checkin',//&with_bonus=1
			'checkin-reset'=>$uri0.'?__=actives/checkin&dowhat=reset&num=0&ymd='.($today-1),
			'financing-pager'=>$uri0.'?__=financing/byshelf&shelfId=1&pageId=1',
		));
		$this->_view->assign('httpGET',  $this->_request->getQuery());
		$this->_view->assign('httpPost',  $this->_request->getPost());
		$this->_view->assign('cookie',  $_COOKIE);
		//$this->_view->assign('code1',\Lib\Subdir\Abc::run());
		throw new \ErrorException('msg append by exception');
		//$this->_view->assign('code2',  \Subdir\Test::run());
		//$this->_view->assign('code3',\Subdir_Type1::run());

	}

	public function logdefineAction()
	{
		\Sooh\Base\Ini::getInstance()->viewRenderType('echo');
		echo '<html><head><meta http-equiv="content-type" content="text/html;charset=utf-8"></head><body>';
		echo '设备标示的格式：idfa:68753A44-4D6F-1226-9C60-0050E4C00067<br><table border=1 cellspacing=0 cellpadding=0 width=100%>';
		$r = current(\Prj\Misc\LogFieldDefine::$define);
		$r = array_keys($r);
		echo '<tr><td><b>evt</b></td><td>'.implode('</td><td>',$r).'</tr>';
		foreach(\Prj\Misc\LogFieldDefine::$define as $evt=>$r){
			echo '<tr><td><b>'.$evt.'</b></td><td>'.implode('</td><td>',$r).'</tr>';
		}
		echo '</table>';
	}
	/**
	 * 测试页面-注册
	 */
	public function registerAction() {

	}

	/**
	 * 测试页面-登录
	 */
	public function loginAction() {

	}

	/**
	 * 接口测试工具
	 */
	public function testAction()
	{
		$key = $this->_request->get('key', '');
        $act = $this->_request->get('act', '');
		if ($key === \Sooh\Base\Ini::getInstance()->get('TestKey')) {
            if (empty($act)) {
			    $auths = [
				    'Orders/add','Oauth/authrize', 'Oauth/appreg', 'Oauth/applogin', 'Oauth/login', 'Oauth/token',
				    'Oauth/sendInvalidcode', 'Oauth/refresh', 'Oauth/userInfo', 'Oauth/resetPwd', 'Oauth/validForReg',
				    'Oauth/quickReg', 'Oauth/quickLogin',
			        'Passport/login', 'Passport/sendInvalidcode', 'Passport/checkInvalidcode', 'Passport/getInvitationCode',
				    'Passport/resetPwd', 'Passport/checkPwdVer', 'Passport/quickLogin', 'Passport/quickReg',
				    'Passport/getInfoByCookie',
			        'Actives/checkin', 'Actives/getBonusList',
			        'Dev/delUser', 'Dev/clearCheckin', 'Dev/push', 'Dev/addMsg', 'Dev/userInfo', 'Dev/rebate',
			        'Dev/announce', 'Dev/broadcastMsg', 'Dev/sendMsgToUser', 'Dev/testInvestmentVoucher', 'Dev/checkNewValidate',
			        'User/redPacket', 'User/myVouchers', 'User/points', 'User/voucherDetail', 'User/info', 'User/myMsg',
				    'User/readMsg', 'User/delMsg', 'User/setToPush', 'User/getPushSetting', 'User/msgCounts',
				    'User/getInvitationCode', 'User/updDtLast', 'User/invite', 'User/resetTradePwd',
				    'User/Info', 'User/readRedPacket', 'User/checkPaypwd', 'User/checkIdCardSn', 'User/findPayPwd',
				    'User/getParentVoucherList', 'User/getInviteList', 'User/bindcard','User/creatPwd',
			        'Public/feedback', 'Public/receiveVoucher','Financing/detail',
			    ];
            } else {
                $auths = [ucfirst(urldecode($act))];
            }
			$this->_view->assign('auths', $auths);
			$this->_view->assign('uriBase', \Sooh\Base\Ini::getInstance()->get('uriBase')['www']);
		} else {
			die('faile');
		}
	}

	/**
	 * test rebate redpacket
	 *
	 */
	public function rebateAction() {
		$giveRedPacket = (new \Prj\Items\RedPacketForRebate())->give('45271489126248', mt_rand(30000, 3650000), 100);
	}

	/**
	 * 使用Curl的方式请求接口
	 */
	public function testInterfaceAction() {
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$params = $this->_request->get('params');
			$module = $this->_request->get('module');
			$api    = $this->_request->get('api');

			if (!empty($params) && is_array($params)) {
				foreach ($params as $key => $value) {
					if ($value == '') {
						unset($params[$key]);
					}
				}
			}

			$params['__'] = substr($module, 0, -10) . '/' . $api;

			$url = \Sooh\Base\Ini::getInstance()->get('uriBase')['www'];
			$url .= "/index.php?__VIEW__=json&" . http_build_query($params);

			$data = \Sooh\Base\Tools::httpGet($url, null, $_COOKIE);

			$this->_view->assign('url', $url);
			$this->_view->assign('params', $params);
			$this->_view->assign('data', $data);
			return $this->returnOK('success');
		} else {
			return $this->returnError('interface faile');
		}
	}

	/**
	 * 删除用户-已经弃用
	 * @input string phones 手机号，多个用英文逗号隔开
	 * @input string cameFrom 来源，一般为phone
	 * @input string tables 表名，多个用英文逗号隔开
	 */
	public function delUserAction() {
		return $this->returnError('方法已经弃用');
		$params = [
			'phones' => $this->_request->get('phones'),
		    'cameFrom' => $this->_request->get('cameFrom', 'phone'),
		    'tables' => $this->_request->get('tables'),
		];

		$this->loger->target = json_encode($params);
		try {
			$delUser = new \Prj\Tests\User();
			$ret = $delUser->del($params['phones'], $params['cameFrom'], $params['tables']);
			$this->_view->assign('data', $ret);
			return $this->returnOK('success');
		} catch (\Exception $e) {
			return $this->returnError($e->getMessage(), $e->getCode());
		}
	}

	/**
	 * 清空今天的签到记录
	 * @input string phone 手机号
	 * @output {"code":200,"msg":"success"}
	 * @errors {"code":400,"msg":"****"}
	 */
	public function clearCheckinAction() {
		$phone = $this->_request->get('phone');
		$cameFrom = 'phone';
		try {
			$dbLogin = \Sooh\DB\Cases\AccountAlias::getCopy([$phone, $cameFrom]);
			$dbLogin->load();
			if ($dbLogin->exists()) {
				$accountId = $dbLogin->getField('accountId');
				$dbUser = \Prj\Data\User::getCopy($accountId);
				$dbUser->load();
				if ($dbUser->exists()) {
					$checkinBook = $dbUser->getField('checkinBook');
					if (!empty($checkinBook)) {
						end($checkinBook['bonusGot']);
						$lastYmd = key($checkinBook['bonusGot']);
						$lastBonus = current($checkinBook['bonusGot']);
						$lastAmount = $lastBonus['RedPacketForCheckin'];

						//仅仅清空今天的记录
						if ($lastYmd == \Sooh\Base\Time::getInstance()->YmdFull) {
							if (count($checkinBook['bonusGot']) > 1) {
								array_pop($checkinBook['bonusGot']);
								end($checkinBook['bonusGot']);
								$checkinBook['ymd'] = key($checkinBook['bonusGot']);
								$dbUser->setField('checkinBook', $checkinBook);
							} else {
								$dbUser->setField('checkinBook', '');
							}

							$dbCheckin = \Prj\Data\Checkin::getCopy($accountId);
							$dbCheckin->load();
							if ($dbCheckin->exists()) {
								$dbCheckin->delete();
								$dbCheckin->update();
							}

							//扣除签到红包额
							$totalRedpacket = $dbUser->getField('redPacket');
							var_log($totalRedpacket);
							var_log($lastAmount);
							$dbUser->setField('redPacket', ($totalRedpacket - $lastAmount));
							$dbUser->update();
						}
					}
				} else {
					throw new \Sooh\Base\ErrException('user not exists');
				}
			} else {
				throw new \Sooh\Base\ErrException('phone not exists');
			}
			return $this->returnOK('success');
		} catch (\Exception $e) {
			return $this->returnError($e->getMessage(), $e->getCode());
		}
	}

	/**
	 * 测试推送
	 * @input string userId 用户ID
	 * @input string msgType 透传消息自定义类型
	 * @input string msgContent 透传消息自定义内容
	 * @input string isNotify 是否发送通知（与消息不可同时发送）
	 * @input string isMessage 是否发送消息（与通知不可同时发送）
	 * @input string notification 通知体
	 * @input string platform 推送平台
     * @input string msgRet msgRet
	 * @throws \Sooh\Base\ErrException
	 */
	public function pushAction() {
		$userId       = $this->_request->get('userId');
		$msgType      = $this->_request->get('msgType', 'testType');
		$msgContent   = $this->_request->get('msgContent', 'Push msg Test');
		$isNotify     = $this->_request->get('isNotify');
		$isMessage    = $this->_request->get('isMessage');
		$notification = $this->_request->get('notification', 'Push Test~');
		$platform     = $this->_request->get('platform', 'all');
        $msgRet       = $this->_request->get('msgRet');
		if (empty($userId)) {
			$userId = \Sooh\Base\Session\Data::getInstance()->get('accountId');
			if (empty($userId)) {
				return $this->returnError('not found user');
			}
		}

		if(empty($isNotify) && empty($isMessage)) {
			return $this->returnError('please select notify or message');
		}

		try {
			if ($isMessage) {
				$_msgRet = [
					'type' => $msgType,
					'msg' => $msgContent,
				];
				$pushContent = json_encode($msgRet);
//                $pushContent = $msgRet;
				\Lib\Services\Push::getInstance()->push($platform, $userId, null, $pushContent);

//				$_msgRet = [
//					'type'=>'activebonus','step'=>2,'score'=>22
//				];
//				$pushContent = json_encode($_msgRet);
//				\Lib\Services\Push::getInstance()->push($platform, $userId, null, $pushContent);//旧版本
//				\Lib\Services\Push::getInstance()->push($platform, $userId, null, $_msgRet);//标准版本
			}
			if($isNotify) {
				\Lib\Services\Message::getInstance()->push($userId, $notification, $platform);
			}

			////			$notification = 'notifiTest1';
			////			$notification = ['default' => 'notifiTest2'];
//			$notification = ['default' => 'notifiTest3',
//			                 'android' => ['Acontent', 'Atitle', 1, ['k1' => 'v1', 'k2' => 'v2']],
//							 'ios' => ['Icontent', 'happy', 1, true, null, 'THE-CATEGORY']
//			];
//			\Lib\Services\Push::getInstance()->push($platform ? : 'all', $userId, $notification ? : null, $msg ? : 'JPush Test!~');
			return $this->returnOK('success');
		} catch (Exception $e) {
			return $this->returnError($e->getMessage(), $e->getCode());
		}
	}

	/**
	 * 发送消息
	 * @input string sendId sendId
	 * @input string receiverId receiverId
	 * @input string notification notification
	 * @input string message message
	 * @input integer type type
	 * @input integer isPush true|false
	 * @output {"code":200,"msg":"***"}
	 * @errors {"code":400,"msg":"***"}
	 */
	public function addMsgAction() {
		$sendId = $this->_request->get('sendId');
		$receiverId = $this->_request->get('receiverId');
		$notification = $this->_request->get('notification');
		$message = $this->_request->get('message');
		$type = $this->_request->get('type');
		$isPush = $this->_request->get('isPush');

		try {
			$message = \Lib\Services\Message::getInstance()->add($sendId, $receiverId, $type, 'dev/testMsg', $message, null, empty($isPush) ? false : true);
			return $this->returnOK('success');
		} catch (Exception $e) {
			return $this->returnError($e->getMessage(), $e->getCode());
		}
	}

	/**
	 * 给用户发一条自定义消息
	 * @input integer receiverId 接收者的ID-必填
	 * @input string content 消息内容本身-必填
	 * @input string title 消息标题-必填
	 * @input string type 消息内容类型-必填
	 * @input string extras JSON-格式的可选参数-默认为{"k1":"v1"}
	 */
	public function sendMsgToUserAction() {
		$receiverId = $this->_request->get('receiverId');
		$content = $this->_request->get('content');
		$title = $this->_request->get('title');
		$type = $this->_request->get('type');
		$extras = $this->_request->get('extras', '{"k1":"v1"}');
		if (!empty($extras)) {
			if (json_decode($extras, true)) {
				$extras = json_decode($extras, true);
			} else {
				return $this->returnError('extras error');
			}
		}

		if (empty($receiverId) || empty($content) || empty($title) || empty($type)) {
			return $this->returnError('params error');
		}

		$message = [$content, $title, $type, $extras];

		try {
			$message = \Lib\Services\Message::getInstance()->add(0, $receiverId, 1, '测试自定义消息', ['message' => $message]);
			return $this->returnOK('success');
		} catch (Exception $e) {
			return $this->returnError($e->getMessage(), $e->getCode());
		}
	}

	/**
	 * 全局广播
	 * @input string platform platform
	 * @input string notification notification(json)
	 * @output {"code":200,"msg":"success"}
	 * @errors {"code":400,"msg":"error"}
	 * @author LTM <605415184@qq.com>
	 */
	public function announceAction() {
		$platform = $this->_request->get('platform');
		$notification = $this->_request->get('notification');

		//test
		$platform = 'all';
		$notification = [
			'default' => '这是一条全局广播，App即将上线喽，注意看你的平台信息',
		    'android' => ['你是android，App即将上线喽', 'App即将上线喽!', 1, ['k1' => 'v1', 'k2' => 'v2']],
		    'ios' => ['你是IOS，App即将上线喽!', 'happy_sound', 99, true, ['k1' => 'v1', 'k2' => 'v2'], null],
		    'winphone' => ['你是1% ^_^!!，App即将上线喽!', 'App即将上线喽!', 'homepage', ['k1' => 'v1', 'k2' => 'v2']],
		];
		$title = is_array($platform) ? '全局广播notify:' . json_encode($platform) : '全局广播notify:' . $platform;

		if ($notification) {
			try {
				$ret = \Lib\Services\Message::getInstance()->add('0', $platform, 1, $title, $notification, null, true);
				return $this->returnOK('success');
			} catch (Exception $e) {
				return $this->returnError($e->getMessage(), $e->getCode());
			}
		}
		return $this->returnError('error');
	}

	/**
	 * 广播一条自定义消息
	 */
	public function broadcastMsgAction() {
		$platform = 'all';
		$message = ['这是一条自定义消息-广播', '我的自定义的title', 'msg_type1', ["key1"=>"value1", "key2"=>"value2"]];
		try {
			$ret = \Lib\Services\Message::getInstance()->add('0', $platform, 1, '全局广播自定义消息', ['message' => $message], null, true);
			return $this->returnOK('success');
		} catch (Exception $e) {
			return $this->returnError($e->getMessage(), $e->getCode());
		}
	}

	/**
	 * 获取一个用户的所有信息
	 * @input string phone phone
	 * @input string cameFrom cameFrom
	 * @output {"code":200,"msg":"***"}
	 * @errors {"code":400,"msg":"***"}
	 * @throws ErrorException
	 */
	public function userInfoAction() {
		$phone    = $this->_request->get('phone');
		$cameFrom = $this->_request->get('cameFrom', 'phone');

		$dbLogin = \Sooh\DB\Cases\AccountAlias::getCopy([$phone, $cameFrom]);
		$dbLogin->load();
		if ($dbLogin->exists()) {
			$ret['phone']  = $phone;
			$userId        = $dbLogin->getField('accountId');
			$ret['userId'] = $userId;

			$dbAccount = \Sooh\DB\Cases\AccountStorage::getCopy($userId);
			$dbAccount->load();
			if ($dbAccount->exists()) {
				$ret['password']     = $dbAccount->getField('passwd');
				$ret['passwordSalt'] = $dbAccount->getField('passwdSalt');
				$ret['pwdVer']       = $dbAccount->getField('pwdVer');
				$ret['deviceId']     = $dbAccount->getField('deviceId');
				$ret['regYmd']       = $dbAccount->getField('regYmd');
				$ret['regHHiiss']    = $dbAccount->getField('regHHiiss');
				$ret['regClient']    = $dbAccount->getField('regClient');
				$ret['regIP']        = $dbAccount->getField('regIP');
				$ret['dtForbidden']  = $dbAccount->getField('dtForbidden');
				$ret['loginFailed']  = $dbAccount->getField('loginFailed');
				$ret['nickname']     = $dbAccount->getField('nickname');
				$ret['lastIP']       = $dbAccount->getField('lastIP');
				$ret['lastDt']       = $dbAccount->getField('lastDt');
				$ret['contractId']   = $dbAccount->getField('contractId');
				$ret['customData']   = $dbAccount->getField('customData');
			}

			$this->_view->assign('data', $ret);
			return $this->returnOK('success');
		} else {
			return $this->returnError('server busy');
		}
	}

    /**
     * 测试页面-显示表结构
     * @throws ErrorException
     * @input string db 数据库名字
     * @input string tb 表名
     */
    public function showTableAction(){
        $dbName = $this->_request->get('db');
        $tbName = $this->_request->get('tb');
        $showType = 'echo';
        \Sooh\Base\Ini::getInstance()->viewRenderType($showType);
        echo "<style>.button{font-size:13px;color:#808080;display:inline-block;border: 1px solid lightgray;padding: 3px;margin: 2px;}</style>";
        echo "<style>.button:hover{border-color: #E25453;background-color: #E25453;color: #ffffff}</style>";
        $db = \Sooh\DB\Broker::getInstance();
        if(empty($dbName))
        {
            $ext = ",'db_oauth' ";
            $dbName = 'db_p2p';
        }
        if(empty($tbName))
        {
            //$sql = "select * from information_schema.COLUMNS WHERE TABLE_SCHEMA = '$dbName' ORDER BY TABLE_NAME";// AND TABLE_NAME = '$tbName' ";
            $sql = "select a.TABLE_SCHEMA,a.TABLE_NAME,b.TABLE_COMMENT,a.COLUMN_NAME,a.DATA_TYPE,a.CHARACTER_MAXIMUM_LENGTH,a.COLUMN_DEFAULT,a.COLUMN_COMMENT from information_schema.COLUMNS a,information_schema.TABLES b WHERE a.TABLE_SCHEMA in ('$dbName'$ext)  and a.TABLE_SCHEMA = b.TABLE_SCHEMA and a.TABLE_NAME = b.TABLE_NAME ORDER BY TABLE_NAME";
        }else{
            //$sql = "select * from information_schema.COLUMNS WHERE TABLE_SCHEMA = '$dbName'  AND TABLE_NAME = '$tbName' ORDER BY TABLE_NAME ";
            $sql = "select a.TABLE_SCHEMA,a.TABLE_NAME,b.TABLE_COMMENT,a.COLUMN_NAME,a.DATA_TYPE,a.CHARACTER_MAXIMUM_LENGTH,a.COLUMN_DEFAULT,a.COLUMN_COMMENT from information_schema.COLUMNS a,information_schema.TABLES b WHERE a.TABLE_SCHEMA in ('$dbName'$ext)  and a.TABLE_NAME = '$tbName' and a.TABLE_SCHEMA = b.TABLE_SCHEMA and a.TABLE_NAME = b.TABLE_NAME ORDER BY TABLE_NAME";
        }

        $result = $db->execCustom(['sql'=>$sql]);
        $rs = $db->fetchAssocThenFree($result);
        $this->_view->assign('a',$rs);
        //header("Content-type:text/html;charset=utf-8");
        $tempTbname = '';
        if($showType=='echo')
        {
            echo '<meta charset="utf-8">';
            echo "<style>*{text-decoration: none}td{border: 1px solid lightgray;padding: 5px;font-size: 12px}table{border: 1px solid lightgray;margin-top: 10px;margin-left: 50px;}</style>";
            echo "<div>@input string db 数据库名字</div>";
            echo "<div>@input string tb 表名字</div>";
            $num = 0;
            $tableArr = array(
                array('NAME'=>'订单','TABLE_NAME'=>'tb_investment_0'),
                array('NAME'=>'券','TABLE_NAME'=>'tb_vouchers_0'),
                array('NAME'=>'钱包流水','TABLE_NAME'=>'tb_wallettally_0'),
                array('NAME'=>'标的','TABLE_NAME'=>'tb_wares_0'),
                array('NAME'=>'用户','TABLE_NAME'=>'tb_user_0'),
                array('NAME'=>'银行卡','TABLE_NAME'=>'tb_user_bankcard_0'),
                array('NAME'=>'充值提现','TABLE_NAME'=>'tb_recharges_0'),
                array('NAME'=>'日历','TABLE_NAME'=>'tb_calendar'),
            );
            $name = '';
            foreach($tableArr as $K=>$v)
            {
                if($name!=$v['TABLE_NAME'])
                {
                    $num++;
                    echo '<a class="button" href="index.php?__=index/dev/showtable&tb='.$v['TABLE_NAME'].'">'.($v['NAME']?$v['NAME']:$v['TABLE_NAME']).'</a>';
                    if($num%3==0)echo "<br>";
                }
                $name = $v['TABLE_NAME'];
            }
            foreach($rs as $k=>$v)
            {
                if(substr($v['TABLE_NAME'],-1)!=0)continue;
                if($tempTbname!=$v['TABLE_NAME'])
                {
                    if(!empty($tempTbname))echo "</table>";
                    echo "<table cellspacing=0 cellpadding=0>";
                    echo "<tr style='background-color: #eaeaea'><td colspan='2'>".$v['TABLE_SCHEMA'].".".$v['TABLE_NAME']."</td><td colspan='2'>".$v['TABLE_COMMENT']."</td></tr>";
                    echo "<tr style='background-color: #eaeaea'><td>字段名</td><td>类型</td><td>默认值</td><td>备注</td></tr>";
                }
                echo "<tr><td>".$v['COLUMN_NAME']."</td><td>".$v['DATA_TYPE']."(".$v['CHARACTER_MAXIMUM_LENGTH'].")</td><td>".($v['COLUMN_DEFAULT']!==''?$v['COLUMN_DEFAULT']:'\'\'')."</td><td>".$v['COLUMN_COMMENT']."</td></tr>";
                $tempTbname = $v['TABLE_NAME'];
            }
        }
    }
    /**
     * 测试页面-显示各模块地址
     */
    public function showUrlAction()
    {
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        array_key_exists('xxx','xxx');
        $arrPage = array(
            '购买接口测试'=>\Sooh\Base\Tools::uri(null,'test1','dev'),
            '接口文档'=>\Sooh\Base\Tools::uri(null,'apidoc','dev'),
            '登录'=>\Sooh\Base\Tools::uri(null,'loginone','dev'),
            '登出'=>\Sooh\Base\Tools::uri(null,'loginout','dev'),
            '标的列表'=>\Sooh\Base\Tools::uri(['__VIEW__'=>'json'],'byshelf','financing'),
            '标的信息'=>\Sooh\Base\Tools::uri(['__VIEW__'=>'json'],'detail','financing'),
            '后台'=>\Sooh\Base\Tools::uri([],'index','manager','manage'),
            //'标的状态重置'=>\Sooh\Base\Tools::uri(['status'=>11],'testChangeStatus','wares','manage'),
            '显示表字典'=>\Sooh\Base\Tools::uri(['tb'=>'tb_wares_0'],'showtable','dev'),
            //'添加一条标的'=>\Sooh\Base\Tools::uri(null,'addWares','wares','manage'),
            '添加一个券'=>\Sooh\Base\Tools::uri(['type'=>8],'addVoucher','dev'),
            //'测试回款计划表'=>\Sooh\Base\Tools::uri(null,'test','orders'),
            '订单详情'=>\Sooh\Base\Tools::uri(['orderId'=>'1014460285913862916'],'detail','orders'),
            '个人账户'=>\Sooh\Base\Tools::uri(['__VIEW__'=>'json'],'info','user'),
            '我的券'=>\Sooh\Base\Tools::uri([],'myVouchers','user'),
            '我的红包'=>\Sooh\Base\Tools::uri(['__VIEW__'=>'json'],'redPacket','user'),
            '钱包流水'=>\Sooh\Base\Tools::uri([],'wallettallyList','user'),
            '订单列表'=>\Sooh\Base\Tools::uri(['cmd'=>''],'myInvest','user'),
            '绑卡'=>\Sooh\Base\Tools::uri([],'bindcard','user'),
            '充值'=>\Sooh\Base\Tools::uri([],'recharge','user'),
            //'充值回调'=>\Sooh\Base\Tools::uri(['cmd'=>'rechargeResult','service'=>'PayGW'],'call','service',''),
            '提现'=>\Sooh\Base\Tools::uri([],'withdraw','user'),
            //'实名认证'=>\Sooh\Base\Tools::uri([],'setrealname','user'),
            //'测试远程支付网关'=>\Sooh\Base\Tools::uri([],'testGW','dev'),
            //'自我payPW测试'=>\Sooh\Base\Tools::uri(['cmd'=>'rechargeResult'],'call','service'),
            '充值自我回调'=>\Sooh\Base\Tools::uri(['orderId'=>''],'testRechargeResult','user'),
            '创一个用户并登录'=>\Sooh\Base\Tools::uri([],'testadduser','dev'),
            '创建365条日历'=>\Sooh\Base\Tools::uri([],'testAddCalendar','dev'),
            //'解绑银行卡'=>\Sooh\Base\Tools::uri(['userId'=>''],'testUnbinding','dev'),
            //'清空支付密码'=>\Sooh\Base\Tools::uri(['userId'=>''],'testClearPaypwd','dev'),
            '还款一览'=>\Sooh\Base\Tools::uri(['userId'=>''],'testReturnPlan','dev'),
            //'还款批量更新'=>\Sooh\Base\Tools::uri([],'testUpdateReturnPlan','dev'),
            '我的还款计划'=>\Sooh\Base\Tools::uri([],'myReturnPlan','user'),
            //'批量更新订单的shelfId'=>\Sooh\Base\Tools::uri([],'investShelfId','dev'),
            '我的收益产品'=>\Sooh\Base\Tools::uri(['cmd'=>'service'],'myInvest','user'),
            //'payGW回调测试'=>\Sooh\Base\Tools::uri(['service'=>''],'callback','dev'),
            //'支付全套测试'=>\Sooh\Base\Tools::uri(['service'=>''],'payTest','dev'),
            //'报错测试'=>\Sooh\Base\Tools::uri([],'testShowError','dev'),
            '日志查询'=>\Sooh\Base\Tools::uri([],'logview','dev'),
            '清空所有还款操作'=>\Sooh\Base\Tools::uri([],'clearReturnPlan','dev'),
            '上架标的'=>\Sooh\Base\Tools::uri([],'waresOpen','dev'),
            '充值订单详情'=>\Sooh\Base\Tools::uri(['ordersId'=>''],'rechargeDetail','user'),
            '查看日志'=>\Sooh\Base\Tools::uri(['key'=>''],'tgh_showLog','dev'),
            '工具箱'=>\Sooh\Base\Tools::uri([],'tgh_tools','dev'),
            '手动回款回调'=>\Sooh\Base\Tools::uri(['ordersId'=>'','sn'=>''],'testReturnFund','dev'),

            //'购买页面'=>\Sooh\Base\Tools::uri(['waresId'=>'1448433155857725'],'addOrder','user'),
        );
        $arrJson = array();
        foreach($arrPage as $k=>$v)
        {
            $arrPage[$k] = \Sooh\Base\Ini::getInstance()->get('uriBase')['www'].$v;

            $arrJson[$k] = $arrPage[$k].'&__VIEW__=json';

        }
        $ignoreArr = array(
            '后台','标的状态重置','显示表字典','添加一条标的','添加一个券','测试回款计划表',
            '购买接口测试',
        );
        foreach($ignoreArr as $v)
        {
            unset($arrJson[$v]);
        }
        $arr = array(
            'page'=>$arrPage,
            //'json'=>$arrJson,
        );
        //$arr['server'] = $_SERVER;
        $this->_view->assign('本地',$arr);

    }

    /**
     * 测试页面-登录一个账户
     * @input string accountId
     */
    public function loginOneAction()
    {
//        $this->onLogin([], '', '');
//        return $this->returnOK('success');
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        if(\Sooh\Base\Ini::getInstance()->get('noGW')){
            $userId = 90003837339748;
        }else{
            $userId = 888898182406;
        }
        $accountId = $this->_request->get('userId');//90003837339748  //52389993716807
        if(empty($accountId)){
            $accountId = $userId;
        }else{
            if(strlen($accountId)==11){
                $user = \Prj\Data\User::getByPhone($accountId);
                if(empty($user)){
                    return $this->returnError();
                }else{
                    $accountId = $user->getField('userId');
                }
            }
        }
        $this->user = \Prj\Data\User::getCopy($accountId);
        $this->user->load();
        if(!$this->user->exists())
        {
            $this->returnError('登录失败');
        }
        else
        {
            $this->returnOK('登录成功');
        }
        $accountInfo = $this->user->dump();
        $sess = \Sooh\Base\Session\Data::getInstance();
        $sess->set('accountId', $accountInfo['userId'],86400);
        $sess->set('nickname', $accountInfo['nickname']);
        $sess->set('tokenRefresh','xxx');
        $sess->set('pwdVer','0');
        $sess->set('tokenAccess', 'yyy');
        $uni = \Lib\Misc\UniqueOp::createFor($this->_request->get('waresId','1446519809479610'),'orders/add');
        $this->_view->assign('accountId',$sess->get('accountId'));
        $this->_view->assign('pwdVer',$sess->get('pwdVer'));
        $this->_view->assign('nickname',$sess->get('nickname'));
        $this->_view->assign('uni',$uni);

    }
    /**
     *
     */
    public function testGWAction()
    {
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $url = \Sooh\Base\Ini::getInstance()->get('payGW')[0];//"?service=PayGW&cmd=binding&SN=1447827430425753516&userId=583516&realname=%E6%B1%A4%E9%AB%98%E8%88%AA&idType=1&idCode=340823199311284918&bankId=icbc&bankCard=6222021304002871932&phone=13262798028&userIP=180.173.45.210&dt=1447827430&sign=a8eb7660685cac26be316bf526c8d002";
        //$url = "http://www.baidu.com";

        $rest = \Sooh\Base\Tools::httpGet($url);
        error_log('code last='.\Sooh\Base\Tools::httpCodeLast().' ret='.$rest);


        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT,20);
        $result = curl_exec($ch);
        curl_close($ch);
        var_log($result,'>>>>>>>>>>>>>>>>');
        if(empty($result))
        {
            $this->returnError($url);
        }
        else
        {
            $this->returnOK();
            $this->_view->assign('result',$result);
        }
    }

    /**
     * 测试页面-登出
     */
    public function loginOutAction()
    {
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $sess = \Sooh\Base\Session\Data::getInstance();
        $sess->set('accountId','');
        //$_COOKIE['SoohSessId'] = '';
        $this->returnOK('登出成功');

    }

    /**
     * 测试页面-添加一张兑换券
     * @input string userId
     * @input string type 8/4/2 空：随机
     */
    public function addVoucherAction()
    {
        if(!$this->debug())return;
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $userId = $this->_request->get('userId',90003837339748);
        $arr = array(
            \Prj\Consts\Voucher::type_real,
            \Prj\Consts\Voucher::type_fake,
            \Prj\Consts\Voucher::type_yield,
        );
        $k = rand(0,2);
        $type = $this->_request->get('type',$arr[$k]);
        if($type==\Prj\Consts\Voucher::type_yield)
        {
            $amount = 1;
        }
        else
        {
            $amount = 2000;
        }

        if(!in_array($type,$arr))$type = \Prj\Consts\Voucher::type_fake;
        \Prj\Misc\OrdersVar::$introForUser = '首投奖励';
        \Prj\Misc\OrdersVar::$explain1 = '限APP投资';
        \Prj\Misc\OrdersVar::$explain2 = '消费满100元使用';

        $tmp = \Prj\Data\Vouchers::newForUser($userId,$type,$amount);
        if($tmp->exists())
        {
            $this->_view->assign('ID',$tmp->getField('voucherId'));
            $this->returnOK('新增成功');
        }
        $voucher = $tmp->dump();
        $this->_view->assign('voucher',$voucher);
    }

    /**
     * 测试-用户一览
     */
    public function userListAction()
    {
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $dt = time();
        $args = json_encode([
            'sn'=>rand(10000,99999),
            'waresId'=>'1449475357591957',
            'status'=>'success',
            'msg'=>'',
        ]);
        $data = [
            'dt'=>$dt,
            'sign'=>md5($dt . 'asgdfw4872hfhjksdhr8732trsj'),
            'service'=>'payGW',
            'cmd'=>'confirmResult',
            'args'=>$args,
        ];
        $url = "http://".$_SERVER['HTTP_HOST'].\Sooh\Base\Tools::uri($data,'call','service');
        $this->_view->assign('url',$url);
    }

    /**
     * 测试-解绑银行卡
     * By Hand
     */
    public function testUnbindingAction()
    {
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        if (!$this->debug()) return;
        $this->_view->assign('notice','解绑银行卡');
        $userId = $this->_request->get('userId');
        if(empty($userId))return $this->returnError('请输入用户ID');
        $user = \Prj\Data\User::getCopy($userId);
        $user->load();
        $user->setField('tradePwd','');
        $user->setField('salt','');
        $user->setField('ymdBindcard','');
        $user->setField('ymdFirstBuy','');
        $user->setField('ymdLastBuy','');
        $user->setField('ymdFirstCharge','');
        $user->update();
        $rs = \Prj\Data\BankCard::getList($userId,['statusCode'=>\Prj\Consts\BankCard::enabled]);
        foreach($rs as $v)
        {
            $bank = \Prj\Data\BankCard::getCopy($v['orderId']);
            $bank->load();
            $bank->setField('statusCode',\Prj\Consts\BankCard::abandon);
            $bank->update();
            $new[] = $bank->dump();
        }
        $this->_view->assign('rs',$new);
        $this->returnOK();
    }

    /**
     * 测试-清支付密码
     * By Hand
     */
    public function testClearPaypwdAction()
    {
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        if (!$this->debug()) return;
        $this->_view->assign('notice','清空支付密码');
        $userId = $this->_request->get('userId');
        if(empty($userId))return $this->returnError('请输入用户ID');
        $user = \Prj\Data\User::getCopy($userId);
        $user->load();
        $user->setField('tradePwd','');
        $user->setField('salt','');
        $user->update();
        $this->returnOK();
    }

    /**
     * 测试-添加一个用户并且登录
     * By Hand
     */
    public function testAddUserAction()
    {
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        if (!$this->debug()) return;
        $rand = rand(10000000,99999999);
        $userId = '8888'.$rand;
        $user = \Prj\Data\User::createNew($userId, '132'.$rand, '', '', '', '', '');
        $user->load();
        $user->setField('tradePwd',md5('123456'.'abcd'));
        $user->setField('salt','abcd');
        $user->setField('wallet','999999');
        $user->setField('isSuperUser','1');
        $user->update();
        $this->_view->assign('userId', $user->getField('userId'));
        $sess = \Sooh\Base\Session\Data::getInstance();
        $sess->set('accountId', $user->getField('userId'),9999);
        $sess->set('nickname', $user->getField('nickname'));
        var_log($sess,'session>>>>>>>>>>>>>>>>>>>>>');
        var_log($sess->get('accountId'),'accountId>>>>>>>>>>>>>>');
    }


    /**
     * 测试=添加365条日历信息
     */
    public function testAddCalendarAction()
    {
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        if (!$this->debug()) return;
        $cal = \Prj\Data\Calendar::addYear();
        if($cal)
        {
            $this->returnOK();
            $this->_view->assign('result',$cal);
        }
        else
        {
            $this->returnError();
        }
    }

    /**
     * 测试-起息测试
     */
    public function testReturnPlanAction()
    {
        //accountId
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        if (!$this->debug()) return;
        $orderId = $this->_request->get('orderId','1014480918022231726');
        $userId = $this->_request->get('userId',\Sooh\Base\Session\Data::getInstance()->get('accountId'));
        $data['userId'] = $userId;

        $user = \Prj\Data\User::getCopy($userId);
        $invest = \Prj\Data\Investment::getCopy($orderId);

        $user->load();
        $invest->load();
        $waresId = $invest->getField('waresId');
        $data['waresId'] = $waresId;
        $ware = \Prj\Data\Wares::getCopy($waresId);
        $ware->load();

        $returnPlan = \Prj\ReturnPlan\Std01\ReturnPlan::calendar($invest);
        $data['dtStart'] = $returnPlan->getDtStart($ware);
        $data['dtStartYmd'] = $returnPlan->dtStartYmd;
        $data['plan'] = $returnPlan->decode();
        $this->_view->assign('data',$data);
        $this->returnOK();
    }

    /**
     * 支付全流程自动化测试
     */
    public function payTestAction()
    {
        $r['waresId'] = $this->_request->get('waresId');
        if(empty($r['waresId']))
        {
            $o = \Prj\Data\Wares::getCopy('');
            $db = $o->db();
            $tb = $o->tbname();
            $rs = $db->getRecord($tb,['waresId'],['statusCode'=>\Prj\Consts\Wares::status_open],'rsort timeStartPlan');
            var_log($rs,'获取最新的在售标的>>>>>>>>>>');
            $r['waresId'] = $rs['waresId'];
        }

        $wares        = \Prj\Data\Wares::getCopy($r['waresId']);
        $wares->load();
        $r['remain']     = $wares->getField('remain');
        $r['uniqueOpId'] = \Lib\Misc\UniqueOp::createFor($r['waresId'], 'orders/add');

        //$vouchers        = array_merge($this->_myVouchers()['redPacketList'],$this->_myVouchers()['voucherList']);
        $this->user = \Prj\Data\User::getCopy('90003837339748');
        $this->user->load();
        $wallet = $this->user->getField('wallet');
        //$this->_view->assign('v', $vouchers);
        $this->_view->assign('r', $r);
        $this->_view->assign('wallet', $wallet);
    }

    public function testShowErrorAction()
    {
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        implode(',','aaa');
    }

    /**
     * 测试-批量更新订单的回款计划
     */
    public function testUpdateReturnPlanAction()
    {
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        if (!$this->debug()) return;
        $o = \Prj\Data\Investment::getCopy('');
        $db = $o->db();
        $tb = $o->tbname();
        $rs = $db->getRecords($tb,'*',['orderStatus'=>\Prj\Consts\OrderStatus::$running]);
        foreach($rs as $k=>$v)
        {
            $invest = \Prj\Data\Investment::getCopy($v['ordersId']);
            $invest->load();
            $plan = \Prj\ReturnPlan\Std01\ReturnPlan::calendar($invest);
            if(empty($plan))continue;
            //var_log('update_'.$v['ordersId'].'...............');
            $calendar = $plan->decode();
            $returnNext = $plan->getYmdNext();
            $old = $invest->getField('returnPlan');
            if(empty($old))
            {
                $data[] = $invest->getField('ordersId');
            }
            $invest->setField('returnPlan',$calendar);
            $invest->setField('returnNext',$returnNext);
            $invest->update();
        }
        $this->_view->assign('data',$data);
        $this->returnOK();
    }
    /**
     * 批量更新订单shelfID
     */
    public function investShelfIdAction()
    {
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $o = \Prj\Data\Investment::getCopy('');
        $db = $o->db();
        $tb = $o->tbname();
        $rs = $db->getRecords($tb,'ordersId,waresId');
        if(!empty($rs))
        {
            foreach($rs as $v)
            {
                $invest = \Prj\Data\Investment::getCopy($v['ordersId']);
                $wares = \Prj\Data\Wares::getCopy($v['waresId']);
                $wares->load();
                $invest->load();
                if(!$wares->exists())
                {
                    $data[] = '标的不存在';
                    continue;
                }
                $shelfId = $invest->getField('shelfId');
                var_log($shelfId,'shelf>>>>>>>>>>');
                if(empty($shelfId))
                {
                    $data[] = $v['ordersId'];
                }
                $invest->setField('shelfId',$wares->getField('shelfId'));
                $invest->update();
            }
        }
        $this->_view->assign('data',$data);
    }

    public function callbackAction()
    {
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $args = [
            'orderId'=>1,
        ];
        $cmd = $this->_request->get('service');
        if(empty($cmd))$cmd = 'withdrawResult';
        $args = json_encode($args);
        $url = \Sooh\Base\Tools::uri([
            'service'=>'payGW',
            'cmd'=>$cmd,
            'args'=>$args,
        ],'call','service');
        $data['url'] = 'http://'.$_SERVER['SERVER_NAME'].$url;
        $this->_view->assign('data',$data);
    }

    /**
     * 上架标的
     */
    public function waresOpenAction()
    {
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $this->_view->assign('data',$this->_shangJia());
        $this->returnOK();
    }


    protected function _shangJia()
    {
        $wares = \Prj\Data\Wares::getCopy('');
        $db = $wares->db();
        $tb = $wares->tbname();
        $where = [
            'statusCode'=>\Prj\Consts\Wares::status_ready,
            'timeStartPlan['=>date('YmdHis'),
        ];
        $rs = $db->getRecords($tb,'waresId',$where);
        var_log($where,'where>>>');
        var_log($rs,'rs>>>');
        if(!empty($rs))
        {
            foreach($rs as $k=>$v)
            {
                $ware = \Prj\Data\Wares::getCopy($v['waresId']);
                $ware->load();
                $ware->setField('statusCode',\Prj\Consts\Wares::status_open);
                $ware->update();
            }
            return $rs;
        }
        else
        {
            return [];
        }
    }

    protected function _testRechargeResult($orderId)
    {
        $recharge = \Prj\Data\Recharges::getCopy($orderId);
        $recharge->load();
        $amount = $recharge->getField('amount');
        $args   = json_encode(['orderId' => $orderId, 'amount' => $amount, 'status' => 'success']);
        $time   = time();
        $sign   = md5($time . 'asgdfw4872hfhjksdhr8732trsj');
        //RpcConfig
        $url = \Sooh\Base\Ini::getInstance()->get('RpcConfig')['urls'][0] . "&service=PayGW&cmd=rechargeResult&args=" . $args . "&dt=" . $time . "&sign=" . $sign;
        var_log($url, __CLASS__ . '479>>>>>>>>>');
        $result = file_get_contents($url);
        $test = json_decode($result,true);
        $test['url'] = $url;
        return $test;
    }

    public function rechargeResultAction()
    {
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $recharge = \Prj\Data\Recharges::loopAll(['amountFlg'=>\Prj\Consts\OrderType::recharges,'orderStatus'=>\Prj\Consts\OrderStatus::waiting]);
        $data = [];
        if(!empty($recharge))
        {
            foreach($recharge as $v)
            {
                $data[] = $this->_testRechargeResult($v['ordersId']);
            }
        }
        $this->_view->assign('data',$data);
        $this->returnOK();
    }
	//清空所有还款计划的操作
    public function clearReturnPlanAction()
    {
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $invests = \Prj\Data\Investment::loopFindRecords(["returnPlan!"=>""]);
        foreach($invests as $v)
        {
            $tempRp = \Prj\ReturnPlan\Std01\ReturnPlan::createPlan($v['returnPlan']);
            foreach($tempRp->calendar as $vv)
            {
                if(!empty($vv['status']))
                {
                    $tempRp->updatePlanByMonth('sn','',$vv['planDateYmd']);
                    $tempRp->updatePlanByMonth('realDateYmd',0,$vv['planDateYmd']);
                    $tempRp->updatePlanByMonth('realPayAmount',0,$vv['planDateYmd']);
                    $tempRp->updatePlanByMonth('realPayInterest',0,$vv['planDateYmd']);
                    $tempRp->updatePlanByMonth('status',0,$vv['planDateYmd']);
                    $tempRp->updatePlanByMonth('exp','',$vv['planDateYmd']);
                    $data[] = $tempRp->getPlanByMonth($vv['planDateYmd']);
                    $invest = \Prj\Data\Investment::getCopy($v['ordersId']);
                    $invest->load();
                    $invest->setField('returnPlan',$tempRp->decode());
                    $invest->update();
                }
            }
        }
        $wares = \Prj\Data\Wares::loopFindRecords(['returnPlan!'=>'','payStatus!'=>0]);
        foreach($wares as $v)
        {
            $tempRp = \Prj\ReturnPlan\All01\ReturnPlan::createPlan($v['returnPlan']);
            foreach($tempRp->calendar as $vv)
            {
                if(!empty($vv['status']))
                {
                    $tempRp->updatePlanByMonth('sn','',$vv['planDateYmd']);
                    $tempRp->updatePlanByMonth('realDateYmd',0,$vv['planDateYmd']);
                    $tempRp->updatePlanByMonth('realPay',0,$vv['planDateYmd']);
                    $tempRp->updatePlanByMonth('status',0,$vv['planDateYmd']);
                    $tempRp->updatePlanByMonth('exp','',$vv['planDateYmd']);
                    $data1[] = $tempRp->getPlanByMonth($vv['planDateYmd']);
                    $ware = \Prj\Data\Wares::getCopy($v['waresId']);
                    $ware->load();
                    $ware->setField('returnPlan',$tempRp->decode());
                    $ware->update();
                }
            }
            if(!empty($v['payStatus']))
            {
                $ware = \Prj\Data\Wares::getCopy($v['waresId']);
                $ware->load();
                $ware->setField('payStatus',0);
                $ware->setField('paySN',0);
                $ware->update();
            }
        }
        $this->_view->assign('data',$data);
        $this->_view->assign('data1',$data1);
    }

	public function testReadConfAction() {
//		$obj = new \Prj\ReadConf();
//		$conf = $obj->run();
//
//		die();
	}

	public function testuserconfAction() {
		$tag = 'reg_name';
		$brand = '小虾理财';
		$code = 666666;
		$num_time = 15;
		$msgTitle = '注册短信';
		$msgType = 1;

		$ret = \Prj\Message\Message::run(
			['event' => $tag, 'brand' => $brand, 'code' => $code, 'num_time' => $num_time],
			['phone' => '18616700069', 'userId' => '31357270704817', 'msgTitle' => $msgTitle, 'msgType' => $msgType]
		);

		var_dump($ret);
		die();
	}

	public function testInvestmentVoucherAction() {
		//发母红包
//		try {
//			$ret = (new \Prj\Items\RedPacketForShare())->giveParent('81568478941117', 5000000);
//		} catch(\ErrException $e) {
//			var_dump($e);
//		}
		//领取子红包
//		try {
//			$ret = (new \Prj\Items\RedPacketForShare())->giveChild('81568478941117', '9014568125432611117');
//		} catch(\ErrException $e) {
//			var_dump($e);
//		}

//		var_log(\Prj\Data\Config::get('wx', 'group'));
		var_log(\Prj\Data\User::getMineInvitedTree(\Sooh\Base\Session\Data::getInstance()->get('accountId')));
	}

    public function testDayCheckAction(){
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');

       // $o = \Prj\Check\DayRecharges::saveData('20160114');
       // $o = \Prj\Check\DayWithdraw::saveData('20160114');
        //$o = \Prj\Check\DayBuy::saveData('20160114');
        //$o = \Prj\Check\DayLoan::saveData('20160114');
       $o = \Prj\Check\DayPayback::saveData('20160114');
        $o = \Prj\Check\DayPaysplit::saveData('20160114');
//        $o = \Prj\Check\DayLoan::saveData('20151210');
//        $o = \Prj\Check\DayPayback::saveData('20151210');

        //$o = \Prj\Check\DayRecharges::check('20160114');
      //  $o = \Prj\Check\DayWithdraw::check('20160114');
      //  $o = \Prj\Check\DayBuy::check('20160114');
      //  $o = \Prj\Check\DayLoan::check('20160114');
        $o = \Prj\Check\DayPayback::check('20160114');
        $o = \Prj\Check\DayPaysplit::check('20160114');

    }

    public function testGetConfigAction(){
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $data = \Prj\Data\TbConfig::get('ORDER_FIRST_RED_ON');
        var_log($data,'data>>>>');
    }

    /**
     * 回调接口测试
     */
    public function testPaygwResultAction(){
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $cmd = $this->_request->get('cmd');
        $dt = time();
        $sign = md5($dt.'asgdfw4872hfhjksdhr8732trsj');
        $args = $this->_request->get('args');
        if(empty($args)){
            $args = '{"sn":"1456481529829785","ordersId":"1014564812413155831","realPayAmount":"5200","realPayInterest":"20","status":"success"}';
        }
        //RpcConfig
        $url = \Sooh\Base\Ini::getInstance()->get('RpcConfig')['urls'][0] . "&service=PayGW&cmd=".$cmd."&args=" . $args . "&dt=" . $dt . "&sign=" . $sign;
        var_log($url, __CLASS__ . '479>>>>>>>>>');
        $result = file_get_contents($url);
        $test = json_decode($result,true);
        $test['url'] = $url;
        $this->_view->assign('result',json_decode($result,true));
        $this->returnOK();
    }

    public function testFreeAction(){
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $cmd = $this->_request->get('cmd',4);
        if($cmd==1){
            $ordersId = '1014570780564861485';
            $ret = \Prj\Items\Rebate::openRebate($ordersId);
        }elseif($cmd==2){
            try{
                $ret = \Prj\Items\Rebate::rebateResult('112233','2','35719716165380','success','');

            }catch (\ErrorException $e){
                return $this->returnError($e->getMessage());
            }
            var_log($ret,'ret>>>');
        }elseif($cmd==3){
            $tmp = new \Lib\Services\PayGW();
            $ret = $tmp->transResult('1014581260149196181','success');
            var_log($ret,'ret>>>');
        }elseif($cmd==4){
            $with = new \Prj\Items\Withdraw('');
            $with->setNum(100,'201501');
            echo $with->getWithdrawLeft();
        }

    }

    //满标转账手动回调
    public function testTransResultAction(){
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $waresId = $this->_request->get('waresId');
        if(empty($waresId)){
            return $this->returnError('no_waresId');
        }else{
            $wares = \Prj\Data\Wares::getCopy($waresId);
            $wares->load();
            if(!$wares->exists()){
                return $this->returnError('no_wares');
            }else{
                $sn = $wares->getField('paySn');
                $invests = \Prj\Data\Investment::loopFindRecords(['waresId'=>$waresId,'orderStatus'=>\Prj\Consts\OrderStatus::waiting]);
                if(empty($sn)){
                    return $this->returnError('no_sn');
                }else{
                    if(empty($invests)){
                        return $this->returnError('no_invest');
                    }else{
                        foreach($invests as $v){
                            $payGW = new \Lib\Services\PayGW();
                            $ret[] = $payGW->transResult($v['ordersId'],'success');
                        }
                    }
                }
            }
        }
        $this->_view->assign('ret',$ret);
        return $this->returnOK();
    }
    //回款手动回调
    public function testReturnFundAction(){
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $sn = $this->_request->get('sn');
        $ordersId = $this->_request->get('ordersId');
        if(empty($sn)){
            return $this->returnError('no_sn');
        }else{
            if(empty($ordersId)){
                return $this->returnError('no_ordersId');
            }else{
                $invest = \Prj\Data\Investment::getCopy($ordersId);
                $invest->load();
                if(!$invest->exists()){
                    return $this->returnError('no_invest');
                }else{
                    $planStr = $invest->getField('returnPlan');
                    if(empty($planStr)){
                        return $this->returnError('no_plan');
                    }else{
                        $plan = \Prj\ReturnPlan\Std01\ReturnPlan::createPlan($planStr);
                        //$ret['planStr'] = $planStr;
                        //$ret['plan'] = $plan;
                        //$ret['sinPlan'] = $plan->getPlan(['sn'=>$sn]);
                        $sinPlan = current($plan->getPlan(['sn'=>$sn])) ;
                        if(empty($sinPlan)){
                            //$this->_view->assign('ret',$ret);
                            return $this->returnError('no_single_plan');
                        }else{
                            $amount = $sinPlan['realPayAmount'];
                            $interest = $sinPlan['realPayInterest'];
                            $payGW = \Lib\Services\PayGW::getInstance();
                            $ret[] = $payGW->returnFundResult($sn,$ordersId,$amount,$interest,$sinPlan['realPayinterestSub'],'success');
                        }
                    }
                }
            }
        }
        $this->returnOK();
        $this->_view->assign('ret',$ret[0]['code']);
        $this->_view->assign('msg',$ret[0]['msg']);
    }


	/**
	 * 校验新的参数验证库
	 */
	public function checkNewValidateAction()
	{
//		$or = new \Lib\Oauth\OauthResponse();
//		var_log($or->error_access_token_expired['code']);
//		$params = [
//			'phone1' => '18616700069',
//		    'password' => '23fasdfa',
//		    'accountId' => '15152515152512',
//		];
//
//		$inputValida = new \Lib\Misc\InputValidation();
//
//		var_log($inputValida->validate($params, null, ['phone1' => 'phone']));
	}

	/**
	 * 我的红包列表
	 * list:
	 * {
	 *     "voucherId":             券ID
	 *     "userId":                用户ID
	 *     "voucherType":           券类型 8：红包；4：利息券；2：加息券
	 *     "amount":                金额，分为单位
	 *     "codeCreate":            codeCreate
	 *     "descCreate":            签到奖励
	 *     "timeCreate":            创建时间
	 *     "dtExpired":             过期时间
	 *     "voucherTPL":            "Std01",
	 *     "dtUsed":                使用时间
	 *     "orderId":               使用时的订单ID
	 *     "statusCode":            状态 -1：废弃；0：未使用；1：已使用
	 *     "iRecordVerID":          "2",
	 *     "limitsShelf":           "类型限制",
	 *     "limitsType":            "类型限制",
	 *     "limitsTag":             "标签限制"
	 *     "explain":               "使用说明"
	 *  }
	 * @input integer pageId 第几页 默认第一页
	 * @input string order 排序条件 默认以获得时间从大到小
	 * @input string pageSize 每页条数 默认5条
	 * @output {"code":200,"msg":"success","usedAmount":"已使用金额","unuseAmount":"未使用金额","list":"****"}
	 * @errors {"code":400,"msg":"error"}
	 */
	public function testPageAction()
	{
		$pageId = $this->_request->get('pageId', 1) - 0;
		//$order = $this->_request->get('order');
		$pageSize = $this->_request->get('pageSize', 2) - 0;
		$timeMax = date('YmdHis', strtotime('+30 days'));//30天之内
		$this->loger->target = $pageId;
		try {
			$accountId = \Sooh\Base\Session\Data::getInstance()->get('accountId');

			$pager = new \Sooh\DB\Pager($this->_request->get('pageSize', 100) - 0);
			$map = ['userId' => $accountId, 'voucherType' => \Prj\Consts\Voucher::type_real, 'statusCode]' => 0, 'timeCreate[' => $timeMax];

			$pager->init(\Prj\Data\Vouchers::loopGetRecordsCount($map), $pageId);

			$pager = new \Sooh\DB\Pager($pageSize);
			$pager->init(\Prj\Data\Vouchers::loopGetRecordsCount($map), $pageId);

			if (!empty($_COOKIE['lastPage'])) {
				$where = json_decode($_COOKIE['lastPage'], true);
			} else {
				$where = ['where' => $map];
			}
			$this->_view->assign('where', $where);
			$this->_view->assign('cookie', $_COOKIE['lastPage']);

			$list = \Prj\Data\Vouchers::loopGetRecordsPage(array('timeCreate' => 'rsort', 'voucherId' => 'sort'), $where, $pager);
//			$this->_view->assign('_list', $list);

			setcookie('lastPage', json_encode($list['lastPage']));

			$_user = \Prj\Data\User::getCopy($accountId);
			$_user->load();
			$this->_view->assign('usedAmount', $_user->getField('redPacketUsed'));
			$this->_view->assign('unuseAmount', $_user->getField('redPacket'));
			$this->_view->assign('list', $list['records']);
			return $this->returnOK();
		} catch (Exception $e) {
			return $this->returnError($e->getMessage(), $e->getCode());
		}
	}
    public function tgh_testWaresCacheAction(){
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $waresId = '1453188976524172';
        $ware = \Prj\Data\Wares::getCopy($waresId);
        $ware->load();
        $data['exp'] = $ware->getField('exp');


        $ware->refreshCache();
        $this->_view->assign('data',$data);
    }



	public function testGiveBonusAction()
	{
		$userId = 81568478941117;//localServer-mine-18616700069
		$itemGiver = new \Prj\Items\ItemGiver($userId);

//		$itemGiver->add('Register', 1);
//		$itemGiver->give();
		$finalItems = $itemGiver->add('NewRegisterRedPacket', 3)->give();
		if (empty($finalItems)) {
			$err = $itemGiver->getLastError();
		}

		$this->_view->assign('finalItems', $finalItems);
		$this->_view->assign('err', $err ? : '');
	}

	public function testWeekAction()
	{
		//周常任务
		try {
			$userId = 81568478941117;//localServer-mine-18616700069
			error_log('start checkin week');
			$weekCheckIn = \Prj\ActivePoints\Checkin::getCopy($userId);
			var_log($weekCheckIn, 'weekCheckIn');

			$weekCheckIn->addNum(1);
			$weekCheckIn->updUser();
			error_log('end checkin week');
		} catch (Exception $e) {
			$this->loger->ret = '周常-签到领积分发送失败';
		}
	}

	public function testFirstLoginAppAction()
	{
		$accountId = 81568478941117;
		try {
			$itemGiver = new \Prj\Items\ItemGiver($accountId);
			$finalItems = $itemGiver->add('NewFirstLoginAppRedPacket', 1)->give();
			if (empty($finalItems)) {
				$this->loger->ext = $itemGiver->getLastError();
			} else {
                \Prj\Message\Message::run(
					[
						'event' => 'first_login_packet',
						'brand' => \Prj\Message\Message::MSG_BRAND,
						'num_packet' => 1,//数量
						'private_gift' => sprintf('%.02f', $finalItems[0][1] / 100),//金额
						'num_deadline' => 48,//有效期
					],
					['userId' => $accountId]
				);
			}
		} catch (\Exception $e) {
			$this->loger->ext = 'send NewFirstLoginAppRedPacket faild:' . $e->getMessage();
		}
	}

    public function testAPPAction(){

    }

    /**
     * 还款计划1970
     * 还款计划错误检查
     * @throws ErrorException
     * @throws Exception
     */
    public function tgh_testCheckReturnPlanAction(){
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $cmd = $this->_request->get('cmd');
        $do = $this->_request->get('do');
        $rs = \Prj\Data\Investment::loopAll([]);
        $plan = [];
        $num = 0;
        if(!empty($rs)){
            foreach($rs as $v){
                $returnPlanStr = $v['returnPlan'];
                if(!empty($returnPlanStr)){
                    $returnPlan = \Prj\ReturnPlan\Std01\ReturnPlan::createPlan($returnPlanStr);
                    $dt = $returnPlan->dtStart;
                    if($cmd=='dtStart'){
                        if($returnPlan->dtStart>'20000000'){
                            continue;
                        }else{
                            $newDt = \Prj\ReturnPlan\Std01\ReturnPlan::calendar($v['ordersId'])->dtStart;
                        }
                    }

                    $calendar = $returnPlan->calendar;
                    if(!empty($calendar)){
                        var_log($calendar);
                        foreach($calendar as $v){
                            if($cmd=='error'){
                                if($v['interestStatic']>=0)continue;
                            }
                            if($do=='update'){
                                if($v['interestStatic']<0){
                                    $returnPlan->updatePlanByMonth('days',0,$v['planDateYmd']);
                                    $returnPlan->updatePlanByMonth('interestStatic',0,$v['planDateYmd']);
                                    $invest = \Prj\Data\Investment::getCopy($v['ordersId']);
                                    $invest->load();
                                    $invest->setField('returnPlan',$returnPlan->decode());
                                    $invest->update();
                                }
                                if($cmd=='dtStart' && $do=='update'){
                                    $returnPlan->dtStart = $newDt;
                                    $invest = \Prj\Data\Investment::getCopy($v['ordersId']);
                                    $invest->load();
                                    $invest->setField('returnPlan',$returnPlan->decode());
                                    $invest->update();
                                }
                            }
                            $tmp = $returnPlan->getPlanByMonth($v['planDateYmd']);
                            $tmp['dtStart'] = $dt;
                            $tmp['newDtStart'] = date('Ymd',$newDt);
                            $plan[] = $tmp;
                            $num++;
                        }
                    }

                    //$plan[] = $returnPlan;
                }
            }
        }
        if($cmd=='export'){
            header("Content-type:text/html;charset=utf-8");
            var_export($plan);
        }
        $this->_view->assign('num',$num);
        $this->_view->assign('plan',$plan);
    }

    /**
     * 购买接口测试页面 测试
     * @throws ErrorException
     */
    public function test1Action()
    {
        $userIdentifier = \Sooh\Base\Session\Data::getInstance()->get('accountId');
        if ($userIdentifier){
            \Sooh\Base\Log\Data::getInstance()->userId = $userIdentifier;
            $this->user = \Prj\Data\User::getCopy($userIdentifier);
        }else{
            throw new \ErrorException(\Prj\ErrCode::errNotLogin,401);
        }

        $r['waresId'] = $this->_request->get('waresId');
        if(empty($r['waresId']))
        {
            $o = \Prj\Data\Wares::getCopy('');
            $db = $o->db();
            $tb = $o->tbname();
            $rs = $db->getRecord($tb,['waresId'],['statusCode'=>\Prj\Consts\Wares::status_open],'rsort timeStartPlan');
            var_log($rs,'获取最新的在售标的>>>>>>>>>>');
            $r['waresId'] = $rs['waresId'];
        }

        $wares        = \Prj\Data\Wares::getCopy($r['waresId']);
        $wares->load();
        $r['remain']     = $wares->getField('remain');
        $r['uniqueOpId'] = \Lib\Misc\UniqueOp::createFor($r['waresId'], 'orders/add');
        $vouchers        = array_merge($this->_myVouchers()['redPacketList'],$this->_myVouchers()['voucherList']);
        $this->user->load();
        $wallet = $this->user->getField('wallet');
        $this->_view->assign('v', $vouchers);
        $this->_view->assign('r', $r);
        $this->_view->assign('wallet', $wallet);
    }

    protected function _myVouchers($waresId='',$voucherType = [],$orderBy = '' , $amount = 0)
    {
        if(empty($orderBy))$orderBy = 'rsort timeCreate';
        $redPacket = [];
        $voucher = [];
        $this->user->load();
        $userId = $this->user->userId;
        $user = $this->user;
        $o = \Prj\Data\Vouchers::getCopy($userId);
        $db = $o->db();
        $tb = $o->tbname();
        $where = [
            'userId'=>$userId,
            'dtExpired]'=>\Sooh\Base\Time::getInstance()->ymdhis(),
            'statusCode'=>\Prj\Consts\Voucher::status_unuse,
            'limitsAmount['=>$amount
        ];
        if(!empty($voucherType))
        {
            $where['voucherType'] = $voucherType;
        }
        $rs = $db->getRecords($tb,'*',$where,$orderBy);
        foreach($rs as $k=>$v)
        {
            $limit = [
                'limitsShelf'=>$v['limitsShelf'],
                'limitsType'=>$v['limitsType'],
                'limitsTag'=>$v['limitsTag'],
                'limitsAmount'=>$amount,
                'limitsDeadline'=>$v['limitsDeadline'],
            ];
            try{
                if(!empty($waresId) && !\Prj\Data\Vouchers::checkLimit($waresId,$limit))
                {
                    unset($rs[$k]);
                    continue;
                }
            }catch (\ErrorException $e){
                unset($rs[$k]);
                continue;
            }

            if($v['voucherType']==\Prj\Consts\Voucher::type_real)
            {
                $redPacket[] = $v;
            }
            else
            {
                $voucher[] = $v;
            }
        }
        $new['redPacketList'] = $redPacket;
        $new['voucherList'] = $voucher;
        return $new;
    }

	public function testEchoJsonAction()
	{
		$arr = [
			'lyq',
		    'dali',
		    'shengge',
		];
		return $this->returnOK('data', json_encode($arr));
	}

    /**
     * 查看默认还款计划
     */
    public function tgh_returnPlanByOrdersIdAction(){
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $ordersId = $this->_request->get('ordersId');
        if(empty($ordersId))return $this->returnError('请传入ordersId');
        $returnPlan = \Prj\ReturnPlan\Std01\ReturnPlan::calendar($ordersId);
        $this->_view->assign('returnPlan',$returnPlan->decode());
        $this->_view->assign('ahead',$returnPlan->getAheadInterest(1));
    }

    /**
     * 查看标的默认还款计划
     */
    public function tgh_returnPlanByWaresIdAction(){
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $waresId = $this->_request->get('waresId');
        if(empty($waresId))return $this->returnError('请传入waresId');
        $returnPlan = \Prj\ReturnPlan\All01\ReturnPlan::calendar($waresId);
        $this->_view->assign('returnPlan',$returnPlan->decode());
        $this->_view->assign('ahead',$returnPlan->getAheadInterest(1));
    }

    /**
     * 拉取日志内容
     */
    public function tgh_getLogConAction(){
        \Sooh\Base\Ini::getInstance()->viewRenderType('echo');
        $key = $this->_request->get('key');
        echo "<style>red{color:red}</style>";
        echo "<style>*{font-size: 15px;background-color: #000000;color: white;font-family: 'Consolas'}</style>";
        $path = "/var/www/logs/php_errors.log";
        $file = fopen($path,'r');
        fseek($file,-60000,SEEK_END);
        while(!feof($file)){
            $str = fgets($file)."<br>";
            $str = str_replace('error',"<red>error</red>",$str);
            $str = str_replace('warning',"<red>warning</red>",$str);
            $str = str_replace('syntax',"<red>syntax</red>",$str);
            $str = str_replace('>>>',"<red>>>></red>",$str);
            $str = str_replace('-------------------------------------------------------',"<br><br><br>-------------------------------------------------------",$str);
            if(!empty($key))$str = str_replace($key,"<red>".$key."</red>",$str);
            echo $str;
        }
    }

    /**
     * 获取日志长度变化
     * @throws \Sooh\Base\ErrException
     */
    public function tgh_getLogLenAction(){
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $path = "/var/www/logs/php_errors.log";
        $nowSize = filesize($path);
        $oldSize = \Sooh\Base\Session\Data::getInstance()->get('oldSize');
        $this->_view->assign('size',$nowSize);
        $change = $nowSize-$oldSize;
        $this->_view->assign('change',$change);
        $refresh = $change>2000?1:0;
        $this->_view->assign('refresh',$refresh);
        \Sooh\Base\Session\Data::getInstance()->set('oldSize',$nowSize);
    }

    /**
     * 查看日志
     */
    public function tgh_showLogAction(){
        header("Content-type: text/html; charset=utf-8");
    }

    public function tgh_testAction(){
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        var_log(\Prj\Data\Recharges::getLastRedirectUrl('11315673597674',30));
    }

    public function tgh_test1Action(){
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $sn = '146762799314822123';
        $borrowerId = '11315673597674';
        $amount = 100000;
        $status = 'success';
        $msg = '回调失败';
        $rs = \Lib\Services\PayGW::getInstance()->remitResult($sn,$borrowerId,$amount,$status,$msg);
        $this->_view->assign('rs',$rs);
    }

    /**
     * 激活返利 ps:下一步#返利回调
     * @throws ErrorException
     */
    public function tgh_jihuofanliAction(){
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $investId = $this->_request->get('ordersId');
        if(empty($investId))return $this->returnError('no_ordersId');
        $ret = \Prj\Items\Rebate::openRebate($investId);
        $this->_view->assign('ret',$ret);
    }

    public function tgh_rebateResultAction(){
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $rebateId = $this->_request->get('rebateId');
        if(empty($rebateId))return $this->returnError('no_rebateId');
        $rebate = \Prj\Data\Rebate::getCopy($rebateId);
        $rebate->load();
        if(!$rebate->exists())return $this->returnError('no_rebate');
        $arr = $rebate->dump();
        $ret = \Prj\Items\Rebate::rebateResult($arr['sn'],$arr['amount'],$arr['userId'],'success');
        $this->_view->assign('ret',$ret);
    }

    /**
     * 测试:管理费对账
     */
    public function tgh_dayMangeAction(){
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $ymd = date('Ymd');
        \Prj\Check\DayManage::saveData($ymd);
        \Prj\Check\DayManage::check($ymd);
    }

    /**
     * 更新标的的待处理订单数
     */
    public function tgh_updateWaitInvestNumAction(){
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $waresWhere = [
            'statusCode]'=>\Prj\Consts\Wares::status_open,
        ];
        $waresList = \Prj\Data\Wares::loopFindRecords($waresWhere);
        if(!empty($waresList)){
            foreach($waresList as $v){
                $waresId = $v['waresId'];
                $investWhere = [
                    'waresId'=>$waresId,
                    'orderStatus'=>\Prj\Consts\OrderStatus::waiting,
                ];
                $num = \Prj\Data\Investment::loopGetRecordsCount($investWhere);
                if(!empty($num)){
                    $ware = \Prj\Data\Wares::getCopy($waresId);
                    $ware->load();
                    $ware->setField('waitInvestNum',$num);
                    $ware->update();
                    $data[$waresId] = $num;
                }
            }
        }
        $this->_view->assign('data',$data);
    }

    public function tgh_toolsAction(){
        header("Content-type: text/html; charset=utf-8");
    }

	/**
	 * 清楚当前账户的accessToken
	 * @throws \Sooh\Base\ErrException
	 */
	public function delSessTokenAction()
	{
		\Sooh\Base\Session\Data::getInstance()->set('accessToken', null);
		return $this->returnOK(\Sooh\Base\Session\Data::getInstance()->get('accessToken') ? : '');
	}

	/**
	 * 简单删除一个帐号
	 * @input string phone 手机号
	 */
	public function delPhoneAction()
	{
		$phone = $this->_request->get('phone');
		$rules = [
			'phone' => [
				\Lib\Misc\InputValidation::$define['phone'],
				\Prj\Lang\Broker::getMsg('passport.phone_number_is_not_valid'),
			],
		];
		if (\Lib\Misc\InputValidation::validateParams(['phone' => $phone], $rules) === false) {
			return $this->returnError(\Lib\Misc\InputValidation::$errorMsg);
		}

		$dbAlias = \Sooh\DB\Cases\AccountAlias::getCopy($phone);
		$dbAlias->load();
		if ($dbAlias->exists()) {
			$dbAlias->delete();
			$this->returnOK('success');
		} else {
			$this->returnError('not found');
		}
	}

	/**
	 * 获取配置值
	 * @input string name name
	 */
	public function getConfigAction()
	{
		$name = $this->_request->get('name');
		$this->_view->assign('value', \Prj\Data\Config::get($name));
		return $this->returnOK('success');
	}

    public function tgh_flowResultAction(){
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $ordersId = $this->_request->get('ordersId');
        if(empty($ordersId)){
            return $this->returnError('no_ordersId');
        }else{
            $invest = \Prj\Data\Investment::getCopy($ordersId);
            $invest->load();
            if(!$invest->exists()){
                return $this->returnError('no_invest');
            }
            $amount = $invest->getField('amount');
            $payGW = new \Lib\Services\PayGW();
            $ret = $payGW->abortResult($ordersId,$amount,'success');
            $this->_view->assign('ret',$ret);
        }
    }

    public function tgh_dayWithdrawAction(){
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $cmd = $this->_request->get('cmd','DayPaysplit');
        $ymd = $this->_request->get('ymd',date('Ymd'));
        $className = '\Prj\Check\\'.$cmd;
        $data[$cmd] = (new $className($ymd))->getLocalData();
        $this->_view->assign('data',$data);
    }

    public function tgh_waresRealReturnPlanAction(){
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $waresId = $this->_request->get('waresId');
        if(empty($waresId))return $this->returnError('no_waresId');
        $ware = \Prj\Data\Wares::getCopy($waresId);
        $ware->load();
        $data = [];
        if($ware->load()){
            $plan = $ware->getField('returnPlan');
            if($plan)$data = $plan;
        }
        $this->_view->assign('waresReturnPlan',$data);
        $this->returnOK();
    }

    public function tgh_ordersRealReturnPlanAction(){
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $ordersId = $this->_request->get('ordersId');
        if(empty($ordersId))return $this->returnError('no_ordersId');
        $invest = \Prj\Data\Investment::getCopy($ordersId);
        $invest->load();
        $plan = $invest->getField('returnPlan');
        $this->_view->assign('ordersReturnPlan',$plan);
        $this->returnOK();
    }

    /**
     * 一键添加标的
     */
    public function tgh_addNewWareAction(){
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');

        $deadLine = $this->_request->get('deadLine');
        $dlUnit = $this->_request->get('dlUnit');
        $amount = $this->_request->get('amount');
        $shelfId = $this->_request->get('shelfId');
        if(empty($deadLine) || empty($dlUnit) ||empty($amount) || empty($shelfId))return $this->returnError('args_error');

        $fields['waresName'] = '回款标-'.date('md').'-'.rand(1000,9999);
        $fields['deadLine'] = $deadLine;
        $fields['dlUnit'] = $dlUnit;
        if($dlUnit=='天') //1：1次   2：按月
        {
            $fields['returnType'] = \Prj\Consts\ReturnType::single;
            $days = $fields['deadLine'];
        }
        else
        {
            $fields['returnType'] = \Prj\Consts\ReturnType::byMonth;
            $days = $fields['deadLine']*30;
        }
        $fields['tags'] = '';
        $fields['shelfId'] = $shelfId;
        $fields['timeStartPlan'] = \Sooh\Base\Time::getInstance()->ymdhis();
        $fields['timeStartReal'] = \Sooh\Base\Time::getInstance()->ymdhis();
        //$fields['timeEndReal'] = $fields['timeStartPlan'];
        $fields['ymdPayPlan'] = strtotime("+$days days",strtotime($fields['timeStartPlan']));
        $fields['interestStartType'] = \Prj\Consts\InterestStart::whenFull;
        $fields['waresSN'] = 1;
        $fields['statusCode'] = \Prj\Consts\Wares::status_open;
        $fields['priceStart'] = 1;
        $fields['priceStep'] = 1;
        $fields['amount'] = $amount;
        $fields['remain'] = $amount;
        $fields['yieldStatic'] = 0.14;
        $fields['managementTrans'] = 2000;
        $fields['managementConfirm'] = 3000;
        $fields['autoReturnFund'] = 0;
        $fields['autoConfirm'] = 0;
        if(strpos($fields['tags'],'活动')!==false){
            $fields['yieldStaticAdd'] = 0.12;
        }
        $fields['introDisplay'] = ($shelfId == \Prj\Consts\Wares::shelf_static) ? '{"a":"\u9879\u76ee\u771f\u662f\u597d\u9879\u76ee\u554a","b":{"name":"\u5f20\u5148\u751f","married":"\u5df2\u5a5a","rootAddr":"\u4e0a\u6d77\u5e02\u6d66\u4e1c\u65b0\u533a","addr":"\u4e0a\u6d77\u5e02\u6d66\u4e1c\u65b0\u533a","reason":"\u505a\u751f\u610f"},"c":{"addr":"\u4e0a\u6d77\u5e02\u6d66\u4e1c\u65b0\u533a","ymd":"20140101","price":20000000},"d":{"id":{"name":"","img":[{"url":"asset_14600274964379","desc":""}]},"book":{"name":"","img":[]},"married":{"name":"","img":[]},"credit":{"name":"","img":[]},"inspect":{"name":"","img":[]},"contract":{"name":"","img":[]},"receipt":{"name":"","img":[]},"property":{"name":"","img":[]}}}':'{"a":"\u9879\u76ee\u771f\u662f\u597d\u9879\u76ee\u554a","b":{"name":"\u5f20\u5148\u751f","idCard":"","reason":"\u505a\u751f\u610f"},"c":{"brand":"","idCar":"","meter":"","ymd":"20140101","buy":0,"price":20000000},"d":{"id":{"name":"","img":[{"url":"wares_14597628389955","desc":""}]},"book":{"name":"","img":[]},"drive":{"name":"","img":[]},"bill":{"name":"","img":[]},"tax":{"name":"","img":[]},"key":{"name":"","img":[]}}}';
        $fields['viewTPL'] = ($shelfId == \Prj\Consts\Wares::shelf_static) ? 'Std02' : 'Std03';

        $fields['borrowerId'] = '46960782986568';
        $fields['returnTPL'] = 'Std01';

        $wares = \Prj\Data\Wares::getCopy(time() . rand(100000, 999999));
        $wares->load();
        foreach($fields as $k=>$v){
            $wares->setField($k,$v);
        }
        \Prj\Wares\Wares::setSortVal($wares);
        $wares->update();
        $this->_view->assign($wares->dump());
    }

    public function tgh_checkUserAmountAction(){
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        \Prj\Tool\Func::debug_log();
    //46061253494017
        $data = [];
        $userList = \Prj\Data\User::loopFindRecords([]);
        if(!empty($userList)){
            foreach($userList as $v){
                $userId = $v['userId'];
                if($v['ymdFirstCharge']){
                    $url = 'http://115.28.138.127:8080/backgrount_payment/service/service?service=PayGW&cmd=queryBalance&userId='.$userId;
                    $ret = \Prj\Tool\Func::curl_post($url,[]);
                    $ret = json_decode($ret,true);
                    $tmp['userId'] = $userId;
                    $tmp['localAmount'] = $v['wallet']-0;
                    $tmp['gwAmount'] = $ret?$ret['data']['amount']*100:0;
                    var_log($tmp);
                    $data[] = $tmp;
                }
            }
        }

        $this->_view->assign('data',$data);
    }

	/**
	 * @input int pageId pageId
	 */
	public function generalTestAction() {
		$arr = [
			122,
		    '1231',
		    'asdfa',
		    '2134dfs',
		    '[500,500]'
		];

		foreach ($arr as $k => $v) {
			$this->_view->assign('key' . $k, $v);
			$this->_view->assign('ret' . $k, $this->parseJson($v, true));
		}
	}

	/**
	 * @param string     $str
	 * @param bool|false $toArr
	 * @param int        $depth
	 * @param int        $options
	 * @return int|mixed|string
	 */
	public function parseJson( $str,  $toArr = false,  $depth = 512,  $options = 0) {
		if (is_numeric($str)) {
			return intval($str);
		} elseif (!is_string($str)) {
			return 'The string is not a valid';
		} else {
			if (json_decode($str, $toArr, $depth, $options) && json_last_error() === JSON_ERROR_NONE) {
				return json_decode($str, $toArr, $depth, $options);
			} else {
				//解析出错
				if (!function_exists('json_last_error_msg')) {
					function json_last_error_msg() {
						static $ERRORS = array(
							JSON_ERROR_NONE => 'No error',
							JSON_ERROR_DEPTH => 'Maximum stack depth exceeded',
							JSON_ERROR_STATE_MISMATCH => 'State mismatch (invalid or malformed JSON)',
							JSON_ERROR_CTRL_CHAR => 'Control character error, possibly incorrectly encoded',
							JSON_ERROR_SYNTAX => 'Syntax error',
							JSON_ERROR_UTF8 => 'Malformed UTF-8 characters, possibly incorrectly encoded'
						);

						$error = json_last_error();
						return isset($ERRORS[$error]) ? $ERRORS[$error] : 'Unknown error';
					}
				}
				return json_last_error_msg();
			}
		}
	}

	/**
	 * @input int investmentAmount investmentAmount单位元
	 * @input int expires expires单位天
	 * @throws \Sooh\Base\ErrException
	 */
	public function rateTestAction()
	{
		set_time_limit(100);
		$ret = [];
		$checkinRule = [
			'16_24' => 230,
			'24_33' => 1355,
			'33_41' => 3415,
			'41_50' => 3415,
			'50_58' => 1355,
			'58_67' => 230,
		];
		$maxAmount = 22700;//固定金额

		$investmentAmount = $this->_request->get('investmentAmount');
		$expires = $this->_request->get('expires');

		$redpacketForShare = new \Prj\Items\RedPacketForShare();
		$parentAmount = $this->getAmountByInvestment($investmentAmount * 100, $expires);
		$childNum = $this->getNumByInvestment($parentAmount);
		$childAmount = $this->shardingAmountOrderNum($parentAmount, $childNum);

		$ret['share'] = [
			'parentAmount' => $parentAmount,
		    'childNum' => $childNum,
		    'childAmount' => $childAmount,
		    'childAmountSum' => array_sum($childAmount),
		];


		$this->_view->assign('check', $ret);
		return $this->returnOK('success');
	}

	private function getRand($rule) {
		if (count($rule) == 1) {
			$result = key($rule);
		} else {
			$result = '';
			$ruleSum = array_sum($rule);

			//概率数组循环
			$vSum = 0;
			$randNum = mt_rand(1, $ruleSum);
			foreach ($rule as $k => $v) {
				$vSum += $v;
				if ($randNum <= $vSum) {
					$result = $k;
					break;
				}
			}
		}

		unset ($rule);

		$loc = strpos($result, '_');
		if ($loc) {
			//TODO 处理（统一）边界问题
			$result = mt_rand(substr($result, 0, $loc), substr($result, $loc + 1));
		}
		return $result;
	}

	private function getAmountByCheckinAction($days, $checkinRule, $maxAmount)
	{
		if ($days >= 7) {
			return $maxAmount;
		} else {
			return $this->getRand($checkinRule);
		}
	}

	private function getAmountByInvestment($investment, $investmentExpires)
	{
        $x = $investment / 1200;
        $ret = floor(6.7453 * log($x) - 30.013) * 100;
        if ($ret <= 100) {
            return 100;
        } elseif ($ret >= 3500) {
            return 3500;
        } else {
            return $ret;
        }
	}

	private function getNumByInvestment($voucherAmount)
	{
        if ($voucherAmount < 1000) {
            return 3;
        } elseif ($voucherAmount > 10000) {
            return 20;
        } else {
            if (function_exists('exp')) {
                return round(3.4225 * exp(0.000179 * $voucherAmount));
            }
            throw new \Sooh\Base\ErrException('server not support Mathematical Functions!');
        }
        return 3;
	}

	private function shardingAmountOrderNum($totalAmount, $totalNum, $result = [])
	{
		/**
		 * 使用递归完成最简单的红包切分
		 * 优点：简单、移动、快速
		 * 缺点：非常大的概率出现较大额红包，红包金额差距大！
		 * 改进：加入波动配置，根据配置计算金额波动
		 */
        $totalAmount = intval($totalAmount);
        $totalNum = intval($totalNum);
        if ($totalAmount == 0 || $totalNum == 0 || ($totalAmount < $totalNum)) {
            return false;
        }

        //等额1分也是爱
        if ($totalAmount == $totalNum) {
            $result = [];
            for ($i = 0; $i < $totalNum; $i++) {
                $result[] = 1;
            }
            return $result;
        }

        //最后一个红包
        if ($totalNum == 1) {
            array_push($result, $totalAmount);
            shuffle($result);
            return $result;
        }

        $meanAmount = floor($totalAmount * 2 / $totalNum);
        $mineAmount = mt_rand(1, $meanAmount);
        array_push($result, $mineAmount);
        var_log($result, 'result ' . $totalNum);
        return $this->shardingAmountOrderNum(($totalAmount - $mineAmount), ($totalNum - 1), $result);
	}

    function tgh_splitAheadAction(){
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $ordersId = $this->_request->get('ordersId');
        if(empty($ordersId))return $this->returnError();
        $invest = \Prj\Data\Investment::getCopy($ordersId);
        $invest->load();
        $rp = \Prj\ReturnPlan\Std01\ReturnPlan::createPlan($invest->getField('returnPlan'));
        $rp->splitAhead(3);
        $this->_view->assign('rp',$rp->decode());
    }

    function tgh_makeGigtPayAciton(){
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $data = [

        ];
    }

    function tgh_addRedPacketAction(){
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        if(empty($userId))return $this->returnError('no_userId');
        $userId = $this->_request->get('userId');
        $user = \Prj\Data\User::getCopy($userId);
        $user->load();
        $item = new \Prj\Items\ItemGiver($userId);
        $ret = $item->add('RedPacketOfKefu',1)->give([]);
        $user->update();
        $ret['user'] = $user->dump();
        $this->_view->assign('ret',$ret);
    }

    function tgh_dbUserAction(){
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $userId = $this->_request->get('userId');
        if(empty($userId))return $this->returnError('no_userId');

        $user = \Prj\Data\User::getCopy($userId);
        $user->load();
        $userInfo = $user->dump();
        $this->_view->assign('user',$userInfo);
    }

    function tgh_remitResultAction(){
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $waresId = $this->_request->get('waresId');
        $sn = $this->_request->get('sn');
        if(empty($waresId))return $this->returnError('no_waresId');
        if(empty($sn))return $this->returnError('no_sn');
        $ware = \Prj\Data\Wares::getCopy($waresId);
        $ware->load();
        if(!$ware->exists())return $this->_returnError('void_ware');
        $rp = \Prj\ReturnPlan\All01\ReturnPlan::createPlan($ware->getField('returnPlan'));
        var_log($rp,'rp >>> ');
        var_log($sn,'sn >>>>>>>>> ');
        $plan = current($rp->getPlan(['remitSN'=>$sn]));
        var_log($rp->getPlan(['remitSN'=>$sn]),'plan >>> ');
        $tmp = new \Lib\Services\PayGW();
        $ret = $tmp->remitResult($sn,$waresId,$ware->getField('borrowerId'),$plan['remitAmount'],'success','hahaha');
        $this->_view->assign('ret',$ret);
    }

    function tgh_withdrawResultAction(){
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $ordersId = $this->_request->get('ordersId');
        $status = $this->_request->get('status');
        if(!$ordersId)return $this->returnError('no_ordersId');
        $withdraw = \Prj\Data\Recharges::getCopy($ordersId);
        $withdraw->load();
        if(!$withdraw->exists())return $this->returnError('no_withdraw');
        $ret = \Lib\Services\PayGWCmd::getInstance()->withdrawResult($ordersId,$withdraw->getField('amountAbs'),$status);
        $this->_view->assign('ret',$ret);
    }

	public function lyqTestAction()
	{
        $userId = $this->_request->get('userId', '46060504739211');
        $alert = $this->_request->get('alert', '这好似一条复杂push');
        $shelfId = $this->_request->get('shelfId', '1111111111111');
        $waresId = $this->_request->get('waresId', '222222222222');
        $type = 'xxremind';
        $value = 'bidRemind';
        $extras = [
            'type' => $type,
            'value' => $value,
            'shelfId' => $shelfId,
            'waresId' => $waresId,
        ];
        $notify = [
            'android'  => [$alert, null, null, $extras],
            'ios'      => [$alert, null, null, null, $extras, null],
            'winphone' => [$alert, null, null, $extras]
        ];

        try {
            \Lib\Services\Message::getInstance()->push($userId, $notify);
        } catch (\Exception $e) {
            var_log($e, 'e');
        }

	}

    /**
     * 获取图片
     * By Hand
     */
    public function tgh_getImageAction()
    {
        $this->ini->viewRenderType('echo');
        $fileId = $this->_request->get('fileId');
        header('Content-type: image/jpg');
        echo \Prj\Data\Files::getDataById($fileId);
    }

    public function tgh_sysImageAction()
    {
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $url = 'http://10.1.1.188/index.php?__=dev/tgh_getImage&fileId=wares_14575242877555';
        $data = file_get_contents($url);
        $ret = file_put_contents('images/wares/'.rand(1000,9999).'.png',$data);
        $this->_view->assign('data',$ret);
    }

    public function tgh_cmdAction(){
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $cmd = 'ls';
        exec($cmd,$output);
        $this->_view->assign('output',$output);
    }

    /**
     * 添加运营流水
     */
    public function tgh_addSystallyAction(){
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $dowhat = ['managementTrans','managementConfirm'];
        $ret = \Lib\Services\PayGW::getInstance()->giftPayResult($dowhat[array_rand($dowhat)],time().rand(1000,9999),rand(1000,9999),rand(1000,9999),'success');
        $this->_view->assign('ret',$ret);
    }

    public function tgh_resultAction(){
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $rpc = \Sooh\Base\Rpc\Broker::factory('PayGW');
        var_log($rpc,' >>> ');
        $sys = \Lib\Services\PayGW::getInstance($rpc);
        try{
            $ret = $sys->test('123456','654321');
        }catch(\Sooh\Base\ErrException $e){
            $this->loger->error('send order to gw failed where addorder '.$e->getMessage());
            $code = $e->getCode();
            if($code==400){
                $this->_view->assign('retAll',['ret'=>$e->getMessage(),'got'=>$e->customData]);
            }elseif($code==500){
                $this->_view->assign('retAll',['ret'=>$e->getMessage(),'got'=>$e->customData]);
            }
        }

        var_log($ret,' >>> ');
    }

    public function tgh_showServerAction(){
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $this->_view->assign('server',$_SERVER);
    }

    public function tgh_getPostAction(){
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $this->_view->assign('zhangsheng is a sb',$this->_request->get('list'));
    }


    public function tgh_transRetryAction(){
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        usleep(300000);
        $ordersId = [

        ];
    }

    public function tgh_showReturnAction(){
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $waresId = $this->_request->get('waresId');
        $confirmSN = $this->_request->get('confirmSN');
        $pay = new \Lib\Services\PayGW();
        $data = $pay->showReturnFund($waresId,$confirmSN);
        $this->_view->assign('data',$data);
        $this->_view->assign('count',count($data['list']));
    }

    public function tgh_showInterestSubAction(){
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $waresId = $this->_request->get('waresId');
        $pay = new \Lib\Services\PayGW();
        $data = $pay->showInterestSub($waresId);
        $this->_view->assign('data',$data);
        $this->returnOK();
        //$this->_view->assign('count',count($data['list']));
    }

    public function tgh_showInterestSubResultAction(){
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $ordersId = $this->_request->get('ordersId');
        $interestSub = $this->_request->get('interestSub');
        $sn = $this->_request->get('sn');
        $pay = new \Lib\Services\PayGW();
        $data = $pay->returnInterestSubResult($ordersId,$interestSub,$sn,'success');
        $this->_view->assign('data',$data);
        $this->returnOK();
        //$this->_view->assign('count',count($data['list']));
    }

    public function tgh_setTagsAction(){
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $waresId = $this->_request->get('waresId');
        $tags = $this->_request->get('tags');
        $wares = \Prj\Data\Wares::getCopy($waresId);
        $wares->load();
        if(!$wares->exists())return $this->returnError('参数错误');
        $wares->setField('tags',$tags);
        $wares->update();
        return $this->returnOK($tags);
    }

    public function tgh_returnPlanTestAction()
    {
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $pay = new \Lib\Services\PayGW();
        $this->_view->assign('ret', $pay->returnInterestSubResult('1014640683735289748', '46', 123456, 'success'));
    }

    public function tgh_refreshWaresSortAction(){
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $records = \Prj\Data\Wares::loopFindRecords([]);
        $list = [];
        array_walk($records,function($v , $k)use(&$list){
            $tmp['waresId'] = $v['waresId'];
            $wares = \Prj\Data\Wares::getCopy($v['waresId']);
            $wares->load();
            $wares->setField('sortval',$wares->getSort());
            $wares->update();
            $tmp['sortval'] = $v['sortval'];
            $list[] = $tmp;
        });
        $this->_view->assign('list',$list);
    }
}



