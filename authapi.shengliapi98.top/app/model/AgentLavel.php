<?php


namespace app\model;


use app\traits\TraitModel;
use think\Model;

class AgentLavel extends Model
{
    use TraitModel;
    public $name = 'common_agent_level';

    public static function agent_pid($agent_id)
    {
        if ($agent_id <= 0) return 0;
        //查询用户所有上级,包含自己
        $user = self::where('agent_id', $agent_id)->column('agent_pid');
        array_push($user, $agent_id);
        return $user;
    }

    //修改的时候查询的统计分销不能包括需要修改的人，包括就多算了
    public static function agent_down_all($agent_id, $edit = false)
    {
        if ($agent_id <= 0) return 0;
        return self::agent_rate($agent_id, $edit);
    }

    public static function agent_rate($agent_id, $edit = false)
    {
        $agent_id = intval($agent_id);
        //查询这个人的顶级
        $find = self::where('agent_id', $agent_id)->where('agent_pid_level', 0)->find();

        if (empty($find)) return 0;
        //等于 0是顶级
        $number = 0;
        if ($find->agent_pid == 0) {
            $find->agent_pid=$agent_id;
        } else{
            //不是顶级的时候查询顶级的分销比例
            $number +=UserModel::where('id',$find->agent_pid)->value('agent_rate');
        }

        //查询所有该顶级下面的人员,加上顶级自己的分销
        // $sum = self::where('agent_pid|id', $find->agent_pid);
        $sum = self::where('agent_pid', $find->agent_pid);
        if ($edit) $sum->where('agent_id', '<>', $agent_id);
        $number+=$sum->sum('rate');
        return $number;
    }

}