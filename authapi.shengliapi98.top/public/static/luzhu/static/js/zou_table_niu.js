
var tableId = $.getUrlParam('tableId');
var xueNumber = $.getUrlParam('xueNumber');
var gameType = $.getUrlParam('gameType')
var pu = $.getUrlParam('pu')
var create_date = $.getUrlParam('create_date')
var tableName = $.getUrlParam('tableName')
//露珠结果 
let luzhuResult = []
//牌的结果 
let pokerResult = {}
//ws
let socketTask = null
let main_zhuang = 'main_zhuang'
let main_xian1 = 'main_xian1'
let main_xian2 = 'main_xian2'
let main_xian3 = 'main_xian3'

window.onload = () => {
	startZoushi()
}



/**
 * 加载走势数据
 * **/
function startZoushi(){
	var dataParams = {
		game_type: gameType,
		table_id: tableId,
		xue: xueNumber,
		pu: pu,
		create_date: create_date,
		token: $.getUrlParam('token'),
	}
	show_print_explain('牛牛', tableName, xueNumber, pu, create_date)
	$.post(`${baseUrl}/print/start`,dataParams, (data) => {
		let res = JSON.parse(data)
		if(res.code == 1) {
			luzhuResult = res.data
			fillResult()
		}else {
			console.log("获取露珠数据失败")
		}
		// var result = JSON.parse(res)
		// fillResult()
	})
	// 获取走势图数据
	// $.post(baseCattleUrl+'/cattle/get_table/get_nn_data?tableId='+tableId,(data) => {
	// 	let res = JSON.parse(data)
	// 	if(res.code == 1) {
	// 		luzhuResult = res.data
	// 		fillResult()
	// 	}else {
	// 		console.log("获取露珠数据失败")
	// 	}
	// })
}

/**
 * 往格子里填结果 
 * **/
function fillResult() {
	// let count = luzhuResult.length
	// if(count > 0) {
	for(let i = 0; i<4; i++) {
		main_zhuang = `main_zhuang_${i}`
		main_xian1 = `main_xian1_${i}`
		main_xian2 = `main_xian2_${i}`
		main_xian3 = `main_xian3_${i}`
		let start = i * 40
		let end = (i + 1) * 40
		let tempLuzhu = luzhuResult.slice(start, end)
		if(tempLuzhu.length < 40) {
			for(let i= tempLuzhu.length; i < 40; i++) {
				tempLuzhu.push({'1': {result: null},'2': {result: null},'3': {result: null},'4': {result: null}})
			}
		}
		tempLuzhu.forEach((res, index) => {
			let isZhuangMax = true
			//闲大于庄
			let isXianMax = false
			
			isZhuangMax = tuckResult(main_zhuang, index, res['1'])
			isXianMax = tuckResult(main_xian1, index, res['2'], res['1'])
			isZhuangMax = !isZhuangMax ? isZhuangMax : isXianMax
			
			isXianMax = tuckResult(main_xian2, index, res['3'], res['1'])
			isZhuangMax = !isZhuangMax ? isZhuangMax : isXianMax
			
			isXianMax = tuckResult(main_xian3, index, res['4'], res['1'])
			isZhuangMax = !isZhuangMax ? isZhuangMax : isXianMax
			if(isZhuangMax) {
				$(`#${main_zhuang}_${index}`).css({'backgroundColor': 'red'})
			}
		})
	}
}

/**
 * 把结果数字放入格子
 * @param id 容器id
 * * @param index 下标
 * @param result 结果
 * @param zhuangResult 庄的结果 
 * **/
function tuckResult(id, index, result, zhuangResult){
	let dom = $(`#${id}`)
	if(result && result.image){
		let txt = result.result >= 10 ? "牛" : result.result == 0 ? '无' : result.result
		let backColor = "#3A3A3A"
		//是否大于庄家
		let isGreater = result.win.win == 2 ? true : false
		switch(id) {
			case main_xian1: 
				backColor = isGreater ? '#2D4399' : backColor
			break
			case main_xian2: 
				backColor = isGreater ? '#0692F3' : backColor
			break
			case main_xian3: 
		
				backColor = isGreater ? '#01AC99' : backColor
			break
			
		}
		dom.append(
			`<div class="main_cell" >
				<div class="main_text" id="${id}_${index}" style='background-color: ${backColor}'>${txt} </div> 
			</div>`
		)
		//如果颜色没有改说明庄大于闲  true为庄大于闲
		if(backColor == "#3A3A3A") {
			return true
		}else {
			return false
		}
	}else{
		dom.append(`<div class="main_cell"></div>`)
	}
}

/**
 * 展示查询露珠的时间靴号铺号说明
**/
function show_print_explain(gameName, tableName, xueNumber, puNumber, date) {
	$("#zou_game_name").html(gameName)
	$("#zou_game_table_name").html(tableName)
	$("#zou_game_xue").html(xueNumber)
	$("#zou_game_date").html(date)
}

//打印
function printHtml() {
	window.print();
}


