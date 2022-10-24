<?php


namespace app\controller\game;

use app\controller\Base;

use app\model\GameType as models;
use app\model\GameTypeLangModel;
use app\traits\PublicCrudTrait;


class GameName extends Base
{
    protected $model;
    use PublicCrudTrait;

    /**
     * 游戏列表控制器
     */
    public function initialize()
    {
        $this->model = new models();
        parent::initialize(); // TODO: Change the autogenerated stub
    }

    /**
     * 列表
     */
    public function index()
    {
        //当前页
        $page = $this->request->post('page', 1);
        //每页显示数量
        $limit = $this->request->post('limit', 20);
        //查询搜索条件
        $post = array_filter($this->request->post());
        $map = [];
        $list = $this->model->page_list($map, $limit, $page);
        $this->success($list);
    }

    public function add()
    {
        $post = $this->request->post();
        if (!isset($post['type_name']) || empty($post['type_name'])) $this->failed('游戏名必填');
        $user = $this->model->where('type_name', $post['type_name'])->find();
        if ($user) $this->failed('该游戏以存在');

        if (!empty($post['explain'])) {
            $post['explain'] = str_replace(config('ToConfig.app_update.image_url'), '', $post['explain']);
        }

        $save = $this->model->save($post);
        if ($save) $this->success([]);
        $this->failed('新增失败');
    }

    public function edit()
    {
        $post = $this->request->post();

        if (!isset($post['id']) || $post['id'] <= 0) $this->failed('ID必填');
        if (!isset($post['type_name']) || empty($post['type_name'])) $this->failed('游戏名必填');
        $find = $this->model->where('type_name', $post['type_name'])->where('id', '<>', $post['id'])->find();
        if ($find) $this->failed('该游戏以存在');

        if (!empty($post['explain'])) {
            $post['explain'] = str_replace(config('ToConfig.app_update.image_url'), '', $post['explain']);
        }
        //执行修改数据
        $save = $this->model->update($post);
        if ($save) $this->success([]);
        $this->failed('修改失败');
    }

    //游戏规则多语言
    public function game_lang_list()
    {
        $post = $this->request->post();
        if (!isset($post['id']) || $post['id'] <= 0) $this->failed('ID必填');
        $info = GameTypeLangModel::game_explain($post['id']);
        $this->success($info);
    }

    public function game_lang_edit()
    {
        //过滤数据
        $postField = 'id,zh,en,jp,kor,tha,vnm';
        $post = $this->request->only(explode(',', $postField), 'post', null);
        $post = array_filter($post);
        if (!isset($post['id']) || $post['id'] <= 0) $this->failed('ID必填');
        $id = $post['id'];
        unset($post['id']);
        try {
            foreach ($post as $key => $value) {
                $lang_type = $key;
                if ($key == 'zh') {
                    $find = GameTypeLangModel::page_one(['game_type' => $id, 'lang_type' => 'zh-cn']);
                    $lang_type = 'zh-cn';
                } elseif ($key == 'en') {
                    $find = GameTypeLangModel::page_one(['game_type' => $id, 'lang_type' => 'en-us']);
                    $lang_type = 'en-us';
                } else {
                    $find = GameTypeLangModel::page_one(['game_type' => $id, 'lang_type' => $key]);
                }
                if (empty($find)) {//为空时插入数据
                    GameTypeLangModel::set_insert(['game_type' => $id, 'lang_type' => $lang_type, 'explain' => $value]);
                    continue;
                }
                //非空时修改数据
                GameTypeLangModel::set_update(['id' => $find->id, 'explain' => $value]);
                continue;
            }
        } catch (\Exception $e) {
            $this->failed($e->getMessage());// 这是进行异常捕获
        }
        $this->success([]);
    }
}