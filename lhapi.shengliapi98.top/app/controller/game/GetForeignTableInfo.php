<?php

namespace app\controller\game;

use app\BaseController;
use app\model\Luzhu;
use app\model\LuzhuHeguan;
use app\model\LuzhuPreset;
use app\model\Table;
use app\service\CardDragonTigerService;
use app\job\TableEndTaskJob;
use app\validate\BetOrder as validates;
use think\exception\ValidateException;
use think\facade\Queue;

class GetForeignTableInfo extends BaseController
{
    public function get_table_video()
    {
        $params = $this->request->param();
        $returnData = array();
        $info = Table::order('id desc')->find($params['tableId']);
        $returnData['video_near'] = $info['video_near'];
        $returnData['video_far'] = $info['video_far'];
        // 返回数据
        show($returnData, 1);
    }

    //获取荷官台桌露珠信息
    public function get_hg_lz_list(): string
    {
        $params = $this->request->param();
        $returnData = LuzhuHeguan::LuZhuList($params);
        show($returnData, 1);
    }

    //获取台桌露珠信息
    public function get_lz_list(): string
    {
        $params = $this->request->param();
        $returnData = Luzhu::LuZhuList($params);
        show($returnData, 1);
    }

    // 获取台桌列表
    public function get_table_list(): string
    {
        $gameTypeView = array(
            '1' => '_nn',
            '2' => '_lh',
            '3' => '_bjl'
        );
        $infos = Table::where(['status' => 1])->order('id asc')->select()->toArray();
        empty($infos) && show($infos, 1);
        foreach ($infos as $k => $v) {
            // 设置台桌类型 对应的 view 文件
            $infos[$k]['viewType'] = $gameTypeView[$v['game_type']];
            $number = rand(100, 3000);// 随机人数
            $infos[$k]['number'] = $number;
            // 获取靴号
            //正式需要加上时间查询
            $luZhu = Luzhu::where(['status' => 1, 'table_id' => $v['id']])->whereTime('create_time', 'today')->select()->toArray();

            if (isset($luZhu['xue_number'])) {
                $infos[$k]['xue_number'] = $luZhu['xue_number'];
                continue;
            }
            $infos[$k]['xue_number'] = 1;
        }
        show($infos, 1);
    }

    // 获取统计数据
    public function get_table_count(): string
    {
        $params = $this->request->param();
        $map = array();
        $map['status'] = 1;
        if (!isset($params['tableId']) || !isset($params['xue']) || !isset($params['gameType'])) {
            show([], 0,'');
        }

        $map['table_id'] = $params['tableId'];
        $map['xue_number'] = $params['xue'];
        $map['game_type'] = $params['gameType']; // 代表百家乐
		
		$nowTime = time();
		$startTime = strtotime(date("Y-m-d 09:00:00", time()));
		// 如果小于，则算前一天的
		if ($nowTime < $startTime) {
		    $startTime = $startTime - (24 * 60 * 60);
		} else {
		    // 保持不变 这样做到 自动更新 露珠
		}

        $returnData = array();
		$returnData['zhuang'] = Luzhu::whereTime('create_time','>=', date('Y-m-d H:i:s',$startTime))->where('result', 'like', '1|%')->where($map)->order('id asc')->count();
        $returnData['xian'] = Luzhu::whereTime('create_time','>=', date('Y-m-d H:i:s',$startTime))->where('result', 'like', '2|%')->where($map)->order('id asc')->count();
        $returnData['he'] = Luzhu::whereTime('create_time','>=', date('Y-m-d H:i:s',$startTime))->where('result', 'like', '3|%')->where($map)->order('id asc')->count();
        $returnData['zhuangDui'] = Luzhu::whereTime('create_time','>=', date('Y-m-d H:i:s',$startTime))->where('result', 'like', '%|1')->where($map)->order('id asc')->count();
        $returnData['xianDui'] = Luzhu::whereTime('create_time','>=', date('Y-m-d H:i:s',$startTime))->where('result', 'like', '%|2')->where($map)->order('id asc')->count();
        $returnData['zhuangXianDui'] = Luzhu::whereTime('create_time','>=', date('Y-m-d H:i:s',$startTime))->where('result', 'like', '%|3')->where($map)->order('id asc')->count();
        $returnData['zhuangDui'] += $returnData['zhuangXianDui'];
        $returnData['xianDui'] += $returnData['zhuangXianDui'];
        // 返回数据
        show($returnData, 1);
    }

