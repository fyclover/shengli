<?php


namespace app\model;

use app\service\OpenDragonTigerService;
use app\service\OpenNiuNiuService;
use app\service\OpenPaiCalculationService;
use app\service\OpenThreeService;
use app\traits\TraitModel;
use think\facade\Log;
use think\Model;

class Luzhu extends Model
{
    use TraitModel;

    public $name = 'dianji_lu_zhu';
    //露珠
    public static $status = 1; //其他作废 1正常露珠
    public static $decor = ['r' => '红桃', 'h' => '黑桃', 'f' => '方块', 'm' => '梅花'];
    public static $poker = ['1' => 1, '2' => 2, '3' => 3, '4' => 4, '5' => 5, '6' => 6, '7' => 7, '8' => 8, '9' => 9, '10' => 10, 'J' => 11, 'Q' => 12, 'K' => 13];


    public static function onBeforeDelete($user)
    {
        Log::write($_SERVER['REMOTE_ADDR'] . '||||' . date('Y-m-d H:i:s') . '||||' . self::getLastSql(), 'error');
        return false;
    }

    public static function page_list($map, $limit, $page, $date)
    {
        $list = self::alias('a');
        $list = self::where_date_model($list,$date,'a.create_time');
        return $list->where($map)
            ->join('dianji_game_type b', 'a.game_type = b.id', 'left')
            ->join('dianji_table c', 'a.table_id = c.id', 'left')
            ->field('a.*,b.type_name,c.table_title')
            ->order('id desc')
            ->paginate(['list_rows' => $limit, 'page' => $page], false)
            ->each(function ($item, $key) {
                if (mb_strlen($item->result_pai) <= 0) {
                    $item['z'] = '';
                    $item['x'] = '';
                } else {
                    $pai = array();
                    switch ($item->game_type) {
                        case 3:
                            $pai = self::luzhu_text($item->result_pai);
                            $item['z'] = $pai['z'];
                            $item['x'] = $pai['x'];
                            break;
                        case 2:
                            $pai = self::luzhu_text_lh($item->result_pai);
                            $item['z'] = $pai['z'];
                            $item['x'] = $pai['x'];
                            break;
                        case 6:
                            $pai = self::luzhu_text_nn($item->result_pai);
                            $item['z'] = $pai['z'];
                            $item['x'] = $pai['x'];
                            break;
                        case 8:
                            $pai = self::luzhu_text_three($item->result_pai);
                            $item['z'] = $pai['z'];
                            $item['x'] = $pai['x'];
                            break;
                    }

                }

            });
    }

    public static function luzhu_text($json): array
    {
        if (mb_strlen($json) <= 0) return ['z' => '庄:', 'x' => '闲:'];
        $pai = new OpenPaiCalculationService();
        if (empty($json)) return ['z' => '庄:', 'x' => '闲:'];
        if (!is_array(json_decode($json, true))) {
            return ['z' => '龙:', 'x' => '虎:'];
        }
        $pai_result = $pai->runs(json_decode($json, true));
        $chinese = $pai->pai_info($pai_result);

        return $chinese;
    }

    public static function luzhu_text_three($json): array
    {

        if (mb_strlen($json) <= 0) return ['z' => '庄:', 'x' => '闲1-3:'];
        $pai = new OpenThreeService();
        if (empty($json)) return ['z' => '庄:', 'x' => '闲1-3:'];
        if (!is_array(json_decode($json, true))) {
            return ['z' => '庄:', 'x' => '闲1-3:'];
        }
        $pai_result = $pai->runs(json_decode($json, true));
        $chinese = $pai->pai_chinese($pai_result);
        $a = ['z' => $chinese, 'x' => ''];
        return $a;
    }

    public static function luzhu_text_lh($json): array
    {
        if (mb_strlen($json) <= 0) return ['z' => '龙:', 'x' => '虎:'];
        $pai = new OpenDragonTigerService();
        if (empty($json)) return ['z' => '龙:', 'x' => '虎:'];
        if (!is_array(json_decode($json, true))) {
            return ['z' => '龙:', 'x' => '虎:'];
        }
        $pai_result = $pai->runs(json_decode($json, true));
        $chinese = $pai->pai_info($pai_result);
        return $chinese;
    }

