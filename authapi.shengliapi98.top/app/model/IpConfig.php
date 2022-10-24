<?php


namespace app\model;


use app\traits\TraitModel;
use think\Model;

class IpConfig extends Model
{
    use TraitModel;
    public $name = 'common_sys_ip_config';
}