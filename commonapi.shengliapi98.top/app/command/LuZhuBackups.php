<?php


namespace app\command;


use app\model\Luzhu;
use app\model\LuZhuBackups as LuZhuBackupsModel;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\facade\Db;

/**
 * 露珠备份
 * 每一周备份一次，实现内容：让露珠表一直只保存两周的露珠数据，其他的全部转移到该露珠备份表去
 */

class LuZhuBackups extends Command
{
    protected function configure()
    {
        $this->setName('lu_zhu_backups')->setDescription('Here is the lu_zhu_backups');
    }

    protected function execute(Input $input, Output $output)
    {
        //查询数据库最大的id
        $find = LuZhuBackupsModel::order('id desc')->find();
        $id = isset($find->id) ? $find->id : 0;
        $date = date("Y-m-d", strtotime("-1 week"));//两周前的时间
        //查询露珠表最近两周的数据
        $sel = Luzhu::whereTime('create_time', '<', $date)->where('id','>',$id)->order('id asc')->select();
        $data = $sel->toArray();
        if (empty($data)) return  $output->writeln('执行成功,数据不存在');

        //对露珠数据进行备份到新的露珠表
        $insert = LuZhuBackupsModel::limit(100)->insertAll($data);
       
        //备份成功后删除露珠表的数据
        if (!$insert) return $output->writeln('执行成功');
         $sel->delete();
         return $output->writeln('执行成功');
         //数据库无触发器，不需要下面的
        //对表的触发器进行删除
        $delete  = use_mysql_query_sql('DROP TRIGGER cant_delete');
        if (!$delete) return $output->writeln('执行成功');

        $sel->delete();
        //删除数据成功之后 对表进行添加  触发器
        $sql ="
            CREATE TRIGGER cant_delete
            BEFORE 
            DELETE
            ON ntp_dianji_lu_zhu
            FOR EACH ROW
            BEGIN
                INSERT INTO `ntp_dianji_run_log` (`id`, `run_time`, `msg`) VALUES (NULL, '2021-11-17 00:00:00', '11');
                SIGNAL SQLSTATE 'HY000' SET MESSAGE_TEXT = '手动抛出异常' ; 
            END";
        use_mysql_query_sql($sql);
        return $output->writeln('执行成功');
    }
}