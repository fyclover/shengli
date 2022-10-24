<?php

namespace app\controller\information;

use app\BaseController;
use app\model\HomeTokenModel;
use app\model\Notice;
use think\facade\Lang;

class WebConfig extends BaseController
{

    //获取配置文件
    public function get_config(): string
    {
        $name = $this->request->param('name', '');
        return show(get_config($name));
    }

    //公告
    public function notice_list()
    {
        //获取公告位置
        $id = $this->request->param('id/d', 1);
        $map = ['status' => 1, 'position' => $id];
        $web_maintain = 0;
        //查询网址是否在维护中。。。 //$id ==3 时是查询维护中的通告
        if ($id == 3) {
            $get_config = get_config('web_maintain');
            $list = Notice::where($map)->find();
            !empty($get_config) && $web_maintain = $get_config->value;
            $list['web_maintain_status'] = $web_maintain;
        } else {
            $list = Notice::where($map)->select();
        }
      //  $list = $list->hidden(['create_time', 'status', 'position', 'id']);

        show($list);
    }

    public function curl_user_info()
    {
        $token = $this->request->post('token');
        if (empty($token)) return show([], env('code.error'), 'token不存在');
        $res = HomeTokenModel::auth_token($token); //查询token
        if (empty($res)) return show([], 505, 'token无效');
        //校验是否过期的token
        if (time() - strtotime($res['create_time']) >= env('token.home_token_time', 180)) return show([], 505, 'token过期');
        //token没过期，修改当前token在线时间
        return show([], 200, '在线');
    }

    public function get_what_and_img()
    {
        $list = \app\model\SysConfig::where('name', 'in', ['app_webchat_what', 'app_feiji', 'app_webchat_what_img', 'app_feiji_img'])->select()->toArray();

        if ($list) {
            $data = [];
            foreach ($list as $key => $value) {
                if ($value['name'] == 'app_feiji_img') {
                    $data['app_feiji']['image'] = $value['value'];
                }
                if ($value['name'] == 'app_feiji') {
                    $data['app_feiji']['address'] = $value['value'];
                }

                if ($value['name'] == 'app_webchat_what_img') {
                    $data['app_webchat_what']['image'] = $value['value'];
                }
                if ($value['name'] == 'app_webchat_what') {
                    $data['app_webchat_what']['address'] = $value['value'];
                }
            }
            unset($list);
            $list = $data;
        }
        return show($list, 200, 'ok');
    }

    //获取短信国家
    public function get_code_country()
    {
        $lang = $this->request->post('lang','zh-cn');
        if ($lang == 'jpn') $lang ='jp';
        Lang::load(app()->getRootPath().'/app/lang/'.$lang.'.php');
        $code_country = get_config('code_country');
        if (empty($code_country) || empty($code_country->value)) return show([]);

        $array_code_country = explode('|',$code_country->value);

        //遍历数组得到多语言数据
        $data =[];
        foreach ($array_code_country as $key=>$value){
            $res = $this->code_country(intval($value));
            if(empty($res)) continue;
            $data[$key] = $res;
        }
        return show($data);
    }

    private function code_country(int $num)
    {
        $code = [
            ['country' => '', 'name' => 'China', 'num' => 86],
            ['country' => '', 'name' => 'Thailand', 'num' => 66],
            ['country' => '', 'name' => 'Vietnam', 'num' => 84],
            ['country' => '', 'name' => 'Korea', 'num' => 82],
            ['country' => '', 'name' => 'Japan', 'num' => 81],
            ['country' => '', 'name' => 'Philippines', 'num' => 63],
            ['country' => '', 'name' => 'Cambodia', 'num' => 855],
        ];

        foreach ($code as $key => $value) {
            if ($value['num'] == $num) {
                $value['country'] = lang($value['name']);
                return $value;
            }
        }
        return [];
    }
}
