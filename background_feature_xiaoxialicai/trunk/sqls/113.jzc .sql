USE db_p2p;
INSERT INTO `tb_config` VALUES ('weekScore_UsedShareVoucher', '{1:1,2:2,10:3,100:5}', '#周活跃-分享红包#格式{达到多少：可获多少积分,达到多少:可获多少积分……}', 1, '', '');

INSERT INTO `tb_config_ram` VALUES ('weekScore_UsedShareVoucher', '{1:1,2:2,10:3,100:5}', '#周活跃-分享红包#格式{达到多少：可获多少积分,达到多少:可获多少积分……}', 1, '', '');

ALTER TABLE `tb_user_0` ADD `ap_UsedShareVoucher` BIGINT( 20 ) UNSIGNED NOT NULL DEFAULT '0' COMMENT '活跃值—用户分享红包' AFTER `ap_BuyAmount` ;
ALTER TABLE `tb_user_1` ADD `ap_UsedShareVoucher` BIGINT( 20 ) UNSIGNED NOT NULL DEFAULT '0' COMMENT '活跃值—用户分享红包' AFTER `ap_BuyAmount` ;

insert into db_p2p.tb_config set k='dbsql.ver',v='113-jzc' ON DUPLICATE KEY UPDATE v='113-jzc';

