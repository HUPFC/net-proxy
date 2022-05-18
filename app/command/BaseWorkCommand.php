<?php


namespace app\command;


use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use Workerman\Worker;

class BaseWorkCommand extends Command
{
    protected string $name;
    protected string $desc = 'the workerman command';


    protected function configure()
    {
        // 指令配置
        $this->setName($this->name)
            ->addArgument('action', Argument::OPTIONAL, "start|stop|restart|reload|status|connections", 'start')
            ->addOption('mode', 'm', Option::VALUE_OPTIONAL, 'Run the workerman server in daemon mode.')
            ->setDescription($this->desc);
    }

    protected function execute(Input $input, Output $output)
    {
        // 指令输出
        $output->writeln($this->name.' start');

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

        Worker::$stdoutFile = $this->app->getRuntimePath().'/worker/'.$this->name.'.log';
        Worker::$pidFile = $this->app->getRuntimePath().$this->name.'.pid';
        Worker::$processTitle .= ' '.$this->name;
    }
}