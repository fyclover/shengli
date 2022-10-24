<?php


namespace app\model;


use app\traits\TraitModel;
use think\facade\Cache;
use think\Model;


class UserModel extends Model
{
    public $name = 'common_user';
    use TraitModel;

    public function getPwdAttr($value)
    {
        return '';
    }
    public static function onAfterWrite()
    {
        Cache::clear();
        return true;
    }
    public function getwithdrawPwdAttr($value)
    {
        return '';
    }

    public static function page_one(int $id)
    {
        $find = self::lock(true)->find($id);
        if (empty($find)) return [];
        return $find->toArray();
    }
    /**
     * @param $limit
     * @param $page
     * @param null $user
     * @return mixed
     */
    public static function page_agent($map,$limit, $page)
    {
        return self::alias('a')
            ->where($map)
            ->join('common_admin b', 'a.market_uid = b.id', 'left')
            ->join('dianji_user_set c', 'a.id = c.u_id', 'left')
            ->field('a.*,b.user_name admin')
            ->field('a.*,b.user_name admin,c.xima_lv')
            ->paginate(['list_rows' => $limit, 'page' => $page], false)
            ->each(function ($item, $key) {
                !empty($item->invitation_code) && $item->tg_url_google = captchaUrl($item->invitation_code);
                $item->tg_url_txt = tg_url() . $item->id;
                //查询代理商的额度
                $MoneyLog = new MoneyLog();
                $quota = $MoneyLog->where('uid', $item['id'])
                    ->where('status', 'in', [105, 106, 305, 306])
                    ->field('money,status as status_text,uid,source_id')
                    ->select()
                    ->toArray();

                $item->quota = 0;
                if (empty($quota)) return;
                foreach ($quota as $key => $value) {
                    if($value['uid'] == $value['source_id'])   continue;
                    $item->quota += intval($value['money']);

                }
            });
    }
    public static function queryMap(array $map, int $type = 1)
    {
        if ($type == 1) {
            return self::where($map)->find();
        }
        return self::where($map)->select();
    }

    public function user_sort($userAll, $map, $order, $limit, $page)
    {

        $money = new MoneyLog();
        $GameRecords = new GameRecords();

        $user_where = self::user_all_where($userAll, 'id');

        $list = self::where($map)->where($user_where)->order($order)
            ->field('id,user_name,create_time,type,status,money_balance')
            ->paginate(['list_rows' => $limit, 'page' => $page], false)->each(function ($item, $key) use ($money, $GameRecords) {
                //查询用户累计充值
                $item['recharge'] = $money->where('uid', $item['id'])->where('status', 'in', MoneyLog::$recharge_status)->sum('money');
                //查询用户累计提现
                $item['withdrawal'] = $money->where('uid', $item['id'])->where('status', 'in', MoneyLog::$withdrawal_status)->sum('money');
                //差寻用户下注总输赢
                $item['t_win_amt'] = $money->where('uid', $item['id'])->where('status', 'in', MoneyLog::$game_order_status)->sum('money');
                //用户累计下注
                $item['t_bet_amt'] = 0;
                $records = $GameRecords->where(['user_id' => $item['id'], 'close_status' => 2])->field('sum(bet_amt) as t_bet_amt,sum(win_amt) as t_win_amt')->find();
                if (!empty($records)) $item['t_bet_amt'] = $records->t_bet_amt;
            });
        return $list;
    }

    //从写统计
    public function count_register($map,$date)
    {
        //时间统计
        $res = $this->where_date_model($this,$date);
        return $res->where($map)->count();
    }

