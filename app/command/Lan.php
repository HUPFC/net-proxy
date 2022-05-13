<?php
declare (strict_types = 1);

namespace app\command;

use app\service\work\LanWork;
use think\console\Input;
use think\console\Output;
use Workerman\Worker;

class Lan extends BaseWorkCommand
{

    /**
     * @throws \Exception
     */
    protected function execute(Input $input, Output $output)
    {
        parent::execute($input,$output);
        Worker::$stdoutFile = $this->app->getRuntimePath().'work/lan.log';
        (new LanWork())->start();
        Worker::runAll();
    }
}
