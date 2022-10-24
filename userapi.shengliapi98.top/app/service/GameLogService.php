<?php


namespace app\service;


class GameLogService
{
    //时间表达式查询
    public static function get_where_time_by($model, $start, $end, $type, $field = 'created_at')
    {
        //时间表达式判断
        if (!empty($start) && !empty($end)) {
            $model = $model->whereTime($field, 'between', [$start, $end]);
        } elseif (!empty($start)) {
            $model = $model->whereTime($field, '>=', $start);
        } elseif (!empty($end)) {
            $model = $model->whereTime($field, '<=', $end);
        } elseif ($type > 0) {
            switch ($type) {
                case 1:
                    $model = $model->whereTime($field, 'today');
                    break;
                case 2:
                    $model = $model->whereTime($field, 'yesterday');
                    break;
                case 3:
                    $model = $model->whereTime($field, 'week');
                    break;
                case 4:
                    $model = $model->whereTime($field, 'month');
                    break;
            }
        }
        return $model;
    }
}