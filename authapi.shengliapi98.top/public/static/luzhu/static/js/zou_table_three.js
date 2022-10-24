
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
	show_print_explain('三公', tableName, xueNumber, pu, create_date)
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
	// let zhuangDom = $(`#${main_zhuang}`)
	// let xianDom1 = $(`#${main_xian1}`)
	// let xianDom2 = $(`#${main_xian2}`)
	// let xianDom3 = $(`#${main_xian3}`)
	// zhuangDom.html("")
	// xianDom1.html("")
	// xianDom2.html("")
	// xianDom3.html("")
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
				tempLuzhu.push({'1': {result: null,},'2': {result: null},'3': {result: null},'4': {result: null}})
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
	if(result.image) {
		let txtHmlt = result.result_num.num
		txtHmlt = handleCellText(result, txtHmlt)
		let backColor = "#3A3A3A"
		if(zhuangResult) {
			//win 2是比庄大  win 1是比庄小
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
		}
		dom.append(
			`<div class="main_cell" >
				<div class="main_text" id="${id}_${index}" style='background-color: ${backColor}'>
					${txtHmlt}
				</div> 
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
 * 设置每个格子的内容
 * @param {result} 结果  
 * @param {txtHmlt} 原有的文本
 **/
function handleCellText(result, txtHmlt) {
	switch(result.result_num.g) {
		case 0: 
			txtHmlt = `<span>${txtHmlt}</span>`
		break
		case 1:
			txtHmlt = `<span>单公</span><span>${txtHmlt}</span>`
		break
		case 2:
			txtHmlt = `<span>双公</span><span>${txtHmlt}</span>`
		break
		case 3:
			txtHmlt =  `<span>三公</span>`
		break
		
	}
	if(result.result_num.num >= 20) {
		switch(result.result_num.num.toString()) {
			case '33':
				txtHmlt = `<span>三条</span><span>K</span>`
			break
			case '32':
				txtHmlt = `<span>三条</span><span>Q</span>`
			break
			case '31':
				txtHmlt = `<span>三条</span><span>J</span>`
			break
			case '30':
				txtHmlt = `<span>三条</span><span>10</span>`
			break
			case '21':
				txtHmlt = `<span>三条</span><span>A</span>`
			break
			case '23':
			case '999':
				txtHmlt = `<span>三条</span><span>3</span>`
			break
			default:
				txtHmlt = `<span>三条</span><span>${result.result_num.pai[0]}</span>`
			break
			
		}
	}
	return txtHmlt
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


