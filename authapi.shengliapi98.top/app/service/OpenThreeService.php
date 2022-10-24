<?php


namespace app\service;

/**
 */
class OpenThreeService
{
    public $flower = ['h' => '黑桃', 'r' => '红桃', 'm' => '梅花', 'f' => '方块'];//大到小
    public $flower_num = ['h' => 3, 'r' => 2, 'm' => 1, 'f' => 0];//下标
    public $pai_lv = [40, 41, 42, 43, 44, 45, 46, 47, 48];

    //执行所有的函数
    public function runs(array $pai): array
    {
        $calculation = $this->calculation($pai);
        $calculation_start = $this->calculation_start($calculation);
        return $this->calculation_result($calculation_start);
    }

    /**
     * 整理出所有数据
     * @param $pai /数组形式的结果数据
     * @return array
     */
    public function calculation(array $pai): array
    {
        //整理出所有数据
        $data = [];
        foreach ($pai as $key => $value) {
            if ($value == '0|0') {
                unset($pai[$key]);
                continue;
            }
            $data[$key] = explode('|', $value);
            $data[$key][0] = intval($data[$key][0]);
        }
        return $data;
    }

    /**
     * 整理牌型
     * @param array $data
     * @return array
     */
    public function calculation_start(array $data): array
    {
        $i = 0;
        $pai = [];
        foreach ($data as $key => $value) {
            if ($key <= 3) {
                $pai[1]['point'][$i] = $value[0];
                $pai[1]['image'][$i] = $value[1] . $value[0] . '.svg';
                $pai[1]['info'][$i . 'k'] = $value[0];
                $pai[1]['decor'][$i . 'k'] = $value[1];
            } elseif ($key > 3 && $key <= 6) {
                $pai[2]['point'][$i] = $value[0];
                $pai[2]['image'][$i] = $value[1] . $value[0] . '.svg';
                $pai[2]['info'][$i . 'k'] = $value[0];
                $pai[2]['decor'][$i . 'k'] = $value[1];
            } elseif ($key > 6 && $key <= 9) {
                $pai[3]['point'][$i] = $value[0];
                $pai[3]['image'][$i] = $value[1] . $value[0] . '.svg';
                $pai[3]['info'][$i . 'k'] = $value[0];
                $pai[3]['decor'][$i . 'k'] = $value[1];
            } elseif ($key > 9 && $key <= 12) {
                $pai[4]['point'][$i] = $value[0];
                $pai[4]['image'][$i] = $value[1] . $value[0] . '.svg';
                $pai[4]['info'][$i . 'k'] = $value[0];
                $pai[4]['decor'][$i . 'k'] = $value[1];
            }
            $i++;
        }
        return $pai;
    }

    //结果整理输出
    public function calculation_result(array $calculation): array
    {
        if (empty($calculation)) return [];
        $data = [];
        foreach ($calculation as $key => $value) {
            array_multisort($value['point'], SORT_DESC);
            array_multisort($value['info'], SORT_DESC);
            $data[$key]['point'] = $value['point'];
            $data[$key]['image'] = $value['image'];
            $data[$key]['decor'] = $value['decor'];
            $data[$key]['info'] = $value['info'];

            //重置开奖结果
            $num = self::algorithm(array_values($value['point']));
            $data[$key]['result'] = $num['num'];
            $data[$key]['result_num'] = $num;
        }
        return $data;
    }

    /**
     * @param $card
     * @return int 0
     */
    private static function algorithm($card)
    {
        // 1- 9 为基本点数，  10 普通三公， 20 为 333，21 AAA - kkk
        // 20-3   21-a  22-2 '23不要，变为20' 24-4 25-5 26-6  27-7  28 -8 29-9  30-10  31-11 32-12  33-13

        //['g'=>'公牌数','num'=>点数]   //$card = [10,11,1];
        $return = function (int $g, int $num, array $pai) {
            return ['g' => $g, 'num' => $num, 'pai' => $pai];
        };
        //错误牌型
        if (!is_array($card) || count($card) !== 3) {
            return $return(-1, -1, []);
        }

        rsort($card);
        $n = 0;//存储、J、Q、K张数。
        $v = 0;//记录超级 3公  也就是 333
        for ($i = 0; $i < 3; $i++) {
            if ($card[$i] > 10) {
                $n++;
            }
            if ($card[$i] == 3){
                $v++;
            }
        }

        //三张牌都是  3
        if ($v == 3) return $return($n, 999, $card);//全是3  超级三公

        //三张花牌
        if ($n == 3) {
            $num = 10;
            if ($card[0]  == $card[1] && $card[0]  == $card[2] ){
                switch ($card[1]){
                    case 11:$num = 31;break;
                    case 12:$num = 32;break;
                    case 13:$num = 33;break;
                }
            }
            return $return($n, $num, $card);//全是花牌 三公
        }

        switch ($n) {
            case 0:  //没有一张J、Q、K。
            {
                $num = 0;//三张牌是一样的
                if ($card[0]  == $card[1] && $card[0]  == $card[2] ){
                    switch ($card[1]){
                        case 1:$num = 21;break;
                        case 2:$num = 22;break;
                        case 3:$num = 999;break;
                        case 4:$num = 24;break;
                        case 5:$num = 25;break;
                        case 6:$num = 26;break;
                        case 7:$num = 27;break;
                        case 8:$num = 28;break;
                        case 9:$num = 29;break;
                        case 10:$num = 30;break;
                    }
                    return $return($n, $num, $card);
                }else{
                    for ($i = 0; $i < 3; $i++) {
                        $num += $card[$i];
                    }
                    return $return($n, $num % 10, $card);
                }
                //得到点数
                break;
            }
            case 1:  //一张J、Q、K。
            case 2:  //二张J、Q、K。
            {
                $num = 0;
                for ($i = 0; $i < 3; $i++) {
                    if ($card[$i] > 10) {
                        continue;
                    }
                    $num += $card[$i];
                }
                //得到点数
                return $return($n, $num % 10, $card);
                break;
            }
        }
    }

