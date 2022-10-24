<?php
declare (strict_types = 1);

namespace app\validate;

use think\Validate;

class Admin extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名' =>  ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'user_name'  =>  'require|max:200',
        'pwd' =>  'alphaNum',
        'role'=>'require',
        'market_level'=>'require',
        'remarks'=>'max:200',
        'phone'=>'max:200',
        'invitation_code'=>'max:200',
        'id'=>'require'
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名' =>  '错误信息'
     *
     * @var array
     */
    protected $message  =   [
        'user_name.require' => '名称必填',
        'user_name.max'     => '名称最多不能超过200个字符',
        'pwd.require' => '密码必填',
        'role.require'     => '角色必填',
        'market_level.require' => '市场部级别必填',
        'remarks.max'     => '备注最多不能超过200个字符',
        'invitation_code.max'     => '邀请码最多不能超过200个字符',
        'id.require'     => 'ID必填',
    ];

    /**
     * 验证场景
     * @var \string[][]
     */
    protected $scene  = [
        'add'=>['user_name','pwd','role','market_level','remarks','invitation_code'],
        'edit'=>['user_name','pwd','role','market_level','remarks','id'],
        'detail'=>['id'],

    ];

}
