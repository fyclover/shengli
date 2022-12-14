<?php


namespace app\controller\desktop;

use app\controller\Base;

use app\model\Luzhu as models;
use app\model\MoneyLog;
use app\traits\PublicCrudTrait;


class VoidLuzhu extends Base
{
    protected $model;
    use PublicCrudTrait;

    /**
     * 作废露珠控制器
     */
    public function initialize()
    {
        $this->model = new models();
        parent::initialize(); // TODO: Change the autogenerated stub
    }

    /**
     * 列表
     */
    public function index()
    {
        //当前页
        $page = $this->request->post('page', 1);
        //每页显示数量
        $limit = $this->request->post('limit', 10);
        $post = $this->request->post();
        $map = $date = [];
        $map[] = ['a.status', '=', 3];
        isset($post['table_id']) && $map [] = ['table_id', '=', $post['table_id']];
        $list = $this->model->page_list($map, $limit, $page, $date);
        return $this->success($list);
    }

    //作废露珠退还情况 数据
    public function retreat()
    {
        $id = $this->request->post('id/d', 0);
        if ($id <= 0) $this->failed('露珠id参数错误');
        //查询退还日志
        $log = new MoneyLog();

        $map = array();
        $map[] = ['a.source_id', '=', $id];
        $map[] = ['a.status', 'between', [509,510]];
        $log_list =  $log->alias('a')
            ->field('a.*,user_name')
            ->where($map)
            ->join('common_user u','a.uid=u.id','left')
            ->select();


        return $this->success($log_list);
    }
}
