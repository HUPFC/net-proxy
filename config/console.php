<?php
// +----------------------------------------------------------------------
// | 控制台配置
// +----------------------------------------------------------------------
use app\command\Gate;
use app\command\Lan;
use app\command\Register;

return [
    // 指令定义
    'commands' => [
        'gate'=> Gate::class,
        'register'=> Register::class,
        'lan'=>Lan::class
    ],
];
