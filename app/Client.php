<?php
/**
 * Created by PhpStorm.
 * User: jiangheng
 * Date: 19-6-4
 * Time: 上午9:37
 */

namespace App;

use EasySwoole\Rpc\Config;
use EasySwoole\Rpc\Response;
use EasySwoole\Rpc\Rpc;
use EasySwoole\Rpc\Task\TaskObject;

class Client
{
    /**
     * 选择向哪个服务发起请求
     *
     * @var string
     */
    public $node_service_name = 'node_chezhu';

    public function send($msg = 'hello world', $func = 'func1')
    {
        $config = new Config();
        $rpc = new Rpc($config);
        $nodelist = $config->getNodeManager()->allServiceNodes();
        echo "所有服务节点：" . PHP_EOL;
        print_r($nodelist);
        go(function () use($rpc, $msg, $func) {
           $client = $rpc->client();
           //选择要调用的服务
           $serviceClient = $client->selectService($this->node_service_name);
           $arg = [
               "func" => [TestProject::class, $func],
               "msg" => $msg
           ];
           $serviceClient->createTask()->setAction('call')->setArg($arg)
               ->setOnSuccess(function (Response $response) {
                   print_r($response->getMessage());
                   echo PHP_EOL;
               })->setOnFail(function (Response $response, TaskObject $taskObject) {
                   echo "fail:" . $response->getStatus() . PHP_EOL;
               });
           $client->exec(0.5);
        });
    }
}