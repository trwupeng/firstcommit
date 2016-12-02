/*
Navicat MySQL Data Transfer

Source Server Version : 50616
Source Database       : db_p2p

Target Server Type    : MYSQL
Target Server Version : 50616
File Encoding         : 65001

Date: 2016-03-30 19:01:45
*/

use db_p2p;

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for `tb_agreement_0`
-- ----------------------------
DROP TABLE IF EXISTS `tb_agreement_0`;
CREATE TABLE `tb_agreement_0` (
  `verName` varchar(20) NOT NULL COMMENT '议协用途',
  `verId` bigint(20) unsigned NOT NULL COMMENT '协议版本号',
  `verTpl` tinyint(4) unsigned NOT NULL COMMENT '显示模版（保留）',
  `userId` varchar(36) NOT NULL COMMENT '更新者id',
  `userName` varchar(36) NOT NULL COMMENT '更新者昵称',
  `content` text NOT NULL COMMENT '内容',
  `createTime` int(11) unsigned NOT NULL COMMENT '创建时间',
  `iRecordVerID` int(11) NOT NULL DEFAULT '0' COMMENT 'iRecordVerID',
  `status` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '状态位：正在使用为1，未在使用为0',
  PRIMARY KEY (`verName`,`verId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='协议管理';

-- ----------------------------
-- Records of tb_agreement_0
-- ----------------------------
INSERT INTO `tb_agreement_0` VALUES ('binding', '1', '0', 'root', '', '<!doctype html>\r\n<html lang=\"en\">\r\n<head>\r\n<meta charset=\"utf-8\">\r\n<title>绑卡协议</title>\r\n<meta name=\"viewport\" id=\"viewport\" content=\"width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no\">\r\n<link type=\"text/css\" rel=\"stylesheet\" href=\"/css/base.css\"/>\r\n <link type=\"text/css\" rel=\"stylesheet\" href=\"/css/agreement.css\"/>\r\n<script type=\"text/javascript\" src=\"/js/jquery-1.11.3.min.js\"></script>\r\n<script type=\"text/javascript\" src=\"/js/App.js\"></script>\r\n</head>\r\n\r\n<body>\r\n<section>\r\n   <h3>“新浪支付”服务开通协议</h3>\r\n<p class=\"p2\">新浪支付服务协议（以下简称“本协议”）是北京新浪支付科技有限公司（以下简称“新浪支付”）与新浪支付用户（以下简称“用户”或“您”）就新浪支付认证支付服务（以下简称“本服务”）的使用等相关事项所订立的有效合约。用户通过网络页面点击确认或以其他方式选择接受本协议，即表示您授权借记卡发卡行根据新浪支付的指令划扣您相应借记卡账户中的相应款项。 </p>\r\n\r\n<p class=\"p2\">在接受本协议之前，请您仔细阅读本协议的全部内容。如果您不同意本协议的任意内容，或者无法准确理解本协议任何条款的含义，请不要进行后续操作。 </p>\r\n\r\n<p class=\"p2\">您应确保您是使用本服务的借记卡持有人，可合法、有效使用该借记卡且未侵犯任何第三方合法权益，否则因此造成新浪支付、持卡人损失的，您应负责赔偿并承担全部法律责任，包括但不限于冻结您全部或部分新浪支付账户（含该账户之关联账户）及资金、从您的前述新浪支付账户中扣除相应的款项等。 </p>\r\n\r\n<p class=\"p2\">您同意新浪支付有权依据其自身判断对涉嫌欺诈或被他人控制并用于欺诈目的的新浪支付账户采取相应的措施，上述措施包括但不限于冻结账户及资金、处置涉嫌欺诈的资金等。 您应妥善保管借记卡卡号、密码、发卡行预留的手机号码以及新浪支付账号、密码、数字证书、新浪支付账户绑定的手机号码、来自于发卡行和/或新浪支付向您发送的校验码等与借记卡或与新浪支付账户有关的一切信息和设备。如您遗失或泄露前述信息和/或设备的，您应及时通知发卡行及/或新浪支付，以减少可能发生的损失。因您的原因所致损失需由您自行承担。 </p>\r\n\r\n<p class=\"p2\">您同意，如您的新浪支付账户或密码遭到未获授权的使用，或者发生任何其他安全问题时，立即通知新浪支付。 </p>\r\n\r\n<p class=\"p2\">为保障您的账户资金安全，您同意使用认证支付所绑定的借记卡将作为默认认证支付借记卡，即仅可使用所绑定借记卡完成充值、提现功能。 </p>\r\n\r\n<p class=\"p2\">您开通本服务时，应当认真输入并确认您待验证的本人的借记卡信息、银行预留手机号、短信动态验证码，通过实名信息验证后，完成绑卡及扣款操作，在不解绑的前提下第二次及后期操作时只需要选择已经绑定的借记卡，输入短信动态验证码即可完成支付。 </p>\r\n\r\n<p class=\"p2\">您在使用本服务时，应当认真确认交易信息，包括且不限于标的名称、数量、金额等，并按新浪支付业务流程发出符合《新浪支付服务协议》约定的指令。您认可和同意：您发出的指令不可撤回或撤销，新浪支付有权根据您的指令委托银行或第三方从储蓄卡中划扣资金给收款人。届时您不应以非本人意愿交易或其他任何原因要求新浪支付退款或承担其他责任。 </p>\r\n\r\n<p class=\"p2\">您在对使用本服务过程中发出指令的真实性及有效性承担全部责任； </p>\r\n\r\n<p class=\"p2\">您承诺，新浪支付依照您的指令进行操作的一切风险由您承担。 </p>\r\n\r\n<p class=\"p2\">您认可新浪支付账户的使用记录数据、交易金额数据等均以新浪支付系统平台记录的数据为准。 </p>\r\n\r\n<p class=\"p2\">您授权新浪支付有权留存您在新浪支付网站填写的相应信息，并授权新浪支付查询该储蓄卡信息，包括且不限于储蓄卡余额等内容，以供后续向您持续性地提供相应服务（包括但不限于将本信息用于向您推广、提供其他更加优质的产品或服务）。 出现下列情况之一的，新浪支付有权立即终止您使用新浪支付相关服务而无需承担任何责任：  </p>\r\n<p class=\"p2\">  （1）将本服务用于非法目的； </p>\r\n<p class=\"p2\">  （2）违反本协议的约定； </p>\r\n<p class=\"p2\">  （3）违反新浪支付和/或新浪支付关联公司和/或新浪金融集团旗下其他公司网站的条款、协议、规则、通告等相关规定及您与前述主体签署的任一协议，而被上述任一主体终止提供服务的； </p>\r\n<p class=\"p2\">  （4）新浪支付认为向您提供本服务存在风险的； </p>\r\n<p class=\"p2\">  （5）您的储蓄卡有效期届满（如有）。</p>\r\n\r\n<p class=\"p2\">若您未违反《新浪支付服务协议》及本协议约定且因不能归责于您的原因，造成储蓄卡内资金通过本服务出现损失，您可向新浪支付申请补偿。您应在知悉资金发生损失后及时通知新浪支付并按要求提交相关的申请材料和证明文件，新浪支付将通过自行补偿或保险公司赔偿的方式处理您的申请。具体处理方式由新浪支付自行选择决定，新浪支付承诺不会因此损害您的合法权益。 </p>\r\n\r\n<p class=\"p2\">不论新浪支付选择何种方式保障您使用本服务的资金安全，您同意并认可新浪支付最终的补偿行为或其它赔偿行为并不代表前述资金损失应归责于新浪支付，亦不代表新浪支付须为此承担其他任何责任。您同意，新浪支付在向您支付补偿的同时，即刻取得您可能或确实存在的就前述资金损失而产生的对第三方的所有债权及其他权利，包括但不限于就上述债权向第三方追偿的权利，且您不再就上述已经让渡给新浪支付的债权向该第三方主张任何权利，亦不再就资金损失向新浪支付主张任何权利。 </p>\r\n\r\n<p class=\"p2\">若您从其它渠道挽回了资金损失，或有新证据证明您涉嫌欺诈的，或者发生您应当自行承担责任的其他情形，您应在第一时间通知新浪支付并将返还新浪支付已补偿的款项，否则新浪支付有权自行采取包括但不限于从您的新浪支付账户（含该账户之关联账户）划扣等方式向您进行追偿。</p>\r\n\r\n<p class=\"p2\">您同意，本协议适用中华人民共和国大陆地区法律。因新浪支付与您就本协议的签订、履行或解释发生争议，双方应努力友好协商解决。如协商不成，由新浪支付所在地人民法院管辖审理双方的纠纷或争议。 </p>\r\n\r\n<p class=\"p2\">本协议内容包括协议正文及所有新浪支付已经发布的或将来可能发布的新浪支付服务的使用规则。所有规则为本协议不可分割的一部分，与协议正文具有相同法律效力。若您在本协议内容发生修订后，继续使用本服务的，则视为您同意最新修订的协议内容；否则您须立即停止使用本服务。 </p>\r\n\r\n<p class=\"p2\">本协议未尽事宜，您需遵守新浪支付网站上公布的《新浪支付服务协议》及相关规则。</p>\r\n</section>\r\n</body>\r\n</html>\r\n', '1459259044', '6', '1');
INSERT INTO `tb_agreement_0` VALUES ('diya', '1', '0', 'root', '', '<!doctype html>\r\n<html>\r\n<head>\r\n<meta charset=\"utf-8\">\r\n<title>抵押合同</title>\r\n<meta name=\"viewport\" id=\"viewport\" content=\"width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no\">\r\n<style type=\"text/css\">\r\n     html {\r\n	-webkit-text-size-adjust: 100%;\r\n	-ms-text-size-adjust: 100%;\r\n}\r\n\r\n/* 去除iPhone中默认的input样式 */\r\ninput[type=\"submit\"], input[type=\"reset\"], input[type=\"button\"], input {\r\n	-webkit-appearance: none;\r\n	resize: none;\r\n}\r\n\r\n/* 取消链接高亮  */\r\nbody, div, ul, li, ol, h1, h2, h3, h4, h5, h6, input, textarea, select, p, dl, dt, dd, a, img, button, form, table, th, tr, td, tbody, article, aside, details, figcaption, figure, footer, header, hgroup, menu, nav, section {\r\n	-webkit-tap-highlight-color: rgba(0, 0, 0, 0);\r\n}\r\n\r\n/* 设置HTML5元素为块 */\r\narticle, aside, details, figcaption, figure, footer, header, hgroup, menu, nav, section {\r\n	display: block;\r\n}\r\n\r\n/* 图片自适应 */\r\nimg {\r\n	width: 100%;\r\n	height: auto;\r\n	width: auto\\9; /* ie8 */\r\n	-ms-interpolation-mode: bicubic;/*为了照顾ie图片缩放失真*/\r\n}\r\n\r\n/* 初始化 */\r\nbody, div, ul, li, ol, h1, h2, h3, h4, h5, h6, input, textarea, select, p, dl, dt, dd, a, img, button, form, table, th, tr, td, tbody, article, aside, details, figcaption, figure, footer, header, hgroup, menu, nav, section {\r\n	margin: 0;\r\n	padding: 0;\r\n	border: none;\r\n}\r\nbody {\r\n	font-family: \'黑体\';\r\n	color: #3B3B3B;\r\n	background-color: #fff;\r\n}\r\nem, i {\r\n	font-style: normal;\r\n}\r\nstrong {\r\n	font-weight: normal;\r\n}\r\n.clearfix:after {\r\n	content: \"\";\r\n	display: block;\r\n	visibility: hidden;\r\n	height: 0;\r\n	clear: both;\r\n}\r\n.clearfix {\r\n	zoom: 1;\r\n}\r\na {\r\n	text-decoration: none;\r\n	color: #3b3b3b;\r\n	font-family: \'黑体\';\r\n}\r\na:hover {\r\n	\r\n	text-decoration: none;\r\n}\r\nul, ol {\r\n	list-style: none;\r\n}\r\nh1, h2, h3, h4, h5, h6 {\r\n	font-size: 100%;\r\n	font-family: \'黑体\';\r\n}\r\nimg {\r\n	border: none;\r\n}\r\n\r\n/*分割线*/\r\nhtml{\r\n	font-size: 40px;\r\n	margin:0 auto;\r\n	color: #3B3B3B;\r\n	min-width: 320px;\r\n	max-width: 640px; 	 	 	\r\n}\r\nbody{\r\n	margin:0 12px;\r\n}\r\n\r\nsection{ width:96%; margin:0 auto; overflow:hidden;}\r\nsection h3{ font-size:0.4rem; margin:0.5rem 0; text-align:center;}\r\nh4{ font-size:0.35rem; margin:0 auto; margin-bottom:0.5rem; font-weight:normal;}\r\n.p1{ font-size:0.4rem; font-weight:bold; margin-bottom:0.3rem;}\r\n.p2{ font-size:0.35rem; text-indent:1.5em; margin-bottom:0.125rem;}\r\n.p3{ font-size:0.35rem; text-indent:1.5em; margin-bottom:0.125rem;}\r\n.p4{ font-size:0.25rem; text-align:right; margin-top:0.2rem;}\r\n.p4 span{ border-bottom:1px solid #000;}\r\n.p4 span i{ font-style:normal; color:#e53d3a;}\r\n.diyaren{ width:3rem; border-bottom:1px solid #000;}\r\n.identy{ width:6rem;border-bottom:1px solid #000;}\r\n.num{ font-weight:bold;}\r\n.red{ color:#e53d3a; }\r\n.red .diyaren{ width:3rem; border-bottom:1px solid #e53d3a;}\r\n.user{ font-size:0.35rem; margin-bottom:0.3rem; overflow:hidden;}\r\n.user .left{ float:left; margin-right:3.5rem; text-indent:1.5em;}\r\n.user .right{ float:left;}\r\n.date{ font-size:0.35rem; text-align:right; margin-right:1rem;}\r\n\r\n \r\n </style>\r\n<!-- <script type=\"text/javascript\" src=\"http://code.jquery.com/jquery-1.11.3.min.js\"></script>-->\r\n <script type=\"text/javascript\">\r\n window.onload=function(){\r\n        function getFont(){\r\n                var html1=document.documentElement;     \r\n                var screen=html1.clientWidth;\r\n                if(screen <= 320){\r\n                     html1.fontSize = \'40px\';   \r\n                }else if(screen >= 640){\r\n                      html1.fontSize = \'80px\';  \r\n                }else{\r\n                      html1.style.fontSize=0.125*screen+\'px\';  \r\n                }\r\n                \r\n                }\r\n        getFont();\r\n        window.onresize=function(){\r\n                getFont();\r\n        }\r\n}\r\n \r\n </script>\r\n</head>\r\n\r\n<body>\r\n<section>\r\n<p class=\"p4\"> 合同编号：<span> 抵<i>2016-</i></span> </p>\r\n<h3>抵押合同</h3>\r\n<p class=\"p2\" style=\"text-indent:0;\">甲方（抵押权人）：<span class=\"diyaren\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></p>\r\n<p class=\"p2\" style=\"text-indent:0;\">身份证号码：<span class=\"identy\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></p>\r\n<p class=\"p2\" style=\"text-indent:0;\">乙方（抵押权人）：<span class=\"diyaren\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></p>\r\n<p class=\"p2\" style=\"text-indent:0;\">身份证号码：<span class=\"identy\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></p> \r\n<p class=\"p2\" style=\"text-indent:0;\">&nbsp;&nbsp;（抵押权人）：<span class=\"diyaren\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></p>\r\n<p class=\"p2\" style=\"text-indent:0;\">身份证号码：<span class=\"identy\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></p>\r\n<p class=\"p2\">因 <span class=\"diyaren\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>（《借款合同》中出借人）与<span class=\"diyaren\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>（《借款合同》中借款人）签定了编号为<span class=\"diyaren\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> 的《借款合同》，约定<span class=\"diyaren\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>（出借人）向<span class=\"diyaren\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>（借款人）提供借款<span class=\"diyaren\">&nbsp;&nbsp;&nbsp;&nbsp;</span>万元人民币，月利率款<span class=\"diyaren\">&nbsp;&nbsp;&nbsp;&nbsp;</span>%，期限<span class=\"diyaren\">&nbsp;&nbsp;</span>个月，自<span class=\"diyaren\">&nbsp;&nbsp;</span>年<span class=\"diyaren\">&nbsp;&nbsp;</span>月<span class=\"diyaren\">&nbsp;&nbsp;</span>日至<span class=\"diyaren\">&nbsp;&nbsp;</span>年<span class=\"diyaren\">&nbsp;&nbsp;</span>月<span class=\"diyaren\">&nbsp;&nbsp;</span>日止。在《借款合同》中约定了由本合同的甲方与本合同的乙方签订编号为<span class=\"diyaren\">&nbsp;&nbsp;&nbsp;&nbsp;</span>的《借款合同》的《抵押合同》，根据《中华人民共和国合同法》及《中华人民共和国担保法》等有关法律、法规的规定，当事各方本着平等自愿、诚实信用的原则，经协商一致，达成本合同。</p>  \r\n<p class=\"p2\"><span class=\"num\">第一条</span> 乙方将其合法拥有的房地产以不转移占有的方式抵押给甲方，为编号为<span class=\"diyaren\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>的《借款合同》中借款人<span class=\"diyaren\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>的<span class=\"diyaren\">&nbsp;&nbsp;&nbsp;&nbsp;</span>万元的借款提供担保。  </p>  \r\n<p class=\"p2\"><span class=\"num\">第二条</span> 抵押房地产坐落于：<span class=\"diyaren\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>，建筑面积：<span class=\"diyaren\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>平方米,房地产权证编号：<span class=\"diyaren\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>，房地产权利人：<span class=\"diyaren\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>，房地产协议价值：<span class=\"diyaren\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>万元整。</p>\r\n<p class=\"p3\"><span class=\"num\">第三条</span> <span class=\"red\">鉴于该抵押房地产已抵押给<span class=\"diyaren\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>，抵押担保金额为人民币押给<span class=\"diyaren\">&nbsp;&nbsp;&nbsp;&nbsp;</span>万元整，期限自<span class=\"diyaren\">&nbsp;&nbsp;</span>年<span class=\"diyaren\">&nbsp;&nbsp;</span>月<span class=\"diyaren\">&nbsp;&nbsp;</span>日至<span class=\"diyaren\">&nbsp;&nbsp;</span>年<span class=\"diyaren\">&nbsp;&nbsp;</span>月<span class=\"diyaren\">&nbsp;&nbsp;</span>日</span>，乙方愿将该抵押房地产<span class=\"red\">的余额部分再次</span>抵押给甲方，甲方已知其为第<span class=\"diyaren\">&nbsp;&nbsp;</span>顺位受偿人。</p>\r\n<p class=\"p2\"><span class=\"num\">第四条</span> 抵押房地产担保的范围为编号为<span class=\"diyaren\">&nbsp;&nbsp;&nbsp;&nbsp;</span>的《借款合同》项下的借款本金、利息、罚息、违约金及实现债权的费用等。担保的还款顺序与《借款合同》中约定的还款顺序一致。</p> \r\n<p class=\"p2\"><span class=\"num\">第五条</span> 乙方同意，当编号为<span class=\"diyaren\">&nbsp;&nbsp;&nbsp;&nbsp;</span>的《借款合同》中<span class=\"diyaren\">&nbsp;&nbsp;&nbsp;&nbsp;</span>（借款人）违约不能按期归还借款本息时，甲方可以按本合同约定行使抵押权。用于归还<span class=\"diyaren\">&nbsp;&nbsp;&nbsp;&nbsp;</span>所欠的<span class=\"diyaren\">&nbsp;&nbsp;</span>万元借款的本息及其他应承担的费用。</p> \r\n<p class=\"p2\"><span class=\"num\">第六条</span> 乙方特别声明与承诺</p>\r\n<p class=\"p2\">1、乙方保证向甲方提供的一切文件资料和情况说明真实、准确、合法、完整、有效。</p>\r\n<p class=\"p2\">2、乙方未隐瞒已发生或即将发生的可能降低乙方还款能力的事件（包括但不限于涉嫌违法、受到行政处罚、诉讼、仲裁、融资、贷款、处置主要资产、向第三方提供任何形式的担保）。</p>\r\n<p class=\"p2\">3、乙方保证对抵押房产拥有合法的完全所有权或处分权，在签署本合同时，抵押房产上不存在任何形式的权属争议或其他权利瑕疵，其他任何第三人不会向该抵押房产主张权利。</p> \r\n<p class=\"p2\">4、乙方郑重承诺：借款合同项下的抵押房产不是抵押人及其所抚养家属的生活必需品，抵押人保证不以此为由抗辩甲方及人民法院对该抵押房产的执行。如乙方未按时、足额履行还款义务导致甲方向人民法院申请强制执行或提起诉讼请求人民法院处置、变现抵押房产时，乙方及其所抚养的家属自愿无条件搬离该房屋并配合人民法院的执行工作，无条件腾空房屋并搬到甲方提供的周转房中居住，并保证该房屋的结构及其附属设施完好。如房屋有损坏或因未及时搬迁而给甲方造成损失，乙方应承担相应的赔偿责任，赔偿款于交付房产时一并交付。</p> \r\n<p class=\"p2\"><span class=\"num\">第七条</span> 强制执行公证 </p> \r\n<p class=\"p2\">1、本合同经<span class=\"red\" style=\"border-bottom:1px solid #e53d3a; text-align:center;\">上海市东方</span>公证处公证后具有强制执行效力。如借款期限届满或者抵押权人《借款合同》约定宣布合同提前到期，债务人（包括担保人）未及时、足额履行到期债务的，抵押权人有权向<span class=\"red\" style=\"border-bottom:1px solid #e53d3a; text-align:center;\">上海市东方</span>公证处申请出具执行证书，债务人及担保人则放弃抗辩权，自愿直接接受有管辖权的人民法院的强制执行。</p> \r\n<p class=\"p2\">2、<span class=\"red\" style=\"border-bottom:1px solid #e53d3a; text-align:center;\">上海市东方</span>公证处在受理出具执行证书的申请后，将以邮寄信函方式向：<span class=\"diyaren\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>（借款人）（包括担保人）核实债务履行情况，<span class=\"red\">寄件地址为</span><span class=\"red\" style=\"border-bottom:1px solid #e53d3a; text-align:center;\">上海市</span>，公证处信函一经寄出即视为送达。<span class=\"diyaren\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>（借款人）应在自<span class=\"red\" style=\"border-bottom:1px solid #e53d3a; text-align:center;\">上海市东方</span>公证处发出信函之日起的七日内按照信函要求向公证处说明债务履行情况，<span class=\"diyaren\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>（借款人）逾期未向公证处说明债务履行情况的或未按信函要求的方式说明债务履行情况的，视为<span class=\"diyaren\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>（借款人）承认未履行或未适当履行本合同项下债务的事实，公证处可依法出具执行证书；如<span class=\"diyaren\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>（借款人）向公证处承认未履行或未适当履行本合同项下债务的事实，公证处可依法出具执行证书；如<span class=\"diyaren\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>（借款人）否认未履行或未适当履行本合同项下债务但不能举证证明的，公证处可依法出具执行证书。</p>\r\n<p class=\"p2\"> 3、如<span class=\"diyaren\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>（借款人）向公证处否认未履行或未适当履行本合同项下债务的，<span class=\"diyaren\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>（借款人）应在向公证处否认未履行或未适当履行本合同项下债务之日起的七日内提交充分有效的证据。如<span class=\"diyaren\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>（借款人）未按期向公证处提交证据的，或提交的证据经公证处审查无法充分证明<span class=\"diyaren\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>（借款人）已经履行本合同义务的，或提交的证据经公证处审查无法充分证明<span class=\"diyaren\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>（借款人）有权延期履行债务或拒绝履行债务的，公证处可依法出具执行证书。</p> \r\n<p class=\"p2\">4、若<span class=\"diyaren\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>（借款人）及担保人需变更发函核实约定的地址，须书面通知债权人，由抵押权人、<span class=\"diyaren\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>（借款人）一并书面告知公证处。</p> \r\n<p class=\"p2\"><span class=\"num\">第八条</span> 本合同自双方签字之日起生效。本合同一式<span class=\"red\" style=\"border-bottom:1px solid #e53d3a; text-align:center;\">四</span><span class=\"red\">份</span>，甲、乙双方各执一份，房地产登记部门、同一式<span class=\"red\" style=\"border-bottom:1px solid #e53d3a; text-align:center;\">上海市东方</span>公证处各留存一份。</p>\r\n<div class=\"user\">\r\n   <div class=\"left\">甲方:</div>\r\n   <div class=\"right\">乙方:</div>\r\n</div> \r\n<div class=\"date\">年&nbsp;&nbsp;月&nbsp;&nbsp;日</div>      \r\n</section>\r\n</body>\r\n</html>\r\n', '1458875028', '1', '1');
INSERT INTO `tb_agreement_0` VALUES ('invest', '1', '0', 'root', '', '<!doctype html>\r\n<html>\r\n<head>\r\n<meta charset=\"utf-8\">\r\n<title>借款协议</title>\r\n<meta name=\"viewport\" id=\"viewport\" content=\"width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no\">\r\n<style type=\"text/css\">\r\n     html {\r\n	-webkit-text-size-adjust: 100%;\r\n	-ms-text-size-adjust: 100%;\r\n}\r\n\r\n/* 去除iPhone中默认的input样式 */\r\ninput[type=\"submit\"], input[type=\"reset\"], input[type=\"button\"], input {\r\n	-webkit-appearance: none;\r\n	resize: none;\r\n}\r\n\r\n/* 取消链接高亮  */\r\nbody, div, ul, li, ol, h1, h2, h3, h4, h5, h6, input, textarea, select, p, dl, dt, dd, a, img, button, form, table, th, tr, td, tbody, article, aside, details, figcaption, figure, footer, header, hgroup, menu, nav, section {\r\n	-webkit-tap-highlight-color: rgba(0, 0, 0, 0);\r\n}\r\n\r\n/* 设置HTML5元素为块 */\r\narticle, aside, details, figcaption, figure, footer, header, hgroup, menu, nav, section {\r\n	display: block;\r\n}\r\n\r\n/* 图片自适应 */\r\nimg {\r\n	width: 100%;\r\n	height: auto;\r\n	width: auto\\9; /* ie8 */\r\n	-ms-interpolation-mode: bicubic;/*为了照顾ie图片缩放失真*/\r\n}\r\n\r\n/* 初始化 */\r\nbody, div, ul, li, ol, h1, h2, h3, h4, h5, h6, input, textarea, select, p, dl, dt, dd, a, img, button, form, table, th, tr, td, tbody, article, aside, details, figcaption, figure, footer, header, hgroup, menu, nav, section {\r\n	margin: 0;\r\n	padding: 0;\r\n	border: none;\r\n}\r\nbody {\r\n	font-family: \'黑体\';\r\n	color: #3B3B3B;\r\n	background-color: #fff;\r\n}\r\nem, i {\r\n	font-style: normal;\r\n}\r\nstrong {\r\n	font-weight: normal;\r\n}\r\n.clearfix:after {\r\n	content: \"\";\r\n	display: block;\r\n	visibility: hidden;\r\n	height: 0;\r\n	clear: both;\r\n}\r\n.clearfix {\r\n	zoom: 1;\r\n}\r\na {\r\n	text-decoration: none;\r\n	color: #3b3b3b;\r\n	font-family: \'黑体\';\r\n}\r\na:hover {\r\n	\r\n	text-decoration: none;\r\n}\r\nul, ol {\r\n	list-style: none;\r\n}\r\nh1, h2, h3, h4, h5, h6 {\r\n	font-size: 100%;\r\n	font-family: \'黑体\';\r\n}\r\nimg {\r\n	border: none;\r\n}\r\n\r\n/*分割线*/\r\nhtml{\r\n	font-size: 40px;\r\n	margin:0 auto;\r\n	color: #3B3B3B;\r\n	min-width: 320px;\r\n	max-width: 640px; 	 	 	\r\n}\r\nbody{\r\n	margin:0 12px;\r\n}\r\n\r\nsection{ width:96%; margin:0 auto; overflow:hidden;}\r\nsection h3{ font-size:0.4rem; margin:0.5rem 0; text-align:center;}\r\nh4{ font-size:0.35rem; margin:0 auto; margin-bottom:0.5rem; font-weight:normal;}\r\n.p1{ font-size:0.4rem; font-weight:bold; margin-bottom:0.3rem;}\r\n.p2{ font-size:0.35rem; text-indent:1.5em; margin-bottom:0.125rem;}\r\n.p3{ font-size:0.35rem; text-indent:1.5em; margin-bottom:0.125rem;}\r\n.p4{ font-size:0.25rem; text-align:right; margin-top:0.2rem;}\r\n.p4 span{ border-bottom:1px solid #000;}\r\n.p4 span i{ font-style:normal; color:#e53d3a;}\r\n.diyaren{ width:3rem; border-bottom:1px solid #000;}\r\n.identy{ width:6rem;border-bottom:1px solid #000;}\r\n.num{ font-weight:bold;}\r\n.red{ color:#e53d3a; }\r\n.red .diyaren{ width:3rem; border-bottom:1px solid #e53d3a;}\r\n.user{ font-size:0.35rem; margin-bottom:0.3rem; overflow:hidden;}\r\n.user .left{ float:left; margin-right:3.5rem; text-indent:1.5em;}\r\n.user .right{ float:left;}\r\n.date{ font-size:0.35rem; text-align:right; margin-right:1rem;}\r\n\r\n \r\n </style>\r\n <!--<script type=\"text/javascript\" src=\"http://code.jquery.com/jquery-1.11.3.min.js\"></script>-->\r\n <script type=\"text/javascript\">\r\n window.onload=function(){\r\n        function getFont(){\r\n                var html1=document.documentElement;     \r\n                var screen=html1.clientWidth;\r\n                if(screen <= 320){\r\n                     html1.fontSize = \'40px\';   \r\n                }else if(screen >= 640){\r\n                      html1.fontSize = \'80px\';  \r\n                }else{\r\n                      html1.style.fontSize=0.125*screen+\'px\';  \r\n                }\r\n                \r\n                }\r\n        getFont();\r\n        window.onresize=function(){\r\n                getFont();\r\n        }\r\n}\r\n \r\n </script>\r\n</head>\r\n\r\n<body>\r\n<section>\r\n<p class=\"p4\">合同编号：<span style=\"color:#e53d3a; border-bottom:1px solid #e53d3a;\">借2016-</span> </p>\r\n<h3>借款合同</h3>\r\n<p class=\"p2\" style=\"text-indent:0;\">甲方（出借人）：<span class=\"diyaren\">                        </span></p>\r\n<p class=\"p2\" style=\"text-indent:0;\">身份证号码：<span class=\"identy\">                              </span></p>\r\n<p class=\"p2\" style=\"text-indent:0;\">乙方（借款人）：<span class=\"diyaren\">                        </span></p>\r\n<p class=\"p2\" style=\"text-indent:0;\">身份证号码：<span class=\"identy\">                              </span></p> \r\n<p class=\"p2\" style=\"text-indent:0;\">丙方（借款平台）：上海小虾网络科技有限公司</p>\r\n<p class=\"p2\" style=\"margin:0.3rem 0;\">特别提示：在签订本合同之前，请务必仔细阅读本合同全部条款。如有任何疑问或不明之处，请及时向丙方及专业人士咨询。本合同一经签订，即视为各方理解并同意本合同全部条款。</p>\r\n<p class=\"p2\"><span class=\"num\">第一条</span> 根据《中华人民共和国合同法》及《中华人民共和国担保法》等有关法律、法规的规定，当事各方本着平等自愿、诚实信用的原则，经协商一致，达成本合同。</p>\r\n<p class=\"p2\"><span class=\"num\">第二条</span> 借款期限、利息及支付</p>\r\n<p class=\"p2\">2．1根据三方协商意见，甲方向乙方提供借款<span class=\"diyaren\">       </span>万元人民币。</p>\r\n<p class=\"p2\">2．2本次借款的利息按月计算，三方商定为月利率<span class=\"diyaren\">       </span>，由乙方按本合同约定的时间支付，利息的计算足一个月的按借款额乘以月利息，不足一个月的，按实际借款的天数除以30再乘以月利率计算。</p>\r\n<p class=\"p2\">2．3本次借款的期限<span class=\"diyaren\">       </span>个月，自<span class=\"diyaren\">  </span>年<span class=\"diyaren\">  </span>月<span class=\"diyaren\">  </span>日至<span class=\"diyaren\">  </span>年<span class=\"diyaren\">  </span>月<span class=\"diyaren\">  </span>日止。到期后若需要续借，则需要重新签订借款合同。</p>\r\n<p class=\"p2\">2．4本次借款以甲乙双方在丙方平台开立的第三方存管账户的转账为准，资金自甲方的第三方存管账户中转入到乙方的第三方存管账户视为借款的转款完成，利息自借款转款完成时开始计算。因丙方的撮合原因导致甲方的利息计算与本合同的利息计算不符的，差额部分由丙方承担。</p>\r\n<p class=\"p2\"><span class=\"num\">第三条</span> 乙方借款用途为<span class=\"identy\">                   </span>，乙方应按合同约定用途使用借款，不得挪作他用。甲方及丙方有权监督乙方的款项使用情况，若发现乙方未能按本借款用途使用的款项的，甲方有权提前收回借款，并由乙方承担按本合同约定的借款期限计算的利息及实际借款金额10%的违约金，该违约金由甲、丙两方平分。</p>\r\n<p class=\"p2\"><span class=\"num\">第四条</span> 借款本息的归还</p>\r\n<p class=\"p2\">4．1本次借款的本金由乙方在借款到期时一次性归还给甲方。</p>\r\n<p class=\"p2\">4．2本次借款的利息按月支付，自借款实际到账日起一个整月的，当日支付当月利息，借款到期日前不足一个月的，按实际借款天数按1．2的标准支付利息。</p>\r\n<p class=\"p2\">4．3经甲、乙、丙三方协商一致同意，甲方在本次借款中只收取月息<span class=\"diyaren\">       </span>的利息，乙方按照本合同2．2约定需要支付的利息的多余部分作为丙方提供借款平台的服务费用直接支付给丙方。</p>\r\n<p class=\"p2\">4．4乙方归还借款的本息，仍通过甲、乙、丙三方在第三方存管开立的存管账户中完成，乙方应当按本合同第四条前三款的约定将本息转入甲、丙在第三方存管开立的账户中，款项的到达以第三方存管的转账记录为准。</p>\r\n<p class=\"p2 num\">第五条 本合同项下的借款发放后，乙方可提前偿还全部借款，借款利息按合同约定借款期限计算。</p>\r\n<p class=\"p2\"><span class=\"num\">第六条</span> 乙方每笔还款不足以清偿到期债务的，按此先后顺序清偿：1、实现债权的费用（包括但不限于律师费、财产保全费、诉讼费、执行费、拍卖费、差旅费、公证费、欠付的各类费用）；2、违约金；3、逾期罚息；4、借款利息；5、借款本金。</p>\r\n<p class=\"p2\"><span class=\"num\">第七条</span> 乙方特别声明与承诺</p>\r\n<p class=\"p2\">7．1对乙方具有约束力的任何合同或担保权利，与签署和履行本合同并无抵触之处。</p>\r\n<p class=\"p2\">7．2乙方保证向甲方提供的一切文件资料和情况说明真实、准确、合法、完整、有效。</p>\r\n<p class=\"p2\">7．3乙方未隐瞒已发生或即将发生的可能降低乙方还款能力的事件（包括但不限于涉嫌违法、受到行政处罚、诉讼、仲裁、融资、贷款、处置主要资产、向第三方提供任何形式的担保）。</p>\r\n<p class=\"p2\">7．4乙方自愿接受甲、丙方对于本次借款使用的监督，严格按照本合同约定的借款用途使用本借款。</p>\r\n<p class=\"p2\"><span class=\"num\">第八条</span> 甲方的权利与义务</p>\r\n<p class=\"p2\">8．1甲方有权按照本合同的约定收回借款的本金及利息。</p>\r\n<p class=\"p2\">8．2甲方有权监督乙方对本次借款的使用。</p>\r\n<p class=\"p2\">8．3乙方未按甲方要求提供申请借款所需的全部文件资料的，甲方有权拒绝放款。</p>\r\n<p class=\"p2\">8．4为本合同项下借款债务设立的担保未实现（包括但不限于办妥抵押登记、出质登记、质物移交、保证人签署担保函），甲方有权拒绝放款。</p>\r\n<p class=\"p2\">8．5乙方挂失质物或担保人担保能力下降，甲方有权要求乙方重新提供担保，乙方未能提供担保或提供担保不足以担保本合同项下的债务的，甲方有权提前收回借款。</p>\r\n<p class=\"p2\">8．6发生可能降低乙方还款能力的事件（包括但不限于涉嫌违法、受到行政处罚、诉讼、仲裁、融资、贷款、处置主要资产、向第三方提供任何形式的担保）或乙方发生其他足以影响其偿还债务能力的事件的，甲方有权提前收回借款还要求乙方追加担保。</p>\r\n<p class=\"p2 num\">如出现8．5、8．6情况时甲方已发放借款的，甲方有权宣布合同提前到期，并有权提前收回全部借款本息，乙方应在接到甲方通知之日起三个工作日内清偿，否则按逾期处理，由此产生的不利后果由乙方承担。</p>\r\n<p class=\"p2\"><span class=\"num\">第九条</span> 乙方的权利和义务</p>\r\n<p class=\"p2\">9．1乙方有权利按照本合同约定的时间及金额得到借款。并自主支配所借款项的使用。</p>\r\n<p class=\"p2\">9．2乙方应按本合同的约定按时、足额归还借款本金及利息。</p>\r\n<p class=\"p2\">9．3乙方须按甲方、丙方的要求提供借款所需的各种资料，并保证所提供的资的真实性、合法性、有效性。</p>\r\n<p class=\"p2\">9．4乙方须按甲、丙方的要求提供担保或促使提供担保的担保人尽快履行担保义务。</p>\r\n<p class=\"p2\">9．5乙方自觉按受甲、丙方对本次借款资金使用的监督。</p>\r\n<p class=\"p2\">9．6乙方的居住地、联系方式、办公地址等发生变更，必须在变更后三日内书面通知甲方。</p>\r\n<p class=\"p2\">9．7乙方发生可能降低乙方还款能力的事件（包括但不限于涉嫌违法、受到行政处罚、诉讼、仲裁），应立刻书面通知甲方。</p>\r\n<p class=\"p2\">9．8乙方进行融资、贷款、处置主要资产或向第三方提供任何形式的担保需经甲方书面同意。</p>\r\n<p class=\"p2\"><span class=\"num\">第十条</span> 丙方的权利和义务</p>\r\n<p class=\"p2\">10．1丙方有权按照本合同的约定收取平台撮合服务费。</p>\r\n<p class=\"p2\">10．2丙方有权监督乙方对本次借款的使用，当发现乙方有违约使用资金的情况时，应及时通知甲方。</p>\r\n<p class=\"p2\">10．3丙方应当及时关注乙方经济状况，当乙方经济状况发生明显恶化时，及时通知甲方。</p>\r\n<p class=\"p2\">10．4在乙方归还本金或利息时间点到时，丙方及时提醒乙方还款，若有违约情况，及时通知甲方。</p>\r\n<p class=\"p2\">10．5丙方应当积极撮合甲乙双方借款事宜，若因撮合时间差产生的甲方应收利息，由丙方负责支付。</p>\r\n<p class=\"p2\">10．6丙方应当积极跟踪乙方或乙方指定的担保人提供担保登记事项。</p>\r\n<p class=\"p2\"><span class=\"num\">第十一条</span> 担保</p>\r\n<p class=\"p2\">11．1乙方应当为本次借款行为提供抵押、质押等担保手续。</p>\r\n<p class=\"p2\">11．2乙方可以指定第三人为本次借款提供担保，但该第三人及其提供的担保财产应当经过甲方、丙方的认可。</p>\r\n<p class=\"p2\">11．3乙方或乙方指定的担保人应当就本次借款另行签订《抵押合同》，《抵押合同》的编号为<span class=\"diyaren\">       </span>。</p>\r\n<p class=\"p2\">11．4甲方、丙方共同指定<span class=\"diyaren\">       </span>为代理人，<span class=\"diyaren\">       </span>可以以自己的名义与乙方或乙方指定的担保人签订担保合同，并办理相关的抵押、质押登记，所产生的权利和义务均同甲、丙方承担。</p>\r\n<p class=\"p2\"><span class=\"num\">第十二条</span> 违约责任</p>\r\n<p class=\"p2\">12．1本合同生效后，任何一方不履行、不完全履行或延迟履行合同义务的，应当按照约定承担违约责任，并赔偿由此给对方造成的损失（包括实际损失和预期收益）。</p>\r\n<p class=\"p2 num\">12．2乙方有下列情形之一的，即构成违约：</p>\r\n<p class=\"p2 num\">（1）未按期偿还任何一期借款本金或利息的；</p>\r\n<p class=\"p2 num\">（2）未按约定用途使用借款的；</p>\r\n<p class=\"p2 num\">（3）违反声明与承诺条款的；</p>\r\n<p class=\"p2 num\">（4）未按约定履行通知义务或者未经甲方书面同意而实施相关行为的；</p>\r\n<p class=\"p2 num\">（5）发生其他违反本合同的行为的。</p>\r\n<p class=\"p2 num\">12．3如乙方未按本合同约定的期限偿还全部借款本息，或乙方接到甲方根据本合同第八条之规定宣布合同提前到期的通知之日起三个工作日内未能偿还全部借款本息则构成逾期，需向甲方支付利息、违约金（利息以本合同的约定按实际占用天数计算，违约金按实际借款金额的10%计算，逾期60日的，按逾期金额日千分之二计算至实际偿还）。</p>\r\n<p class=\"p2\">12．4如乙方未按时、足额还款或出现其他风险导致甲方向人民法院起诉申请执行，乙方应承担甲方因处理此类情况而支出的全部费用（包括但不限于律师费、财产保全费、诉讼费、执行费、拍卖费、差旅费、公证费、欠付的各类费用）。</p>\r\n<p class=\"p2\">12．5如乙方未按时足额履行还款义务，乙方同意并授权甲方可以将乙方的违约情况向媒体发布公告等形式进行催收，费用由乙方承担，乙方不持异议。</p>\r\n<p class=\"p2\"><span class=\"num\">第十三条</span> 其他约定条款</p>\r\n<p class=\"p2\">13．1借款人为两人或两人以上的，各借款人对本合同项下债务承担连带责任。任一借款人有权代表全体借款人处理与本合同有关的全部事务，包括但不限于收取和归还借款，签署借据、补充合同等。</p>\r\n<p class=\"p2\">13．2乙方同意甲方、丙方向信用信息管理机构查询其信用状况，并同意甲方、丙方根据本合同的履行情况向信用信息管理机构或有关方提供乙方的信用信息。</p>\r\n<p class=\"p2\">13．3甲方有权将本合同项下债权转让给任何第三方而无需征得乙方同意。乙方同意甲方为债权转让之目的使用乙方的信息披露给有关方，包括但不限于真实姓名、联系方式、信用状况等必要的个人信息和交易信息。乙方同意授权委托甲方、丙方在债权转让后代为管理后续还款事项，包括但不限于（1）在收到乙方支付的应偿还借款本息后，代乙方向债权受让人偿还；（2）收取与乙方相关的法律文件，如债权转让通知书等。</p>\r\n<p class=\"p2\"><span class=\"num\">第十四条</span> 因本合同发生的纠纷，由<span class=\"num\">丙方住所地</span>的人民法院管辖。</p>\r\n<p class=\"p2\"><span class=\"num\">第十五条</span> 本合同自各方签署之日成立，自甲方向乙方提供借款之日起生效。本合同一式<span class=\"red\" style=\"border-bottom:1px solid #e53d3a; text-align:center;\">叁</span><span class=\"red\">份</span>，甲、乙、丙三方各执一份。</p>\r\n<div class=\"user\" style=\"margin-top:0.3rem;\">\r\n   <div class=\"left\" style=\"text-indent:0;\">甲方:</div>\r\n   <div class=\"right\">乙方:</div>\r\n</div>\r\n<p class=\"p2\" style=\"text-indent:0;\">丙方：<span class=\"num\">上海小虾网络科技有限公司</span></p>\r\n<div class=\"date\">年  月  日</div> \r\n</section>\r\n</body>\r\n</html>\r\n', '1459237907', '2', '1');
INSERT INTO `tb_agreement_0` VALUES ('jiekuan', '1', '0', 'root', '', '<!doctype html>\r\n<html>\r\n<head>\r\n<meta charset=\"utf-8\">\r\n<title>借款协议</title>\r\n<meta name=\"viewport\" id=\"viewport\" content=\"width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no\">\r\n<style type=\"text/css\">\r\n     html {\r\n	-webkit-text-size-adjust: 100%;\r\n	-ms-text-size-adjust: 100%;\r\n}\r\n\r\n/* 去除iPhone中默认的input样式 */\r\ninput[type=\"submit\"], input[type=\"reset\"], input[type=\"button\"], input {\r\n	-webkit-appearance: none;\r\n	resize: none;\r\n}\r\n\r\n/* 取消链接高亮  */\r\nbody, div, ul, li, ol, h1, h2, h3, h4, h5, h6, input, textarea, select, p, dl, dt, dd, a, img, button, form, table, th, tr, td, tbody, article, aside, details, figcaption, figure, footer, header, hgroup, menu, nav, section {\r\n	-webkit-tap-highlight-color: rgba(0, 0, 0, 0);\r\n}\r\n\r\n/* 设置HTML5元素为块 */\r\narticle, aside, details, figcaption, figure, footer, header, hgroup, menu, nav, section {\r\n	display: block;\r\n}\r\n\r\n/* 图片自适应 */\r\nimg {\r\n	width: 100%;\r\n	height: auto;\r\n	width: auto\\9; /* ie8 */\r\n	-ms-interpolation-mode: bicubic;/*为了照顾ie图片缩放失真*/\r\n}\r\n\r\n/* 初始化 */\r\nbody, div, ul, li, ol, h1, h2, h3, h4, h5, h6, input, textarea, select, p, dl, dt, dd, a, img, button, form, table, th, tr, td, tbody, article, aside, details, figcaption, figure, footer, header, hgroup, menu, nav, section {\r\n	margin: 0;\r\n	padding: 0;\r\n	border: none;\r\n}\r\nbody {\r\n	font-family: \'黑体\';\r\n	color: #3B3B3B;\r\n	background-color: #fff;\r\n}\r\nem, i {\r\n	font-style: normal;\r\n}\r\nstrong {\r\n	font-weight: normal;\r\n}\r\n.clearfix:after {\r\n	content: \"\";\r\n	display: block;\r\n	visibility: hidden;\r\n	height: 0;\r\n	clear: both;\r\n}\r\n.clearfix {\r\n	zoom: 1;\r\n}\r\na {\r\n	text-decoration: none;\r\n	color: #3b3b3b;\r\n	font-family: \'黑体\';\r\n}\r\na:hover {\r\n	\r\n	text-decoration: none;\r\n}\r\nul, ol {\r\n	list-style: none;\r\n}\r\nh1, h2, h3, h4, h5, h6 {\r\n	font-size: 100%;\r\n	font-family: \'黑体\';\r\n}\r\nimg {\r\n	border: none;\r\n}\r\n\r\n/*分割线*/\r\nhtml{\r\n	font-size: 40px;\r\n	margin:0 auto;\r\n	color: #3B3B3B;\r\n	min-width: 320px;\r\n	max-width: 640px; 	 	 	\r\n}\r\nbody{\r\n	margin:0 12px;\r\n}\r\n\r\nsection{ width:96%; margin:0 auto; overflow:hidden;}\r\nsection h3{ font-size:0.4rem; margin:0.5rem 0; text-align:center;}\r\nh4{ font-size:0.35rem; margin:0 auto; margin-bottom:0.5rem; font-weight:normal;}\r\n.p1{ font-size:0.4rem; font-weight:bold; margin-bottom:0.3rem;}\r\n.p2{ font-size:0.35rem; text-indent:1.5em; margin-bottom:0.125rem;}\r\n.p3{ font-size:0.35rem; text-indent:1.5em; margin-bottom:0.125rem;}\r\n.p4{ font-size:0.25rem; text-align:right; margin-top:0.2rem;}\r\n.p4 span{ border-bottom:1px solid #000;}\r\n.p4 span i{ font-style:normal; color:#e53d3a;}\r\n.diyaren{ width:3rem; border-bottom:1px solid #000;}\r\n.identy{ width:6rem;border-bottom:1px solid #000;}\r\n.num{ font-weight:bold;}\r\n.red{ color:#e53d3a; }\r\n.red .diyaren{ width:3rem; border-bottom:1px solid #e53d3a;}\r\n.user{ font-size:0.35rem; margin-bottom:0.3rem; overflow:hidden;}\r\n.user .left{ float:left; margin-right:3.5rem; text-indent:1.5em;}\r\n.user .right{ float:left;}\r\n.date{ font-size:0.35rem; text-align:right; margin-right:1rem;}\r\n\r\n \r\n </style>\r\n <!--<script type=\"text/javascript\" src=\"http://code.jquery.com/jquery-1.11.3.min.js\"></script>-->\r\n <script type=\"text/javascript\">\r\n window.onload=function(){\r\n        function getFont(){\r\n                var html1=document.documentElement;     \r\n                var screen=html1.clientWidth;\r\n                if(screen <= 320){\r\n                     html1.fontSize = \'40px\';   \r\n                }else if(screen >= 640){\r\n                      html1.fontSize = \'80px\';  \r\n                }else{\r\n                      html1.style.fontSize=0.125*screen+\'px\';  \r\n                }\r\n                \r\n                }\r\n        getFont();\r\n        window.onresize=function(){\r\n                getFont();\r\n        }\r\n}\r\n \r\n </script>\r\n</head>\r\n\r\n<body>\r\n<section>\r\n<p class=\"p4\">合同编号：<span style=\"color:#e53d3a; border-bottom:1px solid #e53d3a;\">借2016-</span> </p>\r\n<h3>借款合同</h3>\r\n<p class=\"p2\" style=\"text-indent:0;\">甲方（出借人）：<span class=\"diyaren\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></p>\r\n<p class=\"p2\" style=\"text-indent:0;\">身份证号码：<span class=\"identy\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></p>\r\n<p class=\"p2\" style=\"text-indent:0;\">乙方（借款人）：<span class=\"diyaren\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></p>\r\n<p class=\"p2\" style=\"text-indent:0;\">身份证号码：<span class=\"identy\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></p> \r\n<p class=\"p2\" style=\"text-indent:0;\">丙方（借款平台）：上海小虾网络科技有限公司</p>\r\n<p class=\"p2\" style=\"margin:0.3rem 0;\">特别提示：在签订本合同之前，请务必仔细阅读本合同全部条款。如有任何疑问或不明之处，请及时向丙方及专业人士咨询。本合同一经签订，即视为各方理解并同意本合同全部条款。</p>\r\n<p class=\"p2\"><span class=\"num\">第一条</span> 根据《中华人民共和国合同法》及《中华人民共和国担保法》等有关法律、法规的规定，当事各方本着平等自愿、诚实信用的原则，经协商一致，达成本合同。</p>\r\n<p class=\"p2\"><span class=\"num\">第二条</span> 借款期限、利息及支付</p>\r\n<p class=\"p2\">2．1根据三方协商意见，甲方向乙方提供借款<span class=\"diyaren\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>万元人民币。</p>\r\n<p class=\"p2\">2．2本次借款的利息按月计算，三方商定为月利率<span class=\"diyaren\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>，由乙方按本合同约定的时间支付，利息的计算足一个月的按借款额乘以月利息，不足一个月的，按实际借款的天数除以30再乘以月利率计算。</p>\r\n<p class=\"p2\">2．3本次借款的期限<span class=\"diyaren\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>个月，自<span class=\"diyaren\">&nbsp;&nbsp;</span>年<span class=\"diyaren\">&nbsp;&nbsp;</span>月<span class=\"diyaren\">&nbsp;&nbsp;</span>日至<span class=\"diyaren\">&nbsp;&nbsp;</span>年<span class=\"diyaren\">&nbsp;&nbsp;</span>月<span class=\"diyaren\">&nbsp;&nbsp;</span>日止。到期后若需要续借，则需要重新签订借款合同。</p>\r\n<p class=\"p2\">2．4本次借款以甲乙双方在丙方平台开立的第三方存管账户的转账为准，资金自甲方的第三方存管账户中转入到乙方的第三方存管账户视为借款的转款完成，利息自借款转款完成时开始计算。因丙方的撮合原因导致甲方的利息计算与本合同的利息计算不符的，差额部分由丙方承担。</p>\r\n<p class=\"p2\"><span class=\"num\">第三条</span> 乙方借款用途为<span class=\"identy\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>，乙方应按合同约定用途使用借款，不得挪作他用。甲方及丙方有权监督乙方的款项使用情况，若发现乙方未能按本借款用途使用的款项的，甲方有权提前收回借款，并由乙方承担按本合同约定的借款期限计算的利息及实际借款金额10%的违约金，该违约金由甲、丙两方平分。</p>\r\n<p class=\"p2\"><span class=\"num\">第四条</span> 借款本息的归还</p>\r\n<p class=\"p2\">4．1本次借款的本金由乙方在借款到期时一次性归还给甲方。</p>\r\n<p class=\"p2\">4．2本次借款的利息按月支付，自借款实际到账日起一个整月的，当日支付当月利息，借款到期日前不足一个月的，按实际借款天数按1．2的标准支付利息。</p>\r\n<p class=\"p2\">4．3经甲、乙、丙三方协商一致同意，甲方在本次借款中只收取月息<span class=\"diyaren\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>的利息，乙方按照本合同2．2约定需要支付的利息的多余部分作为丙方提供借款平台的服务费用直接支付给丙方。</p>\r\n<p class=\"p2\">4．4乙方归还借款的本息，仍通过甲、乙、丙三方在第三方存管开立的存管账户中完成，乙方应当按本合同第四条前三款的约定将本息转入甲、丙在第三方存管开立的账户中，款项的到达以第三方存管的转账记录为准。</p>\r\n<p class=\"p2 num\">第五条 本合同项下的借款发放后，乙方可提前偿还全部借款，借款利息按合同约定借款期限计算。</p>\r\n<p class=\"p2\"><span class=\"num\">第六条</span> 乙方每笔还款不足以清偿到期债务的，按此先后顺序清偿：1、实现债权的费用（包括但不限于律师费、财产保全费、诉讼费、执行费、拍卖费、差旅费、公证费、欠付的各类费用）；2、违约金；3、逾期罚息；4、借款利息；5、借款本金。</p>\r\n<p class=\"p2\"><span class=\"num\">第七条</span> 乙方特别声明与承诺</p>\r\n<p class=\"p2\">7．1对乙方具有约束力的任何合同或担保权利，与签署和履行本合同并无抵触之处。</p>\r\n<p class=\"p2\">7．2乙方保证向甲方提供的一切文件资料和情况说明真实、准确、合法、完整、有效。</p>\r\n<p class=\"p2\">7．3乙方未隐瞒已发生或即将发生的可能降低乙方还款能力的事件（包括但不限于涉嫌违法、受到行政处罚、诉讼、仲裁、融资、贷款、处置主要资产、向第三方提供任何形式的担保）。</p>\r\n<p class=\"p2\">7．4乙方自愿接受甲、丙方对于本次借款使用的监督，严格按照本合同约定的借款用途使用本借款。</p>\r\n<p class=\"p2\"><span class=\"num\">第八条</span> 甲方的权利与义务</p>\r\n<p class=\"p2\">8．1甲方有权按照本合同的约定收回借款的本金及利息。</p>\r\n<p class=\"p2\">8．2甲方有权监督乙方对本次借款的使用。</p>\r\n<p class=\"p2\">8．3乙方未按甲方要求提供申请借款所需的全部文件资料的，甲方有权拒绝放款。</p>\r\n<p class=\"p2\">8．4为本合同项下借款债务设立的担保未实现（包括但不限于办妥抵押登记、出质登记、质物移交、保证人签署担保函），甲方有权拒绝放款。</p>\r\n<p class=\"p2\">8．5乙方挂失质物或担保人担保能力下降，甲方有权要求乙方重新提供担保，乙方未能提供担保或提供担保不足以担保本合同项下的债务的，甲方有权提前收回借款。</p>\r\n<p class=\"p2\">8．6发生可能降低乙方还款能力的事件（包括但不限于涉嫌违法、受到行政处罚、诉讼、仲裁、融资、贷款、处置主要资产、向第三方提供任何形式的担保）或乙方发生其他足以影响其偿还债务能力的事件的，甲方有权提前收回借款还要求乙方追加担保。</p>\r\n<p class=\"p2 num\">如出现8．5、8．6情况时甲方已发放借款的，甲方有权宣布合同提前到期，并有权提前收回全部借款本息，乙方应在接到甲方通知之日起三个工作日内清偿，否则按逾期处理，由此产生的不利后果由乙方承担。</p>\r\n<p class=\"p2\"><span class=\"num\">第九条</span> 乙方的权利和义务</p>\r\n<p class=\"p2\">9．1乙方有权利按照本合同约定的时间及金额得到借款。并自主支配所借款项的使用。</p>\r\n<p class=\"p2\">9．2乙方应按本合同的约定按时、足额归还借款本金及利息。</p>\r\n<p class=\"p2\">9．3乙方须按甲方、丙方的要求提供借款所需的各种资料，并保证所提供的资的真实性、合法性、有效性。</p>\r\n<p class=\"p2\">9．4乙方须按甲、丙方的要求提供担保或促使提供担保的担保人尽快履行担保义务。</p>\r\n<p class=\"p2\">9．5乙方自觉按受甲、丙方对本次借款资金使用的监督。</p>\r\n<p class=\"p2\">9．6乙方的居住地、联系方式、办公地址等发生变更，必须在变更后三日内书面通知甲方。</p>\r\n<p class=\"p2\">9．7乙方发生可能降低乙方还款能力的事件（包括但不限于涉嫌违法、受到行政处罚、诉讼、仲裁），应立刻书面通知甲方。</p>\r\n<p class=\"p2\">9．8乙方进行融资、贷款、处置主要资产或向第三方提供任何形式的担保需经甲方书面同意。</p>\r\n<p class=\"p2\"><span class=\"num\">第十条</span> 丙方的权利和义务</p>\r\n<p class=\"p2\">10．1丙方有权按照本合同的约定收取平台撮合服务费。</p>\r\n<p class=\"p2\">10．2丙方有权监督乙方对本次借款的使用，当发现乙方有违约使用资金的情况时，应及时通知甲方。</p>\r\n<p class=\"p2\">10．3丙方应当及时关注乙方经济状况，当乙方经济状况发生明显恶化时，及时通知甲方。</p>\r\n<p class=\"p2\">10．4在乙方归还本金或利息时间点到时，丙方及时提醒乙方还款，若有违约情况，及时通知甲方。</p>\r\n<p class=\"p2\">10．5丙方应当积极撮合甲乙双方借款事宜，若因撮合时间差产生的甲方应收利息，由丙方负责支付。</p>\r\n<p class=\"p2\">10．6丙方应当积极跟踪乙方或乙方指定的担保人提供担保登记事项。</p>\r\n<p class=\"p2\"><span class=\"num\">第十一条</span> 担保</p>\r\n<p class=\"p2\">11．1乙方应当为本次借款行为提供抵押、质押等担保手续。</p>\r\n<p class=\"p2\">11．2乙方可以指定第三人为本次借款提供担保，但该第三人及其提供的担保财产应当经过甲方、丙方的认可。</p>\r\n<p class=\"p2\">11．3乙方或乙方指定的担保人应当就本次借款另行签订《抵押合同》，《抵押合同》的编号为<span class=\"diyaren\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>。</p>\r\n<p class=\"p2\">11．4甲方、丙方共同指定<span class=\"diyaren\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>为代理人，<span class=\"diyaren\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>可以以自己的名义与乙方或乙方指定的担保人签订担保合同，并办理相关的抵押、质押登记，所产生的权利和义务均同甲、丙方承担。</p>\r\n<p class=\"p2\"><span class=\"num\">第十二条</span> 违约责任</p>\r\n<p class=\"p2\">12．1本合同生效后，任何一方不履行、不完全履行或延迟履行合同义务的，应当按照约定承担违约责任，并赔偿由此给对方造成的损失（包括实际损失和预期收益）。</p>\r\n<p class=\"p2 num\">12．2乙方有下列情形之一的，即构成违约：</p>\r\n<p class=\"p2 num\">（1）未按期偿还任何一期借款本金或利息的；</p>\r\n<p class=\"p2 num\">（2）未按约定用途使用借款的；</p>\r\n<p class=\"p2 num\">（3）违反声明与承诺条款的；</p>\r\n<p class=\"p2 num\">（4）未按约定履行通知义务或者未经甲方书面同意而实施相关行为的；</p>\r\n<p class=\"p2 num\">（5）发生其他违反本合同的行为的。</p>\r\n<p class=\"p2 num\">12．3如乙方未按本合同约定的期限偿还全部借款本息，或乙方接到甲方根据本合同第八条之规定宣布合同提前到期的通知之日起三个工作日内未能偿还全部借款本息则构成逾期，需向甲方支付利息、违约金（利息以本合同的约定按实际占用天数计算，违约金按实际借款金额的10%计算，逾期60日的，按逾期金额日千分之二计算至实际偿还）。</p>\r\n<p class=\"p2\">12．4如乙方未按时、足额还款或出现其他风险导致甲方向人民法院起诉申请执行，乙方应承担甲方因处理此类情况而支出的全部费用（包括但不限于律师费、财产保全费、诉讼费、执行费、拍卖费、差旅费、公证费、欠付的各类费用）。</p>\r\n<p class=\"p2\">12．5如乙方未按时足额履行还款义务，乙方同意并授权甲方可以将乙方的违约情况向媒体发布公告等形式进行催收，费用由乙方承担，乙方不持异议。</p>\r\n<p class=\"p2\"><span class=\"num\">第十三条</span> 其他约定条款</p>\r\n<p class=\"p2\">13．1借款人为两人或两人以上的，各借款人对本合同项下债务承担连带责任。任一借款人有权代表全体借款人处理与本合同有关的全部事务，包括但不限于收取和归还借款，签署借据、补充合同等。</p>\r\n<p class=\"p2\">13．2乙方同意甲方、丙方向信用信息管理机构查询其信用状况，并同意甲方、丙方根据本合同的履行情况向信用信息管理机构或有关方提供乙方的信用信息。</p>\r\n<p class=\"p2\">13．3甲方有权将本合同项下债权转让给任何第三方而无需征得乙方同意。乙方同意甲方为债权转让之目的使用乙方的信息披露给有关方，包括但不限于真实姓名、联系方式、信用状况等必要的个人信息和交易信息。乙方同意授权委托甲方、丙方在债权转让后代为管理后续还款事项，包括但不限于（1）在收到乙方支付的应偿还借款本息后，代乙方向债权受让人偿还；（2）收取与乙方相关的法律文件，如债权转让通知书等。</p>\r\n<p class=\"p2\"><span class=\"num\">第十四条</span> 因本合同发生的纠纷，由<span class=\"num\">丙方住所地</span>的人民法院管辖。</p>\r\n<p class=\"p2\"><span class=\"num\">第十五条</span> 本合同自各方签署之日成立，自甲方向乙方提供借款之日起生效。本合同一式<span class=\"red\" style=\"border-bottom:1px solid #e53d3a; text-align:center;\">叁</span><span class=\"red\">份</span>，甲、乙、丙三方各执一份。</p>\r\n<div class=\"user\" style=\"margin-top:0.3rem;\">\r\n   <div class=\"left\" style=\"text-indent:0;\">甲方:</div>\r\n   <div class=\"right\">乙方:</div>\r\n</div>\r\n<p class=\"p2\" style=\"text-indent:0;\">丙方：<span class=\"num\">上海小虾网络科技有限公司</span></p>\r\n<div class=\"date\">年&nbsp;&nbsp;月&nbsp;&nbsp;日</div> \r\n</section>\r\n</body>\r\n</html>\r\n', '1458875048', '1', '1');
INSERT INTO `tb_agreement_0` VALUES ('recharges', '1', '0', 'root', '', '<!doctype html>\r\n<html lang=\"en\">\r\n<head>\r\n<meta charset=\"utf-8\">\r\n<title>充值协议</title>\r\n<meta name=\"viewport\" id=\"viewport\" content=\"width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no\">\r\n<link type=\"text/css\" rel=\"stylesheet\" href=\"/css/base.css\"/>\r\n <link type=\"text/css\" rel=\"stylesheet\" href=\"/css/agreement.css\"/>\r\n<script type=\"text/javascript\" src=\"/js/jquery-1.11.3.min.js\"></script>\r\n<script type=\"text/javascript\" src=\"/js/App.js\"></script>\r\n</head>\r\n\r\n<body>\r\n<section>\r\n   <h3>平台充值协议</h3>\r\n   <p class=“p0”>www.xiaoxialicai.com（以下简称“网站”）是<span>上海小虾科技有限公司</span>\r\n运用大数据挖掘，建立独特实用的信用模型和专业化严谨的风控管理体系，并拥有完善的财富管理机制、完善的金融风险定价体系和丰富的理财团队，且与多家金融机构和银行合作，以确保为中小企业的线上交易和行为数据提供最安全、最优质的借贷服务。上海小虾科技有限公司的信贷互联网专业服务平台，秉承诚信、透明、公平、高效的原则，依托互联网企业的网络融资服务平台和电子商务平台，以客户利益为中心，为客户提供全方位、个性化、多样化的理财产品和借款方式，致力于打造中国最具有专业性的创新型互联网金融综合借贷服务平台。小虾理财根据本协议约定为小虾理财会员提供服务，小虾理财已将本协议中免除、限制小虾理财责任或限制会员权利的条款已提醒您特别注意，请保证您在仔细阅读、并完全理解本协议的情况下，选择接受或不接受本协议。因小虾理财的服务仅向小虾理财会员提供，若您一经注册成为小虾理财会员或使用小虾理财提供的服务，则表示您完全接受以下条款的约束，并视为对本协议全部条款已充分理解、完全接受，否则您无权使用小虾理财提供的服务。若您不接受以下条款，请您停止注册和停止使用小虾理财的服务。</p>\r\n\r\n<p class=\"p2\">授权人姓名：{$userName}</p>\r\n\r\n<p class=\"p2\">证件类型：身份证</p>\r\n\r\n<p class=\"p2\">证件号码：{$userIdCard}</p>\r\n\r\n<p class=\"p2\">小虾理财用户ID：{$userId}</p>\r\n\r\n<p class=\"p2\">日期：{$ymd}</p>\r\n\r\n<p class=\"p2\">被授权人：上海小虾科技有限公司（“小虾理财”）就授权人向其小虾理财用户名项下账户（“授权人小虾理财账户”）充值的相关事宜向小虾理财授权如下：</p>\r\n\r\n<p class=\"p2\">一、授权人授权小虾理财根据授权人的充值指令从本授权书第二条所述的授权人的银行账户通过银行或小虾理财指定的第三方支付机构（以下统称为“第三方机构”）主动扣收本授权书第二条所述的等值于充值金额的款项，用于向授权人小虾理财账户充值（“充值服务”）。</p>\r\n\r\n<p class=\"p2\">二、授权人的银行账户及充值金额如下：</p>\r\n\r\n<p class=\"p2\">户名：{$userName}</p>\r\n\r\n<p class=\"p2\">账号：{$bankCard}</p>\r\n\r\n<p class=\"p2\">开户银行：{$bankId}</p>\r\n\r\n<p class=\"p2\">充值金额：{$amount} 人民币元（含第三方机构需收的手续费（如有））</p>\r\n\r\n<p class=\"p2\">三、授权人知晓并确认，本授权书系使用授权人小虾理财用户名、以网络在线点击确认的方式向小虾理财发出。 本授权书自授权人在小虾理财网站点击确认时生效，由小虾理财通过第三方机构从授权人的银行账户中代扣相当于充值金额的款项。 授权人已经通过本授权书确认代扣款项的银行账户信息，在代扣的过程中，小虾理财根据本授权书提供的银行账户信息进行相关操作，无需再向授权人确认银行账户信息和密码。 本授权书一经生效即不可撤销。授权人确认并承诺，小虾理财根据本授权书所采取的全部行动和措施的法律后果均由授权人承担。 </p>\r\n \r\n<p class=\"p2\">四、授权人知晓并同意,受授权人银行账户状态、银行、第三方支付机构及网络等原因所限, 本授权书项下充值可能会通过多次代扣交易方可完成,小虾理财不对充值服务的资金到账时间做任何承诺。 小虾理财或第三方机构仅根据本授权书所述的授权人的授权进行相关操作, 小虾理财或第三方机构无义务对其根据本授权书所采取的全部行动和措施的时效性和结果承担任何责任。 </p>\r\n\r\n<p class=\"p2\">特此授权。 </p>\r\n\r\n<p class=\"p2\">版权所有  上海小虾科技有限公司未经许可不得复制、转载或摘编，违者必究! </p>\r\n<p class=\"p2\">Copyright Shanghai Lujiazui International Financial Asset Exchange Co.,LTD. ALL Rights Reserved</p> \r\n\r\n</section>\r\n</body>\r\n</html>\r\n', '1459237996', '3', '1');
INSERT INTO `tb_agreement_0` VALUES ('register', '1', '0', 'root', '', '<!doctype html>\r\n<html>\r\n<head>\r\n<meta charset=\"utf-8\">\r\n<title>协议</title>\r\n <meta name=\"viewport\" id=\"viewport\" content=\"width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no\">\r\n <!--<link type=\"text/css\" rel=\"stylesheet\" href=\"css/base.css\"/>\r\n <link type=\"text/css\" rel=\"stylesheet\" href=\"css/xiaoxiaagreement.css\"/>-->\r\n <style type=\"text/css\">\r\n     html {\r\n	-webkit-text-size-adjust: 100%;\r\n	-ms-text-size-adjust: 100%;\r\n}\r\n\r\n/* 去除iPhone中默认的input样式 */\r\ninput[type=\"submit\"], input[type=\"reset\"], input[type=\"button\"], input {\r\n	-webkit-appearance: none;\r\n	resize: none;\r\n}\r\n\r\n/* 取消链接高亮  */\r\nbody, div, ul, li, ol, h1, h2, h3, h4, h5, h6, input, textarea, select, p, dl, dt, dd, a, img, button, form, table, th, tr, td, tbody, article, aside, details, figcaption, figure, footer, header, hgroup, menu, nav, section {\r\n	-webkit-tap-highlight-color: rgba(0, 0, 0, 0);\r\n}\r\n\r\n/* 设置HTML5元素为块 */\r\narticle, aside, details, figcaption, figure, footer, header, hgroup, menu, nav, section {\r\n	display: block;\r\n}\r\n\r\n/* 图片自适应 */\r\nimg {\r\n	width: 100%;\r\n	height: auto;\r\n	width: auto\\9; /* ie8 */\r\n	-ms-interpolation-mode: bicubic;/*为了照顾ie图片缩放失真*/\r\n}\r\n\r\n/* 初始化 */\r\nbody, div, ul, li, ol, h1, h2, h3, h4, h5, h6, input, textarea, select, p, dl, dt, dd, a, img, button, form, table, th, tr, td, tbody, article, aside, details, figcaption, figure, footer, header, hgroup, menu, nav, section {\r\n	margin: 0;\r\n	padding: 0;\r\n	border: none;\r\n}\r\nbody {\r\n	font-family: \'黑体\';\r\n	color: #3B3B3B;\r\n	background-color: #fff;\r\n}\r\nem, i {\r\n	font-style: normal;\r\n}\r\nstrong {\r\n	font-weight: normal;\r\n}\r\n.clearfix:after {\r\n	content: \"\";\r\n	display: block;\r\n	visibility: hidden;\r\n	height: 0;\r\n	clear: both;\r\n}\r\n.clearfix {\r\n	zoom: 1;\r\n}\r\na {\r\n	text-decoration: none;\r\n	color: #3b3b3b;\r\n	font-family: \'黑体\';\r\n}\r\na:hover {\r\n	\r\n	text-decoration: none;\r\n}\r\nul, ol {\r\n	list-style: none;\r\n}\r\nh1, h2, h3, h4, h5, h6 {\r\n	font-size: 100%;\r\n	font-family: \'黑体\';\r\n}\r\nimg {\r\n	border: none;\r\n}\r\n\r\n/*分割线*/\r\nhtml{\r\n	font-size: 40px;\r\n	margin:0 auto;\r\n	color: #3B3B3B;\r\n	min-width: 320px;\r\n	max-width: 640px; 	 	 	\r\n}\r\nbody{\r\n	margin:0 12px;\r\n}\r\n\r\nsection{ width:96%; margin:0 auto; overflow:hidden;}\r\nsection h3{ font-size:0.4rem; margin:0.5rem 0; text-align:center;}\r\nh4{ font-size:0.35rem; margin:0 auto; margin-bottom:0.5rem; font-weight:normal;}\r\n.p1{ font-size:0.4rem; font-weight:bold; margin-bottom:0.3rem;}\r\n.p2{ font-size:0.35rem; text-indent:1.5em; margin-bottom:0.125rem;}\r\n.p3{ font-size:0.35rem; text-indent:1.5em; margin-bottom:0.125rem;}\r\n.p4{ font-size:0.25rem; text-align:right; margin-top:0.2rem;}\r\n.p4 span{ border-bottom:1px solid #000;}\r\n.p4 span i{ font-style:normal; color:#e53d3a;}\r\n.diyaren{ width:3rem; border-bottom:1px solid #000;}\r\n.identy{ width:6rem;border-bottom:1px solid #000;}\r\n.num{ font-weight:bold;}\r\n.red{ color:#e53d3a; }\r\n.red .diyaren{ width:3rem; border-bottom:1px solid #e53d3a;}\r\n.user{ font-size:0.35rem; margin-bottom:0.3rem; overflow:hidden;}\r\n.user .left{ float:left; margin-right:3.5rem; text-indent:1.5em;}\r\n.user .right{ float:left;}\r\n.date{ font-size:0.35rem; text-align:right; margin-right:1rem;}\r\n\r\n \r\n </style>\r\n <!--<script type=\"text/javascript\" src=\"http://code.jquery.com/jquery-1.11.3.min.js\"></script>-->\r\n <script type=\"text/javascript\">\r\n window.onload=function(){\r\n        function getFont(){\r\n                var html1=document.documentElement;     \r\n                var screen=html1.clientWidth;\r\n                if(screen <= 320){\r\n                     html1.fontSize = \'40px\';   \r\n                }else if(screen >= 640){\r\n                      html1.fontSize = \'80px\';  \r\n                }else{\r\n                      html1.style.fontSize=0.125*screen+\'px\';  \r\n                }\r\n                \r\n                }\r\n        getFont();\r\n        window.onresize=function(){\r\n                getFont();\r\n        }\r\n}\r\n \r\n </script>\r\n</head>\r\n\r\n<body>\r\n<section>\r\n    <h3>小虾理财平台注册及服务协议</h3>\r\n    <h4>重要须知：</h4>\r\n    <h4>本协议是您（用户）与上海小虾网络科技有限公司之间就小虾理财平台网站注册及服务相关事宜所订立的契约，适用于您在小虾理财平台注册及使用小虾理财平台服务的全部行为。</h4>\r\n    <h4>在注册成为小虾理财平台用户前，请您务必认真、仔细阅读并充分理解本协议全部内容，特别是其中所涉及的对小虾理财平台的责任免除及对您的权利限制的条款。您在小虾理财平台提供的网络页面上点击以合理的理解表明您希望与本公司签订本协议的按钮（例如，按钮上书写“同意协议并注册”或类似文字，且页面上同时列明了本协议的内容或者可以有效展示本协议内容的链接），即表示您在充分阅读并理解本协议的内容基础上确认接受并签署本协议，同时表示您与小虾理财平台已达成协议并同意接受本协议的全部约定内容，包括与本协议有关的各项规则及本网站所包含的已经发布的或者将来发布的各类声明、规则、说明等（若服务协议中没有约定的以本网站发布的各类声明、规则、说明等中对应内容为准）。如您不同意或者不接受本协议的全部或者部分内容，请您不要注册成为小虾理财平台用户及使用小虾理财平台服务。</h4>\r\n    <p class=\"p1\">一． 小虾理财平台的账户及服务</p>\r\n    <p class=\"p2\">小虾理财平台是指通过www.xiaoxialicai.com注册后由小虾理财平台相关公司为用户提供的各类金融产品的交易平台，包括但不限于P2P网络借贷、基金、股票等（具体以小虾理财平台及相关交易网站实时展示为准）。\r\n    </p>\r\n    <p class=\"p2\">小虾理财平台采用实名制注册，用户应如实填写和提交账号注册和认证资料，并对资料的真实性、合法性、准确性和有效性承担责任。</p>\r\n    <p class=\"p2\">用户注册成功后，小虾理财平台将给予每个用户一个用户账号及相应的密码，该用户账号和密码由用户负责保管；用户应当对以其用户账号进行的所有活动和事件负法律责任。</p>\r\n    <p class=\"p2\">用户可使用小虾理财账户登录小虾理财平台及相关交易网站而无需另行注册，如需发起交易可在各相关网站开设与小虾理财平台账户相关联的交易账户，交易规则以各相关网站公示或提示为准，用户的交易行为应遵守上述交易规则。除非另有其它明示规定，小虾理财平台所展示的新产品、新功能、新服务，均受到本协议之规范。</p>\r\n    <p class=\"p2\">小虾理财平台仅作为网络环境下用户或者关联网站发布信息的场所，鉴于网络服务的特殊性，用户同意小虾理财平台有权不经事先通知，随时变更、中断或终止部分或全部的网络服务。</p>\r\n    <p class=\"p2\">小虾理财平台需要定期或不定期地进行检修或者维护，如因此类情况而造成网络服务在合理时间内的中断，小虾理财平台无需为此承担任何责任。小虾理财平台保留不经事先通知为维修保养、升级或其它目的暂停本服务任何部分的权利。</p>\r\n    <p class=\"p2\">小虾理财平台有权于任何时间暂时或永久修改或终止服务（或其任何部分），而无论其通知与否，小虾理财平台对用户和任何第三人均无需承担任何责任。</p>\r\n    <p class=\"p2\">用户确认知悉，小虾理财平台的账户仅供小虾理财平台及相关交易网站提供居间服务及交易环境，基于交易行为发生的相关的资金管理及划付服务均由具有法定资质的第三方机构提供，小虾理财平台对用户以完成交易为目的的资金管理及划付的指令及最终的结果、时效性不承担任何责任。针对冒用他人账户的行为，小虾理财平台保留对实际使用人追究责任的权利。</p>\r\n    <p class=\"p2\">小虾理财账户的所有权归小虾理财平台所有，用户完成注册申请手续后，获得小虾理财账户的使用权。用户同意小虾理财平台基于自行之考虑，有权对符合以下条件的小虾理财账户做回收处理，回收处理情形下小虾理财平台的服务相应终止，且小虾理财平台有权立即关闭或删除用户小虾理财账户及其中所有相关信息及文件：\r\n   <p class=\"p3\">(1)未绑定通过实名认证的各平台账户；</p>\r\n   <p class=\"p3\">(2)连续12个月未用于登录；</p>\r\n   <p class=\"p3\">(3)不存在未到期的有效业务超过12个月 ；</p>\r\n   <p class=\"p3\">(4)小虾理财平台有合理理由认为用户的行为已经违反本协议的文字及精神及公平正义、公序良俗等社会公益价值。</p>\r\n   <p class=\"p1\">二． 小虾理财平台服务内容</p>\r\n   <p class=\"p2\">小虾理财平台只接受持有中华人民共和国有效身份证的18周岁以上的具有完全民事行为能力的自然人或者依据《中华人民共和国公司法》依法设立并存续的，在中国大陆地区合法开展经营活动的法人或组织成为网站用户，如用户不符合资格，请勿继续操作注册。小虾理财平台保留因用户注册信息不实而随时中止或终止用户资格的权利，包括但不限于对用户小虾理财账号进行回收、封号等操作，由此所产生的损失由用户自行承担，小虾理财平台将不承担任何责任。</p>\r\n   <p class=\"p2\">用户在注册小虾理财平台及使用相关网站服务时应当根据小虾理财平台的要求提供自己的个人资料，并在使用过程中依据小虾理财平台和相关网站的要求更新上述个人资料并保证此等资料完整、有效。如因注册信息不真实或更新不及时而引起的问题，小虾理财平台及相关网站不负任何责任。如发现用户以虚假信息注册而骗取小虾理财账号使用权，或注册信息存在违法和不良信息的，小虾理财平台有权单方采取限期改正、暂停使用、注销登记、收回等措施，且上述措施的采用不以通知为必要条件。</p>\r\n   <p class=\"p2\">用户成功注册为小虾理财平台用户后，应当妥善保管自己的用户名和密码，不得将账号、密码进行转让、出售、出借、出租、赠与或授权给第三方使用，若用户授权他人使用其账户应对被授权人在该账户下发生的所有行为负全部责任。因密码被遗忘、被第三方破解，使用的计算机被入侵等原因造成的交易风险均亦由用户自行承担。用户在此确认以其用户名和密码登录小虾理财平台及相关交易网站情形下，经由用户的小虾理财账户发出的一切指令均视为用户本人的行为和真实意思表示，该等指令不可撤销，由此产生的一切责任和后果由用户本人承担。</p>\r\n   <p class=\"p2\">用户不得利用小虾理财平台从事任何违法违规活动，用户在此承诺合法使用小虾理财平台提供的服务，遵守中国现行法律、法规、规章、规范性文件的规定以及小虾理财平台及相关交易网站的规则、协议等规范。若用户违反上述规定，所产生的一切法律责任和后果由用户自行承担。用户承担法律责任的形式包括但不限于：对受到侵害者进行赔偿，以及如小虾理财平台首先承担了因用户行为导致的行政处罚或侵权损害赔偿责任后，用户应向小虾理财平台进行赔偿。如因此给小虾理财平台造成损失的，由用户赔偿小虾理财平台的损失。小虾理财平台保留将用户违法违规行为及有关信息资料进行公示、计入用户信用档案、按照法律法规的规定提供给有关政府部门或按照有关协议约定提供给第三方的权利。</p>\r\n   <p class=\"p2\">如用户在小虾理财平台的某些行为或言论不合法、违反有关协议约定、侵犯小虾理财平台及相关交易网站的利益等，小虾理财平台有权基于独立判断直接删除用户在小虾理财平台上作出的上述行为或言论，有权中止、终止、限制用户使用小虾理财平台服务，而无需通知用户，亦无需承担任何责任。</p>\r\n  </p>\r\n  <p class=\"p1\">三． 不保证条款</p>\r\n  <p class=\"p2\">小虾理财平台提供的信息和服务中不含有任何明示、暗示的，对任何用户、任何交易的真实性、准确性、可靠性、有效性、完整性等的任何保证和承诺，用户需根据自身风险承受能力，衡量小虾理财平台披露的内容的真实性、可靠性、有效性、完整性，用户因其选择使用小虾理财平台提供的服务、参与的交易等而产生的直接或间接损失均由用户自己承担，包括但不限于资金损失、利润损失、营业中断等。</p>\r\n  <p class=\"p2\">基于互联网的特殊性，无法保证小虾理财平台的服务不会中断，对于包括但不限于设备、系统存在缺陷，计算机发生故障、遭到病毒、黑客攻击或者发生地震、海啸等不可抗力而造成服务中断或因此给用户造成的损失，由用户自己承担。</p>\r\n  <p class=\"p1\">四． 隐私保护</p>\r\n  <p class=\"p2\">小虾理财平台有义务就用户提供的及自行合法收集的用户信息采用合理的、必要的手段及措施进行保护，以更充分的了解用户需求、协助用户完成交易。</p>\r\n  <p class=\"p2\">小虾理财平台有权利就用户提供的及自行合法收集的用户信息进行合法的使用（包括以不伤害用户隐私权为前提的商业上的使用），且在如下情况下，小虾理财平台可能会披露您的信息：</p>\r\n  <p class=\"p3\">(1)事先获得用户的授权；</p>\r\n  <p class=\"p3\">(2)用户使用共享功能；</p>\r\n  <p class=\"p3\">(3)根据法律、法规、法律程序的要求或政府主管部门的强制性要求；</p>\r\n  <p class=\"p3\">(4)以学术研究或公共利益为目的；</p>\r\n  <p class=\"p3\">(5)为维护小虾理财平台的合法权益，例如查找、预防、处理欺诈或安全方面的问题；</p>\r\n  <p class=\"p3\">(6)为维护小虾理财平台其他用户的合法权益，即在用户违反小虾理财平台及相关交易网站的规则及交易协议约定情形下，小虾理财平台有权将用户的信息进行黑名单披露并共享给与小虾理财平台及相关交易网站有合作关系的第三方；</p>\r\n  <p class=\"p3\">(7)符合相关服务条款或使用协议的规定。</p>\r\n  <p class=\"p2\">小虾理财平台采用行业标准惯例以保护用户的个人信息和资料，鉴于技术限制，不能确保用户的全部私人通讯及其他个人资料不会通过不明的途径泄露出去。</p>\r\n  <p class=\"p2\">针对本条款约定情形下的信息使用及披露有可能给用户造成的损失，小虾理财平台及相关交易网站不承担任何责任。</p>\r\n  <p class=\"p1\">五． 使用规则</p>\r\n  <p class=\"p2\">用户在使用小虾理财平台及相关交易网站服务时，必须遵守中华人民共和国相关法律法规的规定，用户应同意将不会利用本服务进行任何违法或不正当的活动，包括但不限于下列行为:</p>\r\n  <p class=\"p2\">(1)上载、展示、张贴、传播或以其它方式传送含有下列内容之一的信息：</p>\r\n  <p class=\"p3\">1)反对宪法所确定的基本原则的；</p>\r\n  <p class=\"p3\">2)危害国家安全，泄露国家秘密，颠覆国家政权，破坏国家统一的；</p>\r\n  <p class=\"p3\">3)损害国家荣誉和利益的；</p>\r\n  <p class=\"p3\">4)煽动民族仇恨、民族歧视、破坏民族团结的；</p>\r\n  <p class=\"p3\">5)破坏国家宗教政策，宣扬邪教和封建迷信的；</p>\r\n  <p class=\"p3\">6)散布谣言，扰乱社会秩序，破坏社会稳定的；</p>\r\n  <p class=\"p3\">7)散布淫秽、色情、赌博、暴力、凶杀、恐怖或者教唆犯罪的；</p>\r\n  <p class=\"p3\">8)侮辱或者诽谤他人，侵害他人合法权利的；</p>\r\n  <p class=\"p3\">9)含有虚假、有害、胁迫、侵害他人隐私、骚扰、侵害、中伤、粗俗、猥亵、或其它道德上令人反感的内容；</p>\r\n  <p class=\"p3\">10)含有中国法律、法规、规章、条例以及任何具有法律效力之规范所限制或禁止的其它内容的；</p>\r\n  <p class=\"p2\">(2)不得为任何非法目的而使用小虾理财平台服务系统。</p>\r\n  <p class=\"p2\">用户违反本协议或相关的服务条款的规定，导致或产生的任何第三方向小虾理财平台及其相关交易网站主张的任何索赔、要求或损失（包括合理的律师费），将由用户向小虾理财平台与相关交易网站进行赔偿，以使之免受损害。对此，小虾理财平台有权视用户行为的性质，采取包括但不限于删除用户发布信息内容、暂停使用许可、终止服务、限制使用、回收小虾理财账号、追究法律责任等措施。对恶意注册小虾理财账号或利用小虾理财账号进行违法活动，骚扰、欺骗其他用户，以及其他违反法律规定及违反本协议的行为，小虾理财平台有权回收其小虾理财账号。同时，小虾理财平台公司会视司法部门的要求，协助调查。</p>\r\n  <p class=\"p2\">用户不得对本协议及本服务的任何部分或基于本协议或本服务之使用或获得的信息，进行出售、转售或用于任何其它商业目的。</p>\r\n  <p class=\"p1\">六． 知识产权</p>\r\n  <p class=\"p2\">小虾理财平台内容受中国知识产权法律法规及各国际版权公约的保护。用户承诺不以任何形式复制、模仿、传播、出版、公布、展示小虾理财平台内容，包括但不限于电子的、机械的、复印的、录音录像的方式和形式等。</p>\r\n  <p class=\"p2\">用户未经授权不得将小虾理财平台包含的资料等任何内容发布到任何其他网站或者服务器。任何未经授权对小虾理财平台内容的使用均属于违法行为。\r\n用户保证，其发布在小虾理财平台上的任何信息和内容都没有侵犯任何第三方的知识产权，也不存在可能导致侵犯第三方知识产权的情形。若任何第三方因用户发布的信息和内容提出权属异议或引起任何纠纷，用户自行承担责任，与小虾理财平台无关。\r\n</p>\r\n<p class=\"p2\">用户同意，其发布上传到小虾理财平台上可公开获取区域的任何内容，用户同意小虾理财平台有权将内容用于其他合法用途，包括但不限于部分或者全部地复制、修改、改编、翻译、组装、分拆、推广、分发、广播、表演、演绎、出版。</p>\r\n<p class=\"p1\">七． 法律适用和争议解决</p>\r\n<p class=\"p2\">本协议的签订、效力、履行、终止、解释和争端解决适用中国法律法规。如双方就本协议内容或其执行发生任何争议，双方应尽量友好协商解决；协商不成时，任何一方均可向小虾理财平台所在地的人民法院提起诉讼。</p>\r\n<p class=\"p1\">八． 其他</p>\r\n<p class=\"p2\">本协议自用户签署并成功注册为小虾理财平台用户之日起生效，除非小虾理财平台终止本协议或者用户丧失小虾理财平台用户资格，否则本协议始终有效。出于运行和交易安全的需要，小虾理财平台可以暂时停止提供或者限制本服务部分功能，或提供新的功能，在任何功能减少、增加或者变化时，只要用户仍然使用小虾理财平台服务，表示用户仍然同意本协议或者变更后的协议。\r\n本协议终止并不免除用户根据本协议或其他有关协议、规则已经发生的行为或达成的交易项下所应承担的义务和责任。\r\n</p>\r\n<p class=\"p2\">小虾理财平台未行使或执行本服务协议任何权利或规定，不构成对前述权利之放弃。</p>\r\n<p class=\"p2\">如本协议中的任何条款无论因何种原因完全或部分无效或不具有执行力，本协议的其余条款仍应有效并且有约束力。</p>\r\n<p class=\"p2\">小虾理财平台对本协议享有最终的解释权。</p>\r\n</section>\r\n</body>\r\n</html>\r\n', '1458872652', '2', '1');
INSERT INTO `tb_agreement_0` VALUES ('zhaiquan', '1', '0', 'root', '', '<!doctype html>\r\n<html>\r\n<head>\r\n<meta charset=\"utf-8\">\r\n<title>债权转让服务协议</title>\r\n<meta name=\"viewport\" id=\"viewport\" content=\"width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no\">\r\n<style type=\"text/css\">\r\n     html {\r\n	-webkit-text-size-adjust: 100%;\r\n	-ms-text-size-adjust: 100%;\r\n}\r\n\r\n/* 去除iPhone中默认的input样式 */\r\ninput[type=\"submit\"], input[type=\"reset\"], input[type=\"button\"], input {\r\n	-webkit-appearance: none;\r\n	resize: none;\r\n}\r\n\r\n/* 取消链接高亮  */\r\nbody, div, ul, li, ol, h1, h2, h3, h4, h5, h6, input, textarea, select, p, dl, dt, dd, a, img, button, form, table, th, tr, td, tbody, article, aside, details, figcaption, figure, footer, header, hgroup, menu, nav, section {\r\n	-webkit-tap-highlight-color: rgba(0, 0, 0, 0);\r\n}\r\n\r\n/* 设置HTML5元素为块 */\r\narticle, aside, details, figcaption, figure, footer, header, hgroup, menu, nav, section {\r\n	display: block;\r\n}\r\n\r\n/* 图片自适应 */\r\nimg {\r\n	width: 100%;\r\n	height: auto;\r\n	width: auto\\9; /* ie8 */\r\n	-ms-interpolation-mode: bicubic;/*为了照顾ie图片缩放失真*/\r\n}\r\n\r\n/* 初始化 */\r\nbody, div, ul, li, ol, h1, h2, h3, h4, h5, h6, input, textarea, select, p, dl, dt, dd, a, img, button, form, table, th, tr, td, tbody, article, aside, details, figcaption, figure, footer, header, hgroup, menu, nav, section {\r\n	margin: 0;\r\n	padding: 0;\r\n	border: none;\r\n}\r\nbody {\r\n	font-family: \'黑体\';\r\n	color: #3B3B3B;\r\n	background-color: #fff;\r\n}\r\nem, i {\r\n	font-style: normal;\r\n}\r\nstrong {\r\n	font-weight: normal;\r\n}\r\n.clearfix:after {\r\n	content: \"\";\r\n	display: block;\r\n	visibility: hidden;\r\n	height: 0;\r\n	clear: both;\r\n}\r\n.clearfix {\r\n	zoom: 1;\r\n}\r\na {\r\n	text-decoration: none;\r\n	color: #3b3b3b;\r\n	font-family: \'黑体\';\r\n}\r\na:hover {\r\n	\r\n	text-decoration: none;\r\n}\r\nul, ol {\r\n	list-style: none;\r\n}\r\nh1, h2, h3, h4, h5, h6 {\r\n	font-size: 100%;\r\n	font-family: \'黑体\';\r\n}\r\nimg {\r\n	border: none;\r\n}\r\n\r\n/*分割线*/\r\nhtml{\r\n	font-size: 40px;\r\n	margin:0 auto;\r\n	color: #3B3B3B;\r\n	min-width: 320px;\r\n	max-width: 640px; 	 	 	\r\n}\r\nbody{\r\n	margin:0 12px;\r\n}\r\n\r\nsection{ width:96%; margin:0 auto; overflow:hidden;}\r\nsection h3{ font-size:0.4rem; margin:0.5rem 0; text-align:center;}\r\nh4{ font-size:0.35rem; margin:0 auto; margin-bottom:0.5rem; font-weight:normal;}\r\n.p1{ font-size:0.4rem; font-weight:bold; margin-bottom:0.3rem;}\r\n.p2{ font-size:0.35rem; text-indent:1.5em; margin-bottom:0.125rem;}\r\n.p3{ font-size:0.35rem; text-indent:1.5em; margin-bottom:0.125rem;}\r\n.p4{ font-size:0.25rem; text-align:right; margin-top:0.2rem;}\r\n.p4 span{ border-bottom:1px solid #000;}\r\n.p4 span i{ font-style:normal; color:#e53d3a;}\r\n.diyaren{ width:3rem; border-bottom:1px solid #000;}\r\n.identy{ width:6rem;border-bottom:1px solid #000;}\r\n.num{ font-weight:bold;}\r\n.red{ color:#e53d3a; }\r\n.red .diyaren{ width:3rem; border-bottom:1px solid #e53d3a;}\r\n.user{ font-size:0.35rem; margin-bottom:0.3rem; overflow:hidden;}\r\n.user .left{ float:left; margin-right:3.5rem; text-indent:1.5em;}\r\n.user .right{ float:left;}\r\n.date{ font-size:0.35rem; text-align:right; margin-right:1rem;}\r\n\r\n \r\n </style>\r\n <!--<script type=\"text/javascript\" src=\"http://code.jquery.com/jquery-1.11.3.min.js\"></script>-->\r\n <script type=\"text/javascript\">\r\n window.onload=function(){\r\n        function getFont(){\r\n                var html1=document.documentElement;     \r\n                var screen=html1.clientWidth;\r\n                if(screen <= 320){\r\n                     html1.fontSize = \'40px\';   \r\n                }else if(screen >= 640){\r\n                      html1.fontSize = \'80px\';  \r\n                }else{\r\n                      html1.style.fontSize=0.125*screen+\'px\';  \r\n                }\r\n                \r\n                }\r\n        getFont();\r\n        window.onresize=function(){\r\n                getFont();\r\n        }\r\n}\r\n \r\n </script>\r\n</head>\r\n\r\n<body>\r\n<section>\r\n<h3>债权转让服务协议</h3>\r\n<p class=\"p2\" style=\"text-indent:0;\">甲方（转让方）：<span class=\"diyaren\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></p>\r\n<p class=\"p2\" style=\"text-indent:0;\">证件号码：<span class=\"diyaren\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></p>\r\n<p class=\"p2\" style=\"text-indent:0; margin-bottom:0.3rem;\">平台ID：<span class=\"diyaren\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></p>\r\n<p class=\"p2\" style=\"text-indent:0;\">乙方（服务商）：上海小虾网络科技有限公司</p>\r\n<p class=\"p2\" style=\"text-indent:0; margin-bottom:0.3rem;\">住所：上海市浦东新区福山路388号宏嘉大厦8楼</p>\r\n<p class=\"p2\" style=\"text-indent:0;\">丙方（受让方）：<span class=\"diyaren\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></p>\r\n<p class=\"p2\" style=\"text-indent:0;margin-bottom:0.3rem;\">平台ID：<span class=\"diyaren\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></p>\r\n<p class=\"p2\">根据《中华人民共和国合同法》及相关法律法规的规定，三方遵循平等、自愿、互利和诚实信用原则，就财富通产品达成如下合作条款：</p>\r\n<p class=\"p2 num\">第一条  基本约定</p>\r\n<p class=\"p2\"><span class=\"num\">1.1 受让方：</span>指本协议的丙方，其愿意受让乙方推荐和提供的标的债权。</p>\r\n<p class=\"p2\"><span class=\"num\">1.2 标的债权：</span>指由甲方转让，丙方同意受让的应收债权。标的债权以人民币计价。</p>\r\n<p class=\"p2\" style=\"text-indent:3em;\">标的债权须同时满足以下条件：</p>\r\n<p class=\"p2\" style=\"text-indent:3em;\">(1)债权清晰、无瑕疵；</p>\r\n<p class=\"p2\" style=\"text-indent:3em;\">(2)欠款方有足额还款能力，资信良好，从未发生恶意欠款；</p>\r\n<p class=\"p2\" style=\"text-indent:3em;\">(3)欠款方／第三人己提供足额的房地产抵押、上市公司股权质押或其他可靠的担保措施；</p>\r\n<p class=\"p2\" style=\"text-indent:3em;\">(4)借款赍金用途符合法律规定，不得用于法律、法规所禁止的领域。</p>\r\n<p class=\"p2\"><span class=\"num\">1.3 转让方：</span>是指甲方即原债权人。转让方应先以合法资金向欠款人提供借款，从而形成真实、合法、有效的应收账款债权，即用于转让的标的债权。</p>\r\n<p class=\"p2\"><span class=\"num\">1.4 预期收益：</span>是指丙方因受让标的债权而获得的投资回报（相当于甲方投资收益），预期收益来源于标的债权中债务人支付的融资成本。预期收益计算方法为：预朗收益=受让本金×预期年化收益率／360×当期实际存续天数。</p>\r\n<p class=\"p2\" style=\"text-indent:3em;\"> 预期收益支付方式根据本协议2.5的规定执行</p>\r\n<p class=\"p2\"><span class=\"num\">1.5 服务费用：</span>本协议甲方和丙方不需要另行承担任何服务费用。</p>\r\n<p class=\"p2\"><span class=\"num\">1.6 本金回收：</span>在债务人还款期限届满之日起3个工作日内，甲方和乙方负责安排让债务人偿还丙方的受让本金，或安排第三方收购丙方持有的标的债权。</p>\r\n<p class=\"p2\"><span class=\"num\">1.7通知：</span>本次债权转让由甲方或乙方负责通知到债务人，方式以书面或电话通知为准，本协议生效后三日内通知到债务人，甲方享有的对债务的全部抗辩权自本协议生效后转移到丙方。</p>\r\n<p class=\"p2\"><span class=\"num\">1.8原《借款合同》：</span>丙方受让甲方债权后，甲方与原债务人签订的《借款合同》的所有条款均对丙方有效，丙方应当享有该协议的全部权利并承担全部义务。</p>\r\n<p class=\"p2 num\">第二条  个性化条款</p>\r\n<p class=\"p2\">2.1受让本金及收益：受让的收益本金人民币（大写）<span class=\"diyaren\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>（小写¥<span class=\"diyaren\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> ）。丙方同意支付与受让本金相同的资金作为受让标的债权的对价。甲方已收到的收益归甲方所有，甲方按上述1.4条计算方法计算的收益未收到部分由丙先行垫付给甲方。</p>\r\n<p class=\"p2\"><span class=\"num\">2.2 债权收益：</span>丙方的债权受让收益计算按甲方与债务人原签订的借款合同执行，甲方已收部分归甲方所有，甲方未收部分按《借款合同》的约定由丙方收取。</p>\r\n<p class=\"p2\"><span class=\"num\">2.3 预期收益率：</span>丙方可获得的预期年化收益率为<span class=\"diyaren\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>%。</p>\r\n<p class=\"p2\"><span class=\"num\">2.4 收益分配方式：</span>按月分配。债权受让前的收益归甲方所有，按照1.4条的计算方法进行计算，丙方的收益自接受债权日起，预期收益每月分配一次，分配日详见甲方原签订的《借款合同》。</p>\r\n</section>\r\n<p class=\"p2\"><span class=\"num\">2.5 收益账户：</span>指丙方名下用于接受预期收益分配和受让本金的返还的银行账户。丙方指定账户丙方在小虾理财平台绑定的银行账户。</p>\r\n<p class=\"p2 num\" style=\"text-indent:3em;\">因银行卡毁损、挂失、销户等原因造成该账户无法使用而需要更换收益账户的，甲方应向丙方提出申请并按照丙方需求提交相关材料。</p>\r\n<p class=\"p2 num\">第三条 债权管理</p>\r\n<p class=\"p2\"><span class=\"num\">3.1</span> 丙方作为标的债权的新债权人，在此授权甲方和乙方全权处理债权转让后的管理事务，包括但不限于了解及监督借款资金使用、债务人工作或经营情况以及担保物价值变化等，一旦发现可能有损甲方利益的情况，甲方和乙方有权及时采取资产保全措施并及时通报丙方。</p>\r\n<p class=\"p2\"><span class=\"num\">3.2</span> 本协议下标的债权转让后，其相关附属权利（包括抵押权、质押权等）均一并转让予受让方（丙方）。丙方作为新的债权人可授权转让方（原债权人）代为继续行使光管附属权利，不作变更登记，丙方对此表示认可。</p>\r\n<p class=\"p2\"><span class=\"num\">3.3</span> 债务到期，债务人未足额履行还款义务的，甲方、乙方应负责协助丙方向债务人追讨并依法处置担保物。</p>\r\n<p class=\"p2 num\">第四条  各方义务</p>\r\n<p class=\"p2 num\">4.1 丙方义务</p>\r\n<p class=\"p2\">4.1.1 保证其用于受让标的债权的资金来源合法且拥有完整的处分权，如果第三人对其资金来源提出异议，由丙方承担相应后果。</p>\r\n<p class=\"p2\">4.1.2 具有独立的风险承受能力，对债权转让的方式、结构和可能存在的风险已经理解并表示认可。</p>\r\n<p class=\"p2\">4.1.3 承诺签订本协议后按时按照本协议的约定支付受让债权对价。</p>\r\n<p class=\"p2\">4.1.4 受让期限届满，在全额收到投资本金和逾期收益后，将本协议原件交还至乙方。</p>\r\n<p class=\"p2\">4.1.5 自愿接受乙方客服人员电话回访，配合乙方核实相关信息。</p>\r\n<p class=\"p2\">4.1.6 本协议有效期内，不注销受益账户。</p>\r\n<p class=\"p2 num\">4.2乙方义务</p>\r\n<p class=\"p2\">4.2.1保证提供的标的债权真实有效。</p>\r\n<p class=\"p2\">4.2.2确保对提供标的债权的债务人进行了全面的尽职调查，并列丙方受让标的债权后与债务人形成新的债权债务关系的真实性进行见证。</p>\r\n<p class=\"p2\">4.2.3标的债权的债务人出现违约行为时，及时采取有效措施协助相关方进行催讨或依法协助处置担保物。</p>\r\n<p class=\"p2 num\">4.3甲方义务</p>\r\n<p class=\"p2\">4. 3.1对乙方用于转让的标的债权进行尽职审查，以保证其真实有效。</p>\r\n<p class=\"p2\">4.3.2对标的债权的债务人的还款能力、担保物价值等做出独立评价，并向丙方进行风险揭示。</p>\r\n<p class=\"p2\">4.3.3若因甲方在债权转让过程中有故意隐瞒不利于还款的真相或重大过失致使乙方债权无法得到偿还的，甲方应全额赔偿丙方损失。</p>\r\n<p class=\"p2\">4.3.4指定专人对标的债权的全套材料（包括借款合同／担保合同、担保物权属材料、抵／质押登记文件等）进行保管，发生丢失承担相应责任。</p>\r\n<p class=\"p2\">4.3.5对丙方个人信息、资产情况及其相关服务情况和资料依法保密。</p>\r\n<p class=\"p2 num\">第五条  风险及对策</p>\r\n<p class=\"p2 num\">5.1政策风险</p>\r\n<p class=\"p2\" style=\"text-indent:3em;\"><span class=\"num\">风险：</span>国家因宏观政策、财政政策、货币政策、行业政策、地区发展政策等因素引起的系统风险，该风险可能导致甲方资金遭受损失。</p>\r\n<p class=\"p2\" style=\"text-indent:3em;\"><span class=\"num\">对策：</span>甲方、乙方持续研究国家法律政策和行业信息，坚决不进入高风险行业，确保每笔债权合法合规。</p>\r\n<p class=\"p2 num\">5.2不可抗力</p>\r\n<p class=\"p2\" style=\"text-indent:3em;\"><span class=\"num\">风险：</span>由于战争、动乱、自然灾害等不可抗力因素的出现而可能导致丙方的资金遭受损失。</p>\r\n<p class=\"p2\" style=\"text-indent:3em;\"><span class=\"num\">对策：</span>对于比较敏感的抵押物／质押物，要求办理足额保险。</p>\r\n<p class=\"p2 num\">5. 3债务人履约风险</p>\r\n<p class=\"p2\" style=\"text-indent:3em;\"><span class=\"num\">风险：</span>债务人可能因为工作变动或经营状况恶化丧失还款能力，从而使丙方资金遭受损失。</p>\r\n<p class=\"p2\" style=\"text-indent:3em;\"><span class=\"num\">对策：</span>甲方、乙方会对每笔债权的债务人进行严格的尽职调查，债权存续期间持续跟踪债务人还款能力。如果债务人不能及时还款，甲方承诺先行垫付，确保丙方资金安全。</p>\r\n<p class=\"p2 num\">第六条  税务处理</p>\r\n<p class=\"p2\" style=\"text-indent:3em;\">本协议项下的相关税收由各方根据法律规定各自承担。丙方在本业务中产生的相关税收应自行向主管税务机关申报和缴纳，乙方不负责代扣，也不负责代为处理。</p>\r\n<p class=\"p2 num\">第七条  信息披露</p>\r\n<p class=\"p2\" style=\"text-indent:3em;\">债权转让后，如果债务人的经营情况、还款能力和抵／质押物发生重大变化并危及到债权的实现，甲方应获悉上述情况后的2个工作日内将相关信息披露给丙方。</p>\r\n<p class=\"p2 num\">第八条  违约责任</p>\r\n<p class=\"p2\" style=\"text-indent:3em;\">任何一方违反本协议的约定，使得本协议的全部或部分不能履行，均应承担违约责任，并赔偿对方因此遭受的全部损失（包括由此产生的诉讼费、律师费、公证费、评估费、保全费用、拍卖费和强制执行费用）；如多方违约，根据实际情况各自承担相应的责任。违约方应赔偿因其违约而给其他方造成的损失，包括合同履行后可以获得的利益，但不得超过违约合同一方订立合同时可以预见或应当预见的因违反合同可能造成的损失。</p>\r\n<p class=\"p2 num\">第九条  争议处理</p>\r\n<p class=\"p2\" style=\"text-indent:3em;\">本协议在履行过程中，如产生任何争执或纠纷，且协商不成的，向有管辖权的人民法院提起诉讼。</p>\r\n<p class=\"p2 num\">第十条  其他事项</p>\r\n<p class=\"p2\"><span class=\"num\">10.1</span>如果丙方财产涉及继承或赠与，必须由主张权利的继承人或受赠人向甲方、乙方出示国家权威机关认证的继承或赠与权利归属证明文件，经甲方、乙方确认后方予协助办理资产转移手续，由此产生的相关税费，由主张权利的继承人或受赠人向其主管税务机关申报、缴纳，甲方、乙方不负责相关事宜处理。</p>\r\n<p class=\"p2\"><span class=\"num\">10.2</span>本协议仅原件具有法律效力，传真件、复印件、扫描件等副本不具有法律效力。</p>\r\n<p class=\"p2\"><span class=\"num\">10.3</span>各方确认，本协议的签署、生效和履行以不违反中国的法律法规为前提。如果本协议中的任何一条或多条违反适用的法律法规，则该条将被视为无效，但该无效条款并不影响本协议其他条款的效力，各方仍应履行。</p>\r\n<p class=\"p2\"><span class=\"num\">10.4</span>本协议不得修改、添加及删除任何条款，如果有修改、添加或删除，该条款一律无效。如各方有其他约定，须签订正式的补充协议。</p>\r\n<p class=\"p2\"><span class=\"num\">10.5</span>本协议自甲、丙方签章（为自然人的签字；为法人的加盖公章并经法定代表人签字或盖章）、乙方加盖合同专用章后各方签署后生效，自各方权利义务履行完毕时终止。</p>\r\n<p class=\"p2 num\" style=\"margin:0.3rem 0;\">甲方：</p>\r\n<p class=\"p2 num\" style=\"margin-bottom:0.3rem;\">乙方：上海小虾网络科技有限公司</p>\r\n<p class=\"p2 num\" style=\"margin-bottom:0.3rem;\">丙方：</p>\r\n<div class=\"date\" style=\"margin-right:0;\">日期：XXXX年XX月XX日</div>\r\n</body>\r\n</html>\r\n', '1458875092', '1', '0');

-- ----------------------------
-- Table structure for `tb_apFetchLog_ram`
-- ----------------------------
DROP TABLE IF EXISTS `tb_apFetchLog_ram`;
CREATE TABLE `tb_apFetchLog_ram` (
  `autoid` bigint(20) NOT NULL DEFAULT '0',
  `userId` bigint(20) NOT NULL DEFAULT '0',
  `dt` int(11) NOT NULL DEFAULT '0' COMMENT '领奖的时间',
  `score` int(11) NOT NULL DEFAULT '0' COMMENT '领奖的分值段',
  `surname` varchar(5) NOT NULL DEFAULT '' COMMENT '姓',
  `phone` bigint(20) NOT NULL DEFAULT '0' COMMENT '手机号',
  `itemName` varchar(64) NOT NULL DEFAULT '' COMMENT '奖励物品',
  `itemNum` int(11) NOT NULL DEFAULT '0' COMMENT '奖励数量',
  PRIMARY KEY (`autoid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for `tb_asset_0`
-- ----------------------------
DROP TABLE IF EXISTS `tb_asset_0`;
CREATE TABLE `tb_asset_0` (
  `assetId` bigint(20) NOT NULL DEFAULT '0' COMMENT '资产ID',
  `assetName` varchar(60) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '名称',
  `assetDesc` varchar(300) NOT NULL DEFAULT '' COMMENT '描述',
  `amount` bigint(20) NOT NULL DEFAULT '0' COMMENT '金额',
  `remain` bigint(20) NOT NULL DEFAULT '0' COMMENT '资产剩余金额',
  `startYmd` bigint(20) NOT NULL DEFAULT '0' COMMENT '开始日期',
  `endYmd` bigint(20) NOT NULL DEFAULT '0' COMMENT '结束时间',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态',
  `borrowerId` varchar(30) NOT NULL DEFAULT '' COMMENT '借款人的新浪ID',
  `viewTPL` varchar(10) NOT NULL DEFAULT '' COMMENT '模板',
  `introDisplay` text NOT NULL COMMENT '详细信息',
  `createTime` bigint(20) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `iRecordVerID` int(11) NOT NULL DEFAULT '0',
  `sLockData` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`assetId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for `tb_calendar`
-- ----------------------------
DROP TABLE IF EXISTS `tb_calendar`;
CREATE TABLE `tb_calendar` (
  `Ymd` int(11) NOT NULL DEFAULT '0' COMMENT 'yyyymmdd',
  `workday` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否工作日',
  `planTotalWithdraw` bigint(20) NOT NULL DEFAULT '0' COMMENT '当日总提现限额',
  `realTotalWithdraw` bigint(20) NOT NULL DEFAULT '0' COMMENT '当日实际申请提现总额',
  `perWithdraw` bigint(20) NOT NULL DEFAULT '0' COMMENT '个人当日提现限额',
  `iRecordVerID` int(11) NOT NULL DEFAULT '0',
  `sLockData` varchar(300) DEFAULT NULL,
  PRIMARY KEY (`Ymd`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='每日配置表';

-- ----------------------------
-- Records of tb_calendar
-- ----------------------------
INSERT INTO `tb_calendar` VALUES ('20151118', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20151119', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20151120', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20151121', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20151122', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20151123', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20151124', '1', '100000000', '1301', '50000000', '9', '');
INSERT INTO `tb_calendar` VALUES ('20151125', '1', '100000000', '1200', '50000000', '3', '');
INSERT INTO `tb_calendar` VALUES ('20151126', '1', '100000000', '115100', '50000000', '7', '');
INSERT INTO `tb_calendar` VALUES ('20151127', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20151128', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20151129', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20151130', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20151201', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20151202', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20151203', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20151204', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20151205', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20151206', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20151207', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20151208', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20151209', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20151210', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20151211', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20151212', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20151213', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20151214', '1', '100000000', '1', '50000000', '3', '');
INSERT INTO `tb_calendar` VALUES ('20151215', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20151216', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20151217', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20151218', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20151219', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20151220', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20151221', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20151222', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20151223', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20151224', '1', '100000000', '19900', '50000000', '9', '');
INSERT INTO `tb_calendar` VALUES ('20151225', '1', '100000000', '99000', '50000000', '3', '');
INSERT INTO `tb_calendar` VALUES ('20151226', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20151227', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20151228', '1', '100000000', '1400', '50000000', '5', '');
INSERT INTO `tb_calendar` VALUES ('20151229', '1', '100000000', '23127802', '50000000', '63', '');
INSERT INTO `tb_calendar` VALUES ('20151230', '1', '100000000', '100000000', '50000000', '3', '');
INSERT INTO `tb_calendar` VALUES ('20151231', '1', '100000000', '623957', '50000000', '39', '');
INSERT INTO `tb_calendar` VALUES ('20160101', '1', '100000000', '2293100', '50000000', '17', '');
INSERT INTO `tb_calendar` VALUES ('20160102', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160103', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160104', '1', '100000000', '1000000', '50000000', '3', '');
INSERT INTO `tb_calendar` VALUES ('20160105', '1', '100000000', '443601', '50000000', '55', '');
INSERT INTO `tb_calendar` VALUES ('20160106', '1', '100000000', '170000', '50000000', '9', '');
INSERT INTO `tb_calendar` VALUES ('20160107', '1', '100000000', '30100', '50000000', '9', '');
INSERT INTO `tb_calendar` VALUES ('20160108', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160109', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160110', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160111', '1', '100000000', '5174900', '50000000', '33', '');
INSERT INTO `tb_calendar` VALUES ('20160112', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160113', '1', '100000000', '50100', '50000000', '5', '');
INSERT INTO `tb_calendar` VALUES ('20160114', '1', '100000000', '20000', '50000000', '5', '');
INSERT INTO `tb_calendar` VALUES ('20160115', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160116', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160117', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160118', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160119', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160120', '1', '100000000', '20200', '50000000', '5', '');
INSERT INTO `tb_calendar` VALUES ('20160121', '1', '100000000', '171300', '50000000', '15', '');
INSERT INTO `tb_calendar` VALUES ('20160122', '1', '100000000', '855400', '50000000', '3', '');
INSERT INTO `tb_calendar` VALUES ('20160123', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160124', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160125', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160126', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160127', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160128', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160129', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160130', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160131', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160201', '1', '100000000', '10400', '50000000', '7', '');
INSERT INTO `tb_calendar` VALUES ('20160202', '1', '100000000', '1252', '50000000', '3', '');
INSERT INTO `tb_calendar` VALUES ('20160203', '1', '100000000', '10000', '50000000', '3', '');
INSERT INTO `tb_calendar` VALUES ('20160204', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160205', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160206', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160207', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160208', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160209', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160210', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160211', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160212', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160213', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160214', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160215', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160216', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160217', '1', '100000000', '20000', '50000000', '5', '');
INSERT INTO `tb_calendar` VALUES ('20160218', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160219', '1', '100000000', '60000', '50000000', '5', '');
INSERT INTO `tb_calendar` VALUES ('20160220', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160221', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160222', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160223', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160224', '1', '100000000', '10000', '50000000', '3', '');
INSERT INTO `tb_calendar` VALUES ('20160225', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160226', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160227', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160228', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160229', '1', '100000000', '0', '100000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160301', '1', '100000000', '0', '100000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160302', '1', '100000000', '650200', '100000', '17', '');
INSERT INTO `tb_calendar` VALUES ('20160303', '1', '100000000', '150000', '100000', '11', '');
INSERT INTO `tb_calendar` VALUES ('20160304', '1', '100000000', '178900', '100000', '31', '');
INSERT INTO `tb_calendar` VALUES ('20160305', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160306', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160307', '1', '100000000', '125506', '100000', '27', '');
INSERT INTO `tb_calendar` VALUES ('20160308', '1', '100000000', '4300', '100000', '35', '');
INSERT INTO `tb_calendar` VALUES ('20160309', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160310', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160311', '1', '100000000', '400', '50000000', '5', '');
INSERT INTO `tb_calendar` VALUES ('20160312', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160313', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160314', '1', '100000000', '2000', '50000000', '3', '');
INSERT INTO `tb_calendar` VALUES ('20160315', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160316', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160317', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160318', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160319', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160320', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160321', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160322', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160323', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160324', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160325', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160326', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160327', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160328', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160329', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160330', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160331', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160401', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160402', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160403', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160404', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160405', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160406', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160407', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160408', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160409', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160410', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160411', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160412', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160413', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160414', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160415', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160416', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160417', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160418', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160419', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160420', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160421', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160422', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160423', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160424', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160425', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160426', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160427', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160428', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160429', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160430', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160501', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160502', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160503', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160504', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160505', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160506', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160507', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160508', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160509', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160510', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160511', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160512', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160513', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160514', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160515', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160516', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160517', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160518', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160519', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160520', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160521', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160522', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160523', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160524', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160525', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160526', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160527', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160528', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160529', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160530', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160531', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160601', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160602', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160603', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160604', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160605', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160606', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160607', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160608', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160609', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160610', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160611', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160612', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160613', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160614', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160615', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160616', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160617', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160618', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160619', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160620', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160621', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160622', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160623', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160624', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160625', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160626', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160627', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160628', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160629', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160630', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160701', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160702', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160703', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160704', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160705', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160706', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160707', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160708', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160709', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160710', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160711', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160712', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160713', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160714', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160715', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160716', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160717', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160718', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160719', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160720', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160721', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160722', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160723', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160724', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160725', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160726', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160727', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160728', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160729', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160730', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160731', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160801', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160802', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160803', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160804', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160805', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160806', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160807', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160808', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160809', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160810', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160811', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160812', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160813', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160814', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160815', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160816', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160817', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160818', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160819', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160820', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160821', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160822', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160823', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160824', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160825', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160826', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160827', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160828', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160829', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160830', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160831', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160901', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160902', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160903', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160904', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160905', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160906', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160907', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160908', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160909', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160910', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160911', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160912', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160913', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160914', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160915', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160916', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160917', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160918', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160919', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160920', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160921', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160922', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160923', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160924', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160925', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160926', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160927', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160928', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160929', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20160930', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20161001', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20161002', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20161003', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20161004', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20161005', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20161006', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20161007', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20161008', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20161009', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20161010', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20161011', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20161012', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20161013', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20161014', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20161015', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20161016', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20161017', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20161018', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20161019', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20161020', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20161021', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20161022', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20161023', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20161024', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20161025', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20161026', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20161027', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20161028', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20161029', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20161030', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20161031', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20161101', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20161102', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20161103', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20161104', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20161105', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20161106', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20161107', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20161108', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20161109', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20161110', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20161111', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20161112', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20161113', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20161114', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20161115', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20161116', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20161117', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20161118', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20161119', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20161120', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20161121', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20161122', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20161123', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20161124', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20161125', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20161126', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20161127', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20161128', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20161129', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20161130', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20161201', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20161202', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20161203', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20161204', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20161205', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20161206', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20161207', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20161208', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20161209', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20161210', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20161211', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20161212', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20161213', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20161214', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20161215', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20161216', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20161217', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20161218', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20161219', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20161220', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20161221', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20161222', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20161223', '1', '100000000', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20161224', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20161225', '0', '0', '0', '50000000', '1', null);
INSERT INTO `tb_calendar` VALUES ('20161226', '1', '100000000', '0', '50000000', '1', null);

-- ----------------------------
-- Table structure for `tb_checkin_0`
-- ----------------------------
DROP TABLE IF EXISTS `tb_checkin_0`;
CREATE TABLE `tb_checkin_0` (
  `userId` bigint(20) unsigned NOT NULL COMMENT '用户ID',
  `ymd` int(11) unsigned NOT NULL COMMENT '签到日期',
  `total` int(11) unsigned zerofill NOT NULL COMMENT '总签到次数',
  `number` int(11) unsigned NOT NULL COMMENT '当前签到次数',
  `date` int(11) unsigned NOT NULL COMMENT '具体签到时间',
  `iRecordVerID` int(11) NOT NULL COMMENT 'iRecordVerID',
  `bonus` varchar(255) DEFAULT NULL COMMENT '奖励',
  PRIMARY KEY (`userId`,`ymd`),
  KEY `ymd` (`ymd`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='用户签到表';


-- ----------------------------
-- Table structure for `tb_checkin_1`
-- ----------------------------
DROP TABLE IF EXISTS `tb_checkin_1`;
CREATE TABLE `tb_checkin_1` (
  `userId` bigint(20) unsigned NOT NULL COMMENT '用户ID',
  `ymd` int(11) unsigned NOT NULL COMMENT '签到日期',
  `total` int(11) unsigned zerofill NOT NULL COMMENT '总签到次数',
  `number` int(11) unsigned NOT NULL COMMENT '当前签到次数',
  `date` int(11) unsigned NOT NULL COMMENT '具体签到时间',
  `iRecordVerID` int(11) NOT NULL COMMENT 'iRecordVerID',
  `bonus` varchar(255) DEFAULT NULL COMMENT '奖励',
  PRIMARY KEY (`userId`,`ymd`),
  KEY `ymd` (`ymd`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='用户签到表';


-- ----------------------------
-- Table structure for `tb_clientPatch`
-- ----------------------------
DROP TABLE IF EXISTS `tb_clientPatch`;
CREATE TABLE `tb_clientPatch` (
  `autoid` int(11) NOT NULL AUTO_INCREMENT,
  `copartnerId` int(11) DEFAULT NULL,
  `clientType` int(11) NOT NULL,
  `ver` varchar(16) NOT NULL,
  `ver1` int(11) NOT NULL DEFAULT '0',
  `ver2` int(11) NOT NULL DEFAULT '0',
  `ver3` int(11) NOT NULL DEFAULT '0',
  `ver4` int(11) NOT NULL DEFAULT '0',
  `var1` int(11) NOT NULL DEFAULT '0',
  `ymd` int(11) NOT NULL COMMENT '????',
  `enforce` tinyint(4) NOT NULL DEFAULT '0' COMMENT '?????0?????1????',
  `info` varchar(500) NOT NULL DEFAULT '' COMMENT '????',
  `url` varchar(256) NOT NULL DEFAULT 'http[s]://' COMMENT '????',
  `full` tinyint(4) NOT NULL DEFAULT '1' COMMENT '?????',
  PRIMARY KEY (`autoid`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;


-- ----------------------------
-- Table structure for `tb_config`
-- ----------------------------
DROP TABLE IF EXISTS `tb_config`;
CREATE TABLE `tb_config` (
  `k` varchar(64) NOT NULL,
  `v` varchar(500) NOT NULL,
  `intro` varchar(255) NOT NULL DEFAULT '' COMMENT '描述',
  `iRecordVerID` int(11) NOT NULL DEFAULT '1',
  `sLockData` varchar(200) NOT NULL DEFAULT '',
  `extlimit` varchar(300) NOT NULL DEFAULT '',
  PRIMARY KEY (`k`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='系统配置表';

-- ----------------------------
-- Records of tb_config
-- ----------------------------
INSERT INTO `tb_config` VALUES ('BIND_FIRST_RED_AMOUNT', '[500,500]', '#绑卡红包金额#单位分([有邀请人,没有邀请人])', '5', '', '');
INSERT INTO `tb_config` VALUES ('CHARGE_FIRST_RED_AMOUNT', '[500,500]', '#充值红包金额#单位分([有邀请人,没有邀请人])', '5', '', '');
INSERT INTO `tb_config` VALUES ('CUSTOMER_INVITE_CONTENT', '独乐了不如众乐乐，小虾理财，红包加返利，我已加入，就等你了！', '客户端分享时的内容', '1', '', '');
INSERT INTO `tb_config` VALUES ('CUSTOMER_INVITE_PICURL', 'http://apidev.xiaoxialicai.com/images/share.png', '客户端分享时的图片链接', '1', '', '');
INSERT INTO `tb_config` VALUES ('CUSTOMER_INVITE_TITLE', '小虾理财，就等你了！', '客户端分享时的标题', '1', '', '');
INSERT INTO `tb_config` VALUES ('CUSTOMER_INVITE_URL', 'http://apidev.xiaoxialicai.com/spread/register/inviteReg', '客户端分享时的超链接', '1', '', '');
INSERT INTO `tb_config` VALUES ('DayActiveUserNum', '{\"day\":\"20160330\",\"user_num\":58}', '周常奖励当日领取人数', '149', '', '');
INSERT INTO `tb_config` VALUES ('dbsql.ver', '117-tgh', '', '1', '', '');
INSERT INTO `tb_config` VALUES ('FIRSTLOGINAPP_RED_AMOUNT', '500', '首次激活app的红包奖励', '1', '', '');
INSERT INTO `tb_config` VALUES ('NewbieStepbonus', '{\"register\":\"曾经一无所有，直到注册以后...\",\"bind\":\"绑的不是卡，是千金难买的安心。\",\"recharge\":\"让你的每一分钱享受王的待遇！\",\"invest\":\"第一次给了你，只愿奖励再多点！\"}', '#引导页四句话#', '5', '', '');
INSERT INTO `tb_config` VALUES ('ORDER_ASSIGN_AMOUNT', '100000', '#购买奖励满足金额#单位分\r\n', '5', '', '');
INSERT INTO `tb_config` VALUES ('ORDER_FIRST_RED_FULE', '{\"50000\":[4500,4000],\"10000\":[2500,1500],\"500000\":[8000,6000],\"1000000\":[12000,8000]}', '#首购送红包金额规则#{\"投资额达到多少\":[自己获得多少,邀请人获得多少]}', '5', '', '');
INSERT INTO `tb_config` VALUES ('ORDER_FIRST_RED_ON', '1', '首购红包开关', '1', '', '');
INSERT INTO `tb_config` VALUES ('ORDER_FIRST_RED_TYPE', '8', '#首购奖励##{\"0\":\"关闭\",\"8\":\"红包\"}', '5', '', '');
INSERT INTO `tb_config` VALUES ('REGISTER_RED_AMOUNT', '500', '#注册红包金额#单位分', '5', '', '');
INSERT INTO `tb_config` VALUES ('safety', '[{\"title\":\"逾期应对\",\"content\":\"当发生付息逾期、还本逾期时，小虾理财将会使用风险准备金先行向投资用户进行资金垫付，同时通过专人向借款人追回借款。当借款无法及时追回时，小虾理财将向法院申请拍卖抵押房产或将抵押物转出。 小虾理财的风险准备金采取动态模式。根据平台交易量的上升同步上升。并保证每月公开当月的风险准备金，公示由银行出具的《资金报告》，公示在小虾官网及APP。\"}]', '#安全保障#', '5', '', '');
INSERT INTO `tb_config` VALUES ('SHARE_VOUCHER_DESC', '这是一个红包满天飞的季节。快来领取属于你的红包吧！', '#对外分享的描述', '5', '', '');
INSERT INTO `tb_config` VALUES ('SHARE_VOUCHER_PIC', 'http://apidev.xiaoxialicai.com/images/share.png', '#对外分享的图片地址#http://xxxx/sss.jpg', '5', '', '');
INSERT INTO `tb_config` VALUES ('SHARE_VOUCHER_TITLE', '发红包啦！', '#对外分享的标题', '5', '', '');
INSERT INTO `tb_config` VALUES ('SHARE_VOUCHER_URL', 'apidev.xiaoxialicai.com/index.php?__=public/share', '#对外分享的链接地址#http://xxxxx/xxx', '5', '', '');
INSERT INTO `tb_config` VALUES ('WECHAT_ACCESSTOKEN', '{\"accessToken\":\"lIA9hxoXb16OjDJ7qIZDhzEC0WCe0ejTwytOUeXDwCaRIIE9lGGBdB8L0tzGSgcqk9erl9vOY-pb6aD95bBT5KWBCrLrsMK9VOdFNyO3534ZVEdABAUQZ\",\"expiresIn\":1452226849}', '#微信token#格式{\"accessToken\":\"xxxxxx-xxx\",\"expiresIn\":1452226849}', '5', '', '');
INSERT INTO `tb_config` VALUES ('weekScore_BuyAmount', '{1:1,2:2,10:3,100:5}', '#购买金额得分#格式{达到多少：可获多少积分,达到多少:可获多少积分……}', '5', '', '');
INSERT INTO `tb_config` VALUES ('weekScore_BuyTimes', '{1:1,2:2,10:3,100:5}', '#购买次数得分#格式{达到多少：可获多少积分,达到多少:可获多少积分……}', '5', '', '');
INSERT INTO `tb_config` VALUES ('weekScore_Checkin', '{1:1,2:2,10:3,100:5}', '#周活跃-签到次数得分#格式{达到多少：可获多少积分,达到多少:可获多少积分……}', '5', '', '');
INSERT INTO `tb_config` VALUES ('weekScore_Invited', '{1:1,2:2,10:3,100:5}', '#周活跃-邀请用户数得分#格式{达到多少：可获多少积分,达到多少:可获多少积分……}', '5', '', '');
INSERT INTO `tb_config` VALUES ('weekScore_InvitedInvest', '{1:1,2:2,10:3,100:5}', '#周活跃邀请用户购买的金额得分#格式{达到多少：可获多少积分,达到多少:可获多少积分……}', '5', '', '');
INSERT INTO `tb_config` VALUES ('weekScore_RechargeAmount', '{1:1,2:2,10:3,100:5}', '#充值金额得分#格式{达到多少：可获多少积分,达到多少:可获多少积分……}', '5', '', '');
INSERT INTO `tb_config` VALUES ('weekScore_RechargeTimes', '{1:1,2:2,10:3,100:5}', '#充值次数得分#格式{达到多少：可获多少积分,达到多少:可获多少积分……}', '5', '', '');
INSERT INTO `tb_config` VALUES ('weekScore_RedPacketTimes', '{5:5,10:10,15:15,30:20,50:30}', '#子红包使用次数得分#格式{达到多少：可获多少积分,达到多少:可获多少积分……}', '5', '', '');
INSERT INTO `tb_config` VALUES ('weekScore_UsedShareVoucher', '{1:1,2:2,10:3,100:5}', '#周活跃-分享红包#格式{达到多少：可获多少积分,达到多少:可获多少积分……}', '5', '', '');
INSERT INTO `tb_config` VALUES ('WITHDRAW_DAY_AMOUNT', '50000000', '#提现日限额#单位分', '5', '', '');
INSERT INTO `tb_config` VALUES ('WITHDRAW_MONTH_TIMES', '5', '#提现月免费次数#', '5', '', '');
INSERT INTO `tb_config` VALUES ('WITHDRAW_PER_TIME_LIMIT_AMOUNT', '5000000', '#日提现限额#单位分', '5', '', '');

-- ----------------------------
-- Table structure for `tb_config_ram`
-- ----------------------------
DROP TABLE IF EXISTS `tb_config_ram`;
CREATE TABLE `tb_config_ram` (
  `k` varchar(64) NOT NULL,
  `v` varchar(500) NOT NULL,
  `intro` varchar(255) NOT NULL DEFAULT '' COMMENT '描述',
  `iRecordVerID` int(11) NOT NULL DEFAULT '1',
  `sLockData` varchar(200) NOT NULL DEFAULT '',
  `extlimit` varchar(300) NOT NULL DEFAULT '',
  PRIMARY KEY (`k`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='系统配置表(内存)';


-- ----------------------------
-- Table structure for `tb_contract_0`
-- ----------------------------
DROP TABLE IF EXISTS `tb_contract_0`;
CREATE TABLE `tb_contract_0` (
  `contractId` bigint(20) NOT NULL DEFAULT '0' COMMENT '议协ID',
  `copartnerAbs` varchar(36) DEFAULT NULL COMMENT '合作商英文简称',
  `ymdStart` int(255) NOT NULL DEFAULT '20000101' COMMENT '开始时间',
  `ymdEnd` int(255) NOT NULL DEFAULT '20990101' COMMENT '结束时间',
  `profitsPlan` int(255) NOT NULL DEFAULT '0' COMMENT '分成方案',
  `profitsFix` float(10,5) NOT NULL DEFAULT '1.00000' COMMENT '分成修正',
  `notes` varchar(128) NOT NULL DEFAULT '' COMMENT '备注',
  `remarks` varchar(50) NOT NULL DEFAULT '' COMMENT '专门备注用于哪个活动',
  `statusCode` int(11) NOT NULL DEFAULT '0' COMMENT '状态',
  `iRecordVerID` int(255) NOT NULL DEFAULT '0' COMMENT '[系统]',
  `promotionWay` varchar(15) NOT NULL DEFAULT '' COMMENT '推广方式',
  `flgDisplay` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否显示此协议的数据给渠道看  0 否，1  是',
  `displayRule` tinyint(2) NOT NULL DEFAULT '0' COMMENT '设定隐藏的规则， 0 无规则，1注册，2绑卡， 3购买',
  `hidePercent` tinyint(4) NOT NULL DEFAULT '0' COMMENT '设定要隐藏的比例',
  `displayPercent` tinyint(4) NOT NULL DEFAULT '0' COMMENT '设定要显示的比例',
  PRIMARY KEY (`contractId`),
  KEY `copartner` (`copartnerAbs`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='推广协议';

-- ----------------------------
-- Table structure for `tb_copartner_0`
-- ----------------------------
DROP TABLE IF EXISTS `tb_copartner_0`;
CREATE TABLE `tb_copartner_0` (
  `copartnerId` int(11) NOT NULL DEFAULT '0' COMMENT 'copartnerId',
  `copartnerName` varchar(36) NOT NULL COMMENT '合作方ID',
  `copartnerAbs` varchar(36) DEFAULT NULL COMMENT '合作商英文简称',
  `authCode` varchar(36) NOT NULL DEFAULT '' COMMENT '授权码',
  `contractorBiz` varchar(60) DEFAULT NULL COMMENT '联系人1(业务)',
  `contractorDev` varchar(60) DEFAULT NULL COMMENT '联系人2(技术)',
  `flgDisable` tinyint(255) NOT NULL DEFAULT '0' COMMENT '渠道使用状态',
  `iRecordVerID` int(255) NOT NULL DEFAULT '0',
  PRIMARY KEY (`copartnerId`),
  UNIQUE KEY `abs` (`copartnerAbs`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='推广合作商';


-- ----------------------------
-- Table structure for `tb_dayBuy`
-- ----------------------------
DROP TABLE IF EXISTS `tb_dayBuy`;
CREATE TABLE `tb_dayBuy` (
  `sn` bigint(20) NOT NULL,
  `ymd` bigint(20) NOT NULL,
  `paycorp` smallint(6) NOT NULL DEFAULT '0',
  `waresId` bigint(20) NOT NULL,
  `waresIdLocal` bigint(20) NOT NULL DEFAULT '0',
  `shelfId` smallint(6) NOT NULL DEFAULT '0',
  `userId` bigint(20) NOT NULL,
  `userIdLocal` bigint(20) NOT NULL DEFAULT '0',
  `amount` bigint(20) NOT NULL,
  `amountLocal` bigint(20) NOT NULL DEFAULT '0',
  `amountExtra` bigint(20) NOT NULL DEFAULT '0',
  `amountExtraLocal` bigint(20) NOT NULL DEFAULT '0',
  `file` varchar(100) NOT NULL DEFAULT '',
  `diff` varchar(100) NOT NULL DEFAULT '' COMMENT '差异',
  `checkk` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0未通过/1已通过',
  `exp` varchar(300) NOT NULL DEFAULT '',
  `updateTime` bigint(20) NOT NULL,
  `havePay` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1网关有数据   0网关没数据',
  `haveLocal` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1有本地数据     0没数据',
  PRIMARY KEY (`sn`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='充值流水（对账）';


-- ----------------------------
-- Table structure for `tb_dayInterest`
-- ----------------------------
DROP TABLE IF EXISTS `tb_dayInterest`;
CREATE TABLE `tb_dayInterest` (
  `sn` bigint(20) NOT NULL,
  `ymd` bigint(20) NOT NULL,
  `paycorp` smallint(6) NOT NULL DEFAULT '0',
  `userId` bigint(20) NOT NULL,
  `userIdLocal` bigint(20) NOT NULL DEFAULT '0',
  `amount` bigint(20) NOT NULL,
  `amountLocal` bigint(20) NOT NULL DEFAULT '0',
  `file` varchar(100) NOT NULL DEFAULT '',
  `diff` varchar(100) NOT NULL DEFAULT '' COMMENT '差异',
  `checkk` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0未通过/1已通过',
  `exp` varchar(300) NOT NULL DEFAULT '',
  `tallyId` bigint(20) NOT NULL DEFAULT '0' COMMENT '流水号',
  `statusCode` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态码',
  `updateTime` bigint(20) NOT NULL,
  `havePay` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1网关有数据   0网关没数据',
  `haveLocal` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1有本地数据     0没数据',
  PRIMARY KEY (`sn`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='存钱罐日收益';

-- ----------------------------
-- Table structure for `tb_dayLoan`
-- ----------------------------
DROP TABLE IF EXISTS `tb_dayLoan`;
CREATE TABLE `tb_dayLoan` (
  `sn` bigint(20) NOT NULL,
  `ymd` bigint(20) NOT NULL,
  `waresId` bigint(20) NOT NULL DEFAULT '0',
  `waresIdLocal` bigint(20) NOT NULL DEFAULT '0',
  `paycorp` smallint(6) NOT NULL DEFAULT '0',
  `borrowerId` bigint(20) NOT NULL DEFAULT '0',
  `borrowerIdLocal` bigint(20) NOT NULL DEFAULT '0',
  `amount` bigint(20) NOT NULL,
  `amountLocal` bigint(20) NOT NULL DEFAULT '0',
  `file` varchar(100) NOT NULL DEFAULT '',
  `diff` varchar(100) NOT NULL DEFAULT '' COMMENT '差异',
  `checkk` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0未通过/1已通过',
  `exp` varchar(300) NOT NULL DEFAULT '',
  `updateTime` bigint(20) NOT NULL,
  `havePay` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1网关有数据   0网关没数据',
  `haveLocal` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1有本地数据     0没数据',
  PRIMARY KEY (`sn`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='充值流水（对账）';


-- ----------------------------
-- Table structure for `tb_dayPayback`
-- ----------------------------
DROP TABLE IF EXISTS `tb_dayPayback`;
CREATE TABLE `tb_dayPayback` (
  `sn` bigint(20) NOT NULL,
  `ymd` bigint(20) NOT NULL,
  `waresId` bigint(20) NOT NULL DEFAULT '0',
  `waresIdLocal` bigint(20) NOT NULL DEFAULT '0',
  `paycorp` smallint(6) NOT NULL DEFAULT '0',
  `borrowerId` bigint(20) NOT NULL,
  `borrowerIdLocal` bigint(20) NOT NULL DEFAULT '0',
  `amount` bigint(20) NOT NULL,
  `amountLocal` bigint(20) NOT NULL DEFAULT '0',
  `file` varchar(100) NOT NULL DEFAULT '',
  `diff` varchar(100) NOT NULL DEFAULT '' COMMENT '差异',
  `checkk` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0未通过/1已通过',
  `exp` varchar(300) NOT NULL DEFAULT '',
  `updateTime` bigint(20) NOT NULL,
  `havePay` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1网关有数据   0网关没数据',
  `haveLocal` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1有本地数据     0没数据',
  PRIMARY KEY (`sn`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='充值流水（对账）';

-- ----------------------------
-- Table structure for `tb_dayPaysplit`
-- ----------------------------
DROP TABLE IF EXISTS `tb_dayPaysplit`;
CREATE TABLE `tb_dayPaysplit` (
  `sn` bigint(20) NOT NULL,
  `ymd` bigint(20) NOT NULL,
  `paycorp` smallint(6) NOT NULL DEFAULT '0',
  `waresId` bigint(20) NOT NULL,
  `waresIdLocal` bigint(20) NOT NULL DEFAULT '0',
  `userId` bigint(20) NOT NULL,
  `userIdLocal` bigint(20) NOT NULL DEFAULT '0',
  `amount` bigint(20) NOT NULL,
  `amountLocal` bigint(20) NOT NULL DEFAULT '0',
  `interest` bigint(20) NOT NULL DEFAULT '0',
  `interestLocal` bigint(20) NOT NULL DEFAULT '0',
  `file` varchar(100) NOT NULL DEFAULT '',
  `diff` varchar(100) NOT NULL DEFAULT '' COMMENT '差异',
  `checkk` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0未通过/1已通过',
  `exp` varchar(300) NOT NULL DEFAULT '',
  `updateTime` bigint(20) NOT NULL,
  `havePay` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1网关有数据   0网关没数据',
  `haveLocal` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1有本地数据     0没数据',
  PRIMARY KEY (`sn`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='充值流水（对账）';

-- ----------------------------
-- Table structure for `tb_dayRecharges`
-- ----------------------------
DROP TABLE IF EXISTS `tb_dayRecharges`;
CREATE TABLE `tb_dayRecharges` (
  `sn` bigint(20) NOT NULL,
  `ymd` bigint(20) NOT NULL,
  `paycorp` smallint(6) NOT NULL DEFAULT '0',
  `userId` bigint(20) NOT NULL,
  `userIdLocal` bigint(20) NOT NULL DEFAULT '0',
  `amount` bigint(20) NOT NULL,
  `amountLocal` bigint(20) NOT NULL DEFAULT '0',
  `file` varchar(100) NOT NULL DEFAULT '',
  `diff` varchar(100) NOT NULL DEFAULT '' COMMENT '差异',
  `checkk` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0未通过/1已通过',
  `exp` varchar(300) NOT NULL DEFAULT '',
  `updateTime` bigint(20) NOT NULL,
  `havePay` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1网关有数据   0网关没数据',
  `haveLocal` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1有本地数据     0没数据',
  PRIMARY KEY (`sn`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='充值流水（对账）';

-- ----------------------------
-- Table structure for `tb_dayWithdraw`
-- ----------------------------
DROP TABLE IF EXISTS `tb_dayWithdraw`;
CREATE TABLE `tb_dayWithdraw` (
  `sn` bigint(20) NOT NULL,
  `ymd` bigint(20) NOT NULL,
  `paycorp` smallint(6) NOT NULL DEFAULT '0',
  `userId` bigint(20) NOT NULL,
  `userIdLocal` bigint(20) NOT NULL DEFAULT '0',
  `amount` bigint(20) NOT NULL,
  `amountLocal` bigint(20) NOT NULL DEFAULT '0',
  `poundage` bigint(20) NOT NULL DEFAULT '0',
  `poundageLocal` bigint(20) NOT NULL DEFAULT '0',
  `file` varchar(100) NOT NULL DEFAULT '',
  `diff` varchar(100) NOT NULL DEFAULT '' COMMENT '差异',
  `checkk` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0未通过/1已通过',
  `exp` varchar(300) NOT NULL DEFAULT '',
  `updateTime` bigint(20) NOT NULL,
  `havePay` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1网关有数据   0网关没数据',
  `haveLocal` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1有本地数据     0没数据',
  PRIMARY KEY (`sn`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='充值流水（对账）';

-- ----------------------------
-- Table structure for `tb_exchangecodes_0`
-- ----------------------------
DROP TABLE IF EXISTS `tb_exchangecodes_0`;
CREATE TABLE `tb_exchangecodes_0` (
  `excode` varchar(8) NOT NULL DEFAULT '' COMMENT '兑换码',
  `grpId` varchar(10) NOT NULL DEFAULT '' COMMENT '分组id',
  `batchId` char(4) NOT NULL DEFAULT '' COMMENT '批次id',
  `dtExpire` int(11) NOT NULL DEFAULT '0' COMMENT '过期时间',
  `userId` varchar(36) NOT NULL DEFAULT '0' COMMENT '领奖的用户id',
  `dtFetch` int(11) NOT NULL DEFAULT '0' COMMENT '领取时间',
  `ordersId` varchar(36) NOT NULL DEFAULT '0' COMMENT '领奖的订单id',
  `iRecordVerID` int(11) NOT NULL COMMENT 'iRecordVerID',
  PRIMARY KEY (`excode`),
  KEY `ifused2` (`grpId`,`batchId`,`userId`),
  KEY `ifused1` (`grpId`,`userId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;


-- ----------------------------
-- Table structure for `tb_exchangecodes_1`
-- ----------------------------
DROP TABLE IF EXISTS `tb_exchangecodes_1`;
CREATE TABLE `tb_exchangecodes_1` (
  `excode` varchar(8) NOT NULL DEFAULT '' COMMENT '兑换码',
  `grpId` varchar(10) NOT NULL DEFAULT '' COMMENT '分组id',
  `batchId` char(4) NOT NULL DEFAULT '' COMMENT '批次id',
  `dtExpire` int(11) NOT NULL DEFAULT '0' COMMENT '过期时间',
  `userId` varchar(36) NOT NULL DEFAULT '0' COMMENT '领奖的用户id',
  `dtFetch` int(11) NOT NULL DEFAULT '0' COMMENT '领取时间',
  `ordersId` varchar(36) NOT NULL DEFAULT '0' COMMENT '领奖的订单id',
  `iRecordVerID` int(11) NOT NULL COMMENT 'iRecordVerID',
  PRIMARY KEY (`excode`),
  KEY `ifused2` (`grpId`,`batchId`,`userId`),
  KEY `ifused1` (`grpId`,`userId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for `tb_exchangecodes_grp`
-- ----------------------------
DROP TABLE IF EXISTS `tb_exchangecodes_grp`;
CREATE TABLE `tb_exchangecodes_grp` (
  `grpId` varchar(10) NOT NULL DEFAULT '' COMMENT '分组id',
  `batchId` char(4) NOT NULL DEFAULT '' COMMENT '批次id',
  `dtAddGrp` int(11) NOT NULL DEFAULT '0' COMMENT '分组创建时间',
  `dtAddBatch` int(11) NOT NULL DEFAULT '0' COMMENT '批次创建时间',
  `intro` varchar(500) NOT NULL DEFAULT '' COMMENT '说明',
  `bonusini` varchar(200) NOT NULL DEFAULT '' COMMENT '奖励',
  `dtExpire` int(11) NOT NULL DEFAULT '0' COMMENT '过期时间',
  `batchNum` mediumint(8) NOT NULL DEFAULT '0' COMMENT '批次兑换码数量',
  `useNum` mediumint(8) NOT NULL DEFAULT '0' COMMENT '批次兑换码已兑换数量',
  PRIMARY KEY (`grpId`,`batchId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for `tb_feedback_0`
-- ----------------------------
DROP TABLE IF EXISTS `tb_feedback_0`;
CREATE TABLE `tb_feedback_0` (
  `feedbackId` bigint(20) unsigned NOT NULL COMMENT '反馈ID',
  `userId` bigint(20) unsigned NOT NULL COMMENT '提交者ID',
  `deviceId` varchar(256) NOT NULL COMMENT '唯一设备ID',
  `content` text NOT NULL COMMENT '内容',
  `createTime` bigint(20) unsigned NOT NULL COMMENT '创建时间',
  `status` tinyint(4) unsigned NOT NULL COMMENT '状态位',
  `iRecordVerID` int(11) NOT NULL COMMENT 'iRecordVerID',
  `extends` varchar(255) NOT NULL COMMENT '扩展、其他',
  `exp` varchar(2000) DEFAULT NULL COMMENT '????',
  PRIMARY KEY (`feedbackId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='意见反馈';

-- ----------------------------
-- Table structure for `tb_feedback_1`
-- ----------------------------
DROP TABLE IF EXISTS `tb_feedback_1`;
CREATE TABLE `tb_feedback_1` (
  `feedbackId` bigint(20) unsigned NOT NULL COMMENT '反馈ID',
  `userId` bigint(20) unsigned NOT NULL COMMENT '提交者ID',
  `deviceId` varchar(256) NOT NULL COMMENT '唯一设备ID',
  `content` text NOT NULL COMMENT '内容',
  `createTime` bigint(20) unsigned NOT NULL COMMENT '创建时间',
  `status` tinyint(4) unsigned NOT NULL COMMENT '状态位',
  `iRecordVerID` int(11) NOT NULL COMMENT 'iRecordVerID',
  `extends` varchar(255) NOT NULL COMMENT '扩展、其他',
  `exp` varchar(2000) DEFAULT NULL COMMENT '????',
  PRIMARY KEY (`feedbackId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='意见反馈';

-- ----------------------------
-- Table structure for `tb_files`
-- ----------------------------
DROP TABLE IF EXISTS `tb_files`;
CREATE TABLE `tb_files` (
  `fileId` varchar(64) NOT NULL,
  `fileData` mediumblob,
  PRIMARY KEY (`fileId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC COMMENT='文件数据（图片，协议模板）';


-- ----------------------------
-- Table structure for `tb_investment_0`
-- ----------------------------
DROP TABLE IF EXISTS `tb_investment_0`;
CREATE TABLE `tb_investment_0` (
  `ordersId` bigint(20) NOT NULL COMMENT 'ordersId',
  `waresId` bigint(20) NOT NULL DEFAULT '0' COMMENT 'waresId',
  `waresName` varchar(128) NOT NULL DEFAULT '' COMMENT '标的名称',
  `shelfId` smallint(6) NOT NULL DEFAULT '0' COMMENT '类型 位了区分浮动/固定',
  `userId` bigint(20) NOT NULL COMMENT 'userId',
  `rebateId` bigint(20) NOT NULL DEFAULT '0' COMMENT '返利流水号',
  `nickname` varchar(36) NOT NULL DEFAULT '' COMMENT '姓名',
  `amount` bigint(20) NOT NULL DEFAULT '0' COMMENT '实际投资额 单位分',
  `amountExt` int(11) NOT NULL DEFAULT '0' COMMENT '活动赠送投资额（可取现） 单位分',
  `amountFake` int(11) NOT NULL DEFAULT '0' COMMENT '活动赠送投资额（不可取现） 单位分',
  `yieldStaticAdd` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '定固年化收益率上浮',
  `yieldStatic` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '定固年化收益率',
  `yieldExt` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '加息券加息',
  `interest` int(11) NOT NULL DEFAULT '0' COMMENT '总收益 单位分',
  `interestStatic` int(11) NOT NULL DEFAULT '0' COMMENT '固定收益',
  `interestAdd` int(11) NOT NULL DEFAULT '0' COMMENT '活动收益',
  `interestFloat` int(11) NOT NULL DEFAULT '0' COMMENT '浮动收益',
  `interestExt` int(11) NOT NULL DEFAULT '0' COMMENT '加息券收益 单位分',
  `interestSub` int(11) NOT NULL DEFAULT '0' COMMENT '平台贴息',
  `returnAmount` bigint(20) NOT NULL DEFAULT '0' COMMENT '累计返还本金',
  `returnInterest` int(10) NOT NULL DEFAULT '0' COMMENT '累计返还利息',
  `brief` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '投资摘要（显示列表时的数据）',
  `extDesc` varchar(256) NOT NULL COMMENT '动活赠送说明',
  `orderTime` bigint(20) NOT NULL DEFAULT '0' COMMENT '下单时间',
  `transTime` bigint(20) NOT NULL DEFAULT '0' COMMENT '转账扣款时间',
  `orderStatus` int(11) NOT NULL DEFAULT '0' COMMENT '单订状态',
  `codeCreate` varchar(128) NOT NULL DEFAULT '' COMMENT '创建流水的代码标示',
  `descCreate` varchar(128) NOT NULL DEFAULT '' COMMENT '用途的用户说明',
  `vouchers` varchar(400) NOT NULL DEFAULT '' COMMENT '使用券',
  `iRecordVerID` int(11) NOT NULL DEFAULT '0' COMMENT 'iRecordVerID',
  `returnType` int(11) NOT NULL DEFAULT '0' COMMENT '还款方式：0未定，1：一次定还本付息，2:按月付息，到期还本。。。',
  `lastReturnFundYmd` int(11) NOT NULL DEFAULT '0' COMMENT '上次付息日',
  `returnNext` int(11) NOT NULL DEFAULT '0' COMMENT '下次还款日',
  `returnPlan` varchar(4000) NOT NULL DEFAULT '',
  `exp` varchar(100) NOT NULL DEFAULT '' COMMENT '网关处理结果',
  `licence` varchar(300) NOT NULL DEFAULT '' COMMENT '许可协议',
  `firstTime` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否该用户的首次购买',
  PRIMARY KEY (`ordersId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='用户投资订单表';


-- ----------------------------
-- Table structure for `tb_investment_1`
-- ----------------------------
DROP TABLE IF EXISTS `tb_investment_1`;
CREATE TABLE `tb_investment_1` (
  `ordersId` bigint(20) NOT NULL COMMENT 'ordersId',
  `waresId` bigint(20) NOT NULL DEFAULT '0' COMMENT 'waresId',
  `waresName` varchar(128) NOT NULL DEFAULT '' COMMENT '标的名称',
  `shelfId` smallint(6) NOT NULL DEFAULT '0' COMMENT '类型 位了区分浮动/固定',
  `userId` bigint(20) NOT NULL COMMENT 'userId',
  `rebateId` bigint(20) NOT NULL DEFAULT '0' COMMENT '返利流水号',
  `nickname` varchar(36) NOT NULL DEFAULT '' COMMENT '姓名',
  `amount` bigint(20) NOT NULL DEFAULT '0' COMMENT '实际投资额 单位分',
  `amountExt` int(11) NOT NULL DEFAULT '0' COMMENT '活动赠送投资额（可取现） 单位分',
  `amountFake` int(11) NOT NULL DEFAULT '0' COMMENT '活动赠送投资额（不可取现） 单位分',
  `yieldStaticAdd` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '定固年化收益率上浮',
  `yieldStatic` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '定固年化收益率',
  `yieldExt` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '加息券加息',
  `interest` int(11) NOT NULL DEFAULT '0' COMMENT '总收益 单位分',
  `interestStatic` int(11) NOT NULL DEFAULT '0' COMMENT '固定收益',
  `interestAdd` int(11) NOT NULL DEFAULT '0' COMMENT '活动收益',
  `interestFloat` int(11) NOT NULL DEFAULT '0' COMMENT '浮动收益',
  `interestExt` int(11) NOT NULL DEFAULT '0' COMMENT '加息券收益 单位分',
  `interestSub` int(11) NOT NULL DEFAULT '0' COMMENT '平台贴息',
  `returnAmount` bigint(20) NOT NULL DEFAULT '0' COMMENT '累计返还本金',
  `returnInterest` int(10) NOT NULL DEFAULT '0' COMMENT '累计返还利息',
  `brief` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '投资摘要（显示列表时的数据）',
  `extDesc` varchar(256) NOT NULL COMMENT '动活赠送说明',
  `orderTime` bigint(20) NOT NULL DEFAULT '0' COMMENT '下单时间',
  `transTime` bigint(20) NOT NULL DEFAULT '0' COMMENT '转账扣款时间',
  `orderStatus` int(11) NOT NULL DEFAULT '0' COMMENT '单订状态',
  `codeCreate` varchar(128) NOT NULL DEFAULT '' COMMENT '创建流水的代码标示',
  `descCreate` varchar(128) NOT NULL DEFAULT '' COMMENT '用途的用户说明',
  `vouchers` varchar(400) NOT NULL DEFAULT '' COMMENT '使用券',
  `iRecordVerID` int(11) NOT NULL DEFAULT '0' COMMENT 'iRecordVerID',
  `returnType` int(11) NOT NULL DEFAULT '0' COMMENT '还款方式：0未定，1：一次定还本付息，2:按月付息，到期还本。。。',
  `lastReturnFundYmd` int(11) NOT NULL DEFAULT '0' COMMENT '上次付息日',
  `returnNext` int(11) NOT NULL DEFAULT '0' COMMENT '下次还款日',
  `returnPlan` varchar(4000) NOT NULL DEFAULT '',
  `exp` varchar(100) NOT NULL DEFAULT '' COMMENT '网关处理结果',
  `licence` varchar(300) NOT NULL DEFAULT '' COMMENT '许可协议',
  `firstTime` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否该用户的首次购买',
  PRIMARY KEY (`ordersId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='用户投资订单表';


-- ----------------------------
-- Table structure for `tb_invitecodes_0`
-- ----------------------------
DROP TABLE IF EXISTS `tb_invitecodes_0`;
CREATE TABLE `tb_invitecodes_0` (
  `inviteCode` varchar(12) NOT NULL DEFAULT '' COMMENT '邀请码',
  `userId` bigint(20) NOT NULL DEFAULT '0' COMMENT '属于哪个人的',
  `iRecordVerID` int(11) NOT NULL DEFAULT '0' COMMENT 'iRecordVerID',
  PRIMARY KEY (`inviteCode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='用户邀请码';


-- ----------------------------
-- Table structure for `tb_invitecodes_1`
-- ----------------------------
DROP TABLE IF EXISTS `tb_invitecodes_1`;
CREATE TABLE `tb_invitecodes_1` (
  `inviteCode` varchar(12) NOT NULL DEFAULT '' COMMENT '邀请码',
  `userId` bigint(20) NOT NULL DEFAULT '0' COMMENT '属于哪个人的',
  `iRecordVerID` int(11) NOT NULL DEFAULT '0' COMMENT 'iRecordVerID',
  PRIMARY KEY (`inviteCode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='用户邀请码';


-- ----------------------------
-- Table structure for `tb_managers_0`
-- ----------------------------
DROP TABLE IF EXISTS `tb_managers_0`;
CREATE TABLE `tb_managers_0` (
  `cameFrom` varchar(36) NOT NULL,
  `loginName` varchar(36) NOT NULL,
  `nickname` varchar(36) DEFAULT NULL,
  `dept` varchar(20) NOT NULL DEFAULT '' COMMENT '部门',
  `passwd` varchar(36) DEFAULT NULL,
  `passwdSalt` varchar(36) DEFAULT NULL,
  `regYmd` int(255) NOT NULL DEFAULT '0',
  `regIP` varchar(16) NOT NULL DEFAULT '0.0.0.0',
  `rights` varchar(2000) NOT NULL DEFAULT '' COMMENT '权限',
  `lastIP` varchar(16) NOT NULL DEFAULT '0.0.0.0',
  `lastYmd` int(11) NOT NULL DEFAULT '0',
  `lastHis` int(11) NOT NULL DEFAULT '0',
  `iRecordVerID` int(20) NOT NULL DEFAULT '0',
  `sLockData` varchar(100) NOT NULL DEFAULT '',
  `dtForbidden` varchar(10) DEFAULT '0' COMMENT '禁止登录时间',
  PRIMARY KEY (`cameFrom`,`loginName`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='后台管理员';

-- ----------------------------
-- Records of tb_managers_0
-- ----------------------------
INSERT INTO `tb_managers_0` VALUES ('local', 'dawa', '运营专员权限测试1', '', '111111', null, '0', '0.0.0.0', '', '140.206.74.218', '1452497701', '0', '43', '', '0');
INSERT INTO `tb_managers_0` VALUES ('local', 'dawa_002', '财务专员权限测试', '', 'ccKj1a9g', null, '0', '0.0.0.0', '*.*', '0.0.0.0', '0', '0', '2', '', '0');
INSERT INTO `tb_managers_0` VALUES ('local', 'dawa_a1', '管理员权限测试', '', '111111', null, '0', '0.0.0.0', '*.*', '0.0.0.0', '0', '0', '1', '', '0');
INSERT INTO `tb_managers_0` VALUES ('local', 'dawa_a3', '运营经理权限测试', '', '111111', null, '0', '0.0.0.0', '*.*', '0.0.0.0', '0', '0', '4', '', '0');
INSERT INTO `tb_managers_0` VALUES ('local', 'erwa', '客服经理权限测试', '', '111111', null, '0', '0.0.0.0', '*.*', '0.0.0.0', '0', '0', '3', '', '0');
INSERT INTO `tb_managers_0` VALUES ('local', 'erwa_001', '财务经理权限测试', '', '111111', null, '0', '0.0.0.0', '*.*', '0.0.0.0', '0', '0', '2', '', '0');
INSERT INTO `tb_managers_0` VALUES ('local', 'root', '超级管理员', '', '123456', '', '0', '0.0.0.0', '*.*', '140.206.74.218', '1459335103', '0', '3038', '', '0');

-- ----------------------------
-- Table structure for `tb_managers_rights`
-- ----------------------------
DROP TABLE IF EXISTS `tb_managers_rights`;
CREATE TABLE `tb_managers_rights` (
  `loginName` varchar(20) NOT NULL DEFAULT '' COMMENT '管理员',
  `rightsType` varchar(20) NOT NULL DEFAULT '' COMMENT '权限分组',
  `rights` varchar(500) NOT NULL DEFAULT '' COMMENT '理管权限',
  `rptRights` varchar(500) NOT NULL DEFAULT '' COMMENT '报表权限',
  `iRecordVerID` int(11) NOT NULL DEFAULT '0',
  `sLockData` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`loginName`,`rightsType`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='用户权限表';

-- ----------------------------
-- Records of tb_managers_rights
-- ----------------------------
INSERT INTO `tb_managers_rights` VALUES ('root', 'rpt', '', 'fin_rpt,bus_rpt,opt_rpt', '0', null);

-- ----------------------------
-- Table structure for `tb_marketing_second_0`
-- ----------------------------
DROP TABLE IF EXISTS `tb_marketing_second_0`;
CREATE TABLE `tb_marketing_second_0` (
  `userId` bigint(20) NOT NULL DEFAULT '0',
  `phone` bigint(20) NOT NULL DEFAULT '0',
  `nickname` varchar(60) NOT NULL DEFAULT '',
  `ymdReg` int(11) NOT NULL DEFAULT '0',
  `ymdBindcard` int(11) NOT NULL DEFAULT '0',
  `ymdFirstBuy` int(11) NOT NULL DEFAULT '0',
  `ymdSend` int(11) NOT NULL DEFAULT '0' COMMENT '短信发送日期',
  `statusSend` tinyint(4) NOT NULL DEFAULT '0' COMMENT '短信发送状态',
  `ymdCall` int(11) NOT NULL DEFAULT '0' COMMENT '拨打电话日期',
  `statusCall` tinyint(4) NOT NULL DEFAULT '0' COMMENT '拨打电话状态',
  `redPacket` int(11) NOT NULL DEFAULT '0' COMMENT '激励红包金额 单位分',
  `iRecordVerID` int(11) NOT NULL DEFAULT '0',
  `updateTime` bigint(20) NOT NULL DEFAULT '0',
  `updateUser` varchar(60) NOT NULL DEFAULT '',
  `sLockData` varchar(200) NOT NULL DEFAULT '',
  `exp` varchar(500) NOT NULL DEFAULT '' COMMENT '备注',
  `exp1` varchar(500) NOT NULL DEFAULT '',
  PRIMARY KEY (`userId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='二次营销';

-- ----------------------------
-- Records of tb_marketing_second_0
-- ----------------------------

-- ----------------------------
-- Table structure for `tb_message_0`
-- ----------------------------
DROP TABLE IF EXISTS `tb_message_0`;
CREATE TABLE `tb_message_0` (
  `msgId` bigint(20) unsigned NOT NULL COMMENT '消息ID',
  `title` varchar(150) NOT NULL DEFAULT '' COMMENT '标题',
  `content` text NOT NULL COMMENT '内容',
  `ext` varchar(255) NOT NULL DEFAULT '' COMMENT '扩展字段',
  `sendId` bigint(20) unsigned NOT NULL COMMENT '发送者ID',
  `receiverId` bigint(20) unsigned NOT NULL COMMENT '接受者ID',
  `createTime` bigint(20) unsigned NOT NULL COMMENT '创建时间',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态（0：未读1：已读-1：已删除）',
  `type` varchar(255) NOT NULL COMMENT '消息类型：注册、红包。。',
  `iRecordVerID` int(11) NOT NULL DEFAULT '0' COMMENT 'iRecordVerID',
  PRIMARY KEY (`msgId`),
  KEY `sendId` (`sendId`),
  KEY `receiverId` (`receiverId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='消息表';

-- ----------------------------
-- Table structure for `tb_message_1`
-- ----------------------------
DROP TABLE IF EXISTS `tb_message_1`;
CREATE TABLE `tb_message_1` (
  `msgId` bigint(20) unsigned NOT NULL COMMENT '消息ID',
  `title` varchar(150) NOT NULL DEFAULT '' COMMENT '标题',
  `content` text NOT NULL COMMENT '内容',
  `ext` varchar(255) NOT NULL DEFAULT '' COMMENT '扩展字段',
  `sendId` bigint(20) unsigned NOT NULL COMMENT '发送者ID',
  `receiverId` bigint(20) unsigned NOT NULL COMMENT '接受者ID',
  `createTime` bigint(20) unsigned NOT NULL COMMENT '创建时间',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态（0：未读1：已读-1：已删除）',
  `type` varchar(255) NOT NULL COMMENT '消息类型：注册、红包。。',
  `iRecordVerID` int(11) NOT NULL DEFAULT '0' COMMENT 'iRecordVerID',
  PRIMARY KEY (`msgId`),
  KEY `sendId` (`sendId`),
  KEY `receiverId` (`receiverId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='消息表';

-- ----------------------------
-- Table structure for `tb_odd`
-- ----------------------------
DROP TABLE IF EXISTS `tb_odd`;
CREATE TABLE `tb_odd` (
  `odd` decimal(10,2) NOT NULL COMMENT '零头数额',
  `desc` varchar(64) NOT NULL COMMENT '生产原因'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC COMMENT='资金零头表';


-- ----------------------------
-- Table structure for `tb_pointstally_0`
-- ----------------------------
DROP TABLE IF EXISTS `tb_pointstally_0`;
CREATE TABLE `tb_pointstally_0` (
  `tallyId` bigint(20) NOT NULL DEFAULT '0' COMMENT 'tallyId',
  `userId` bigint(20) NOT NULL DEFAULT '0' COMMENT 'userId',
  `orderId` bigint(20) NOT NULL DEFAULT '0' COMMENT 'orderId',
  `tallyType` int(11) NOT NULL COMMENT '类型',
  `statusCode` int(11) NOT NULL DEFAULT '0' COMMENT '状态',
  `timeCreate` bigint(20) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `codeCreate` varchar(128) NOT NULL DEFAULT '' COMMENT '创建流水的代码说明',
  `descCreate` varchar(128) NOT NULL DEFAULT '' COMMENT '创建流水的用户说明',
  `nOld` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '原余额',
  `nAdd` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '增加额（可负）',
  `nNew` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '新余额',
  `iRecordVerID` int(11) NOT NULL COMMENT 'iRecordVerID',
  PRIMARY KEY (`tallyId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='商城积分流水表';


-- ----------------------------
-- Table structure for `tb_pointstally_1`
-- ----------------------------
DROP TABLE IF EXISTS `tb_pointstally_1`;
CREATE TABLE `tb_pointstally_1` (
  `tallyId` bigint(20) NOT NULL DEFAULT '0' COMMENT 'tallyId',
  `userId` bigint(20) NOT NULL DEFAULT '0' COMMENT 'userId',
  `orderId` bigint(20) NOT NULL DEFAULT '0' COMMENT 'orderId',
  `tallyType` int(11) NOT NULL COMMENT '类型',
  `statusCode` int(11) NOT NULL DEFAULT '0' COMMENT '状态',
  `timeCreate` bigint(20) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `codeCreate` varchar(128) NOT NULL DEFAULT '' COMMENT '创建流水的代码说明',
  `descCreate` varchar(128) NOT NULL DEFAULT '' COMMENT '创建流水的用户说明',
  `nOld` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '原余额',
  `nAdd` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '增加额（可负）',
  `nNew` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '新余额',
  `iRecordVerID` int(11) NOT NULL COMMENT 'iRecordVerID',
  PRIMARY KEY (`tallyId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='商城积分流水表';

-- ----------------------------
-- Table structure for `tb_rebate_0`
-- ----------------------------
DROP TABLE IF EXISTS `tb_rebate_0`;
CREATE TABLE `tb_rebate_0` (
  `rebateId` bigint(20) NOT NULL DEFAULT '0',
  `userId` bigint(20) NOT NULL DEFAULT '0' COMMENT '邀请人ID',
  `childUserId` bigint(20) NOT NULL DEFAULT '0' COMMENT '受邀人ID',
  `childNickname` varchar(100) NOT NULL DEFAULT '',
  `childPhone` bigint(20) NOT NULL DEFAULT '0',
  `investId` bigint(20) NOT NULL DEFAULT '0' COMMENT '订单号',
  `waresId` bigint(20) NOT NULL DEFAULT '0' COMMENT '标的ID',
  `exp` varchar(255) NOT NULL,
  `amount` bigint(20) NOT NULL,
  `sumAmount` bigint(20) NOT NULL DEFAULT '0',
  `type` smallint(6) NOT NULL DEFAULT '0' COMMENT '类型',
  `statusCode` smallint(6) NOT NULL,
  `updateYmd` bigint(20) NOT NULL DEFAULT '0',
  `createYmd` bigint(20) NOT NULL,
  `iRecordVerID` int(11) NOT NULL DEFAULT '0',
  `sLockData` varchar(200) NOT NULL default '',
  PRIMARY KEY (`rebateId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='返利表';


-- ----------------------------
-- Table structure for `tb_rebate_1`
-- ----------------------------
DROP TABLE IF EXISTS `tb_rebate_1`;
CREATE TABLE `tb_rebate_1` (
  `rebateId` bigint(20) NOT NULL DEFAULT '0',
  `userId` bigint(20) NOT NULL DEFAULT '0' COMMENT '邀请人ID',
  `childUserId` bigint(20) NOT NULL DEFAULT '0' COMMENT '受邀人ID',
  `childNickname` varchar(100) NOT NULL DEFAULT '',
  `childPhone` bigint(20) NOT NULL DEFAULT '0',
  `investId` bigint(20) NOT NULL DEFAULT '0' COMMENT '订单号',
  `waresId` bigint(20) NOT NULL DEFAULT '0' COMMENT '标的ID',
  `exp` varchar(255) NOT NULL,
  `amount` bigint(20) NOT NULL,
  `sumAmount` bigint(20) NOT NULL DEFAULT '0',
  `type` smallint(6) NOT NULL DEFAULT '0' COMMENT '类型',
  `statusCode` smallint(6) NOT NULL,
  `updateYmd` bigint(20) NOT NULL DEFAULT '0',
  `createYmd` bigint(20) NOT NULL,
  `iRecordVerID` int(11) NOT NULL DEFAULT '0',
  `sLockData` varchar(200) NOT NULL default '',
  PRIMARY KEY (`rebateId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='返利表';

-- ----------------------------
-- Table structure for `tb_recharges_0`
-- ----------------------------
DROP TABLE IF EXISTS `tb_recharges_0`;
CREATE TABLE `tb_recharges_0` (
  `ordersId` bigint(20) NOT NULL COMMENT 'ordersId',
  `userId` bigint(20) NOT NULL COMMENT 'userId',
  `amount` bigint(20) NOT NULL DEFAULT '0' COMMENT '水流的金额',
  `amountAbs` bigint(20) NOT NULL DEFAULT '0' COMMENT '取正后的金额',
  `amountFlg` tinyint(4) NOT NULL DEFAULT '0' COMMENT '20:充值，30:提现',
  `poundage` bigint(10) NOT NULL DEFAULT '0' COMMENT '手续费',
  `orderTime` bigint(20) NOT NULL DEFAULT '0' COMMENT '下单时间',
  `payTime` bigint(20) NOT NULL DEFAULT '0' COMMENT '付支状态变更时间',
  `exp` varchar(100) NOT NULL DEFAULT '' COMMENT '回调说明',
  `orderStatus` int(11) NOT NULL DEFAULT '0' COMMENT '订单状态,0：初建（保留）,-1: 中断，废弃的（系统状态）,4:支付失败,2:订单已受理，等待处理结果,3:订单已受理，等待支付网关处理结果,8: 支付成功,起息前,10:起息后，回款中,21:正常回款（延期由平台垫付）,20:延期回款中,38:提前还款,39:结束：已全部回款，提现成功，充值成功,-4:异常,37:充值订单专用:成功充值，但需要更新用户的钱包余额.',
  `payCorp` int(11) NOT NULL DEFAULT '0' COMMENT '指定的支付通道ID',
  `bankAbs` varchar(16) NOT NULL DEFAULT '' COMMENT '银行缩写',
  `bankCard` varchar(20) NOT NULL DEFAULT '' COMMENT '银行卡号',
  `batchId` bigint(20) NOT NULL DEFAULT '0' COMMENT '批次操作号',
  `withdrawYmd` int(11) NOT NULL DEFAULT '0' COMMENT '提现计划到账日期',
  `iRecordVerID` int(11) NOT NULL DEFAULT '0' COMMENT 'iRecordVerID',
  PRIMARY KEY (`ordersId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='充值流水表';


-- ----------------------------
-- Table structure for `tb_recharges_1`
-- ----------------------------
DROP TABLE IF EXISTS `tb_recharges_1`;
CREATE TABLE `tb_recharges_1` (
  `ordersId` bigint(20) NOT NULL COMMENT 'ordersId',
  `userId` bigint(20) NOT NULL COMMENT 'userId',
  `amount` bigint(20) NOT NULL DEFAULT '0' COMMENT '水流的金额',
  `amountAbs` bigint(20) NOT NULL DEFAULT '0' COMMENT '取正后的金额',
  `amountFlg` tinyint(4) NOT NULL DEFAULT '0' COMMENT '20:充值，30:提现',
  `poundage` bigint(10) NOT NULL DEFAULT '0' COMMENT '手续费',
  `orderTime` bigint(20) NOT NULL DEFAULT '0' COMMENT '下单时间',
  `payTime` bigint(20) NOT NULL DEFAULT '0' COMMENT '付支状态变更时间',
  `exp` varchar(100) NOT NULL DEFAULT '' COMMENT '回调说明',
  `orderStatus` int(11) NOT NULL DEFAULT '0' COMMENT '订单状态,0：初建（保留）,-1: 中断，废弃的（系统状态）,4:支付失败,2:订单已受理，等待处理结果,3:订单已受理，等待支付网关处理结果,8: 支付成功,起息前,10:起息后，回款中,21:正常回款（延期由平台垫付）,20:延期回款中,38:提前还款,39:结束：已全部回款，提现成功，充值成功,-4:异常,37:充值订单专用:成功充值，但需要更新用户的钱包余额.',
  `payCorp` int(11) NOT NULL DEFAULT '0' COMMENT '指定的支付通道ID',
  `bankAbs` varchar(16) NOT NULL DEFAULT '' COMMENT '银行缩写',
  `bankCard` varchar(20) NOT NULL DEFAULT '' COMMENT '银行卡号',
  `batchId` bigint(20) NOT NULL DEFAULT '0' COMMENT '批次操作号',
  `withdrawYmd` int(11) NOT NULL DEFAULT '0' COMMENT '提现计划到账日期',
  `iRecordVerID` int(11) NOT NULL DEFAULT '0' COMMENT 'iRecordVerID',
  PRIMARY KEY (`ordersId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='充值流水表';


-- ----------------------------
-- Table structure for `tb_redpackettally_0`
-- ----------------------------
DROP TABLE IF EXISTS `tb_redpackettally_0`;
CREATE TABLE `tb_redpackettally_0` (
  `tallyId` bigint(20) NOT NULL DEFAULT '0' COMMENT 'tallyId',
  `userId` bigint(20) NOT NULL DEFAULT '0' COMMENT 'userId',
  `orderId` bigint(20) NOT NULL DEFAULT '0' COMMENT 'orderId',
  `tallyType` int(11) NOT NULL COMMENT '类型',
  `statusCode` int(11) NOT NULL DEFAULT '0' COMMENT '状态',
  `codeCreate` varchar(128) NOT NULL DEFAULT '' COMMENT '创建流水的代码标示',
  `descCreate` varchar(128) NOT NULL DEFAULT '' COMMENT '用途的用户说明',
  `timeCreate` bigint(20) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `nOld` bigint(20) DEFAULT NULL COMMENT '原余额 单位分',
  `nAdd` bigint(20) DEFAULT NULL COMMENT '增加额（可负） 单位分',
  `nNew` bigint(20) DEFAULT NULL COMMENT '新余额 单位分',
  `iRecordVerID` int(11) NOT NULL COMMENT 'iRecordVerID',
  PRIMARY KEY (`tallyId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='红包流水表';

-- ----------------------------
-- Table structure for `tb_redpackettally_1`
-- ----------------------------
DROP TABLE IF EXISTS `tb_redpackettally_1`;
CREATE TABLE `tb_redpackettally_1` (
  `tallyId` bigint(20) NOT NULL DEFAULT '0' COMMENT 'tallyId',
  `userId` bigint(20) NOT NULL DEFAULT '0' COMMENT 'userId',
  `orderId` bigint(20) NOT NULL DEFAULT '0' COMMENT 'orderId',
  `tallyType` int(11) NOT NULL COMMENT '类型',
  `statusCode` int(11) NOT NULL DEFAULT '0' COMMENT '状态',
  `codeCreate` varchar(128) NOT NULL DEFAULT '' COMMENT '创建流水的代码标示',
  `descCreate` varchar(128) NOT NULL DEFAULT '' COMMENT '用途的用户说明',
  `timeCreate` bigint(20) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `nOld` bigint(20) DEFAULT NULL COMMENT '原余额 单位分',
  `nAdd` bigint(20) DEFAULT NULL COMMENT '增加额（可负） 单位分',
  `nNew` bigint(20) DEFAULT NULL COMMENT '新余额 单位分',
  `iRecordVerID` int(11) NOT NULL COMMENT 'iRecordVerID',
  PRIMARY KEY (`tallyId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='红包流水表';

-- ----------------------------
-- Table structure for `tb_returnlog_0`
-- ----------------------------
DROP TABLE IF EXISTS `tb_returnlog_0`;
CREATE TABLE `tb_returnlog_0` (
  `sn` bigint(20) NOT NULL DEFAULT '0' COMMENT '款回交易流水号',
  `ordersId` bigint(20) NOT NULL DEFAULT '0' COMMENT 'ordersId',
  `waresId` bigint(20) NOT NULL DEFAULT '0' COMMENT 'waresId',
  `assetsId` bigint(20) NOT NULL DEFAULT '0' COMMENT 'assetsId',
  `userId` bigint(20) NOT NULL DEFAULT '0' COMMENT 'userId',
  `returnedAmount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '偿还本金',
  `returnedInterest` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '偿还利息',
  `returnedYmd` int(11) NOT NULL DEFAULT '0' COMMENT '还款日期',
  PRIMARY KEY (`sn`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='还款记录';


-- ----------------------------
-- Table structure for `tb_returnlog_1`
-- ----------------------------
DROP TABLE IF EXISTS `tb_returnlog_1`;
CREATE TABLE `tb_returnlog_1` (
  `sn` bigint(20) NOT NULL DEFAULT '0' COMMENT '款回交易流水号',
  `ordersId` bigint(20) NOT NULL DEFAULT '0' COMMENT 'ordersId',
  `waresId` bigint(20) NOT NULL DEFAULT '0' COMMENT 'waresId',
  `assetsId` bigint(20) NOT NULL DEFAULT '0' COMMENT 'assetsId',
  `userId` bigint(20) NOT NULL DEFAULT '0' COMMENT 'userId',
  `returnedAmount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '偿还本金',
  `returnedInterest` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '偿还利息',
  `returnedYmd` int(11) NOT NULL DEFAULT '0' COMMENT '还款日期',
  PRIMARY KEY (`sn`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='还款记录';

-- ----------------------------
-- Table structure for `tb_session_0`
-- ----------------------------
DROP TABLE IF EXISTS `tb_session_0`;
CREATE TABLE `tb_session_0` (
  `sessionId` varchar(40) NOT NULL,
  `sessionData` varchar(2000) DEFAULT NULL,
  `iRecordVerID` int(20) NOT NULL DEFAULT '0',
  `accountId` varchar(36) DEFAULT NULL,
  `lastUpdate` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`sessionId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for `tb_session_1`
-- ----------------------------
DROP TABLE IF EXISTS `tb_session_1`;
CREATE TABLE `tb_session_1` (
  `sessionId` varchar(40) NOT NULL,
  `sessionData` varchar(2000) DEFAULT NULL,
  `iRecordVerID` int(20) NOT NULL DEFAULT '0',
  `accountId` varchar(36) DEFAULT NULL,
  `lastUpdate` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`sessionId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for `tb_shelf`
-- ----------------------------
DROP TABLE IF EXISTS `tb_shelf`;
CREATE TABLE `tb_shelf` (
  `shelfId` int(20) unsigned NOT NULL COMMENT 'shelfId',
  `shelfName` varchar(128) NOT NULL COMMENT '类型名称',
  PRIMARY KEY (`shelfId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='类型定义';

-- ----------------------------
-- Table structure for `tb_shortened_url_0`
-- ----------------------------
DROP TABLE IF EXISTS `tb_shortened_url_0`;
CREATE TABLE `tb_shortened_url_0` (
  `shortId` char(20) NOT NULL DEFAULT '0' COMMENT '短链ID',
  `contractId` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'contractId',
  `scale` smallint(4) unsigned NOT NULL COMMENT '比率',
  `copartnerName` varchar(80) NOT NULL DEFAULT '' COMMENT '短链位置',
  `instruction` varchar(255) NOT NULL DEFAULT '' COMMENT '说明',
  `createTime` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `status` tinyint(3) NOT NULL DEFAULT '1' COMMENT '状态位',
  PRIMARY KEY (`shortId`,`contractId`),
  KEY `contractId` (`contractId`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='短链表';

-- ----------------------------
-- Table structure for `tb_sms_valid_0`
-- ----------------------------
DROP TABLE IF EXISTS `tb_sms_valid_0`;
CREATE TABLE `tb_sms_valid_0` (
  `phone` bigint(20) NOT NULL,
  `dat` varchar(500) NOT NULL,
  `iRecordVerID` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`phone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='短信表';


-- ----------------------------
-- Table structure for `tb_sms_valid_1`
-- ----------------------------
DROP TABLE IF EXISTS `tb_sms_valid_1`;
CREATE TABLE `tb_sms_valid_1` (
  `phone` bigint(20) NOT NULL,
  `dat` varchar(500) NOT NULL,
  `iRecordVerID` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`phone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='短信表';


-- ----------------------------
-- Table structure for `tb_sns_wechat_0`
-- ----------------------------
DROP TABLE IF EXISTS `tb_sns_wechat_0`;
CREATE TABLE `tb_sns_wechat_0` (
  `openId` varchar(50) NOT NULL DEFAULT '' COMMENT 'openId',
  `userId` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'userId',
  `loginName` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '登录名',
  `expiresIn` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '上一次绑定的时间',
  `iRecordVerID` int(11) NOT NULL DEFAULT '0' COMMENT 'KVOBJ-row-version',
  PRIMARY KEY (`openId`),
  KEY `userId` (`userId`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='微信绑定用户表';


-- ----------------------------
-- Table structure for `tb_sns_wechat_1`
-- ----------------------------
DROP TABLE IF EXISTS `tb_sns_wechat_1`;
CREATE TABLE `tb_sns_wechat_1` (
  `openId` varchar(50) NOT NULL DEFAULT '' COMMENT 'openId',
  `userId` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'userId',
  `loginName` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '登录名',
  `expiresIn` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '上一次绑定的时间',
  `iRecordVerID` int(11) NOT NULL DEFAULT '0' COMMENT 'KVOBJ-row-version',
  PRIMARY KEY (`openId`),
  KEY `userId` (`userId`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='微信绑定用户表';

-- ----------------------------
-- Table structure for `tb_systally_0`
-- ----------------------------
DROP TABLE IF EXISTS `tb_systally_0`;
CREATE TABLE `tb_systally_0` (
  `sn` bigint(20) NOT NULL DEFAULT '0',
  `amount` bigint(20) NOT NULL DEFAULT '0',
  `userId` bigint(20) NOT NULL DEFAULT '0',
  `waresId` bigint(20) NOT NULL DEFAULT '0',
  `type` smallint(6) NOT NULL DEFAULT '0',
  `statusCode` smallint(6) NOT NULL DEFAULT '0',
  `tallyYmd` bigint(20) NOT NULL DEFAULT '0',
  `exp` varchar(300) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `iRecordVerID` int(11) NOT NULL,
  PRIMARY KEY (`sn`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC COMMENT='平台流水表\r\n';


-- ----------------------------
-- Table structure for `tb_systally_1`
-- ----------------------------
DROP TABLE IF EXISTS `tb_systally_1`;
CREATE TABLE `tb_systally_1` (
  `sn` bigint(20) NOT NULL DEFAULT '0',
  `amount` bigint(20) NOT NULL DEFAULT '0',
  `userId` bigint(20) NOT NULL DEFAULT '0',
  `waresId` bigint(20) NOT NULL DEFAULT '0',
  `type` smallint(6) NOT NULL DEFAULT '0',
  `statusCode` smallint(6) NOT NULL DEFAULT '0',
  `tallyYmd` bigint(20) NOT NULL DEFAULT '0',
  `exp` varchar(300) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `iRecordVerID` int(11) NOT NULL,
  PRIMARY KEY (`sn`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC COMMENT='平台流水表\r\n';

-- ----------------------------
-- Table structure for `tb_user_0`
-- ----------------------------
DROP TABLE IF EXISTS `tb_user_0`;
CREATE TABLE `tb_user_0` (
  `userId` bigint(20) NOT NULL DEFAULT '0' COMMENT 'userId',
  `vipLevel` tinyint(4) NOT NULL DEFAULT '0' COMMENT '用户vip等级 0:非vip',
  `ymdReg` int(11) NOT NULL DEFAULT '0' COMMENT '注册日期',
  `hisReg` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '注册时分秒',
  `ymdFirstBuy` int(11) NOT NULL DEFAULT '0' COMMENT '首次功成下单',
  `ymdLastBuy` int(11) NOT NULL DEFAULT '0' COMMENT '最后一次功成下单',
  `ymdFirstCharge` int(11) NOT NULL DEFAULT '0' COMMENT '首次充值',
  `ymdBindcard` int(11) NOT NULL DEFAULT '0' COMMENT '首次绑卡日期',
  `firstLoginApp` int(5) unsigned NOT NULL DEFAULT '0' COMMENT '首次登录APP的平台',
  `ipReg` varchar(16) NOT NULL DEFAULT '0.0.0.0' COMMENT '注册时的IP',
  `ipLast` varchar(16) NOT NULL DEFAULT '0.0.0.0' COMMENT '最后一次登入的IP',
  `dtLast` bigint(20) NOT NULL DEFAULT '0' COMMENT '最后登入的时间',
  `phone` bigint(16) NOT NULL DEFAULT '0' COMMENT '默认手机号',
  `nickname` varchar(60) NOT NULL DEFAULT '' COMMENT '用户名',
  `tradePwd` varchar(40) NOT NULL DEFAULT '' COMMENT '交易密码',
  `salt` char(4) NOT NULL DEFAULT '' COMMENT '盐',
  `failedForbidden` varchar(255) NOT NULL DEFAULT '' COMMENT '用户支付密码的限制json：[''forbidden'' => 是否锁定, ''forbiddenExpires'' => 锁定时间, ''errorExpires'' => 错误次数时间, ''errorCount'' => 错误次数]',
  `wallet` bigint(20) DEFAULT '0' COMMENT '钱包余额 单位分',
  `interestTotal` bigint(20) NOT NULL DEFAULT '0' COMMENT '累计收益',
  `redPacket` int(11) DEFAULT '0' COMMENT '未使用红包余额 单位分',
  `redPacketUsed` int(11) unsigned DEFAULT '0' COMMENT '已使用的红包额度 单位分',
  `redPacketDtLast` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '最后读取红包时间',
  `points` int(11) NOT NULL DEFAULT '0' COMMENT '商城积分',
  `rebate` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '返利金额',
  `pushSetting` varchar(255) NOT NULL DEFAULT '' COMMENT '推送设置',
  `copartnerId` int(11) NOT NULL DEFAULT '0' COMMENT '推广渠道id',
  `clientType` smallint(4) NOT NULL COMMENT '客户端类型',
  `contractId` bigint(20) NOT NULL DEFAULT '0' COMMENT '推广协议Id',
  `protocol` varchar(16) NOT NULL COMMENT '注册时的协议版本号',
  `inviteByUser` bigint(12) NOT NULL DEFAULT '0' COMMENT '邀请人',
  `inviteByParent` bigint(20) NOT NULL DEFAULT '0' COMMENT '邀请人的邀请人',
  `inviteByRoot` bigint(20) NOT NULL DEFAULT '0' COMMENT '根邀请人',
  `myInviteCode` varchar(12) NOT NULL DEFAULT '' COMMENT '我的邀请码',
  `checkinBook` varchar(1000) NOT NULL DEFAULT '' COMMENT '签到簿',
  `isBorrower` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是不是借款人',
  `isSuperUser` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否超级用户  1是 0不是',
  `iRecordVerID` int(11) NOT NULL DEFAULT '0' COMMENT 'iRecordVerID',
  `sLockData` varchar(200) DEFAULT '',
  `idCard` varchar(32) NOT NULL DEFAULT '' COMMENT '身份证件号码',
  `lastWithdraw` bigint(20) NOT NULL DEFAULT '0' COMMENT '上次提现日期',
  `withdrawLeft` varchar(1000) NOT NULL DEFAULT '' COMMENT '提现赠送次数',
  `rebating` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '待返金额',
  `exchangecodegrp` varchar(501) NOT NULL DEFAULT '_' COMMENT '兑换码领取情况',
  `ap_fetched` varchar(500) NOT NULL DEFAULT '[]' COMMENT '活跃值—领取情况',
  `ap_Checkin` bigint(20) NOT NULL DEFAULT '0' COMMENT '活跃值—签到',
  `ap_Invited` bigint(20) NOT NULL DEFAULT '0' COMMENT '活跃值—邀请用户数',
  `ap_InvitedInvest` bigint(20) NOT NULL DEFAULT '0' COMMENT '活跃值—邀请用户购买的金额',
  `ap_RechargeTimes` bigint(20) NOT NULL DEFAULT '0' COMMENT '活跃值—充值次数',
  `ap_RechargeAmount` bigint(20) NOT NULL DEFAULT '0' COMMENT '活跃值—充值金额',
  `ap_BuyTimes` bigint(20) NOT NULL DEFAULT '0' COMMENT '活跃值—购买次数',
  `ap_BuyAmount` bigint(20) NOT NULL DEFAULT '0' COMMENT '活跃值—购买金额',
  `ap_UsedShareVoucher` bigint(20) unsigned NOT NULL DEFAULT '0',
  `clientFlgs` varchar(300) NOT NULL DEFAULT '{"ever":{},"daily":{}}' COMMENT '客户端自用标志位',
  `ap_RedPacketTimes` bigint(20) NOT NULL DEFAULT '0' COMMENT '活跃值—子红包使用次数',
  PRIMARY KEY (`userId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='平台用户表';


-- ----------------------------
-- Table structure for `tb_user_1`
-- ----------------------------
DROP TABLE IF EXISTS `tb_user_1`;
CREATE TABLE `tb_user_1` (
  `userId` bigint(20) NOT NULL DEFAULT '0' COMMENT 'userId',
  `vipLevel` tinyint(4) NOT NULL DEFAULT '0' COMMENT '用户vip等级 0:非vip',
  `ymdReg` int(11) NOT NULL DEFAULT '0' COMMENT '注册日期',
  `hisReg` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '注册时分秒',
  `ymdFirstBuy` int(11) NOT NULL DEFAULT '0' COMMENT '首次功成下单',
  `ymdLastBuy` int(11) NOT NULL DEFAULT '0' COMMENT '最后一次功成下单',
  `ymdFirstCharge` int(11) NOT NULL DEFAULT '0' COMMENT '首次充值',
  `ymdBindcard` int(11) NOT NULL DEFAULT '0' COMMENT '首次绑卡日期',
  `firstLoginApp` int(5) unsigned NOT NULL DEFAULT '0' COMMENT '首次登录APP的平台',
  `ipReg` varchar(16) NOT NULL DEFAULT '0.0.0.0' COMMENT '注册时的IP',
  `ipLast` varchar(16) NOT NULL DEFAULT '0.0.0.0' COMMENT '最后一次登入的IP',
  `dtLast` bigint(20) NOT NULL DEFAULT '0' COMMENT '最后登入的时间',
  `phone` bigint(16) NOT NULL DEFAULT '0' COMMENT '默认手机号',
  `nickname` varchar(60) NOT NULL DEFAULT '' COMMENT '用户名',
  `tradePwd` varchar(40) NOT NULL DEFAULT '' COMMENT '交易密码',
  `salt` char(4) NOT NULL DEFAULT '' COMMENT '盐',
  `failedForbidden` varchar(255) NOT NULL DEFAULT '' COMMENT '用户支付密码的限制json：[''forbidden'' => 是否锁定, ''forbiddenExpires'' => 锁定时间, ''errorExpires'' => 错误次数时间, ''errorCount'' => 错误次数]',
  `wallet` bigint(20) DEFAULT '0' COMMENT '钱包余额 单位分',
  `interestTotal` bigint(20) NOT NULL DEFAULT '0' COMMENT '累计收益',
  `redPacket` int(11) DEFAULT '0' COMMENT '未使用红包余额 单位分',
  `redPacketUsed` int(11) unsigned DEFAULT '0' COMMENT '已使用的红包额度 单位分',
  `redPacketDtLast` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '最后读取红包时间',
  `points` int(11) NOT NULL DEFAULT '0' COMMENT '商城积分',
  `rebate` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '返利金额',
  `pushSetting` varchar(255) NOT NULL DEFAULT '' COMMENT '推送设置',
  `copartnerId` int(11) NOT NULL DEFAULT '0' COMMENT '推广渠道id',
  `clientType` smallint(4) NOT NULL COMMENT '客户端类型',
  `contractId` bigint(20) NOT NULL DEFAULT '0' COMMENT '推广协议Id',
  `protocol` varchar(16) NOT NULL COMMENT '注册时的协议版本号',
  `inviteByUser` bigint(12) NOT NULL DEFAULT '0' COMMENT '邀请人',
  `inviteByParent` bigint(20) NOT NULL DEFAULT '0' COMMENT '邀请人的邀请人',
  `inviteByRoot` bigint(20) NOT NULL DEFAULT '0' COMMENT '根邀请人',
  `myInviteCode` varchar(12) NOT NULL DEFAULT '' COMMENT '我的邀请码',
  `checkinBook` varchar(1000) NOT NULL DEFAULT '' COMMENT '签到簿',
  `isBorrower` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是不是借款人',
  `isSuperUser` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否超级用户  1是 0不是',
  `iRecordVerID` int(11) NOT NULL DEFAULT '0' COMMENT 'iRecordVerID',
  `sLockData` varchar(200) DEFAULT '',
  `idCard` varchar(32) NOT NULL DEFAULT '' COMMENT '身份证件号码',
  `lastWithdraw` bigint(20) NOT NULL DEFAULT '0' COMMENT '上次提现日期',
  `withdrawLeft` varchar(1000) NOT NULL DEFAULT '' COMMENT '提现赠送次数',
  `rebating` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '待返金额',
  `exchangecodegrp` varchar(501) NOT NULL DEFAULT '_' COMMENT '兑换码领取情况',
  `ap_fetched` varchar(500) NOT NULL DEFAULT '[]' COMMENT '活跃值—领取情况',
  `ap_Checkin` bigint(20) NOT NULL DEFAULT '0' COMMENT '活跃值—签到',
  `ap_Invited` bigint(20) NOT NULL DEFAULT '0' COMMENT '活跃值—邀请用户数',
  `ap_InvitedInvest` bigint(20) NOT NULL DEFAULT '0' COMMENT '活跃值—邀请用户购买的金额',
  `ap_RechargeTimes` bigint(20) NOT NULL DEFAULT '0' COMMENT '活跃值—充值次数',
  `ap_RechargeAmount` bigint(20) NOT NULL DEFAULT '0' COMMENT '活跃值—充值金额',
  `ap_BuyTimes` bigint(20) NOT NULL DEFAULT '0' COMMENT '活跃值—购买次数',
  `ap_BuyAmount` bigint(20) NOT NULL DEFAULT '0' COMMENT '活跃值—购买金额',
  `ap_UsedShareVoucher` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '活跃值—用户分享红包',
  `clientFlgs` varchar(300) NOT NULL DEFAULT '{"ever":{},"daily":{}}' COMMENT '客户端自用标志位',
  `ap_RedPacketTimes` bigint(20) NOT NULL DEFAULT '0' COMMENT '活跃值—子红包使用次数',
  PRIMARY KEY (`userId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='平台用户表';


-- ----------------------------
-- Table structure for `tb_user_bankcard_0`
-- ----------------------------
DROP TABLE IF EXISTS `tb_user_bankcard_0`;
CREATE TABLE `tb_user_bankcard_0` (
  `orderId` bigint(20) NOT NULL DEFAULT '0',
  `userId` bigint(20) NOT NULL COMMENT 'userId',
  `payCorp` int(11) NOT NULL DEFAULT '0' COMMENT '验证的支付通道',
  `bankId` varchar(16) NOT NULL COMMENT '银行简写',
  `bankCard` varchar(20) NOT NULL COMMENT '行卡号银',
  `isDefault` tinyint(4) NOT NULL DEFAULT '0' COMMENT '否是默认卡',
  `statusCode` tinyint(4) NOT NULL DEFAULT '-1' COMMENT '状态：-1:待验证，0：禁用；1正常使用',
  `timeCreate` bigint(20) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `resultMsg` varchar(200) NOT NULL DEFAULT '' COMMENT '验证结果描述',
  `resultTime` bigint(20) NOT NULL DEFAULT '0' COMMENT '更新验证状态的时间',
  `unBindTime` bigint(20) NOT NULL DEFAULT '0' COMMENT '解绑日期',
  `iRecordVerID` int(11) NOT NULL DEFAULT '0' COMMENT 'iRecordVerID',
  `idCardType` tinyint(4) NOT NULL DEFAULT '0' COMMENT '证件类型',
  `idCardSN` varchar(32) NOT NULL DEFAULT '' COMMENT '证件号码',
  `realName` varchar(60) NOT NULL DEFAULT '' COMMENT '姓名',
  `phone` varchar(16) NOT NULL DEFAULT '' COMMENT '手机号',
  `cardId` varchar(32) DEFAULT '' COMMENT '绑卡以后产生唯一标识',
  PRIMARY KEY (`orderId`),
  KEY `userId` (`userId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='用户绑定的银行卡';


-- ----------------------------
-- Table structure for `tb_user_bankcard_1`
-- ----------------------------
DROP TABLE IF EXISTS `tb_user_bankcard_1`;
CREATE TABLE `tb_user_bankcard_1` (
  `orderId` bigint(20) NOT NULL DEFAULT '0',
  `userId` bigint(20) NOT NULL COMMENT 'userId',
  `payCorp` int(11) NOT NULL DEFAULT '0' COMMENT '验证的支付通道',
  `bankId` varchar(16) NOT NULL COMMENT '银行简写',
  `bankCard` varchar(20) NOT NULL COMMENT '行卡号银',
  `isDefault` tinyint(4) NOT NULL DEFAULT '0' COMMENT '否是默认卡',
  `statusCode` tinyint(4) NOT NULL DEFAULT '-1' COMMENT '状态：-1:待验证，0：禁用；1正常使用',
  `timeCreate` bigint(20) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `resultMsg` varchar(200) NOT NULL DEFAULT '' COMMENT '验证结果描述',
  `resultTime` bigint(20) NOT NULL DEFAULT '0' COMMENT '更新验证状态的时间',
  `unBindTime` bigint(20) NOT NULL DEFAULT '0' COMMENT '解绑日期',
  `iRecordVerID` int(11) NOT NULL DEFAULT '0' COMMENT 'iRecordVerID',
  `idCardType` tinyint(4) NOT NULL DEFAULT '0' COMMENT '证件类型',
  `idCardSN` varchar(32) NOT NULL DEFAULT '' COMMENT '证件号码',
  `realName` varchar(60) NOT NULL DEFAULT '' COMMENT '姓名',
  `phone` varchar(16) NOT NULL DEFAULT '' COMMENT '手机号',
  `cardId` varchar(32) DEFAULT '' COMMENT '绑卡以后产生唯一标识',
  PRIMARY KEY (`orderId`),
  KEY `userId` (`userId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='用户绑定的银行卡';


-- ----------------------------
-- Table structure for `tb_vouchers_0`
-- ----------------------------
DROP TABLE IF EXISTS `tb_vouchers_0`;
CREATE TABLE `tb_vouchers_0` (
  `voucherId` bigint(20) NOT NULL DEFAULT '0' COMMENT 'vouchersId',
  `userId` bigint(20) NOT NULL DEFAULT '0' COMMENT '给哪个用户的',
  `voucherType` varchar(16) NOT NULL DEFAULT '' COMMENT '券类型',
  `amount` int(11) NOT NULL DEFAULT '0' COMMENT '券金额 单位分/加息 单位%',
  `codeCreate` varchar(128) NOT NULL DEFAULT '' COMMENT '创建流水的代码标示',
  `descCreate` varchar(128) NOT NULL DEFAULT '' COMMENT '创建流水的用户说明',
  `timeCreate` bigint(20) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `dtExpired` bigint(20) NOT NULL DEFAULT '0' COMMENT '失效时间',
  `voucherTPL` varchar(10) NOT NULL DEFAULT 'Std01',
  `dtUsed` bigint(20) NOT NULL DEFAULT '0' COMMENT '实际使用时间',
  `orderId` bigint(20) NOT NULL DEFAULT '0' COMMENT '订单',
  `statusCode` int(11) NOT NULL DEFAULT '0' COMMENT '状态',
  `iRecordVerID` int(11) NOT NULL DEFAULT '0' COMMENT 'iRecordVerID',
  `limitsShelf` varchar(20) NOT NULL DEFAULT '' COMMENT '类型限制',
  `limitsType` varchar(20) NOT NULL DEFAULT '' COMMENT '类型限制',
  `exp1` varchar(100) NOT NULL DEFAULT '' COMMENT '使用说明',
  `exp2` varchar(100) NOT NULL DEFAULT '' COMMENT '使用说明',
  `limitsTag` varchar(20) NOT NULL DEFAULT '' COMMENT '标签限制',
  `limitsAmount` int(11) NOT NULL DEFAULT '0' COMMENT '限制使用金额',
  `limitsDeadline` int(11) NOT NULL DEFAULT '0' COMMENT '限制标的期限',
  `pid` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '父券ID，多用于字母红包等',
  `uniqueId` varchar(50) NOT NULL DEFAULT '' COMMENT '区别与userId的唯一用户标识ID，用于分享券',
  `createYmd` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '创建年月日',
  PRIMARY KEY (`voucherId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='发放到用户的各类券';


-- ----------------------------
-- Table structure for `tb_vouchers_1`
-- ----------------------------
DROP TABLE IF EXISTS `tb_vouchers_1`;
CREATE TABLE `tb_vouchers_1` (
  `voucherId` bigint(20) NOT NULL DEFAULT '0' COMMENT 'vouchersId',
  `userId` bigint(20) NOT NULL DEFAULT '0' COMMENT '给哪个用户的',
  `voucherType` varchar(16) NOT NULL DEFAULT '' COMMENT '券类型',
  `amount` int(11) NOT NULL DEFAULT '0' COMMENT '券金额 单位分/加息 单位%',
  `codeCreate` varchar(128) NOT NULL DEFAULT '' COMMENT '创建流水的代码标示',
  `descCreate` varchar(128) NOT NULL DEFAULT '' COMMENT '创建流水的用户说明',
  `timeCreate` bigint(20) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `dtExpired` bigint(20) NOT NULL DEFAULT '0' COMMENT '失效时间',
  `voucherTPL` varchar(10) NOT NULL DEFAULT 'Std01',
  `dtUsed` bigint(20) NOT NULL DEFAULT '0' COMMENT '实际使用时间',
  `orderId` bigint(20) NOT NULL DEFAULT '0' COMMENT '订单',
  `statusCode` int(11) NOT NULL DEFAULT '0' COMMENT '状态',
  `iRecordVerID` int(11) NOT NULL DEFAULT '0' COMMENT 'iRecordVerID',
  `limitsShelf` varchar(20) NOT NULL DEFAULT '' COMMENT '类型限制',
  `limitsType` varchar(20) NOT NULL DEFAULT '' COMMENT '类型限制',
  `exp1` varchar(100) NOT NULL DEFAULT '' COMMENT '使用说明',
  `exp2` varchar(100) NOT NULL DEFAULT '' COMMENT '使用说明',
  `limitsTag` varchar(20) NOT NULL DEFAULT '' COMMENT '标签限制',
  `limitsAmount` int(11) NOT NULL DEFAULT '0' COMMENT '限制使用金额',
  `limitsDeadline` int(11) NOT NULL DEFAULT '0' COMMENT '限制标的期限',
  `pid` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '父券ID，多用于字母红包等',
  `uniqueId` varchar(50) NOT NULL DEFAULT '' COMMENT '区别与userId的唯一用户标识ID，用于分享券',
  `createYmd` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '创建年月日',
  PRIMARY KEY (`voucherId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='发放到用户的各类券';


-- ----------------------------
-- Table structure for `tb_vouchers_interim_0`
-- ----------------------------
DROP TABLE IF EXISTS `tb_vouchers_interim_0`;
CREATE TABLE `tb_vouchers_interim_0` (
  `voucherId` bigint(20) unsigned NOT NULL COMMENT '券ID',
  `pid` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '父ID',
  `voucherType` varchar(16) NOT NULL DEFAULT '' COMMENT '券类型',
  `amount` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '金额',
  `timeCreate` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `dtExpired` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '有效期',
  `isUsed` tinyint(2) unsigned NOT NULL DEFAULT '1' COMMENT '是否已使用：1未使用，2已使用',
  `timeUsed` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '使用时间，领取时间',
  `isLock` tinyint(2) NOT NULL DEFAULT '1' COMMENT '是否被锁：1未被锁，2被锁',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '状态位：1可领取，2已领取',
  `iRecordVerID` int(11) NOT NULL DEFAULT '0' COMMENT 'iRecordVerID',
  PRIMARY KEY (`voucherId`),
  KEY `pid` (`pid`),
  KEY `isUsed` (`isUsed`) USING BTREE,
  KEY `isLock` (`isLock`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='子红包-临时表';


-- ----------------------------
-- Table structure for `tb_vouchers_interim_1`
-- ----------------------------
DROP TABLE IF EXISTS `tb_vouchers_interim_1`;
CREATE TABLE `tb_vouchers_interim_1` (
  `voucherId` bigint(20) unsigned NOT NULL COMMENT '券ID',
  `pid` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '父ID',
  `voucherType` varchar(16) NOT NULL DEFAULT '' COMMENT '券类型',
  `amount` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '金额',
  `timeCreate` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `dtExpired` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '有效期',
  `isUsed` tinyint(2) unsigned NOT NULL DEFAULT '1' COMMENT '是否已使用：1未使用，2已使用',
  `timeUsed` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '使用时间，领取时间',
  `isLock` tinyint(2) NOT NULL DEFAULT '1' COMMENT '是否被锁：1未被锁，2被锁',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '状态位：1可领取，2已领取',
  `iRecordVerID` int(11) NOT NULL DEFAULT '0' COMMENT 'iRecordVerID',
  PRIMARY KEY (`voucherId`),
  KEY `pid` (`pid`),
  KEY `isUsed` (`isUsed`) USING BTREE,
  KEY `isLock` (`isLock`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='子红包-临时表';


-- ----------------------------
-- Table structure for `tb_wallettally_0`
-- ----------------------------
DROP TABLE IF EXISTS `tb_wallettally_0`;
CREATE TABLE `tb_wallettally_0` (
  `tallyId` bigint(20) NOT NULL DEFAULT '0' COMMENT 'tallyId',
  `userId` bigint(20) NOT NULL DEFAULT '0' COMMENT 'userId',
  `orderId` bigint(20) NOT NULL DEFAULT '0' COMMENT 'orderId',
  `sn` bigint(20) NOT NULL DEFAULT '0' COMMENT '网关流水号',
  `tallyType` int(11) NOT NULL COMMENT '类型',
  `freeze` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否冻结的 0没冻结 1已冻结',
  `statusCode` int(11) NOT NULL DEFAULT '0' COMMENT '状态',
  `codeCreate` varchar(128) NOT NULL DEFAULT '' COMMENT '创建流水的代码标示',
  `descCreate` varchar(128) NOT NULL DEFAULT '' COMMENT '用途的用户说明',
  `timeCreate` bigint(20) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `nOld` bigint(20) DEFAULT NULL COMMENT '原余额 单位分',
  `nAdd` bigint(20) DEFAULT NULL COMMENT '增加额（可负） 单位分',
  `nNew` bigint(20) DEFAULT NULL COMMENT '新余额 单位分',
  `ext` bigint(20) NOT NULL DEFAULT '0' COMMENT '使用的红包',
  `iRecordVerID` int(11) NOT NULL COMMENT 'iRecordVerID',
  `poundage` bigint(20) DEFAULT NULL COMMENT '手续费 单位分',
  PRIMARY KEY (`tallyId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='钱包流水表';


-- ----------------------------
-- Table structure for `tb_wallettally_1`
-- ----------------------------
DROP TABLE IF EXISTS `tb_wallettally_1`;
CREATE TABLE `tb_wallettally_1` (
  `tallyId` bigint(20) NOT NULL DEFAULT '0' COMMENT 'tallyId',
  `userId` bigint(20) NOT NULL DEFAULT '0' COMMENT 'userId',
  `orderId` bigint(20) NOT NULL DEFAULT '0' COMMENT 'orderId',
  `sn` bigint(20) NOT NULL DEFAULT '0' COMMENT '网关流水号',
  `tallyType` int(11) NOT NULL COMMENT '类型',
  `freeze` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否冻结的 0没冻结 1已冻结',
  `statusCode` int(11) NOT NULL DEFAULT '0' COMMENT '状态',
  `codeCreate` varchar(128) NOT NULL DEFAULT '' COMMENT '创建流水的代码标示',
  `descCreate` varchar(128) NOT NULL DEFAULT '' COMMENT '用途的用户说明',
  `timeCreate` bigint(20) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `nOld` bigint(20) DEFAULT NULL COMMENT '原余额 单位分',
  `nAdd` bigint(20) DEFAULT NULL COMMENT '增加额（可负） 单位分',
  `nNew` bigint(20) DEFAULT NULL COMMENT '新余额 单位分',
  `ext` bigint(20) NOT NULL DEFAULT '0' COMMENT '使用的红包',
  `iRecordVerID` int(11) NOT NULL COMMENT 'iRecordVerID',
  `poundage` bigint(20) DEFAULT NULL COMMENT '手续费 单位分',
  PRIMARY KEY (`tallyId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='钱包流水表';

-- ----------------------------
-- Table structure for `tb_wares_0`
-- ----------------------------
DROP TABLE IF EXISTS `tb_wares_0`;
CREATE TABLE `tb_wares_0` (
  `waresId` bigint(20) unsigned NOT NULL COMMENT 'waresId',
  `waresName` varchar(128) NOT NULL COMMENT '标的名称',
  `waresSN` int(11) NOT NULL DEFAULT '0' COMMENT '期数',
  `deadLine` smallint(6) NOT NULL DEFAULT '360' COMMENT '期限',
  `dlUnit` varchar(10) NOT NULL DEFAULT '月',
  `tags` varchar(128) NOT NULL DEFAULT '' COMMENT '标签英文逗号分隔',
  `mainType` smallint(6) NOT NULL DEFAULT '0' COMMENT '大类',
  `subType` smallint(6) NOT NULL DEFAULT '0' COMMENT '小类',
  `userLimit` varchar(128) NOT NULL DEFAULT '0' COMMENT '限制可购买的用户类型，英文逗号隔开  0：无限制',
  `vipLevel` smallint(6) NOT NULL DEFAULT '0' COMMENT '限制购买的vip等级',
  `priceStart` int(11) NOT NULL DEFAULT '0' COMMENT '起投金额 单位分',
  `priceStep` int(11) NOT NULL DEFAULT '1' COMMENT '递增金额 单位分',
  `amount` bigint(20) NOT NULL DEFAULT '0' COMMENT '集募总额 单位分',
  `remain` bigint(20) NOT NULL DEFAULT '0' COMMENT '剩余额 单位分',
  `realRaise` bigint(20) NOT NULL DEFAULT '0' COMMENT '实际募集总额',
  `yieldStatic` decimal(10,4) NOT NULL DEFAULT '0.0000' COMMENT '定固年化收益率',
  `yieldStaticAdd` decimal(10,4) NOT NULL DEFAULT '0.0000' COMMENT '定固年化收益率上浮',
  `yieldFloatFrom` decimal(10,4) NOT NULL DEFAULT '0.0000' COMMENT '浮动年化收益率下限',
  `yieldFloatTo` decimal(10,4) NOT NULL DEFAULT '0.0000' COMMENT '浮动年化收益率上限',
  `yieldDesc` varchar(256) NOT NULL DEFAULT '' COMMENT '年化率变更详细说明',
  `introDisplay` text COMMENT '产品介绍',
  `shelfId` smallint(4) DEFAULT '0' COMMENT '类型',
  `statusCode` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态',
  `returnType` int(11) NOT NULL DEFAULT '0' COMMENT '还款方式：0未定，1：一次定还本付息，2:按月付息，到期还本。。。',
  `interestStartType` int(11) NOT NULL DEFAULT '0' COMMENT '起息方式：0:购买起息，1，购买次日起息，2:募集满起息，3:募集满次日起息',
  `timeStartPlan` bigint(20) NOT NULL DEFAULT '0' COMMENT '计划上架时间',
  `timeStartReal` bigint(20) NOT NULL DEFAULT '0' COMMENT '际实上架时间',
  `timeEndPlan` bigint(20) NOT NULL DEFAULT '0' COMMENT '闭关募集结束时间',
  `timeEndReal` bigint(11) NOT NULL DEFAULT '0' COMMENT '实际募集结束时间',
  `ymdPayReal` int(11) NOT NULL DEFAULT '0' COMMENT '实际还款日期',
  `ymdPayPlan` int(11) NOT NULL DEFAULT '0' COMMENT '预计还款日期',
  `viewTPL` varchar(10) NOT NULL DEFAULT 'Std01' COMMENT '标的模板字段',
  `returnTPL` varchar(10) NOT NULL DEFAULT 'Std01',
  `lastPaybackYmd` bigint(20) NOT NULL DEFAULT '0' COMMENT '借款人最近一次还款日',
  `paySn` bigint(20) NOT NULL DEFAULT '0' COMMENT '网关订单号',
  `payStatus` int(11) NOT NULL DEFAULT '0' COMMENT '网关状态',
  `payGift` bigint(20) NOT NULL DEFAULT '0' COMMENT '平台垫付',
  `repay` bigint(20) NOT NULL DEFAULT '0' COMMENT '企业还钱',
  `payYmd` bigint(20) NOT NULL DEFAULT '0' COMMENT '转账时间',
  `returnPlan` varchar(4000) NOT NULL DEFAULT '' COMMENT '还款计划',
  `exp` varchar(100) NOT NULL DEFAULT '' COMMENT '备注',
  `item` varchar(60) NOT NULL DEFAULT '' COMMENT '基金',
  `assetId` bigint(20) NOT NULL DEFAULT '0' COMMENT '资产来源',
  `borrowerId` bigint(20) NOT NULL DEFAULT '0' COMMENT '借款人ID',
  `iRecordVerID` int(11) NOT NULL DEFAULT '0' COMMENT '列所锁',
  `sLockData` varchar(200) DEFAULT '',
  PRIMARY KEY (`waresId`),
  KEY `statusCode` (`statusCode`,`timeStartPlan`),
  KEY `timeStartReal` (`timeStartReal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='类型上的标的';

-- ----------------------------
-- Table structure for `tb_wares_0_ram`
-- ----------------------------
DROP TABLE IF EXISTS `tb_wares_0_ram`;
CREATE TABLE `tb_wares_0_ram` (
  `waresId` bigint(20) unsigned NOT NULL COMMENT 'waresId',
  `waresName` varchar(128) NOT NULL COMMENT '标的名称',
  `waresSN` int(11) NOT NULL DEFAULT '0' COMMENT '期数',
  `deadLine` smallint(6) NOT NULL DEFAULT '360' COMMENT '期限',
  `dlUnit` varchar(10) NOT NULL DEFAULT '月',
  `tags` varchar(128) NOT NULL DEFAULT '' COMMENT '标签英文逗号分隔',
  `mainType` smallint(6) NOT NULL DEFAULT '0' COMMENT '大类',
  `subType` smallint(6) NOT NULL DEFAULT '0' COMMENT '小类',
  `userLimit` varchar(128) NOT NULL DEFAULT '0' COMMENT '限制可购买的用户类型，英文逗号隔开  0：无限制',
  `vipLevel` smallint(6) NOT NULL DEFAULT '0' COMMENT '限制购买的vip等级',
  `priceStart` int(11) NOT NULL DEFAULT '0' COMMENT '起投金额 单位分',
  `priceStep` int(11) NOT NULL DEFAULT '1' COMMENT '递增金额 单位分',
  `amount` bigint(20) NOT NULL DEFAULT '0' COMMENT '集募总额 单位分',
  `remain` bigint(20) NOT NULL DEFAULT '0' COMMENT '剩余额 单位分',
  `realRaise` bigint(20) NOT NULL DEFAULT '0' COMMENT '实际募集总额',
  `yieldStatic` decimal(10,4) NOT NULL DEFAULT '0.0000' COMMENT '定固年化收益率',
  `yieldStaticAdd` decimal(10,4) NOT NULL DEFAULT '0.0000' COMMENT '定固年化收益率上浮',
  `yieldFloatFrom` decimal(10,4) NOT NULL DEFAULT '0.0000' COMMENT '浮动年化收益率下限',
  `yieldFloatTo` decimal(10,4) NOT NULL DEFAULT '0.0000' COMMENT '浮动年化收益率上限',
  `yieldDesc` varchar(256) NOT NULL DEFAULT '' COMMENT '年化率变更详细说明',
  `introDisplay` text COMMENT '产品介绍',
  `shelfId` smallint(4) DEFAULT '0' COMMENT '类型',
  `statusCode` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态',
  `returnType` int(11) NOT NULL DEFAULT '0' COMMENT '还款方式：0未定，1：一次定还本付息，2:按月付息，到期还本。。。',
  `interestStartType` int(11) NOT NULL DEFAULT '0' COMMENT '起息方式：0:购买起息，1，购买次日起息，2:募集满起息，3:募集满次日起息',
  `timeStartPlan` bigint(20) NOT NULL DEFAULT '0' COMMENT '计划上架时间',
  `timeStartReal` bigint(20) NOT NULL DEFAULT '0' COMMENT '际实上架时间',
  `timeEndPlan` bigint(20) NOT NULL DEFAULT '0' COMMENT '闭关募集结束时间',
  `timeEndReal` bigint(11) NOT NULL DEFAULT '0' COMMENT '实际募集结束时间',
  `ymdPayReal` int(11) NOT NULL DEFAULT '0' COMMENT '实际还款日期',
  `ymdPayPlan` int(11) NOT NULL DEFAULT '0' COMMENT '预计还款日期',
  `viewTPL` varchar(10) NOT NULL DEFAULT 'Std01' COMMENT '标的模板字段',
  `returnTPL` varchar(10) NOT NULL DEFAULT 'Std01',
  `lastPaybackYmd` bigint(20) NOT NULL DEFAULT '0' COMMENT '借款人最近一次还款日',
  `paySn` bigint(20) NOT NULL DEFAULT '0' COMMENT '网关订单号',
  `payStatus` int(11) NOT NULL DEFAULT '0' COMMENT '网关状态',
  `payGift` bigint(20) NOT NULL DEFAULT '0' COMMENT '平台垫付',
  `repay` bigint(20) NOT NULL DEFAULT '0' COMMENT '企业还钱',
  `payYmd` bigint(20) NOT NULL DEFAULT '0' COMMENT '转账时间',
  `returnPlan` varchar(4000) NOT NULL DEFAULT '' COMMENT '还款计划',
  `exp` varchar(100) NOT NULL DEFAULT '' COMMENT '备注',
  `item` varchar(60) NOT NULL DEFAULT '' COMMENT '基金',
  `assetId` bigint(20) NOT NULL DEFAULT '0' COMMENT '资产来源',
  `borrowerId` bigint(20) NOT NULL DEFAULT '0' COMMENT '借款人ID',
  `iRecordVerID` int(11) NOT NULL DEFAULT '0' COMMENT '列所锁',
  `sLockData` varchar(200) DEFAULT '',
  PRIMARY KEY (`waresId`),
  KEY `statusCode` (`statusCode`,`timeStartPlan`),
  KEY `timeStartReal` (`timeStartReal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='类型上的标的';


-- ----------------------------
-- Table structure for `tb_wechat_bind_phone_0`
-- ----------------------------
DROP TABLE IF EXISTS `tb_wechat_bind_phone_0`;
CREATE TABLE `tb_wechat_bind_phone_0` (
  `openId` varchar(50) NOT NULL,
  `phone` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '绑定手机号',
  `userId` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `createTime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updTime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `iRecordVerID` int(11) NOT NULL,
  PRIMARY KEY (`openId`),
  KEY `phone` (`phone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='微信绑定手机号-不用于SNS';

-- ----------------------------
-- Table structure for `tb_wechat_bind_phone_1`
-- ----------------------------
DROP TABLE IF EXISTS `tb_wechat_bind_phone_1`;
CREATE TABLE `tb_wechat_bind_phone_1` (
  `openId` varchar(50) NOT NULL,
  `phone` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '绑定手机号',
  `userId` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `createTime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updTime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `iRecordVerID` int(11) NOT NULL,
  PRIMARY KEY (`openId`),
  KEY `phone` (`phone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='微信绑定手机号-不用于SNS';

-- ----------------------------
-- Table structure for `tb_wechat_userinfo_0`
-- ----------------------------
DROP TABLE IF EXISTS `tb_wechat_userinfo_0`;
CREATE TABLE `tb_wechat_userinfo_0` (
  `openid` varchar(50) NOT NULL COMMENT '用户的唯一标识',
  `nickname` varchar(80) NOT NULL DEFAULT '' COMMENT '用户昵称',
  `sex` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '性别：1男；2女；0未知',
  `province` varchar(40) NOT NULL DEFAULT '' COMMENT '用户个人资料填写的省份',
  `city` varchar(40) NOT NULL DEFAULT '' COMMENT '用户个人资料填写的城市',
  `country` varchar(80) NOT NULL DEFAULT '' COMMENT '国家，如中国CN',
  `headimgurl` varchar(255) NOT NULL DEFAULT '' COMMENT '用户头像',
  `privilege` varchar(255) NOT NULL DEFAULT '' COMMENT '用户特权信息，如微信沃卡用户位：chinaunicom',
  `unionid` varchar(80) NOT NULL DEFAULT '' COMMENT 'UnionID机制，需要开放平台',
  `iRecordVerID` int(11) NOT NULL DEFAULT '0' COMMENT 'KVOBJ-row-version',
  PRIMARY KEY (`openid`),
  KEY `unionid` (`unionid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='微信用户信息表';


-- ----------------------------
-- Table structure for `tb_wechat_userinfo_1`
-- ----------------------------
DROP TABLE IF EXISTS `tb_wechat_userinfo_1`;
CREATE TABLE `tb_wechat_userinfo_1` (
  `openid` varchar(50) NOT NULL COMMENT '用户的唯一标识',
  `nickname` varchar(80) NOT NULL DEFAULT '' COMMENT '用户昵称',
  `sex` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '性别：1男；2女；0未知',
  `province` varchar(40) NOT NULL DEFAULT '' COMMENT '用户个人资料填写的省份',
  `city` varchar(40) NOT NULL DEFAULT '' COMMENT '用户个人资料填写的城市',
  `country` varchar(80) NOT NULL DEFAULT '' COMMENT '国家，如中国CN',
  `headimgurl` varchar(255) NOT NULL DEFAULT '' COMMENT '用户头像',
  `privilege` varchar(255) NOT NULL DEFAULT '' COMMENT '用户特权信息，如微信沃卡用户位：chinaunicom',
  `unionid` varchar(80) NOT NULL DEFAULT '' COMMENT 'UnionID机制，需要开放平台',
  `iRecordVerID` int(11) NOT NULL DEFAULT '0' COMMENT 'KVOBJ-row-version',
  PRIMARY KEY (`openid`),
  KEY `unionid` (`unionid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='微信用户信息表';

-- ----------------------------
-- Table structure for `tb_withdraw_num_0`
-- ----------------------------
DROP TABLE IF EXISTS `tb_withdraw_num_0`;
CREATE TABLE `tb_withdraw_num_0` (
  `numId` bigint(20) NOT NULL DEFAULT '0',
  `userId` bigint(20) NOT NULL DEFAULT '0',
  `num` tinyint(4) NOT NULL DEFAULT '0',
  `month` int(11) NOT NULL DEFAULT '0',
  `updateUser` varchar(20) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `updateTime` bigint(20) NOT NULL DEFAULT '0',
  `iRecordVerID` int(11) NOT NULL DEFAULT '0',
  `statusCode` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0无效  1有效',
  `exp` varchar(200) NOT NULL DEFAULT '' COMMENT '说明',
  PRIMARY KEY (`numId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for `tb_user_idcard_0`
-- ----------------------------
DROP TABLE IF EXISTS `tb_user_idcard_0`;
CREATE TABLE `tb_user_idcard_0` (
  `id` varchar(18) NOT NULL,
  `userId` bigint not NULL DEFAULT 0 COMMENT '用户ID',
  `iRecordVerID` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='身份证表';
