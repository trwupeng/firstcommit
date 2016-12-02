use db_p2p;
alter table tb_copartner_0 add authCode varchar(36) NOT NULL DEFAULT '' COMMENT '授权码' after copartnerAbs;
alter table tb_contract_0 add flgDisplay TINYINT(1) not null default 0  comment '是否显示此协议的数据给渠道看  0 否，1  是'  after promotionWay;
alter table tb_contract_0 add displayRule tinyint(2) not null default 0 comment '设定隐藏的规则， 0 无规则，1按注册，2按绑卡， 3按购买';
alter table tb_contract_0 add displayPercent tinyint(4) not null default 0 comment '设定要显示的比例';

insert into tb_config set k='dbsql.ver',v='111.lilianqi' ON DUPLICATE KEY UPDATE v='111.lilianqi';
