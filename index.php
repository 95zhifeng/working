<?php 

use \Workerman\Worker;
use \Workerman\Lib\Timer;
require_once __DIR__ . '/Workerman/Autoloader.php';

$ws = new Worker("websocket://0.0.0.0:2000");

//已连接客户端 ，便于统计在线用户
$ws->hasConnections = array();
//监听客户端发来消息
$ws->onMessage = function ($connection, $message) use ($ws) {
//握手成功
//解码客户端连接发来的消息

$data = json_decode($message,true);
//将已连接客户端存入变量中
$ws->hasConnections[$connection->id] = array('name' => $data['name'], 'id' => $connection->id);
//拼装返回的数据结构
$back_data = array(
    'content' => $data['content'], 
    'nick' => '<b style="color:red">系统：</b>', 
    'client_id' => $connection->id, 
    'client_name' => $data['name'],
    'type' => 'login',
    'clients' => $ws->hasConnections, 
    'time' => date('Y-m-d H:i')
    );
//向所有在线用户广播消息
foreach ($ws->connections as $connection)
{
    $connection->send(json_encode($back_data));

}

};

// 设置连接的onClose回调
$ws->onClose = function($connection)
{
    echo "connection closed\n";
};


Worker::runAll();


 ?>