<?php


namespace app\service\work;


use think\Exception;

class BaseWork
{
    /**
     * 通过端口映射进行转发流量
     * Gate 网关层，维持用户连接 公网IP
     * Lan 内网work进程，局域网IP，接收register推送，并转发流量给对应机器
     * Register 服务层，公网IP channel组件用于维持 gate与 lan之间的关系
     * register channel事件
     *  注：由于gate与lan之间通过regiter channel组件的事件订阅通信
     *     所以业务代码中 gate仅与user进行通信，lan仅与局域网转发机器进行通信
     *  user_gate_connect 用户与gate建立连接,此时Lan应与内网目标机器之间建立连接
     *  user_gate_message 用户发送消息给message,此时Lan应将message发送给内网目标机器
     *  user_gate_close 用户与gate之间关闭连接
     *
     *  lan_target_connect lan与目标机器之间建立连接
     *  target_lan_message 内网目标发送信息给lan，此时gate应将message转发给用户
     *  target_lan_close 内网目标关闭了lan的连接，此时gate应主动关闭用户连接
     *
     *
     *
     */
    //gate网关地址，维持与用户的长连接
    protected string $gate_bind;

    //channel组件创建一个register进程，维护gate 与 client的长连接
    protected string $register_bind;
    protected string $register_gate_ip;
    protected string $register_lan_ip;
    protected int $register_port = 9601;

    protected array $gate_port;
    protected array $lan_port;
    protected array $lan_ip;


    /**
     * 内网穿透成功后的端口流量转发
     * 服务端启动时 会检索CLIENT_lan 绑定对应register_port
     * windows server端仅支持一个
     */
    protected bool $is_win = true;

    protected array $lan_map;

    /**
     * @throws Exception
     */
    public function __construct(){

        $this->gate_bind = env('work.gate_bind');
        $this->register_bind = env('work.register_bind');
        $this->register_gate_ip = env('work.register_gate_ip');
        $this->register_lan_ip = env('work.register_lan_ip');
        $this->register_port = env('work.register_port');
        $this->gate_port = env('work.gate_port');
        $this->lan_port = env('work.lan_port');
        $this->lan_ip = env('work.lan_ip');

        if (!$this->gate_bind
            || !$this->register_bind
            || !$this->register_port
            || !$this->gate_port
            || !$this->lan_port
            || !$this->lan_ip
        ){
            throw new Exception('配置参数错误 '.var_export($this));
        }

        $this->lanInit();

        if (strpos(PHP_OS,'WIN') === false){
            $this->is_win = false;
        }
    }

    /**
     * @throws Exception
     */
    protected function lanInit(){
        $lan_map = [];

        foreach ($this->gate_port as $k=>$v){
            if (!preg_match("/^\d*|-$/",$v)){
                throw new Exception('配置格式错误 gate_port '.var_export($v));
            }
            $gate_ports = explode('-',$v);
            if (!isset($this->lan_port[$k])
                || !preg_match("/^\d*|-$/",$this->lan_port[$k])
            ){
                throw new Exception('配置格式错误 lan_port '.var_export($this->lan_port[$k]));
            }
            $lan_ports = explode('-',$this->lan_port[$k]);
            $lan_ip = $this->lan_ip[$k];
            if (count($gate_ports)>2 || $gate_ports<0){
                throw new Exception('配置格式错误 gate_port '.$v);
            }
            if (count($lan_ports)>2 || $lan_ports<0){
                throw new Exception('配置格式错误 lan_ports '.$this->lan_port[$k]);
            }
            if (!$lan_ip){
                throw new Exception('配置格式错误 lan_ip '.$lan_ip);
            }


            if (count($gate_ports) === 1){
                $lan_map[intval($gate_ports[0])] = [
                    'lan_port'=>intval($lan_ports[0]),
                    'lan_ip'=>$this->lan_ip[$k],
                ];
            }else{
                $middle = $gate_ports[1]-$gate_ports[0];
                if ($middle<0){
                    throw new Exception('配置格式错误 gate_ports '.$v);
                }
                for ($i=0;$i<=$middle;$i++){
                    $lan_map[$gate_ports[0]+$i] = [
                        'lan_port'=>intval($lan_ports[0])+$i,
                        'lan_ip'=>$lan_ip
                    ];
                }
            }
        }
        $this->lan_map = $lan_map;
    }
}