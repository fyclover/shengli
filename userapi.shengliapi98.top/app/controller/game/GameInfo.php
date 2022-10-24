<?php


namespace app\controller\game;


use app\model\GameRecords;
use app\model\GameType;
use app\model\GameTypeLangModel;
use app\model\Luzhu;
use app\model\LuZhuBackups;
use app\model\Odds;
use app\model\Table;
use app\controller\Base;
use app\model\UserSet;
use app\service\WorkerOpenPaiService;

class GameInfo extends Base
{

    //获取游戏列表
    public function get_game_list(): string
    {
        $list = GameType::where(['status' => 1])->order('sort desc')->select();
        $list = $list->hidden(['status']);
        if (empty($list)) show($list);
        $list = $list->toArray();
        foreach ($list as $key => &$value) {
            //查询游戏规则多语言
            $language = GameTypeLangModel::page_one(['game_type' => $value['id'], 'lang_type' => self::$user['language']]);
            if (!empty($language)) {
                $value['explain'] = $language->explain;
            }
            //3百家乐 2龙虎 1 21点
            switch ($value['id']) {
                case 1:
                    $value['image'] = config('ToConfig.app_update.image_url') . '/resources/gameimg/21.png';
                    $value['type_name'] = lang('scene');
                    break;
                case 2:
                    $value['image'] = config('ToConfig.app_update.image_url') . '/resources/gameimg/lh.png';
                    $value['type_name'] = lang('dragon tiger');
                    break;
                case 3:
                    $value['image'] = config('ToConfig.app_update.image_url') . '/resources/gameimg/bjl.png';
                    $value['type_name'] = lang('baccarat');
                    break;
                case 6:
                    $value['image'] = config('ToConfig.app_update.image_url') . '/resources/gameimg/21.png';
                    $value['type_name'] = lang('cattle');
                    break;
                case 8:
                    $value['image'] = config('ToConfig.app_update.image_url') . '/resources/gameimg/sg.png';
                    $value['type_name'] = lang('three general');
                    break;
            }
        }
        show($list);
    }

    public function get_game_info(): string
    {
        $id = $this->request->post('id/d', 0);
        if ($id <= 0) show([], env('code.error'), '游戏ID必填');
        $find = GameType::find($id);
        show($find);
    }


    //获取台桌视频  前端要求单独写出来
    public function get_table_info_video()
    {
        $id = $this->request->param('id', 0);
        if ($id <= 0) show([], env('code.error'), '台桌ID必填');
        $info = Table::page_one($id);//获取台桌信息
        $info = Table::table_open_video_url($info);//获取视频url
        show(['video_far' => $info->video_far, 'video_near' => $info->video_near]);
    }

    //获取台桌信息
    public function get_table_info()
    {
        $id = $this->request->param('id', 0);
        if ($id <= 0) show([], env('code.error'), '台桌ID必填');
        //获取台桌信息
        $info = Table::page_one($id);
        //获取台桌倒计时和视频地址
        $info = Table::table_opening_count_down($info);
        //获取最新的靴号和铺号
        $bureau_number = bureau_number($id, true);
        $info['bureau_number'] = $bureau_number['bureau_number'];
        $info['info_number'] = $bureau_number['xue'];
        //获取当前用户是否下注，下注了记住免佣状态。免佣状态
        $info->is_exempt = GameRecords::user_status_bureau_number_is_exempt($id, $bureau_number['xue'], self::$user);
        show($info);
    }

    //获取台桌列表
    public function get_table_list(): string
    {

        $game_type = $this->request->param('type_id', 0);
        //每个游戏的台桌列表。不存在就是所有台桌
        $map = [];
        if ($game_type > 0) $map['game_type'] = $game_type;
        //$map['status'] = 1;
        $list = Table::page_repeat($map, 'list_order asc');
        $list = $list->hidden(['game_play_staus', 'is_dianji', 'is_weitou', 'is_diantou', 'list_order']);
        //计算台桌倒计时
        if (empty($list)) show($list);
        foreach ($list as $key => &$value) {
            //获取视频地址
            $value = Table::table_opening_count_down($value);
            $value['p'] = rand(10, 40) . '.' . rand(1, 9) . 'k/' . rand(10, 40);
            $value['t'] = rand(10, 40) . '.' . rand(1, 9) . 'k/' . rand(10, 40);
            $value['b'] = rand(10, 40) . '.' . rand(1, 9) . 'k/' . rand(10, 40);
        }
        show($list);
    }

