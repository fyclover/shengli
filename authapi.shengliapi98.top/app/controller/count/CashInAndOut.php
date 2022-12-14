<?php


namespace app\controller\count;


use app\controller\Base;
use app\model\MoneyLog;

class CashInAndOut extends Base
{
    protected $model;

    public function initialize()
    {
        $this->model = new MoneyLog();
        parent::initialize(); // TODO: Change the autogenerated stub
    }

    //统计出入金
    public function index()
    {
        $post = array_filter($this->request->post());
        isset($post['start']) && $date['start'] = $post['start'];
        isset($post['end']) && $date['end'] = $post['end'];
        $res =$this->model;
        if (isset($date['start']) && isset($date['end'])) {
            $res = $res->whereTime('a.create_time', 'between', [$date['start'], $date['end']]);
        } elseif (isset($date['start'])) {
            $res = $res->whereTime('a.create_time', '>=', $date['start']);
        } elseif (isset($date['end'])) {
            $res = $res->whereTime('a.create_time', '<=', $date['end']);
        }
        $data = $this->count_in_and_out($res);
        return admin_show($data);
    }

    //后台上下分出入金
    public function count_in_and_out($res)
    {
        return $res->alias('a')->field('sum(money) money,a.status as attrs,a.status')
            ->where('a.type', 3)
            ->join('common_user b','a.uid=b.id','left')
            ->where('is_fictitious', 0)
            ->where(function ($query) {
                $query->whereOr('a.status','in', '101,102');
            })
            ->group('status')
            ->select();
    }
}