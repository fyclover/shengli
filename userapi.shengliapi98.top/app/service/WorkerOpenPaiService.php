<?php


namespace app\service;


use app\model\Luzhu;
use app\model\Table;
use app\model\HomeTokenModel;
use app\model\UserModel;

class WorkerOpenPaiService
{
    public static $code = 200;

    public function lu_zhu_and_table_info($data)
    {   //获取露珠信息
        if ($data['game_table_type'] == 'luzhu_res'){
            return $this->lu_zhu_list(intval($data['table_id']),intval($data['game_type']));
        }
        //获取台座列表
        if ($data['game_table_type'] == 'table_list'){
            return $this->get_table_list(intval($data['game_type']));
        }

        if ($data['game_table_type'] == 'user_info' && isset($data['token'])){
            return $this->get_user_info($data['token']);
        }

        return [];
    }

    // 台座露珠列表
    public function lu_zhu_list($table_id = 1, $game_type = 3)
    {
        //百家乐台桌
        $info = Luzhu::table_lu_zhu_list($table_id, $game_type);
        return $info;
    }

    //获取台桌列表
    public function get_table_list(int $game_type)
    {
        //每个游戏的台桌列表。不存在就是所有台桌
        $map = [];
        if ($game_type > 0) $map[] = ['game_type','=',$game_type];
        //if ($table_id > 0) $map[] = ['id','=',$table_id];
        //$map['status'] = 1;

        $list = Table::page_repeat($map, 'list_order asc');
        $list = $list->hidden(['game_play_staus', 'is_dianji', 'is_weitou', 'is_diantou', 'list_order']);
        //计算台桌倒计时
        if (empty($list)) return $list;
        foreach ($list as $key => &$value) {
            //获取视频地址
            $value = Table::table_opening_count_down($value);
            $value->p = rand(10, 40) . '.' . rand(1, 9) . 'k/' . rand(10, 40);
            $value->t = rand(10, 40) . '.' . rand(1, 9) . 'k/' . rand(10, 40);
            $value->b = rand(10, 40) . '.' . rand(1, 9) . 'k/' . rand(10, 40);
        }
        // 专门针对百家乐 进行统计 扩容 开始
        foreach ($list as $key => &$value) {
            //
         
            $resultDui = $this->get_count($value['id']);
            //获取视频地址
            $value->bjl_b = $resultDui['zhuang'];
            $value->bjl_p = $resultDui['xian'];
            $value->bjl_t = $resultDui['he'];
            $value->bjl_bpair = $resultDui['zhuang_dui'];
            $value->bjl_ppair = $resultDui['xian_dui'];
            $value->bjl_pu = $resultDui['count'];
        }
        // 专门针对百家乐 进行统计 扩容 结束
        return $list;
    }

    public function get_count($tableId){

        $number = Luzhu::where('table_id', $tableId)->order('id desc')->find();
        $xue_number = isset($number->xue_number) ? $number->xue_number : 1;

        //获取庄 闲 和 局数  总局数
        $game_count = Luzhu::where(['table_id' => $tableId])
            ->field('count(id) id_count,result')
            ->whereTime('create_time', 'today')
            ->where('status', 1)
            ->where('xue_number', $xue_number)
            ->group('result')
            ->select()
            ->toArray();

        $game_count_all = ['zhuang' => 0, 'xian' => 0, 'he' => 0, 'zhuang_dui' => 0, 'xian_dui' => 0, 'xue_num' => $xue_number, 'count' => 0];

        $count = [];
        if (!empty($game_count)) {
            foreach ($game_count as $key => $value) {
                if (strpos($value['result'], '1|') || strpos($value['result'], '1|') === 0 || strpos($value['result'], '4|') === 0) {
                    $value['res'] = 1;
                    $value['text'] = '庄赢';
                }
                if (strpos($value['result'], '2|') || strpos($value['result'], '2|') === 0) {
                    $value['res'] = 2;
                    $value['text'] = '闲赢';
                }
                if (strpos($value['result'], '3|') || strpos($value['result'], '3|') === 0) {
                    $value['res'] = 3;
                    $value['text'] = '和赢';
                }
                // 增加 庄对 闲对 的统计 开始
                $duizi = explode("|",$value['result']);
                if(isset($duizi[1])){
                    if ($duizi[1] == 1 ||  $duizi[1] == 3) {
                        $value['res_dui'] = 1;
                    }
                    if ($duizi[1] == 2 ||  $duizi[1] == 3) {
                        $value['res_dui'] = 2;
                    }
                }
                // 增加 庄对 闲对 的统计 结束
                $count[$key] = $value;
            }

            foreach ($count as $key => $value) {
                if (!isset($value['res'])) {
                    continue;
                }
                if ($value['res'] == 1) {
                    $game_count_all['zhuang'] += $value['id_count'];
                }

                if ($value['res'] == 2) {
                    $game_count_all['xian'] += $value['id_count'];
                }

                if ($value['res'] == 3) {
                    $game_count_all['he'] += $value['id_count'];
                }

                // 增加 庄对 闲对的 统计 开始
                if(isset($value['res_dui'])){
                    if ($value['res_dui'] == 1) {
                        $game_count_all['zhuang_dui'] += $value['id_count'];
                    }
                    if ($value['res_dui'] == 2) {
                        $game_count_all['xian_dui'] += $value['id_count'];
                    }
                }
                // 增加 庄对 闲对的 统计 结束

                $game_count_all['count'] += $value['id_count'];
            }
        }

        return $game_count_all;
    }

