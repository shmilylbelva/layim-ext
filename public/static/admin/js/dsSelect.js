function dsSelect(listId) {
    let obj = {};
    if (!dsSelect_tools.notNull(listId)) {
        alert("初始化失败，请检查table表的id参数！");
        return;
    }
    obj = (function(obj) {
        obj.listId = listId;
        obj.multiSelect = false;
        return obj;
    }
    )(obj);
    obj.init = function() {
        dsSelect_take.initView(obj);
        dsSelect_take.initEvent(obj);
    }
    ;
    obj.setLeftData = function(datasArray, showParam,img,id) {
        dsSelect_take.setLeftDatas(this, datasArray, showParam,img,id);
        dsSelect_take.initEvent(obj);
    }
    ;
    obj.setRightData = function(datasArray, showParam,img,id) {
        dsSelect_take.setRightDatas(this, datasArray, showParam,img,id);
        dsSelect_take.initEvent(obj);
    }
    ;
    obj.getSelectLeftValus = function() {
        return dsSelect_tools.eventGetValueLR(obj, "l");
    }
    ;
    obj.getSelectRightValus = function() {
        return dsSelect_tools.eventGetValueLR(obj, "r");
    }
    ;
    obj.disableButtons = function() {
        dsSelect_take.disableEvent(obj);
    }
    ;
    obj.restartButtons = function() {
        dsSelect_take.restartButtonsEvent(this);
        dsSelect_take.initEvent(obj);
    }
    ;
    return obj;
}
;let dsSelect_take = {
    "initView": function(obj) {
        let listId = obj.listId;
        $("#" + listId).html(dsSelect_models.baseModel());
    },
    "initEvent": function(obj) {
        let listId = obj.listId;
        $("#" + listId + " .dsList li").off();
        dsSelect_tools.eventTakeWithLR(obj, "l");
        dsSelect_tools.eventTakeWithLR(obj, "r");
        dsSelect_tools.eventSelectToRight(obj);
        dsSelect_tools.eventSelectToLeft(obj);
        dsSelect_tools.eventAllToRight(obj);
        dsSelect_tools.eventAllToLeft(obj);
    },
    "setLeftDatas": function(obj, datasArray, showParam,img,id) {
        dsSelect_tools.setDatasWithLR(obj, "l", datasArray, showParam,img,id);
    },
    "setRightDatas": function(obj, datasArray, showParam,img,id) {
        dsSelect_tools.setDatasWithLR(obj, "r", datasArray, showParam,img,id);
    },
    "disableEvent": function(obj) {
        let listId = obj.listId;
        let selectToRightBt = $("#" + listId + " .selectRight");
        let selectToLeftBt = $("#" + listId + " .selectLeft");
        let allToRightBt = $("#" + listId + " .allRight");
        let allToLeftBt = $("#" + listId + " .allLeft");
        allToLeftBt.off();
        allToRightBt.off();
        selectToLeftBt.off();
        selectToRightBt.off();
        $(".dsButton").css("background-color", "#AAAAAA");
    },
    "restartButtonsEvent": function(obj) {
        $(".dsButton").css("background-color", "#485d74");
    }
};
let dsSelect_models = {
    "baseModel": function() {
        let str = '<div class="left">' + '<ul class="dsList">' + '</ul>' + '</div>' + '<div class="center">' + '<div class="dsButton selectRight"> 入群> </div>' + '<div class="dsButton selectLeft"> 出群< </div>' + '<div class="dsButton allRight"> 全部入群>>> </div>' + '<div class="dsButton allLeft"> 全部出群<<< </div>'+'<div class="dsButton sure"> 确认 </div>' + '</div>' + '<div class="right">' + '<ul class="dsList">' + '</ul>' + '</div>';
        return str;
    },
    "selectItemModel": function(uid, value,img,id) {
        let str = '<li id="' + id + '"><img alt="image" class="img-circle" src="'+img+'">' + value + '</li>';
        return str;
    }
};
let dsSelect_tools = {
    "notNull": function(str) {
        return str != null && str != "";
    },
    "eventTakeWithLR": function(obj, lr) {
        let lrtemp = ".left";
        if (lr == "r") {
            lrtemp = ".right";
        }
        let listId = obj.listId;
        $("#" + listId + " " + lrtemp + " .dsList li").on("mousedown", function(event) {
            if (event.ctrlKey && event.button == 0) {
                if ($(this).hasClass("selectItem")) {
                    $(this).removeClass("selectItem")
                } else {
                    $(this).addClass("selectItem");
                }
            } else {
                if ($(this).hasClass("selectItem")) {
                    $(this).removeClass("selectItem")
                } else {
                    if (!obj.multiSelect) {
                        $("#" + listId + " " + lrtemp + " .dsList li").not(this).removeAttr("class")
                    }
                    $(this).addClass("selectItem");
                }
            }
        })
    },
    "eventSelectToRight": function(obj) {
        let listId = obj.listId;
        let selectToRightBt = $("#" + listId + " .selectRight");
        let rightListObj = $("#" + listId + " .right .dsList");
        selectToRightBt.off();
        selectToRightBt.on("mousedown", function() {
            let selectObjs = $("#" + listId + " .left .dsList .selectItem");
            if (selectObjs.length > 0) {
                selectObjs.removeAttr("class");
                selectObjs.appendTo(rightListObj);
                dsSelect_take.initEvent(obj);
            }
        });
    },
    "eventSelectToLeft": function(obj) {
        let listId = obj.listId;
        let selectToLeftBt = $("#" + listId + " .selectLeft");
        let leftListObj = $("#" + listId + " .left .dsList");
        selectToLeftBt.off();
        selectToLeftBt.on("mousedown", function() {
            let selectObjs = $("#" + listId + " .right .dsList .selectItem");
            if (selectObjs.length > 0) {
                selectObjs.removeAttr("class");
                selectObjs.appendTo(leftListObj);
                dsSelect_take.initEvent(obj);
            }
        });
    },
    "eventAllToRight": function(obj) {
        let listId = obj.listId;
        let allToRightBt = $("#" + listId + " .allRight");
        let rightListObj = $("#" + listId + " .right .dsList");
        allToRightBt.off();
        allToRightBt.on("mousedown", function() {
            let selectObjs = $("#" + listId + " .left .dsList li");
            if (selectObjs.length > 0) {
                selectObjs.removeAttr("class");
                selectObjs.appendTo(rightListObj);
                dsSelect_take.initEvent(obj);
            }
        });
    },
    "eventAllToLeft": function(obj) {
        let listId = obj.listId;
        let allToLeftBt = $("#" + listId + " .allLeft");
        let leftListObj = $("#" + listId + " .left .dsList");
        allToLeftBt.off();
        allToLeftBt.on("mousedown", function() {
            let selectObjs = $("#" + listId + " .right .dsList li");
            if (selectObjs.length > 0) {
                selectObjs.removeAttr("class");
                selectObjs.appendTo(leftListObj);
                dsSelect_take.initEvent(obj);
            }
        });
    },
    "eventGetValueLR": function(obj, lr) {
        let listId = obj.listId;
        let lrtemp = ".left";
        if (lr == "r") {
            lrtemp = ".right";
        }
        let result = [];
        let selectObjs = $("#" + listId + " " + lrtemp + " .dsList li");
        if (selectObjs == null || selectObjs.length == 0) {
            return result;
        }
        for (let i = 0; i < selectObjs.length; i++) {
            let selectItem = selectObjs[i];
            // console.log(selectItem.id);
            result[i] = selectItem.id;
        }
        return result;
    },
    "setDatasWithLR": function(obj, lr, datasArray, showParam,img,id) {
        let lrtemp = ".left";
        if (lr == "r") {
            lrtemp = ".right";
        }
        let listId = obj.listId;
        let listRightObj = $("#" + listId + " " + lrtemp + " .dsList");
        for (let i = 0; i < datasArray.length; i++) {
            let dataItem = datasArray[i];
            let u = dsSelect_tools.guid();
            let appendStr = dsSelect_models.selectItemModel(u, dataItem[showParam],dataItem[img],dataItem[id]);
            listRightObj.append(appendStr);
            $("#" + listId + " " + lrtemp + " .dsList li[u='" + u + "']").data("tempData", dataItem);
        }
    },
    "guid": function() {
        return (S4() + S4() + "-" + S4() + "-" + S4() + "-" + S4() + "-" + S4() + S4() + S4());
    }
};
function S4() {
    return (((1 + Math.random()) * 0x10000) | 0).toString(16).substring(1);
}
;