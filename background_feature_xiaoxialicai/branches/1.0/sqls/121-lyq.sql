USE db_p2p;

UPDATE `tb_config` SET `v`='{100:5,500:10,1000:25,3000:40,5000:80}\r\n{100:5,500:10,1000:25,3000:40,5000:80}' WHERE (`k`='weekScore_BuyAmount');

UPDATE `tb_config` SET `v`='{7:10}' WHERE (`k`='weekScore_Checkin');

UPDATE `tb_config` SET `v`='{5:2,10:5,15:8,30:15,50:30}' WHERE (`k`='weekScore_Invited');

UPDATE `tb_config` SET `v`='{100:3,500:5,1000:12,3000:20,5000:40}' WHERE (`k`='weekScore_InvitedInvest');

UPDATE `tb_config` SET `v`='{5:5,10:10,15:15,30:20,50:30}' WHERE (`k`='weekScore_UsedShareVoucher');

DELETE FROM `tb_config_ram`;

insert into db_p2p.tb_config set k='dbsql.ver',v='121-lyq' ON DUPLICATE KEY UPDATE v='121-lyq';