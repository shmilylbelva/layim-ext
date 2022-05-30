
# 如何运行  
1、将代码下载到本地环境，并配置好虚拟域名(没有也行)，使 网址根目录指向public ,例如E:\WWW\layim\public， 
  
2、新建数据库名 为 layim-ext ，并导入 back 文件夹下的 layimext.sql 表，配置文件中的账户密码默认为root，如果和你的数据库不一样请修改，地址为application/database.php,workerman配置地址vendor/Workerman/Applications/Config/Db.php   
  
3、启动 getwayworker，请双击/vendor/Workerman/applications/start_for_win.bat,然后不要关闭窗口。 linux  进入/vendor/workerman/applications   php start.php start -d

4、访问聊天系统，进入前台，前台用户密码默认为admin，用户在chatuser表，密码登录即可聊天。 请用两个浏览器打开，登录不同的账户互相聊天。 后台地址为/admin，账户密码为admin 

5、启动 workerman后，不要关闭！！！

6、服务器上搭建请 参考 https://github.com/shmilylbelva/laykefu 开放端口 8282 端口 ，pcntl相关函数取消禁用

功能大概完成了40%，陆续完善中

详细截图 https://fly.layui.com/jie/54508/

演示账号  纸飞机    admin
		 罗玉凤	   admin