    //用户购买的结果转汉字
    public function user_pai_chinese(int $res): string
    {
        $res = intval($res);
        $string = $res % 2 == 0 ? '翻倍' : '平倍';
        switch ($res) {
            case 40://闲1
            case 41:
                $string .= '闲1';
                break;
            case 42://闲2
            case 43:
                $string .= '闲2';
                break;
            case 44://闲3
            case 45:
                $string .= '闲3';
                break;
            case 46:
                $string = '超倍闲1';
                break;
            case 47:
                $string = '超倍闲2';
                break;
            case 48:
                $string = '超倍闲3';
                break;
        }
        return $string;
    }

    //开牌结果转汉字
    public function pai_chinese(array $paiInfo): string
    {
        $string = '';
        foreach ($paiInfo as $key => $value) {

            if ($key == 1) {
                $string = '庄:';
            } else {
                $string .= '+闲' . ($key - 1) . ':';//赋值为闲1 闲2。。。
            }

            //单双公转换汉字
            $gong=function (array $result_num){
                if ($result_num['g'] == 1){
                    return '单公';
                }else if($result_num['g'] == 2){
                    return '双公';
                }
                return '';
            };

            if ($value['result'] > 0 && $value['result'] < 10) {

                if(isset($value['result_num'])){
                    $string .= $gong($value['result_num']);
                }

                $string .=  $value['result'];
            } elseif ($value['result'] >= 10) {
                if ($value['result'] >= 20){
                    $string .= $this->number_transformation($value['result']);
                }else{
                    $string .= '三公';
                }
            } elseif ($value['result'] === 0) {
                if(isset($value['result_num'])){
                    $string .= $gong($value['result_num']);
                }
                $string .= '0';
            }
        }

        return $string;
    }

    //结算时候计算 订单是否中奖  win ==1 庄赢  ==2 闲赢
    public function user_win_or_not($pai, $pai_result)
    {
        //['r'=>'红桃','h'=>'黑桃','f'=>'方块','m'=>'梅花'];
        $zhuang_result = $pai_result[1];
        ## @param $open_result 当前闲开牌的结果 闲家的点数 $open_info 花色于与点数的结合  $xian_decor 牌点数组成的花色下标
        ## @param $open_pai 当前闲开牌所有牌点数  $zhuang_pai庄牌型   $zhuang_decor庄花色 $zhuang庄点数
        switch ($pai) {
            case 40://闲1 赢  取第二铺牌和庄最对比谁大
            case 41:
            case 46:
                return $this->size_comparison($pai_result[2], $zhuang_result);
                break;
            case 42: //闲2
            case 43:
            case 47:
                return $this->size_comparison($pai_result[3], $zhuang_result);
                break;
            case 44: //闲3
            case 45:
            case 48:
                return $this->size_comparison($pai_result[4], $zhuang_result);
                break;
        }
    }

    //露珠展示
    public function lz_exhibition(array $pai_result)
    {
        $array = [];
        foreach ($pai_result as $key => $value) {
            $array[$key]['result'] = $value['result'];
            $array[$key]['image'] = $value['image'];
            $array[$key]['result_num'] = $value['result_num'];
        }
        return $array;
    }

    //开牌结果转闪屏
    public function pai_flash(array $paiInfo): array
    {
        $map = [];
        foreach ($this->pai_lv as $key => $value) {
            $win = $this->user_win_or_not($value, $paiInfo);
            //win == 2 表示闲赢
            if ($win['win'] == 2) {
                $map[] = $value;
            }
        }
        return $map;
    }

