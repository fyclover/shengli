<?php
use think\facade\Route;

Route::rule('login/index$', '/login.Login/index');//登陆
Route::rule('login/captcha$', '/login.Login/captcha');//验证码
Route::rule('login/captcha_check$', 'admin/Login/captcha_check');//验证码
Route::rule('login/agent$', '/login.agentLogin/index');//服务商登陆

//base里面的方法。都可以调用，三方登录过后调取接口获得后台用户信息（自己信息）
Route::rule('admin/info$', '/auth.Action/curl_user_info');

Route::rule('action/list$', '/auth.Action/index');//后台控制列表
Route::rule('action/add$', '/auth.Action/add');//后台控制列表
Route::rule('action/edit$', '/auth.Action/edit');//后台控制列表
Route::rule('action/del$', '/auth.Action/del');//后台控制列表
Route::rule('action/status$', '/auth.Action/status');//后台控制列表

Route::rule('auth/action$', '/auth.BranchAuth/action_list');//控制器列表
Route::rule('auth/action_edit$', '/auth.BranchAuth/action_edit');//控制器列表
Route::rule('auth/menu$', '/auth.BranchAuth/menu_list');//菜单列表
Route::rule('auth/menu_edit$', '/auth.BranchAuth/menu_edit');//菜单列表

Route::rule('menu/list$', '/auth.Menu/index');//后台菜单列表
Route::rule('menu/add$', '/auth.Menu/add');//后台菜单添加
Route::rule('menu/edit$', '/auth.Menu/edit');//后台菜单修改
Route::rule('menu/detail$', '/auth.Menu/detail');//后台菜单修改
Route::rule('menu/del$', '/auth.Menu/del');//后台菜单删除
Route::rule('menu/column$', '/auth.Menu/lists');//后台表单列表
Route::rule('menu/status$', '/auth.Menu/status');//后台表单列表

Route::rule('role/list$', '/auth.Role/index');//角色列表
Route::rule('role/add$', '/auth.Role/add');//角色列表add
Route::rule('role/edit$', '/auth.Role/edit');//角色列表edit
Route::rule('role/del$', '/auth.Role/del');//角色列表del
Route::rule('role/status$', '/auth.Role/status');//角色列表

Route::rule('role_menu/list$', '/auth.RoleMenu/index');//角色菜单列表分组
Route::rule('role_menu/add$', '/auth.RoleMenu/add');//角色菜单列表添加
Route::rule('role_menu/edit$', '/auth.RoleMenu/edit');//角色菜单列表

Route::rule('power/list$', '/auth.RolePower/index');//角色 api接口列表
Route::rule('power/add$', '/auth.RolePower/add');//角色 api接口列表
Route::rule('power/edit$', '/auth.RolePower/edit');//角色 api接口列表


Route::rule('relevant/list$', '/game.AgentList/index');//代理关系链

//下注
Route::rule('records/list$', '/game.Records/index');
Route::rule('records/edit$', '/game.Records/edit');
Route::rule('records/del$', '/game.Records/del');
Route::rule('records/retreat$', '/game.Records/retreat');
//游戏列表
Route::rule('gamename/list$', '/game.GameName/index');
Route::rule('gamename/status$', '/game.GameName/status');
Route::rule('gamename/edit$', '/game.GameName/edit');
Route::rule('gamename/add$', '/game.GameName/add');
Route::rule('gamename/del$', '/game.GameName/del');
//游戏规则
Route::rule('gamelang/list$', '/game.GameName/game_lang_list');
Route::rule('gamelang/edit$', '/game.GameName/game_lang_edit');

//盈亏排行榜
Route::rule('profit/list$', '/game.Profit/index');
Route::rule('usersort/list$', '/game.UserSort/index');
//游戏赔率列表
Route::rule('gameodds/list$', '/game.Odds/index');
Route::rule('gameodds/edit$', '/game.Odds/edit');
Route::rule('gameodds/add$', '/game.Odds/add');
Route::rule('gameodds/del$', '/game.Odds/del');
Route::rule('gameodds/game$', '/game.Odds/game');



Route::rule('config/list$', '/config.SysConfig/index');//后台配置文件列表
Route::rule('config/add$', '/config.SysConfig/add');//后台添加
Route::rule('config/edit$', '/config.SysConfig/edit');//后台修改
Route::rule('config/detail$', '/config.SysConfig/detail');//配置详情
Route::rule('config/del$', '/config.SysConfig/del');//配置删除
Route::rule('config/info$', '/config.SysConfig/config_info');//配置
#Route::rule('config/info$', '/config.SysConfig/config_info');//配置
Route::rule('clear/token$', '/config.SysConfig/clear_token');//清理token
Route::rule('mysql/backups$', '/config.SysConfig/mysql_backups');//备份露珠表
Route::rule('mysql/records$', '/config.SysConfig/mysql_records');//备份下注表

Route::rule('dashboard/all$', '/count.Dashboard/index');//控制面板


