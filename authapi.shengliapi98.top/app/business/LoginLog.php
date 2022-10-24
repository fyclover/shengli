<?php


namespace app\business;


class LoginLog
{
    //登陆日志
    public function login($type=1)
    {
        $log['unique']=session('home_user.id') ? session('home_user.id') : session('admin_user.id');
        $log['login_type']=$type;
        $log['login_time']=date('Y-m-d H:i:s');
        $log['login_ip']=isset($_SERVER['HTTP_ALI_CDN_REAL_IP']) ? $_SERVER['HTTP_ALI_CDN_REAL_IP'] : $_SERVER['REMOTE_ADDR'];
        $log['login_equipment']= isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : json_encode($_SERVER);
        (new \app\model\LoginLog())->save($log);
    }
}