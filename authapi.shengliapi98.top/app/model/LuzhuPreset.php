<?php

namespace app\model;

use app\service\OpenDragonTigerService;
use app\service\OpenNiuNiuService;
use app\service\OpenPaiCalculationService;
use app\service\OpenThreeService;
use think\Model;

class LuzhuPreset extends Model
{
    public $name = 'dianji_lu_zhu_preset';
    /**
     * 预设置露珠
     */
    public static function preset($post)
    {
        switch ($post['game_type']){
            case 1:
            case 3:
                $open = new OpenPaiCalculationService();
                break;
            case 2:
                $open = new OpenDragonTigerService();
                break;
            case 6:
                $open = new OpenNiuNiuService();
                break;
            case 8:
                $open = new OpenThreeService();
                break;
        }
        if (!isset($open)) return false;
        //验证牌json是否合法
        try {
            $open->runs($post['pai_result']);
        }catch (\Exception $e){
            return false;
        }
        return true;
    }

}