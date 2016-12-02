
ALTER TABLE `tb_exchangecodes_grp`
ADD COLUMN `exp`  varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '红包类' AFTER `useNum`;

insert into tb_config set k='dbsql.ver',v='139.wupeng' ON DUPLICATE KEY UPDATE v='139.wupeng';

