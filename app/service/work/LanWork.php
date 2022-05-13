<?php


namespace app\service\work;


use app\service\log\StdLog;
use Channel\Client;
use think\Exception;
use Workerman\Connection\AsyncTcpConnection;
use Workerman\Worker;

class LanWork extends BaseWork
{
    /**
     * @throws \Exception
     */
    public function start(){
        $work = new Worker();
        $work->onWorkerStart = function(Worker $worker){
            StdLog::self()->setWorker($worker);
            Client::connect($this->register_lan_ip,$this->register_port);

            Client::on('user_gate_connect',function($eventData) use ($worker){
                StdLog::self()->info('on user_gate_connect '.var_export($eventData,true));
                $config = $this->lan_map[$eventData['gate_port']];
                $target = 'tcp://'.$config['lan_ip'].':'.$config['lan_port'];

                //建立与内网目标机器之间的异步tcp连接
                $async = new AsyncTcpConnection($target);

                $async->onConnect = function(AsyncTcpConnection $conn) use ($eventData){
                    $lan_conn = [
                        'id'=>$eventData['id'],
                        'ip'=>$conn->getRemoteIp(),
                        'port'=>$conn->getRemotePort(),
                        'gate_port'=>$conn->getLocalPort(),
                    ];

                    Client::publish('lan_target_connect',$lan_conn);
                    StdLog::self()->info('publish lan_target_connect '.var_export($lan_conn,true));
                };

                $async->onMessage = function(AsyncTcpConnection $conn,$data) use ($eventData){
                    $lan_message = [
                        'id'=>$eventData['id'],
                        'ip'=>$conn->getRemoteIp(),
                        'port'=>$conn->getRemotePort(),
                        'lan_port'=>$conn->getLocalPort(),
                        'data'=>$data
                    ];

                    Client::publish('target_lan_message',$lan_message);
                    StdLog::self()->info('publish target_lan_message size:'.strlen($lan_message['data']));
                };

                $async->onClose = function(AsyncTcpConnection $conn) use ($eventData){
                    $lan_close = [
                        'id'=>$eventData['id'],
                        'ip'=>$conn->getRemoteIp(),
                        'port'=>$conn->getRemotePort(),
                        'lan_port'=>$conn->getLocalPort(),
                    ];

                    Client::publish('target_lan_close',$lan_close);
                    StdLog::self()->info('publish target_lan_close:'.var_export($lan_close,true));
                };

                $async->connect();
                $worker->connections[$eventData['id']] = $async;
            });

            //转发用户流量
            Client::on('user_gate_message',function($eventData) use ($worker){
                if (!isset($worker->connections[$eventData['id']])){
                    StdLog::self()->error('on user_gate_message fail,lan tcp has closed ');
                }else{
                    $worker->connections[$eventData['id']]->send($eventData['data']);
                    StdLog::self()->info('on user_gate_message size:'.strlen($eventData['data']));
                }

            });
            Client::on('user_gate_close',function($eventData) use ($worker){
                try{
                    if (!isset($worker->connections[$eventData['id']])){
                        StdLog::self()->error('on user_gate_close fail,lan tcp has closed ');
                    }else{
                        $worker->connections[$eventData['id']]->close();
                        StdLog::self()->info('on user_gate_close '.var_export($eventData,true));
                    }
                }catch (\Exception $e){
                    StdLog::self()->error('on user_gate_close error '.var_export($e->getMessage(),true));
                }
            });

        };


    }
}