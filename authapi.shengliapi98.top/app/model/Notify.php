<?php


namespace app\model;


use app\traits\TraitModel;
use think\Model;

class Notify extends Model
{
    use TraitModel;

    public $name = 'common_notify';
    public $notifys = [1 => '全体', 2 => '私人'];


    public static function insert_one(string $mark = null, string $unique = null, int $type = 1, int $status = 1)
    {
        return self::insert([
            'type' => $type,
            'status' => $status,
            'unique' => $unique,
            'mark' => $mark,
            'create_time' => date('Y-m-d H:i:s'),
        ]);
    }

}