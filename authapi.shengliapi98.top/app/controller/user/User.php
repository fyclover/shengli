<?php

namespace app\controller\user;


use app\controller\Base;
use app\model\AdminModel;
use app\model\AgentLavel;
use app\model\MoneyLog;
use app\model\UserModel as models;
use app\model\UserSet;
use app\traits\PublicCrudTrait;
use app\validate\User as validates;
use think\exception\ValidateException;
use think\facade\Db;

class User extends Base
{
    protected $model;
    use PublicCrudTrait;

    public function initialize()
    {
        $this->model = new models();
        parent::initialize(); // TODO: Change the autogenerated stub
    }

    //获取列表信息
    public function index()
    {
        //当前页
        $page = $this->request->post('page', 1);
        //每页显示数量
        $limit = $this->request->post('limit', 10);
        //查询搜索条件
        $post = array_filter($this->request->post());
        $map = $date = [];
        isset($post['user_name']) && $map [] = ['a.user_name', 'like', '%' . $post['user_name'] . '%'];
        isset($post['phone']) && $map[] = ['a.phone', '=', $post['phone']];
        isset($post['market_uid']) && $map [] = ['b.user_name', 'like', '%' . $post['market_uid'] . '%'];
        if (isset($post['is_fictitious']) && empty(session('admin_user.agent'))){
            if ($post['is_fictitious'] == 3){
                $map [] = ['a.is_fictitious', '=', 0];
            } else{
                $map [] = ['a.is_fictitious', '=', $post['is_fictitious']];
            }
        }
        $map [] = ['a.type', '=', $post['type']];
        //代理查询存在是，清除其他查询只保留代理查询
        isset($post['agent_name']) && $map ['agent_name'] = $post['agent_name'];
        isset($post['start']) && $date['start'] = $post['start'];
        isset($post['end']) && $date['end'] = $post['end'];
        if ($this->request->admin_user->agent == 1){
                if(isset($post['is_fictitious']) && $post['is_fictitious'] == 3){
                    $map[]=['a.id','in',$this->request->admin_user->user_list];
                }else{
                    $map[]=['a.id','in',$this->request->admin_user->agent_list];
                }
        }
      
        $list = $this->model->page_list($map, $limit, $page, $date);

        return $this->success($list);
    }

    public function user_info()
    {
        $id = $this->request->post('id', 0);
        if ($id < 1) return $this->failed('ID错误');
        $info = $this->model->user_info($id);
        return $this->success($info);
    }

    //代理商个人信息
    public function agent()
    {
        //当前页
        $page = $this->request->post('page', 1);
        //每页显示数量
        $limit = $this->request->post('limit', 10);
        $map=[];

        if ($this->request->admin_user->agent == 1){
            if(!empty($this->request->admin_user->agent_list)){
                // array_push($this->request->admin_user->agent_list,$this->request->admin_user->id);
                $map []=['a.id','in',$this->request->admin_user->id];
            }else{
                $map []=['a.id','in',$this->request->admin_user->id];
            }
        }else{
            $map []=['a.id','in',$this->request->admin_user->agent_all_list];
        }

        $list = $this->model->page_agent($map ,$limit, $page);
        return $this->success($list);
    }

    public function add()
    {
        //过滤数据
        $postField = 'remarks,xima_lv,phone,market_uid,user_name,money_balance,money_freeze,agent_rate,pwd,withdraw_pwd,nickname,type,status,is_real_name,is_fictitious,id_code';
        $post = $this->request->only(explode(',', $postField), 'post', null);

        try {
            validate(validates::class)->scene('add')->check($post);
        } catch (ValidateException $e) {
            // 验证失败 输出错误信息
            return $this->failed($e->getError());
        }
        //$post = array_filter($post);

        //查询是否重复的用户名
        $find = $this->model->where('user_name', $post['user_name'])->find();
        if ($find) return $this->failed('用户已存在');

        //洗码率存入另外的表
        $xima = 0;
        if (isset($post['xima_lv'])) {
            if ($post['xima_lv'] > 100) admin_show([], 0, '洗码率不能高于100%');
            $xima = $post['xima_lv'];
            unset($post['xima_lv']);
        }

        //加密密码
        $post['pwd'] = !empty($post['pwd']) && isset($post['pwd']) ? pwdEncryption($post['pwd']) : home_Initial_pwd();
        //分销比例

        if (!isset($post['agent_rate']) || empty($post['agent_rate'])) $post['agent_rate'] = 0;
        if ($post['type'] != 1) $post['agent_rate'] = 0;//用户没有代理分销金额
        //生成用户邀请码
        $post['invitation_code'] = generateCode();


        //洗码率不能大于平台配置的
        if ($xima > get_config('app_xima')['value']){
            $this->failed('洗码率不能大于'.get_config('app_xima')['value']);
        }

        if ($this->request->admin_user->agent) {
            //代理添加代理
            return $this->agent_add($post, $xima);
        } else {
            return $this->user_add($post, $xima);
        }
    }
    
