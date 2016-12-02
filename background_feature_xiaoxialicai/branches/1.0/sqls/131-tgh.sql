USE db_p2p;


INSERT INTO `db_p2p`.`tb_config` (`k`, `v`, `intro`, `iRecordVerID`, `sLockData`, `extlimit`) VALUES ('IMG_CDN_SERVER', '', '#图片CDN地址#(192.168.1.1?id=)', '1', '', '');

use db_p2p;
update tb_wares_0 set tags = '新手' where tags = 'Y新手';
update tb_wares_0 set tags = '活动' where tags = 'X活动';
update tb_wares_0 set tags = '活动,新手' where tags = 'X活动,Y新手';

update tb_wares_0 set sortval = '100' where tags = '新手';
update tb_wares_0 set sortval = '1000' where tags = '活动';
update tb_wares_0 set sortval = '1100' where tags = '活动,新手';

insert into db_p2p.tb_config set k='dbsql.ver',v='131-tgh' ON DUPLICATE KEY UPDATE v='131-tgh';