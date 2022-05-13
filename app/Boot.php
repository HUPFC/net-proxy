<?php


namespace app;


use app\common\config\ClientConfig;
use think\App;
use think\Exception;
use think\facade\Config;

class Boot extends BaseController
{
    /**
     * @throws \Exception
     */
    public function __construct(App $app)
    {

        parent::__construct($app);
        error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED & ~E_WARNING);

        if(env('app_env') === 'prod'){
            ini_set('display_errors',0);
            ini_set('log_errors',1);
            ini_set('error_log','/logs/pdf.com/error.log');
        }else{
            ini_set('display_errors',1);
            ini_set('log_errors',0);
        }

        define('DEBUG',$this->request->isDebug());
        define('ENV',env('app_env','prod'));
    }
}