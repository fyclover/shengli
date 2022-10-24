<?php


namespace app\service;



use app\model\Luzhu;

class CardServiceBase
{
    /**
     * redis 开牌展示
     * @param $table_id
     * @param $game_type
     * @throws \Psr\SimpleCache\InvalidArgumentException
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
            case 1:
            case 3:
                return $service->get_pai_info_bjl($pai_data);
                break;
            case 6:
                return $service->get_pai_info_nn($pai_data);
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

    //牛牛获取露珠
    public function get_nn_lu_zhu_info($table_id,$xue,$gameType = 6)
    {
        $map = array();
        $map['status'] = 1;
        $map['table_id'] = $table_id;
        if ($xue > 0) $map['xue_number'] = $xue;

        // 增加靴号的重新处理
        if($xue <= 0){
            $one_info = Luzhu::where($map)->whereTime('create_time', 'today')->order('id desc')->find();
            !empty($one_info) &&  $map['xue_number'] = $one_info->xue_number;
        }

        $map['game_type'] = $gameType;  // 代表百家乐 | 牛牛

        $info = Luzhu::whereTime('create_time', 'today')->where('result','<>',0)->where($map)->order('id desc')->limit(8)->select()->toArray();
        //获得本地本次开牌结果
        $card = new OpenNiuNiuService();
        $luzhu =[];
        // 发给前台的 数据
        foreach ($info as $k => $val) {
            $result_info = $card->runs(json_decode($val['result_pai'], true));
            if (!empty($result_info))$luzhu []= $card->lz_exhibition($result_info);
        }
        return $luzhu;
    }
}

