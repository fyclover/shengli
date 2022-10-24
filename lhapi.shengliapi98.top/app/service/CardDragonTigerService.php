<?php


namespace app\service;


use app\job\BetMoneyLogInsert;
use app\model\GameRecords;
use app\model\Luzhu;
use app\model\LuzhuHeguan;
use app\model\LuzhuPreset;
use app\model\UserModel;
use app\job\UserTigerSettleTaskJob;
use think\Exception;
use think\facade\Db;
use think\facade\Queue;

class CardDragonTigerService extends CardServiceBase
{
    /**
     * 龙虎牌面结算
     * 1 计算完成 修改用户总赢。修改用户金额
     * 2 游戏结果计算
     * 3 洗码费计算(如果出现台面作废，洗码费就不写入，所有必须是正常开牌的才把经费通过定时任务发放)
     * 4 代理计算
     * 5 开牌以后 开牌以后的下注信息 转移 插入到 资金记录表 redis  TransferBetService 执行
     * @param $post
     */
    public function open_game($post,$HeguanLuzhu,$id): string
    {
        $luzhuModel = new Luzhu();
        //插入开牌信息
        $save = false;
        Db::startTrans();
        try {
           $luzhuModel->save($post);
            LuzhuHeguan::insert($HeguanLuzhu);
            $save = true;
            Db::commit();
        }catch (\Exception $e){
            $save = false;
            Db::commit();
        }

        //开牌的时候 把开牌信息存入到redis...用作桌面 牌显示
        redis()->set('table_id_' . $post['table_id'] . '_' . $post['game_type'], $post['result_pai'], 5);
        if (!$save) show([], 0, '开牌失败');
        if ($id >0 ) LuzhuPreset::IsStatus($id);
        $post['luzhu_id'] = $luzhuModel->id;
        $queue = Queue::later(1, UserTigerSettleTaskJob::class, $post,'lh_open_queue');
        if ($queue == false) {
            show([], 0, 'dismiss job queue went wrong');
        }
        show([]);
    }

