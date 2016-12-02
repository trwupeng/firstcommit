1. 安装配置
	a. 解压redis-3.2.5.tar.gz
	b. 进入redis-3.2.5目录，执行make 和 make install，默认安装于/usr/local/bin
	c. 将redis-3.2.5/redis.conf 复制到 /etc/
	d. 配置redis.conf
		daemonize yes
		pidfile /var/run/redis_3888.pid
		port 3888
		bind 0.0.0.0(建议绑定内网地址)
		logfile /var/log/redis_3888.log
		loglevel warning
		slave-serve-stale-data no
		slave-read-only yes
		requirepass ihBnNmFWF07mx^csKusWVrZm*n91cEbX
		maxclients 100
		maxmemory 1g
		databases 1
		dir /var/
		slaveof 主库地址 主库端口(从库)
		masterauth ihBnNmFWF07mx^csKusWVrZm*n91cEbX
		slave-priority 1( 主库设置1，从库设置100，200，300等)

	e. 启动redis /usr/local/bin/redis-server /etc/redis.conf

2. 安装监控
	a. 在/etc/目录下新建文件sentinel.conf
	b. 配置sentinel.conf
		daemonize yes
		bind 0.0.0.0
		sentinel monitor xxmaster 192.168.1.1 3888 2
		sentinel auth-pass xxmaster ihBnNmFWF07mx^csKusWVrZm*n91cEbX
		sentinel down-after-milliseconds xxmaster 30000
		sentinel failover-timeout xxmaster 180000
		sentinel parallel-syncs xxmaster 1
		pidfile /var/run/sentinel_3886.pid
		logfile /var/log/sentinel_3886.log
		loglevel warning

	c. 启动监控 /usr/local/bin/redis-sentinel /etc/sentinel.conf
		

