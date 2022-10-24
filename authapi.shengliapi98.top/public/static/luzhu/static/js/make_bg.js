$(function(){
	// 其它背景图
	var h = '';
	for(var i = 1; i <= 324; i++){
		h += '<div class="table_bg_td"></div>'
	}
	$(".table_bg").html(h)
	// 珠子背景图
	var h = '';
	for(var i = 1; i <= 66; i++){
		h += '<div class="table_zhuzi_bg_td"></div>'
	}
	$(".table_zhuzi_bg").html(h)
})