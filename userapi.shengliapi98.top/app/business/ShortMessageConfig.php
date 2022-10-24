<?php

namespace app\business;

class ShortMessageConfig
{
    /**
     * @param int $phone 手机号码
     * @return array
     */
    public static function shortMessageConfigInfo(int $phone): array
    {
        $url = 'https://api.onbuka.com/v3/sendSms';
        $user = 'tqiwsblj';
        $pwd = 'bwddniqn';
        $time = time();

        $header[] = "Content-Type:application/json;charset=UTF-8";
        $header[] = "Sign:" . md5($user . $pwd . $time);
        $header[] = "Timestamp:" . $time;
        $header[] = "Api-Key:" . $user;
        $numbers = $phone;
        //生成验证码
        $captcha = rand(1000, 9999);
        //存入redis
        redis()->SADD('register_captcha', $captcha);
        $content = '【太阳汇】本次验证码 verification code 是：'.$captcha;
        $post = [
            "appId" => "OlE0udxf",
            "numbers" => $numbers,
            "content" => $content,
        ];
        return [$url, $post, $header];
    }
}