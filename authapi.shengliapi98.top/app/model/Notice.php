<?php


namespace app\model;


use app\traits\TraitModel;
use think\Model;

class Notice extends Model
{
    use TraitModel;

    public $name = 'common_notice';
    public $positions =
        [
            1 => '首页通知',
            2 => '个人中心',
            3 => '网址维护',
        ];

    public function getStatusAttr($value)
    {
        return $value;
        $type = [0 => '下架', 1 => '上架',];
        return isset($type[$value]) ? $type[$value] : $value;
    }

    public function getPositionAttr($value)
    {
        return $value;
        return isset($this->position[$value]) ? ['key'=>$value,'value'=>$this->position[$value]] : $value;
    }
    public static function page_list($where,$limit,$page)
    {


        return self::alias('a')
            ->where($where)
            ->order('id desc')
            ->paginate(['list_rows'=>$limit,'page'=>$page])->each(function($item, $key){
               $item->value = isset($item->positions[$item->position]) ? $item->positions[$item->position] : '';
            });
    }



}