    public function agentedit()
    {
        $id = $this->request->post('id',0);
        $pwd = $this->request->post('pwd','');
        
        if($id <=0 || empty($pwd)){
            return $this->failed('参数错误');
        }
        //加密密码
        $pwd = pwdEncryption($pwd);
        $this->model->where('id',$id)->update(['pwd'=>$pwd]);
        return $this->success([]);
    }

    /**
     * 修改方法
     * @return mixed
     */
    public function edit()
    {
        //过滤数据
        $postField = 'remarks,xima_lv,phone,market_uid,user_name,money_balance,money_freeze,agent_rate,pwd,withdraw_pwd,nickname,type,status,is_real_name,is_fictitious,id';
        $post = $this->request->only(explode(',', $postField), 'post', null);
        //验证数据
        try {
            validate(validates::class)->scene('edit')->check($post);
        } catch (ValidateException $e) {
            // 验证失败 输出错误信息
            return $this->failed($e->getError());
        }

        //查询是否重复的用户名
        $find = $this->model->where('user_name', $post['user_name'])->where('id', '<>', $post['id'])->find();
        if ($find) return $this->failed('用户已存在');
        //加密密码
        !empty($post['pwd']) && $post['pwd'] = pwdEncryption($post['pwd']);
        if (empty($post['pwd'])) unset($post['pwd']);
        if (empty($post['withdraw_pwd'])) unset($post['withdraw_pwd']);
        //洗码率存入另外的表
        $xima = 0;

        if (isset($post['xima_lv'])) {
            if ($post['xima_lv'] >= 100) {
                return $this->failed('洗码率不能大于100');
            }
            $xima = $post['xima_lv'];
        }

        unset($post['xima_lv']);

        return $this->user_edit($post, $xima);
    }


    //判断业务员存不存在
    public function is_admin($post)
    {
        if (!isset($post['market_uid']) || empty($post['market_uid'])) return false;
        $admin = (new AdminModel())->find($post['market_uid']);
        if (!$admin) return false;
        return $post;
    }

    //修改虚拟账号
    public function is_status()
    {
        $id = $this->request->post('id', 0);
        if ($id <= 0) return $this->failed('用户不存在');
        $find = $this->model->find($id);
        $find->is_fictitious = $find->is_fictitious == 1 ? 0 : 1;
        $save = $find->save();
        if ($save) return $this->success([]);
        return $this->failed('修改失败');
    }

    /**
     * money_change_type: 1 1增加
     *uid: 20  用户id
     *change_money:  变化金额
     *money_ststus: 90 90余额修改
     *用户余额修改
     */
    public function money_edit()
    {

        if (cache('cache_post_'.$this->request->admin_user->id)){
            return $this->failed('2秒内不可重复操作');
        }
        cache('cache_post_'.$this->request->admin_user->id,time(),2);

        $postField = 'money_change_type,change_money,uid,money_ststus';
        $post = $this->request->only(explode(',', $postField), 'post', null);
        $post = array_filter($post);
        //验证数据
        try {
            validate(validates::class)->scene('money')->check($post);
        } catch (ValidateException $e) {
            // 验证失败 输出错误信息
            return $this->failed($e->getError());
        }

        $app_agent_user_money_edit = isset(get_config('app_agent_user_money_edit')['value']) ? get_config('app_agent_user_money_edit')['value']:0;

        if (!in_array($post['uid'],$this->request->admin_user->user_list) && $app_agent_user_money_edit != 0){
            return $this->failed('请开启编辑代理下线的权限');
        }

        //查询当前用户的余额
        $find = $this->model->lock(true)->find($post['uid']);
        if (!$find) return $this->failed('用户不存在');
        $post['change_money'] = abs($post['change_money']);
        // '编辑可用余额'; 90
        if ($post['money_ststus'] == 90) {
            return $this->balance_edit($post, $find);
        }

        //洗码金额
        if ($post['money_ststus'] == 91) {
            return $this->frozen_edit($post, $find);
        }
        //编辑额度代理额度
        if ($post['money_ststus'] == 92) {
            return $this->points_edit($post, $find);
        }

        return $this->failed('请求错误');
    }

