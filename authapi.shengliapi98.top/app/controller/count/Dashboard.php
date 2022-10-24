<?php

namespace app\controller\count;

use app\controller\Base;
use app\model\GameRecords;
use app\model\HomeTokenModel;
use app\model\MoneyLog;
use app\model\UserModel;

class Dashboard extends Base
{
    //控制面板统计数据

    public function index()
    {
        $start = $this->request->post('start', '');
        $end = $this->request->post('end', '');

        $date = [];
        !empty($start) && $date['start'] = $start;
        !empty($end) && $date['end'] = $end;

        if ($this->request->admin_user->agent == 1) {
            $this->agent_dashboard($date); //代理
        } else {
            $this->admin_dashboard($date);//后台管理
        }
    }

    public function agent_dashboard($date)
    {
        $admin_user = $this->request->admin_user;//当前登录用户信息
        $day = ['start' => date('Y-m-d') . ' 00:00:00', 'end' => date('Y-m-d') . ' 23:59:59'];//今日的时间
        $data = [];
        $user = new UserModel();
        //1 统计注册人数
        $data['count_register'] = $user->count_register([['id|agent_id', 'in', $admin_user['user_all_list']]], $date);
        //2 统计今日注册人数
        $data['today_register'] = $user->count_register([['id|agent_id', 'in', $admin_user['user_all_list']]], $day);
        $MoneyLog = new MoneyLog();
        //3 历史充值总额
        $data['count_recharge'] = $MoneyLog->count_recharge([['status', 'in', MoneyLog::$agent_recharge_status], ['uid', 'in', $admin_user['user_list']]], $date);
        //4 今日充值总额
        $data['today_recharge'] = $MoneyLog->count_recharge([['status', 'in',  MoneyLog::$agent_recharge_status], ['uid', 'in', $admin_user['user_list']]], $day);
        //5 历史提现总额
        $data['count_with'] = $MoneyLog->count_recharge([['status', 'in',  MoneyLog::$agent_withdrawal_status], ['uid', 'in', $admin_user['user_list']]], $date);
        //6 今日提现总额
        $data['today_with'] = $MoneyLog->count_recharge([['status', 'in', MoneyLog::$agent_withdrawal_status], ['uid', 'in', $admin_user['user_list']]], $day);
        //17 历史授权额
        $data['count_grant'] = $MoneyLog->count_recharge([['source_id', '=', $admin_user['id']], ['status', 'in',  MoneyLog::$agent_recharge_status], ['uid', 'in', $admin_user['agent_list']]], $date);
        //18 今日授权额
        $data['today_grant'] = $MoneyLog->count_recharge([['source_id', '=', $admin_user['id']], ['status', 'in',  MoneyLog::$agent_recharge_status], ['uid', 'in', $admin_user['agent_list']]], $day);
        //19 历史扣除授权额
        $data['count_dec_grant'] = $MoneyLog->count_recharge([['source_id', '=', $admin_user['id']], ['status', 'in', MoneyLog::$agent_withdrawal_status], ['uid', 'in', $admin_user['agent_list']]], $date);
        //20 今日扣除授权额
        $data['today_dec_grant'] = $MoneyLog->count_recharge([['source_id', '=', $admin_user['id']], ['status', 'in', MoneyLog::$agent_withdrawal_status], ['uid', 'in', $admin_user['agent_list']]], $day);
        //12 历史盈亏
        $data['count_win_bet'] = $MoneyLog->count_recharge([['status', 'in', MoneyLog::$game_order_status], ['uid', 'in', array_merge_func($admin_user['user_all_list'],$admin_user['agent_all_list'])]], $date);
        //13 今日盈亏
        $data['today_win_bet'] = $MoneyLog->count_recharge([['status', 'BETWEEN', MoneyLog::$game_order_status], ['uid', 'in', array_merge_func($admin_user['user_all_list'],$admin_user['agent_all_list'])]], $day);
        //历史洗码费
        $agent_user_list = array_merge_func($admin_user['agent_list'], $admin_user['user_list']);
        $agent_user_list = array_unique($agent_user_list);
        $data['count_xima'] = $MoneyLog->count_recharge([['status', 'in',MoneyLog::$xima_status], ['uid', 'in', $agent_user_list]], $date);
        //今日洗码费
        $data['today_xima'] = $MoneyLog->count_recharge([['status', 'in', MoneyLog::$xima_status], ['uid', 'in', $agent_user_list]], $day);
        $GameRecords = new GameRecords();
        //7 历史下注总数
        $GameRecordsWhere = [['user_id', 'in', $admin_user['user_all_list']],['close_status','=',2]];
        $data['count_order'] = $GameRecords->count_order_bet($GameRecordsWhere, $date);
        //8 今日下注总数
        $data['today_order'] = $GameRecords->count_order_bet($GameRecordsWhere, $day);
        //10 历史下注总额
        $data['count_order_money'] = $GameRecords->count_order_bet_sum($GameRecordsWhere, $date);
        //11 今日下注总额
        $data['today_order_money'] = $GameRecords->count_order_bet_sum($GameRecordsWhere, $day);
        //14 历史洗码总量
        $data['count_xima_sum'] = $GameRecords->count_order_bet_sum($GameRecordsWhere, $date, 'shuffling_num');
        //15 今日洗码总量
        $data['today_xima_sum'] = $GameRecords->count_order_bet_sum($GameRecordsWhere, $day, 'shuffling_num');
        //16 当前在线
        //在线人数 半小时
        $HomeTokenModel = new HomeTokenModel();
        $data['today_online'] = $HomeTokenModel->count_user([['user_id', 'in', $admin_user['user_all_list']], ['is_fictitious', '=', 0]]);
        admin_show($data);
    }

