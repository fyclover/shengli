<?php


namespace app\model;


use think\Model;

class MoneyLog extends Model
{
    public $name = 'common_pay_money_log';

    public static function copy_usurp_mysql()
    {
        return self::connect("mysql");
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
        $type = [101 => '充值', 102 => '提现', 105 => '后台代理额度充值', 106 => '后台代理额度扣除', 201 => '提现',
            301 => '积分操作', 305 => '代理操作额度增加', 306 => '代理操作额度扣除',
            401 => '套餐分销奖励', 403 => '充值分销奖励',
            501 => '游戏', 502 => '龙虎下注', 503 => '百家乐下注', 506 => '牛牛下注',
            602 => '洗码费', 601 => '代理费',
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
            ];
            return $type_list;
        }

        return isset($type[$value]) ? $type[$value] : $value;
    }

//    public static function page_list($where, $limit, $page, $order, $date = [], $isAll = 2)
//    {
//        $date = array_filter($date);
//        $res = self::alias('a');
//        if (isset($date['start']) && isset($date['end'])) {
//            $date['start'] .= ' 00:00:00';
//            $date['end'] .= ' 23:59:00';
//            $res = $res->whereTime('a.create_time', 'between', [$date['start'], $date['end']]);
//        } elseif (isset($date['start'])) {
//            $res = $res->whereTime('a.create_time', '>=', $date['start']);
//        } elseif (isset($date['end'])) {
//            $res = $res->whereTime('a.create_time', '<=', $date['end']);
//        }
//        //查询代理下的所有用户
//        if ($isAll == 1) {
//            $map = self::whereMapUserClassA();
//        } else {
//            $map = self::whereMapUserAll('b');
//        }
//
//        return $res->where($where)
//            ->where($map)
//            ->join('common_user b', 'a.uid = b.id', 'left')
//            ->join('common_admin c', 'a.market_uid = c.id', 'left')
//            ->field('a.*,b.user_name,c.user_name admin_name,is_fictitious,b.type user_type')
//            ->order($order)
//            ->paginate(['list_rows' => $limit, 'page' => $page], false);
//    }

//    public function page_list_or($date = null, $status = [], $name = null, $isAll = 1, $limit = 10, $page = 0)
//    {
//        //时间统计
//        $res = $this->where_date($date);
//        //查询代理下的所有用户
//        if ($isAll == 1) {
//            $map = $this->whereMapUserClassA();
//        } else {
//            $map = $this->whereMapUserAll('b');
//        }
//
//
//        $where[] = ['b.is_fictitious', '=', 0];
//        $where[] = ['b.type', '=', 1];
//        if ($name != null) { //查询该用户
//            //查询是否有权限查询这个用户
//            $auth = $this->whereMapUserAllAuth($name);
//            if (!$auth) return 0;
//            $where = [];
//
//            $map = [];
//            $find = UserModel::where('user_name', $name)->find();
//            if (!empty($find)) {
//                //用户存在的时候
//                $where[] = ['uid', '=', $find->id];
//            } else {
//                return 0;
//            }
//
//        }
//
//        $where[] = ['a.status', 'in', $status];
//        $list = $res->alias('a')
//            ->join('common_user b', 'a.uid=b.id', 'left')
//            ->where($map)
//            ->where($where)
//            ->field('a.*,b.user_name,is_fictitious,b.type user_type')
//            ->order('a.id desc')
//            ->paginate(['list_rows' => $limit, 'page' => $page], false);
//
//        return $list;
//    }




//   public function count_money($date = null, $status = [], $name = null, $isAll = 1, $type = 2,$sel=[]): int
//    {
//        //时间统计
//        $res = $this->where_date($date);
//        //查询代理下的所有用户
//        if ($isAll == 1) {
//            $map = $this->whereMapUserClassA();
//        } else {
//            $map = $this->whereMapUserAll('b');
//        }
//        $where[] = ['a.status', 'in', $status];
//        $where[] = ['b.is_fictitious', '=', 0];
//        if ($type != 0) $where[] = ['b.type', '=', $type];
//        if ($name != null) { //查询该用户
//            $find = UserModel::where('user_name', $name)->find();
//            if (!empty($find)) {
//                $where[] = ['uid', '=', $find->id];
//            }
//        }
//
//        $money = $res->alias('a')
//            ->join('common_user b', 'a.uid=b.id', 'left')
//            ->where($map)
//            ->where($sel)
//            ->where($where)
//            ->sum('a.money');
//        return $money;
//    }

//    public function count_money_or($date = null, $status = [], $name = null, $isAll = 1): int
//    {
//        //时间统计
//        $res = $this->where_date($date);
//        //查询代理下的所有用户
//        if ($isAll == 1) {
//            $map = $this->whereMapUserClassA();
//        } else {
//            $map = $this->whereMapUserAll('b');
//        }
//
//
//        $where[] = ['b.is_fictitious', '=', 0];
//        if ($name != null) { //查询该用户
//            //查询是否有权限查询这个用户
//            $auth = $this->whereMapUserAllAuth($name);
//            if (!$auth) return 0;
//            $where = [];
//
//            $map = [];
//            $find = UserModel::where('user_name', $name)->find();
//            if (!empty($find)) {
//                //用户存在的时候
//                $where[] = ['uid', '=', $find->id];
//            } else {
//                return 0;
//            }
//
//
//        }
//        $where[] = ['a.status', 'in', $status];
//        $money = $res->alias('a')
//            ->join('common_user b', 'a.uid=b.id', 'left')
//            ->where($map)
//            ->where($where)
//            ->sum('a.money');
//
//        return $money;
//    }

    public static function post_insert_log($type, $status, $money_before, $money_end, $money, $uid, $source_id, $mark, $date = null, $market_uid = 0)
    {

        if ($status < 100) {
            switch ($status) {
                case 1:
                    $status = 501;
                    break;
                case 2:
                    $status = 502;
                    break;
                case 3:
                    $status = 503;
                    break;
                case 4:
                    $status = 504;
                    break;
                case 5:
                    $status = 505;
                    break;
                case 6:
                    $status = 506;
                    break;
                case 7:
                    $status = 507;
                    break;
                case 8:
                    $status = 508;
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
    public function count_recharge($map, $date): int
    {
        $res = $this->where_date_model($this, $date);
        return $res->where($map)->sum('money');
    }

}