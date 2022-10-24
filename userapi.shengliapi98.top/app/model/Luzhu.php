<?php


namespace app\model;


use app\service\OpenNiuNiuService;
use think\facade\Log;
use think\Model;

class Luzhu extends Model
{

    public $name = 'dianji_lu_zhu';
    //露珠
    public static $status = 1; //其他作废 1正常露珠
    public static $decor = ['r' => '红桃', 'h' => '黑桃', 'f' => '方块', 'm' => '花子'];
    public static $poker = ['1' => 1, '2' => 2, '3' => 3, '4' => 4, '5' => 5, '6' => 6, '7' => 7, '8' => 8, '9' => 9, '10' => 10, 'J' => 11, 'Q' => 12, 'K' => 13];

    //获取百家乐最新露珠信息
    public static function table_lu_zhu_list($table_id = 0, $game_type = 0)
    {
        $map = [
            ['table_id', '=', $table_id],
            ['game_type', '=', $game_type]
        ];
        //先查询最大的靴号和铺号
        $find = self::where($map)
            ->cache(10)
            ->whereTime('create_time', 'today')
            ->order('id desc')
            ->find();
        !empty($find) && $map[] = ['xue_number', '=', $find->xue_number];

        $limit = 66;
        $order = 'id asc';
        if ($game_type == 2) {
            $limit = 180;
        }elseif ($game_type == 6){
            $limit = 10;
            $order = 'id desc';
        }
        //查询露珠信息
        $map[]= ['result', '<>', 0];
        $list = self::whereTime('create_time', 'today')
            ->where($map)
            ->order($order)
            ->limit($limit)
            ->cache(10)
            ->select()
            ->toArray();
        if (empty($list)) return [];

        //牛牛露珠
        if ($game_type == 6) {
            return self::card_nn_lu_zhu($list);
        }
//        if ($game_type == 2 || $game_type == 3) {
//        }
        return self::card_bjl_lu_zhu($list);
    }

    public static function card_bjl_lu_zhu($list)
    {
        $i = 0;
        $returnData = [];
        foreach ($list as $k => $val) {
            $tmp = array();
            $t = explode("|", $val['result']);
            $tmp['result'] = $t[0];
            $tmp['ext'] = $t[1];
            if ($tmp['result'] != 0) {
                $k = 'k' . $i;
                $returnData[$k] = $tmp;
                $i++;
            }
        }
        return $returnData;
    }

    public static function card_nn_lu_zhu($list)
    {
        $card = new OpenNiuNiuService();
        $luzhu = [];
        // 发给前台的 数据
        foreach ($list as $k => $val) {
            $result_info = $card->runs(json_decode($val['result_pai'], true));
            if (!empty($result_info)) $luzhu [] = $card->lz_exhibition($result_info);
        }
        return $luzhu;
    }
}