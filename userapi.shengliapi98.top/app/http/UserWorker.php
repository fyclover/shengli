<?php

use Workerman\Worker;
use \Workerman\Lib\Timer;

require_once __DIR__ . '/../../vendor/autoload.php';


// 初始化一个worker容器，监听1234端口
//,$context
$worker = new Worker(\app\business\RequestUrl::$HttpWorkerUser);

// ====这里进程数必须必须必须设置为1====
$worker->count = 1;
// 新增加一个属性，用来保存uid到connection的映射(uid是用户id或者客户端唯一标识)
$worker->uidConnections = array();
// 当有客户端发来消息时执行的回调函数
$worker->onMessage = function ($connection, $data) {
    global $worker;
    static $request_count;
    // 业务处理略
    if (++$request_count > 10000) {
        // 请求数达到10000后退出当前进程，主进程会自动重启一个新的进程
        Worker::stopAll();
    }
    $data = json_decode($data, true);

    if (!empty($data)) {
        $data = array_filter($data);
    }
    
    if (isset($data['user_id'])) {
        $connection->uid = $data['user_id'];
        $connection->data_info = $data;
        if (isset($data['user_id']) && isset($data['token'])) {
            $user_info = \app\business\Curl::post(\app\business\RequestUrl::$CurlUserInfo, ['token' => $data['token']]);
            return $connection->send(json_encode($user_info));
        }
    }
    return $connection->send('连接成功');
};


// 当有客户端连接断开时
$worker->onClose = function ($connection) {
    global $worker;
    if (isset($connection->uid)) {
        $connection->close();
        // 连接断开时删除映射
        unset($worker->uidConnections[$connection->uid]);
        echo "断开连接";
    }
};

// 向所有验证的用户推送数据
function broadcast($message)
{
    global $worker;
    foreach ($worker->uidConnections as $connection) {
        $connection->send($message);
    }
}

// 针对uid推送数据
function sendMessageByUid($uid, $message)
{
    global $worker;
    if (isset($worker->uidConnections[$uid])) {
        $connection = $worker->uidConnections[$uid];
        $connection->send($message);
    }
}
// 运行所有的worker（其实当前只定义了一个）
Worker::runAll();