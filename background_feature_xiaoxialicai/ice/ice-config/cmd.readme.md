/opt/Ice-3.6.1/bin/icegridregistry --Ice.Config=registry.cfg & 
/opt/Ice-3.6.1/bin/icegridnode --Ice.Config=node1.cfg & 
/opt/Ice-3.6.1/bin/icegridnode --Ice.Config=node2.cfg &

/opt/Ice-3.6.1/bin/icegridadmin --Ice.Config=node1.cfg 
因为resitry里没设置帐号，所以随便用哪个节点的配置登入，随意输入用户密码即可

增加app的配置（注意不要重复添加，好像会有问题）
 >>>application add app.xml

确认应用已经被成功部署：
>>>application list
PrinterApplication

查看对象适配器的当前端点信息：
>>>adapter endpoints PrinterServer1.PrinterAdapter
dummy -t:tcp -h 192.168.1.193 -p 1933
（如果节点中的服务还没有被激活，则显示）


添加部署的另一个写法： 
icegridadmin –Ice.Config=config.grid -e “application add app_rep.xml” 

若要重新部署，执行：icegridadmin –Ice.Config=config.grid -e “application update app_rep.xml”