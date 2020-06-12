/**
 * Created by PhpStorm.
 * User: shmilylbelva
 * Email: 1028604181@qq.com
 * Date: 2019/4/20
 * Time: 4:20 PM
 * todo 206 line add data-json 将数据存在json里面，不单独存每个参数
 */
 
;!function(win){
    "use strict";

    var layimExt = function(){
        this.v = '1.0'; //版本号  
        touch($('body'), '*[layimExt-event]', function(e){
            var othis = $(this), methid = othis.attr('layimExt-event');
            events[methid] ? events[methid].call(this, othis, e) : '';
        });                      
    },token = ''
    ,socket = null //实例化后的websocket
    ,layim = null //等于config.layim
    ,layer = null 
    ,mine = null //当前用户的id 
    ,call = {}
    ,config = { //用户可设置的参数
        layim:'',
        layimConf:'',
        switchColor: "rgb(100, 189, 99)",
    },touch = function(obj, child, fn){
        var move, type = typeof child === 'function', end = function(e){
          var othis = $(this);
          if(othis.data('lock')){
            return;
          }
          move || fn.call(this, e);
          move = false;
          othis.data('lock', 'true');
          setTimeout(function(){
            othis.removeData('lock');
          }, 1000);
        };

        if(type){
          fn = child;
        }

        obj = typeof obj === 'string' ? $(obj) : obj;

        if(type){
          obj.on('touchmove', function(){
            move = true;
          }).on('touchend', end);
        } else {
          obj.on('touchmove', child, function(){
            move = true;
          }).on('touchend', child, end);
        }
    },ajax = function(options){
        var dataType = options.dataType || 'json',
            header = options.header || {},
            contentType = options.contentType || 'application/x-www-form-urlencoded',
            success = options.success || {},
            error = options.error || {},
            type = options.type || 'get',
            data = options.data || null;
		$.ajax({
			url: options.url,
            dataType: dataType,
			contentType: contentType,
		    type: type,
		    data: data,
	        // headers: {//当使用前后端分离时用token
	        //     'Authorization': 'Bearer ' + config.token,
	        //     'Content-Type': 'application/json'
	        // },		    
		    success: success,
		    error: error
		});   
    },isMobile = function(){
        if( navigator.userAgent.match(/Android/i)
            || navigator.userAgent.match(/webOS/i)
            || navigator.userAgent.match(/iPhone/i)
            || navigator.userAgent.match(/iPad/i)
            || navigator.userAgent.match(/iPod/i)
            || navigator.userAgent.match(/BlackBerry/i)
            || navigator.userAgent.match(/Windows Phone/i)
        ){
            return true;
        }
        return false;
    },socketSend = function(data){//发送socket
        if($.isEmptyObject(config.socketPath.url) )return false;
        
        switch (socket.readyState) {
            case WebSocket.CONNECTING:
                //正在连接
                break;
            case WebSocket.OPEN:
                $.isEmptyObject(data) || socket.send(JSON.stringify(data));
                break;
            case WebSocket.CLOSING:
                // 正在关闭
                break;
            case WebSocket.CLOSED:
                layer.open({
                    content: '连接已断开，是否刷新页面？'
                    ,shadeClose: false
                    ,btn: ['确认', '取消']
                    ,skin: 'footer'
                    ,yes:function(index){
                        window.location.reload(); 
                        // config.logout.data.userid = mine;
                        // var options = {
                        //     url : config.logout.url,
                        //     data : config.logout.data,
                        //     success:function(res){
                        //         if (res.code == 0) {               
                        //             window.location.href="/"; 
                        //         }
                        //     }
                        // }         
                        // ajax(options);                          
                    }
                });  
                break;
        }        
        
    },layimInit = function(){  
        layim = config.layim;//全局变量layim
        layer = config.layer;//全局变量layer
        layim.config(config.layimConf);//初始化layim
        mine = layim.cache().mine.id;        
        layim.on('sendMessage', function(res){
            sendMessage(res);
        });        
        layim.on('detail', function(res){
            detail(res);
        });         
       
    },initSocket = function(){ 
        socket = new WebSocket(config.socketPath.url+'/'+config.socketPath.data);
        //连接成功时触发
        socket.onopen = function(){
            socketSend(config.socketInit.data);
            $.isEmptyObject(config.socketPing.data) || setInterval(function(){
                socketSend(config.socketPing.data);
            },config.socketPing.times || 20000);              
        };           
     
        socket.onclose = function(e) {
            if (e.type == 'close') {
                socket.close();
                layer.open({
                    content: '连接已断开，是否刷新页面？'
                    ,shadeClose: false
                    ,btn: ['确认', '取消']
                    ,skin: 'footer'
                    ,yes:function(index){
                        window.location.reload(); 
                    }
                });  
            }            
        };
        socket.onerror = function(e) {
            if (e.type == 'error') {
                socket.close();
                layer.open({
                    content: '连接错误，是否重新登录？'
                    ,shadeClose: false
                    ,btn: ['确认', '取消']
                    ,skin: 'footer'
                    ,yes:function(index){
                        config.logout.data.userid = mine;
                        var options = {
                            url : config.logout.url,
                            data : config.logout.data,
                            success:function(res){
                                if (res.code == 0) {               
                                    window.location.href="/"; 
                                }
                            }
                        }         
                        ajax(options);                          
                    }
                });  
            }
        };              
    },init = function(){          
        var options = {
            url : config.getList.url,
            data : config.getList.data,
            success:function(res){
                if (res.code == 0) {    
                    config.layimConf.init = res.data;
                    layimInit();
                    $.isEmptyObject(config.socketPath.url) || initSocket();
                }
            }
        }         
        ajax(options);                         
    },sendMessage = function(res){ 
        // 发送消息 
        layui.each(call.sendMessage, function(index, item){
            item && item(res);
        });        
             
    },detail = function(res){
        console.log(res);
        config.getMembers.data.groupid = res.id;
        var options = {
            url:config.getMembers.url,
            data:config.getMembers.data,
            success:function(resp){
                if (resp.code == 0) {
                    var info = resp.data.list;
                    var _html = '<div class="layui-layim-list layim-list-group detail" data-json="'+encodeURIComponent(JSON.stringify(res))+'">';
                    for (var i = 0; i < info.length; i++) {
                        _html += '<li layimExt-event="userInfo" data-type="friend"   data-id="'+info[i].userid+'" class="layim-friend'+info[i].userid+' "><div><img src="'+info[i].useravatar+'"></div><span>'+info[i].username+'</span>';
                        _html += '<p>'+info[i].usersign +'</p><span class="layim-msg-status">new</span>'
                        if (info[i].userid == mine) {
                            var role = info[i].role;
                        }
                        if (info[i].role == config.userIdentity.owner) {
                            _html += '<span class="layim-group-role-owner">群主</span>';
                        }else if (info[i].role == config.userIdentity.manager) {
                            _html += '<span class="layim-group-role-manager">管理员</span>';
                        }
                        _html += '</li>'
                    }
                    _html += '</div>';  
                    //弹出面板
                    layim.panel({
                        title: res.name + ' 群成员信息' //标题
                        ,tpl: _html 
                    });

                    $(".layim-title:last").find('p').append('<i class="layui-icon layim-chat-back" id="layim-new-group"  data-type="group" data-avatar="'+res.avatar+'" data-groupid="'+res.id+'" data-groupname="'+res.groupname+'" data-id="'+mine+'" layimExt-event="memberSet" style="right: 0px;position: absolute;">&#xe65f</i><i class="layui-icon layim-chat-back" id="layim-new-group"  data-type="group" data-groupid="'+res.id+'" data-groupname="'+res.groupname+'" data-id="'+mine+'" layimExt-event="groupSet" style="right: 42px;position: absolute;">&#xe716;</i>');                                      
                }
            }
        }
        ajax(options);
        layui.each(call.detail, function(index, item){
            item && item(res);
        });                      
    },groupSearch = function(othis){
          var search = $(".layui-m-layermain").find('.layui-layim-group-search');
          var main = $(".layui-m-layermain").find('.layim-list-group');
          var input = search.find('input'), find = function(e){
            var val = input.val().replace(/\s/);
            var data = [];
            var group = $(".layim-list-group li") || [], html = '';        
            if(val === ''){
              for(var j = 0; j < group.length; j++){
                  group.eq(j).css("display","block");
              }
            } else {
                for(var j = 0; j < group.length; j++){
                    name = group.eq(j).find('span').html();
                    if(name.indexOf(val) === -1){
                        group.eq(j).css("display","none");
                    }else{
                        group.eq(j).css("display","block"); 
                    }
                }
            }
          };       
          search.show();
          input.focus();
          input.off('keyup', find).on('keyup', find);
    },addBtn = function(data){
        $(".layim-title:last").append('<span layimExt-event="'+data.event+'" class="'+data.class+'" data-json="'+encodeURIComponent(JSON.stringify(data.data))+'">'+data.title+'</span>');
        $(".add-into-checkbox").click(function(){
            if($(this).is(':checked')){
                $(this).next().css('background','#58b184');
            }else{
                $(this).next().css('background','#fff');
            }
            var len = $("input:checkbox:checked").length;
            if (len > 0) {
                $("."+data.class).html(data.title+'('+len+')');
            }else{
                $("."+data.class).html(data.title);
            }
        });
    },PrePage = function(page){
        //返回上一页
        var othis = $('.layui-m-layer:last'),PrePage = othis;
        for (var i = 1; i < page; i++) {
            PrePage = PrePage.prev();
        }
        setTimeout(function(){
            PrePage.find('.layim-panel').removeClass('layui-m-anim-left').addClass('layui-m-anim-rout');
            PrePage.prev().find('.layim-panel').removeClass('layui-m-anim-lout').addClass('layui-m-anim-right');                                
            PrePage.remove();
        }, 800);         
    },userInfoTpl = function(res,info){
        var _html = '<div class="layui-layim-list layim-list-group layim-setting layim-friend'+info.id+'">';
            _html += '<li  ><div><span class="layim-m-avatar">头像</span><img style="left: 75%;" src="'+info.avatar+'"></div></li>';
            _html += '<li  ><div><span class="layim-m-avatar">昵称</span></div><span class="layui-icon layui-m-info r30 fs18">'+info.username+'</span></li>';  
            _html += '<li  ><div><span class="layim-m-avatar">签名</span></div><span class="layui-icon layui-m-info r30 fs14 doubleline">'+info.sign+'</span></li>';                                     
            _html += '<li  ><div><span class="layim-m-avatar">入群时间</span></div><span class="layui-icon layui-m-info r30 fs14 ">'+res.add_time+'</span></li>';                                     
            _html += '<li  ><div><span class="layim-m-avatar">最近发言时间</span></div><span class="layui-icon layui-m-info r30 fs14 doubleline" style="max-width: 125px;">'+res.lastlog+'</span></li>';                                     
            _html += '<li  ><div><span class="layim-m-avatar">是否在线</span></div><span class="layui-icon layui-m-info r30 fs14 ">'+res.status+'</span></li>';                                     
            _html += '</div>';  
            _html += '<div class="layui-layim-list layim-list-group layim-setting" data-groupname="'+info.groupname+'" data-groupid="'+info.groupid+'" data-id="'+info.id+'">';
            if ((res.role == config.userIdentity.user || res.role == config.userIdentity.manager) && res.mine == config.userIdentity.owner) {
                _html += '<li  ><div><span class="layim-m-avatar">设为管理员</span></div><div class="sitchdiv"><span id="role" class="switch-off"></span></div></li>';                                     
            }
            if ((res.role == config.userIdentity.user && (res.mine == config.userIdentity.manager || res.mine == config.userIdentity.owner)) || (res.role == config.userIdentity.manager &&  res.mine == config.userIdentity.owner)) {
                if (res.gag == 0 || res.gag == undefined) {
                    _html += '<li id="gag" layimExt-event="gag">';
                }else{
                    _html += '<li id="gag" layimExt-event="removeGag">';
                }
                _html += '<div><span class="layim-m-avatar">设置禁言</span></div>';
                _html += '<i class="layui-icon layui-m-info r5">&#xe602;</i>'; 
                if (res.gag != 0 && res.gag != undefined) {
                    _html += '<span id="gaging" class="layui-icon layui-m-info r30 fs14 ">禁言中</span>';
                }   
            }                                 
            _html += '</li></div>';  
            _html += '<div class="layui-layim-list layim-list-group layim-setting send" data-groupname="'+info.groupname+'" data-groupid="'+info.groupid+'" data-id="'+info.id+'">';
            if (info.id != layim.cache().mine.id) {
                _html += '<button layim-event="chat" data-type="friend" data-id="'+info.id+'" data-index="0" class="btn" style="background-color:#01AAED">发消息</button>';   
            }
            if ((res.role == config.userIdentity.user && (res.mine == config.userIdentity.manager || res.mine == config.userIdentity.owner)) || (res.role == config.userIdentity.manager &&  res.mine == config.userIdentity.owner)) {
                _html += '<button layimExt-event="removeGroup" class="btn">移出本群</button>';   
            }
            _html += '</div>';  
            return _html; 
    },groupInfoTpl = function(res,data){
        if (res.data.info.owner_id == data.id) {
            var _html = '<div class="layui-layim-list layim-list-group layim-setting layim-group'+data.groupid+'" data-groupname="'+res.data.info.groupname+'" data-groupid="'+data.groupid+'" data-id="'+data.id+'">';
                _html += '<li layim-event="setGroupAvatar"><div><span class="layim-m-avatar">群头像</span><img id="set_avatar" style="left: 75%;" src="'+res.data.info.avatar+'"></div>'
                _html += '<i class="layui-icon layui-m-info">&#xe602;</i></li>';
                _html += '<li layim-event="setGroupname"><div><span class="layim-m-avatar">群号</span></div><span class="layui-icon layui-m-info r30 fs18 " >'+res.data.info.group_no+'</span></li>';
                _html += '<li layim-event="setGroupname"><div><span class="layim-m-avatar">群名称</span></div><span class="layui-icon layui-m-info r30 fs18 " id="set_groupname">'+res.data.info.groupname+'</span>';
                _html += '<i class="layui-icon layui-m-info">&#xe602;</i></li>';
                _html += '</div>';
                _html += '<div class="layui-layim-list layim-list-group layim-setting" data-groupname="'+res.data.info.groupname+'" data-groupid="'+data.groupid+'" data-id="'+data.id+'">';
                _html += '<li  ><div><span class="layim-m-avatar">允许搜索找到群</span></div><div class="sitchdiv"><span id="public" class="switch-on"></span></div></li>';
                _html += '<li  ><div><span class="layim-m-avatar">加群是否需要审核</span></div><div class="sitchdiv"><span id="verify" class="switch-on"></span></div></li>';
                _html += '</div>';
                _html += '<div class="layui-layim-list layim-list-group layim-setting send" data-groupname="'+res.data.info.groupname+'" data-groupid="'+data.groupid+'" data-id="'+data.id+'">';
                // _html += '<button layim-event="handoverGroup" class="btn" style="background-color:#01AAED">转让该群</button>';
                _html += '<button layim-event="disbandedGroup" class="btn">解散该群</button>';
                _html += '</div>';
            }else{
            var _html = '<div class="layui-layim-list layim-list-group layim-setting layim-group'+data.groupid+'" data-groupname="'+res.data.info.groupname+'" data-groupid="'+data.groupid+'" data-id="'+data.id+'">';
                _html += '<li><div><span class="layim-m-avatar">群头像</span><img id="set_avatar" style="left: 75%;" src="'+res.data.info.avatar+'"></div></li>';
                _html += '<li><div><span class="layim-m-avatar">群号</span></div><span class="layui-icon layui-m-info r30 fs18 " >'+res.data.info.group_no+'</span></li>';
                _html += '<li><div><span class="layim-m-avatar">群名称</span></div><span class="layui-icon layui-m-info r30 fs18 " id="set_groupname">'+res.data.info.groupname+'</span></li>';
                _html += '</div>';
                _html += '<div class="layui-layim-list layim-list-group layim-setting send" data-groupname="'+res.data.info.groupname+'" data-groupid="'+data.groupid+'" data-id="'+data.id+'">';
                _html += '<button layim-event="leaveGroup" class="btn">退出该群</button>';
                _html += '</div>';
            }
            return _html;
    },addSureIntoGroupTpl = function(resp){
        var friend = layim.cache().friend,list = {};
        for (var i = 0; i < friend.length; i++) {
            $.extend(list, friend[i].list);
        }           
        var  groupUsers = resp.data.list;
        var _html = '<div class="layui-layim-group-search" layim-event="groupSearch"><i class="layui-icon layim-serach-icon" >&#xe615;</i><input placeholder="搜索" class="layim-friend-search"></div>';
        _html += '<div class="layui-layim-list layim-list-group" style="margin-top: 57px;">';
        for (var i = 0; i < Object.keys(list).length; i++) {
            _html += '<li data-type="friend" data-type="friend" data-id="'+list[i].id+'" data-index="0" class="layim-friend'+list[i].id+' "><div><img src="'+list[i].avatar+'"></div><span>'+list[i].username+'</span>';
            var inGroup = false;
            for (var j = 0; j < groupUsers.length; j++) {
                if (groupUsers[j]['userid'] == list[i].id) {
                    inGroup = true;
                    break;
                }
            }
            if (inGroup) {
                _html += '<div class="add-into-opacity-checked layui-icon" style="background:#a0bdae">&#xe605;</div>';
            }else{
                _html += '<input type="checkbox" name="ids" class="add-into-checkbox" value="'+list[i].id+'">';         
                _html += '<div class="add-into-opacity layui-icon">&#xe605;</div>'; 
            }

            _html += '<p>'+list[i].sign+'</p><span class="layim-msg-status">new</span></li>'; 
        }
        _html += '</div>';    
        return _html;    
    },delUserFromGroupTpl = function(res){
        var _html = '<div class="layui-layim-group-search" layim-event="groupSearch"><i class="layui-icon layim-serach-icon" >&#xe615;</i><input placeholder="搜索" class="layim-friend-search"></div>';
        _html += '<div class="layui-layim-list layim-list-group" style="margin-top: 57px;">';
        for (var i = 0; i < res.data.list.length; i++) {
            if (res.data.list[i].role == config.userIdentity.user) {
                _html += '<li data-type="friend" data-type="friend" data-id="'+res.data.list[i].id+'" data-index="0" class="layim-friend'+res.data.list[i].id+' "><div><img src="'+res.data.list[i].useravatar+'"></div><span>'+res.data.list[i].username+'</span>';
                _html += '<input type="checkbox" name="ids" class="add-into-checkbox" value="'+res.data.list[i].id+'">';         
                _html += '<div class="add-into-opacity layui-icon">&#xe605;</div>'; 
                _html += '<p>'+res.data.list[i].usersign+'</p><span class="layim-msg-status">new</span></li>';          
            }
        }
        _html += '</div>';
        return _html;
    },gagTpl = function(res){
        var _html = '<div class="layui-layim-list layim-list-group layim-setting layim-friend'+res.id+'">';
            _html += '<li class="m2"><div><span class="layim-m-avatar">10分钟</span></div><input class="gagradio" type="radio" name="gag" value="10" title="gag"></li>';
            _html += '<li class="m2"><div><span class="layim-m-avatar">30分钟</span></div><input class="gagradio" type="radio" name="gag" value="30" title="gag"></li>';  
            _html += '<li class="m2"><div><span class="layim-m-avatar">1小时</span></div><input class="gagradio" type="radio" name="gag" value="60" title="gag"></li>';                                     
            _html += '<li class="m2"><div><span class="layim-m-avatar">3小时</span></div><input class="gagradio" type="radio" name="gag" value="180" title="gag"></li>';                                     
            _html += '<li class="m2"><div><span class="layim-m-avatar">12小时</span></div><input class="gagradio" type="radio" name="gag" value="720" title="gag"></li>';                                     
            _html += '<li class="m2"><div><span class="layim-m-avatar">1天</span></div><input class="gagradio" type="radio" name="gag" value="1440" title="gag"></li>';                                     
            _html += '</div>'; 
            _html += '<div class="layui-layim-list layim-list-group layim-setting send" data-groupname="'+res.groupname+'" data-groupid="'+res.groupid+'" data-id="'+res.id+'">';
            _html += '<button layimExt-event="sureGag" class="btn" style="background-color:#01AAED">确定</button>';   
            _html += '</div>'; 
            return _html;         
    },switchInit = function(){
        var s = "<span class='slider'></span>";
        $("[class^=switch]").append(s);
        $("[class^=switch]").click(function() {
            if ($(this).hasClass("switch-disabled")) {
                return;
            }
            if ($(this).hasClass("switch-on")) {
                $(this).removeClass("switch-on").addClass("switch-off");
                $(".switch-off").css({
                    'border-color': '#dfdfdf',
                    'box-shadow': 'rgb(223, 223, 223) 0px 0px 0px 0px inset',
                    'background-color': 'rgb(255, 255, 255)'
                });
            } else {
                $(this).removeClass("switch-off").addClass("switch-on");
                if (config.switchColor) {
                    var c = config.switchColor;
                    $(this).css({
                        'border-color': c,
                        'box-shadow': c + ' 0px 0px 0px 16px inset',
                        'background-color': c
                    });
                }
                if ($(this).attr('themeColor')) {
                    var c2 = $(this).attr('themeColor');
                    $(this).css({
                        'border-color': c2,
                        'box-shadow': c2 + ' 0px 0px 0px 16px inset',
                        'background-color': c2
                    });
                }
            }
        });
        if (config.switchColor) {
            var c = config.switchColor;
            $(".switch-on").css({
                'border-color': c,
                'box-shadow': c + ' 0px 0px 0px 16px inset',
                'background-color': c
            });
            $(".switch-off").css({
                'border-color': '#dfdfdf',
                'box-shadow': 'rgb(223, 223, 223) 0px 0px 0px 0px inset',
                'background-color': 'rgb(255, 255, 255)'
            });
        }
        if ($('[themeColor]').length > 0) {
            $('[themeColor]').each(function() {
                var c = $(this).attr('themeColor') || config.switchColor;
                if ($(this).hasClass("switch-on")) {
                    $(this).css({
                        'border-color': c,
                        'box-shadow': c + ' 0px 0px 0px 16px inset',
                        'background-color': c
                    });
                } else {
                    $(".switch-off").css({
                        'border-color': '#dfdfdf',
                        'box-shadow': 'rgb(223, 223, 223) 0px 0px 0px 0px inset',
                        'background-color': 'rgb(255, 255, 255)'
                    });
                }
            });
        }

    },switchEvent = function(res){
        $("#role").click(function() {
            if ($(this).hasClass("switch-disabled")) {
                return;
            }
            config.manager.data.groupid = res.groupid;
            config.manager.data.userid = res.userid;           
            var options = {
                url:config.manager.url,
                data:config.manager.data,
                success:function(resp){
                    if (resp.code == 0) {
                        if (resp.data == config.userIdentity.manager) {
                            showOn("#role");
                        }else if (resp.data == config.userIdentity.user){
                            showOff("#role");
                        }                                   
                    }
                    layer.open({
                        content: resp.msg
                        ,skin: 'msg'
                        ,time: 2 //2秒后自动关闭
                    });                      
                }
            }
            ajax(options);
        });
    },switchPublic = function(res){
        $("#public").click(function() {
            if ($(this).hasClass("switch-disabled")) {
                return;
            }
            config.isPublic.data.groupid = res.groupid;
            config.isPublic.data.userid = res.userid;           
            var options = {
                url:config.isPublic.url,
                data:config.isPublic.data,
                success:function(resp){
                    if (resp.code == 0) {
                        if (resp.data == config.userIdentity.manager) {
                            showOn("#public");
                        }else if (resp.data == config.userIdentity.user){
                            showOff("#public");
                        } 
                        layer.open({
                            content: resp.msg
                            ,skin: 'msg'
                            ,time: 2 //2秒后自动关闭
                        });                                    
                    }
                }
            }
            ajax(options);
        });   
    },showOn = function(ele){
        $(ele).removeClass("switch-off").addClass("switch-on");
        if (config.switchColor) {
            var c = config.switchColor;
            $(ele).css({
                'border-color': c,
                'box-shadow': c + ' 0px 0px 0px 16px inset',
                'background-color': c
            });
        }
        if ($(ele).attr('themeColor')) {
            var c2 = $(ele).attr('themeColor');
            $(ele).css({
                'border-color': c2,
                'box-shadow': c2 + ' 0px 0px 0px 16px inset',
                'background-color': c2
            });
        }
    },showOff = function(ele){
        $(ele).removeClass("switch-on").addClass("switch-off");
        $(".switch-off").css({
            'border-color': '#dfdfdf',
            'box-shadow': 'rgb(223, 223, 223) 0px 0px 0px 0px inset',
            'background-color': 'rgb(255, 255, 255)'
        });
    },getData = function(othis){
        //做到这里了，数据用json串储存
        var object = {};
        var data = (!$.isEmptyObject(othis.data()))?othis.data():object; 
        var parentdata = (!$.isEmptyObject(othis.parent().data()))?othis.parent().data():object; 
        if (othis.attr('data-json')) {
            data.json = JSON.parse(decodeURIComponent(othis.attr('data-json')));
        }
        if (othis.parent().attr('data-json')) {
            parentdata.json = JSON.parse(decodeURIComponent(othis.parent().attr('data-json')));
        }    
        $.extend(data, parentdata);  
        return data;            
    },x = function(res){
        config.getMembers.data.groupid = res.id; 
        var options = {
            url:config.getMembers,
            data:config.getMembers.data,
            success:function(resp){
                if (resp.code == 0) {
                                   
                }
            }
        }
        ajax(options);
    },events = { //自定义事件
        //增减群员
        memberSet: function(){
            var othis = $(this),data = getData(othis);
            var _html = '<div class="layui-m-layerbtn"><span yes="" style="border-radius: 5px 5px 0 0;" layimExt-event="addUserIntoGroup" data-type="group" data-groupid="'+data.groupid+'" data-avatar ="'+data.avatar+'" data-groupname="'+data.groupname+'" data-id="'+mine+'" >增加群成员</span>'
                        +'<span no="" layimExt-event="delUserFromGroup" data-type="group" data-groupid="'+data.groupid+'" data-groupname="'+data.groupname+'" data-id="'+mine+'">删除群成员</span><span yes="" style="color:#000" id="closeMemberManage">取消</span></div>';     
            //页面层
            var index = layer.open({
                    type: 1
                    ,content: _html
                    ,shadeClose: false
                    ,skin:'footer'
                });  
                $('.layui-m-layer:last').find('.layui-m-layercont').css({'padding':'0px','background':'0'});
                $("#closeMemberManage").click(function(){layer.close(index);});           
        },userInfo: function(){
            var othis = $(this),data = getData(othis); 
            config.getUserInfo.data.userid = data.id;        
            // config.getUserInfo.data.groupid = data.groupid;      
            config.getUserInfo.data.groupid = data.json.id;    
            var options = {
                url:config.getUserInfo.url,
                data:config.getUserInfo.data,
                success:function(resp){
                    if (resp.code == 0) {
                        //查看自己的资料
                        if (data.id == mine) {
                            var info = layim.cache().mine;
                        }else{
                            info = resp.data.userinfo;
                            // var user = layim.cache().friend[data.index].list;
                            // var user = layim.cache().friend[0].list;
                            // for (var i = 0; i < user.length; i++) {
                            //     if(user[i].id == data.id){
                            //         var info = user[i];
                            //     }
                            // }     
                        }  
                        info.groupid = data.json.id;
                        info.groupname = data.json.groupname;                        
                        var tpl = userInfoTpl(resp.data,info);
                        //弹出面板
                        layim.panel({
                          title: info.username + ' 的资料' //标题
                          ,tpl: tpl //模版，基于laytpl语法
                        });  
                        switchInit();
                        if (resp.data.role == 2) {
                            showOn("#role");
                        }
                        switchEvent({userid:data.id,groupid:data.json.id});                                                            
                    }
                }
            }
            ajax(options);
            layui.each(call.userInfo, function(index, item){
                item && item(data);
            });            

        },gag: function(){
            var othis = $(this),data = getData(othis);
            var tpl = gagTpl(data);                                    
            //弹出面板
            layim.panel({
              title: ' 禁言时长' //标题
              ,tpl: tpl //模版，基于laytpl语法
            });
            $("input[type='radio']").click(function() {
                $(this).parent().siblings().removeClass('gagdiv');
                $(this).parent().addClass('gagdiv');
            });
            layui.each(call.gag, function(index, item){
                item && item(data);
            });            
        },sureGag: function(data){
            var othis = $(this),data = getData(othis);
            var gagTime = othis.parent().parent().find("input[type='radio']:checked").val();
            var gag = gagTime*60;
            config.sureGag.data.userid = data.id;
            config.sureGag.data.groupid = data.groupid;
            config.sureGag.data.gag = gag;
            var options = {
                url:config.sureGag.url,
                data:config.sureGag.data,
                success:function(resp){
                    if (resp.code == 0) {
                        layui.each(call.socketSureGag, function(index, item){
                            item && item(data);
                        });                          
                        PrePage(1);
                        $("#gag").attr("layimExt-event","removeGag");
                        $("#gag").append('<span id="gaging" class="layui-icon layui-m-info r30 fs14 ">禁言中</span>');         
                    }
                }
            }
            config.sureGag.url && ajax(options); 
            layui.each(call.sureGag, function(index, item){
                item && item(data);
            });                                                         
        },removeGag: function(){
            var othis = $(this),data = getData(othis);
            layer.open({
                content: '解除禁言？'
                ,shadeClose: false
                ,btn: ['确认', '取消']
                ,skin: 'footer'
                ,yes:function(index){
                    config.removeGag.data.userid = data.id;
                    config.removeGag.data.groupid = data.groupid;
                    var options = {
                        url:config.removeGag.url,
                        data:config.removeGag.data,
                        success:function(resp){
                            if (resp.code == 0) {
                                layui.each(call.socketRemoveGag, function(index, item){
                                    item && item(data);
                                });                                  
                                $("#gag").attr("layimExt-event","gag");
                                $("#gag").find("#gaging").remove();           
                            }
                            layer.open({
                                content: resp.msg || '可能未配置removeGag'
                                ,skin: 'msg'
                                ,time: 2 //2秒后自动关闭
                            });  
                            layer.close(index);                               
                        }
                    }
                    config.removeGag.url && ajax(options); 
                    layui.each(call.removeGag, function(index, item){
                        item && item(data);
                    });  
                }
            });             
        },removeGroup: function(){
            var othis = $(this),data = getData(othis);
            layer.open({
                content: '确定将该用户移出本群？'
                ,shadeClose: false
                ,btn: ['确认', '取消']
                ,skin: 'footer'
                ,yes:function(index){
                    config.removeGroup.data.userid = data.id;
                    config.removeGroup.data.groupid = data.groupid;
                    var options = {
                        url:config.removeGroup.url,
                        data:config.removeGroup.data,
                        success:function(resp){
                            if (resp.code == 0) {
                                $('.detail').find(".layim-friend"+data.id).remove();                             
                                layui.each(call.socketRemoveGroup, function(index, item){
                                    item && item(data);
                                });   
                            }
                            layer.open({
                                content: resp.msg || '可能未配置removeGroup'
                                ,skin: 'msg'
                                ,time: 3 //2秒后自动关闭
                            }); 
                            //返回上一页
                            PrePage(2);                           
                        }
                    }
                    config.removeGroup.url && ajax(options); 
                    layui.each(call.removeGroup, function(index, item){
                        item && item(data);
                    }); 
                    layer.close(index);        
                }
            });             
        },delUserFromGroup: function(){
            var othis = $(this),data = getData(othis);
            config.getMembers.data.groupid = data.groupid;
            var options = {
                url:config.getMembers.url,
                data:config.getMembers.data,
                success:function(resp){
                    if (resp.code == 0) {
                        var tpl = delUserFromGroupTpl(resp);
                        //弹出面板
                        layim.panel({
                            title: '群成员' //标题
                            ,tpl: tpl 
                            ,data: {}                 
                        }); 
                        addBtn({
                            event:'delSureFromGroup',
                            class:'layim-del-group',
                            title:'删除',
                            data:data
                        });
                        groupSearch();                                                               
                    }
                }
            }
            ajax(options); 
        },delSureFromGroup: function(){
            var othis = $(this),data = getData(othis);
            console.log(data);
            var ids = []; 
            $(".layui-m-layer:last").find("input:checkbox:checked").each(function(){
                ids.push($(this).val());
            });
            if (ids.length == 0) {
                layer.open({
                    content:'还没有选择成员',
                    skin:'msg',
                    time:2,
                });
                return false;
            }
            layer.open({
                content: '确定删除？'
                ,shadeClose: false
                ,btn: ['确定', '取消']
                ,yes:function(index){
                    config.delSureFromGroup.data.userid = data.json.id;
                    config.delSureFromGroup.data.members = data.ids = ids;
                    config.delSureFromGroup.data.groupid = data.json.groupid;
                    var options = {
                        url:config.delSureFromGroup.url,
                        data:config.delSureFromGroup.data,
                        success:function(resp){
                            if (resp.code == 0) {
                                for (var i = 0; i < ids.length; i++) {
                                    $(".layim-list-group").find('.layim-friend'+ids[i]).remove(); 
                                }    
                                layui.each(call.socketDelSureFromGroup, function(index, item){
                                    item && item(data);
                                });   
                            }
                            layer.open({
                                content: resp.msg || '可能未配置delSureFromGroup'
                                ,skin: 'msg'
                                ,time: 3 //2秒后自动关闭
                            });  
                        }
                    }
                    config.delSureFromGroup.url && ajax(options); 
                    layui.each(call.delSureFromGroup, function(index, item){
                        item && item(data);
                    }); 
                    layer.close(index);
                }
            });            

        },addUserIntoGroup: function(){
            var othis = $(this),data = getData(othis);
            config.getMembers.data.groupid = data.groupid;
            var options = {
                url:config.getMembers.url,
                data:config.getMembers.data,
                success:function(resp){
                    if (resp.code == 0) {
                        var tpl = addSureIntoGroupTpl(resp);
                        //弹出面板
                        layim.panel({
                            title: '邀请好友入群' //标题
                            ,tpl: tpl 
                            ,data: {}                 
                        }); 
                        addBtn({
                            event:'addSureIntoGroup',
                            class:'layim-sure-group',
                            title:'确定',
                            data:data
                        });
                        groupSearch();                                                               
                    }
                }
            }
            ajax(options);
        },addSureIntoGroup: function(){
            var othis = $(this),data = getData(othis);
            var ids = []; 
            $(".layui-m-layer:last").find("input:checkbox:checked").each(function(){
                ids.push($(this).val());
            });
            if (ids.length == 0) {
                layer.open({
                    content:'还没有选择成员',
                    skin:'msg',
                    time:2,
                });
                return false;
            }
            layer.open({
                content: '确定邀请？'
                ,shadeClose: false
                ,btn: ['确定', '取消']
                ,yes:function(index){
                    config.addSureIntoGroup.data.userid = data.json.id;
                    config.addSureIntoGroup.data.members = data.ids = ids;
                    config.addSureIntoGroup.data.groupid = data.json.groupid;
                    var options = {
                        url:config.addSureIntoGroup.url,
                        data:config.addSureIntoGroup.data,
                        success:function(resp){
                            if (resp.code == 0) {
                                for (var i = 0; i < ids.length; i++) {
                                    $(".layim-list-group").find('.layim-friend'+ids[i]).remove(); 
                                }    
                                layui.each(call.socketAddIntoGroup, function(index, item){
                                    item && item(data);
                                });   
                            }
                            layer.open({
                                content: resp.msg || '可能未配置addSureIntoGroup'
                                ,skin: 'msg'
                                ,time: 3 //2秒后自动关闭
                            });  
                        }
                    }
                    config.addSureIntoGroup.url && ajax(options); 
                    layui.each(call.addIntoGroup, function(index, item){
                        item && item(data);
                    }); 
                    layer.close(index);
                }
            });               
        },groupSet: function(){
            var othis = $(this),data = getData(othis);
            config.getGroupSet.data.userid = data.id;             
            config.getGroupSet.data.groupid = data.groupid;    
            var options = {
                url:config.getGroupSet.url,
                data:config.getGroupSet.data,
                success:function(resp){
                    if (resp.code == 0) {                      
                        var tpl = groupInfoTpl(resp,data);
                        //弹出面板
                        layim.panel({
                          title: resp.data.info.groupname + ' 群资料' //标题
                          ,tpl: tpl //模版，基于laytpl语法
                        });  
                        switchInit();
                        if (resp.data.role == 2) {
                            showOn("#role");
                        }
                        switchEvent({userid:data.id,groupid:data.json.id});                                                            
                    }
                }
            }
            ajax(options);
            layui.each(call.userInfo, function(index, item){
                item && item(data);
            });            

        }
    }


    layimExt.prototype.init = function(options){ 
        $.each(options, function(index, item){
            item && (config[index] = item);
        });  
        isMobile && init();
    }
    //监听事件
    layimExt.prototype.on = function(events, callback){
        if(typeof callback === 'function'){           
            call[events] ? call[events].push(callback) : call[events] = [callback];
        }
        return this;
    };       
    //监听事件
    layimExt.prototype.socketSend = function(data){
        socketSend(data);
    };    
 
    win.layimExt = new layimExt();
  
}(window);

