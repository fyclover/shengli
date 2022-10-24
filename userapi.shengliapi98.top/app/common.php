<?php

function redis()
{
    return think\facade\Cache::store('redis');
}

//code 1失败
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

function home_api_token($id)
{
    return md5($id . 'home' . date('Y-m-d H:i:s', time()) . 'token') . randomkey(mt_rand(10, 30));
}

//地址掩码 20—40位
function randomkey($length)
{
    $pattern = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLOMNOPQRSTUVWXYZ';
    $key = '';
    for ($i = 0; $i < $length; $i++) {
        $key .= $pattern{mt_rand(0, 35)}; //生成php随机数
    }
    return $key;
}

function pwdEncryption($pwd)
{
    if (empty($pwd))
        return false;
    return base64_encode($pwd);
}

//生成用户账号 10 - 30
function userkey($length)
{
    $pattern = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLOMNOPQRSTUVWXYZ';
    $key = '';
    for ($i = 0; $i < $length; $i++) {
        $key .= $pattern{mt_rand(0, 35)}; //生成php随机数
    }
    return 'user' . $key . date('Ymd');
}

//用户提现默认密码
function home_tx_pwd()
{
    return 'aa123456';
}

function get_config($name = null)
{
    if ($name == null) {
        return \app\model\SysConfig::select();
    }
    return \app\model\SysConfig::where('name', $name)->find();
}

//生成台座局号
function bureau_number($table_id,$xue_number = false){
    $xue = xue_number($table_id);
    $table_bureau_number =  date('YmdH').$table_id.$xue['xue_number'].$xue['pu_number'];
    if ($xue_number) return ['bureau_number'=>$table_bureau_number,'xue'=>$xue];
    return $table_bureau_number;
}

function xue_number($table_id)
{
    // 缺少时间
    $nowTime = time();
    $startTime = strtotime(date("Y-m-d 04:00:00", time()));
    // 如果小于，则算前一天的
    if ($nowTime < $startTime) {
        $startTime = $startTime - (24 * 60 * 60);
    } else {
        // 保持不变 这样做到 自动更新 露珠
    }
    //取才创建时间最后一条数据
    $find = \app\model\Luzhu::where('table_id', $table_id)->where('status',1)->whereTime('create_time', 'today')->order('id desc')->find();
    if (empty($find)) return ['xue_number' => 1, 'pu_number' => 1];
    $xue = $find->xue_number;
    if ($find->result == 0){
        $pu = $find->pu_number;
    }else{
        $pu = $find->pu_number + 1;
    }
    return ['xue_number' => $xue, 'pu_number' => $pu];
}

/**
 * @param $data ['time','sign']
 */
function sign_auth($data,$string = 'tyh'){
   $sign =  md5($data['time'].$string);
    if ($sign != $data['sign']){
        show([], config('ToConfig.http_code.error'), 'sign验证失败');
    }
    return true;
}