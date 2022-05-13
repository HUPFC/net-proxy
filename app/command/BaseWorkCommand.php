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

class BaseWorkCommand extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('convert')
            ->addArgument('action', Argument::OPTIONAL, "start|stop|restart|reload|status|connections", 'start')
            ->addOption('mode', 'm', Option::VALUE_OPTIONAL, 'Run the workerman server in daemon mode.')
            ->setDescription('the workerman command');
    }

    protected function execute(Input $input, Output $output)
    {
        // 指令输出
        $output->writeln('convert start');

        $action = $input->getArgument('action');
        $mode = $input->getOption('mode');


        // 重新构造命令行参数,以便兼容workerman的命令
        global $argv;

        $argv = [];

        array_unshift($argv, 'think', $action);
        if ($mode == 'd') {
            $argv[] = '-d';
        } else if ($mode == 'g') {
            $argv[] = '-g';
        }

        // 在这里放心的实例化worker,
        // 就像参照workerman文档写一样,
        // 无非在workerman的文档里,代码是新建纯php文件,但在这里,写到了一个方法里.
    }
}
