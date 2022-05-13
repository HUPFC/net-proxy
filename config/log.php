<?php

// +----------------------------------------------------------------------
// | 日志设置
// +----------------------------------------------------------------------
return [
    // 默认日志记录通道
    'default'      => env('log.channel', 'file'),
    // 日志记录级别
    'level'        => [
        'debug',
        'info',
        'notice',
        'warning',
        'error',
        'critical',
        'alert',
        'emergency'
    ],
    // 日志类型记录的通道 ['error'=>'email',...]
    'type_channel' => [],
    // 关闭全局日志写入
    'close'        => false,
    // 全局日志处理 支持闭包
    'processor'    => null,

    // 日志通道列表
    'channels'     => [
        'file' => [
            // 日志记录方式
            'type'           => 'File',
            // 日志保存目录
            'path'           => env('APP_ENV','prod') !== 'prod'?app()->getRuntimePath().'log/':'/logs/pdf.com/',
            // 单文件日志写入
            'single'         => 'tp',
            // 独立日志级别
            'apart_level'    => [],
            // 最大日志文件数量
            'max_files'      => 30,
            // 日志输出格式化
            'format'         => '[%s][%s] %s',
            // 是否实时写入
            'realtime_write' => env('APP_ENV') !== 'prod',
            'time_format'   =>    'Y-m-d H:i:s',
            'file_size'   	=> 	100000000,
        ]
    ],

];
