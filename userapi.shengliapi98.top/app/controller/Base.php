<?php

namespace app\controller;

use app\BaseController;
use app\model\HomeTokenModel;
use app\model\UserModel;
use app\model\UserSet;
use think\facade\Lang;

class Base extends BaseController
{
    public static $user;

    public function initialize()
    {
        $this->user_info();
        parent::initialize(); // TODO: Change the autogenerated stub
    }

    //获取用户信息
    public function user_info()
    {


        $token = $this->request->header('x-csrf-token');
        if (empty($token)) $token = $this->request->post('token');
        if (empty($token)) return show([], 505, 'token不存在');
        $res = HomeTokenModel::auth_token($token); //查询token
        if (empty($res)) return show([], 505, 'token无效');
        //校验是否过期的token
        if (time() - strtotime($res['create_time']) >= env('token.home_token_time', 600)) return show([], 504, 'token过期');
        //token没过期，修改当前token在线时间
        HomeTokenModel::update_token($token);
        //查询当前用户信息
        $user_info = UserModel::page_one($res['user_id']);

        if (empty($user_info) || $user_info['status'] != 1) return show([], 503, '用户异常');
        //查询用户洗马什么的信息
        $lang = $this->request->param('lang','zh-cn');
        if ($lang == 'jpn') $lang ='jp';
        $user_xima = UserSet::page_one($user_info['id'],$lang);
        if(empty($user_xima['language'])){
            $user_xima['language'] = $lang;
        }
        Lang::load(app()->getRootPath().'/app/lang/'.$user_xima['language'].'.php');
        //session 登陆写入日志
        $res['token'] = $token;
        //合并用户信息和洗码信息
        $user = array_merge($user_info,$user_xima);
        self::$user = $user;
    }
}