<?php
namespace think\cache;
use app\common\lib\cache\Redis;

/**
 * 仅用于全局注册容器代码提示
 * Class App
 * @method Redis hSet(string $key,string $field,mixed $value = [],integer $expire = null)
 * @method Redis hGet(string $key,string $field,mixed $default = null)
 * @method Redis hDelete(string $key,string $field)
 * @method Redis lPush(string $key,mixed $value = [])
 * @method Redis lPop(string $key,mixed $default=null)
 */
class Driver
{

}