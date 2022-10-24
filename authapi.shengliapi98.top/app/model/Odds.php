<?php


namespace app\model;


use app\traits\TraitModel;
use think\Model;

class Odds extends Model
{
    use TraitModel;

    public $name = 'dianji_game_peilv';

    public function getStartTimeAttr($value)
    {
        if ($value == 0) return $value;
        $status = date('Y-m-d H:i:s', $value);
        return ['test' => $status, 'value' => $value];
    }

    public static function page_list($map, $limit, $page)
    {
        return self::alias('a')
            ->where($map)
            ->join((new GameType())->name . ' b', 'a.game_type_id = b.id', 'left')
            ->field('a.*,b.type_name')
            ->order('id desc')
            ->paginate(['list_rows' => $limit, 'page' => $page], false);
    }

    /**
     * @param array $map //返回数据
     * @param null $order //排序
     * @param $limit
     * @param $page
     * @param $field
     */
    public static function data_list(array $map = [], $order = null, $limit = 0, $page = 0, $field = false)
    {
        $self = self::where($map);
        !empty($self) && $self = $self->order($order);
        if ($limit > 0 && $page > 0) $self = $self->limit($page, $limit);
        if ($field) $self = $self->field('*,game_tip_name as label,peilv as odds,id');
        return $self->select();
    }

    public static function data_one($id)
    {
        return self::find($id);
    }

    //赔率计算展示 $info赔率信息
    public static function odds_calculation_exhibition($info)
    {
        $info->ren = rand(10, 100);
        $info->money = rand(500, 10000);
        if ($info->id == 8) {
            $array = explode('/', $info->odds);
            $array[0] = '1:' . $array[0];
            $array[1] = '1:' . $array[1];
            $info->odds = $array;
        } else {
            $info->odds = '1:' . $info->odds;
        }
        return $info;
    }
}