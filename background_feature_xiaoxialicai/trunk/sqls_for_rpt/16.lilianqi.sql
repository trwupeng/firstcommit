use db_p2prpt;

alter table tb_voucher_grant add column orderNumber bigint(20) not null default 0  comment '序号' after taskId;
alter table tb_voucher_grant drop primary key;
update tb_voucher_grant set orderNumber = phone;
alter table tb_voucher_grant add PRIMARY key (taskId, orderNumber);
alter table tb_voucher_grant add column amount bigint(20) not null default 0 comment '金额 分' after voucherName;

update tb_voucher_grant set amount = 5 where voucherName = 'RedPacketOfKefu';
update tb_voucher_grant set amount = 2 where voucherName = 'RedPacketOfUserDefined';