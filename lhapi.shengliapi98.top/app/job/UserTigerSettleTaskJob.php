<?php
namespace app\job;

use app\service\CardDragonTigerService;
use think\facade\Log;
use think\queue\Job;

/**
 * 龙虎开拍后结算
 * Class UserTigerSettleTaskJob
 * @package app\job
 */
class UserTigerSettleTaskJob
{
    public function fire(Job $job, $data = null)
    {
        $info = $data;

        #逻辑执行
        $isJobDone = $this->doHelloJob($data);

        if ($isJobDone){
            $job->delete();
            return true;
        }
        #逻辑执行结束
        if ($job->attempts() > 3) {
            Log::info('用户结算执行失败:'.json_encode($info));
            $job->delete();
            return true;
            //通过这个方法可以检查这个任务已经重试了几次了
        }
    }

    private function doHelloJob($data) {
        // 根据消息中的数据进行实际的业务处理...
        if (empty($data)){
            return true;
        }

        $luzhu_id = $data['luzhu_id'];
        unset($data['luzhu_id']);
        $card_service = new CardDragonTigerService();
        $res = $card_service->user_settlement($luzhu_id,$data);
        if (!$res){
            return false;
        }
        return true;
    }
}