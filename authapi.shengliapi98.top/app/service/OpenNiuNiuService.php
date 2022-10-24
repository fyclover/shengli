<?php


namespace app\service;

/**
 */
class OpenNiuNiuService
{
    public $flower = ['h' => '黑桃', 'r' => '红桃', 'm' => '梅花', 'f' => '方块'];//大到小
    public $flower_num = ['h' => 3, 'r' => 2, 'm' => 1, 'f' => 0];//下标
    public $pai_lv = [30, 31, 32, 33, 34, 35, 36, 37, 38];

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
            if ($key <= 5) {
                $pai[1]['point'][$i] = $value[0];
                $pai[1]['image'][$i] = $value[1] . $value[0] . '.svg';
                $pai[1]['info'][$i . 'k'] = $value[0];
                $pai[1]['decor'][$i . 'k'] = $value[1];
            } elseif ($key > 5 && $key <= 10) {
                $pai[2]['point'][$i] = $value[0];
                $pai[2]['image'][$i] = $value[1] . $value[0] . '.svg';
                $pai[2]['info'][$i . 'k'] = $value[0];
                $pai[2]['decor'][$i . 'k'] = $value[1];
            } elseif ($key > 10 && $key <= 15) {
                $pai[3]['point'][$i] = $value[0];
                $pai[3]['decor'][$i . 'k'] = $value[1];
                $pai[3]['image'][$i] = $value[1] . $value[0] . '.svg';
                $pai[3]['info'][$i . 'k'] = $value[0];
            } elseif ($key > 15 && $key <= 20) {
                $pai[4]['point'][$i] = $value[0];
                $pai[4]['decor'][$i . 'k'] = $value[1];
                $pai[4]['image'][$i] = $value[1] . $value[0] . '.svg';
                $pai[4]['info'][$i . 'k'] = $value[0];
            } elseif ($key > 20 && $key <= 25) {
                $pai[5]['point'][$i] = $value[0];
                $pai[5]['decor'][$i . 'k'] = $value[1];
                $pai[5]['image'][$i] = $value[1] . $value[0] . '.svg';
                $pai[5]['info'][$i . 'k'] = $value[0];
            } elseif ($key > 25 && $key <= 30) {
                $pai[6]['point'][$i] = $value[0];
                $pai[6]['decor'][$i . 'k'] = $value[1];
                $pai[6]['image'][$i] = $value[1] . $value[0] . '.svg';
                $pai[6]['info'][$i . 'k'] = $value[0];
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

            if ($num > 0 && $num < 10) {
                $data[$key]['result'] = $num; //牛 1-9
            } elseif ($num === 0) {
                $data[$key]['result'] = 10;//牛牛
            } elseif ($num === -1) {
                $data[$key]['result'] = 0;//没牛
            } elseif ($num === 999) {
                $data[$key]['result'] = 999;//金花牛
            }
        }
        return $data;
    }

    /**
     * @param $card
     * @return int 0 牛牛  -1 没牛 正常输出多少就是多少牛
     */
    private static function algorithm($card)
    {
        //$card = [10,11,5,5,4];
        if (!is_array($card) || count($card) !== 5) {
            return -1;
        }
        $cow = -1;
        //计算5张牌总值，cow计算牛几。
        $cardall = 0;
        $n = 0;//存储、J、Q、K张数。
        //$v = 0;//存储J、Q、K张数。
        for ($i = 0; $i < 4; $i++)//对5张牌从大到小排序。
        {
            for ($j = $i + 1; $j < 5; $j++) {
                if ($card[$i] < $card[$j]) {
                    $a = $card[$i];
                    $card[$i] = $card[$j];
                    $card[$j] = $a;
                }
            }
        }
        $v = 0;
        for ($i = 0; $i < 5; $i++) {
            if ($card[$i] > 10) {
                $v++;
            }
            if ($card[$i] >= 10) {
                $n++;
                $card[$i] = 10;
            }
            $cardall += $card[$i];
        }

        if ($v == 5) return 999;//全是花牌的牛牛
        if ($n == 5) return 0;//全是大于10 的牛牛

        switch ($n) {
            case 0:  //5张牌中没有一张10、J、Q、K。
            {
                for ($i = 0; $i < 4; $i++) {
                    for ($j = $i + 1; $j < 5; $j++) {
                        //减掉两张牌 == 0 是10的倍数  表示 其他三张加起来等于 10  反向推理
                        if (($cardall - $card[$i] - $card[$j]) % 10 == 0) {
                            $cow = ($card[$i] + $card[$j]) % 10;
                            return $cow;
                        }
                    }
                }
                break;
            }
            case 1:  //5张牌中有一张10、J、Q、K。
            {
                //先判断是否有牛牛，不能判断剩余四张相加为10倍数为牛牛，如 Q 8 5 4 3
                //只能先判断两张是否是10的倍数，如果是再判断剩余是否是10的倍数；有限判断出牛牛；再来判断三张是否有10的倍数，有的话有牛，否则无牛
                for ($i = 1; $i < 4; $i++) {
                    for ($j = $i + 1; $j < 5; $j++) {
                        if (($card[$i] + $card[$j]) % 10 == 0) {
                            $cow = ($cardall - $card[0]) % 10;
                            return $cow;
                        }
                    }
                }
                //再来判断三张是否有10的倍数，有的话有牛，否则无牛 判断是否有牛
                for ($i = 1; $i < 5; $i++)  //剩下四张牌有三张加起来等于10
                {
                    if (($cardall - $card[0] - $card[$i]) % 10 == 0) {
                        $cow = ($cardall - $card[0]) % 10;
                        return $cow;
                    }
                }
                break;
            }
            case 2:  //5张牌中有两张10、J、Q、K。  三张是个牛就有问题，应该优先输出
            {
                if (($cardall - $card[0] - $card[1]) % 10 == 0)//优先牛牛输出 如 J Q 2 3 5；这里先检查剩余是否为牛牛，否则算法有漏洞
                {
                    $cow = 0;
                } else {
                    //10 10 6 5 3     n=2  i=3  j=4   cardall = 34
                    for ($i = $n; $i < 4; $i++)//剩下三（四）张牌有两张加起来等于10。
                    {
                        for ($j = $i + 1; $j < 5; $j++) {
                            if (($card[$i] + $card[$j]) == 10) {
                                $temp = $cardall;
                                for ($k = 0; $k < $n; $k++)//总值减去10、J、Q、K的牌。
                                {
                                    $temp -= $card[$k]; // 18
                                }
                                $cow = $temp % 10;  //8
                            }
                        }
                    }

                }
                break;
            }
            case 3:  //5张牌中有三张10、J、Q、K。
            case 4:  //5张牌中有四张10、J、Q、K。
            case 5:  //5张牌中五张都是10、J、Q、K。
            {
                //$cow = ($card[3] + $card[4]) % 10;
                for ($i = 0; $i < $n; $i++)//总值减去10、J、Q、K的牌。
                {
                    $cardall -= $card[$i];
                }
                return $cardall % 10;
                break;
            }
        }
        return $cow;
    }

    //用户购买的结果转汉字
    public function user_pai_chinese(int $res): string
    {
        $res = intval($res);
        $string = $res % 2 == 0 ? '翻倍' : '平倍';
        switch ($res) {
            case 30://闲1
            case 31:
                $string .= '闲1';
                break;
            case 32://闲2
            case 33:
                $string .= '闲2';
                break;
            case 34://闲3
            case 35:
                $string .= '闲3';
                break;
            case 36:
                $string = '超牛闲1';
                break;
            case 37:
                $string = '超牛闲2';
                break;
            case 38:
                $string = '超牛闲3';
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

            if ($value['result'] > 0 && $value['result'] < 10) {
                $string .= '牛' . $value['result'];
            } elseif ($value['result'] === 10) {
                $string .= '牛牛';
            } elseif ($value['result'] === 0) {
                $string .= '没牛';
            } elseif ($value['result'] === 999) {
                $string .= '金花牛';
            }

        }

        return $string;
    }

    //结算时候计算 订单是否中奖  win ==1 庄赢  ==2 闲赢
    public function user_win_or_not($pai, $pai_result)
    {
        //['r'=>'红桃','h'=>'黑桃','f'=>'方块','m'=>'梅花'];
        $zhuang = $pai_result[1]['result'];//庄家点数
        $zhuang_pai = $pai_result[1]['point'];//庄家牌
        $zhuang_info = $pai_result[1]['info'];//庄家花色
        $zhuang_decor = $pai_result[1]['decor'];//庄家花色组合
        ## @param $open_result 当前闲开牌的结果 闲家的点数 $open_info 花色于与点数的结合  $xian_decor 牌点数组成的花色下标
        ## @param $open_pai 当前闲开牌所有牌点数  $zhuang_pai庄牌型   $zhuang_decor庄花色 $zhuang庄点数
        $calculation = function ($xian, $open_pai, $open_info, $xian_decor) use ($zhuang, $zhuang_pai, $zhuang_info, $zhuang_decor) {
            ##获取花色
            //拿到最大一张牌的花色的key值，并去取出最大一张花色牌
            $x_num = $this->number_repeat($open_pai[0],$open_info);
            //($num,$point,$info,$decor)
            $x = $this->size_decor($x_num,$open_pai,$open_info,$xian_decor);
            $z_num = $this->number_repeat($zhuang_pai[0],$zhuang_pai);
            $z = $this->size_decor($z_num,$zhuang_pai,$zhuang_info,$zhuang_decor);
            #获取花色结束

            ##获取花色
//            //拿到最大一张牌的花色的key值，并去取出最大一张花色牌
//            $key1 = array_search($open_pai[0], $open_info);//拿到花色下标
//            $key2 = array_search($zhuang_pai[0], $zhuang_info);//拿到花色下标
//            $x = $this->flower_num[$xian_decor[$key1]];//闲最大牌花色对应的数字
//            $z = $this->flower_num[$zhuang_decor[$key2]];//庄最大牌花色对应的数字
            #获取花色结束

            $return = ['xian_decor' => $x, 'zhuang_decor' => $z, 'xian' => $xian, 'zhuang' => $zhuang];
            if ($xian > $zhuang) {
                return array_merge($return, ['win' => 2]);
            } elseif ($xian < $zhuang) {
                return array_merge($return, ['win' => 1]);
            }

            //点数相等时 查看谁的最大的牌 比较大
            if ($open_pai[0] > $zhuang_pai[0]) {
                // 判断 闲的点数最大的一个 是否 大于庄的最大点数
                // 牌点数是倒序，最大一张就是第一张 point 这是点数倒序存放
                return array_merge($return, ['win' => 2]);
            } elseif ($open_pai[0] < $zhuang_pai[0]) {
                return array_merge($return, ['win' => 1]);
            }
            //点数相等时。判断谁的花色大
            if ($x > $z) {//闲大于庄
                return array_merge($return, ['win' => 2]);
            }

            return array_merge($return, ['win' => 1]);
        };

        switch ($pai) {
            case 30://闲1 赢  取第二铺牌和庄最对比谁大
            case 31:
            case 36:
                return $calculation($pai_result[2]['result'], $pai_result[2]['point'], $pai_result[2]['info'], $pai_result[2]['decor']);
                break;
            case 32: //闲2
            case 33:
            case 37:
                return $calculation($pai_result[3]['result'], $pai_result[3]['point'], $pai_result[3]['info'], $pai_result[3]['decor']);
                break;
            case 34: //闲3
            case 35:
            case 38:
                return $calculation($pai_result[4]['result'], $pai_result[4]['point'], $pai_result[4]['info'], $pai_result[4]['decor']);
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
        }
        return $array;
    }

    //开牌结果转闪屏
    public function pai_flash(array $paiInfo): array
    {
        $map = [];
        foreach ($this->pai_lv as $key => $value) {
            $win = $this->user_win_or_not($value, $paiInfo);
            //win == 2表示闲赢
            if ($win['win'] == 2) {
                $map[] = $value;
            }
        }
        return $map;
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
    //$num 最大一张出现次数  $point 牌型 $info花色  $decor //庄家花色组合
    protected function size_decor($num,$point,$info,$decor)
    {
        if ($num < 1) {
            $k = array_search($point[0], $info);
            return  $this->flower_num[$decor[$k]];
        }

        $record=[];//记录所有的下标，并取最大一个
        for ($i=0;$i < $num;$i++){
            ##获取花色
            //拿到最大一张牌的花色的key值，并去取出最大一张花色牌
            $key1 = array_search($point[$i], $info);//拿到花色下标
            unset($info[$key1]);
            $record[] = $this->flower_num[$decor[$key1]];//闲最大牌花色对应的数字
        }
        array_multisort($record, SORT_DESC);
        return $record[0];
    }

    /** 2 3 4  闲1下标  闲2下标 闲3下标
     * @param $key
     */
    public function html_show_style($key,$result_info)
    {
        $pei_lv = [30, 32, 34];//定义 闲1  闲2 闲3  基础赔率
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
}