    public function get_user_info(string $token)
    {
        if (empty($token)) return ['code'=>505,'msg'=>'token过期'];
        $res = HomeTokenModel::auth_token($token); //查询token
        if (empty($res)) return ['code'=>505,'msg'=>'token无效'];
        //校验是否过期的token
        if (time() - strtotime($res['create_time']) >= env('token.home_token_time', 180)) return  ['code'=>505,'msg'=>'token过期'];
        //token没过期，修改当前token在线时间
        //HomeTokenModel::update_token($token);
        //查询当前用户信息
        $user_info = UserModel::page_one($res['user_id']);
        return ['code'=>200,'msg'=>'成功','data'=>$user_info];
    }


    ###########################################################
    //获取牌型
    //百家乐开牌
    public function get_pai_info_bjl($pai_data)
    {
        $pai_data = $pai_info = json_decode($pai_data, true);
        //h r m f
        $info = [];
        foreach ($pai_info as $key => $value) {
            if ($value == '0|0') {
                unset($pai_info[$key]);
                continue;
            }
            $pai = explode('|', $value);
            if ($key == 1 || $key == 2 || $key == 3) {
                $info['zhuang'][$key] = $pai[1] . $pai[0] . '.png';
            } else {
                $info['xian'][$key] = $pai[1] . $pai[0] . '.png';
            }
        };
        //获取扑克点数
        $card = new OpenPaiCalculationService();
        $pai_result = $card->runs($pai_data);
        $pai_flash = $card->pai_flash($pai_result);
        return ['result' => $pai_result, 'info' => $info, 'pai_flash' => $pai_flash];
    }

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

    //牛牛开牌
    public function get_pai_info_nn($pai_data)
    {
        $pai_data = $pai_info = json_decode($pai_data, true);

        $card = new OpenNiuNiuService();
        $pai_result = $card->runs($pai_data);

        $pai_res = [];
        if (empty($pai_result)) return [];
        foreach ($pai_result as $key => $value) {
            $pai_res[$key] = array_values($value['image']);
        }
        //获取扑克点数
        //$pai_result = $card->runs($pai_data);
        $pai_flash = $card->pai_flash($pai_result);
        return ['result' => $pai_res, 'pai_flash' => $pai_flash];
    }

    public function get_pai_info_three($pai_data){
        $pai_data = $pai_info = json_decode($pai_data, true);
        $card = new OpenThreeService();

        $pai_result = $card->runs($pai_data);
        $pai_res = [];
        if (empty($pai_result)) return [];
        foreach ($pai_result as $key => $value) {
            $pai_res[$key] = array_values($value['image']);
        }

        $pai_flash = $card->pai_flash($pai_result);
        return ['result' => $pai_res,'pai_flash'=>$pai_flash];
    }
}