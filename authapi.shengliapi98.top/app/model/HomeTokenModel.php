<?php


namespace app\model;


use app\traits\TraitModel;
use think\Model;

class HomeTokenModel extends Model
{
    public $name='common_home_token';

    use TraitModel;
    //统计在现数
    public function count_user($map = [])
    {
        $date['start']=date("Y-m-d H:i:s",time()-1800);
        //时间统计
        //查询代理下的所有用户
        $count = $this->alias('a')
            ->join('common_user b', 'a.user_id=b.id', 'left')
            ->where($map)
            ->whereTime('a.create_time', '>=', $date['start'])
           ->count();
        return $count;
    }
}