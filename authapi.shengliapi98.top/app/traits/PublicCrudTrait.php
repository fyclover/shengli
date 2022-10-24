<?php


namespace app\traits;

use app\model\GameType;
use app\model\MoneyLog;
use app\model\UserModel;
use app\validate\Status;
use think\exception\ValidateException;

/**
 * 模型公用删除
 * Trait PublicCrudTrait
 * @package app\common\traites
 */
trait PublicCrudTrait
{
    use PublicResponseTrait;

    //模型软删除
    public function del()
    {
        $id = $this->request->post('id', 0);
        if ($id < 1) $this->failed('ID错误');

        //模型删除
        $del = $this->model->del($id);
        if ($del) $this->success([]);
        $this->failed('删除失败，数据不存在');
    }

    public function edit()
    {
        echo 'trait edit';
    }

    public function desc()
    {
        echo 'trait desc';
    }

    /**
     * 查询
     * @return mixed
     */
    public function detail()
    {
        //过滤数据
        $postField = 'id';
        $post = $this->request->only(explode(',', $postField), 'post', null);

        //验证数据
        try {
            validate(Status::class)->scene('detail')->check($post);
        } catch (ValidateException $e) {
            // 验证失败 输出错误信息
            $this->failed($e->getError());
        }

        //查询
        $user = $this->model->find($post['id']);
        if ($user) $this->success($user);
        $this->failed('数据不存在');
    }

    /**
     * 状态切换 上下架
     */
    public function status()
    {
        //过滤数据
        $postField = 'id,status';
        $post = $this->request->only(explode(',', $postField), 'post', null);
        try {
            validate(Status::class)->scene('status')->check($post);
        } catch (ValidateException $e) {
            // 验证失败 输出错误信息
            $this->failed($e->getError());
        }

        $save = $this->model->setStatus($post);
        if ($save) $this->success([]);
        $this->failed('修改失败');
    }

    /**
     * 状态切换 上下架
     */
    public function is_show()
    {
        //过滤数据
        $postField = 'id,show';
        $post = $this->request->only(explode(',', $postField), 'post', null);

        try {
            validate(Status::class)->scene('show')->check($post);
        } catch (ValidateException $e) {
            // 验证失败 输出错误信息
            $this->failed($e->getError());
        }

        $save = $this->model->where('id', $post['id'])->update(['is_show' => $post['show']]);
        if ($save) $this->success([]);
        $this->failed('修改失败');
    }

    //游戏分类
    public function game()
    {
        return $this->success(GameType::one());
    }

    /**
     * @param $user_name /用户名称
     * @param string $range /查询的用户
     */
    protected function map_user_name($user_name, $range = 'direct_list',$alias = 'uid')
    {
        if (empty($user_name)) return [];
        $user_find = UserModel::where('user_name|nickname', $user_name)->find();
        if (empty($user_find)) $this->failed('用户不存在');
        if (!in_array($user_find->id, $this->request->admin_user[$range]) && $this->request->admin_user->agent >0) $this->failed('没权限查看该用户');  //没权限查看该用户
        return [$alias, '=', $user_find->id];
    }

    protected function map_status(int $status)
    {
        if ($this->request->admin_user->agent > 0) { //存在状态查询时后，统计金额
            $status_map = $this->agent_status($status);
        } else {
            $status_map = $this->admin_status($status);
        }
        return $status_map;
    }

    protected function agent_status(int $status)
    {
        $status_name = '';
        $status_where = [];
        switch ($status) {
            case 101:
                $status_name = '充值';
                $status_where[] = ['status', 'in', MoneyLog::$agent_recharge_status];
                break;
            case 102:
                $status_name = '提现';
                $status_where[] = ['status', 'in', MoneyLog::$agent_withdrawal_status];
                break;
            case 501:
                $status_name = '下注';
                $status_where[] = ['status', 'in', MoneyLog::$game_order_status];
                break;
            case 602:
                $status_name = '洗码';
                $status_where[] = ['status', 'in', MoneyLog::$xima_status];
                break;
        }
        return ['status_name' => $status_name, 'status_where' => $status_where];
    }

    protected function admin_status(int $status)
    {
        $status_name = '';
        $status_where = [];
        switch ($status) {
            case 101:
                $status_name = '充值';
                $status_where[] = ['status', 'in', MoneyLog::$admin_recharge_status];
                $status_where[] = ['source_id','=',0];
                break;
            case 102:
                $status_name = '提现';
                $status_where[] = ['status', 'in', MoneyLog::$admin_withdrawal_status];
                $status_where[] = ['source_id','=',0];
                break;
            case 501:
                $status_name = '下注';
                $status_where[] = ['status', 'in', MoneyLog::$game_order_status];
                break;
            case 602:
                $status_name = '洗码';
                $status_where[] = ['status', 'in', MoneyLog::$xima_status];
                break;
        }
        return ['status_name' => $status_name, 'status_where' => $status_where];
    }
}