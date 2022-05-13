<?php


namespace app\service\work;


use Channel\Server;

class RegisterWork extends BaseWork
{
    public function start(){
        $work = new Server($this->register_bind,$this->register_port);

    }
}