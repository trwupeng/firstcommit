# ICE系统说明

## 简介

rpc解决方案，带中央部署，带负载均衡，管理后台有很多命令：[https://doc.zeroc.com/display/Ice36/icegridadmin+Command+Line+Tool](https://doc.zeroc.com/display/Ice36/icegridadmin+Command+Line+Tool)
解决平台日志以及事后补发奖励等操作的耦合性

## 安装

1）ICE的安装

	wget https://zeroc.com/download/GPG-KEY-zeroc-release
	rpm --import GPG-KEY-zeroc-release
	cd /etc/yum.repos.d
	wget https://zeroc.com/download/rpm/zeroc-ice-el6.repo
	yum -y install ice-all-runtime ice-all-devel

*国外的服务器，有时会报服务器找不到，多试几次*

2）代码部分

- SVN:background_feature/ice的部分，复制到 /var/www/SoohIce目录下
- 复制 /var/www/SoohIce/ice-phplibs/IcePHP.5.4.45.so（自带的icephp.so好像有问题，用自己编译的吧） 到php的库目录下并更改php.ini实现加载（需要重启php-fpm）
- 赋予ice-writable 写权限 chmod -R a+w /var/www/SoohIce/ice-writable


## 配置文件变更

假设ICE总控中心部署在12.34.56.78上

- /var/www/licai_php/conf/globls.php 增加 ：

`$GLOBALS['CONF']['IceCenter4Evt']='SzcIceGrid/Locator:tcp -h 12.34.56.78 -p 4061';`

- /var/www/SoohIce/ice-config/register.cfg 修改里面的路径

- /var/www/SoohIce/ice-config/node？？？.cfg 修改里面的路径以及总控中心的地址：

`Ice.Default.Locator=SzcIceGrid/Locator:tcp -h 12.34.56.78 -p 4061`

- /var/www/SoohIce/ice-config/xiaoxia.xml 中修改各个服务节点的监听ip(不是总控中心的)

`<server-instance   template = "XXEvtServerTpl" index = "101"  listenIP="-h 1.2.3.4"   />`

## 启动总控中心，更新应用配置

1）后台启动 

`icegridregistry --Ice.Config=/var/www/SoohIce/ice-config/registry.cfg & `

2）加载或更新应用配置

确认下面命令中配置文件node100.cfg中的总控中心的Ip正确后

` icegridadmin --Ice.Config=/var/www/SoohIce/ice-config/node100.cfg `

*启动后询问user，password的时候输入任意字符（目前没限制）*

**首次加载**

`>>> application add /var/www/SoohIce/ice-config/xiaoxia.xml`

**更新应用配置**

`>>> application update /var/www/SoohIce/ice-config/xiaoxia.xml`

**检查加载的应用列表**

`>>> application list`

## 启动节点

**配置文件**：

1. ice-config/node???.cfg中总控中心IP地址正确
2. ice-config/xiaoxia.xml 有相应的节点，节点id同上面配置文件名的id，并且节点中server的ip设置成内网地址
3. 确认 ice-writable/iceNodes/node？？？目录存在并有写权限
	

**后台启动**

`icegridnode --Ice.Config=/var/www/SoohIce/ice-config/node100.cfg &`

**停止方式**

1) 登入总控中心

` icegridadmin --Ice.Config=/var/www/SoohIce/ice-config/node100.cfg `

2) 执行命令停止

`node shutdown node???`

等该节点正在执行的任务都跑完后，相关进程就结束了，可以通过ps ax | grep ice来检查

## 其他

不一定要登入总控中心，可以通过下面的命令直接执行 

`icegridadmin -u p -p p  --Ice.Config=/var/www/SoohIce/ice-config/node100.cfg -e "application update /var/www/SoohIce/ice-config/xiaoxia.xml"`

`icegridadmin -u p -p p  --Ice.Config=/var/www/SoohIce/ice-config/node100.cfg -e "node shutdown node100"`

！！缺系统自动启动的脚本！！


