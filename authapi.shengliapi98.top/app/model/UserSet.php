<?php


namespace app\model;


use think\Model;

class UserSet extends Model
{
    public $name = 'dianji_user_set';

    //查询洗马数据
    public static function page_one(int $uid)
    {
        $find = self::where('u_id',$uid)->hidden(['id'])->field('*,id x_id')->find();
        if (empty($find)) {//不存在新增一条默认的
            $id  =  self::insertGetId(['u_id'=>$uid]);
            $find = self::field('*,id x_id')->find($id);
        };
        return $find->toArray();
    }

    public static function user_insert($u_id = 0, $xima = 0, $is_xian_hong = 0, $post = false)
    {
        $find = self::where('u_id',$u_id)->find();
        if ($find) {
            //看看是否存在多条.实在是容易出现多条，不知道哪里引起
            $column = self::where('u_id',$u_id)->order('id desc')->column('id');
            if (count($column) > 1){
                for ($i=0;$i < count($column)-1;$i++){
                    self::where('id',$column[$i])->delete();
                }
            }

            if ($xima != $find->xima_lv){
                return false;
                self::where('u_id',$u_id)->update(['xima_lv'=>$xima]);
            }
            return true;
        };

        if ($post){
            $post =  array_filter($post);
            return self::insert($post);
        }

        return self::insert(['u_id' => $u_id, 'xima_lv' => $xima, 'is_xian_hong' => $is_xian_hong]);

    }
}