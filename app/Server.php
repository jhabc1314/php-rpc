<?php
/**
 * Created by PhpStorm.
 * User: jackdou
 * Date: 19-6-4
 * Time: 上午9:36
 */

namespace App;

class Server
{
    /**
     * @var RpcClass
     */
    public $class;
    /**
     * RPC 启动的服务前缀
     * 使用 ps aux | grep CheZhu 可以查看启动的所有进程
     *
     * @var string
     */
    public $service_name = 'CheZhu';

    /**
     * easyswoole 需要的服务名称，客户端会根据此名称查找对应的服务，不同的服务需要名字不一样
     *
     * @var string
     */
    public $node_service_name = 'node_chezhu';

    /**
     * 无主化服务管理广播的端口 每隔一段时间自动心跳检测，向这个地址广播自己的存在
     * 如果有多个服务在一台机器上则只需要一个ip，如果在不同的机器上，则把每个机器的ip端口都加进来
     * 这个建议维护成一个配置中心，方便随时添加和下线，不用每次改代码
     *
     * @var array
     */
    public $broadcast_address = ['127.0.0.1:9600'];

    /**
     * 服务管理监听端口 通过此端口获取服务的心跳广播
     *
     * @var string
     */
    public $listen_address = '127.0.0.1:9600';


    public function __construct(RpcClass $class)
    {
        $this->class = $class;
    }

    public function start()
    {
        $config = $this->class->getConfig($this->node_service_name, $this->broadcast_address, $this->listen_address);
        $rpc = $this->class->getRpc($config);
        $swoole_process = $this->class->getAutoFindProcess($rpc, $this->service_name . "_autofind");
        $tcp = $this->class->getTcp($swoole_process);
        $this->class->setProcessName($this->service_name);
        $this->class->run($rpc, $tcp);
    }

    public function stop()
    {
        if (($master_pid = file_get_contents(APP_PATH . "../runtime/server.pid")) != '') {
            //$master_pid = explode(',', $con)[0];
            //$manager_pid = explode(',', $con)[1];
            posix_kill($master_pid, SIGTERM);
            //posix_kill($manager_pid, SIGTERM);
        } else {
            echo "server is not running" . PHP_EOL;
        }
    }

    public function reload()
    {
        if (($master_pid = file_get_contents(APP_PATH . "../runtime/server.pid")) != '') {
            //$master_pid = explode(',', $con)[0];
            //$manager_pid = explode(',', $con)[1];
            posix_kill($master_pid, SIGUSR1);
            //posix_kill($manager_pid, SIGUSR1);
        } else {
            echo "server is not running" . PHP_EOL;
        }
    }
}

