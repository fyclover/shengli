<?php

namespace app\controller\information;

use app\model\GameRecords;
use app\model\UserSet;
use app\model\PayBank;
use app\controller\Base;
use app\validate\Binding as validates;
use think\exception\ValidateException;

class UserInfo extends Base
{
    //获取用户信息
    //获取当前 用户的个人信息
    public function get_user(): string
    {
        if (empty(self::$user)) show([],env('code.error'), 'user information does not exist');

        if (empty(self::$user['user_chip'])) {
            self::$user['user_chip'] = [];
        } else {
            self::$user['user_chip'] = json_decode(self::$user['user_chip'], true)['chip'];
        }
        self::$user['back'] = PayBank::where('u_id', self::$user['id'])->cache(600)->find();
        //获取用户本局下注金额
        self::$user['game_records'] = GameRecords::where(['close_status'=>1,'user_id'=>self::$user['id']])
            ->whereTime('created_at', '-10 Minutes')
            ->field('sum(deposit_amt) deposit_money,sum(bet_amt) bet_money')
            ->find();
        show(self::$user);
    }

    //用户上传头像
    public function user_img_update()
    {
        $img = $this->request->post('img', '');
        if (empty($img)) show([], env('code.error'), 'upload failed');
        $save = UserSet::where('u_id', self::$user['id'])->update(['head_img' => $img]);
        if (!$save) show([],env('code.error'), 'upload failed');
        show();
    }

    public function user_pay_bank()
    {
        //过滤数据
        $postField = 'card,name,user_name,address';
        $post = $this->request->only(explode(',', $postField), 'post', null);
        //验证数据
        try {
            validate(validates::class)->scene('bin')->check($post);
        } catch (ValidateException $e) {
            // 验证失败 输出错误信息
            return show([], env('code.error'), $e->getError());
        }

        $find = PayBank::where('u_id', self::$user['id'])->find();
        if ($find) {
            PayBank::where('u_id', self::$user['id'])->update($post);
        } else {
            $post['u_id'] = self::$user['id'];
            $post['status'] = 0;
            $post['is_default'] = 1;
            PayBank::insert($post);
        }
        return show($post);
    }

    public function user_pay_bank_info()
    {
        $find = PayBank::where('u_id', self::$user['id'])->find();
        return show($find);
    }
}