    /**
     * 用户余额修改
     * @param $post /数据
     * @param $find /模型查询数据
     * @return mixed
     */
    protected function balance_edit($post, $find)
    {
        if ($find->type == 1)  return $this->failed('不可改代理商余额');
        $balance = $find->money_balance;
        //查询用户钱是否够扣
        if ($find->money_balance < $post['change_money'] && $post['money_change_type'] != 1) return $this->failed('用户钱包不够');


        //代理
        if ($this->request->admin_user->agent){
            //查询代理本身的代理额度，防止断网触发多次
            $admin_user = $this->model->where('id',$this->request->admin_user->id)->find();
            //1 判断代理是否够扣，  代理扣除 给代理增加余额 并且要代理额度够给用户增加
            if ($post['money_change_type'] == 1 && $post['change_money'] > $admin_user->points){
                return $this->failed('代理额度不够');
            }
            $status = $post['money_change_type'] == 1 ? 105 : 106;
            $mark = $post['money_change_type'] == 1 ? '代理操作余额充值' : '代理操作余额扣除';
            $mark .='会员:'.$find->id.'-'.$find->user_name;
            $source_id = $this->request->admin_user->id;
            $market_uid = 0;

        }else{//非代理
            $mark = $post['money_change_type'] == 1 ? '后台余额充值' : '后台余额扣除';
            $status = $post['money_change_type'] == 1 ? 101 : 102;
            $source_id = 0;
            $market_uid = $this->request->admin_user->id;
        }

        //state是 1时是增加
        $find->money_balance = $post['money_change_type'] == 1 ? $find->money_balance + $post['change_money'] : $find->money_balance - $post['change_money'];
        $find->money_total_recharge +=   $post['money_change_type'] == 1 ?  $post['change_money']:0;
        $find->money_total_withdraw +=   $post['money_change_type'] == 1 ?  0:$post['change_money']*-1;
        $mark .='  ID:'.$this->request->admin_user->id.' 操作人:'.$this->request->admin_user->user_name;

        //执行修改数据
        $moneylog = new \app\model\MoneyLog();
        $save = false;
        $money_log_change_money = $post['change_money'];
        Db::startTrans();
        try {
            if ($this->request->admin_user->agent){
                //是代理修改的时候需要修改代理本身的余额,用户增加 代理减少
                if ($post['money_change_type'] == 1){
                    $type = 2;
                    $money_log_change_money = $money_log_change_money*-1;
                    $points_end = $admin_user->points - $post['change_money'];
                    $this->model->where('id',$this->request->admin_user->id)->dec('points',$post['change_money'])->update();
                }else{
                    $type = 1;
                    $points_end = $admin_user->points + $post['change_money'];
                    $this->model->where('id',$this->request->admin_user->id)->inc('points',$post['change_money'])->update();
                }
                //代理额度从操作日志
                $moneylog->post_insert_log($type,$status,$admin_user->points,$points_end,$money_log_change_money,$admin_user->id,$source_id,$mark,null,$market_uid);

            }
            $find->save();
            //写用户金额操作日志
            if ($status == 106 || $status == 102){
                $money_log_change_money =  $post['change_money'] *-1;
            }else{
                $money_log_change_money = abs($money_log_change_money);
            }
            $moneylog->post_insert_log(3,$status,$balance,$find->money_balance,$money_log_change_money,$find->id,$source_id,$mark,null,$market_uid);

            $save = true;
            Db::commit();
        } catch (ValidateException $e) {
            Db::rollback();
            // 验证失败 输出错误信息
            return $this->failed($e->getError());
        }

        if ($save) return $this->success([]);
        return $this->failed('修改失败');
    }

