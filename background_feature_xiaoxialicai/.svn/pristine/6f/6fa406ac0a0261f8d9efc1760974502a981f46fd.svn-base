CREATE DEFINER = CURRENT_USER PROCEDURE `batchUpdateUserRedpacket`()
    COMMENT '批量刷新user_0和user_1的红包余额'
BEGIN
/**
批量刷新user_0和user_1的红包余额
步骤：
1，从user表中获取userId的集合；
2，遍历vouchers，统计每个userId对应的红包和；
3，更新user表的红包余额；
@author：LiangYanQing
*/
	DECLARE cur_userId BIGINT;
	DECLARE dtNow BIGINT;
	DECLARE cur_amountSum INT;
	/* 获取tb_user_0中的userId */
	DECLARE col_userId0 CURSOR FOR
		SELECT userId FROM db_p2p.tb_user_0;
	/* 获取tb_user_1中的userId */
	DECLARE col_userId1 CURSOR FOR
		SELECT userId FROM db_p2p.tb_user_1;

	/* 格式化当前时间为YmdHis */
	SET dtNow = DATE_FORMAT(NOW(), '%Y%m%d%H%i%s');
	SET cur_amountSum = 0;

	/* 打开CURSOR结果集 */
	OPEN col_userId0;
		BEGIN
			/* 声明EXIT HANDLER终止遍历 */
			DECLARE EXIT HANDLER FOR 1329 BEGIN END;
			/* 遍历tb_user_0中的userId */
			LOOP
				/* 取出当前userId */
				FETCH col_userId0 INTO cur_userId;
				/* 查询tb_vouchers_0，获取所有未过期可用的红包总额 */
				SELECT sum(amount) INTO cur_amountSum FROM db_p2p.tb_vouchers_0 WHERE `userId` = cur_userId AND voucherType = 8 AND dtUsed = 0 AND statusCode = 0 AND dtExpired > dtNow;

				/* 过滤/初始化NULL为0 */
				IF cur_amountSum IS NULL THEN SET cur_amountSum = 0;
				END IF;

				/* 更新tb_user_0的红包余额与最近过期时间 */
				UPDATE db_p2p.tb_user_0 SET redPacket = cur_amountSum, redPacketRecentlyExpired = dtNow WHERE `userId` = cur_userId;

				SET cur_amountSum = 0;
			END LOOP;
		END;
	/* 关闭CURSOR的结果集 */
	CLOSE col_userId0;

	/* 遍历与查询tb_user_1和tb_vouchers_1表，代码一模一样 */
	OPEN col_userId1;
		BEGIN
			DECLARE EXIT HANDLER FOR 1329 BEGIN END;

			LOOP
				FETCH col_userId1 INTO cur_userId;
				SELECT sum(amount) INTO cur_amountSum FROM db_p2p.tb_vouchers_1 WHERE `userId` = cur_userId AND voucherType = 8 AND dtUsed = 0 AND statusCode = 0 AND dtExpired > dtNow;

				IF cur_amountSum IS NULL THEN SET cur_amountSum = 0;
				END IF;

				UPDATE db_p2p.tb_user_1 SET redPacket = cur_amountSum, redPacketRecentlyExpired = dtNow WHERE `userId` = cur_userId;

				SET cur_amountSum = 0;
			END LOOP;
		END;
	CLOSE col_userId1;
END;