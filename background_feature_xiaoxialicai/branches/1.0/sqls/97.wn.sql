
CREATE TABLE if not exists db_logs.tb_devsms (
  `phone` bigint(20) NOT NULL,
  `dt` bigint(20) NOT NULL,
  `msg` varchar(300) NOT NULL,
  PRIMARY KEY (`phone`,`dt`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

insert into tb_config set k='dbsql.ver',v='97-wn' ON DUPLICATE KEY UPDATE v='97-wn';