    public function admin_dashboard($date)
    {
        $admin_user = $this->request->admin_user;//当前登录用户信息
        $day = ['start' => date('Y-m-d') . ' 00:00:00', 'end' => date('Y-m-d') . ' 23:59:59'];//今日的时间
        $data = [];
        $user = new UserModel();
        //1 统计注册人数
        $data['count_register'] = $user->count_register([['is_fictitious', '=', 0]], $date);
        //2 统计今日注册人数
        $data['today_register'] = $user->count_register([['is_fictitious', '=', 0]], $day);
        $MoneyLog = new MoneyLog();
        //3 历史充值总额
        $data['count_recharge'] = $MoneyLog->count_recharge([['status', '=', 101], ['uid', 'in', $admin_user['user_list']]], $date);
        //4 今日充值总额
        $data['today_recharge'] = $MoneyLog->count_recharge([['status', '=', 101], ['uid', 'in', $admin_user['user_list']]], $day);
        //5 历史提现总额
        $data['count_with'] = $MoneyLog->count_recharge([['status', 'in', [102]], ['uid', 'in', $admin_user['user_list']]], $date);
        //6 今日提现总额
        $data['today_with'] = $MoneyLog->count_recharge([['status', 'in', [102]], ['uid', 'in', $admin_user['user_list']]], $day);
        //17 历史授权额
        $data['count_grant'] = $MoneyLog->count_recharge([['source_id', '=', 0], ['status', 'in', [105]], ['uid', 'in', $admin_user['agent_list']]], $date);
        //18 今日授权额
        $data['today_grant'] = $MoneyLog->count_recharge([['source_id', '=', 0], ['status', 'in', [105]], ['uid', 'in', $admin_user['agent_list']]], $day);
        //19 历史扣除授权额
        $data['count_dec_grant'] = $MoneyLog->count_recharge([['source_id', '=', 0], ['status', 'in', [106]], ['uid', 'in', $admin_user['agent_list']]], $date);
        //20 今日扣除授权额
        $data['today_dec_grant'] = $MoneyLog->count_recharge([['source_id', '=', 0], ['status', 'in', [106]], ['uid', 'in', $admin_user['agent_list']]], $day);
        //12 历史盈亏
        $data['count_win_bet'] = $MoneyLog->count_recharge([['status', 'in', MoneyLog::$game_order_status], ['uid', 'in',array_merge_func($admin_user['user_all_list'],$admin_user['agent_all_list'])]], $date);
        //13 今日盈亏
        $data['today_win_bet'] = $MoneyLog->count_recharge([['status', 'in', MoneyLog::$game_order_status], ['uid', 'in', array_merge_func($admin_user['user_all_list'],$admin_user['agent_all_list'])]], $day);
        //历史洗码费
        $agent_user_list = array_merge($admin_user['agent_list'], $admin_user['user_list']);
        $agent_user_list = array_unique($agent_user_list);
        $data['count_xima'] = $MoneyLog->count_recharge([['status', 'in', MoneyLog::$xima_status], ['uid', 'in', $agent_user_list]], $date);
        //今日洗码费
        $data['today_xima'] = $MoneyLog->count_recharge([['status', 'in', MoneyLog::$xima_status], ['uid', 'in', $agent_user_list]], $day);
        $GameRecords = new GameRecords();
        //7 历史下注总数
        //下注需要查询不是 虚拟用户的所有会员
        $GameRecordsWhere = [['user_id', 'in', $admin_user['user_all_list']], ['close_status' ,'=', 2]];

        $data['count_order'] = $GameRecords->count_order_bet($GameRecordsWhere, $date);
        //8 今日下注总数
        $data['today_order'] = $GameRecords->count_order_bet($GameRecordsWhere, $day);
        //10 历史下注总额
        $data['count_order_money'] = $GameRecords->count_order_bet_sum($GameRecordsWhere, $date);
        //11 今日下注总额
        $data['today_order_money'] = $GameRecords->count_order_bet_sum($GameRecordsWhere, $day);
        //14 历史洗码总量
        $data['count_xima_sum'] = $GameRecords->count_order_bet_sum($GameRecordsWhere, $date, 'shuffling_num');
        //15 今日洗码总量
        $data['today_xima_sum'] = $GameRecords->count_order_bet_sum($GameRecordsWhere, $day, 'shuffling_num');

        //在线人数 半小时
        $HomeTokenModel = new HomeTokenModel();
        $data['today_online'] = $HomeTokenModel->count_user([['is_fictitious', '=', 0]]);
        admin_show($data);
    }
}