    //用户结算以及写入订单信息,单独写方便后面检查
    public function user_settlement($luzhu_id, $post): bool
    {
        $oddsModel = new GameRecords();     // 初始化 model
        // 获取 1 小时内 未结算 的投注数据
        $win_the_prize = $oddsModel
            ->whereTime('created_at', date("Y-m-d H:i:s", strtotime("-1 hour")))
            ->where([
                'table_id' => $post['table_id'], 'game_type' => $post['game_type'],
                'xue_number' => $post['xue_number'], 'pu_number' => $post['pu_number'],
                'close_status' => 1,
            ])
            ->select()
            ->toArray();

        if (empty($win_the_prize)) return true; // 如果没有投注数据

        $data = [];//保存当前修改下注信息数据
        $user = [];//保存修改用户获取金额
        $user_paiCai = []; // 用户派彩统计
        $card = new OpenDragonTigerService();   //获得本地本次开牌结果   result_pai ：{"1":"13|f","2":"6|m"}
        $pai_result = $card->runs(json_decode($post['result_pai'], true));      // result_pai 的翻译器  获取谁赢了 点数 图片是啥

        // 遍历 下注结果  查询当前用户是否中奖   返回 false 是没中奖  true是中奖
        foreach ($win_the_prize as $key => $value) {
            $data[$key]['detail'] = $value['detail'] .
                '-购买：' . $card->user_pai_chinese($value['result']) .
                ',开：' . $card->pai_chinese($pai_result) .
                '||本次结果记录' . json_encode($pai_result);      // 组装 remark 备注信息
            $user_win_or_not = $card->user_win_or_not(intval($value['result']), $pai_result);   // 判断 是否 赢钱了   result : 20 龙 21 虎 22 和   pai_result: 1龙 2虎 3和
            $data[$key]['close_status'] = 2;                    // 修改下注结算状态 2 牌面已结算
            $data[$key]['win_amt'] = $value['bet_amt'] * -1;    // 会员总赢默认为 为下注金额
            $data[$key]['id'] = $value['id'];                   // 保留ID
            $data[$key]['lu_zhu_id'] = $luzhu_id;               // 更新露珠 ID
            //判断该用户是否 赢了。 true 赢   false 输  用户购买 结果和开奖结果是否一样
            if ($user_win_or_not == true) {
                //会员下注金额*比例
                $money = $value['game_peilv'] * $value['bet_amt'];
                $data[$key]['win_amt'] = $money;//计算会员总赢  赔率 * 下注金额
                $data[$key]['delta_amt'] = $money + $value['bet_amt'];//计算会员变化金额.盈利+下注本金
                //
                $user[$key]['money_balance_add_temp'] = $money + $value['bet_amt']; //用户需要增加的金额 盈利+下注本金
                $user[$key]['user_id'] = $value['user_id'];
                $user[$key]['bet_amt'] = $value['bet_amt'];
                $user[$key]['table_id'] = $value['table_id'];
                $user[$key]['game_type'] = $value['game_type'];
                $user[$key]['win'] = $money;
                // 增加 专门的用户派彩的统计
                $user_paiCai[$key]['user_id'] = $value['user_id'];
                $user_paiCai[$key]['bet_amt'] = $value['bet_amt'];
                $user_paiCai[$key]['table_id'] = $value['table_id'];
                $user_paiCai[$key]['game_type'] = $value['game_type'];
                $user_paiCai[$key]['win'] = $money;
            }else{
                // 增加 专门的用户派彩的统计
                $user_paiCai[$key]['user_id'] = $value['user_id'];
                $user_paiCai[$key]['bet_amt'] = $value['bet_amt'];
                $user_paiCai[$key]['table_id'] = $value['table_id'];
                $user_paiCai[$key]['game_type'] = $value['game_type'];
                $user_paiCai[$key]['win'] = $value['bet_amt'] * -1;

                // 如果 输了 买了 龙虎  开 和局的情况，特殊处理一下 开始  不算 洗码
                if ($pai_result['win'] == 3){   // 结果和局
                    if($value['result'] == 20 || $value['result'] == 21){   // 用户购买 龙20 或者 虎21
                        // 处理 派彩逻辑
                        $user_paiCai[$key]['win'] = 0;  // 此刻派彩金额为0
                        // 处理 下注数据
                        $data[$key]['win_amt'] = 0;             // 赢钱的数据为0
                        $data[$key]['shuffling_num'] = 0;       // 洗码量 为0
                        // 处理用户返钱的事情
                        $user[$key]['money_balance_add_temp'] = $value['bet_amt']; //用户需要增加的金额 盈利+下注本金
                        $user[$key]['user_id'] = $value['user_id'];
                        $user[$key]['bet_amt'] = $value['bet_amt'];
                        $user[$key]['table_id'] = $value['table_id'];
                        $user[$key]['game_type'] = $value['game_type'];
                        $user[$key]['win'] = 0;
                        // 处理 结束了
                    }
                }
                // 如果 输了 买了 龙虎  开 和局的情况，特殊处理一下 结束
            }
        }
        // 遍历完成
        ######统计用户总赢总输。用于派彩显示 开始######
        if (!empty($user_paiCai)) {
            $userCount = [];
            foreach ($user_paiCai as $v) {
                if (array_key_exists($v['user_id'], $userCount)) {
                    $userCount[$v['user_id']]['win'] += $v['win'];
                } else {
                    $userCount[$v['user_id']] = $v;
                }
            }
            //存入redis
            foreach ($userCount as $v) {
                redis()->set('user_' . $v['user_id'] . '_table_id_' . $v['table_id'] . '_' . $v['game_type'], $v['win'], 5);
            }
        }
        ######统计用户总赢总输。用于派彩显示 结束######

        Db::startTrans();       // 启动事务
        try {
            // 用户下注 赢钱的  以及 开和 退钱的
            if (!empty($user)) {
                foreach ($user as $key => $value) {
                    //查询用户当前余额
                    $find = (new UserModel())->where('id', $value['user_id'])->lock(true)->find();
                    $save = array();//查询用户当前余额
                    $save['money_before'] = $find->money_balance;
                    $save['money_end'] = $find->money_balance + $value['money_balance_add_temp'];
                    $save['uid'] = $value['user_id'];
                    $save['type'] = 1;
                    $save['status'] = 502;
                    $save['source_id'] = $luzhu_id;
                    $save['money'] = $value['money_balance_add_temp'];
                    $save['create_time'] = date('Y-m-d H:i:s');
                    $save['mark'] = '下注结算--变化:' . $value['money_balance_add_temp'] . '下注：' . $value['bet_amt'] . '总赢：' . $value['win'];
                    $user_update = (new UserModel())->where('id', $value['user_id'])->inc('money_balance', $value['money_balance_add_temp'])->update();
                    if ($user_update){
                        redis()->LPUSH('bet_settlement_money_log',json_encode($save));
                    }
                }
            }
            !empty($data) && $oddsModel->saveAll($data);    //  修改用户下注数据状态
            Db::commit();       // 提交事务
        } catch (\Exception $e) {
            Db::rollback();     // 回滚事务
            return false;
        }
        //执行用户资金写入
        Queue::later(2, BetMoneyLogInsert::class, $post,'lh_money_log_queue');
        return true;
    }
}

