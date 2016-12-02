
USE db_p2prpt;

DROP TABLE IF EXISTS `tb_copartner_worth`;
create table `tb_copartner_worth`(
  `ymd` int(11) NOT NULL comment '日期',
  `contractId` bigint(20) not null comment '协议id',
  `week` TINYINT(4) not null default 0 comment '周几',
  `newRegNum` int(11) not null default 0 comment '新增注册人数',
  `newBindNum` int(11) not null default 0 comment '新增认证绑卡人数',
  `newBoughtNum` int(11) not null default 0 comment '新增首次投资人数',
  `newBoughtAmount` bigint(20) not null default 0 comment '新增首次投资金额 分',
  `carLoanBoughtNum` int(11) not null default 0 comment '当日车贷产品投资人数',
  `carLoanBoughtAmount` int(20) not null default 0 comment '当日车贷投资金额 分',
  `houseLoanBoughtNum` int(11) not null default 0 comment '当日房贷投资人数',
  `houseLoanBoughtAmount` int(20) not null default 0 comment '当日房贷投资金额 分',
  `boughtNum` int(11) not null default 0 comment '当日所有标的投资人数',
  `boughtAmount` bigint(20) not null default 0 comment '当日所有标的投资金额',
  `unexpiredAmount` bigint (20) not null default 0 comment '在投金额',
  PRIMARY KEY (`ymd`, `contractId`)

) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='渠道数据分析表';

insert into tb_rpt_database_ver values ('3.lilianqi', '增加渠道数据分析功能，菜单：报表-》渠道倒入量');