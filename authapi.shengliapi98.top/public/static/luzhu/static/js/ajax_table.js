$(function(){
	// base url
	var baseUrl = 'http://admin.kdy128.com'
	// 获取 数据
	var tableId = $.getUrlParam('tableId');
	console.log('tableId:'+tableId)
	// 获取 系统 数据
	// 默认 如果 没有 台桌的 ID 直接报错
	if(tableId == null){
		alert("参数错误,请联系技术处理！")
		window.location.href="/index.html?tableId=1"
	}
	// 获取基础数据 code = 1 成功  code = 0 失败 msg 信息 data.data 是返回的数据
	$.getJSON(baseUrl+'/api/diantou/table/getTableInfo/gameType/3/tableId/'+tableId,function(data){
		if(data.code == 1){
			$("#num_tai").html(data.data.lu_zhu_name)
			$(".right_money_banker_player").html(data.data.right_money_banker_player)
			$(".right_money_tie").html(data.data.right_money_tie)
			$(".right_money_pair").html(data.data.right_money_pair)
			$(".right_money_banker_player_cny").html(data.data.right_money_banker_player_cny)
			$(".right_money_tie_cny").html(data.data.right_money_tie_cny)
			$(".right_money_pair_cny").html(data.data.right_money_pair_cny)
			$("#num_xue").html(data.data.num_xue);
			$("#num_pu").html(data.data.num_pu);
			$("#set_time").html(data.data.time_start);
		}else{
			console.log("获取数据失败")
		}
	})
	
// 操作结束了
})

function getTableCount(){
	var xue = parseInt($("#num_xue").html());
	// 获取走势图数据
	$.getJSON(baseUrl+'/api/diantou/table/getTableCount/gameType/3/tableId/'+tableId+'/xue/'+xue,function(data){
		if(data.code == 1){
			$(".right_wenlu_banker_num").html(data.data.zhuang)
			$(".right_wenlu_player_num").html(data.data.xian)
			$(".right_wenlu_tie_num").html(data.data.he)
			$(".right_wenlu_zhuangdui_num").html(data.data.zhuangDui)
			$(".right_wenlu_xiandui_num").html(data.data.xianDui)
		}else{
			console.log("获取数据失败")
		}
	})
	
}
// 执行开始
var timeHandle = setInterval(getTableCount,1000)

// 全屏 与 退出 全屏 
// 全屏
function fullScreen() {
  var el = document.documentElement;
  var rfs = el.requestFullScreen || el.webkitRequestFullScreen || el.mozRequestFullScreen || el.msRequestFullScreen;
  if(typeof rfs != "undefined" && rfs) {
    rfs.call(el);
  } 
  else if(typeof window.ActiveXObject != "undefined") {
    //for IE，这里其实就是模拟了按下键盘的F11，使浏览器全屏
    var wscript = new ActiveXObject("WScript.Shell");
    if(wscript != null) {
        wscript.SendKeys("{F11}");
    }
  }
}
// 退出全屏
function exitFullScreen() {
  var el = document;
  var cfs = el.cancelFullScreen || el.webkitCancelFullScreen || el.mozCancelFullScreen || el.exitFullScreen;
  if(typeof cfs != "undefined" && cfs) {
    cfs.call(el);
  } 
  else if(typeof window.ActiveXObject != "undefined") {
    //for IE，这里和fullScreen相同，模拟按下F11键退出全屏
    var wscript = new ActiveXObject("WScript.Shell");
    if(wscript != null) {
        wscript.SendKeys("{F11}");
    }
  }
}