右滑删除 、查找好友、群 、创建群聊、个人信息修改、好友界面、好友设置、群聊天分类、群设置、群员管理、禁言、群列表、群主界面、[删除群员、增加群员、设置、[消息盒子


![1219176_1561950479363_7345.jpg](https://upload-images.jianshu.io/upload_images/2825702-310fabd43873c7ab.jpg?imageMogr2/auto-orient/strip%7CimageView2/2/w/1240)

![1219176_1561950470528_97940.jpg](https://upload-images.jianshu.io/upload_images/2825702-1d8dd60be1d54486.jpg?imageMogr2/auto-orient/strip%7CimageView2/2/w/1240)

![1219176_1561950595424_32621.jpg](https://upload-images.jianshu.io/upload_images/2825702-826119d32f854134.jpg?imageMogr2/auto-orient/strip%7CimageView2/2/w/1240)

![1219176_1561950609392_38384.jpg](https://upload-images.jianshu.io/upload_images/2825702-b7a63bb5e713b12b.jpg?imageMogr2/auto-orient/strip%7CimageView2/2/w/1240)

![1219176_1561950669441_82845.jpg](https://upload-images.jianshu.io/upload_images/2825702-caa62ce5fb6f34b5.jpg?imageMogr2/auto-orient/strip%7CimageView2/2/w/1240)

![1219176_1561953825625_89321.jpg](https://upload-images.jianshu.io/upload_images/2825702-609892191a9ac3a9.jpg?imageMogr2/auto-orient/strip%7CimageView2/2/w/1240)


配置起来也很方便，像下面这样就行
```
    layui.config({version:'1.0.3'}).use('mobile', function(){
      var mobile = layui.mobile,
      layim = mobile.layim; 
      var layer = layui['layer-mobile'];
        //基础配置
        var layimConf = {
            // //获取主面板列表信息
            init:  null,
            uploadFile: {
                url: "/index/upload/uploadFile"
            }
            ,uploadImage: {
                url: "/index/upload/uploadimg"
            }
            ,moreList: [{
              alias: 'setting'
              ,title: '设置'
              ,iconUnicode: '' //图标字体的unicode，可不填
              ,iconClass: '' //图标字体的class类名
            }]                
            ,brief: false //是否简约模式（默认false，如果只用到在线客服，且不想显示主面板，可以设置 true）
            ,title: '爱信' //主面板最小化后显示的名称
            ,maxLength: 3000 //最长发送的字符长度，默认3000
            ,notice: true //是否开启好友（默认true，即开启）
            ,isgroup: true //是否开启群组（默认true，即开启）
            ,copyright: false //是否授权，如果通过官网捐赠获得LayIM，此处可填true
        };

        layimExt.init({
            //socket地址以及携带的参数，如果需要自己初始化socket，可忽略该配置
            socketPath:{
                url:'',//ws://127.0.0.1:8282
                data:"{$data.code}"
            },
            //客户端主动发送的心跳(若心跳由服务端负责，可忽略该配置)
            socketPing:{
                data:{
                    type:"ping"
                },
                times:"20000"
            },            
            //socket握手成功后调用的方法(生命周期内只调用一次)，有个性化需求可忽略该配置
            socketInit:{
                data:{
                    type:"init",
                    id:"{$data.id}",
                    username:"{$data.username}",
                    avatar:"{$data.avatar}",
                    sign:"{$data.sign}"
                }
            },           
            //栗子
            //返回参数参考 https://www.layui.com/doc/modules/layim.html#init
            // getList:{
            //     url:'/index/getList',//接口地址 
            //     type:'get',//默认为get方式
            //     data:{
            //          id: 1,
            //          type: friend
            //     }//额外参数
            // },
            //初始化列表
            getList:{
                url:'/index/index/getList',
                type:'get',//默认为get方式
                data:{}//额外参数                
            },
            //获取群成员列表
            getMembers:{
                url:'/index/index/getMembers',
                data:{}
            },            
            // //获取我的好友
            // getMyfriend:{
            //     url:'/index/index/getMyfriend',
            //     data:{}
            // },
            //获取成员详细信息
            getUserInfo:{
                url:'/index/index/getUserInfo',
                data:{}
            },
            //群设置管理员
            manager:{
                url:'/index/index/manager',
                data:{}
            },                  
            //确认删除群成员
            addSureIntoGroup:{
                url:'/index/index/addSureIntoGroup',
                data:{}
            },            
            //确认删除群成员
            delSureFromGroup:{
                url:'/index/index/delSureFromGroup',
                data:{}
            },            
            //是否是公开群
            isPublic:{
                url:'/index/index/isPublic',
                data:{}
            },            
            //入群是否需要验证
            verify:{
                url:'/index/index/verify',
                data:{}
            },
            //确认禁言
            sureGag:{
                url:'/index/index/gag',
                data:{}
            },
            //解除禁言
            removeGag:{
                url:'/index/index/gag',
                data:{}
            },              
            //移出群
            removeGroup:{
                url:'/index/index/removegroup',
                data:{}
            },              
            //退出
            logout:{
                url:'/index/index/logout',
                data:{}
            },            
            layim:layim,
            layimConf:layimConf,
            layer:layer,
            userIdentity:{
                owner:'1',
                manager:'2',
                user:'3'
            }
        });     


        layimExt.on('userInfo',function(data){
            
        });
     
        layimExt.on('detail',function(data){

        });         
        layimExt.on('gag',function(data){

        });
        layimExt.on('sureGag',function(data){

        });    
        layimExt.on('removeGag',function(data){
            
        });         
        layimExt.on('addSureIntoGroup',function(data){
            
        });           
        layimExt.on('delSureFromGroup',function(data){
            
        });   
        // 发送socket消息        
        layimExt.on('sendMessage',function(data){
            layimExt.socketSend({
                type:"chatMessage",
                data:{
                    mine: data.mine,
                    to: data.to
                }
            });
        });            
        // 添加群成员后发送socket消息        
        layimExt.on('socketAddSureIntoGroup',function(data){
            layimExt.socketSend({
                type:"addIntoGroup",
                data:{
                    uids: data.uids,
                    id:data.groupid,
                    groupname:data.groupname
                }
            });
        });         
        // 删除群成员后发送socket消息        
        layimExt.on('socketDelSureFromGroup',function(data){
            layimExt.socketSend({
                type:"delFromGroup",
                data:{
                    uids: data.uids,
                    id:data.groupid,
                    groupname:data.groupname
                }
            });
        });        
        // 确认禁言成功后 发送socket消息        
        layimExt.on('socketSureGag',function(data){
            layimExt.socketSend({
                type:'gag',
                data:{
                    uid: data.id,
                    id: data.groupid,
                    groupname:data.groupname
                }
            });
        });   
   
        // 取消禁言成功后 发送socket消息        
        layimExt.on('socketRemoveGag',function(data){
            layimExt.socketSend({
                type:'removeGag',
                data:{
                    uid: data.id,
                    id: data.groupid,
                    groupname:data.groupname
                }
            });
        });           
        
        // 群员移出群成功后 发送socket消息        
        layimExt.on('socketRemoveGroup',function(data){
            layimExt.socketSend({
                type:'removeMember',
                data:{
                    uid: data.id,
                    id: data.groupid,
                    groupname:data.groupname
                }
            });
        });                  
    
    });  
```