    public static function page_list($where, $limit, $page, $date)
    {

        $map = [];
        $find = null;
        //单独查询代理商信息开始
        //如果是单独查询代理及代理 以下用户信息
        if (isset($where['agent_name'])) {
            $map[] = ['user_name', 'like', '%' . $where['agent_name'] . '%'];
            $find = self::where($map)->where('type', 1)->find();
            unset($where['agent_name']);
            if (!$find) show([]);
            //获取查询代理商信息
        }

        //单独查询代理商信息结束
        //代理商登陆只获得与代理商相关的信息开始
        $map = self::whereMapUser($find);
        //代理商登陆只获得与代理商相关的信息结束

        //时间查询存在
        $res = self::alias('a')->where($where)->where(function ($query) use ($map) {
            $query->whereOr($map);
        });
        if (isset($date['start']) && isset($date['end'])) {
            $date['start'] .= ' 00:00:00';
            $date['end'] .= ' 23:59:00';
            $res = $res->whereTime('a.create_time', 'between', [$date['start'], $date['end']]);
        } elseif (isset($date['start'])) {
            $res = $res->whereTime('a.create_time', '>=', $date['start']);
        } elseif (isset($date['end'])) {
            $date['end'] .= ' 23:59:00';
            $res = $res->whereTime('a.create_time', '<=', $date['end']);
        }

        return $res->join('dianji_user_set c', 'c.u_id = a.id', 'left')
            ->field('
            a.*,c.xima_lv,
            bjl_xian_hong_xian_max,bjl_xian_hong_zhuang_max,bjl_xian_hong_he_max,
            bjl_xian_hong_zhuang_dui_max,bjl_xian_hong_xian_dui_max,bjl_xian_hong_lucky6_max,
            lh_xian_hong_long_max,lh_xian_hong_hu_max,lh_xian_hong_he_max,bjl_xian_hong_xian_min,
            bjl_xian_hong_zhuang_min,bjl_xian_hong_he_min,bjl_xian_hong_zhuang_dui_min,
            bjl_xian_hong_xian_dui_min,bjl_xian_hong_lucky6_min,lh_xian_hong_long_min,
            lh_xian_hong_hu_min,lh_xian_hong_he_min,is_xian_hong,
            nn_xh_chaoniu_min,nn_xh_chaoniu_max,nn_xh_fanbei_min,nn_xh_fanbei_max,nn_xh_pingbei_min,nn_xh_pingbei_max,
            sg_xh_chaoniu_min,sg_xh_chaoniu_max,sg_xh_fanbei_min,sg_xh_fanbei_max,sg_xh_pingbei_min,sg_xh_pingbei_max
            ')
            ->order('id desc')
            ->paginate(['list_rows' => $limit, 'page' => $page], false)
            ->each(function ($item, $key) {
                $item->tg_url_google = $item->tg_url_txt = '';
                if ($item['type'] == 1) {
                    !empty($item->invitation_code) && $item->tg_url_google = captchaUrl($item->invitation_code);
                    $item->tg_url_txt = tg_url() . $item->invitation_code;
                    //查询代理商的额度
                    $MoneyLog = new MoneyLog();
                    $quota = $MoneyLog->where('uid', $item['id'])
                        ->where('status', 'in', [105, 106, 305, 306])
                        ->field('money,status as status_text,uid,source_id')
                        ->select()
                        ->toArray();
                    $item->quota = 0;

                    if (!empty($quota)) {
                        foreach ($quota as $key => $value) {
                            if ($value['source_id'] == $value['uid']){
                                continue;
                            }
                            $item->quota += intval($value['money']);
                            // if ($value['status_text'] == 105 || $value['status_text'] == 305) {
                            //     $item->quota += intval($value['money']);
                            // } else {
                            //     $item->quota -= $value['money'];
                            // }
                        }
                    }
                }
                //查询代理昵称
                if ($item['agent_id'] > 0) {
                    $item->agent_nickname = self::where('id', $item['agent_id'])->value('nickname');
                }
                //查询管路员
                if ($item->market_uid > 0) {
                    $item->admin = AdminModel::where('id', $item->market_uid)->value('user_name');
                }

                //查询是否在线
                if ($item->id > 0) {
                    $find = HomeTokenModel::where('user_id', $item['id'])->find();
                    $item->online = 0;
                    $item->online_ip = 0;
                    if (!empty($find)) {
                        $item->online_ip = $find->ip;
                        $item->online = 0;
                        //当前时间戳 减去 30分钟时间戳 <= 在线时间戳 表示在线
                        if (strtotime($find->create_time) >= time() - 1800) {
                            $item->online = 1;
                        }
                    }
                }


            });
    }
}