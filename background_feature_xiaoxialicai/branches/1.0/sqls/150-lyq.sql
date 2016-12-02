USE db_p2p;

INSERT INTO `db_p2p`.`tb_config` (
	`k`,
	`v`,
	`intro`,
	`iRecordVerID`,
	`sLockData`,
	`extlimit`
)
VALUES
	(
		'CHECKIN_RED_AMOUNT',
		'{"16_24":230,"24_33":1355,"33_41":3415,"41_50":3415,"50_58":1355,"58_67":230}',
		'#签到红包规则#',
		'1',
		'',
		''
	);

delete from `tb_config_ram`;

insert into db_p2p.tb_config set k='dbsql.ver',v='150-lyq' ON DUPLICATE KEY UPDATE v='150-lyq';