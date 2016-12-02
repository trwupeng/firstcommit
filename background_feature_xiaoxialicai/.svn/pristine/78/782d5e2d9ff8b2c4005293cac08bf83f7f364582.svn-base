USE db_p2p;


        ALTER TABLE `tb_wares_0`
      MODIFY COLUMN `sortval`  int(11) NOT NULL DEFAULT 0 COMMENT '标签值 1000=活动，100=新手，1100=活动，新手' AFTER `waitInvestNum`;

      ALTER TABLE `tb_wares_0_ram`
     MODIFY COLUMN `sortval`  int(11) NOT NULL DEFAULT 0 COMMENT '标签值 1000=活动，100=新手，1100=活动，新手' AFTER `waitInvestNum`;

insert into db_p2p.tb_config set k='dbsql.ver',v='130-wupeng' ON DUPLICATE KEY UPDATE v='130-wupeng';