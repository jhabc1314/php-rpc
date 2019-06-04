# php-rpc
基于 `easyswoole3` 的 `RPC` 服务 `demo`
## 体验条件
- `linux` 或者 `mac` 环境
- `php` > 7.1
- `swoole` > 4.3
## 使用
- git clone git@github.com:jhabc1314/php-rpc.git
- cd php-rpc
- composer install
- php rpc.php -h 查看使用帮助

## 命令
- php rpc.php server start 启动服务
- php rpc.php server stop 停止服务
- php rpc.php server reload 重新加载服务
- php rpc.php client send xxx 发送测试消息