    //获取发送的数据 荷官开牌
    public function set_post_data(): string
    {
        $postField = 'gameType,tableId,xueNumber,puNumber,result,ext,pai_result';
        $params = $this->request->only(explode(',', $postField), 'param', null);

        try {
            validate(validates::class)->scene('lz_post')->check($params);
        } catch (ValidateException $e) {
            return show([], config('ToConfig.http_code.error'), $e->getError());
        }
        $map = array();
        $map['status'] = 1;
        $map['table_id'] = $params['tableId'];
        $map['xue_number'] = $params['xueNumber'];
        $map['pu_number'] = $params['puNumber'];
        $map['game_type'] = $params['gameType'];
		
		$nowTime = time();
		$startTime = strtotime(date("Y-m-d 09:00:00", time()));
		// 如果小于，则算前一天的
		if ($nowTime < $startTime) {
		    $startTime = $startTime - (24 * 60 * 60);
		} else {
		    // 保持不变 这样做到 自动更新 露珠
		}

        //查询当日最新的一铺牌
        $info = Luzhu::whereTime('create_time','>=', date('Y-m-d H:i:s',$startTime))->where('result','<>',0)->where($map)->find();
        if (!empty($info)) show($info, 0, '数据重复上传');

        #####开始预设###########
        //查询是否有预设的开牌信息
        $presetInfo = LuzhuPreset::LuZhuPresetFind($map);
        $map['update_time'] = $map['create_time'] = time();
        $HeguanLuzhu = $map;

        $id = 0;
        if ($presetInfo){
            //插入当前信息
            $id = $presetInfo['id'];
            $map['result'] = $presetInfo['result'];
            $map['result_pai'] = $presetInfo['result_pai'];
        }else{
            //插入当前信息
            $map['result'] = intval($params['result']) . '|' . intval($params['ext']);
            $map['result_pai'] = json_encode($params['pai_result']);
        }
        //荷官正常露珠
        $HeguanLuzhu['result'] = intval($params['result']) . '|' . intval($params['ext']);
        $HeguanLuzhu['result_pai'] = json_encode($params['pai_result']);
        #####结束预设###########
        switch ($map['game_type']){
            case 2:
                //龙虎开牌
                $card = new CardDragonTigerService();
                return $card->open_game($map,$HeguanLuzhu,$id);
            default:
                show([],404,'game_type错误！');
        }
    }

    //删除指定露珠
    public function lz_delete(): string
    {
        $params = $this->request->param();
        $map = array();
        $map['table_id'] = $params['tableId'];
        $map['xue_number'] = $params['num_xue'];
        $map['pu_number'] = $params['num_pu'];
        Luzhu::where($map)->delete();
        show([], config('ToConfig.http_code.error'));
    }

    //清除一张台桌露珠
    public function lz_table_delete(): string
    {
        $table_id = $this->request->param('tableId', 0);
        if ($table_id <= 0) show([], config('ToConfig.http_code.error'), '台桌id错误');
        $del = Luzhu::where(['table_id' => $table_id])->delete();
        if ($del) show([]);
        show([], config('ToConfig.http_code.error'));
    }

