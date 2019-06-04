<?php
/**
 * Created by PhpStorm.
 * User: jiangheng
 * Date: 19-6-4
 * Time: 上午9:39
 */

require_once __DIR__ . "/vendor/autoload.php";
const APP_PATH = __DIR__ . '/app/';

if ($argv[1] == '-h') {
    echo <<<STR
    php rpc.php server start  #启动rpc服务
    php rpc.php server stop   #停止rpc服务
    php rpc.php server reload #重新加载服务，不重启
    ---------------------------
    php rpc.php client send 消息内容 #客户端请求并获取结果
STR;
    echo PHP_EOL;
    exit;
}

switch ($argv[1]) {
    case 'server':
        $server = new App\Server(new App\RpcClass());
        if ($argv[2] == 'start') {
            $server->start();
        } elseif ($argv[2] == 'stop') {
            $server->stop();
        } elseif ($argv[2] == 'reload') {
            $server->reload();
        } else {
            exit("参数错误:" . $argv[2]);
        }
        break;
    case 'client':
        $client = new App\Client();
        if ($argv[2] == 'send') {
            $client->send(isset($argv[3]) ? $argv[3] : 'hello RPC !');
        } else {
            exit("参数错误:" . $argv[2]);
        }
        break;
    default:
        exit("参数错误" . $argv[1]);
}