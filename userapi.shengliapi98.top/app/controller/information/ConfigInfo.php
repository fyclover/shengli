<?php

namespace app\controller\information;

use app\controller\Base;
use app\model\SysConfig;

class ConfigInfo extends Base
{
    //获取公共配置
    public function config_info()
    {
        $name = $this->request->post('name',null);
        if (empty($name)) return show([], 201, '参数错误');
        $info = SysConfig::get_config($name);
        if (empty($info)) return show([], 201, '配置不存在');
        show($info);
    }

}