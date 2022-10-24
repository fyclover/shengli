<?php


namespace app\traits;


use app\model\AgentLavel;
use app\model\GameRecords;
use app\model\MoneyLog;

trait GetTreeTrait
{
    public function fillModelBackends($models, $id = 'id', $pid = 'pid')
    {
        $list = [];
        foreach ($models as $model) {

            $listItem = $this->fillModelBackend($model, $id, $pid);
            $list[] = $listItem;
        }
        return $list;
    }

    public function fillModelBackend($model, $id, $pid)
    {
        if ($model[$id] == 0) return $model;
        $loadList = $this->model->cache(60)->where([$pid => $model[$id]])->select()->toArray();
        if (count($loadList) > 0) {
            $model['children'] = $this->fillModelBackends($loadList, $id, $pid);
        }
        //else {
        //    $model['children'] = [];
        //}
        return $model;
    }


    public function agentFillModelBackends($models, $id = 'id', $pid = 'pid', $date = [])
    {
        $list = [];
        foreach ($models as $key=>&$model) {
            //查询当前的洗码率
            $model['xima_lv']  = \app\model\UserSet::where('u_id',$model['id'])->value('xima_lv');
            $model['xima_lv'] = $model['xima_lv'] == null ? 0:$model['xima_lv'];
            $listItem = $this->agentFillModelBackend($model, $id, $pid, $date);
            $list[] = $listItem;
        }
        return $list;
    }

    public function agentFillModelBackend($model, $id, $pid, $date = [])
    {
        if ($model[$id] == 0) return $model;
        $loadList = $this->model->where([$pid => $model[$id]])->select()->toArray();

        //1 查询当前代理下的所有代理商
        $agent_column = (new AgentLavel())->where('agent_pid', $model[$id])->column('agent_id');
        //查询当前代理的代理商名称
        $model['agent_name'] = '';
        if ($model['agent_id'] > 0) {
            $model['agent_name'] = $this->model->where('id', $model['agent_id'])->value('user_name');
        }
        array_unshift($agent_column, $model[$id]);
        $model['bet_amt'] = 0;
        $model['win_amt'] = 0;
        $model['shuffling_num'] = 0;
        $model['is_ok_shuffling_num'] = 0;
        $model['is_no_shuffling_num'] = 0;
        //2 代理商下的所有用户   如果下面有用户，否则统计自己的帐号的 钱数
        if (!empty($agent_column)) {
            //组装时间查询
            $GameRecordsModel =  new  GameRecords();
            $GameRecords = $GameRecordsModel->this_where_date_model($GameRecordsModel,$date,'created_at');


            $MoneyLogModel = new  MoneyLog();
            $MoneyLogModelTime = $MoneyLogModel->this_where_date_model($MoneyLogModel,$date,'create_time');

            if (count($loadList) > 0) {
                // 还有下一级代理
                $user_column = $this->model->where('type', 2)->where('agent_id', 'in', $agent_column)->column('id');

                if (!empty($user_column)) {
                    //3统计当前所有用户的 总投注，总输赢 总洗码
                    $count = $GameRecords->field('sum(bet_amt) bet_amt,sum(win_amt) win_amt,sum(shuffling_num) shuffling_num')
                        ->where('user_id', 'in', $user_column)
                        ->where('close_status',2)
                        ->find();

                    //查询当前所有用户的总输赢
                    $count->win_amt = $MoneyLogModelTime->where('status','in',MoneyLog::$game_order_status)
                        ->where('uid', 'in', $user_column)
                        ->sum('money');

                    //统计当前用户的洗码免佣和非免佣
                    $count_exempt = $GameRecords->field('sum(shuffling_num) shuffling_num,is_exempt')
                        ->where('user_id', 'in', $user_column)
                        ->where('close_status',2)
                        ->group('is_exempt')
                        ->select();

                }
            } else {
                // 没有了，就当下的
                // 统计当下帐号的 数据
                $temp_user_id = $model[$id];
                $count = $GameRecords->field('sum(bet_amt) bet_amt,sum(win_amt) win_amt,sum(shuffling_num) shuffling_num')
                    ->where('user_id', $temp_user_id)
                    ->where('close_status',2)
                    ->find();
                //查询当前所有用户的总输赢
                $count->win_amt = $MoneyLogModelTime->where('status','in',MoneyLog::$game_order_status)->where('uid',$temp_user_id)->sum('money');

                //统计当前用户的洗码免佣和非免佣
                $count_exempt = $GameRecords->field('sum(shuffling_num) shuffling_num,is_exempt')
                    ->where('user_id', $temp_user_id)
                    ->where('close_status',2)
                    ->group('is_exempt')
                    ->select();
            }

            if (!empty($count)) {
                $model['bet_amt'] = intval($count->bet_amt);
                $model['win_amt'] = floatval($count->win_amt);
                $model['shuffling_num'] = intval($count->shuffling_num);
            }
            if (!empty($count_exempt)) {
                foreach ($count_exempt as $key => $value) {
                    $value['shuffling_num'] = intval($value['shuffling_num']);
                    if ($value['is_exempt'] == 1) {
                        $model['is_ok_shuffling_num'] = $value['shuffling_num'];
                    } else {
                        $model['is_no_shuffling_num'] = $value['shuffling_num'];
                    }
                }
            }

        }
        // 如果有下级，继续递归调用
        if (count($loadList) > 0) {
            $model['children'] = $this->agentFillModelBackends($loadList, $id, $pid,$date);
        }
        // 返回结果
        return $model;
    }
}