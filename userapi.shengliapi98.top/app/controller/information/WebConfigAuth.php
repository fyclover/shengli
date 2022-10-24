<?php

namespace app\controller\information;

use app\model\Notice;
use app\model\Notify;
use app\controller\Base;

class WebConfigAuth extends Base
{
    //公告 需要权限才能拿到个人的公告
    public function notice_list()
    {
        //获取公告位置
        $id= $this->request->param('id/d',1);
        $map =['status'=>1,'position'=>$id];
        $web_maintain = 0;
        //查询网址是否在维护中。。。 //$id ==3 时是查询维护中的通告
        if ($id == 3){
            $list = Notice ::where($map)->find();
            $get_config = get_config('web_maintain');
            !empty($get_config) && $web_maintain=$get_config->value;
            $list['web_maintain_status'] = $web_maintain;
        }else{
            $list = Notice ::where($map)->select();
        }

        //查询该用户是否有作废露珠通告
        $notify = Notify::where(['status'=>1,'type'=>2])
            ->whereTime('create_time','>=',time()-(24*60*60))
            ->whereLike('unique','%'.self::$user['id'].'-%')
            ->field('mark')
            ->find();

        if (!empty($notify)){
            if (isset($list->toArray()[0])){
                $list =  $list->toArray();
               $list [0]['content'] .= '||3.'.$notify->mark;
            }else{
                $list->content .= '||'.$notify->mark;
            }
        }
        show($list);
    }
}