    public static function luzhu_text_nn($json): array
    {
        if (mb_strlen($json) <= 0) return ['z' => '庄:', 'x' => '闲1-3:'];
        $pai = new OpenNiuNiuService();
        if (empty($json)) return ['z' => '庄:', 'x' => '闲1-3:'];
        if (!is_array(json_decode($json, true))) {
            return ['z' => '龙:', 'x' => '虎:'];
        }
        $pai_result = $pai->runs(json_decode($json, true));
        $chinese = $pai->pai_chinese($pai_result);
        $a = ['z' => $chinese, 'x' => ''];
        return $a;
    }

    //露珠打印
    public static function print_list($map,$date){
        $list = self::where_date_model('',$date,'create_time');
        $list =  $list->field("FROM_UNIXTIME(create_time,'%Y-%m-%d') as create_date,game_type,table_id,xue_number")
            ->where($map)
            ->group('create_date,table_id,game_type,xue_number')
            ->select()
            ->toArray();
        if (empty($list)) return [];

        foreach ($list as $key=>&$value){
            //查询台座名称
            $value['game_type_name'] = GameType::where('id',$value['game_type'])->value('type_name');
            $value['table_title'] = Table::where('id',$value['table_id'])->value('table_title');
            //查询游戏名称
        }
        return $list;
    }
    //开牌的时候插入露珠查询
    public static function open_query_one($post)
    {
        // 缺少时间s
        $nowTime = time();
        $startTime = strtotime(date("Y-m-d 04:00:00", time()));
        // 如果小于，则算前一天的
        if ($nowTime < $startTime) {
            $startTime = $startTime - (24 * 60 * 60);
        }

        return self::where([
            'status' => $post['status'],
            'table_id' => $post['table_id'],
            'xue_number' => $post['xue_number'],
            'pu_number' => $post['pu_number']
        ])
            ->where('create_time', '>', $startTime)
            ->find();
    }

    public static function open_update_one($id, $post)
    {
        return self::where('id', $id)->update([
            'status' => $post['status'],
            'game_type' => $post['game_type'],
            'result' => $post['result']
        ]);
    }

    //需要打印出来的露珠
    public static function page_list_print($map, $date, $limit, $page, $order)
    {
        $model = self::where_date_model('', $date, 'create_time');
        return $model->where($map)->limit($page, $limit)->order($order)->where('result', '<>', 0)->select()->toArray();
    }

    //露珠样式
    public static function luzhu_style($game_type, $info)
    {
        $data = [];
        switch ($game_type) {
            case 1:
            case 2:
            case 3:
                $data = self::bjl($info);
                break;
            case 6:
                $data = self::cattle($info);
                break;
            case 8:
                $data = self::three($info);
                break;
        }
        return $data;
    }

    protected static function bjl($info)
    {
        // 发给前台的 数据
        $i = 0;
        foreach ($info as $k => $val) {
            $tmp = array();
            $t = explode("|", $val['result']);
            $tmp['result'] = $t[0];
            $tmp['ext'] = $t[1];
            if ($tmp['result'] != 0) {
                $k = 'k' . $i;
                $returnData[$k] = $tmp;
                $i++;
            }
        }
        return $returnData;
    }

    public static function three($info)
    {
        $card = new OpenThreeService();
        $luzhu = [];
        foreach ($info as $k => $val) {
            $result_info = $card->runs(json_decode($val['result_pai'], true));
            //if (!empty($result_info)) $luzhu [] = $card->lz_exhibition($result_info);
            if (!empty($result_info)) {
                $pai_data_info = $card->lz_exhibition($result_info);
                foreach ($pai_data_info as $key => &$value) {
                    $value['win'] = $card->html_show_style($key, $result_info);
                }
                $luzhu[] = $pai_data_info;
            }
        }
        return $luzhu;
    }

    public static function cattle($info)
    {
        $card = new OpenNiuNiuService();
        $luzhu = [];
        foreach ($info as $k => $val) {
            $result_info = $card->runs(json_decode($val['result_pai'], true));
            //if (!empty($result_info)) $luzhu [] = $card->lz_exhibition($result_info);
            if (!empty($result_info)) {
                $pai_data_info = $card->lz_exhibition($result_info);
                foreach ($pai_data_info as $key => &$value) {
                    $value['win'] = $card->html_show_style($key, $result_info);
                }
                $luzhu[] = $pai_data_info;
            }
        }
        return $luzhu;
    }
}