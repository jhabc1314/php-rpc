<?php
/**
 * Created by PhpStorm.
 * User: jiangheng
 * Date: 19-6-4
 * Time: 上午9:37
 */

namespace App;

use EasySwoole\Rpc\Config;
use EasySwoole\Rpc\Request;
use EasySwoole\Rpc\Response;
use EasySwoole\Rpc\Rpc;
use Swoole\Process;

class RpcClass
{

    public $service_name;

    /**
     * @param $service_name
     * @param array $broadcast_address
     * @param string $listen_address
     *
     * @return Config
     */
    public function getConfig($service_name, array $broadcast_address, string $listen_address)
    {
        $config = new Config();
        $config->setServiceName($service_name);

        $config->getAutoFindConfig()->setAutoFindBroadcastAddress($broadcast_address);
        $config->getAutoFindConfig()->setAutoFindListenAddress($listen_address);
        return $config;
    }

    /**
     * @param Config $config
     *
     * @return Rpc
     */
    public function getRpc(Config $config)
    {
        $rpc = new Rpc($config);
        $rpc->registerAction('call', function (Request $request, Response $response) {
            $param = $request->getArg();
            $func = $param['func'];
            unset($param['func']);
            $result = call_user_func_array($func, $param);
            $response->setMessage($result);
        });
        return $rpc;
    }

    /**
     * @param Rpc $rpc
     * @param string $process_name
     *
     * @return \Swoole\Process
     */
    public function getAutoFindProcess(Rpc $rpc, $process_name = 'rpc_process')
    {
        $auto_find_process = $rpc->autoFindProcess($process_name);
        return $auto_find_process->getProcess();
    }

    /**
     * @param Process $process
     * @param $setting
     *
     * @return \Swoole\Server
     */
    public function getTcp(Process $process, $setting = [])
    {
        $host = isset($setting['host']) ? $setting['host'] : '127.0.0.1';
        $port = isset($setting['port']) ? $setting['port'] : '8821';
        unset($setting['host'], $setting['port']);

        $tcp = new \Swoole\Server($host, $port);
        //添加自定义监听广播进程用来服务下线
        $tcp->addProcess($process);

        $default_setting = [
            'log_file' => APP_PATH . "../runtime/swoole.log",
            'daemonize' => true,
            'pid_file' => APP_PATH . "../runtime/server.pid",
        ];
        //重定向php错误日志
        ini_set('error_log', APP_PATH . '../runtime/php_errors.log');

        $tcp->set(array_merge($default_setting, $setting));
        $tcp->on('Start', function (\Swoole\Server $server) {
            cli_set_process_title($this->service_name . "_master");
            echo "onStart..." . PHP_EOL;
        });
        $tcp->on('WorkerStart', function (\Swoole\Server $server, $work_id) {
            cli_set_process_title($this->service_name . "_worker");
            echo "onWorkerStart..." . PHP_EOL;
        });
        $tcp->on('ManagerStart', function (\Swoole\Server $server) {
            cli_set_process_title($this->service_name . "_manager");
            echo "onManagerStart..." . PHP_EOL;
        });
        return $tcp;
    }

    /**
     * @param Rpc $rpc
     * @param \Swoole\Server $server
     */
    public function run(Rpc $rpc, \Swoole\Server $server)
    {
        $rpc->attachToServer($server);
        $server->start();
    }

    public function setProcessName($service_name)
    {
        $this->service_name = $service_name;
    }
}