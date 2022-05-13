#内网穿透 net-proxy

####项目介绍与使用
1. 使用thinkphp6+workerman+workerman/channel实现端口映射,流量转发
2. 配置文件 .env (.env.example为样例文件)
3. php > 7.2 (其他环境要求参考workerman手册 https://www.workerman.net/doc/workerman/install/requirement.html)
4. workman/channel组件因无密码校验,并且本项目未使用ssl,请勿直接在生产环境使用
5. 服务端、客户端均支持windows、linux
6. 服务端windows启动时,因windows安全限制,仅支持单接口监听（可使用.bat启动多个即可）
7. 仅支持单客户端运行，多客户端需改造channel事件


```
#服务端启动 (linux)
php think gate start --mode d
#服务端启动 (win 需要启动两个进程，仅支持单接口监听，可使用.bat启动多个)
php think gate start --mode d
php think register start --mode d
客户端启动
php think lan start --mode d

console
php think gate|register|lan

关闭
php think gate|register|lan stop

查看状态
php think gate|register|lan status
```

####配置文件
```
[WORK]
GATE_BIND=0.0.0.0  #服务端绑定地址，与用户进行连接
REGISTER_BIND=0.0.0.0   #channel注册地址，负责gate与lan之间信息转发
REGISTER_GATE_IP=192.168.0.2  #gate连接channel的ip
REGISTER_LAN_IP=192.168.0.2   #lan连接channel的ip
REGISTER_PORT=9601   #channel绑定端口

#以下可配置多组 gate与lan必须一一对应
GATE_PORT[]=9501-9502   #服务端监听端口
LAN_PORT[]=3389-3390    #内网对应的转发端口
LAN_IP[]=192.168.0.109  #内网转发IP地址

GATE_PORT[]=9503        
LAN_PORT[]=3389
LAN_IP[]=192.168.0.110
```

#### 协议
MIT