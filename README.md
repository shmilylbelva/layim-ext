
# 如何运行  
1、将代码下载到本地环境，并配置好虚拟域名(没有也行)，使 网址根目录指向public ,例如E:\WWW\layim\public， 
  
2、新建数据库名 为 layim-ext ，并导入 back 文件夹下的 layimext.sql 表，配置文件中的账户密码默认为root，如果和你的数据库不一样请修改，地址为application/database.php,workerman配置地址vendor/Workerman/Applications/Config/Db.php   
  
3、启动 getwayworker，请双击/vendor/Workerman/applications/start_for_win.bat,然后不要关闭窗口。 linux  进入/vendor/workerman/applications   php start.php start -d

4、访问聊天系统，进入前台，前台用户密码默认为admin，用户在chatuser表，密码登录即可聊天。 请用两个浏览器打开，登录不同的账户互相聊天。 后台地址为/admin，账户密码为admin 

5、启动 workerman后，不要关闭！！！

6、服务器上搭建请 参考 https://github.com/shmilylbelva/laykefu 开放端口 8282 端口 ，pcntl相关函数取消禁用


