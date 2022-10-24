<?php
declare (strict_types=1);

namespace app\middleware;


use app\model\AdminLog;
use app\traits\PublicResponseTrait;

class Before
{
    use PublicResponseTrait;

    /**
     * config('ToConfig.action_log')
     * 某方法执行成功之后写入操作日志
     * @param $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {

        //写操作日志
        $path_info = $request->pathinfo();
        $action = $request->action();
        $config = config('ToConfig.action_log');
        //获取时间限制
         if ($path_info == 'luzhu/retreat') {
            $get_config = get_config('get_lu_zhu_operation');
            if (empty($get_config))  $this->failed('露珠操作配置不存在，请配置');
            $get_array = explode('-',$get_config->value);
            $start = strtotime(date('Y-m-d').' '.$get_array[0]);
            $end = strtotime(date('Y-m-d').' '.$get_array[1]);
            if (time() < $start || time()>$end){
                 $this->failed('不在操作时间范围');
            }
            //在操作时间范围
            $pwd = $request->post('pwd',0);
            $env_pwd = env('config_pwd.lu_zhu_pwd',123456);
            if ($pwd != $env_pwd)  $this->failed('密码错误');
        }

        if (empty($action) && !isset($action) || !in_array($action, $config)) return $next($request);
        $ip =  isset($_SERVER['HTTP_ALI_CDN_REAL_IP']) ? $_SERVER['HTTP_ALI_CDN_REAL_IP'] :$_SERVER['REMOTE_ADDR'];
        //操作日志插入
        $save['system'] = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        $save['browser'] = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        $save['ip'] = $ip;
        $save['admin_uid'] = session('admin_user.id');
        $save['create_time'] = date('Y-m-d H:i:s');
        $save['action'] = $path_info;
        (new AdminLog())->save($save);
        return $next($request);
    }
}
