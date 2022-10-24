// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// 设置系统中间数据仓库 用于下层数据 使用
// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
var res_zhuzi_obj = {};// 珠子的 json 对象
var res_dalu_obj_img = {};// 大路的 json 对象
var res_dalu_obj_num = {};// 大路的 json 对象
var res_xiaolu_obj = {};// 小路的 json 对象
var res_dayan_obj = {};// 大眼的 json 对象
var res_sanxing_obj_img = {};// 三星的 json 对象
var res_sanxing_obj_num = {};// 三星的 json 对象
var res_xiaoqiang_obj = {};// 小强的 json 对象
// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// 数据类型定义
/**
 * 主结果：1 庄(龙) 2 闲(虎) 3 和 4 幸运6
 * 扩展： 0 无 1 庄对 2 闲对 3 庄对 + 闲对
 * */
// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// 设置大路的中间数据 为了小路 小强路 及 大眼路
// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
var tmp_dalu_array_for_xiaolu_xiaoqiang_dayan = create_erwei_array(66,66);
var tmp_dalu_array_for_xiaolu_xiaoqiang_dayan_num = create_erwei_array(66,66);
// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// 设置显示位置 中间仓库
// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
var tmp_show_xiaolu = create_erwei_array(66,66);
var tmp_show_xiaoqiang = create_erwei_array(66,66);
var tmp_show_dayan = create_erwei_array(66,66);
// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// 设置 显示位置 最终展示数据
// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
var show_zhuzi = create_erwei_array(30,6);
var show_dalu_img = create_erwei_array(66,6);
var show_dalu_num = create_erwei_array(66,6);
var show_sanxing_img = create_erwei_array(66,3);
var show_sanxing_num = create_erwei_array(66,3);
var show_xiaolu = create_erwei_array(66,6);
var show_xiaoqiang = create_erwei_array(66,6);
var show_dayan = create_erwei_array(66,6);
// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// 设置位置信息 准备 庄闲问路
// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
var change_point_dayan = new Array(66);
var change_point_xiaolu = new Array(66);
var change_point_xiaoqiang = new Array(66);
var change_point_dalu = new Array(66);
// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// 中间量定义
// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function start_zoushi(data) {// 系统启动总开关
	res_org_obj = data
    set_zhuzi_img_path_by_game_type();// 设置游戏类型
    set_all_data_type_is_obj();// 初始化系统的全局变量
    print_all_data_type_is_obj();// 打印全部的中间数据
    set_all_data_type_just_for_show();// 初始化全部的显示数据
    print_all_data_just_for_show();// 打印全部显示数据
    show_html();// 渲染html 显示效果
}
// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// 系统库函数
// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// 是否开启调试模式
function show_msg(msg) {
    if(is_debug == true){
        console.log(msg);
    }
}
// 根据游戏类型 获取 图片路径
function set_zhuzi_img_path_by_game_type() { // 根据游戏类型 判读 数据
    if(game_type == "bjl"){
        img_url_path_by_game_type = "zhuzi";
    }
    if(game_type == "longhu"){
        img_url_path_by_game_type = "longhu";
    }
    if(game_type == "lucky6"){
        img_url_path_by_game_type = "lucky6";
    }
}
// 创建二维数组
function create_erwei_array(x,y) {
    var tmp_array = new Array(x);
    for (var i = 0;i <tmp_array.length;i++){
        tmp_array[i] = new Array(y);
        for(var j = 0;j < y; j++){
            tmp_array[i][j] = "no_color";
        }
    }
    return tmp_array;
}
// 打印二维数组
function print_erwei_array(tmp_array) {
    if(is_debug == true){
        for (var i = 0;i <tmp_array.length;i++){
            var tmp_string = "";
            var tmp_length = tmp_array[i].length;
            for(var j = 0;j < tmp_length; j++){
                if(tmp_array[i][j] != "no_color"){
                    tmp_string += tmp_array[i][j]+",";
                }
            }
            show_msg("第"+i+"维，合计"+j+"个数据，结果为："+tmp_string)
        }
    }
}
//根据游戏反馈的数据 返回 图片名称
function get_imp_name_by_result_type(obj_result) {
    var img_name = 1;

    if(obj_result['result'] == 1 && obj_result["ext"] == 0){// 龙/庄
        img_name = 1;
    }
    if(obj_result['result'] == 1 && obj_result["ext"] == 1){// 龙/庄 庄对
        img_name = 2;
    }
    if(obj_result['result'] == 1 && obj_result["ext"] == 2){// 龙/庄 闲对
        img_name = 3;
    }
    if(obj_result['result'] == 1 && obj_result["ext"] == 3){// 龙/庄 庄对 闲对
        img_name = 4;
    }

    if(obj_result['result'] == 2 && obj_result["ext"] == 0){// 闲/虎
        img_name = 5;
    }
    if(obj_result['result'] == 2 && obj_result["ext"] == 1){// 闲/虎 庄对
        img_name = 6;
    }
    if(obj_result['result'] == 2 && obj_result["ext"] == 2){// 闲/虎 闲对
        img_name = 7;
    }
    if(obj_result['result'] == 2 && obj_result["ext"] == 3){// 闲/虎 庄对 闲对
        img_name = 8;
    }

    if(obj_result['result'] == 3 && obj_result["ext"] == 0){// 和
        img_name = 9;
    }
    if(obj_result['result'] == 3 && obj_result["ext"] == 1){// 和 庄对
        img_name = 10;
    }
    if(obj_result['result'] == 3 && obj_result["ext"] == 2){// 和 闲对
        img_name = 11;
    }
    if(obj_result['result'] == 3 && obj_result["ext"] == 3){// 和 庄对 闲对
        img_name = 12;
    }

    if(obj_result['result'] == 4 && obj_result["ext"] == 0){// 幸运6
        img_name = 13;
    }
    if(obj_result['result'] == 4 && obj_result["ext"] == 1){// 幸运6 庄对
        img_name = 14;
    }
    if(obj_result['result'] == 4 && obj_result["ext"] == 2){// 幸运6 闲对
        img_name = 15;
    }
    if(obj_result['result'] == 4 && obj_result["ext"] == 3){// 幸运6 庄对 闲对
        img_name = 16;
    }
    return img_name;
}
//根据判断 是否存入数据
function set_he_sum_by_he_num(he_num) {
    var tmp_num_sum_obj = {};
    for(var p in  he_num){
        if(he_num[p] == 1){// 向下寻找
            var tmp_sum_he_num = sum_he_by_obj_and_start_position(he_num,p);
            var sum_2 = sum_data_by_position_x(he_num,p);
            var sum_3 = sum_data_by_position_x(tmp_num_sum_obj,p);
            sum_2 = sum_2 + tmp_sum_he_num;
            if(sum_2 > sum_3){
                tmp_num_sum_obj[p] = tmp_sum_he_num;
            }
        }
    }
    return tmp_num_sum_obj;
}
// 求和 当期位置之前的全部数据
function sum_data_by_position_x(he_obj,end_x) {
    var sum = 0;
    var p_num = end_x.substr(1);
    p_num = parseInt(p_num);
    p_num--;
    for(var i = 0; i<= p_num;i++){
        if(typeof he_obj['k'+i] != "undefined"){
            sum += he_obj['k'+i];
        }
    }
    return sum;
}
// 根据起始位置 和 对象数据 获取 当前连续 和的的求和
function sum_he_by_obj_and_start_position(he_obj,start_x) {
    var p_num = start_x.substr(1);
    p_num = parseInt(p_num);
    var tmp_sum = 0;
    var keep_go_flag = 1;
    for(var i = 0; i< 66; i++){
        var p_num_now = p_num + i;
        if(typeof  he_obj["k"+p_num_now] == "undefined"){
            keep_go_flag = 0;
        }
        if(keep_go_flag == 1){
            if(he_obj["k"+p_num_now] == 1){
                tmp_sum++;
            }
        }
    }
    return tmp_sum;
}
// 压缩显示数据
function yasuo_to_show_dalu_img_and_num() {
    var tmp_img = {};
    var tmp_num = {};
    var i = 0;
    for(var p in  res_dalu_obj_img){
        // 不是 和
        var he_array = [9.10,11,12]
        if(he_array.indexOf(res_dalu_obj_img[p]) == -1){
            tmp_img["k"+i] = res_dalu_obj_img[p];
            if(typeof res_dalu_obj_num[p] != "undefined"){// 同步压缩数字
                tmp_num["k"+i] = res_dalu_obj_num[p];
            }

            i++;// 递增
        }
    }
    res_dalu_obj_img = tmp_img;
    res_dalu_obj_num = tmp_num;
}
// 压缩整理 三星数据
function yasuo_to_show_sanxing_img_and_num() { // 三星的 对象数据源 跟 大路的对象数据源 是一样的
    var tmp_obj = {};
    var red_array = [1,2,3,4,13,14,15,16];
    var blue_array = [5,6,7,8];
    for(var p in res_dalu_obj_img){
        if(red_array.indexOf(res_dalu_obj_img[p]) != -1){
            tmp_obj[p] = "red";
        }
        if(blue_array.indexOf(res_dalu_obj_img[p]) != -1){
            tmp_obj[p] = "blue";
        }
    }
    res_sanxing_obj_img = tmp_obj;
    res_sanxing_obj_num = res_dalu_obj_num;
}
// 获取 小路 大眼 小强 针对 大路的 中间数据 仓库
function get_tmp_dalu_array_for_xiaolu_xiaoqiang_dayan() {
    var position_x = 0;// 列
    var position_y = 0;// 行
    for(var p in res_dalu_obj_img){
        var p_num = p.substr(1);
        p_num = parseInt(p_num);
        if(p_num == 0){ // 第一位 的 数字 跟 图片 都单独处理
            tmp_dalu_array_for_xiaolu_xiaoqiang_dayan[0][0] = res_dalu_obj_img[p];
            if(typeof res_dalu_obj_num[p] == "number"){
                tmp_dalu_array_for_xiaolu_xiaoqiang_dayan_num[0][0] = res_dalu_obj_num[p];
            }
        }else{
            var p_num_pre = p_num - 1;
            var duibi = get_result_by_t1_and_t2(res_dalu_obj_img["k"+p_num_pre],res_dalu_obj_img["k"+p_num]);
            if(duibi){// 换列
                position_x++;
                position_y = 0;
            }else {
                position_y++;
            }
            tmp_dalu_array_for_xiaolu_xiaoqiang_dayan[position_x][position_y] = res_dalu_obj_img[p];
            if(typeof res_dalu_obj_num[p] == "number"){
                tmp_dalu_array_for_xiaolu_xiaoqiang_dayan_num[position_x][position_y] = res_dalu_obj_num[p];
            }
        }
    }
}
// 针对大路的 是否 换列的 一个
function get_result_by_t1_and_t2(t1,t2) {
    var result = true;
    var red_array = [1,2,3,4];
    var blue_array = [5,6,7,8];
    var type_t1_red = red_array.indexOf(t1);
    var type_t1_blue = blue_array.indexOf(t1);
    var type_t2_red = red_array.indexOf(t2);
    var type_t2_blue = blue_array.indexOf(t2);
    
    var res_t1 = "";
    var res_t2 = "";
    if (type_t1_red != -1){
        res_t1 = "red";
    }
    if (type_t1_blue != -1){
        res_t1 = "blue";
    }
    if (type_t2_red != -1){
        res_t2 = "red";
    }
    if (type_t2_blue != -1){
        res_t2 = "blue";
    }

    if(res_t1 == res_t2){
        result = false;
    }
    if(res_t1 != res_t2){
        result = true;
    }
    return result;
}
// 设置 大眼 对象
function set_danyan_obj_by_dalu_tmp_array() {
    res_dayan_obj = public_set_xiasanlu_obj_by_dalu_tmp_array(1,1,2,0,1,2)
}
// 设置 小路 对象
function set_xiaolu_obj_by_dalu_tmp_array() {
   res_xiaolu_obj = public_set_xiasanlu_obj_by_dalu_tmp_array(2,1,3,0,2,3);
}
// 设置小强对象
function set_xiaoqiang_obj_by_dalu_tmp_array() {
    res_xiaoqiang_obj = public_set_xiasanlu_obj_by_dalu_tmp_array(3,1,4,0,3,4)
}
// 换行 换列 规则
function get_length_by_array(tmp_array) {
    var i = 0;
    for(var p in tmp_array){
        if(tmp_array[p] != "no_color"){
            i++;
        }
    }
    return i;
}
// 换列的规则
function use_lie(x,y,lie_step) {// 判读 前两列 对齐，就是查看数组的有效长度
    var lie_far = x -lie_step;
    var lie_near = x-1;
    var tmp_color = "no_find"; // 默认为红色
    show_msg("执行对比的列为：近："+lie_near+" 远："+lie_far)
    var lie_far_length = get_length_by_array(tmp_dalu_array_for_xiaolu_xiaoqiang_dayan[lie_far]);
    var lie_near_length = get_length_by_array(tmp_dalu_array_for_xiaolu_xiaoqiang_dayan[lie_near]);
    if(lie_near_length == lie_far_length){// 代码对齐了
        tmp_color = "red";
    }else {
        tmp_color = "blue";
    }
    return tmp_color;
}
// 向下规则
function use_down(x,y,hang_step) {// 向下的规则
    var lie_duibi = x-hang_step;
    show_msg("执行对比的列为：近："+x+" 远："+lie_duibi)
    var tmp_color = "no_find"; // 默认为 蓝色
    var y_before = y - 1;
    if(tmp_dalu_array_for_xiaolu_xiaoqiang_dayan[lie_duibi][y] != "no_color"){ // 当前列 有，则为红
        tmp_color = "red";
    }
    if(tmp_dalu_array_for_xiaolu_xiaoqiang_dayan[lie_duibi][y] == "no_color" && tmp_dalu_array_for_xiaolu_xiaoqiang_dayan[lie_duibi][y_before] == "no_color"){ // 直落 | 长闲 或者 长庄
        tmp_color = "red";
    }
    if(tmp_dalu_array_for_xiaolu_xiaoqiang_dayan[lie_duibi][y] == "no_color" && tmp_dalu_array_for_xiaolu_xiaoqiang_dayan[lie_duibi][y_before] != "no_color"){ // 出一个位置 为蓝
        tmp_color = "blue";
    }
    return tmp_color;
}
// 公共的 调用 函数
function public_set_xiasanlu_obj_by_dalu_tmp_array(start_position_x_1,start_position_y_1,start_position_x_2,start_position_y_2,hang_step_x,lie_step_x) {
    var tmp_obj = {};
    var tmp_array = tmp_dalu_array_for_xiaolu_xiaoqiang_dayan;
    if(tmp_array[start_position_x_1][start_position_y_1] != "no_color" || tmp_array[start_position_x_2][start_position_y_2] != "no_color"){
        var i = 0 ;// 计数开始
        var start_x = start_position_x_1;
        var start_y = start_position_y_1;
        // 符合执行条件 开始执行
        if(tmp_array[start_position_x_1][start_position_y_1] != "no_color"){
            start_x = start_position_x_1;
            start_y = start_position_y_1;
        }else if(tmp_array[start_position_x_2][start_position_y_2] != "no_color"){
            start_x = start_position_x_2;
            start_y = start_position_y_2;
        }
        // show_msg("搜索起始位置为：")
        // show_msg(start_x +"|" + start_y);
		// 单独设计 开始 一列  bug 修正
		for(var y = start_y; y< 66; y++){
		    if(tmp_array[start_x][y] != "no_color"){
		        if(y == 0){// 第一粒
		            tmp_obj["k"+i] = use_lie(start_x,y,lie_step_x);
		        }else{// 其他粒
		            tmp_obj["k"+i] = use_down(start_x,y,hang_step_x);
		        }
		        i++;
		    }
		}			 
        for(var x =start_x; x < 66 ; x++){
            for(var y = start_y; y< 66; y++){
                if(tmp_array[x][y] != "no_color"){
                    if(y == 0){// 第一粒
                        tmp_obj["k"+i] = use_lie(x,y,lie_step_x);
                    }else{// 其他粒
                        tmp_obj["k"+i] = use_down(x,y,hang_step_x);
                    }
                    i++;
                }
            }
        }
    }
    return tmp_obj;
}
//初始化全部的数据
function set_all_data_type_is_obj() {
    // 设置 珠子 源数据：原始数据
    for(var p in res_org_obj){
        res_zhuzi_obj[p] = get_imp_name_by_result_type(res_org_obj[p]);
    }
    // 设置 大路 源数据：原始数据
    var tmp_obj_dalu_img = {};
    var tmp_obj_dalu_num = {};
    var position_i = 0;
    for(var p in res_org_obj){
        if( res_org_obj[p]["result"] != 3){// 非 和
            tmp_obj_dalu_img["k"+position_i] = get_imp_name_by_result_type(res_org_obj[p]);
        }
        if (res_org_obj[p]["result"] == 3) {// 和
            var tmp_i = position_i - 1;
            tmp_obj_dalu_num["k"+tmp_i] = 1; // 存放次数
        }
        position_i++;
    }
    res_dalu_obj_num = set_he_sum_by_he_num(tmp_obj_dalu_num); // 赋值 大路 和求值信息
    res_dalu_obj_img = tmp_obj_dalu_img; // 赋值 大路 图片数据

    yasuo_to_show_dalu_img_and_num();// 压缩显示 大路
    yasuo_to_show_sanxing_img_and_num(); // 压缩显示 小路
    // 准备一下 小路 小强路 大眼路的 数据
    get_tmp_dalu_array_for_xiaolu_xiaoqiang_dayan();// 获取中间 数据 仓库
    // 设置 大眼 源数据：大路红蓝显示数据
    set_danyan_obj_by_dalu_tmp_array();
    // 设置 小路 源数据：大路红蓝显示数据
    set_xiaolu_obj_by_dalu_tmp_array();
    // 设置 小强 源数据：大路红蓝显示数据
    set_xiaoqiang_obj_by_dalu_tmp_array();

}
// 打印全部中间数据 
function print_all_data_type_is_obj() {
    show_msg("打印珠子/龙虎/幸运六")
    show_msg(res_zhuzi_obj)
    show_msg("打印大路 图片信息")
    show_msg(res_dalu_obj_img)
    show_msg("打印大路 数字信息")
    show_msg(res_dalu_obj_num)
    show_msg("打印 大眼")
    show_msg(res_dayan_obj)
    show_msg("打印 小路")
    show_msg(res_xiaolu_obj)
    show_msg("打印 小强")
    show_msg(res_xiaoqiang_obj)
    show_msg("打印 三星 图")
    show_msg(res_sanxing_obj_img)
    show_msg("打印 三星 数字")
    show_msg(res_sanxing_obj_num)
    show_msg("打印 小强 小路 大眼 中间参考 变量")
    print_erwei_array(tmp_dalu_array_for_xiaolu_xiaoqiang_dayan);
    show_msg("打印 小强 小路 大眼 中间参考 变量 数字")
    print_erwei_array(tmp_dalu_array_for_xiaolu_xiaoqiang_dayan_num);
}
// 打印全部的显示数据
function print_all_data_just_for_show() {
    show_msg("+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++")
    show_msg("+++++++++++++++++++++++++++++++++++++显示强有力的界限分割符号++++++++++++++++++++++++++++++++++++++++")
    show_msg("+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++")
    show_msg("显示打印：珠子/龙虎/幸运六")
    print_erwei_array(show_zhuzi)
    show_msg("显示打印：大路 图片信息")
    print_erwei_array(show_dalu_img)
    show_msg("显示打印：大路 数字信息")
    print_erwei_array(show_dalu_num)
    show_msg("显示打印： 大眼")
    print_erwei_array(show_dayan)
    show_msg("显示打印： 小路")
    print_erwei_array(show_xiaolu)
    show_msg("显示打印： 小强")
    print_erwei_array(show_xiaoqiang)
    show_msg("显示打印： 三星 图")
    print_erwei_array(show_sanxing_img)
    show_msg("显示打印： 三星 数字")
    print_erwei_array(show_sanxing_num)
    show_msg("显示打印： 大眼 小路 小强 大路 的转弯节点")
    show_msg(change_point_dayan)
    show_msg(change_point_xiaolu)
    show_msg(change_point_xiaoqiang)
    show_msg(change_point_dalu)
}
// 从 对象 转成 中间 队列
function change_from_obj_to_array(tmp_obj) {
    var position_x = 0;// 列
    var position_y = 0;// 行
    var tmp_array = create_erwei_array(66,66)
    for(var p in tmp_obj){
        var p_num = p.substr(1);
        p_num = parseInt(p_num);
        if(p_num == 0){
            tmp_array[0][0] = tmp_obj[p];
        }else{
            var p_num_pre = p_num - 1;
            if(tmp_obj["k"+p_num_pre] != tmp_obj["k"+p_num]){// 换列
                position_x++;
                position_y = 0;
            }else {
                position_y++;
            }
            tmp_array[position_x][position_y] = tmp_obj[p];
        }
    }
    return tmp_array;
}
// 设置 转弯的显示
function set_show_by_array(org_array,lu_type,org_array_num) {
    var tmp_array_img = create_erwei_array(66,6); // 中间存放位置 图片
    var tmp_array_num = create_erwei_array(66,6); // 中间存放位置 数字
    var tmp_change_point = new Array(66);
    for(var i = 0 ;i < 66;i++){
        tmp_change_point[i] = 5;
    }
    var tmp_use_flag = create_erwei_array(66,66); // 被使用了 标记为 "use"
    for(var i = 0; i<66; i++){
        var change_point = 5; // 记录使用，然后 就没什么用了 默认位置
        for(var j = 0; j<66; j++){
            if(org_array[i][j] != "no_color" && typeof org_array[i][j] != "undefined"){
                show_msg("执行计算程序")
                show_msg("+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++")
                show_msg("++++++++++++++++++++++++开始           ++++++++++++++++++++++++++++++++++++++++++++++++++")
                show_msg("当前判定的为：第"+i+"列，第"+j+"行，数据为："+org_array[i][j]);
                if(org_array_num != "no_num"){
                    show_msg("当前对应的数字信息为："+org_array_num[i][j]);
                    show_msg("当前传入数字的类型为："+(typeof org_array_num[i][j]))
                }

                show_msg("当期准备操作位置的占用信息为："+tmp_use_flag[i][j]);
                show_msg("当前列的预设转弯节点为："+change_point)

                // 首先更新 change_point
                if(j <= change_point && tmp_use_flag[i][j] == "use"){// 如果不是 第一次的，不更新 仅保留最小的 转弯节点
                    change_point = j-1; // 默认向上减少一位，始终更新到第一个节点
                }

                if(j < change_point){// 小于 转弯节点
                    show_msg("执行正常下行程序")
                    tmp_array_img[i][j] = org_array[i][j];// 图 正常进入仓库
                    if(org_array_num != "no_num" && typeof org_array_num[i][j] == "number"){
                        show_msg("正常程序下 存入了数字 到数字显示图层")
                        tmp_array_num[i][j] = org_array_num[i][j]
                    }
                    tmp_use_flag[i][j] = "use";// 正常进入仓库 同时标记为 被使用
                }else if(j>= change_point){ // 大于转弯节点 无论 是否被占用 都会 转弯的
                    show_msg("执行占用转弯程序")
                    //计算位置
                    var change_step = j - change_point;
                    var new_i = i + change_step;
                    var new_j = change_point;
                    show_msg("新存入的位置为：第"+new_i+"列，第"+new_j+"行")
                    // 存放数据
                    tmp_array_img[new_i][new_j] = org_array[i][j];// 正常进入仓库
                    if(org_array_num != "no_num" && typeof org_array_num[i][j] == "number"){
                        show_msg("转弯程序下 存入了数字 到数字显示图层")
                        tmp_array_num[new_i][new_j] = org_array_num[i][j]
                    }
                    tmp_use_flag[new_i][new_j] = "use";// 正常进入仓库 同时标记为 被使用 新位置
                    tmp_use_flag[i][j] = "use";// 正常进入仓库 同时标记为 被使用  老位置
                }
                show_msg("++++++++++++++++++++++++结束           ++++++++++++++++++++++++++++++++++++++++++++++++++")
                show_msg("+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++")

            }
            tmp_change_point[i] = change_point;// 更新每组的 转弯节点 到 全局变量
        }
    }
    if(lu_type == "dayan"){
        change_point_dayan = tmp_change_point;
    }
    if(lu_type == "xiaolu"){
        change_point_xiaolu = tmp_change_point;
    }
    if(lu_type == "xiaoqiang"){
        change_point_xiaoqiang = tmp_change_point;
    }
    if(lu_type == "dalu"){
        change_point_dalu = tmp_change_point;
        show_dalu_num = tmp_array_num;
        //print_erwei_array(tmp_array_num);// 打印 输出一下 数据
    }

    return tmp_array_img;
}
// 设置全部类型的显示数据
function set_all_data_type_just_for_show() {
    // 设置 珠子
    var i = 0;
    for(var p in res_zhuzi_obj){
        var start_x = Math.floor(i / 6);
        var start_y = i % 6;
        show_zhuzi[start_x][start_y] = res_zhuzi_obj[p]
        i++;
    }
    // 设置 三星
    var i = 0;
    for(var p in res_sanxing_obj_img){
        var start_x = Math.floor(i/3);
        var start_y = i%3;
        show_sanxing_img[start_x][start_y] = res_sanxing_obj_img[p];
        if(typeof res_sanxing_obj_num[p] == "number"){
            show_sanxing_num[start_x][start_y] = res_sanxing_obj_num[p];
        }
        i++;
    }
    // 首先 完成 中间数据 转换 从对象 转换到 数组
    tmp_show_dayan = change_from_obj_to_array(res_dayan_obj);
    tmp_show_xiaolu = change_from_obj_to_array(res_xiaolu_obj);
    tmp_show_xiaoqiang = change_from_obj_to_array(res_xiaoqiang_obj);
    /**
     * 说明：以下四路主要涉及的问题就是 转弯的问题
     */
    // 设置 大路
    show_dalu_img = set_show_by_array(tmp_dalu_array_for_xiaolu_xiaoqiang_dayan,"dalu",tmp_dalu_array_for_xiaolu_xiaoqiang_dayan_num);
    // 设置 大眼
    show_dayan = set_show_by_array(tmp_show_dayan,"dayan","no_num");
    // 设置 小路
    show_xiaolu = set_show_by_array(tmp_show_xiaolu,"xiaolu","no_num");
    // 设置 小强
    show_xiaoqiang = set_show_by_array(tmp_show_xiaoqiang,"xiaoqiang","no_num");
}

// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// 系统库函数  展示类函数
// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// 展示 珠子 html 页面
function show_zhuzi_html() {
    show_msg("展示 珠子 html")
    show_pulic_img_html(show_zhuzi,"jingshan_zhuzi",show_zhuzi_html_css_length_px,6,img_url_path_by_game_type);
}
// 展示 大路 html 页面
function show_dalu_html() {
    show_msg("展示 大路 html")
    show_pulic_img_html(show_dalu_img,"jingshan_dalu",show_dalu_html_css_length_px,66,"dalu");
    show_pulic_num_html(show_dalu_num,"jingshan_dalu",show_dalu_html_css_length_px,66);
}
// 展示 小路 html 页面
function show_xiaolu_html() {
    show_msg("展示 小路 html")
    show_pulic_img_html(show_xiaolu,"jingshan_xiaolu",show_xiaolu_html_css_length_px,66,"xiaolu");
}
// 展示 大眼 html 页面
function show_dayan_html() {
    show_msg("展示 大眼 html")
    show_pulic_img_html(show_dayan,"jingshan_dayan",show_dayan_html_css_length_px,66,"dayan");
}
// 展示 小强 html 页面
function show_xiaoqiang_html() {
    show_msg("展示 小强 html")
    show_pulic_img_html(show_xiaoqiang,"jingshan_xiaoqiang",show_xiaoqiang_html_css_length_px,66,"xiaoqiang");
}
// 展示 三星 html 页面
function show_sanxing_html() {
    show_msg("展示 三星 html")
    show_pulic_img_html(show_sanxing_img,"jingshan_sanxing",show_sanxing_html_css_length_px,3,"sanxing");
    show_pulic_num_html(show_sanxing_num,"jingshan_sanxing",show_sanxing_html_css_length_px,3);
}
// 展示页面的公共部分
function show_pulic_img_html(source_array,target_div,step_size,hang_range,img_url_path) {
    var html_content = "";
    var lie_length = source_array.length;
    for(var i = 0; i < lie_length; i++){
        for (var j = 0;j< hang_range;j++){
            var left = step_size * i;
            var top = step_size * j;
            if(source_array[i][j] != "no_color" && source_array[i][j] != "no_find" && typeof source_array[i][j] != "undefined"){
                var tmp_content = "<div class='content' style='left: "+left+"px; top: "+top+"px; width: "+step_size+"px; height: "+step_size+"px;'>"+"<img src='./static/img/"+img_url_path+"/"+source_array[i][j]+".png'>"+"</div>";
                html_content += tmp_content;
            }
        }
    }
    $("."+target_div).html(html_content);
}
// 展示 数字
function show_pulic_num_html(source_array,target_div,step_size,hang_range) {
    var html_content = "";
    var lie_length = source_array.length;
    for(var i = 0; i < lie_length; i++){
        for (var j = 0;j< hang_range;j++){
            var left = step_size * i;
            var top = step_size * j;
            if(source_array[i][j] != "no_color" && source_array[i][j] != "no_find" && typeof source_array[i][j] != "undefined" ){
                var tmp_content = "<div class='content_num' style='left: "+left+"px; top: "+top+"px; width: "+step_size+"px; height: "+step_size+"px;'>"+source_array[i][j]+"</div>";
                html_content += tmp_content;
            }
        }
    }
    $("."+target_div).append(html_content);
}
// 渲染 html 显示 效果
function show_html() {
    show_msg("+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++")
    show_msg("++++++++++++++++++++++++++++++++++++超级霸气 展示html 分隔符+++++++++++++++++++++++++++++++++++++++++")
    show_msg("+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++")
    show_zhuzi_html();
    show_dalu_html();
    show_xiaolu_html();
    show_dayan_html();
    show_xiaoqiang_html();
    show_sanxing_html();
}
// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// 系统库函数  庄闲 问路 参数定义
// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// 静态初始化 变量
var wenlu_img_path = "./static/img/wenlu/";
var wenlu_static_img_path = "./static/img/wenlu_static/";
var wenlu_target_postion = {
    "zhuzi":"jingshan_zhuzi",
    "dalu":"jingshan_dalu",
    "xiaolu":"jingshan_xiaolu",
    "xiaoqiang":"jingshan_xiaoqiang",
    "dayan":"jingshan_dayan",
    "sanxing":"jingshan_sanxing",
}
var wenlu_step = {
    "zhuzi":48,
    "dalu":24,
    "xiaolu":12,
    "xiaoqiang":12,
    "dayan":12,
    "sanxing":24,
}
// 静图地址
var wenlu_static_png_obj_img_tail = {
    "zhuzi":"_zhuzi.png",
    "dalu":"_dalu.png",
    "xiaolu":"_xiaolu.png",
    "xiaoqiang":"_xiaoqiang.png",
    "dayan":"_dayan.png",
    "sanxing":"_zhuzi.png",
}
// 动图 地址
var wenlu_gif_obj_img_tail = {
    "zhuzi":"_zhuzi.gif",
    "dalu":"_dalu.gif",
    "xiaolu":"_xiaolu.gif",
    "dayan":"_dayan.gif",
    "xiaoqiang":"_xiaoqiang.gif",
    "sanxing":"_sanxing.gif",
}
// 根据程序 生成的地址 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// 根据程序 动态调整变量
var wenlu_data_obj_zhuzi_red = {"x":0,"y":0,"color":"red"};
var wenlu_data_obj_zhuzi_blue = {"x":0,"y":0,"color":"blue"};
var wenlu_data_obj_dalu_red = {"x":0,"y":0,"color":"red"};
var wenlu_data_obj_dalu_blue = {"x":0,"y":0,"color":"blue"};
var wenlu_data_obj_dayan_red = {"x":0,"y":0,"color":"red"};
var wenlu_data_obj_dayan_blue = {"x":0,"y":0,"color":"blue"};
var wenlu_data_obj_xiaolu_red = {"x":0,"y":0,"color":"red"};
var wenlu_data_obj_xiaolu_blue = {"x":0,"y":0,"color":"blue"};
var wenlu_data_obj_xiaoqiang_red = {"x":0,"y":0,"color":"red"};
var wenlu_data_obj_xiaoqiang_blue = {"x":0,"y":0,"color":"blue"};
var wenlu_data_obj_sanxing_red = {"x":0,"y":0,"color":"red"};
var wenlu_data_obj_sanxing_blue = {"x":0,"y":0,"color":"blue"};
// 展示显示位置  只是 变更了一下显示 位置
var wenlu_show_data_obj_zhuzi_red = {"x":0,"y":0,"color":"red"};
var wenlu_show_data_obj_zhuzi_blue = {"x":0,"y":0,"color":"blue"};
var wenlu_show_data_obj_dalu_red = {"x":0,"y":0,"color":"red"};
var wenlu_show_data_obj_dalu_blue = {"x":0,"y":0,"color":"blue"};
var wenlu_show_data_obj_dayan_red = {"x":0,"y":0,"color":"red"};
var wenlu_show_data_obj_dayan_blue = {"x":0,"y":0,"color":"blue"};
var wenlu_show_data_obj_xiaolu_red = {"x":0,"y":0,"color":"red"};
var wenlu_show_data_obj_xiaolu_blue = {"x":0,"y":0,"color":"blue"};
var wenlu_show_data_obj_xiaoqiang_red = {"x":0,"y":0,"color":"red"};
var wenlu_show_data_obj_xiaoqiang_blue = {"x":0,"y":0,"color":"blue"};
var wenlu_show_data_obj_sanxing_red = {"x":0,"y":0,"color":"red"};
var wenlu_show_data_obj_sanxing_blue = {"x":0,"y":0,"color":"blue"};
// 问路 庄闲的 html
var wenlu_html_zhuzi_red = "";
var wenlu_html_zhuzi_blue = "";
var wenlu_html_dalu_red = "";
var wenlu_html_dalu_blue = "";
var wenlu_html_xiaolu_red = "";
var wenlu_html_xiaolu_blue = "";
var wenlu_html_dayan_red = "";
var wenlu_html_dayan_blue = "";
var wenlu_html_xiaoqiang_red = "";
var wenlu_html_xiaoqiang_blue = "";
var wenlu_html_sanxing_red = "";
var wenlu_html_sanxing_blue = "";
// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// 系统库函数  问路类函数  执行函数
// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// 庄 问路
function update_zhuang() {
    cleart_wenlu();
    updte_zhuang_do();
    setTimeout("cleart_wenlu()",3000);
}
// 闲问路
function update_xian() {
    cleart_wenlu();
    update_xian_do();
    setTimeout("cleart_wenlu()",3000);
}
// init_wenlu();
// show_zhuang_xian_wenlu();


// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// 系统库函数  下面全部 都是 库函数了 没有执行 流程的函数
// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++


// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// 系统库函数  庄闲 问路 库 函数 开始
// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// 执行初始化 程序
function init_wenlu() {
    /**
     * 1 更新 数据
     * 2 更新 显示数据
     * 3 执行写入操作
     */
    show_msg("+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++")
    show_msg("++++++++++++++++++++++++++++++++++++超级霸气 展示庄闲问路 分隔符++++++++++++++++++++++++++++++++++++++")
    show_msg("+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++")


    update_wenlu_data();
    update_wenlu_show_html();
    print_all_data_last_position_and_color();
    print_all_data_last_position_and_color_for_show();
}
// 更新 动态数据
function update_wenlu_data() {
    update_wenlu_data_all();
    update_wenlu_data_all_for_show();
}
// 打印 所有路数 最后展示的位置 跟 颜色
function print_all_data_last_position_and_color_for_show() {
    show_msg("======================================================================================================")
    show_msg("显示 所有路的 阶段性 数据 之 页面上的 的位置 [视图] 及颜色")
    show_msg("======================================================================================================")

    show_msg("珠子 庄问路 后 位置 与颜色")
    show_msg(wenlu_show_data_obj_zhuzi_red)
    show_msg("珠子 闲问路 后 位置 与颜色")
    show_msg(wenlu_show_data_obj_zhuzi_blue)

    show_msg("大路 庄问路 后 位置 与颜色")
    show_msg(wenlu_show_data_obj_dalu_red)
    show_msg("大路 闲问路 后 位置 与颜色")
    show_msg(wenlu_show_data_obj_dalu_blue)

    show_msg("小路 庄问路 后 位置 与颜色")
    show_msg(wenlu_show_data_obj_xiaolu_red)
    show_msg("小路 闲问路 后 位置 与颜色")
    show_msg(wenlu_show_data_obj_xiaolu_blue)

    show_msg("大眼 庄问路 后 位置 与颜色")
    show_msg(wenlu_show_data_obj_dayan_red)
    show_msg("大眼 闲问路 后 位置 与颜色")
    show_msg(wenlu_show_data_obj_dayan_blue)

    show_msg("小强 庄问路 后 位置 与颜色")
    show_msg(wenlu_show_data_obj_xiaoqiang_red)
    show_msg("小强 闲问路 后 位置 与颜色")
    show_msg(wenlu_show_data_obj_xiaoqiang_blue)

    show_msg("三星 庄问路 后 位置 与颜色")
    show_msg(wenlu_show_data_obj_sanxing_red)
    show_msg("三星 闲问路 后 位置 与颜色")
    show_msg(wenlu_show_data_obj_sanxing_blue)
}
// 打印 所有路数 最后的 位置 跟 颜色
function print_all_data_last_position_and_color() {
    show_msg("======================================================================================================")
    show_msg("显示 所有路的 阶段性 数据 之 数据库 数据中的 历史 数据的 最后一项 的位置 [非视图] 及颜色")
    show_msg("======================================================================================================")
    // 珠子
    var tmp_zhuzi_last_position_and_color = get_array_last_position_and_color(show_zhuzi);
    tmp_zhuzi_last_position_and_color = change_number_to_color_red_or_blue(tmp_zhuzi_last_position_and_color);
    show_msg("珠子 最后的颜色 跟 位置信息")
    show_msg(tmp_zhuzi_last_position_and_color)
    // 三星
    var tmp_sanxing_last_position_and_color = get_array_last_position_and_color(show_sanxing_img);
    show_msg("三星 最后的颜色 跟 位置信息")
    show_msg(tmp_sanxing_last_position_and_color)
    // 大路
    var tmp_dalu_last_position_and_color = get_array_last_position_and_color(tmp_dalu_array_for_xiaolu_xiaoqiang_dayan);
    tmp_dalu_last_position_and_color = change_number_to_color_red_or_blue(tmp_dalu_last_position_and_color);
    show_msg("大路 最后的颜色 跟 位置信息")
    show_msg(tmp_dalu_last_position_and_color)
    // 然后 更新 下三路
    // 大眼
    var tmp_dayan_last_position_and_color = get_array_last_position_and_color(tmp_show_dayan);
    show_msg("大眼 最后的颜色 跟 位置信息")
    show_msg(tmp_dayan_last_position_and_color)
    // 小路
    var tmp_xiaolu_last_position_and_color = get_array_last_position_and_color(tmp_show_xiaolu);
    show_msg("小路 最后的颜色 跟 位置信息")
    show_msg(tmp_xiaolu_last_position_and_color)
    // 小强
    var tmp_xiaoqiang_last_position_and_color = get_array_last_position_and_color(tmp_show_xiaoqiang);
    show_msg("小强 最后的颜色 跟 位置信息")
    show_msg(tmp_xiaoqiang_last_position_and_color)
}
// 开始 更新 动态 数据 庄 数据
function update_wenlu_data_all() {
    show_msg("开始更新 基础数据 庄")
    // 首先 更新 上三路
    // 珠子
    var tmp_zhuzi_last_position_and_color = get_array_last_position_and_color(show_zhuzi);
    tmp_zhuzi_last_position_and_color = change_number_to_color_red_or_blue(tmp_zhuzi_last_position_and_color);// 变更 数字 为 颜色
    var new_postion_zhuzi_red = get_next_position_and_color(tmp_zhuzi_last_position_and_color,"red");
    var new_postion_zhuzi_blue = get_next_position_and_color(tmp_zhuzi_last_position_and_color,"blue");
    // 赋值 庄闲的 结果 到全局变量
    wenlu_data_obj_zhuzi_red = new_postion_zhuzi_red;
    wenlu_data_obj_zhuzi_blue = new_postion_zhuzi_blue;
    show_msg("===================================显示 珠子 庄闲预测后的 新的位置 跟颜色 =============================")
    show_msg("开庄的结果：")
    show_msg(new_postion_zhuzi_red)
    show_msg("开闲的结果：")
    show_msg(new_postion_zhuzi_blue)
    // 执行数据 转存 珠子
    // 三星
    var tmp_sanxing_last_position_and_color = get_array_last_position_and_color(show_sanxing_img);
    var new_postion_sanxing_red = get_next_position_and_color(tmp_sanxing_last_position_and_color,"red");
    var new_postion_sanxing_blue = get_next_position_and_color(tmp_sanxing_last_position_and_color,"blue");
    wenlu_data_obj_sanxing_red = new_postion_sanxing_red;
    wenlu_data_obj_sanxing_blue = new_postion_sanxing_blue;
    show_msg("===================================显示 三星 庄闲预测后的  新的位置 跟颜色 ============================")
    show_msg("开庄的结果：")
    show_msg(new_postion_sanxing_red)
    show_msg("开闲的结果：")
    show_msg(new_postion_sanxing_blue)
    // 执行数据 转存 三星
    // 大路
    var tmp_dalu_last_position_and_color = get_array_last_position_and_color(tmp_dalu_array_for_xiaolu_xiaoqiang_dayan);
    tmp_dalu_last_position_and_color = change_number_to_color_red_or_blue(tmp_dalu_last_position_and_color); // 变更 数字 为 颜色
    var new_postion_dalu_red = get_next_position_and_color(tmp_dalu_last_position_and_color,"red");
    var new_postion_dalu_blue = get_next_position_and_color(tmp_dalu_last_position_and_color,"blue");
    wenlu_data_obj_dalu_red = new_postion_dalu_red;
    wenlu_data_obj_dalu_blue = new_postion_dalu_blue;
    show_msg("====================================显示 大路 庄闲预测后的  新的位置 跟颜色 ===========================")
    show_msg("开庄的结果：")
    show_msg(new_postion_dalu_red)
    show_msg("开闲的结果：")
    show_msg(new_postion_dalu_blue)
    // 执行数据 转存 大路
    // 然后 更新 下三路
    // 大眼
    var tmp_dayan_last_position_and_color = get_array_last_position_and_color(tmp_show_dayan);
    var new_postion_dayan_red = get_next_position_and_color_use_in_xia_san_lu(tmp_dayan_last_position_and_color,"red","dayan");
    var new_postion_dayan_blue = get_next_position_and_color_use_in_xia_san_lu(tmp_dayan_last_position_and_color,"blue","dayan");
    wenlu_data_obj_dayan_red = new_postion_dayan_red;
    wenlu_data_obj_dayan_blue = new_postion_dayan_blue;
    show_msg("=====================================显示 大眼 庄闲预测后的  新的位置 跟颜色 ==========================")
    show_msg("开庄的结果：")
    show_msg(new_postion_dayan_red)
    show_msg("开闲的结果：")
    show_msg(new_postion_dayan_blue)
    // 执行数据 转存 大眼
    // 小路
    var tmp_xiaolu_last_position_and_color = get_array_last_position_and_color(tmp_show_xiaolu);
    var new_postion_xiaolu_red = get_next_position_and_color_use_in_xia_san_lu(tmp_xiaolu_last_position_and_color,"red","xiaolu");
    var new_postion_xiaolu_blue = get_next_position_and_color_use_in_xia_san_lu(tmp_xiaolu_last_position_and_color,"blue","xiaolu");
    wenlu_data_obj_xiaolu_red = new_postion_xiaolu_red;
    wenlu_data_obj_xiaolu_blue = new_postion_xiaolu_blue;
    show_msg("======================================显示 小路 庄闲预测后的  新的位置 跟颜色 =========================")
    show_msg("开庄的结果：")
    show_msg(new_postion_xiaolu_red)
    show_msg("开闲的结果：")
    show_msg(new_postion_xiaolu_blue)
    // 执行数据 转存 小路
    // 小强
    var tmp_xiaoqiang_last_position_and_color = get_array_last_position_and_color(tmp_show_xiaoqiang);
    var new_postion_xiaoqiang_red = get_next_position_and_color_use_in_xia_san_lu(tmp_xiaoqiang_last_position_and_color,"red","xiaoqiang");
    var new_postion_xiaoqiang_blue = get_next_position_and_color_use_in_xia_san_lu(tmp_xiaoqiang_last_position_and_color,"blue","xiaoqiang");
    wenlu_data_obj_xiaoqiang_red = new_postion_xiaoqiang_red;
    wenlu_data_obj_xiaoqiang_blue = new_postion_xiaoqiang_blue;
    show_msg("========================================显示 小强 庄闲预测后的  新的位置 跟颜色 =======================")
    show_msg("开庄的结果：")
    show_msg(new_postion_xiaoqiang_red)
    show_msg("开闲的结果：")
    show_msg(new_postion_xiaoqiang_blue)
    // 执行数据 转存 小强
}
// 获取 数组的 最后一个元素的 i,j 值[即：位置] 及其数值
function get_array_last_position_and_color(org_array) {
    var res = {"x":0,"y":0,"color":"no_color"}
    var position_x = 0;
    var position_y = 0;
    var color = "no_find";
    for(var i = 0; i<org_array.length;i++){
        for(var j = 0; j<org_array[i].length;j++){
            if(typeof org_array[i][j] != "undefined" && org_array[i][j] != "no_color"){
                color = org_array[i][j];
                position_x = i;
                position_y = j;
            }
        }
    }
    res = {"x":position_x,"y":position_y,"color":color}
    return res;
}
// 更新 数字 为 颜色
function change_number_to_color_red_or_blue(org_obj) {
    var tmp_obj = org_obj;
    var red_array = [1,2,3,4];
    var blue_array = [5,6,7,8];
    if(red_array.indexOf(tmp_obj["color"]) != -1){
        tmp_obj["color"] = "red";
    }
    if(blue_array.indexOf(tmp_obj["color"]) != -1){
        tmp_obj["color"] = "blue";
    }
    return tmp_obj;
}
// 根据当前 位置 颜色 及 下面的 参数 传入
function get_next_position_and_color(org_obj,result_type) {
    var tmp_new_position_and_color = {"x":0,"y":0,"color":"no_find"};
    var new_position_x = 0;
    var new_position_y = 0;
    if(result_type == org_obj["color"]){// 相同 向下 一行
        new_position_x = org_obj["x"];
        new_position_y = org_obj["y"] + 1;
    }else{// 不同 换列
        new_position_x = org_obj["x"] + 1;
        new_position_y = 0;
    }
    tmp_new_position_and_color = {"x":new_position_x,"y":new_position_y,"color":result_type};
    return tmp_new_position_and_color;
}
// 根据当前 位置 颜色 及 下面的 参数 传入 调整显示 下三路
function get_next_position_and_color_use_in_xia_san_lu(org_obj,result_type,lushu_type) {
    var tmp_new_position_and_color = {"x":0,"y":0,"color":"no_find"};
    var new_position_x = 0;
    var new_position_y = 0;
    var new_color = "no_find";
    /**
     * 因为下三路 根据 大路的 变化 所以需要先搞定 大路的数据
     * @type {{color: string, x: number, y: number}}
     */

    var tmp_dalu_last_position_and_color = get_array_last_position_and_color(tmp_dalu_array_for_xiaolu_xiaoqiang_dayan);
    tmp_dalu_last_position_and_color = change_number_to_color_red_or_blue(tmp_dalu_last_position_and_color); // 变更 数字 为 颜色
    var new_postion_dalu_red = get_next_position_and_color(tmp_dalu_last_position_and_color,"red");
    var new_postion_dalu_blue = get_next_position_and_color(tmp_dalu_last_position_and_color,"blue");

    // 根据 行 与 列 选择不同的 类型
    // 大眼
    if(result_type == "red"){
        tmp_new_position_and_color = get_position_and_color_inside(org_obj,new_postion_dalu_red,lushu_type)
    }
    if(result_type == "blue"){
        tmp_new_position_and_color = get_position_and_color_inside(org_obj,new_postion_dalu_blue,lushu_type)
    }

    // 返回最终结果
    return tmp_new_position_and_color;
}
// 根据 大路的 信息 返回 下三路的信息 内核 处理函数
function get_position_and_color_inside(org_target_now_obj,dalu_new_position_and_color_obj,lushu_type) {
    var tmp_new_position_and_color = {"x":0,"y":0,"color":"no_find"};
    var new_color = "no_find";
    var step = 0;
    // 分开
    if(lushu_type == "dayan"){
        step = 1;
    }
    if(lushu_type == "xiaolu"){
        step = 2;
    }
    if(lushu_type == "xiaoqiang"){
        step = 3;
    }
    // 下列 操作 就是 为了 判定 是否 是 超出范围的。 超出 就不显示哦
    var lie_this_for_lie = dalu_new_position_and_color_obj["x"] - 1;
    var lie_pre_for_lie = dalu_new_position_and_color_obj["x"] - 1 - step;
    var lie_this_for_hang = dalu_new_position_and_color_obj["x"];
    var lie_pre_for_hang = dalu_new_position_and_color_obj["x"] - step;
    // 先操作 庄
    show_msg("lie_this_for_lie:"+lie_this_for_lie+" lie_pre_for_lie:"+lie_pre_for_lie+" lie_this_for_hang:"+lie_this_for_hang+" lie_pre_for_hang:"+lie_pre_for_hang)
    if(lie_this_for_lie < 0 ||lie_pre_for_lie < 0 ||lie_this_for_hang < 0 ||lie_pre_for_hang < 0 ){// 如果有的列 已经 在很前面了 就没有数据的情况
        show_msg("暂时不满足显示 "+lushu_type+"类型的路数 ")// 将 xy 置为 -1 代表 没数据，不显示
        tmp_new_position_and_color = {"x":-1,"y":-1,"color":"no_find"};
        return tmp_new_position_and_color;
    }
    if(lie_this_for_lie >=0 && lie_pre_for_lie >= 0 && lie_this_for_hang >= 0 && lie_pre_for_hang >= 0){// 正常有数据的情况
        show_msg("执行 处理 操作 "+lushu_type+"类型的路数 ")// 将 xy 置为 -1 代表 没数据，不显示
        if(dalu_new_position_and_color_obj["y"] == 0){//执行换列 判断
            var step_lie = step+1;
            show_msg("执行换列判断 ，x:"+dalu_new_position_and_color_obj["x"]+" y:"+dalu_new_position_and_color_obj["y"])
            new_color = use_lie(dalu_new_position_and_color_obj["x"],dalu_new_position_and_color_obj["y"],step_lie);
        }
        if(dalu_new_position_and_color_obj["y"] > 0){ // 执行 向下 或者 直落 判断
            show_msg("执行向下 直落 判断 ，x:"+dalu_new_position_and_color_obj["x"]+" y:"+dalu_new_position_and_color_obj["y"])
            new_color = use_down(dalu_new_position_and_color_obj["x"],dalu_new_position_and_color_obj["y"],step);
        }
    }
    show_msg("根据大路的数据,执行步进为："+step+"，返回"+lushu_type+"的颜色数据为："+new_color);
    // 根据 当前 获取到的颜色 然后再来 获取新的 位置
    tmp_new_position_and_color = get_next_position_and_color(org_target_now_obj,new_color);
    // 返回最终结果
    return tmp_new_position_and_color;
}
// 根据 当前数据 修改为 
function update_wenlu_data_all_for_show() {
    // 珠子
    wenlu_show_data_obj_zhuzi_red = update_wenlu_data_all_for_show_change_position(wenlu_data_obj_zhuzi_red,5);
    wenlu_show_data_obj_zhuzi_blue = update_wenlu_data_all_for_show_change_position(wenlu_data_obj_zhuzi_blue,5);
    // 三星
    wenlu_show_data_obj_sanxing_red = update_wenlu_data_all_for_show_change_position(wenlu_data_obj_sanxing_red,2);
    wenlu_show_data_obj_sanxing_blue = update_wenlu_data_all_for_show_change_position(wenlu_data_obj_sanxing_blue,2);
    // 大路
    var change_point_dalu_red = wenlu_data_obj_dalu_red["x"];
    wenlu_show_data_obj_dalu_red = update_wenlu_data_all_for_show_change_position_other(wenlu_data_obj_dalu_red,change_point_dalu[change_point_dalu_red]);
    var change_point_dalu_red = wenlu_data_obj_dalu_blue["x"];
    wenlu_show_data_obj_dalu_blue = update_wenlu_data_all_for_show_change_position_other(wenlu_data_obj_dalu_blue,change_point_dalu[change_point_dalu_red]);
    // 小路
    var change_point_xiaolu_red = wenlu_data_obj_xiaolu_red["x"];
    wenlu_show_data_obj_xiaolu_red = update_wenlu_data_all_for_show_change_position_other(wenlu_data_obj_xiaolu_red,change_point_xiaolu[change_point_xiaolu_red]);
    var change_point_xiaolu_red = wenlu_data_obj_xiaolu_blue["x"];
    wenlu_show_data_obj_xiaolu_blue = update_wenlu_data_all_for_show_change_position_other(wenlu_data_obj_xiaolu_blue,change_point_xiaolu[change_point_xiaolu_red]);
    // 大眼
    var change_point_dayan_red = wenlu_data_obj_dayan_red["x"];
    wenlu_show_data_obj_dayan_red = update_wenlu_data_all_for_show_change_position_other(wenlu_data_obj_dayan_red,change_point_dayan[change_point_dayan_red]);
    var change_point_dayan_red = wenlu_data_obj_dayan_blue["x"];
    wenlu_show_data_obj_dayan_blue = update_wenlu_data_all_for_show_change_position_other(wenlu_data_obj_dayan_blue,change_point_dayan[change_point_dayan_red]);
    // 小强
    var change_point_xiaoqiang_red = wenlu_data_obj_xiaoqiang_red["x"];
    wenlu_show_data_obj_xiaoqiang_red = update_wenlu_data_all_for_show_change_position_other(wenlu_data_obj_xiaoqiang_red,change_point_xiaoqiang[change_point_xiaoqiang_red]);
    var change_point_xiaoqiang_red = wenlu_data_obj_xiaoqiang_blue["x"];
    wenlu_show_data_obj_xiaoqiang_blue = update_wenlu_data_all_for_show_change_position_other(wenlu_data_obj_xiaoqiang_blue,change_point_xiaoqiang[change_point_xiaoqiang_red]);

}
// 更新坐标位置的 珠子 三星
function update_wenlu_data_all_for_show_change_position(org_obj,change_point) {
    var position_x = 0;
    var position_y = 0;
    var tmp_obj = org_obj;
    if(org_obj["y"] > change_point){
        position_y = 0;
        position_x = org_obj["x"];
        position_x++

        tmp_obj = {"x":position_x,"y":position_y,"color":org_obj["color"]}
    }
    return tmp_obj;
}
// 更新 大路 小路 大眼 小强
function update_wenlu_data_all_for_show_change_position_other(org_obj,change_point) {
    var position_x = 0;
    var position_y = 0;
    var tmp_obj = org_obj;
    var step = 0;
    if(org_obj["y"] > change_point){
        position_y = change_point;
        step = org_obj["y"] - change_point;
        position_x = org_obj["x"] + step;
        tmp_obj = {"x":position_x,"y":position_y,"color":org_obj["color"]}
    }
    return tmp_obj;
}
// 更新 显示数据
function update_wenlu_show_html() {
    show_msg("开始更新 显示数据");
    wenlu_html_zhuzi_red =      "<div class='content for_remove' style='width: "+wenlu_step["zhuzi"]+"px; height: "+wenlu_step["zhuzi"]+"px; left: "+(wenlu_show_data_obj_zhuzi_red["x"] * wenlu_step["zhuzi"])+"px; top: "+(wenlu_show_data_obj_zhuzi_red["y"] * wenlu_step["zhuzi"])+"px'><img src='"+wenlu_img_path+wenlu_show_data_obj_zhuzi_red["color"]+wenlu_gif_obj_img_tail["zhuzi"]+"'></div>";
    wenlu_html_zhuzi_blue =     "<div class='content for_remove' style='width: "+wenlu_step["zhuzi"]+"px; height: "+wenlu_step["zhuzi"]+"px; left: "+(wenlu_show_data_obj_zhuzi_blue["x"] * wenlu_step["zhuzi"])+"px; top: "+(wenlu_show_data_obj_zhuzi_blue["y"] * wenlu_step["zhuzi"])+"px'><img src='"+wenlu_img_path+wenlu_show_data_obj_zhuzi_blue["color"]+wenlu_gif_obj_img_tail["zhuzi"]+"'></div>";
    wenlu_html_dalu_red =       "<div class='content for_remove' style='width: "+wenlu_step["dalu"]+"px; height: "+wenlu_step["dalu"]+"px; left: "+(wenlu_show_data_obj_dalu_red["x"] * wenlu_step["dalu"])+"px; top: "+(wenlu_show_data_obj_dalu_red["y"] * wenlu_step["dalu"])+"px'><img src='"+wenlu_img_path+wenlu_show_data_obj_dalu_red["color"]+wenlu_gif_obj_img_tail["dalu"]+"'></div>";
    wenlu_html_dalu_blue =      "<div class='content for_remove' style='width: "+wenlu_step["dalu"]+"px; height: "+wenlu_step["dalu"]+"px; left: "+(wenlu_show_data_obj_dalu_blue["x"] * wenlu_step["dalu"])+"px; top: "+(wenlu_show_data_obj_dalu_blue["y"] * wenlu_step["dalu"])+"px'><img src='"+wenlu_img_path+wenlu_show_data_obj_dalu_blue["color"]+wenlu_gif_obj_img_tail["dalu"]+"'></div>";
    wenlu_html_sanxing_red =    "<div class='content for_remove' style='width: "+wenlu_step["sanxing"]+"px; height: "+wenlu_step["sanxing"]+"px; left: "+(wenlu_show_data_obj_sanxing_red["x"] * wenlu_step["sanxing"])+"px; top: "+(wenlu_show_data_obj_sanxing_red["y"] * wenlu_step["sanxing"])+"px'><img src='"+wenlu_img_path+wenlu_show_data_obj_sanxing_red["color"]+wenlu_gif_obj_img_tail["sanxing"]+"'></div>";
    wenlu_html_sanxing_blue =   "<div class='content for_remove' style='width: "+wenlu_step["sanxing"]+"px; height: "+wenlu_step["sanxing"]+"px; left: "+(wenlu_show_data_obj_sanxing_blue["x"] * wenlu_step["sanxing"])+"px; top: "+(wenlu_show_data_obj_sanxing_blue["y"] * wenlu_step["sanxing"])+"px'><img src='"+wenlu_img_path+wenlu_show_data_obj_sanxing_blue["color"]+wenlu_gif_obj_img_tail["sanxing"]+"'></div>";

    if(wenlu_show_data_obj_xiaolu_red["color"] != "no_find"){
        wenlu_html_xiaolu_red =     "<div class='content for_remove' style='width: "+wenlu_step["xiaolu"]+"px; height: "+wenlu_step["xiaolu"]+"px; left: "+(wenlu_show_data_obj_xiaolu_red["x"] * wenlu_step["xiaolu"])+"px; top: "+(wenlu_show_data_obj_xiaolu_red["y"] * wenlu_step["xiaolu"])+"px'><img src='"+wenlu_img_path+wenlu_show_data_obj_xiaolu_red["color"]+wenlu_gif_obj_img_tail["xiaolu"]+"'></div>";
    }
    if(wenlu_show_data_obj_xiaolu_blue["color"] != "no_find"){
        wenlu_html_xiaolu_blue =    "<div class='content for_remove' style='width: "+wenlu_step["xiaolu"]+"px; height: "+wenlu_step["xiaolu"]+"px; left: "+(wenlu_show_data_obj_xiaolu_blue["x"] * wenlu_step["xiaolu"])+"px; top: "+(wenlu_show_data_obj_xiaolu_blue["y"] * wenlu_step["xiaolu"])+"px'><img src='"+wenlu_img_path+wenlu_show_data_obj_xiaolu_blue["color"]+wenlu_gif_obj_img_tail["xiaolu"]+"'></div>";
    }
    if(wenlu_show_data_obj_dayan_red["color"] != "no_find"){
        wenlu_html_dayan_red =      "<div class='content for_remove' style='width: "+wenlu_step["dayan"]+"px; height: "+wenlu_step["dayan"]+"px; left: "+(wenlu_show_data_obj_dayan_red["x"] * wenlu_step["dayan"])+"px; top: "+(wenlu_show_data_obj_dayan_red["y"] * wenlu_step["dayan"])+"px'><img src='"+wenlu_img_path+wenlu_show_data_obj_dayan_red["color"]+wenlu_gif_obj_img_tail["dayan"]+"'></div>";
    }
    if(wenlu_show_data_obj_dayan_blue["color"] != "no_find"){
        wenlu_html_dayan_blue =     "<div class='content for_remove' style='width: "+wenlu_step["dayan"]+"px; height: "+wenlu_step["dayan"]+"px; left: "+(wenlu_show_data_obj_dayan_blue["x"] * wenlu_step["dayan"])+"px; top: "+(wenlu_show_data_obj_dayan_blue["y"] * wenlu_step["dayan"])+"px'><img src='"+wenlu_img_path+wenlu_show_data_obj_dayan_blue["color"]+wenlu_gif_obj_img_tail["dayan"]+"'></div>";
    }
    if(wenlu_show_data_obj_xiaoqiang_red["color"] != "no_find"){
        wenlu_html_xiaoqiang_red =  "<div class='content for_remove' style='width: "+wenlu_step["xiaoqiang"]+"px; height: "+wenlu_step["xiaoqiang"]+"px; left: "+(wenlu_show_data_obj_xiaoqiang_red["x"] * wenlu_step["xiaoqiang"])+"px; top: "+(wenlu_show_data_obj_xiaoqiang_red["y"] * wenlu_step["xiaoqiang"])+"px'><img src='"+wenlu_img_path+wenlu_show_data_obj_xiaoqiang_red["color"]+wenlu_gif_obj_img_tail["xiaoqiang"]+"'></div>";
    }
    if(wenlu_show_data_obj_xiaoqiang_blue["color"] != "no_find"){
        wenlu_html_xiaoqiang_blue = "<div class='content for_remove' style='width: "+wenlu_step["xiaoqiang"]+"px; height: "+wenlu_step["xiaoqiang"]+"px; left: "+(wenlu_show_data_obj_xiaoqiang_blue["x"] * wenlu_step["xiaoqiang"])+"px; top: "+(wenlu_show_data_obj_xiaoqiang_blue["y"] * wenlu_step["xiaoqiang"])+"px'><img src='"+wenlu_img_path+wenlu_show_data_obj_xiaoqiang_blue["color"]+wenlu_gif_obj_img_tail["xiaoqiang"]+"'></div>";
    }


    show_msg(wenlu_html_zhuzi_red);
    show_msg(wenlu_html_zhuzi_blue);
    show_msg(wenlu_html_dalu_red);
    show_msg(wenlu_html_dalu_blue);
    show_msg(wenlu_html_xiaolu_red);
    show_msg(wenlu_html_xiaolu_blue);
    show_msg(wenlu_html_dayan_red);
    show_msg(wenlu_html_dayan_blue);
    show_msg(wenlu_html_sanxing_red);
    show_msg(wenlu_html_sanxing_blue);
}
// 更新 庄问路动作
function updte_zhuang_do() {
    $("."+wenlu_target_postion["zhuzi"]).append(wenlu_html_zhuzi_red);
    $("."+wenlu_target_postion["dalu"]).append(wenlu_html_dalu_red);
    $("."+wenlu_target_postion["xiaolu"]).append(wenlu_html_xiaolu_red);
    $("."+wenlu_target_postion["xiaoqiang"]).append(wenlu_html_xiaoqiang_red);
    $("."+wenlu_target_postion["dayan"]).append(wenlu_html_dayan_red);
    $("."+wenlu_target_postion["sanxing"]).append(wenlu_html_sanxing_red);
}
// 更新 闲问路
function update_xian_do() {
    $("."+wenlu_target_postion["zhuzi"]).append(wenlu_html_zhuzi_blue);
    $("."+wenlu_target_postion["dalu"]).append(wenlu_html_dalu_blue);
    $("."+wenlu_target_postion["xiaolu"]).append(wenlu_html_xiaolu_blue);
    $("."+wenlu_target_postion["xiaoqiang"]).append(wenlu_html_xiaoqiang_blue);
    $("."+wenlu_target_postion["dayan"]).append(wenlu_html_dayan_blue);
    $("."+wenlu_target_postion["sanxing"]).append(wenlu_html_sanxing_blue);
}
// 清除全部 效果
function cleart_wenlu() {
    $(".for_remove").remove()
}
// 自动更新 庄闲 问路 显示效果
function show_zhuang_xian_wenlu() {
    show_msg("开始自动更新 庄闲问路")
    var html_red = "";
    html_red += "<img src='"+wenlu_static_img_path+wenlu_show_data_obj_dayan_red["color"]+wenlu_static_png_obj_img_tail["dayan"]+"' />";
    html_red += "<img src='"+wenlu_static_img_path+wenlu_show_data_obj_xiaolu_red["color"]+wenlu_static_png_obj_img_tail["xiaolu"]+"' />";
    html_red += "<img src='"+wenlu_static_img_path+wenlu_show_data_obj_xiaoqiang_red["color"]+wenlu_static_png_obj_img_tail["xiaoqiang"]+"' />";
    var html_blue = "";
    html_blue += "<img src='"+wenlu_static_img_path+wenlu_show_data_obj_dayan_blue["color"]+wenlu_static_png_obj_img_tail["dayan"]+"' />";
    html_blue += "<img src='"+wenlu_static_img_path+wenlu_show_data_obj_xiaolu_blue["color"]+wenlu_static_png_obj_img_tail["xiaolu"]+"' />";
    html_blue += "<img src='"+wenlu_static_img_path+wenlu_show_data_obj_xiaoqiang_blue["color"]+wenlu_static_png_obj_img_tail["xiaoqiang"]+"' />";
    $("#wenlu_show_red").html(html_red);
    $("#wenlu_show_blue").html(html_blue);
}
// 自动更新 庄闲 问路 显示效果 对于台桌的 露珠
// 大路 小路 小强
function show_zhuang_xian_wenlu_for_luzhu() {
    show_msg("开始自动更新 庄闲问路")
    var right_wenlu_zhuang_img_1 = "<img src='"+wenlu_static_img_path+wenlu_show_data_obj_dayan_red["color"]+wenlu_static_png_obj_img_tail["dayan"]+"' />";
    var right_wenlu_zhuang_img_2 = "<img src='"+wenlu_static_img_path+wenlu_show_data_obj_xiaolu_red["color"]+wenlu_static_png_obj_img_tail["xiaolu"]+"' />";
    var right_wenlu_zhuang_img_3 = "<img src='"+wenlu_static_img_path+wenlu_show_data_obj_xiaoqiang_red["color"]+wenlu_static_png_obj_img_tail["xiaoqiang"]+"' />";
    var right_wenlu_xian_img_1 = "<img src='"+wenlu_static_img_path+wenlu_show_data_obj_dayan_blue["color"]+wenlu_static_png_obj_img_tail["dayan"]+"' />";
    var right_wenlu_xian_img_2 = "<img src='"+wenlu_static_img_path+wenlu_show_data_obj_xiaolu_blue["color"]+wenlu_static_png_obj_img_tail["xiaolu"]+"' />";
    var right_wenlu_xian_img_3 = "<img src='"+wenlu_static_img_path+wenlu_show_data_obj_xiaoqiang_blue["color"]+wenlu_static_png_obj_img_tail["xiaoqiang"]+"' />";

	// 庄问路
	$(".right_wenlu_zhuang_img_1").html(right_wenlu_zhuang_img_1)
	$(".right_wenlu_zhuang_img_2").html(right_wenlu_zhuang_img_2)
	$(".right_wenlu_zhuang_img_3").html(right_wenlu_zhuang_img_3)
	
	// 闲问路
	$(".right_wenlu_xian_img_1").html(right_wenlu_xian_img_1)
	$(".right_wenlu_xian_img_2").html(right_wenlu_xian_img_2)
	$(".right_wenlu_xian_img_3").html(right_wenlu_xian_img_3)
}
// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// 系统库函数  庄闲 问路 库 函数  结束
// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

/**
 * 展示查询露珠的时间靴号铺号说明
**/
function show_print_explain(gameName, tableName, xueNumber, puNumber, date) {
	var gameTypes = {
		'2': {name: '龙虎', path: 'longhu'},
		'3': {name: '百家乐', path: 'bjl'}
	}
	game_type = gameTypes[gameName].path
	$("#zou_game_name").html(gameTypes[gameName].name)
	$("#zou_game_table_name").html(tableName)
	$("#zou_game_xue").html(xueNumber)
	$("#zou_game_date").html(date)
}
