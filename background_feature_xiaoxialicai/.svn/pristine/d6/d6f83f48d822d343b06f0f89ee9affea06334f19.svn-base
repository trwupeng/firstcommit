USE db_p2p;

drop table if exists tb_sms_valid_0;
drop table if exists tb_sms_valid_1;
CREATE TABLE tb_sms_valid_1 (  phone bigint(20) NOT NULL,  dat varchar(500) NOT NULL,  iRecordVerID int(11) NOT NULL DEFAULT '0',  PRIMARY KEY (phone)) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT '短信表';
CREATE TABLE tb_sms_valid_0 (  phone bigint(20) NOT NULL,  dat varchar(500) NOT NULL,  iRecordVerID int(11) NOT NULL DEFAULT '0',  PRIMARY KEY (phone)) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT '短信表';

insert into tb_config set k='dbsql.ver',v='94-wn' ON DUPLICATE KEY UPDATE v='94-wn';