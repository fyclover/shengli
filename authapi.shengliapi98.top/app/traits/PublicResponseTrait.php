<?php


namespace app\traits;

/**
 * 模型公用删除
 * Trait PublicCrudTrait
 * @package app\common\traites
 */
trait PublicResponseTrait
{
    use PublicResponseTrait;

     public function failed(string $message = 'invalid argument', int $code = 0, $data = [])
    {   header('Access-Control-Allow-Origin:*');
        echo json_encode(['data'=>$data,'code'=>$code,'msg'=>$message]);
        exit();
    }

     public function success($data, int $code = 1, string $message = 'ok')
    {
        header('Access-Control-Allow-Origin:*');
        echo json_encode(['data'=>$data,'code'=>$code,'msg'=>$message]);
        exit();
    }
}