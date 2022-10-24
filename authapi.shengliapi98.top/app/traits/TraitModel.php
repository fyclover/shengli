<?php

namespace app\traits;

trait TraitModel
{
    use PublicDateTrait;
    public static $admin_user;
    /**
     * 直接删除
     * @param $id /主键
     * @return bool
     */
    public function del($id)
    {
        $find = $this->find($id);
        if (empty($find)) return false;
        return $find->delete();
    }

    /**
     * 添加数据
     * @param $data /数据数组
     * @param bool $type /true 单条添加,false多条添加
     */
    public function add(array $data, bool $type = true)
    {
        //单条添加
        if ($type) {
            return $this->insert($data);
        }
        //多条添加
        return $this->insertAll($data);
    }


    public function setStatus($post)
    {
        $id = intval($post['id']);
        $status = intval($post['status']);
        if ($id < 1) return false;
        $find = $this->find($id);
        return $find->save(['status' => $status]);
    }

    /**
     * @param array $userAll  能够查询的用户
     * @param string|null $name 用户id 存储的字段
     * @return array
     */
    public static function user_all_where(array $userAll = [],string $name = null)
    {
        if (empty($userAll) || empty($name)){
            return [];
        }
        return [$name,'in',$userAll];
    }

    //荷官转换地址
    public function getHeGuanHeadImgAttr($value)
    {
        if (empty($value)) return '';
        if (is_array($value)) return '';
        $value = explode(',', $value);
        if (count($value) > 1) {
            foreach ($value as $key => $v) {
                $value[$key] = config('ToConfig.app_update.image_url') . $v;
            }
            return $value;
        }
        return config('ToConfig.app_update.image_url') . $value[0];
    }
}