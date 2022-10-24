<?php
declare (strict_types = 1);

namespace app\validate;

use think\Validate;

class PayBank extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名' =>  ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'id'  =>  'require',
        'status'  =>  'require',
        'is_default' =>  'require',
        'address' =>  'require',
        'name' =>  'require',
        'user_name' =>  'require',
        'card' =>  'require',
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名' =>  '错误信息'
     *
     * @var array
     */
    protected $message  =   [
        'id.require'     => 'ID必填',
        'status.require' => '是否删除必填',
        'address.require' => '地址必填',
        'name.require' => '银行名称必填',
        'user_name.require' => '姓名必填',
        'card.require' => '银行卡号必填',
    ];

    /**
     * 验证场景
     * @var \string[][]
     */
    protected $scene  = [
        'del'=>['id'],
        'default'=>['id'],
        'edit'=>['address','name','user_name','card','u_id']
    ];

}