    /**
     * @param $post /数据
     * @param $find /模型查询数据
     * @return mixed
     * 洗码费
     */
    protected function frozen_edit($post, $find)
    {
        $balance = $find->money_freeze;
        //查询用户冻结金额是否够扣
        if ($find->money_freeze < $post['change_money'] && $post['money_change_type'] != 1) return $this->failed('用户洗码不够');
        //state是 1时是增加
        $find->money_freeze = $post['money_change_type'] == 1 ? $find->money_freeze + $post['change_money'] : $find->money_freeze - $post['change_money'];
        $status = $post['money_change_type'] == 1 ? 602 : 702;
        $money =  $post['money_change_type'] == 1 ? $post['change_money'] : $post['change_money']* -1;
        $mark = $this->request->admin_user->user_name.'操作洗码修改,变化前'.$balance.',变化:'.$money;
        //执行修改数据
        $save = false;
        Db::startTrans();
        try {
            $find->save();
            MoneyLog::post_insert_log(3,$status,$balance,$find->money_freeze,$money,$find->id,0,$mark,null,$this->request->admin_user->id);
            $save = true;
            Db::commit();
        } catch (ValidateException $e) {
            Db::rollback();
            // 验证失败 输出错误信息
            return $this->failed($e->getError());
        }

        if ($save) return $this->success([]);
        return $this->failed('修改失败');
    }

    /**
     ** @param $post /数据
     * @param $find /模型查询数据
     * 代理额度
     */
    public function points_edit($post, $find)
    {
        if ($find->type != 1)  return $this->failed('只有代理商才有额度');

        if ($find->points < $post['change_money'] && $post['money_change_type'] != 1) return $this->failed('用户钱包不够');
        //state是 1时是增加

        if ($this->request->admin_user->agent){
            //查询代理本身的代理额度，防止断网触发多次
            $admin_user = $this->model->where('id',$this->request->admin_user->id)->find();
            //1 判断代理是否够扣，  代理扣除 给代理增加余额 并且要代理额度够给用户增加
            if ($post['money_change_type'] == 1 && $post['change_money'] > $admin_user->points){
                return $this->failed('代理额度不够');
            }
            $status = $post['money_change_type'] == 1 ? 305 : 306;
            $mark = $post['money_change_type'] == 1 ? '代理操作额度充值' : '代理操作额度扣除';
            $mark .='会员:'.$find->id.'-'.$find->user_name;
            $source_id = $this->request->admin_user->id;
            $market_uid = 0;
        }else{//非代理
            $mark = $post['money_change_type'] == 1 ? '后台额度充值' : '后台额度扣除';
            $status = $post['money_change_type'] == 1 ? 105 : 106;
            $source_id = 0;
            $market_uid = $this->request->admin_user->id;
        }

        $points = $post['money_change_type'] == 1 ? $find->points + $post['change_money'] : $find->points - $post['change_money'];
        $mark .='  ID:'.$this->request->admin_user->id.' 操作用户:'.$this->request->admin_user->user_name;
        //执行修改数据
        $save = false;
        $moneylog = new \app\model\MoneyLog();
        //代理
        $money_log_change_money = $post['change_money'];
        Db::startTrans();
        try {
            //是代理修改的时候需要修改代理本身的余额,用户增加 代理减少
            if ($this->request->admin_user->agent){
                if ($post['money_change_type'] == 1){
                    $money_log_change_money = $money_log_change_money *-1;
                    $type = 2;
                    $points_end = $admin_user->points - $post['change_money'];
                    $this->model->where('id',$this->request->admin_user->id)->dec('points',$post['change_money'])->update();
                }else{
                    $type = 1;
                    $points_end = $admin_user->points + $post['change_money'];
                    $this->model->where('id',$this->request->admin_user->id)->inc('points',$post['change_money'])->update();
                }
                //代理额度从操作日志
                $moneylog->post_insert_log($type,$status,$admin_user->points,$points_end,$money_log_change_money,$admin_user->id,$source_id,$mark,null,$market_uid);
            }

            $find->where('id',$find['id'])->save(['points'=>$points]);

            if ($status == 106 || $status == 306){
                $money_log_change_money =  $post['change_money'] *-1;
            }else{
                $money_log_change_money = abs($money_log_change_money);
            }
            //写用户金额操作日志
            $moneylog->post_insert_log(3,$status,$find->points,$points,$money_log_change_money,$find->id,$source_id,$mark,null,$market_uid);
            $save = true;
            Db::commit();
        } catch (ValidateException $e) {
            Db::rollback();
            // 验证失败 输出错误信息
            return $this->failed($e->getError());
        }

        if ($save) return $this->success([]);
        return $this->failed('修改失败');
    }

