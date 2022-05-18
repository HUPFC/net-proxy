<?php
declare (strict_types = 1);

namespace app\command;

use app\service\work\GateWork;
use think\console\Input;
use think\console\Output;
use Workerman\Worker;

class Gate extends BaseWorkCommand
{
    protected string $name = 'net-proxy gate';
    protected string $desc = 'the gate command';
    protected function execute(Input $input, Output $output)
    {
        parent::execute($input,$output);
        (new GateWork())->start();
        Worker::runAll();
    }
}
