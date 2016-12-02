USE db_p2p;

delete from db_p2p.tb_wechat_bind_phone_0;
INSERT INTO db_p2p.tb_wechat_bind_phone_0 (`openId`, `phone`, `userId`) SELECT
    sns.openId,
    sns.loginName,
    sns.userId
FROM
    db_p2p.tb_sns_wechat_0 sns;

insert into tb_config set k='dbsql.ver',v='96-lyq' ON DUPLICATE KEY UPDATE v='96-lyq';