<?php

namespace app\traits;


trait GatUserAuthTrait
{
    #$admin_user 如果 $all ==2 时，后台可查询所有用胡信息，1指定查询
    #user_all_list 所有会员 agent_all_list 所有代理
    #user_list 直属会员 agent_list 直属代理   agent 1 代理标识

    //$admin_user 详细数据 看 中间件auth
    public function get_user_auth($admin_user, $name = null, $all = 1)
    {
        //agent_auth ==1 是代理。==0 是管理员
        //查询代理能够查询到的 用户数据
        if ($admin_user->agent == 1) {
            return $admin_user->{$name};
        } else {

            //总后台需要查寻拥有用户数据
            if ($all == 2) {
                return [];
            }
            //没有指定查寻的数据 直接查询所有的用户
            if ($name == null) {
                return [];
            } else {
                return $admin_user->{$name};
            }
        }
    }
}