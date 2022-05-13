<?php


namespace app\service\work;


use app\service\log\StdLog;
use Channel\Client;
use Workerman\Connection\TcpConnection;
use Workerman\Worker;

class GateWork extends BaseWork
{
    public function start(){
        if (!$this->is_win){
            (new RegisterWork())->start();
        }
        foreach ($this->lan_map as $gate_port=>$item){
            $this->build($gate_port);
            if ($this->is_win){
                //windows仅启动一个进程
                break;
            }
        }
    }

    //注：此处只有用户连接gate进程，client与server是通过channel通信的
    public function build($gate_port){
        $server =  'tcp://'.$this->gate_bind.':'.$gate_port;
        $work = new Worker($server);

        $work->onWorkerStart = function(Worker $worker){
            StdLog::self()->setWorker($worker);
            Client::connect($this->register_gate_ip,$this->register_port);

            Client::on('target_lan_message',function($eventData) use ($worker){
                try{
                    if (!isset($worker->connections[$eventData['id']])){
                        StdLog::self()->error('on target_lan_message fail: conn lose');
                    }else{
                        $worker->connections[$eventData['id']]->send($eventData['data']);
                        StdLog::self()->info('on target_lan_message:'.strlen($eventData['data']));
                    }
                }catch (\Exception $e){
                    StdLog::self()->error('on target_lan_message error:'.$e->getMessage().$e->getTraceAsString());
                }

            });

            Client::on('target_lan_close',function($eventData) use ($worker){

                try{
                    if (!isset($worker->connections[$eventData['id']])){
                        StdLog::self()->error('on lan_target_connect fail: conn lose');
                    }else{
                        $worker->connections[$eventData['id']]->close();
                        StdLog::self()->info('on lan_target_connect:'.var_export($eventData));
                    }
                }catch (\Exception $e){
                    StdLog::self()->error('on lan_target_connect error:'.$e->getMessage().$e->getTraceAsString());
                }

            });


            Client::on('lan_target_connect',function($eventData){
                StdLog::self()->info('on lan_target_connect:'.var_export($eventData));
            });

        };

        $work->onConnect = function(TcpConnection $conn){
            $user_conn = [
                'id'=>$conn->id,
                'ip'=>$conn->getRemoteIp(),
                'port'=>$conn->getRemotePort(),
                'gate_port'=>$conn->getLocalPort(),
            ];

            Client::publish('user_gate_connect',$user_conn);
            StdLog::self()->info('publish user_gate_connect:'.var_export($user_conn));
        };


        $work->onMessage = function(TcpConnection $conn,$data){
            $user_message = [
                'id'=>$conn->id,
                'ip'=>$conn->getRemoteIp(),
                'port'=>$conn->getRemotePort(),
                'gate_port'=>$conn->getLocalPort(),
                'data'=>$data,
            ];
            Client::publish('user_gate_message',$user_message);
            StdLog::self()->info('publish user_gate_message size:'.strlen($user_message['data']));
        };

        $work->onClose = function(TcpConnection $conn){
            $user_close = [
                'id'=>$conn->id,
                'ip'=>$conn->getRemoteIp(),
                'port'=>$conn->getRemotePort(),
                'gate_port'=>$conn->getLocalPort(),
            ];
            Client::publish('user_gate_close',$user_close);
            StdLog::self()->info('publish user_gate_close:'.var_export($user_close));
        };
    }



}