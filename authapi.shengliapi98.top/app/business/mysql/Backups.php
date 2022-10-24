<?php


namespace app\business\mysql;

//备份数据表
use app\model\GameRecords;
use app\model\Luzhu;
use think\facade\Db;

class Backups
{
    public function copy_table_lu_zhu($table_name = 'ntp_dianji_lu_zhu'): bool
    {
        //1个月的日期
        $date = strtotime("-1 month");
        for ($i = 1; $i < 100; $i++) {
            //不存在这张备份表时 取这个为表名
            $db = Db::query('SHOW TABLES LIKE ' . "'" . $table_name . '_' . $i . "'");
            if (empty($db)) {
                $arr = Db::query('create table  ' . $table_name . '_' . $i . ' (Select * from ' . $table_name . '  where  create_time < ' . $date . ')');
                if (is_array($arr)) {
                    //成功是清除原来的表数据
                    Luzhu::whereTime('create_time', '<', $date)->delete();
                    break;
                }
                break;
            }
        }
        return true;
    }

    public function copy_table_order($table_name = 'ntp_dianji_records'): bool
    {

        //三个月的日期
        $date = '\'' . date('Y-m-d', strtotime("-3 month")) . '\'';
        $dates = date('Y-m-d', strtotime("-3 month"));
        for ($i = 1; $i < 100; $i++) {
            //不存在这张备份表时 取这个为表名
            $db = Db::query('SHOW TABLES LIKE ' . "'" . $table_name . '_' . $i . "'");
            if (empty($db)) {
                $arr = Db::query('create table  ' . $table_name . '_' . $i . ' (Select * from ' . $table_name . '  where  created_at < ' . $date . ')');
                if (is_array($arr)) {
                    //成功是清除原来的表数据
                    GameRecords::whereTime('created_at', '<', $dates)->delete();
                    break;
                }
                break;
            }
        }
        return true;
    }

    //弃用。用上面的两个就够了
    public function copy_table($table_name = 'ntp_dianji_lu_zhu'): bool
    {
        for ($i = 1; $i < 100; $i++) {
            $db = Db::query('SHOW TABLES LIKE ' . "'" . $table_name . '_' . $i . "'");
            //不存在这张备份表时 取这个为表名
            if (empty($db)) {
                $arr = Db::query('create table  ' . $table_name . '_' . $i . ' (Select * from ' . $table_name . ')');
                if (is_array($arr)) {
                    //成功是清除原来的表数据
                    Db::query('truncate  ' . $table_name);
                    break;
                }
                break;
            }
        }
        return true;
    }

    //防数据篡改机制
    //$where需要备份的数据条件
    //$startModel 旧表模型 $endModel新表模型 $strip清理条数，和where条件相同条数  $del 会变动的字段需要删除
    public function copy_usurp_mysql($startModel, $endModel, $where = [], $del = [], $strip = 100): bool
    {
        //拷贝数据库生产新的数据，并且生产hash值
        $start_data = $startModel->where($where)->order('id desc')->limit($strip)->select()->toArray();//旧表    //查询是否有这张防篡改表
        if (empty($start_data)) return true;
        //存放插入数据
        $add_data = [];
        //查新旧表数据是否更新太慢  防止覆盖掉新表数据
        $find = $endModel->order('id desc')->limit(1)->find();
        //新表数据加上查询的数据如果大于旧表最大的一条数据，说明还没新增到 $strip 条
        if (!empty($find) && $find->id + $strip >= current($start_data)['id']) {
            return true;
        }
        foreach ($start_data as $key => $value) {
            $add_data[$key] = $value;

            //是否有需要删除的字段在进行加密
            if (!empty($del)) {
                foreach ($del as $k => $v) {
                    unset($value[$v]);
                }
            }

            $add_data[$key]['v_hash'] = sha1(md5(implode('', $value)));
            $add_data[$key]['v_edit'] = 0; //是否被修改 0 没 1 有
        }
        //插入数据到新表
        $end_data = $endModel->insertAll($add_data);//新表 插入数据到新表

        if ($end_data) {//查询表数据有多少条，并清理掉100条
            $count = $endModel->count();
            if ($count > $strip * 10) { //删除数据
                $end_data->order('id asc')->limit($strip)->delete();
            }
        }
        return true;
    }

    //判断mysql数据是否被修改
    public function verification_mysql_is_it_modified($startModel, $endModel, $del = [], $strip = 100): array
    {
        //查询新表
        $end_data = $endModel->order('id desc')->limit($strip)->select()->toArray();//旧表    //查询是否有这张防篡改表
        if (empty($end_data)) return ['code' => 0, 'data' => [], 'msg' => '没有数据'];
        //存放被修改的用户
        $user_data = [];
        foreach ($end_data as $key => $value) {
            //查询旧表数据
            $find = $startModel->find($value['id']);
            if (!$find) continue;
            $find = $find->toArray();

            //是否有需要删除的字段在进行加密
            if (!empty($del)) {
                foreach ($del as $k => $v) {
                    unset($find[$v]);
                }
            }

            $v_hash = sha1(md5(implode('', $find)));
            if ($v_hash != $value['v_hash']) { //对比是否相等 不相等表示数据被改了
                $user_data[$key]['id'] = $value['id'];
                $user_data[$key]['v_edit'] = 1; //是否被修改 0 没 1 有
            }
        }
        if (empty($user_data)) return ['code' => 0, 'data' => $user_data, 'msg' => '成功！'];
        $endModel->saveAll($user_data); //有数据被修改
        return ['code' => 1, 'data' => [], 'msg' => '有数据被修改！'];
    }


}