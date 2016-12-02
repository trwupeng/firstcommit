USE db_p2p;

ALTER TABLE `tb_invitecodes_0`
ADD COLUMN `iRecordVerID`  int(11) NOT NULL DEFAULT 0 COMMENT 'iRecordVerID' AFTER `userId`;

ALTER TABLE `tb_invitecodes_1`
ADD COLUMN `iRecordVerID`  int(11) NOT NULL DEFAULT 0 COMMENT 'iRecordVerID' AFTER `userId`;

insert into tb_config set k='dbsql.ver',v='100-lyq' ON DUPLICATE KEY UPDATE v='100-lyq';