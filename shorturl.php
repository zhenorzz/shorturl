<?php

function shorturl($url){
	$url = crc32($url);
	$result = sprintf("%u",$url);
	$show='';
	while($result > 0){
		$s = $result % 62;
		if ($s > 35){
			$s = chr($s + 61);
		} elseif ($s > 9 && $s <= 35){
			$s = chr($s + 55);
		}
		$show .= $s;
		$result = floor($result / 62);
	}
	return $show;
}
$redis = new Redis();
$link = $redis->connect('127.0.0.1', 6379);
if (!$link) {
	echo json_encode(['short_url' => 'failed' , 'error' => 1, 'msg' => 'redis connected failed']);
	exit();
}
$result = $redis->auth('UclbrtHongWei1qaz!QAZ');
if (!$result) {
	echo json_encode(['short_url' => 'failed' , 'error' => 2, 'msg' => 'redis auth failed']);
	exit();
}
$result = $redis->select(9);
if (!$result) {
	echo json_encode(['short_url' => 'failed' , 'error' => 3, 'msg' => 'select db failed']);
	exit();
}
$json = file_get_contents("php://input");
$json = json_decode($json, true);
if ($json['flag'] != 'uclbrt') {
	echo json_encode(['short_url' => 'failed' , 'error' => 4, 'msg' => 'auth failed']);
	exit();
}
$url = $json['url'];
$short = shorturl($url);
$result = $redis->set($short,$url);
if (!$result) {
	echo json_encode(['short_url' => 'failed' , 'error' => 5, 'msg' => 'set data failed']);
	exit();
}
echo json_encode(['short_url' => 'http://' . $_SERVER['HTTP_HOST'] . '/' .$short , 'error' => 0, 'msg' => 'success']);
exit();