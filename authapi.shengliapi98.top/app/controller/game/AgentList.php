<?php


namespace app\controller\game;

use app\controller\Base;

use app\model\UserModel as models;
use app\traits\GatUserAuthTrait;
use app\traits\GetTreeTrait;

class AgentList extends Base
{
    protected $model;
    use  GetTreeTrait;
    use GatUserAuthTrait;
    /**
     * 关系链控制器
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
        //查询搜索条件
        $post = array_filter($this->request->post());
        if ($this->request->admin_user->agent == 1) { //不是代理就查询所有顶级。是代理就查询代理本身
            $map[] =['id','=',$this->request->admin_user->id];
        }else{
            $map[] =['agent_id','=',0];
        }

        $start = $this->request->post('start',null);
        $ent = $this->request->post('end',null);
        $date=[];
        if (!empty($start)) $date['start'] =$start;
        if (!empty($start)) $date['end'] =$ent;
        if (isset($post['user_name']) && !empty($post['user_name'])) {
            unset($map);
            $map[] = $this->map_user_name($post['user_name'],'direct_list','id');
            $list = $this->model->where($map)->paginate(['list_rows' => $limit, 'page' => $page], false)
                ->toArray();
        }else{
            $list = $this->model->where($map)->where('is_fictitious',0)
                ->paginate(['list_rows' => $limit, 'page' => $page], false)
                ->toArray();
        }

        $list['data'] = $this->agentFillModelBackends($list['data'],'id','agent_id',$date);
        $this->success($list);
    }
}