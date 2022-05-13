<?php
declare (strict_types = 1);

namespace app\command;

use app\service\work\GateWork;
use think\console\Input;
use think\console\Output;
use Workerman\Worker;

class Gate extends BaseWorkCommand
{
    protected function execute(Input $input, Output $output)
    {
        parent::execute($input,$output);
        Worker::$stdoutFile = $this->app->getRuntimePath().'work/gate.log';

        (new GateWork())->start();


        Worker::runAll();
    }
}