    protected function size_comparison($xian_result,$zhuang_result)
    {
        $zhuang = $zhuang_result['result'];//庄家点数
        $xian = $xian_result['result'];//闲家点数
        ##获取花色
        //拿到最大一张牌的花色的key值，并去取出最大一张花色牌
        $x_num = $this->number_repeat($xian_result['point'][0],$xian_result['point']);
        $x = $this->size_decor($x_num,$xian_result);
        $z_num = $this->number_repeat($zhuang_result['point'][0],$xian_result['point']);
        $z = $this->size_decor($z_num,$zhuang_result);
        #获取花色结束
        $return = ['xian_decor' => $x, 'zhuang_decor' => $z, 'xian' => $xian, 'zhuang' =>$zhuang];
        //旁段是否是 最大的那种999
        if ($zhuang_result['result'] === 999){
            return array_merge($return, ['win' => 1]);
        }elseif ($xian_result['result'] === 999){
            return array_merge($return, ['win' => 2]);
        }

        //谁点数大谁赢
        if ($xian > $zhuang) {
            return array_merge($return, ['win' => 2]);
        } elseif ($xian < $zhuang) {
            return array_merge($return, ['win' => 1]);
        }

        //点数相等时 查看谁的最大的牌 比较大谁赢
        // 1 看谁的公多
        if ($xian_result['result_num']['g'] > $zhuang_result['result_num']['g']) {
            return array_merge($return, ['win' => 2]);
        } elseif ($xian_result['result_num']['g'] < $zhuang_result['result_num']['g']) {
            return array_merge($return, ['win' => 1]);
        }

        //2 看谁牌大
        //点数相等时 查看谁的最大的牌 比较大谁赢
        if ($xian_result['point'][0] > $zhuang_result['point'][0]) {
            return array_merge($return, ['win' => 2]);
        } elseif ($xian_result['point'][0] < $zhuang_result['point'][0]) {
            return array_merge($return, ['win' => 1]);
        }

        //点数相等时。判断谁的花色大谁赢
        if ($x > $z) {//闲大于庄
            return array_merge($return, ['win' => 2]);
        }
        return array_merge($return, ['win' => 1]);
    }

    //获取自己最大一张牌有几张，比如   44 3 得到 2
    protected function number_repeat($int,$array){
        $num = 0;
        foreach ($array as $key=>$value){
            if ($int == $value)
            {
                $num ++;
            }
        }
        return $num;
    }

    //当前扑克相同的点数超过1个时，获取几张最大相同点数的扑克最大的一张的花色
    //比如三张 k 分别是 红黑方  获取到的 就是黑桃的下标
    protected function size_decor($num,$result)
    {
        if ($num < 1) {
            $k = array_search($result['point'][0], $result['info']);
            return  $this->flower_num[$result['decor'][$k]];
        }

        $record=[];//记录所有的下标，并取最大一个
        for ($i=0;$i < $num;$i++){
            ##获取花色
            //拿到最大一张牌的花色的key值，并去取出最大一张花色牌
            $key1 = array_search($result['point'][$i], $result['info']);//拿到花色下标
            unset($result['info'][$key1]);
            $record[] = $this->flower_num[$result['decor'][$key1]];//闲最大牌花色对应的数字
        }
        array_multisort($record, SORT_DESC);
        return $record[0];
    }

    /** 2 3 4  闲1下标  闲2下标 闲3下标
     * @param $key
     */
    public function html_show_style($key,$result_info)
    {
        $pei_lv = [40, 42, 44];//定义 闲1  闲2 闲3  基础赔率
        $info = [];
        switch ($key) {
            case 2:
                $info  = $this->user_win_or_not($pei_lv[0], $result_info);
                break;
            case 3:
                $info  = $this->user_win_or_not($pei_lv[1], $result_info);
                break;
            case 4:
                $info = $this->user_win_or_not($pei_lv[2], $result_info);
                break;
        }
        return $info;
    }

    /**
     * @param $num
     */
    protected function number_transformation(int $num){
        switch ($num){
            case 21:
                return '三条A';
                break;
            case 22:
                return '三条2';
                break;
            case 23:
            case 999:
                return '三条3';
                break;
            case 24:
                return '三条4';
                break;
            case 25:
                return '三条5';
                break;
            case 26:
                return '三条6';
                break;
            case 27:
                return '三条7';
                break;
            case 28:
                return '三条8';
                break;
            case 29:
                return '三条9';
                break;
            case 30:
                return '三条10';
                break;
            case 31:
                return '三条J';
                break;
            case 32:
                return '三条Q';
                break;
            case 33:
                return '三条K';
                break;
        }

        return 0;
    }
}