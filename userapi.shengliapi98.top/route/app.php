<?php

use think\facade\Route;

Route::rule('user/login/index$', '/login.Login/index');//登陆
Route::rule('user/lz/preset$', '/preset.LuzhuPreset/preset');//预设开奖结果
Route::rule('user/lz/count$', '/preset.LuzhuPreset/getGameTableCount');//预设开奖结果

Route::rule('user/login/register$', '/login.Login/register');//注册
Route::rule('user/login/captcha$', '/login.Login/captcha');//验证码
Route::rule('user/login/test$', '/login.Login/on_trial_user_login');//试用用户登陆
Route::rule('user/login/out$', '/login.Login/sign_out');//退出
Route::rule('user/pwd/update$', '/login.UpdatePwd/change_pwd');//修改密码
Route::rule('user/phone/update$', '/login.UpdatePwd/change_phone');//修改手机
Route::rule('user/chip/update$', '/login.UpdatePwd/update_chip');//修改密码
Route::rule('user/language/update$', '/login.UpdatePwd/update_language');//多语言修改
Route::rule('user/user/index$', '/information.UserInfo/get_user');//用户个人信息

Route::rule('user/login/country$', '/information.WebConfig/get_code_country');//多语言国家
Route::rule('user/user/config$', '/information.WebConfig/get_config');//配置文件
Route::rule('user/user/what$', '/information.WebConfig/get_what_and_img');//配置文件
Route::rule('user/user/bin$', '/information.UserInfo/user_pay_bank');//用户绑定卡号
Route::rule('user/user/bank$', '/information.UserInfo/user_pay_bank_info');//用户卡号信息
Route::rule('user/user/img_update$', '/information.UserInfo/user_img_update');//上传头像
Route::rule('user/notice/list$', '/information.WebConfig/notice_list');//获取公告列表
Route::rule('user/notice_auth/list$', '/information.WebConfigAuth/notice_list');//获取公告列表
Route::rule('user/curl/info$', '/information.WebConfig/curl_user_info');//用户在线状态
//游戏列表
Route::rule('user/game/list$', '/game.GameInfo/get_game_list');
Route::rule('user/game/info$', '/game.GameInfo/get_game_info');
//台桌列表
Route::rule('user/table/list$', '/game.GameInfo/get_table_list');
//台桌信息
Route::rule('user/table/info$', '/game.GameInfo/get_table_info');
//台桌用户金额统计
Route::rule('user/table/bet$', 'home/game.GameInfo/get_table_count');
//游戏赔率
Route::rule('user/odds/list$', '/game.GameInfo/get_odds_list');
//获取视频地址
Route::rule('user/get_table/table_info_video$', '/game.GameInfo/get_table_info_video');
//用户下注记录
Route::rule('user/bet_log/list$', '/game.BetLog/get_bet_log_list');
//用户上下分情况
Route::rule('user/money_out/list$', '/game.BetLog/get_money_in_and_out_log');
Route::rule('user/money_count/list$', '/game.BetLog/get_user_money_count_log');
Route::rule('user/bet_sum/list$', '/game.BetLog/get_bet_by_log');
//用户资金记录
Route::rule('user/money_log/list$', '/game.BetLog/get_money_by_log');
//用户盈亏统计
Route::rule('user/profit_log/list$', '/game.BetLog/get_profit_by_log');

//获取扑克牌型
Route::rule('user/game/poker$', '/game.GameInfo/get_poker_type');

Route::miss(function() {
    return show([],404,'无效路由地址');
});