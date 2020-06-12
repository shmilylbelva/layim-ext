<?php
namespace applications\App;
use \GatewayWorker\Lib\Gateway;
use \GatewayWorker\Lib\Db;
/**
 * 主逻辑
 * 主要是处理 onConnect onMessage onClose 三个方法
 * onConnect 和 onClose 如果不需要可以不用实现并删除
 */
const HTTP_ORIGIN = 'http://ext.laykefu.com';
// const HTTP_ORIGIN = 'http://zhibo.guoshanchina.com';

class Events
{
  
    /**
     * 当客户端连接时触发
     * 当客户端连接上gateway完成websocket握手时触发的回调函数。
     *
     * @param int $client_id 连接id
     */
    public static function onWebSocketConnect($client_id, $data)
    {
        if((HTTP_ORIGIN && $data['server']['HTTP_ORIGIN'] != HTTP_ORIGIN) || !$data['server']['REQUEST_URI'])
        {
         $init_message = array(
             'message_type' => 'unauthorized',
         );          
          Gateway::sendToClient($client_id, json_encode($init_message));
          Gateway::closeClient($client_id);
        }
        // $code = $data['server']['REQUEST_URI'];
        // $code = substr($code,1);
        // // $db = Db::instance('db');
        // $key = 'shmily@laykefu';
        // $str = self::authcode($code,'DECODE',$key,0); //解密
        // $info = explode('-',$str);
        // if ($info[0] < 1 || $info[1] > time()) {
        //   $init_message = array(
        //      'message_type' => 'unauthorized',
        //   );          
        //   Gateway::sendToClient($client_id, json_encode($init_message));
        //   Gateway::closeClient($client_id);
        // }
        // else{
        //   $find = $db->select('code')->from('ext_chatuser')->where("id = {$info[0]}")->query();
        //   if ($find[0]['code'] != $code) {
        //     $init_message = array(
        //        'message_type' => 'quit',
        //        'data' => [
        //           'msg' => '在别处登陆',
        //           'id' => $info[0],
        //        ],
        //     );          
        //     Gateway::sendToClient($client_id, json_encode($init_message));
        //     Gateway::closeClient($client_id);
        //   }
        // }
    }

private static function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {   
    // 动态密匙长度，相同的明文会生成不同密文就是依靠动态密匙   
    $ckey_length = 4;   
       
    // 密匙   
    $key = md5($key ? $key : $GLOBALS['discuz_auth_key']);   
       
    // 密匙a会参与加解密   
    $keya = md5(substr($key, 0, 16));   
    // 密匙b会用来做数据完整性验证   
    $keyb = md5(substr($key, 16, 16));   
    // 密匙c用于变化生成的密文   
    $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): 
substr(md5(microtime()), -$ckey_length)) : '';   
    // 参与运算的密匙   
    $cryptkey = $keya.md5($keya.$keyc);   
    $key_length = strlen($cryptkey);   
    // 明文，前10位用来保存时间戳，解密时验证数据有效性，10到26位用来保存$keyb(密匙b)， 
//解密时会通过这个密匙验证数据完整性   
    // 如果是解码的话，会从第$ckey_length位开始，因为密文前$ckey_length位保存 动态密匙，以保证解密正确   
    $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) :  
sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;   
    $string_length = strlen($string);   
    $result = '';   
    $box = range(0, 255);   
    $rndkey = array();   
    // 产生密匙簿   
    for($i = 0; $i <= 255; $i++) {   
        $rndkey[$i] = ord($cryptkey[$i % $key_length]);   
    }   
    // 用固定的算法，打乱密匙簿，增加随机性，好像很复杂，实际上对并不会增加密文的强度   
    for($j = $i = 0; $i < 256; $i++) {   
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;   
        $tmp = $box[$i];   
        $box[$i] = $box[$j];   
        $box[$j] = $tmp;   
    }   
    // 核心加解密部分   
    for($a = $j = $i = 0; $i < $string_length; $i++) {   
        $a = ($a + 1) % 256;   
        $j = ($j + $box[$a]) % 256;   
        $tmp = $box[$a];   
        $box[$a] = $box[$j];   
        $box[$j] = $tmp;   
        // 从密匙簿得出密匙进行异或，再转成字符   
        $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));   
    }   
    if($operation == 'DECODE') {  
        // 验证数据有效性，请看未加密明文的格式   
        if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) &&  
substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {   
            return substr($result, 26);   
        } else {   
            return '';   
        }   
    } else {   
        // 把动态密匙保存在密文里，这也是为什么同样的明文，生产不同密文后能解密的原因   
        // 因为加密后的密文可能是一些特殊字符，复制过程可能会丢失，所以用base64编码   
        return $keyc.str_replace('=', '', base64_encode($result));   
    }   
} 

   /**
    * 当客户端发来消息时触发
    * @param int $client_id 连接id
    * @param mixed $message 具体消息
    */
   public static function onMessage($client_id, $data) {
       $message = json_decode($data, true);
       $message_type = $message['type'];
       switch($message_type) {
           case 'init':
               // uid
               $uid = $message['id'];
               // 设置session
               $_SESSION = [
                   'username' => $message['username'],
                   'avatar'   => $message['avatar'],
                   'id'       => $uid,
                   'sign'     => $message['sign']
               ];
               //初始化用户之前先退出已登录的该账户用户
                $old_client = Gateway::getClientIdByUid($uid);
                if ($old_client) {
                   $old_message = array(
                       'message_type' => 'quit',
                       'id'           => $uid,
                   );
                  Gateway::sendToClient($old_client[0], json_encode($old_message));
                }
               // 将当前链接与uid绑定
               Gateway::bindUid($client_id, $uid);
               // 通知当前客户端初始化
               $init_message = array(
                   'message_type' => 'init',
                   'id'           => $uid,
               );
               Gateway::sendToClient($client_id, json_encode($init_message));

               //查询最近1周有无需要推送的离线信息
               $db = Db::instance('db');  //数据库链接
               $time = time() - 7 * 3600 * 24;
               $resMsg = $db->select('id,fromid,fromname,fromavatar,timeline,content')->from('ext_chatlog')
                   ->where("toid= {$uid} and timeline > {$time} and type = 'friend' and needsend = 1" )
                   ->query();
               if( !empty( $resMsg ) ){

                   foreach( $resMsg as $key=>$vo ){

                       $log_message = [
                           'message_type' => 'logMessage',
                           'data' => [
                               'username' => $vo['fromname'],
                               'avatar'   => $vo['fromavatar'],
                               'id'       => $vo['fromid'],
                               'type'     => 'friend',
                               'content'  => htmlspecialchars( $vo['content'] ),
                               'timestamp'=> $vo['timeline'] * 1000,
                           ]
                       ];
                       Gateway::sendToUid( $uid, json_encode($log_message) );

                       //设置推送状态为已经推送
                       $db->query("UPDATE `ext_chatlog` SET `needsend` = '0' WHERE id=" . $vo['id']);

                   }
               }

               //查询当前的用户是在哪个分组中,将当前的链接加入该分组
               $ret = $db->query("select `groupid` from `ext_groupdetail` where `userid` = {$uid} group by `groupid`");
               if( !empty( $ret ) ){
                   foreach( $ret as $key=>$vo ){
                       Gateway::joinGroup($client_id, $vo['groupid']);  //将登录用户加入群组
                   }
               }
               unset( $ret );
               return;
               break;
           case 'addUser' :
               //添加用户
               $add_message = [
                   'message_type' => 'addUser',
                   'data' => [
                       'type' => 'friend',
                       'avatar'   => $message['data']['avatar'],
                       'username' => $message['data']['username'],
                       'groupid'  => $message['data']['groupid'],
                       'id'       => $message['data']['id'],
                       'sign'     => $message['data']['sign']
                   ]
               ];
               Gateway::sendToAll( json_encode($add_message), null, $client_id );
               return;
               break;
           case 'delUser' :
               //删除用户
               $del_message = [
                   'message_type' => 'delUser',
                   'data' => [
                       'type' => 'friend',
                       'id'       => $message['data']['id']
                   ]
               ];
               Gateway::sendToAll( json_encode($del_message), null, $client_id );
               return;
               break;
           case 'addGroup':
               //添加群组
               $uids = explode( ',', $message['data']['uids'] );
               $client_id_array = [];
               foreach( $uids as $vo ){
                    $ret = Gateway::getClientIdByUid( $vo );  //当前组中在线的client_id
                    if( !empty( $ret ) ){
                        $client_id_array[] = $ret['0'];

                        Gateway::joinGroup($ret['0'], $message['data']['id']);  //将这些用户加入群组
                    }
               }
               unset( $ret, $uids );
               $add_message = [
                   'message_type' => 'addGroup',
                   'data' => [
                       'type' => 'group',
                       'avatar'   => $message['data']['avatar'],
                       'id'       => $message['data']['id'],
                       'groupname'     => $message['data']['groupname']
                   ]
               ];
               Gateway::sendToAll( json_encode($add_message), $client_id_array, $client_id );
               return;
               break;
           case 'joinGroup':
               //加入群组
               $uid = $message['data']['uid'];
               $ret = Gateway::getClientIdByUid( $uid ); //若在线实时推送
               if( !empty( $ret ) ){
                   Gateway::joinGroup($ret['0'], $message['data']['id']);  //将该用户加入群组

                   $add_message = [
                       'message_type' => 'addGroup',
                       'data' => [
                           'type' => 'group',
                           'avatar'   => $message['data']['avatar'],
                           'id'       => $message['data']['id'],
                           'groupname'     => $message['data']['groupname']
                       ]
                   ];
                   Gateway::sendToAll( json_encode($add_message), [$ret['0']], $client_id );  //推送群组信息
               }

               return;
               break;
           case 'liveState':
               //直播状态
               $video = isset($message['data']['video'])?$message['data']['video']: '';
               $to_id = $message['data']['groupid'];
               if( !empty( $to_id ) ){
                   $chat_message = [
                       'message_type' => 'liveState',
                       'data' => [
                           'state'   => $message['data']['state'],
                           'id'       => $message['data']['groupid'],
                           'url'       => $video,
                       ]
                   ];
                  Gateway::sendToGroup($to_id, json_encode($chat_message), $client_id);//该群在线                   
               }
               return;
               break;

           case 'addMember':
               //添加群组成员
               $uids = explode( ',', $message['data']['uid'] );
               $client_id_array = [];
               foreach( $uids as $vo ){
                   $ret = Gateway::getClientIdByUid( $vo );  //当前组中在线的client_id
                   if( !empty( $ret ) ){
                       $client_id_array[] = $ret['0'];

                       Gateway::joinGroup($ret['0'], $message['data']['id']);  //将这些用户加入群组
                   }
               }
               unset( $ret, $uids );

               $add_message = [
                   'message_type' => 'addGroup',
                   'data' => [
                       'type' => 'group',
                       'avatar'   => $message['data']['avatar'],
                       'id'       => $message['data']['id'],
                       'groupname'     => $message['data']['groupname']
                   ]
               ];
               Gateway::sendToAll( json_encode($add_message), $client_id_array, $client_id );  //推送群组信息
               return;
               break;
           case 'removeMember':
               //将移除群组的成员的群信息移除，并从讨论组移除
               $ret = Gateway::getClientIdByUid( $message['data']['uid'] );
               if( !empty( $ret ) ){

                   Gateway::leaveGroup($ret['0'], $message['data']['id']);

                   $del_message = [
                       'message_type' => 'delGroup',
                       'data' => [
                           'type' => 'group',
                           'id'       => $message['data']['id'],
                           'groupname'       => $message['data']['groupname'],
                       ]
                   ];
                   Gateway::sendToAll( json_encode($del_message), [$ret['0']], $client_id );
               }

               return;
               break;           
            case 'delMember':
               //批量移除群成员
               if ($message['data']['uids'] != '') {
                 $uids = explode( ',', $message['data']['uids'] );
                 $client_id_array = [];
                 foreach( $uids as $vo ){
                      $ret = Gateway::getClientIdByUid( $vo );
                      if( !empty( $ret ) ){
                          $client_id_array[] = $ret['0'];

                          Gateway::leaveGroup($ret['0'], $message['data']['id']);  
                          //将client_id从某个组中删除，不再接收该分组广播(Gateway::sendToGroup)发送的数据。
                      }
                 }
                 unset( $ret, $uids );
                 $del_message = [
                     'message_type' => 'delGroup',
                     'data' => [
                         'type' => 'group',
                         'id'       => $message['data']['id'],
                         'groupname'       => $message['data']['groupname'],
                     ]
                 ];
                 Gateway::sendToAll( json_encode($del_message), $client_id_array, $client_id );
               }
               return;
               break;
           case 'delGroup':
               //删除群组
               $del_message = [
                   'message_type' => 'delGroup',
                   'data' => [
                       'type' => 'group',
                       'id'       => $message['data']['id']
                   ]
               ];
               Gateway::sendToAll( json_encode($del_message), null, $client_id );
               return;
               break;
           case 'chatMessage':
               $db = Db::instance('db');  //数据库链接
               // 聊天消息
               $type = $message['data']['to']['type'];
               $to_id = $message['data']['to']['id'];
               $uid = $_SESSION['id'];
 
               $chat_message = [
                    'message_type' => 'chatMessage',
                    'data' => [
                        'username' => $_SESSION['username'],
                        'avatar'   => $_SESSION['avatar'],
                        'id'       => $type === 'friend' ? $uid : $to_id,
                        'type'     => $type,
                        'content'  => htmlspecialchars($message['data']['mine']['content']),
                        'timestamp'=> time()*1000,
                    ]
               ];
               //聊天记录数组
               $param = [
                   'fromid' => $uid,
                   'toid' => $to_id,
                   'fromname' => $_SESSION['username'],
                   'fromavatar' => $_SESSION['avatar'],
                   'content' => htmlspecialchars($message['data']['mine']['content']),
                   'timeline' => time(),
                   'needsend' => 0
               ];
               switch ($type) {
                   // 私聊
                   case 'friend':
                       // 插入
                       $param['type'] = 'friend';
                       if( empty( Gateway::getClientIdByUid( $to_id ) ) ){
                           $param['needsend'] = 1;  //用户不在线,标记此消息推送
                       }
                       $db->insert('ext_chatlog')->cols( $param )->query();
                       return Gateway::sendToUid($to_id, json_encode($chat_message));
                   // 群聊
                   case 'group':
                       $param['type'] = 'group';
                       $db->insert('ext_chatlog')->cols( $param )->query();
                       print_r($to_id);
                       return Gateway::sendToGroup($to_id, json_encode($chat_message), $client_id);
               }
               return;
               break;
           case 'hide':
           case 'online':
               $status_message = [
                   'message_type' => $message_type,
                   'id'           => $_SESSION['id'],
               ];
               $_SESSION['online'] = $message_type;
               Gateway::sendToAll(json_encode($status_message));
               return;
               break;
           case 'ping':
               return;
           default:
               echo "unknown message $data" . PHP_EOL;
       }
   }
   
   /**
    * 当用户断开连接时触发
    * @param int $client_id 连接id
    */
   public static function onClose($client_id) {
    if ($_SESSION['id']) {
      $logout_message = [
        'message_type' => 'logout',
        'id'           => $_SESSION['id']
      ];
      $db = Db::instance('db');
      $db->query("UPDATE `ext_chatuser` SET `status` = 'outline' WHERE id=" . $_SESSION['id']);
      $client_id = Gateway::getClientIdByUid($_SESSION['id']);
      Gateway::sendToAll(json_encode($logout_message),null,$client_id);
    }

   }
}