    //设置靴号 荷官主动换靴号
    public function set_xue_number(): string
    {
        //过滤数据
        $postField = 'tableId,num_xue,gameType';
        $post = $this->request->only(explode(',', $postField), 'param', null);

        try {
            validate(validates::class)->scene('lz_set_xue')->check($post);
        } catch (ValidateException $e) {
            return show([], config('ToConfig.http_code.error'), $e->getError());
        }
        // 查询当前最新的靴号
        //主动换靴号，获取最新的靴号 +1；铺号为 0
        // 缺少时间
        $nowTime = time();
        $startTime = strtotime(date("Y-m-d 09:00:00", time()));
        // 如果小于，则算前一天的
       if ($nowTime < $startTime) {
           $startTime = $startTime - (24 * 60 * 60);
       } else {
           // 保持不变 这样做到 自动更新 露珠
       }
        //取才创建时间最后一条数据
        $find = Luzhu::where('table_id', $post['tableId'])->whereTime('create_time','>=', date('Y-m-d H:i:s',$startTime))->order('id desc')->find();

        if ($find) {
            $xue_number['xue_number'] = $find->xue_number + 1;
        } else {
            $xue_number['xue_number'] = 1;
        }
        $post['status'] = 1;
        $post['table_id'] = $post['tableId'];
        $post['xue_number'] = $xue_number['xue_number'];
        $post['pu_number'] = 1;
        $post['update_time'] = $post['create_time'] = time();
        $post['game_type'] = $post['gameType'];
        $post['result'] = 0;
        $post['result_pai'] = 0;

        $save = (new Luzhu())->save($post);
        if ($save) show($post);
        show($post, config('ToConfig.http_code.error'));
    }

    //开局信号
    public function set_start_signal(): string
    {
        $table_id = $this->request->param('tableId', 0);
        $time = $this->request->param('time', 0);
        if ($table_id <= 0) show([], config('ToConfig.http_code.error'), 'tableId参数错误');
        $data = [
            'start_time' => time(),
            'status' => 1,
            'run_status' => 1,
            'wash_status'=>0,
            'update_time' => time(),
        ];
        
        if($time > 0){
            $data['countdown_time'] = $time;
        };
        
        $save = Table::where('id', $table_id)
            ->update($data);
        if (!$save) {
            show($data, config('ToConfig.http_code.error'));
        }
        $data['table_id'] = $table_id;
        Queue::later($time, TableEndTaskJob::class, $data,'lh_end_queue');
        redis()->del('table_info_'.$table_id);
        redis()->set('table_set_start_signal_'.$table_id,$table_id,$time+5);//储存redis,作为是否发送给前台倒计时标识
        show($data);
    }

    //结束信号
    public function set_end_signal(): string
    {
        $table_id = $this->request->param('tableId', 0);
        if ($table_id <= 0) show([], config('ToConfig.http_code.error'));
        $save = Table::where('id', $table_id)
            ->update([
                'status' => 1,
                'run_status' => 2,
                'wash_status'=>0,
                'update_time' => time(),
            ]);
        if ($save) show([]);
        show([], config('ToConfig.http_code.error'));
    }

    //台桌信息 靴号 铺号
    public function get_table_info()
    {
        $params = $this->request->param();
        $returnData = array();
        $info = Table::order('id desc')->find($params['tableId']);
        // 发给前台的 数据
        $returnData['lu_zhu_name'] = $info['lu_zhu_name'];
        $returnData['right_money_banker_player'] = $info['xian_hong_zhuang_xian_usd'];
        $returnData['right_money_banker_player_cny'] = $info['xian_hong_zhuang_xian_cny'];
        $returnData['right_money_tie'] = $info['xian_hong_he_usd'];
        $returnData['right_money_tie_cny'] = $info['xian_hong_he_cny'];
        $returnData['right_money_pair'] = $info['xian_hong_duizi_usd'];
        $returnData['right_money_pair_cny'] = $info['xian_hong_duizi_cny'];
        $returnData['video_near'] = $info['video_near'];
        $returnData['video_far'] = $info['video_far'];
        $returnData['time_start'] = $info['countdown_time'];

        // 获取最新的 靴号，铺号
        $xun = bureau_number($params['tableId'],true);
        $returnData['id'] = $info['id'];
        $returnData['num_pu'] = $xun['xue']['pu_number'];
        $returnData['num_xue'] = $xun['xue']['xue_number'];

        // 返回数据
        show($returnData, 1);

    }
    //台桌信息
    public function get_table_wash_brand()
    {
        $tableId = $this->request->param('tableId',0);
        if ($tableId <=0 ) show([], config('ToConfig.http_code.error'),'台座ID必填');
        $table  = Table::where('id',$tableId)->find();
        $status = $table->wash_status == 0 ? 1 : 0;
        $table->save(['wash_status'=>$status]);
        show([], 1);
    }

}