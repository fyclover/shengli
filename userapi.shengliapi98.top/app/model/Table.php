<?php


namespace app\model;


use think\Model;

class Table extends Model
{
    public $name = 'dianji_table';
    protected $autoWriteTimestamp = 'start_time';
    //台桌
    protected $status = [
        1 => '正常',
        2 => '暂停'
    ];

    protected $run_status = [
        0 => '暂停',
        1 => '投注',
        2 => '开牌',
        3 => '洗牌中'
    ];

    public function getStartTimeAttr($value)
    {
        if ($value == 0) return $value;
        $status = date('Y-m-d H:i:s', $value);
        return ['test' => $status, 'value' => $value];
    }

    //荷官转换地址
    public function getHeGuanHeadImgAttr($value)
    {
        if (empty($value)) return '';
        if (is_array($value)) return '';
        $value = explode(',', $value);
        if (count($value) > 1) {
            foreach ($value as $key => $v) {
                $value[$key] = config('ToConfig.app_update.image_url') . $v;
            }
            return $value;
        }
        return config('ToConfig.app_update.image_url') . $value[0];
    }

    //获取分页数据
    public static function page_list($map, $limit, $page)
    {
        return self::alias('a')
            ->where($map)
            ->join((new GameType())->name . ' b', 'a.game_type = b.id', 'left')
            ->field('a.*,b.type_name')
            ->order('id desc')
            ->paginate(['list_rows' => $limit, 'page' => $page], false);
    }

    //获取单条数据
    public static function page_one($id)
    {
        $info = Table::where('status','>=',0)->find($id);
        if (empty($info)) show([], config('ToConfig.http_code.error'), '台桌不存在');
        $info['table_explain'] =  TableLangModel::table_explain_value($info);
        return $info;
    }

    //获取多条数据
    public static function page_repeat($where = [], $order = '')
    {
        $self = self::where($where)->where('status','>=',0);
        !empty($order) && $self->order($order);
        $sel = $self->select();
        //查询游戏的多语言
        foreach ($sel as $key=>&$value){
            $value['table_explain'] =  TableLangModel::table_explain_value($value);
        }
        return $sel;
    }

    //台桌开局倒计时 $info台桌信息
    public static function table_opening_count_down($info)
    {
        $end = time() - ($info->getData('start_time') + $info['countdown_time']);
        $info->end_time = 0;
        if ($end <= 0) {
            $info->end_time = abs($end);
        }
        return self::table_open_video_url($info);
    }

    /**
     * 台桌视频地址
     * @param $info /台桌信息
     * @return mixed
     */
    public static function table_open_video_url($info)
    {
        $info->video_near = $info->video_near . $info->id;
        $info->video_far = $info->video_far . $info->id;
        return $info;
    }
    
    //获取台座限红
    public static function table_is_xian_hong($table_id)
    {

        if ($table_id <= 0) {
            return [];
        }

        $table = self::where('id', $table_id)->find();
        if (empty($table)){
            return [];
        }

        $xianhong = ['xian_hong_max' => 0, 'xian_hong_min' => 0];
        if ($table->is_table_xian_hong == 1) {
            switch ($table->game_type) {
                case 2:
                    $xianhong = ['xian_hong_max' => $table->lh_xian_hong_long_max, 'xian_hong_min' => $table->lh_xian_hong_long_min];
                    break;
                case 3:
                    $xianhong = ['xian_hong_max' => $table->bjl_xian_hong_zhuang_max, 'xian_hong_min' => $table->bjl_xian_hong_zhuang_min];
                    break;
                case 6:
                    $xianhong = ['xian_hong_max' => $table->nn_xh_pingbei_max, 'xian_hong_min' => $table->nn_xh_pingbei_min];
                    break;
                case 8:
                    $xianhong = ['xian_hong_max' => $table->sg_xh_pingbei_max, 'xian_hong_min' => $table->sg_xh_pingbei_min];
                    break;
            }
        }
        return $xianhong;
    }
}