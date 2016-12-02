USE db_p2p;

INSERT INTO `tb_config` (`k`, `v`, `intro`) VALUES ('RANDNAME_STR_LIB', '伟刚勇永健世广山仁波宁贵福生清昌成达安岩中震振壮会思群河哲江超谦亨奇固之轮翰朗伯宏克伦翔以建家致时泰钧娟英华慧巧美珠翠雅芝玉凤洁梅晶妍茜秋珊莎锦雁蓓纨仪荷丹蓉君琴苑婕馨瑗琰纯毓悦昭冰爽琬茗羽希宁欣飘育滢馥筠柔竹霭凝晓欢霄枫舒影荔枝思丽', '#随机名字字库#必须为中文字符');

INSERT INTO `tb_config` (`k`, `v`, `intro`) VALUES ('WECHAT_EMPTY_PIC', 'http://res.xiaoxialicai.com/app/misc/wechatEmptyPic.png', '#微信空白头像');

delete from `tb_config_ram`;

insert into db_p2p.tb_config set k='dbsql.ver',v='141-lyq' ON DUPLICATE KEY UPDATE v='141-lyq';