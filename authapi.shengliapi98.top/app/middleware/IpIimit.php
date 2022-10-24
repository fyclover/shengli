<?php
declare (strict_types=1);

namespace app\middleware;


use app\model\IpConfig;
use app\traits\PublicResponseTrait;

class IpIimit
{
    use PublicResponseTrait;

    //验证IP是否可登陆
    public function handle($request, \Closure $next)
    {
        $ip =  isset($_SERVER['HTTP_ALI_CDN_REAL_IP']) ? $_SERVER['HTTP_ALI_CDN_REAL_IP'] :$_SERVER['REMOTE_ADDR'];
        if (empty($ip))   $this->failed('ip不存在');
        //查询IP是否存在
        $res = (new IpConfig())->where(['ip'=>$ip,'status'=>1])->find();
        if (empty($res))   $this->failed('ip限制登录');
        return $next($request);
    }
}