    public function agent_add($post, $xima)
    {
        $post['agent_id'] = session('admin_user.id');
        if ($post['type'] != 1) {

        } else {
            //给自己增加下级代理
            // 1查询自己所有下级，并获取所有下级的 分销奖励加上自己的 不能超过 固定值
            session('admin_user.agent_rate');
            $total_agent_sale = get_config('app_agent_sale')['value'];
            if ($post['agent_rate'] > $total_agent_sale) return admin_show([], 0, '分销比例不能大于' . $total_agent_sale);

            $agent_rate = AgentLavel::agent_down_all(session('admin_user.id'));//该用户所有分销金额 包括自己
            if (($agent_rate + $post['agent_rate']) > $total_agent_sale) {
                // return admin_show([], 0, '分销链分销比例为:' . ($agent_rate + $post['agent_rate']) . '不合法');
            }
        }
        //添加的时候，写入 洗码率，写入代理的所有上级代理
        $save = false;
        Db::startTrans();
        try {
            $this->model->save($post);
            UserSet::user_insert($this->model->id, $xima);
            if ($post['type'] == 1) {
                //组装插入代理表数据
                //查询用户上级
                $user_all = AgentLavel::agent_pid($post['agent_id']);
                if ($user_all) {
                    sort($user_all);
                    $num = 0;
                    foreach ($user_all as $key => $value) {
                        if ($value == 0) continue;
                        $data[$num]['agent_id'] = $this->model->id;
                        $data[$num]['agent_pid'] = $value;
                        $data[$num]['agent_pid_level'] = $num;
                        $data[$num]['rate'] = $post['agent_rate'];
                        $num++;
                    }
                    AgentLavel::insertAll($data);
                } else {
                    AgentLavel::insert(['agent_id' => $this->model->id, 'agent_pid' => $post['agent_id'], 'agent_pid_level' => 1, 'rate' => $post['agent_rate']]);
                }
            }
            $save = true;
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
        }
        if ($save) return admin_show();
        return admin_show([], 0);
    }

    //用户添加
    public function user_add($post, $xima)
    {
        //添加常规会员开始
        $post['agent_id'] = 0;
        if ($post['type'] != 1) {
            // 默认添加 会员带有代理
            if (isset($post['id_code'])){
                $post['agent_id'] = $post['id_code'];
                unset($post['id_code']); // 暂时先删除了
            }
        } else {
            //添加常规会员结束
            //添加代理开始
            $total_agent_sale = get_config('app_agent_sale')['value'];
            if ($post['agent_rate'] > $total_agent_sale) return admin_show([], 0, '分销比例不能大于' . $total_agent_sale);
            //后台管理员添加代理的时候。需要填写上级ID
            if (isset($post['id_code']) && $post['id_code'] > 0) {
                //查询该 id_code 是否是代理
                $find =$this->model->where('id',$post['id_code'])->find();
                if (empty($find)) return admin_show([], 0, '该代理ID不能存在');
                if ($find->type != 1) return admin_show([], 0, '该ID不是代理');

                // 上级ID存在的时候，
                // 1 需要查询该代理链 的分销比例是否大鱼 配置的 分销比例
                // 2 并统计该分销链的分销比例是否大鱼配置的分销比例
                $agent_rate = AgentLavel::agent_down_all($post['id_code']);//该用户所有分销金额 包括自己
                if (($agent_rate + $post['agent_rate']) > $total_agent_sale) {
                    //  return admin_show([], 0, '分销链分销比例为:' . ($agent_rate + $post['agent_rate']) . '不合法');
                }
                $post['agent_id'] = $post['id_code'];
                unset($post['id_code']);
            }
        }

        //洗码费确认是否合法
        if ($post['agent_id'] > 0){ //查询上一级的洗码
            $agent_xima = UserSet::where('u_id',$post['agent_id'])->find();
            if (!empty($agent_xima)  && $xima > $agent_xima->xima_lv){
                return admin_show([], 0, '洗码率不能大于'.$agent_xima->xima_lv);
            }
        }

        //添加的时候，写入 洗码率，写入代理的所有上级代理
        $save = false;
        Db::startTrans();
        try {
            $this->model->save($post);
            UserSet::user_insert($this->model->id, $xima);
            if ($post['type'] == 1) {
                //组装插入代理表数据
                //查询用户上级
                $user_all = AgentLavel::agent_pid($post['agent_id']);
                if ($user_all) {
                    sort($user_all);
                    $num = 0;
                    $data = [];//存放存入分销比例的
                    foreach ($user_all as $key => $value) {
                        //存入实际洗码的数据
                        if ($value == 0) continue;
                        $data[$num]['agent_id'] = $this->model->id;
                        $data[$num]['agent_pid'] = $value;
                        $data[$num]['agent_pid_level'] = $num;
                        $data[$num]['rate'] = $post['agent_rate'];
                        $num++;
                    }
                    (new AgentLavel())->saveAll($data);
                } else {
                    AgentLavel::insert(['agent_id' => $this->model->id, 'agent_pid' => 0, 'agent_pid_level' => 0, 'rate' => $post['agent_rate']]);
                }
            }
            $save = true;
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            return admin_show([], 0, $e->getMessage());
        }

        if ($save) return admin_show();
        return admin_show([], 0, '新增失败');
    }

