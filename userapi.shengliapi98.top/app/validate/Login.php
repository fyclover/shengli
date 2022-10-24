<?php
declare (strict_types = 1);

namespace app\validate;

use think\Validate;

class Login extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名' =>  ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'user_name'=>  'require|max:50',
        'pwd'=>'require|max:50',
        'phone'=>'require|max:40|min:4',
        'used'=>'require|max:50',
        'nickname'=>'max:20',
        'captcha'=>'require',
        'password'=>'require|max:50',
        'area_code'=>'require|max:50',
        'sign' => 'require|max:32',
        'timestamp'=>'require|number',
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名' =>  '错误信息'
     *
     * @var array
     */
    protected $message  =   [
        'user_name.require' => '{%account number is required}',
        'user_name.max' => '{%wrong account or password}',
        'area_code.require' => '区号必填',
        'area_code.max' => '区号最多50字',
        'pwd.require' => '{%wrong account or password}',
        'pwd.max' => '密码最多50字',
        'password.require' => '确认密码必填',
        'password.max' => '确认密码最多50字',
        'phone.require' => '{%mobile phone number is required}',//手机号必填
        //'phone.number' => '{&no. pure number}',//手机号纯数字
        'phone.min' => '{%minimum 7 digits}',
        'phone.max' => '手机号最多200字',
        'used.require' => '旧密码必填',
        'used.max' => '旧密码最多200字',
        'nickname.max' => '昵称最多20字',
        'captcha.require'=>'{%code is required}',
        'sign.require'=>'{%network connection error}',
        'require'=>'{%network connection error}'
    ];

    /**
     * 验证场景
     * @var \string[][]
     */
    protected $scene  = [
        'login'=>['phone','pwd'],//登陆
        'register'=>['user_name','phone','pwd','password','nickname','captcha','area_code'],//注册
        'forget'=>['phone','pwd'],//忘记密码
        'edit_pwd'=>['pwd'],//修改密码
        'captcha'=>['phone','area_code','sign','timestamp'],//验证码
    ];

}
