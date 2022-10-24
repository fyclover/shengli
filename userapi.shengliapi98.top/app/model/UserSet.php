<?php


namespace app\model;


use think\Model;

class UserSet extends Model
{
    public $name = 'dianji_user_set';

    //查询洗马数据
    public static function page_one(int $uid, $lang = null)
    {
        $find = self::where('u_id', $uid)->hidden(['id'])->field('*,id x_id')->find();
        if (empty($find)) {//不存在新增一条默认的
            $id = self::insertGetId(['u_id' => $uid, 'language' => $lang]);
            $find = self::field('*,id x_id')->find($id);
        };
        return $find->toArray();
    }
    
    public static function user_is_xian_hong($user_id,$game_type = 2){

        if ($user_id<= 0){
            return [];
        }

        $user_info = self::where('u_id',$user_id)->find();
        if (empty($user_info)) {
            return [];
        }
        $xianhong = ['xian_hong_max' => 0, 'xian_hong_min' => 0];

        if ($user_info->is_xian_hong == 1) {
            switch ($game_type) {
                case 2:
                    $xianhong = ['xian_hong_max' => $user_info->lh_xian_hong_long_max, 'xian_hong_min' => $user_info->lh_xian_hong_long_min];
                    break;
                case 3:
                    $xianhong = ['xian_hong_max' => $user_info->bjl_xian_hong_zhuang_max, 'xian_hong_min' => $user_info->bjl_xian_hong_zhuang_min];
                    break;
                case 6:
                    $xianhong = ['xian_hong_max' => $user_info->nn_xh_pingbei_max, 'xian_hong_min' => $user_info->nn_xh_pingbei_min];
                    break;
                case 8:
                    $xianhong = ['xian_hong_max' => $user_info->sg_xh_pingbei_max, 'xian_hong_min' => $user_info->sg_xh_pingbei_min];
                    break;
            }
        }
        return $xianhong;

    }
}