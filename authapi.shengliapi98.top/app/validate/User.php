<?php
declare (strict_types=1);

namespace app\validate;

use think\Validate;

class User extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名' =>  ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'user_name' => 'require|max:200',
        'pwd' => 'alphaNum',
        'withdraw_pwd' => 'integer',
        'nickname' => 'max:200',
        'type' => 'integer',
        'status' => 'integer',
        'is_real_name' => 'integer',
        'is_fictitious' => 'integer',
        'id' => 'require|integer',
        'money_balance'=>'float',
        'agent_rate' => 'float',
        'money_freeze' => 'require|float',
        'invitation_code' => 'alphaNum|max:200',
        'id_code' => 'integer',
//        'admin'=>'integer',
        'market_uid'=>'require',
        'state'=>'require|float',
        'change_money'=>'require|float',
        'uid' => 'require|integer',
        'money_ststus' => 'require|integer',
        'money_change_type' => 'require|integer',
        "xima_lv" => 'require|float',
        //'remarks'=>''

    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名' =>  '错误信息'
     *
     * @var array
     */
    protected $message = [
        //'user_name.require' => '名称必填',
        'user_name.max' => '名称最多不能超过200个字符',
        //'pwd.require' => '密码必填',
        //'pwd.require' => '密码必填',
        'pwd.alphaNum' => '密码必须是字母和数字',
        'withdraw_pwd.integer' => '提现密码必须是数字',
        'nickname.max' => '昵称最多不能超过200个字符',
        'type.integer' => '类型必须是数字',
        'status.integer' => '状态必须是数字',
        'is_real_name.integer' => '实名必须是数字',
        'is_fictitious.integer' => '虚拟账号必须是数字',
        'agent_rate.integer' => '分销必须是数字',
        'xima_lv.integer' => '洗码率',
        'invitation_code.max' => '邀请码最多不能超过200个字符',
        'id.require' => 'ID必填',
        'id.integer' => 'ID必须是整数',
//        'admin.require' => '业务员ID必须是整数',
        'state.integer' => '修改状态必填必须是整数',
        'state.require' => '修改状态必填',
        'id_code.integer' =>'上级ID必须是数字',
        'xima_lv.require'=>'洗码率必填'
    ];

    /**
     * 验证场景
     * @var \string[][]
     */
    protected $scene = [
        'edit' => ['xima_lv','admin','user_name','agent_rate', 'pwd', 'withdraw_pwd', 'nickname', 'type', 'status','is_real_name','is_fictitious','id','invitation_code'],
        'add' => ['id_code','xima_lv','admin','user_name','agent_rate', 'pwd', 'withdraw_pwd', 'nickname', 'type', 'status','is_real_name','is_fictitious','invitation_code'],
        'detail' => ['id'],
        'status'=> ['id','status'],
        'money'=>['uid','change_money','money_ststus','money_change_type'],
    ];

}