    public function user_edit($post, $xima)
    {


        if ($post['type'] != 1 || !isset($post['agent_rate'])) {
            unset($post['id_code']);
            $post['agent_rate'] = 0;
        } else {
            $total_agent_sale = get_config('app_agent_sale')['value'];
            if ($post['agent_rate'] > $total_agent_sale) return admin_show([], 0, '分销比例不能大于' . $total_agent_sale);
            //后台管理员添加代理的时候。需要填写上级ID
            //修改 不能修改 上级ID
            //修改分销比例
            $agent_rate = AgentLavel::agent_down_all($post['id'], true);//该用户所有分销金额 包括自己

            if (($agent_rate + $post['agent_rate']) > $total_agent_sale) {
                //return admin_show([], 0, '分销链分销比例为:' . ($agent_rate + $post['agent_rate']) . '不合法');
            }
        }

        //洗码费确认是否合法
        //洗码率不能大于平台配置的
        if ($xima > get_config('app_xima')['value']){
            $this->failed('洗码率不能大于'.get_config('app_xima')['value']);
        }
        //查询用户的上一级
        $find = $this->model->find($post['id']);
        if (!empty($find) && $find->agent_id > 0){ //查询上一级的洗码
            $agent_xima = UserSet::where('u_id',$find->agent_id)->find();
            if (!empty($agent_xima)  && $xima > $agent_xima->xima_lv){
                return admin_show([], 0, '洗码率不能大于'.$agent_xima->xima_lv);
            }
        }
        ##########

        $save = false;
        Db::startTrans();
        try {
            $this->model->update($post);
            //写入洗码数据
            UserSet::user_insert($post['id'], $xima);
            //更改 代理商信息
            if ($post['type'] == 1) {
                AgentLavel::where('agent_id', $post['id'])->update(['rate' => $post['agent_rate']]);
            }
            Db::commit();
            $save = true;
        } catch (\Exception $e) {
            Db::rollback();
        }

        if ($save)  $this->success([]);
        $this->failed('修改失败');
    }

    public function xian_hong()
    {
        $postField = 'id,bjl_xian_hong_xian_max,bjl_xian_hong_zhuang_max,bjl_xian_hong_he_max,is_xian_hong,bjl_xian_hong_zhuang_dui_max,bjl_xian_hong_xian_dui_max,bjl_xian_hong_lucky6_max,lh_xian_hong_long_max,lh_xian_hong_hu_max,lh_xian_hong_he_max,bjl_xian_hong_xian_min,bjl_xian_hong_zhuang_min,bjl_xian_hong_he_min,bjl_xian_hong_zhuang_dui_min,lh_xian_hong_he_min,bjl_xian_hong_xian_dui_min,bjl_xian_hong_lucky6_min,lh_xian_hong_long_min,lh_xian_hong_hu_min,';
        $postField .='nn_xh_chaoniu_max,nn_xh_chaoniu_min,nn_xh_fanbei_max,nn_xh_fanbei_min,nn_xh_pingbei_max,nn_xh_pingbei_min,';
        $postField .='sg_xh_chaoniu_max,sg_xh_chaoniu_min,sg_xh_fanbei_max,sg_xh_fanbei_min,sg_xh_pingbei_max,sg_xh_pingbei_min';
        $post = $this->request->only(explode(',', $postField), 'post', null);

        $user_set = UserSet::where('u_id', $post['id'])->find();
        $post['u_id'] = $post['id'];
        unset($post['id']);
        if (empty($user_set)) {
            UserSet::user_insert(0, 0, 0, $post);
        }
        $user_set->save($post);
        return $this->success([]);
    }
}