<?php
namespace Prj\Acl;
class Manage extends \Sooh\Base\Acl\Ctrl
{
    public function initMenu()
    {
        /**
         * !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
         * 已经存在的节点不可以在修改顺序
         * 新增节点在数组最后添加
         * !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
         */
        return array(
            //'资产.添加新资产'=>array('manage','iosnatureworth1', 'test1',array(),array()),
            //'资产.审核资产'=>array('manage','iosnatureworth2', 'tst2',array(),array('external'=>true)),
			
            '标的.标的一览'=>array('manage','waresi','index', array(),array()),
            '标的.添加标的'=>array('manage','warese','edit', array(),array()),
            '标的.标的内容审核'=>array('manage','waresc','check', array(),array()),
            '标的.标的上架审核'=>array('manage','warescs','checksale', array(),array()),
            '标的.资产一览'=>array('manage','asset','index', array(),array()),
            '标的.添加资产'=>array('manage','asset','edit', array(),array()),
            '标的.资产审核'=>array('manage','assetc','check', array(),array()),
     
            '运营.订单一览'=>array('manage','investment','index', array(),array()),
            '运营.用户一览'  => ['manage', 'useri', 'index', [], []],
            '运营.充值一览'  => ['manage', 'userre', 'recharge', [], []],
            '运营.提现一览'  => ['manage', 'userw', 'withdraw', [], []],
            '运营.提现申请'  => ['manage', 'userwing', 'withdrawing', [], []],
            //'运营.积分一览'  => ['manage', 'point', 'index', [], []],
           // '运营.真实信息'  => ['manage', 'userh', 'honestInfo', [], []],
            '运营.消息管理'  => ['manage', 'userm', 'mineMsg', [], []],
            '运营.渠道管理' =>array('manage', 'copartners', 'index', array(), array()),
            '运营.协议管理' =>array('manage', 'contracts', 'index', array(), array()),
            //'运营.CMS管理'  => ['manage', 'cms', 'index', [], []],
            //'运营.兑换码' => ['manage', 'excodequery', 'index', [], []],
            '运营.查询兑换码' => ['manage', 'excodequery', 'queryexcode', [], []],
            '运营.用户资金记录' => ['manage', 'usertally', 'tally', [], []],
            '运营.返利一览' => ['manage', 'user', 'rebate', [], []],
            '运营.二次营销-查找用户' => ['report', 'secondmarketsearch', 'index', [], []],
            '运营.二次营销-短信' => ['report', 'secondmarket', 'index', [], []],

            //'运营.借款人一览'  => ['manage', 'user', 'borrower', [], []],
            //'运营.借款人审核'  => ['manage', 'user', 'borrowercheck', [], []],
            //'运营.短链管理' =>array('manage', 'shorturls', 'index', array(), array()),
            
            
            '高级运营.真实信息' =>['manage', 'userh', 'honestInfo', [], []],
            '高级运营.兑换码'=>['manage', 'excode', 'index', [], []],
            '高级运营.赠送提现次数'=>['manage','usersendwithdrawnum','sendWithdrawNum',[],[]],
            //'高级运营.解锁用户' => ['manage', 'userUnbindCard', 'unlockuser', [], []],
            //'高级运营.解锁标的' => ['manage', 'wares', 'unlockwares', [], []],
            '高级运营.交换手机-串号' => ['manage', 'swapuserphone', 'check', [], []],
            //'高级运营.交换手机-登录' => ['manage', 'userchangephone', 'index', [], []],
            //'高级运营.注销手机' => ['manage', 'userphonecancel', 'index', [], []],

            '客服.奖券管理' => ['manage', 'voucher', 'index', [], []],
			'客服.绑卡一览' => ['manage', 'userb1', 'bankcard', [], []],	
            '客服.绑卡未购买' => ['manage', 'userb2', 'bindCardNotInvest', [], []],
            '客服.券发放' => ['manage', 'vouchergrant', 'index', [], []],
           // '客服.日志查询' => ['manage', 'loginquiry', 'index', [], []],
            '客服.反馈管理' => ['manage', 'feedback', 'index', [], []],
           // '客服.日志查询' => ['manage', 'loginquiry', 'index', [], []],
            '客服.反馈管理' => ['manage', 'feedback', 'index', [], []],
            //'客服.二次营销' =>array('manage', 'Marketingsecond', 'indexkefu', array(), array()),

		    
		    
// 			'报表.用户信息'=>array('manage','index','index', array(),array()),
            //'报表.访问权限'=>['report','rptconf', 'conf', [], []],
            '报表.日报整合版'=>array('report','rptdailybasic','recent', array(),array()),
            '报表.日报数字版'=>array('report','rptdailybasic','recent2', array(),array()),
            '报表.渠道导入量'=>array('report','copartnerworth','index',array(),array()),
//            '报表.月报表'=>array('report','monthreport', 'index',array(),array()),
            '报表.红包' =>array('report', 'redpacketreport', 'index', [], []),
//            '报表.绑卡失败原因' => ['report', 'bindfail', 'index', [], []],

            '资金.回款确认'=>array('manage','payback', 'index',array(),array()),
            '资金.对账总览'=>array('manage','check', 'dayCheckOverview',array(),array()),
            '资金.充值对账'  => ['manage', 'check', 'dayRecharges', [], []],
            '资金.提现对账'  => ['manage', 'check', 'dayWithdraw', [], []],
            '资金.购买对账'  => ['manage', 'check', 'dayBuy', [], []],
            '资金.放款对账(给借款人)'  => ['manage', 'check', 'dayLoan', [], []],
            '资金.还款对账'  => ['manage', 'check', 'dayPayback', [], []],
            '资金.回款对账(给投资人)'  => ['manage', 'check', 'dayPaysplit', [], []],
            '资金.管理费对账'  => ['manage', 'check', 'dayManage', [], []],
            '资金.运营资金流水'  => ['manage', 'check', 'dayCompany', [], []],
            '资金.存钱罐日收益'  => ['manage', 'check', 'dayInterest', [], []],


            '系统.管理员一览'=>array('manage','managers','index',array(),array()),
            '系统.客户端版本'=>array('manage','clientversion','index',array(),array()),
            '系统.预警' => ['manage', 'lasthour', 'index', [], []],
            '系统.许可模板' => ['manage', 'agreement', 'index', [], []],
            '系统.日志查询' => ['manage', 'logquery', 'index', [], []],

            '系统.参数配置' => ['manage', 'config', 'index', [], []],
            //'系统.版本SQL维护' => ['manage', 'sqlmanage', 'index', [], []],
			
			'调试.api日志' => ['manage', 'dev', 'apilog', [], []],
			'调试.7xx的短信' => ['manage', 'dev', 'sms7', [], []],
			'调试.标的资金流水' => ['manage', 'dev', 'waremoneylog', [], []],
			//'调试.开发到测试' => ['manage', 'dev', 'dev2test', [], []],
            '活动.蜘蛛-补仓'=>['report', 'spider', 'fillup', [], []],
            '活动.蜘蛛-查询'=>['report', 'spidersearch', 'index', [], []],
            
            
            
            
            '支付网关.绑卡记录'=>['payfix', 'bindingbankcard', 'index', [], []],
            '支付网关.扣款记录'=>['payfix', 'collecttrade', 'index', [], []],
            '支付网关.流水记录'=>['payfix', 'companyprofit', 'record', [], []],
            '支付网关.公司流水记录'=>['payfix', 'companyprofit', 'index', [], []],
            '支付网关.投资记录'=>['payfix', 'companyprofit', 'investment', [], []],            
            '支付网关.充值记录'=>['payfix', 'companyprofit', 'recharge', [], []],             
            '支付网关.提现记录'=>['payfix', 'companyprofit', 'withdraw', [], []],
            
			//'支付网关.执行命令' => ['payfix', 'paygwcmds', 'cmdsintro', [], []],
			//'支付网关.执行命令日志' => ['payfix', 'paygwcmdslog', 'logs', [], []],

            //'支付网关.银行卡解绑' => ['payfix', 'userUnbindCard', 'unbindcard', [], []],
			//'支付网关.补充值记录'  => ['payfix', 'userretryrecharge', 'retryRecharge', [], []],
            //'支付网关.手动扣款'  => ['payfix', 'usercancelamount', 'cancelAmount', [], []],
            //'支付网关.手动转账'  => ['payfix', 'usertransindex', 'transIndex', [], []],
        );
    }
	
    public function dump()
    {
        var_log($this->rights,'rights========');
    }
}