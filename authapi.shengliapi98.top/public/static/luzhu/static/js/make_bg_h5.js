$(function(){
	// 其它背景图
	var h = '';
	for(var i = 1; i <= 156; i++){
		h += '<div class="jingshan_dalu_bg_table_td"></div>'
	}
	$(".jingshan_dalu_bg_table").html(h)
	// 珠子背景图
	var h = '';
	for(var i = 1; i <= 156; i++){
		h += '<div class="jingshan_zhuzi_bg_table_td"></div>'
	}
	$(".jingshan_zhuzi_bg_table").html(h)
	// 珠子背景图
	var h = '';
	for(var i = 1; i <= 156; i++){
		h += '<div class="jingshan_oher_bg_table_td"></div>'
	}
	$(".jingshan_oher_bg_table").html(h)
})