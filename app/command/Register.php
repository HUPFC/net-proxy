<?php
declare (strict_types = 1);

namespace app\command;

use app\config\WorkConfig;
use app\service\work\RegisterWork;
use app\service\work\GateWork;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use Workerman\Worker;

class Register extends BaseWorkCommand
{
    protected function execute(Input $input, Output $output)
    {
        parent::execute($input,$output);
        Worker::$stdoutFile = $this->app->getRuntimePath().'work/register.log';
        (new RegisterWork())->start();
        Worker::runAll();
    }
}
