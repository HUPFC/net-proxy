<?php


namespace app\service\log;

use Workerman\Worker;

class StdLog
{
    /**
     * @var Worker|null $worker
     */
    protected ?Worker $worker = null;

    /**
     * @var self|object
     */
    protected static $self;
    public static function self(): self
    {
        if (self::$self && self::$self instanceof self) {
            return self::$self;
        }
        self::$self = new self();
        return self::$self;
    }

    public function info($string){
        $this->record($string,'info');
    }

    public function error($string){
        $this->record($string,'error');
    }

    public function record($string,$level='info'){
        if (is_array($string)){
            $string = var_export($string);
        }

        $name = 'async';
        $id = 0;
        if ($this->worker instanceof Worker){
            $name =$this->worker->name;
            $id = $this->worker->id;
        }
        $time = date('Y-m-d H:i:s');
        if (app()->isDebug()){
            echo "[$time][$level][$name-$id] ".$string."\n";
        }
    }

    /**
     * @return Worker
     */
    public function getWorker(): Worker
    {
        return $this->worker;
    }

    /**
     * @param Worker $worker
     * @return StdLog
     */
    public function setWorker(Worker $worker): StdLog
    {
        $this->worker = $worker;
        return $this;
    }


}