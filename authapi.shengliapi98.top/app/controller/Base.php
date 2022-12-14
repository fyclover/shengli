<?php

namespace app\controller;

use app\BaseController;
use app\middleware\Auth;
use app\middleware\Before;
use app\middleware\Role;

class Base extends BaseController
{
    protected static $user;
    protected $middleware = [Auth::class,Role::class,Before::class,];//验证token和访问控制器权利

    protected function initialize()
    {
        $this->admin_user_info();
        parent::initialize(); // TODO: Change the autogenerated stub
    }

    /**
     * 用户登录信息 $user
     */
    public function admin_user_info()
    {
       self::$user =  $this->request->admin_user;
    }

    /**
     * 获取用户信息
     */
    public function curl_user_info()
    {
       if ($this->request->admin_user){
           $this->failed('请登录');
       }
       $this->success($this->request->admin_user);
    }
}