USE db_p2p;

ALTER TABLE `tb_wechat_userinfo_0`
MODIFY COLUMN `nickname`  varchar(80) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '用户昵称' AFTER `openid`,
MODIFY COLUMN `sex`  tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '性别：1男；2女；0未知' AFTER `nickname`,
MODIFY COLUMN `province`  varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '用户个人资料填写的省份' AFTER `sex`,
MODIFY COLUMN `city`  varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '用户个人资料填写的城市' AFTER `province`,
MODIFY COLUMN `country`  varchar(80) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '国家，如中国CN' AFTER `city`,
MODIFY COLUMN `headimgurl`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '用户头像' AFTER `country`,
MODIFY COLUMN `privilege`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '用户特权信息，如微信沃卡用户位：chinaunicom' AFTER `headimgurl`,
MODIFY COLUMN `unionid`  varchar(80) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'UnionID机制，需要开放平台' AFTER `privilege`,
ADD COLUMN `iRecordVerID`  int(11) NOT NULL DEFAULT 0 COMMENT 'KVOBJ-row-version' AFTER `unionid`;

insert into tb_config set k='dbsql.ver',v='93-lyq' ON DUPLICATE KEY UPDATE v='93-lyq';