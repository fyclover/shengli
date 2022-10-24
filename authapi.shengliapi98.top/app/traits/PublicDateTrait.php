<?php

namespace app\traits;

trait PublicDateTrait
{
    /**
     * @param $model /模型
     * @param $date /时间
     * @param string $name /时间字段名称
     * @return \app\model\MenuModel|mixed
     */
    public static  function where_date_model($model, $date, $name = 'create_time')
    {
        $date = array_filter($date);
        if (empty($model)) $model = new self();
        if (isset($date['start']) && isset($date['end'])) {
            $res = $model->whereTime($name, 'between', [$date['start'], $date['end']]);
        } elseif (isset($date['start'])) {
            $res = $model->whereTime($name, '>=', $date['start']);
        } elseif (isset($date['end'])) {
            $res = $model->whereTime($name, '<=', $date['end']);
        } else {
            $res = $model;
        }

        return $res;
    }

    public   function this_where_date_model($model, $date, $name = 'create_time')
    {
        $date = array_filter($date);
        if (empty($model)) $model = $this;
        if (isset($date['start']) && isset($date['end'])) {
            $res = $model->whereTime($name, 'between', [$date['start'], $date['end']]);
        } elseif (isset($date['start'])) {
            $res = $model->whereTime($name, '>=', $date['start']);
        } elseif (isset($date['end'])) {
            $res = $model->whereTime($name, '<=', $date['end']);
        } else {
            $res = $model;
        }

        return $res;
    }

    //时间表达式查询
    public static function get_where_time_by($model, $date, $type, $name = 'created_at')
    {
        //时间表达式判断
        $date = array_filter($date);
        if (isset($date['start']) && isset($date['end'])) {
            $model = $model->whereTime($name, 'between', [$date['start'], $date['end']]);
        } elseif (isset($date['start'])) {
            $model = $model->whereTime($name, '>=', $date['start']);
        } elseif (isset($date['end'])) {
            $model = $model->whereTime($name, '<=', $date['end']);
        } elseif ($type > 0) {
            switch ($type) {
                case 1:
                    $model = $model->whereTime($name, 'today');
                    break;
                case 2:
                    $model = $model->whereTime($name, 'yesterday');
                    break;
                case 3:
                    $model = $model->whereTime($name, 'week');
                    break;
                case 4:
                    $model = $model->whereTime($name, 'month');
                    break;
            }
        }
        return $model;
    }
}