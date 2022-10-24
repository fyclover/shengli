<?php


namespace app\model;


use think\Model;

class SysConfig extends Model
{
    public $name = 'common_sys_config';


    public static function get_config(string $name)
    {
        return self::where('name', $name)->find();
    }
}