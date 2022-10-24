//游戏类型：{支持百家乐 龙虎 幸运6 }
var res_org_obj = {}; // 测试数据1 没有返回的时候
var res_org_obj = { // 测试数据1 没有返回的时候
    "k0":{"result":1,"ext":0}
};
var res_org_obj = { // 测试小量数据的
    "k0":{"result":1,"ext":0},"k1":{"result":2,"ext":0}
};
var res_org_obj = { // 测试小量数据的
    "k0":{"result":1,"ext":0},"k1":{"result":2,"ext":0},"k2":{"result":1,"ext":0},"k3":{"result":2,"ext":0,},"k4":{"result":1,"ext":0,},"k5":{"result":2,"ext":0}
};
// var res_org_obj = {// 主要测试 和 数据的
//     "k0":{"result":1,"ext":0},"k1":{"result":1,"ext":1},"k2":{"result":1,"ext":2},"k3":{"result":1,"ext":3},"k4":{"result":2,"ext":0},"k5":{"result":1,"ext":0},"k6":{"result":1,"ext":0},"k7":{"result":1,"ext":0},"k8":{"result":1,"ext":0},"k9":{"result":2,"ext":0},
//     "k10":{"result":2,"ext":0},"k11":{"result":2,"ext":1},"k12":{"result":1,"ext":1},"k13":{"result":2,"ext":0},"k14":{"result":2,"ext":0},"k15":{"result":2,"ext":0},"k16":{"result":1,"ext":0},"k17":{"result":2,"ext":0},"k18":{"result":2,"ext":0},"k19":{"result":2,"ext":0},
//     "k20":{"result":1,"ext":2},"k21":{"result":3,"ext":0},"k22":{"result":3,"ext":1},"k23":{"result":3,"ext":0},"k24":{"result":2,"ext":0},"k25":{"result":1,"ext":1},"k26":{"result":1,"ext":0},"k27":{"result":1,"ext":0},"k28":{"result":1,"ext":0},"k29":{"result":1,"ext":0},
//     "k30":{"result":1,"ext":3},"k31":{"result":2,"ext":0},"k32":{"result":1,"ext":2},"k33":{"result":1,"ext":0},"k34":{"result":3,"ext":0},"k35":{"result":2,"ext":2},"k36":{"result":2,"ext":0},"k37":{"result":1,"ext":0},"k38":{"result":1,"ext":0},"k39":{"result":1,"ext":0},
//     "k40":{"result":1,"ext":0},"k41":{"result":1,"ext":0},"k42":{"result":3,"ext":0},"k43":{"result":1,"ext":0},"k44":{"result":2,"ext":0},"k45":{"result":2,"ext":3},"k46":{"result":2,"ext":0},"k47":{"result":1,"ext":0},"k48":{"result":1,"ext":0},"k49":{"result":1,"ext":0},
// };
// var res_org_obj = {// 主要测试 转弯的
//     "k0":{"result":1,"ext":0},"k1":{"result":1,"ext":0},"k2":{"result":1,"ext":0},"k3":{"result":1,"ext":1},"k4":{"result":1,"ext":0},"k5":{"result":1,"ext":0},"k6":{"result":1,"ext":0},"k7":{"result":1,"ext":0},"k8":{"result":1,"ext":0},"k9":{"result":2,"ext":0},
//     "k10":{"result":2,"ext":1},"k11":{"result":2,"ext":0},"k12":{"result":2,"ext":1},"k13":{"result":2,"ext":0},"k14":{"result":2,"ext":0},"k15":{"result":2,"ext":0},"k16":{"result":2,"ext":0},"k17":{"result":2,"ext":0},"k18":{"result":2,"ext":0},"k19":{"result":2,"ext":0},
//     "k20":{"result":1,"ext":2},"k21":{"result":1,"ext":0},"k22":{"result":1,"ext":1},"k23":{"result":1,"ext":0},"k24":{"result":1,"ext":0},"k25":{"result":1,"ext":1},"k26":{"result":1,"ext":0},"k27":{"result":1,"ext":0},"k28":{"result":1,"ext":0},"k29":{"result":1,"ext":3},
//     "k30":{"result":3,"ext":3},"k31":{"result":3,"ext":0},"k32":{"result":3,"ext":2},"k33":{"result":1,"ext":0},"k34":{"result":1,"ext":0},"k35":{"result":1,"ext":2},"k36":{"result":1,"ext":0},"k37":{"result":1,"ext":0},"k38":{"result":2,"ext":0},"k39":{"result":1,"ext":0},
//     "k40":{"result":1,"ext":0},"k41":{"result":1,"ext":0},"k42":{"result":1,"ext":0},"k43":{"result":1,"ext":0},"k44":{"result":1,"ext":0},"k45":{"result":2,"ext":3},"k46":{"result":2,"ext":0},"k47":{"result":1,"ext":0},"k48":{"result":1,"ext":0},"k49":{"result":1,"ext":0},
// };
// var res_org_obj = {// 主要测试 转弯的
//     "k0":{"result":1,"ext":0},"k1":{"result":1,"ext":0},"k2":{"result":1,"ext":0},"k3":{"result":1,"ext":1},"k4":{"result":1,"ext":0},"k5":{"result":1,"ext":0},"k6":{"result":1,"ext":0},"k7":{"result":1,"ext":0},"k8":{"result":1,"ext":0},"k9":{"result":2,"ext":0},
//     "k10":{"result":2,"ext":1},"k11":{"result":2,"ext":0},"k12":{"result":2,"ext":1},"k13":{"result":2,"ext":0},"k14":{"result":2,"ext":0},"k15":{"result":2,"ext":0},"k16":{"result":2,"ext":0},"k17":{"result":2,"ext":0},"k18":{"result":2,"ext":0},"k19":{"result":2,"ext":0},
//     "k20":{"result":1,"ext":2},"k21":{"result":1,"ext":0},"k22":{"result":1,"ext":1},"k23":{"result":1,"ext":0},"k24":{"result":1,"ext":0},"k25":{"result":1,"ext":1},"k26":{"result":1,"ext":0},"k27":{"result":1,"ext":0},"k28":{"result":1,"ext":0},"k29":{"result":1,"ext":3},
//     "k30":{"result":3,"ext":3},"k31":{"result":3,"ext":0},"k32":{"result":3,"ext":2},"k33":{"result":1,"ext":0},"k34":{"result":1,"ext":0},"k35":{"result":1,"ext":2},"k36":{"result":1,"ext":0},"k37":{"result":1,"ext":0},"k38":{"result":2,"ext":0},"k39":{"result":1,"ext":0},
//     "k40":{"result":1,"ext":0},"k41":{"result":1,"ext":0},"k42":{"result":1,"ext":0},"k43":{"result":1,"ext":0},"k44":{"result":1,"ext":0},"k45":{"result":2,"ext":3},"k46":{"result":2,"ext":0},"k47":{"result":1,"ext":0},
// };