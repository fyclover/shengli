<?php


namespace app\model;


use think\Model;

class GameRecords extends Model
{

    public $name = 'dianji_records';
    public static $getCloseStatus = [1 => '待开牌', 2 => '已结算', 3 => '台面作废', 4 => '修改结果'];
    public static $getAgentStatus = [1 => '洗码结算', 2 => '未结算', 9 => '代理已结算'];//全部结算


    public static function lz_win_or_lose_calculation($item): array
    {
        if (!$item) return ['main' => '', 'vice' =>''];
        if ($item->lu_zhu_id <= 0) return ['main' => '', 'vice' =>''];;
        //查询露珠
        $result = Luzhu::where('id', $item->lu_zhu_id)->value('result');
        $game_type = Luzhu::where('id', $item->lu_zhu_id)->value('game_type');
        return self::win_or_lose_calculation($result,$game_type);
    }

    public static function win_or_lose_calculation($result,$game_type=3): array
    {
        if (!$result) return [];
        $array = explode('|', $result);
        if (!is_array($array)) return [];
        if($game_type == 3){
            $main = [1 => '庄', 2 => '闲', 3 => '和', 4 => '幸',];
            $vice = [0 => '', 1 => '庄对', 2 => '闲对', 3 => '庄闲对'];
        }
        if($game_type == 2){
            $main = [1 => '龙', 2 => '虎', 3 => '和', 4 => '',];
            $vice = [0 => '', 1 => '庄对', 2 => '闲对', 3 => '庄闲对'];
        }
        if($game_type == 6){
            $main = [1 => '庄', 2 => '闲1', 3 => '闲2', 4 => '闲3',];
            $vice = [0 => '无', 1 => '牛1', 2 => '牛2', 3 => '牛3',
                4 => '牛4',5 => '牛5',6 => '牛6',7 => '牛7',8 => '牛8',9 => '牛9',10 => '牛',999 => '牛'];
        }
        if($game_type == 8){
            $main = [1 => '庄', 2 => '闲1', 3 => '闲2', 4 => '闲3',];
            $vice = [];
        }

        $zhuang = isset($array[0]) && isset($main[$array[0]]) ? $main[$array[0]] : '';
        $idle = isset($array[1]) && isset($vice[$array[1]]) ? $vice[$array[1]] : '';
        return ['main' => $zhuang, 'vice' => $idle];
    }

    /**
     * /用户当前局免佣状态
     * @param $table_id /台座ID
     * @param $number /靴号铺号
     * @param $user /用户信息
     * @param $is_order /是否需要知道 是否下单过 下单判断免佣会用到
     * return 默认 is_exempt = 1; $is_exempt->is_exempt查出是0 还是1
     */
    public static function user_status_bureau_number_is_exempt($table_id, $number, $user, $is_order = false)
    {
        $is_exempt = self::where([
            'xue_number' => $number['xue_number'],
            'pu_number' => $number['pu_number'],
            'table_id' => $table_id,
            'user_id' => $user['id']
        ])
            ->whereTime('created_at', 'today')
            ->order('created_at desc')
            ->find();

        #------获取是否下单过开始 下单会用到 101表示没下单过
        if ($is_order == true && empty($is_exempt)) {
            return 101;
        }
        #------获取是否下单过结束

        #####获取当前用户当前局免佣状态
        if (!empty($is_exempt)) {
            return $is_exempt->is_exempt;
        }
        return 0;
        #####获取当前用户当前局免佣状态结束
    }

    //本局游戏总共下注金额
    public static function user_count_money_game_this($table_id, $xue_number, $peilv, $user)
    {
        $money = self::where([
            'xue_number' => $xue_number['xue_number'],
            'pu_number' => $xue_number['pu_number'],
            'table_id' => $table_id,
            'user_id' => $user['id'],
            'game_peilv_id' => $peilv
        ])
            ->whereTime('created_at', 'today')
            ->count('bet_amt');
        return $money;
    }
}