<?php

function worker_tcp($uid, $mag = '', array $data=[], $code = 202)
{
    $client = stream_socket_client(env('worker.two_tcp', 'websocket://0.0.0.0:3002'), $errno, $errmsg, 1);
    // 推送的数据，包含uid字段，表示是给这个uid推送
    $data = array('code' => $code, 'user_id' => $uid, 'data' => $data, 'msg'=>$mag);
    // 发送数据，注意5678端口是Text协议的端口，Text协议需要在数据末尾加上换行符
    fwrite($client, json_encode($data) . "\n");
    // 读取推送结果
    //echo fread($client, 8192);
}

function redis()
{
    return think\facade\Cache::store('redis');
}

//生成台座局号
function bureau_number($table_id,$xue_number = false){
    $xue = xue_number($table_id);
    $table_bureau_number =  date('YmdH').$table_id.$xue['xue_number'].$xue['pu_number'];
    if ($xue_number) return ['bureau_number'=>$table_bureau_number,'xue'=>$xue];
    return $table_bureau_number;
}

//$table_id 台桌ID
function xue_number($table_id)
{
	$nowTime = time();
	$startTime = strtotime(date("Y-m-d 09:00:00", time()));
	// 如果小于，则算前一天的
	if ($nowTime < $startTime) {
	    $startTime = $startTime - (24 * 60 * 60);
	} else {
	    // 保持不变 这样做到 自动更新 露珠
	}
    //取才创建时间最后一条数据
    $find = \app\model\Luzhu::where('table_id', $table_id)->where('status',1)->whereTime('create_time','>=', date('Y-m-d H:i:s',$startTime))->order('id desc')->find();
    if (empty($find)) return ['xue_number' => 1, 'pu_number' => 1];
    $xue = $find->xue_number;
    if ($find->result == 0){
        $pu = $find->pu_number;
    }else{
        $pu = $find->pu_number + 1;
    }
    return ['xue_number' => $xue, 'pu_number' => $pu];
}

function show($data = [], int $code = 200, string $message = 'ok！', int $httpStatus = 0)
{
    $result = [
        'code' => $code,
        'message' => lang($message),
        'data' => $data,
    ];
    header('Access-Control-Allow-Origin:*');
    if ($httpStatus != 0) {
        return json($result, $httpStatus);
    }
    echo json_encode($result);
    exit();
}

function get_config($name = null)
{
    if ($name == null) {
        return \app\model\SysConfig::select();
    }
    return \app\model\SysConfig::where('name', $name)->find();
}