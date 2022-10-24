<?php
declare (strict_types = 1);

namespace app\validate;

use think\Validate;

class TableValidate extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名' =>  ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'id'=>  'require|integer',
        'status'=>  'require|integer',
        'run_status'=>  'require|integer',
        'start_time'=>  'require|date',
        'countdown_time'=>'require',
        'he_guan_head_img'=>'min:10',
        'video_near'=>  'require',
        'video_far'=>  'require',
        'table_title'=>  'require',
        'table_describe'=>  'require',
        'he_guan_name'=>  'require',
        'list_order'=>  'require',
        'xian_hong_zhuang_xian_usd'=>'require',
        'xian_hong_zhuang_xian_cny'=>'require',
        'xian_hong_he_usd'=>'require',
        'xian_hong_he_cny'=>'require',
        'xian_hong_duizi_usd'=>'require',
        'xian_hong_duizi_cny'=>'require',
        'lu_zhu_name'=>'require',
        'remark'=>'require'
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名' =>  '错误信息'
     *
     * @var array
     */
    protected $message  =   [
        'id.require' => 'ID必填',
        'id.integer' => 'ID必须是整数',
        'title.require' => '标题必填',
        'title.max' => '标题最多200字',
        'video_near.require'=>'视频地址必填',
        'video_far.require'=>'视频地址必填',
        'remark.require'=>'备注必填',


    ];

    /**
     * 验证场景
     * @var \string[][]
     */
    protected $scene  = [
        'add'=>[
            'he_guan_name','table_describe','table_title','video_far',
            'video_near','start_time','run_status','status','countdown_time',
            'xian_hong_zhuang_xian_usd','xian_hong_zhuang_xian_cny','xian_hong_he_usd','xian_hong_he_cny',
            'xian_hong_duizi_usd','xian_hong_duizi_cny','lu_zhu_name','remark'
        ],
        'edit'=>[
            'id','he_guan_name','table_describe','table_title','video_far',
            'video_near','start_time','run_status','status','countdown_time',
            'xian_hong_zhuang_xian_usd','xian_hong_zhuang_xian_cny','xian_hong_he_usd','xian_hong_he_cny',
            'xian_hong_duizi_usd','xian_hong_duizi_cny','lu_zhu_name','remark'
        ],
        'type'=>['types','id'],
        'detail'=>['id'],

    ];

}
