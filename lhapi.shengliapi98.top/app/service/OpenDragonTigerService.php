<?php


namespace app\service;

/**
 * 龙虎斗
 * Class OpenDragonTigerService
 * @package app\home\service
 */
class OpenDragonTigerService
{
    public $flower = ['h' => '黑桃', 'r' => '红桃', 'm' => '梅花', 'f' => '方块'];//大到小
    public $flower_num = ['h' => 3, 'r' => 2, 'm' => 1, 'f' => 0];//下标

    //执行所有的函数
    public function runs(array $pai): array
    {
        return $this->calculation_start($this->calculation($pai));
    }

    /**
     * 整理出所有数据
     * @param $pai /数组形式的结果数据
     * @return array
     */
    public function calculation($pai): array
    {
        //整理出所有数据
        $data = [];

        $i = 0;
        foreach ($pai as $key => $value) {
            if ($value == '0|0') {
                unset($pai[$key]);
                continue;
            }
            $data[$i] = explode('|', $value);
            $data[$i][0] = intval($data[$i][0]);
            $i++;
        }
        return $data;
    }

    /**
     * @param array $data /两种扑克信息
     * @return array
     */
    public function calculation_start(array $data): array
    {

        $dragon = $data[0][0];
        $Loong_png = $data[0][1] . $data[0][0] . '.png';
        $tigger = $data[1][0];
        $tigger_png = $data[1][1] . $data[1][0] . '.png';

        $win = 0;
        if ($dragon > $tigger) {
            $win = 1;//龙赢
        } elseif ($dragon < $tigger) {
            $win = 2;//虎赢
        } elseif ($dragon === $tigger) {
            $d = $this->flower_num[$data[0][1]];//龙下标对应花色
            $t = $this->flower_num[$data[1][1]];//虎小标
            if ($d==$t){//花色一样
                $win = 3;//和赢
            }elseif ($d > $t){
                $win = 1;//龙赢
            }elseif ($d < $t){
                $win = 2;//虎赢
            }
        }


        return ['win' => $win, 'dragon' => $dragon, 'tigger' => $tigger, 'dragon_png' => $Loong_png, 'tigger_png' => $tigger_png];
    }

    /**
     * @param $pai /用户购买的赔率牌
     * @param $pai_result /扑克结果
     * @return bool
     */
    public function user_win_or_not($pai, $pai_result)
    {

        switch ($pai) {
            case 20:
                if ($pai_result['win'] == 1) {
                    return true;
                }
                return false;
                break;
            case 21:
                if ($pai_result['win'] == 2) {
                    return true;
                }
                return false;
                break;
            case 22:
                if ($pai_result['win'] == 3) {
                    return true;
                }
                return false;
                break;
        }
        return false;
    }

    //用户购买的结果转汉字
    public function user_pai_chinese(int $res): string
    {
        $res = intval($res);
        $pai = [20 => '龙', 21 => '虎', 22 => '和'];
        return $pai[$res];
    }

    //开牌结果转汉字
    public function pai_chinese(array $paiInfo): string
    {
        if ($paiInfo['win'] == 1) {
            return '龙';
        } elseif ($paiInfo['win'] == 2) {
            return '虎';
        } elseif ($paiInfo['win'] == 3) {
            return '和';
        }
        return '';
    }

    //开牌结果转闪屏
    public function pai_flash(array $paiInfo): array
    {

        $map = [];
        if ($paiInfo['win'] == 1) {
            $map[] = 20;
        } elseif ($paiInfo['win'] == 2) {
            $map[] = 21;
        } elseif ($paiInfo['win'] == 3) {
            $map[] = 22;
        }
        return $map;
    }

    //开牌结果转汉字
    public function pai_info(array $paiInfo): array
    {
        if (empty($paiInfo)) return ['x' => '龙:', 'z' => '虎:'];
        return ['x' => '龙:' . $paiInfo['tigger'] . '  ', 'z' => '虎:' . $paiInfo['dragon']];
    }
}