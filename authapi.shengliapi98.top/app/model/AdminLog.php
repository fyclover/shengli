<?php


namespace app\model;


use app\traits\TraitModel;
use think\Model;

class AdminLog extends Model
{
    use TraitModel;
    public $name = 'common_admin_log';
}