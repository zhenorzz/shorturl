<?php
$redis = new Redis();
$link = $redis->connect('127.0.0.1', 6379);
if (!$link) {
    echo json_encode(['error' => 1, 'msg' => 'redis connected failed']);
    exit();
}
$result = $redis->auth('UclbrtHongWei1qaz!QAZ');
if (!$result) {
    echo json_encode(['error' => 2, 'msg' => 'redis auth failed']);
    exit();
}

$result = $redis->select(9);
if (!$result) {
    echo json_encode(['error' => 3, 'msg' => 'select db failed']);
    exit();
}
$flag = explode('/', $_SERVER['REQUEST_URI'])[1];
$result = $redis->get($flag);
if ($result) {
    header("Location:".$result, true, 301);
    exit();
} else {
    echo json_encode(['error' => 6, 'msg' => 'url not found']);
    exit();
}