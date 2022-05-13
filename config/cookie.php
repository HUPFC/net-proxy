<?php
// +----------------------------------------------------------------------
// | Cookie设置
// +----------------------------------------------------------------------

return [
    // cookie 保存时间 3天
    // 客户端使用token 更新cookie时间
    'expire'    => 259200,
    // cookie 保存路径
    'path'      => '/',
    // cookie 有效域名
    'domain'    => app()->request->rootDomain(),
    //  cookie 启用安全传输
    'secure'    => false,
    // httponly设置
    'httponly'  => true,
    // 是否使用 setcookie
    'setcookie' => true,
    // samesite 设置，支持 'strict' 'lax'
    'samesite'  => '',
];
