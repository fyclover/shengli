<?php


namespace app\model;


use app\traits\TraitModel;
use think\Model;

class MarketLevel extends Model
{
    use TraitModel;
    public $name = 'common_market_level';
}