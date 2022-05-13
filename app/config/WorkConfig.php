<?php


namespace app\config;


class WorkConfig
{
    CONST SERVER_IP = '0.0.0.0';
    CONST SERVER_USER = 'QH';
    CONST SERVER_PASSWORD = '78Q9EQ41321DSD';//server端密码

    CONST CHANNEL_IP = '0.0.0.0';//work/channel组件
    CONST CHANNEL_PORT = 9601;
    /**
     * 内网穿透成功后的端口流量转发
     * 服务端启动时 会检索CLIENT_REPEAT 绑定对应server_port
     * windows server端仅支持一个
     */
    CONST CLIENT_REPEAT = [
        [
            'server_port'=>'9501-9502',
            'repeat_port'=>'3389-3390',//内网穿透后的转发port
            'repeat_ip'=>'192.168.0.109',//内网穿透后的转发ip
        ],
        [
            'server_port'=>'9503',
            'repeat_port'=>'3389',//内网穿透后的转发port
            'repeat_ip'=>'192.168.0.115',//内网穿透后的转发ip
        ],
    ];
}