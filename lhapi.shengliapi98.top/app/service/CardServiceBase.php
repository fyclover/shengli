<?php


namespace app\service;


class CardServiceBase
{
    /**
     * redis 开牌展示
     * @param $table_id
     * @param $game_type
     */
    public function get_pai_info($table_id, $game_type)
    {
        $pai_data = redis()->get('table_id_' . $table_id . '_' . $game_type);
        if (empty($pai_data)) return false;
        $service = new WorkerOpenPaiService();
        switch ($game_type){
            case 2:
                return $service->get_pai_info_lh($pai_data);
                break;
        }
        return [];
    }

    //获取派彩金额
    public function get_payout_money($user,$table_id, $game_type)
    {
        $money = redis()->get('user_'.$user.'_table_id_' . $table_id . '_' . $game_type);
        if ($money === null) return false;
        redis()->del('user_'.$user.'_table_id_' . $table_id . '_' . $game_type);
        return $money;
    }
}

