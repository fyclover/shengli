<?php


namespace app\model;


use app\traits\TraitModel;
use think\Model;

class MoneyLog extends Model
{
    public $name = 'common_pay_money_log';
    use TraitModel;

    public static $agent_recharge_status = [105, 305];//代理充值
    public static $agent_withdrawal_status = [106, 306]; //代理提现
    public static $admin_recharge_status = [101, 105];//管理员充值
    public static $admin_withdrawal_status = [102,106]; //管理员提现
    public static $xima_status = [602];//洗马,702是扣除洗马费
    public static $recharge_status = [101, 105, 305]; //充值
    public static $withdrawal_status = [102, 106, 306]; //提现
    public static $game_order_status = [501, 502, 503, 504, 505, 506, 507, 508, 509, 510];//下注输赢

    //
    public static function onBeforeDelete($user)
    {
        return false;
    }

    public function user()
    {
        return $this->hasOne(UserModel::class, 'id', 'uid');
    }

    public function getTypeAttr($value)
    {
        $type = [1 => '收入', 2 => '支出', 3 => '后台操作', 4 => '提现退款'];
        return isset($type[$value]) ? $type[$value] : $value;
    }

    public function getStatusAttr($value)
    {//105 代理余额充值  //106
        $type = [-1 => '已删除', 101 => '充值', 102 => '提现', 105 => '后台代理额度充值', 106 => '后台代理额度扣除', 201 => '提现',
            301 => '积分操作', 305 => '代理操作额度增加', 306 => '代理操作额度扣除',
            401 => '套餐分销奖励', 403 => '充值分销奖励',
            501 => '游戏', 502 => '龙虎下注', 503 => '百家乐下注', 506 => '牛牛下注',508=>'三公下注',
            602 => '洗码费', 603 => '代理费',
            509 => '退还下注金额', 702 => '退还洗码费', 703 => '退还代理费'];

        if ($value == 'status_type') {
            $type_list = [
                ['id' => 101, 'name' => $type[101]],
                ['id' => 102, 'name' => $type[102]],
                ['id' => 105, 'name' => $type[105]],
                ['id' => 106, 'name' => $type[106]],
                //['id' => 201, 'name' => $type[201]],
                ['id' => 305, 'name' => $type[305]],
                ['id' => 306, 'name' => $type[306]],
                ['id' => 602, 'name' => $type[602]],
                ['id' => 502, 'name' => $type[502]],
                ['id' => 503, 'name' => $type[503]],
                ['id' => 506, 'name' => $type[506]],
                ['id' => 508, 'name' => $type[508]],
            ];
            return $type_list;
        }
        return isset($type[$value]) ? $type[$value] : $value;
    }



    public static function page_list($where, $limit, $page, $order, $date = [])
    {
        $date = array_filter($date);
        $res = self::where_date_model('', $date, 'create_time');
        return $res->where($where)
            ->field('*')
            ->order($order)
            ->paginate(['list_rows' => $limit, 'page' => $page], false)
            ->each(function ($item, $key) {
                $item->status_name = '';
                if ($item->uid > 0) {
                    $find = UserModel::where('id', $item->uid)->field('type as user_type,is_fictitious,user_name')->find();
                    if (!empty($find)){
                        $item->user_name = $find->user_name;
                        $item->user_type = $find->user_type;
                        $item->is_fictitious = $find->is_fictitious;
                    }
                }
            });
    }

    public function count_money( $map = [], $user_list = [], $date = []): float
    {
        $date = array_filter($date);
        $res = $this->this_where_date_model($this, $date);
        return  $res->where($map)->where($user_list)->sum('money');
    }

    public static function post_insert_log($type, $status, $money_before, $money_end, $money, $uid, $source_id, $mark, $date = null, $market_uid = 0)
    {

        if ($status < 100) {
            switch ($status) {
                case 2:
                    $status = 502;
                    break;
                case 3:
                    $status = 503;
                    break;
                case 6:
                    $status = 506;
                    break;
                default:
                    $status = 501;
                    break;
            }
        }
        return self::insert([
            'create_time' => empty($date) ? date('Y-m-d H:i:s') : $date,
            'type' => $type,
            'status' => $status,
            'money_before' => $money_before,
            'money_end' => $money_end,
            'money' => $money,
            'uid' => $uid,
            'source_id' => $source_id,
            'mark' => $mark,
            'market_uid' => $market_uid,
        ]);
    }

    //查询所有的充值
    public function count_recharge($map, $date): float
    {
        $res = $this->where_date_model($this, $date);
        return $res->where($map)->sum('money');
    }


}