   public function get_odds_list(): string
    {
        $game_type = $this->request->param('type_id', 0);
        if ($game_type <= 0) show([], env('code.error'), '游戏类型必填');
        $map = [];
        $map['game_type_id'] = $game_type;
        $list = Odds::data_list($map, 'sort asc', 0, 0, true);


        ##############################################
        //查询台座限红
        $table_id = $this->request->param('table_id', 0);
        $tablexianhong = Table::table_is_xian_hong($table_id);
        ############################################
        //查询用户限红
        $user_id = $this->request->param('user_id', 0);
        $userxianhong = UserSet::user_is_xian_hong($user_id,$game_type);
        ############################################
        //获取最终限红
        if (!empty($userxianhong)){
           $xianhong =  $userxianhong;
        }else{
           $xianhong =  $tablexianhong;
        }
        ############################################

        //计算赔率
        foreach ($list as $key => &$value) {
            $value = Odds::odds_calculation_exhibition($value);
            if (!empty($xianhong)){
                $value->xian_hong_max =  $xianhong['xian_hong_max'];
                $value->xian_hong_min =  $xianhong['xian_hong_min'];
            }
        };
        //查询每个赔率当前在线人数和当前下注金额
        show($list);
    }


    public function get_table_count(): string
    {
        //统计台桌在线用户
        $game_table = $this->request->param('table_id', 0);
        if ($game_table <= 0) show([], env('code.error'), '台桌ID必填');
        //查询该台桌的最新靴
        $number = Luzhu::where('table_id', $game_table)->order('id desc')->find();
        $xue_number = isset($number->xue_number) ? $number->xue_number : 1;
        //获取今日总投注金额
        $bet_amt = GameRecords::where(['table_id' => $game_table])
            ->whereTime('created_at', 'today')
            ->where('xue_number', $xue_number)
            ->sum('bet_amt');
        //获取庄 闲 和 局数  总局数
        $game_count = Luzhu::where(['table_id' => $game_table])
            ->field('count(id) id_count,result')
            ->whereTime('create_time', 'today')
            ->where('status', 1)
            ->where('xue_number', $xue_number)
            ->group('result')
            ->select()
            ->toArray();

        $game_count_all = ['zhuang' => 0, 'xian' => 0, 'he' => 0, 'zhuang_dui' => 0, 'xian_dui' => 0, 'xue_num' => $xue_number, 'count' => 0, 'count_money' => $bet_amt];

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

        show($game_count_all);
    }

    //获取普配牌型，用于前端展示当前牌型
    public function get_poker_type()
    {
        $id = $this->request->post('id/d', 0);
        if ($id <= 0) show([], config('ToConfig.http_code.error'), '露珠ID必填');
        $find = Luzhu::find($id);
        empty($find) && $find = LuZhuBackups::find($id);
        if (empty($find)) show([], config('ToConfig.http_code.error'), '牌型信息不存在');
        //获取台桌开牌信息
        $service = new WorkerOpenPaiService();
        $poker = [];
        switch ($find->game_type) {
            case 2:
                $poker = $service->get_pai_info_lh($find->result_pai);
                break;
            case 3:
                $poker = $service->get_pai_info_bjl($find->result_pai);
                break;
            case 6:
                $poker = $service->get_pai_info_nn($find->result_pai);
                break;
            case 8:
                $poker = $service->get_pai_info_three($find->result_pai);
                break;
        }
        $poker['game_type'] = $find->game_type;
        show($poker);
    }

}