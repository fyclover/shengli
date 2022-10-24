<?php


namespace app\controller\login;

use app\model\HomeTokenModel;
use app\model\UserModel;
use app\model\UserSet;
use app\controller\Base;
use app\model\UserModel as models;
use app\validate\Login as validates;
use think\exception\ValidateException;

class UpdatePwd extends Base
{
    protected $model;

    /**
     * 修改密码
     */
    public function initialize()
    {
        $this->model = new models();
        parent::initialize(); // TODO: Change the autogenerated stub
    }

    //修改密码和手机号 获取用户信息在修改密码
    public function change_pwd(): string
    {
        //过滤数据
        $postField = 'pwd,pwd_copy';
        $post = $this->request->only(explode(',', $postField), 'post', null);
        if (empty($post['pwd']) || empty($post['pwd_copy']))  show([], env('code.error'), 'parameter error');
        if ($post['pwd'] !== $post['pwd_copy'])  show([], env('code.error'), 'parameter error');
        //查询手机号是否存在
        //密码加密
        if (isset($post['pwd'])) $post['pwd'] = pwdEncryption($post['pwd']);
        //执行修改
        $save = $this->model->where('id', self::$user['id'])->update(['pwd' => $post['pwd']]);

        (new HomeTokenModel())->where('user_id', self::$user['id'])->delete();
        if ($save)  show();

        show([], env('code.error'), 'failed to modify password');

    }

    //绑定手机号
    public function change_phone(): string
    {
        $phone = $this->request->param('phone', 0);
        if (strlen($phone) < 11)  show([], env('code.error'), 'incorrect number');

        $find = UserModel::where('phone', $phone)->where('id', '<>', self::$user['id'])->find();
        if ($find)  show([], env('code.error'), 'number already exists');

        $save = $this->model->where('id', self::$user['id'])->update(['phone' => $phone]);
        if ($save)  show();
        show([], env('code.error'), 'binding failed');
    }

    //忘记密码
    public function forget(): string
    {
        //过滤数据
        $postField = 'phone,pwd';
        $post = $this->request->only(explode(',', $postField), 'post', null);

        //验证数据
        try {
            validate(validates::class)->scene('forget')->check($post);
        } catch (ValidateException $e) {
            // 验证失败 输出错误信息
            show([], config('ToConfig.http_code.error'), $e->getError());
        }

        //判断输入账号是否正确
        if ($post['phone'] != self::$user['phone']) show([], env('code.error'), 'login mismatch');;
        //插入数据
        $find = $this->model->where(['phone' => $post['phone']])
            ->save(['pwd' => pwdEncryption($post['pwd'])]);
        if (empty($find)) show([], env('code.error'), 'failed to modify password');
        show();
    }

    //用户修改筹码
    public function update_chip(): string
    {
        //过滤数据
        $postField = 'chip';
        $post = $this->request->only(explode(',', $postField), 'post', null);
        if (empty($post)) show([], env('code.error'), 'parameter error');
        $userSet = new UserSet();
        $userSet->where('u_id', self::$user['id'])->update(['user_chip' => json_encode($post)]);
        show();
    }

    //用户修改筹码
    public function update_language(): string
    {
        $post = $this->request->param('lang', 'zh-cn');
        $userSet = new UserSet();
        $userSet->where('u_id', self::$user['id'])->update(['language' => $post]);
        show();
    }
}