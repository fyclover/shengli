<?php


namespace app\controller\game;

use app\model\Luzhu;
use app\controller\Base;
use app\service\WorkerOpenPaiService;

class GameInfo extends Base
{
    //获取普配牌型，用于前端展示当前牌型
    public function get_poker_type()
    {
        $id = $this->request->post('id/d', 0);
        if ($id <= 0) show([], config('ToConfig.http_code.error'), '露珠ID必填');
        $find = Luzhu::find($id);
        if (empty($find)) show([], config('ToConfig.http_code.error'), '牌型信息不存在');
        //获取台桌开牌信息
        $service = new WorkerOpenPaiService();
        $poker = [];
        switch ($find->game_type) {
            case 2:
                $poker = $service->get_pai_info_lh($find->result_pai);
                break;
        }
        show($poker);
    }
}