//露珠
Route::rule('luzhu/list$', '/desktop.luzhu/index');
Route::rule('luzhu/add$', '/desktop.luzhu/add');
Route::rule('luzhu/edit$', '/desktop.luzhu/edit');
Route::rule('luzhu/status$', '/desktop.luzhu/status');
Route::rule('luzhu/del$', '/desktop.luzhu/newdel');
Route::rule('luzhu/retreat$', '/desktop.luzhu/retreat');
Route::rule('print/start$', '/desktop.PrintLuzhu/printData');//打印露珠
Route::rule('print/list$', '/desktop.PrintLuzhu/index');//打印露珠列表
//作废露珠
Route::rule('vold/list$', '/desktop.VoidLuzhu/index');
Route::rule('vold/retreat$', '/desktop.VoidLuzhu/retreat');//记录


//台桌
Route::rule('desktop/list$', '/desktop.desktop/index');
Route::rule('desktop/add$', '/desktop.desktop/add');
Route::rule('desktop/edit$', '/desktop.desktop/edit');
Route::rule('desktop/status$', '/desktop.desktop/status');
Route::rule('desktop/updatedianji', '/desktop.desktop/updatedianji');
Route::rule('desktop/del$', '/desktop.desktop/del');
Route::rule('desktop/game$', '/desktop.desktop/game');//游戏分类
Route::rule('desktop/table$', '/desktop.desktop/table_list');//根据游戏类型获取台座列表
Route::rule('desktop/isxh$', '/desktop.desktop/is_xh');//限红开启关闭
//多语言
Route::rule('tablelang/list$', '/desktop.desktop/game_lang_list');
Route::rule('tablelang/edit$', '/desktop.desktop/game_lang_edit');


Route::rule('user/is_status$', '/user.User/is_status');//用户是否虚拟账号设置
Route::rule('user/list$', '/user.User/index');//用户列表
Route::rule('user/agent$', '/user.User/agent');//代理商信息
Route::rule('user/agentedit$', '/user.User/agentedit');//代理商修改密码
Route::rule('user/info$', '/user.User/user_info');//指定用户信息
Route::rule('money/edit$', '/user.User/money_edit');//用户余额修改
Route::rule('xian_hong/edit$', '/user.User/xian_hong');//用户限红

Route::rule('user/edit$', '/user.User/edit');//用户修改
Route::rule('user/add$', '/user.User/add');//
Route::rule('user/del$', '/user.User/del');//
Route::rule('user/detail$', '/user.User/detail');//用户详情
Route::rule('user/status$', '/user.User/status');//用户状态修改
Route::rule('userreal/list$', '/user.RealName/index');//用户身份证列表

Route::rule('pay_bank/list$', '/user.PayBank/index');//支付银行卡列表
Route::rule('pay_bank/del$', '/user.PayBank/del');//支付银行卡删除
Route::rule('pay_bank/default$', '/user.PayBank/default');//支付银行卡修改默认卡
Route::rule('pay_bank/info$', '/user.PayBank/info');//用户银行卡信息
Route::rule('pay_bank/edit$', '/user.PayBank/edit');//修改银行卡信息

//用户消费洗码==统计
Route::rule('records/total$', '/count.Records/index');

Route::rule('money/type$', '/log.MoneyLog/status_type');//资金流动类型
Route::rule('login/log$', '/log.LoginLog/index');//登陆日志
Route::rule('money/log$', '/log.MoneyLog/index');//资金流动日志
Route::rule('admin/log$', '/log.AdminLog/index');//后台操作日志
Route::rule('pay/list$', '/log.PayCash/index');//提现列表日志
Route::rule('xima/list$', '/log.PayCash/xima_list');//洗码列表日志
Route::rule('agent_auth/list$', '/log.PayCash/auth_list');//授权列表日志
Route::rule('record_money/list$', 'admin/log.PayCash/record_list');//下注结算列表日志

Route::rule('recharge/list$', '/log.PayRecharge/index');//充值列表日志
Route::rule('recharge/status$', '/log.PayRecharge/status');//确认充值

Route::rule('upload/video$', '/upload.UploadData/video');//都可以上传

Route::rule('admin/list$', '/user.Admins/index');//后台用户列表
Route::rule('admin/add$', '/user.Admins/add');//后台用户添加
Route::rule('/$', '/Index/index');//后台首页
Route::rule('admin/edit$', '/user.Admins/edit');//后台用户修改
Route::rule('admin/detail$', '/user.Admins/detail');//后台用户信息查看
Route::rule('admin/del$', '/user.Admins/del');//后台用户删除


Route::rule('market_level/list$', '/user.MarketLevel/index');//市场部等级
Route::rule('market_level/add$', '/user.MarketLevel/add');//市场部等级
Route::rule('market_level/edit$', '/user.MarketLevel/edit');//市场部等级
Route::rule('market_level/del$', '/user.MarketLevel/del');//市场部等级
Route::rule('market_level/detail$', '/user.MarketLevel/detail');//市场部等级


Route::rule('notice/list$', '/notice.Notice/index');//公告列表
Route::rule('notice/add$', '/notice.Notice/add');//公告添加
Route::rule('notice/edit$', '/notice.Notice/edit');//公告修改
Route::rule('notice/del$', '/notice.Notice/del');//公告删除
Route::rule('notice/detail$', '/notice.Notice/detail');//公告详情
Route::rule('notice/position$', '/notice.Notice/position');//公告位置
Route::rule('notice/status$', '/notice.Notice/status');//公告上下架