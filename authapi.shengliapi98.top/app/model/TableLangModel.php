<?php


namespace app\model;


use think\Model;

class TableLangModel extends Model
{
    public $name = 'dianji_table_lang';

    public function getExplainAttr($value)
    {
        return html_entity_decode(str_replace('/topic/', config('ToConfig.app_update.image_url') . '/topic/', $value));
    }

    public static function table_explain($id)
    {
       return  self::where('table_id',$id)->select();
    }

    public static function page_one($map)
    {
        return  self::where($map)->find();
    }

    public static function set_insert($map){
        return  self::insert($map);
    }

    public static function set_update($map){
        return  self::update($map);
    }
}