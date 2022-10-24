<?php

namespace app\business;

class RequestUrl
{
    public static $CurlUserInfo = 'userapi.shengliapi98.top/user/curl/info';//用户是否在线接口地址，域名就是 useraspi的域名
    public static $HttpWorkerUser = 'websocket://0.0.0.0:21000';//用户在线ws

    public static function user_url():string
    {
        return '/user/user/index';
    }

}