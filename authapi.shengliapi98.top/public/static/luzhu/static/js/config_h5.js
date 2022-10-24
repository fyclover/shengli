// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// 设置系统中间数据仓库 系统配置显示参数
// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
var is_debug = false; // 开启调试模式 true 开启 false 关闭
// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// 设置路径参数
// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
var game_type = "bjl"; // bjl | longhu | lucky6  支持 百家乐 龙虎 幸运六 的展示
var img_url_path_by_game_type = "wait_set"; // 等待设置中
// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// 设置各种步进参数
// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
var show_zhuzi_html_css_length_px = 36;
var show_dalu_html_css_length_px = 36;
var show_xiaolu_html_css_length_px = 18;
var show_dayan_html_css_length_px = 18;
var show_xiaoqiang_html_css_length_px = 18;
var show_sanxing_html_css_length_px = 36;
// 展示 珠子 html 页面
// function show_zhuzi_html() {
//     show_msg("展示 珠子 html")
//     show_pulic_img_html(show_zhuzi,"jingshan_zhuzi",86,6,img_url_path_by_game_type);
// }
// // 展示 大路 html 页面
// function show_dalu_html() {
//     show_msg("展示 大路 html")
//     show_pulic_img_html(show_dalu_img,"jingshan_dalu",24,66,"dalu");
//     show_pulic_num_html(show_dalu_num,"jingshan_dalu",24,66);
// }
// // 展示 小路 html 页面
// function show_xiaolu_html() {
//     show_msg("展示 小路 html")
//     show_pulic_img_html(show_xiaolu,"jingshan_xiaolu",12,66,"xiaolu");
// }
// // 展示 大眼 html 页面
// function show_dayan_html() {
//     show_msg("展示 大眼 html")
//     show_pulic_img_html(show_dayan,"jingshan_dayan",12,66,"dayan");
// }
// // 展示 小强 html 页面
// function show_xiaoqiang_html() {
//     show_msg("展示 小强 html")
//     show_pulic_img_html(show_xiaoqiang,"jingshan_xiaoqiang",12,66,"xiaoqiang");
// }
// // 展示 三星 html 页面
// function show_sanxing_html() {
//     show_msg("展示 三星 html")
//     show_pulic_img_html(show_sanxing_img,"jingshan_sanxing",24,3,"sanxing");
//     show_pulic_num_html(show_sanxing_num,"jingshan_sanxing",24,3);
// }