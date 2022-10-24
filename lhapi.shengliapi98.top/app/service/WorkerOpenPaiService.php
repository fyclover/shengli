<?php


namespace app\service;


use app\model\GameRecords;
use app\model\Luzhu;
use app\model\Table;

class WorkerOpenPaiService
{
    //龙虎开牌
    public function get_pai_info_lh($pai_data)
    {
        $pai_data = $pai_info = json_decode($pai_data, true);
        //获取扑克点数
        $card = new OpenDragonTigerService();
        $pai_result = $card->runs($pai_data);
        $pai_flash = $card->pai_flash($pai_result);
        return ['result' => $pai_result,'pai_flash'=>$pai_flash];
    }

    //获取台桌信息
    public function get_table_info($id,$user_id)
    {
        if ($id <= 0) return [];
        //获取台桌信息
        $info = Table::page_one($id);
        //获取台桌倒计时和视频地址
        $info = Table::table_opening_count_down($info);
        //获取最新的靴号和铺号
        $bureau_number = bureau_number($id, true);
        $info['bureau_number'] = $bureau_number['bureau_number'];
        //获取当前用户是否下注，下注了记住免佣状态。免佣状态
        $user['id'] = $user_id;
        $info['is_exempt'] = GameRecords::user_status_bureau_number_is_exempt($id, $bureau_number['xue'], $user);
        return $info;
    }

    public function get_table_opening_count_down($info)
    {
        //获取台桌倒计时和视频地址
        $info = Table::table_opening_count_down($info);
        return $info;
    }
//    // 台座露珠列表
//    public function lu_zhu_list($table_id = 1, $game_type = 2)
//    {
//        //百家乐台桌
//        $info = Luzhu::table_lu_zhu_list($table_id, $game_type);
//        return $info;
//    }
//
//    //获取台桌列表
//    public function get_table_list($game_type = 2)
//    {
//        //每个游戏的台桌列表。不存在就是所有台桌
//        $map = [];
//        if ($game_type > 0) $map[] = ['game_type','=',$game_type];
//        //$map['status'] = 1;
//
//        $list = Table::page_repeat($map, 'list_order asc');
//        $list = $list->hidden(['game_play_staus', 'is_dianji', 'is_weitou', 'is_diantou', 'list_order']);
//        //计算台桌倒计时
//        if (empty($list)) return $list;
//        foreach ($list as $key => &$value) {
//            //获取视频地址
//            $value = Table::table_opening_count_down($value);
//            $value->p = rand(10, 40) . '.' . rand(1, 9) . 'k/' . rand(10, 40);
//            $value->t = rand(10, 40) . '.' . rand(1, 9) . 'k/' . rand(10, 40);
//            $value->b = rand(10, 40) . '.' . rand(1, 9) . 'k/' . rand(10, 40);
//        }
//        return $list;
//    }
//
//    public function lu_zhu_and_table_info(array $data)
//    {   //获取露珠信息
//        if ($data['game_table_type'] == 'luzhu_list'){
//            return $this->lu_zhu_list($data['table_id']);
//        }
//        //获取台座列表
//        if ($data['game_table_type'] == 'table_list'){
//            return $this->get_table_list($data['game_type']);
//        }
//        return [];
//    }


}