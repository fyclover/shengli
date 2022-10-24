<?php


namespace app\controller\foreign;


use app\BaseController;
use app\model\GameRecords;
use app\model\GameType;
use app\model\Odds;
use app\model\Table;
use app\model\UserModel;

class Dashboard extends BaseController
{
    protected $model;
    protected $game_type;
    /**
     *对外统计列表
     */
    public function initialize()
    {
        $this->model = new GameRecords();
        parent::initialize(); // TODO: Change the autogenerated stub
    }

    public function index()
    {
        $game_type = $this->request->post('game_type/d', 0);
        if ($game_type <= 0) show('game_type参数错误');
        //游戏类型获取
        $game_name = GameType::one([['id', '=', $game_type]]);
        if (empty($game_name)) show('game_type游戏不存在');

        //获取游戏赔率
        $odds = Odds::get_page_list([['game_type_id', '=', $game_type]]);
        if (empty($odds)) show('游戏赔率不存在');

        //查询每个赔率下注的用户
        $this->game_type = $game_type;
        $table_id = $this->request->post('table_id/d', 0);
        //1 查询当前游戏下注的赔率
        $map = [];
        $map[] = ['close_status', '=', 1];//待开牌状态
        $map[] = ['game_type', '=', $game_type];//指定游戏类型
        if ($table_id >0)  $map[] = ['table_id', '=', $table_id];//指定台座

        //1 先查询 下注的用户和下注的台卓
        $user_table_list = $this->model
            ->hasWhere('profile', ['is_fictitious'=>0])
            ->where($map)
            ->whereTime('created_at','today')
            ->field('table_id,user_id')
            ->group('table_id,user_id')
            ->select()
            ->toArray();
        if (empty($user_table_list)) return show([]);

        //分组字段
        $field = 'xue_number,pu_number,game_peilv_id,table_id,user_id,';
        //聚合字段
        $field .= 'sum(bet_amt) sum_bet_amt,sum(deposit_amt) sum_deposit_amt';
        //分组字段
        $group = 'table_id,game_peilv_id,pu_number,xue_number,user_id';
        //排序
        $order = 'user_id asc,table_id asc';

        //2 查询每个用户下注赔率 的金额
        $count_list = $this->model->with('profile')->where($map)->field($field)->group($group)->order($order)->select()->toArray();
        $game_info_list = $this->game_type($game_type, $user_table_list, $count_list, $odds);
        return show($game_info_list);
    }

    protected function game_type($game_type, $user_table_list, $count_list, $odds)
    {
        return $this->game_pei_lv_bjl($user_table_list, $count_list, $odds);
    }

    protected function game_pei_lv_bjl($user_table_list, $count_list)
    {
        $table_list = $this->table_name_list($this->game_type);
        $data = [];
        foreach ($user_table_list as $key => $value) {
            $data[$key]['count'] = 0;
            foreach ($count_list as $k => $val) {//同一张台卓，同一个用户下注 多个赔率，主要的原因其实就是解决同一台座下注多个赔率 合并
                if ($value['user_id'] == $val['user_id'] && $value['table_id'] == $val['table_id']) {
                    $data[$key]['table_id'] = $value['table_id'];//赋值台座id
                    $data[$key]['table_name'] = $this->get_table_name($table_list,intval($value['table_id']));
                    $data[$key]['user_id'] = $value['user_id'];//赋值用户id
                    $data[$key]['user_name'] = $val['profile']['user_name'];//赋值用户名称
                    $data[$key]['xue_number'] = $val['xue_number'];//同一张台卓不可能存在多个 靴和铺
                    $data[$key]['pu_number'] = $val['pu_number'];//赋值 铺
                    $data[$key]['game_peilv_id_' . $val['game_peilv_id']] = $val['game_peilv_id'];//赋值赔率。赔率有多个，所有用_+赔率id
                    $data[$key]['sum_bet_amt_' . $val['game_peilv_id']] = $val['sum_bet_amt'];//赋值下注金额，不同的赔率下注金额不一样
                    $data[$key]['sum_deposit_amt_' . $val['game_peilv_id']] = $val['sum_deposit_amt'];//赋值押金，不同的赔率押金不一样
                    $data[$key]['count'] += $val['sum_bet_amt'];//总计下注
                }
            }


        }
        return $data;
    }

    protected function get_table_name($table_list,$table_id){

        if (empty($table_list)) return '';
        foreach ($table_list as $key=>$value){
            if ($value['id'] == $table_id){
                return $value['table_title'];
            }
        }
    }
    protected function table_name_list($game_type = 3){
        return Table::where('game_type',$game_type)->order('id asc')->select()->toArray();
    }

    public function get_table_list(){
        $game_type = $this->request->param('game_type/d',0);
        if ($game_type <= 0) return show([],0,'game_type错误');
        $table_list =  $this->table_name_list($game_type);
        return show($table_list);
    }
}