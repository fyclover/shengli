// 获取 url 
(function ($) {
	$.getUrlParam = function (name) {
		var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
	　　var r = window.location.search.substr(1).match(reg);
	　　if (r != null) return decodeURI(r[2]); return null;
	}
})(jQuery);