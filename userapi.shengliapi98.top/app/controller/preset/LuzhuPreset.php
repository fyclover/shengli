<?php

namespace app\controller\preset;

use app\BaseController;
use app\model\GameRecords;
use app\validate\Preset as validates;
use think\exception\ValidateException;
use \app\model\LuzhuPreset as LuzhuPresetModel;

class LuzhuPreset extends BaseController
{

    public function preset()
    {
        //过滤数据
        $postField = 'result_pai,result,sign,table_id,game_type,xue_number,pu_number,time';
        $post = $this->request->only(explode(',', $postField), 'post', null);
        //验证数据
        try {
            validate(validates::class)->scene('preset')->check($post);
        } catch (ValidateException $e) {
            // 验证失败 输出错误信息
            return show([], config('ToConfig.http_code.error'), $e->getError());
        }
        //判断sign 是否正确
        sign_auth($post);
        unset($post['time'], $post['sign']);
        //判断sign 是否正确结束
        $post ['remark'] = '预设开奖结果';
        $post ['is_status'] = 0;
        $post ['status'] = 1;
        $post ['update_time'] = $post ['create_time'] = time();
        //################查询当前是否预设了
        $where = [
            'is_status' => 0,
            'table_id' => $post['table_id'],
            'game_type' => $post['game_type'],
            'xue_number' => $post['xue_number'],
            'pu_number' => $post['pu_number'],
        ];
        $model = new LuzhuPresetModel();
        $post['result_pai'] = json($post['result_pai']);//到时候看前台传的数据怎么说在对这个字段修改
        $find = $model->whereTime('create_time', 'today')->where($where)->find();
        //################查询当前是否预设 结束
        if ($find) {
            $find->result_pai = $post['result_pai'];
            $save = $find->save();
        } else {
            $save = $model->insert($post);
        }

        if ($save) show([]);
        return show([], config('ToConfig.http_code.error'), '预设失败');
    }


    public function getGameTableCount()
    {
        //过滤数据
        $postField = 'sign,table_id,game_type,xue_number,pu_number,time';
        $post = $this->request->only(explode(',', $postField), 'post', null);
        try {
            validate(validates::class)->scene('count')->check($post);
        } catch (ValidateException $e) {
            return show([], config('ToConfig.http_code.error'), $e->getError());
        }

        sign_auth($post);
        unset($post['time'], $post['sign']);

        $data = [];
        $post['close_status'] = 1;
        switch ($post['game_type']) {
            case 2:
                $data = $this->tiger($post);
                break;
            case 3:
                $data = $this->bjl($post);
                break;
        }
        show($data);
    }

    protected function tiger($post)
    {
        $model = new GameRecords();
        //1:查询庄闲和 下注金额
        $list = $model->whereTime('created_at', 'today')
            ->field('sum(bet_amt) sum_bet_amt,game_peilv_id,table_id,game_type')
            ->where($post)
            ->where('game_peilv_id', 'in', [20, 21, 22])
            ->group('game_peilv_id,table_id')
            ->select();
        if (empty($list)) return [];
        $data = [];
        foreach ($list as $key => $value) {
            if ($value['game_peilv_id'] == 20) {//闲
                $data[$key]['profit_loss'] = $value['sum_bet_amt'] * 0.97;
                $data[$key]['odds'] = 0.97;
                $data[$key]['sum_bet_amt'] = $value['sum_bet_amt'];
                $data[$key]['game_name'] = '龙';
            }
            if ($value['game_peilv_id'] == 21) {//和
                $data[$key]['odds'] = 8;
                $data[$key]['profit_loss'] = $value['sum_bet_amt'] * 8;
                $data[$key]['sum_bet_amt'] = $value['sum_bet_amt'];
                $data[$key]['game_name'] = '虎';
            }
            if ($value['game_peilv_id'] == 22) {//庄
                $data[$key]['odds'] = 0.97;
                $data[$key]['profit_loss'] = $value['sum_bet_amt'] * 0.97;
                $data[$key]['sum_bet_amt'] = $value['sum_bet_amt'];
                $data[$key]['game_name'] = '和';
            }
            $data[$key]['game_peilv_id'] = $value['game_peilv_id'];
        }

        $list = [];
        $i = 0;
        foreach ($data as $key => $value) {
            $list[$i] = $value;
            $list[$i]['profit_loss_all'] = $list[$i]['profit'] = 0;
            foreach ($data as $k => $v) {
                if ($value['game_peilv_id'] != $v['game_peilv_id']) {
                    $list[$i]['profit_loss_all'] += $v['sum_bet_amt'];
                }
            }

            $list[$i]['profit'] = $list[$i]['profit_loss'] - $list[$i]['profit_loss_all'];
            if ($list[$i]['profit'] <= 0){
                $list[$i]['remark'] = '【赚】'.abs($list[$i]['profit']);
            }
            if ($list[$i]['profit'] > 0){
                $list[$i]['remark'] = '【亏】'.abs($list[$i]['profit']);
            }
            $i++;
        }
        return $list;
    }

    protected function bjl($post)
    {
        $model = new GameRecords();
        //1:查询庄闲和 下注金额
        $list = $model->whereTime('created_at', 'today')
            ->field('sum(bet_amt) sum_bet_amt,game_peilv_id,table_id,game_type')
            ->where($post)
            ->where('game_peilv_id', 'in', [6, 7, 8])
            ->group('game_peilv_id,table_id')
            ->select();
        if (empty($list)) return [];
        $data = [];
        foreach ($list as $key => $value) {
            if ($value['game_peilv_id'] == 8) {//庄
                $data[$key]['profit_loss'] = $value['sum_bet_amt'] * 0.95;
                $data[$key]['odds'] = 0.95;
                $data[$key]['sum_bet_amt'] = $value['sum_bet_amt'];
                $data[$key]['game_name'] = '庄';
            }
            if ($value['game_peilv_id'] == 6) {//闲
                $data[$key]['profit_loss'] = $value['sum_bet_amt'] * 1;
                $data[$key]['odds'] = 1;
                $data[$key]['sum_bet_amt'] = $value['sum_bet_amt'];
                $data[$key]['game_name'] = '闲';
            }
            if ($value['game_peilv_id'] == 7) {//和
                $data[$key]['profit_loss'] = $value['sum_bet_amt'] * 8;
                $data[$key]['odds'] = 8;
                $data[$key]['sum_bet_amt'] = $value['sum_bet_amt'];
                $data[$key]['game_name'] = '和';
            }

            $data[$key]['game_peilv_id'] = $value['game_peilv_id'];
        }

        $list = [];
        $i = 0;
        foreach ($data as $key => $value) {
            $list[$i] = $value;
            $list[$i]['profit_loss_all'] = $list[$i]['profit'] = 0;
            foreach ($data as $k => $v) {
                if ($value['game_peilv_id'] != $v['game_peilv_id']) {
                    $list[$i]['profit_loss_all'] += $v['sum_bet_amt'];
                }
            }

            $list[$i]['profit'] = $list[$i]['profit_loss'] - $list[$i]['profit_loss_all'];
            if ($list[$i]['profit'] <= 0){
                $list[$i]['remark'] = '【赚】'.abs($list[$i]['profit']);
            }
            if ($list[$i]['profit'] > 0){
                $list[$i]['remark'] = '【亏】'.abs($list[$i]['profit']);
            }
            $i++;
        }
        return $list;
    }

}