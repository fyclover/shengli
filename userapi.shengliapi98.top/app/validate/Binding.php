<?php
declare (strict_types = 1);

namespace app\validate;

use think\Validate;

class Binding extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名' =>  ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'card'=>  'require|max:50',
        'name'=>'require|max:50',
        'user_name'=>'require|max:100',
        'address'=>'require|max:200',
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名' =>  '错误信息'
     *
     * @var array
     */
    protected $message  =   [
        'card.require' => '卡号必填',
        'card.max' => '卡号最多50字',
        'name.require' => '银行名称必填',
        'name.max' => '银行名称50字',
        'user_name.require' => '手开户名必填',
        'user_name.max' => '开户名最多100字',
        'address.require' => '开户地址必填',
        'address.max' => '开户地址最多200字',
    ];

    /**
     * 验证场景
     * @var \string[][]
     */
    protected $scene  = [
        'bin'=>['card','name','user_name','address'],//绑定
    ];

}
