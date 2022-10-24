<?php


namespace app\model;


use think\Model;

class Notify extends Model
{

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

    public static function page_list($where, $limit, $page)
    {
        return self::alias('a')
            ->where($where)
            ->order('id desc')
            ->paginate(['list_rows' => $limit, 'page' => $page])
            ->each(function ($item, $key) {
                if (empty($item->unique)) return '';
                //非空时进行分割
                $model = new UserModel();
                $nickname = $model->whereIn('id', $item->unique)->column('nickname');
                if (empty($nickname)) return '';
                $nickname = implode(',', $nickname);
                if (empty($nickname)) return '';
                $item->nickname = $nickname;
            });
    }
}