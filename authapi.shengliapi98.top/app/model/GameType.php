<?php


namespace app\model;


use app\traits\TraitModel;
use think\Model;

class GameType extends Model
{
    use TraitModel;

    public $name = 'dianji_game_type';

    //游戏名称

    public static function one($id = 0)
    {
        if ($id <= 0) {
            return self::select();
        }

        return self::where('id', $id)->value('type_name');
    }

    public static function page_list($map,$limit, $page)
    {
        return self::alias('a')
            ->where($map)
            ->order('id desc')
            ->paginate(['list_rows' => $limit, 'page' => $page], false);
    }

    public function getExplainAttr($value)
    {
        return html_entity_decode(str_replace('/topic/', config('ToConfig.app_update.image_url') . '/topic/', $value));
    }
}