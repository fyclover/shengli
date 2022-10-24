<?php
declare (strict_types=1);

namespace app\middleware;

use app\model\AdminModel;
use app\model\AgentLavel;
use app\model\TokenModel;
use app\model\UserModel;
use app\model\UserSet;
use app\traits\PublicResponseTrait;

class Auth
{
    use PublicResponseTrait;

    public function handle($request, \Closure $next)
    {
        //校验token
        $token = $request->post('token');
        if (empty($token)) $this->failed('token不存在');

        //$type 1后台用户 2代理商后台
        $map['type'] = $request->post('admin_type/d', 1);
        $map['token'] = $token;

        //查询token $type 1后台用户
        $res = (new TokenModel())->where($map)->find();
        if (empty($res)) $this->failed('无效token');

        //校验是否过期的token
        $expiration_time = time() - strtotime($res['create_time']);
        if ($expiration_time >= env('token.token_time', 10)) $this->failed('token过期');
        // config('ToConfig.admin_agent.admin_agent') 代理商 类型
        switch ($map['type']) {
            case 1:
                $request->admin_user = $this->admin_user($token, $res);
                break;
            case 2:
                $request->admin_user = $this->agent_user($token, $res);
                break;
            default:
                $this->failed('登陆类型不存在');
        }
        return $next($request);
    }

    //后台管理员
    public function admin_user($token, $res)
    {
        //校验成功处理逻辑
        //查询用户数据并存入session
        $res = (new AdminModel())->find($res['admin_uid']);
        if (empty($res)) $this->failed('无效token');
        //session 写入日志
        //if (empty(session())) (new \app\common\service\LoginLog())->login();
        $res['token'] = $token;

        //查询后台管理的下级
        $admin_user_list = UserModel::where(['agent_id' => 0])->cache(1800)->select()->toArray();

        //后台管理员的 代理列表 和用户列表
        $agent_list = [];//代理列表
        $user_list = [];//用户列表
        $user_fictitious_list = [];//虚拟用户
        $user_demo_list = [];//试玩用户
        if (!empty($admin_user_list)) {
            foreach ($admin_user_list as $key => $value) {
                if ($value['is_fictitious'] == 1) {//虚拟用户
                    $user_fictitious_list[$key] = $value['id'];
                    continue;
                }
                if ($value['is_fictitious'] == 2) {//试玩用户
                    $user_demo_list[$key] = $value['id'];
                    continue;
                }
                if ($value['type'] == 1 && $value['is_fictitious'] == 0) {//代理列表
                    $agent_list[$key] = $value['id'];
                    continue;
                }
                if ($value['type'] == 2 && $value['is_fictitious'] == 0) {//用户列表
                    $user_list[$key] = $value['id'];
                    continue;
                }
            }
        }
        $res['user_all_list'] = UserModel::where(['is_fictitious' => 0, 'type' => 2])->cache(1800)->column('id');//所有的正式会员
        $res['agent_all_list'] = UserModel::where(['is_fictitious' => 0, 'type' => 1])->cache(1800)->column('id');//所有的正式代理
        $res['agent_list'] = $agent_list;//直属代理
        $res['user_list'] = $user_list;////直属会员
        $res['user_fictitious_list'] = $user_fictitious_list;//虚拟账号
        $res['user_demo_list'] = $user_demo_list;//试玩账号
        $res['agent'] = 0;//后台管理员标识
        $res['direct_list']  = array_merge_func($agent_list, $user_list);
        session('admin_user', $res);
        // 添加中间件执行代码
        return $res;
    }

    //代理商管理员
    public function agent_user($token, $res)
    {
        //校验成功处理逻辑
        //查询用户数据并存入session
        $res = (new UserModel())->find($res['admin_uid']);
        if (empty($res)) $this->failed('无效token');
        if ($res->status != 1) $this->failed('该服务商被禁用');
        if ($res->type != 1) $this->failed('该用户不是代理');
        //session 写入日志
        //if (empty(session())) (new \app\common\service\LoginLog())->login();
        $res['token'] = $token;
        $res['role'] = config('ToConfig.admin_agent.admin_agent_id');
        $res ['user_set'] = UserSet::where('u_id', $res['admin_uid'])->find();
        $res['agent'] = 1;//服务商标示


        //查询代理的直属下级代理
        $agent_list = UserModel::where(['agent_id' => $res->id, 'type' => 1])->cache(1800)->column('id');
        //查询代理的直属下级会员
        $user_list = UserModel::where(['agent_id' => $res->id, 'type' => 2])->cache(1800)->column('id');

        //代理能够查询的所有代理
        $column_agent_id = AgentLavel::where('agent_pid', $res->id)->cache(1800)->column('agent_id');
        array_push($column_agent_id, $res->id);
        $column_agent_id = array_unique($column_agent_id);//拿到所有下级代理
        //代理能够查询的所有会员
        $column_agent_user_id = UserModel::where('agent_id', 'in', $column_agent_id)->cache(1800)->where(['type' => 2])->column('id');

        $res['agent_list'] = empty($agent_list) ? []:$agent_list;//直属代理
        $res['user_list'] =  empty($user_list) ? []:$user_list;//直属会员
        $res['agent_all_list'] =  empty($column_agent_id) ? []:$column_agent_id;//所有代理
        $res['user_all_list'] =  empty($column_agent_user_id) ? []:$column_agent_user_id;//所有会员
        $res['user_demo_list']  = $res['user_fictitious_list'] = [];//虚拟账号
        $res['direct_list']  = array_merge_func($res['agent_list'], $res['user_list']);
        session('admin_user', $res);
        // 添加中间件执行代码
        return $res;
    }

}
