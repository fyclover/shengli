function reloadUrl(){
	window.location.reload(true)
}
// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// 系统启动函数
// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
var res_org_obj = {};
var tableId = $.getUrlParam('tableId');
var xueNumber = $.getUrlParam('xueNumber');
var gameType = $.getUrlParam('gameType')
var pu = $.getUrlParam('pu')
var create_date = $.getUrlParam('create_date')
var tableName = $.getUrlParam('tableName')
// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// 系统启动函数
// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function startZoushi(){
	var dataParams = {
		game_type: gameType,
		table_id: tableId,
		xue: xueNumber,
		pu: pu,
		create_date: create_date,
		token: $.getUrlParam('token'),
	}
	show_print_explain(gameType, tableName, xueNumber, pu, create_date)
	$.post(`${baseUrl}/print/start`,dataParams, (res) => {
		var result = JSON.parse(res)
		var res_org_obj = { // 测试小量数据的
		    "k0":{"result":1,"ext":0},"k1":{"result":2,"ext":0},"k2":{"result":1,"ext":0},"k3":{"result":2,"ext":0,},"k4":{"result":1,"ext":0,},"k5":{"result":2,"ext":0}
		};
		start_zoushi(result.data);
		init_wenlu();
		show_zhuang_xian_wenlu();
	})
	// var xue = xueNumber;
	// // 获取走势图数据
	// $.getJSON(baseUrl+'/api/diantou/table/getData/gameType/3/tableId/'+tableId+'/xue/'+xue,function(data){
	// 	console.log("成功接收到数据")
	// 	console.log(data)
	// 	if(data.code == 1){
	// 		start_zoushi(data.data);// 启动函数 系统主函数入口
	// 		// 更新走势图
	// 		// 更新庄闲数据
	// 		init_wenlu();
	// 		// 展示 庄闲问路的 路径
	// 		show_zhuang_xian_wenlu();
			
	// 	}else{
	// 		console.log("获取数据失败")
	// 	}
	// })
}
//打印
function printHtml() {
	window.print();
}
startZoushi()
// 执行开始
// var timeHandle = setInterval(startZoushi,1000)

