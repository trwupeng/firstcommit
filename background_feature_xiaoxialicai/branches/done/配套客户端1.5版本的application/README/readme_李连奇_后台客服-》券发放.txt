功能说明：
	后台：客服－》券发放
	发放５元红包。有效期是发放时间开始的４８小时。可以使用通知短信和营销短信通知客户。

修改文件：
	application/library/Prj/Items/RedPacketOfKefu.php
	application/modules/Manage/controllers/Vouchergrant.php
	
修改说明：
	1. 之前版本给用户发红包时，将保存用户数据库的代码是直接写到客服红包类中。 现在将客服红包类中保存用户数据的代码与红包类分开。
	2. 原来红包最低投资额是按照元，现在改为分计算。