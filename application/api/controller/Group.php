<?php
/**
 * Created by PhpStorm.
 * User: sxj
 * Date: 2019/3/2
 * Time: 16:41
 */

namespace app\api\controller;


use Firebase\JWT\JWT;
use think\Controller;
use think\facade\Log;
use think\Request;
use \GatewayWorker\Lib\Gateway;

class Group extends Controller
{

    protected $beforeActionList = ['handle'];

    protected $uinfo = [];
    protected $registerAddress = '10.0.2.219:1237';

    //密钥,请不要随意更改
    protected $key = '1gHuiop975cdashyex9Ud23ldsvm2Xq';

    //使用中间件验证用户token是否过期，对应api2中的jwt
    public function handle()
    {
        //$jwt = Request::instance()->header('token');
        try {
            //获取header中的加密信息
            $jwt = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : '';
            if (empty($jwt)) {
                echo json_encode(['code' => -1, 'msg' => "You do not have permission to access.", 'data' => []]);
                die;
            }
            
            $token = explode(' ',$jwt);
            JWT::$leeway = 60;//将时间留点余地。当前时间减去60
            $decoded = JWT::decode($token['1'], $this->key, ['HS256']);
            
            $this->uinfo = $decoded;
            //$arr = (array)$decoded;
            //定义一个header header("token: 66666");
        } catch (\Exception $e) {
            echo json_encode(['code' => -1, 'msg' => $e->getMessage(), 'data' => []]);
            die;
        }

    }

    /**
     * group list
     */
    public function getGroupList(Request $request)
    {
        $data = $request->param();
        $info = db('chatgroup')->field('id,groupname,avatar,state')->select();
        return json( ['code' => 0, 'data' => $info, 'msg' => 'success' ] );
    }

    /**
     * group detail info
     */
    public function getGroupDetail(Request $request)
    {
        $groupid = $request->param('id');
        $pageSize = $request->param('pageSize');
        $pageSize = ($pageSize > 0 && $pageSize < 101)?(int)$pageSize:10;
        $info = db('groupdetail')->paginate($pageSize);
        return json( ['code' => 0, 'data' => $info, 'msg' => 'success' ] );
    }

    /**
     * get group chat message
     */
    public function getGroupMsg(Request $request)
    {
        $groupid = $request->param('id');
        $pageSize = $request->param('pageSize');
        $pageSize = ($pageSize > 0 && $pageSize < 101)?(int)$pageSize:10;
        if($groupid){
            $info = db('chatlog')->field('fromid,fromname,content,timeline')->where('type', 'group')->where('toid',$groupid)->order('id desc')->paginate($pageSize);
            return json( ['code' => 0, 'data' => $info, 'msg' => 'success' ] );
        }else{
            return json( ['code' => -1, 'data' => '', 'msg' => 'params is null' ] );
        }        

    }

    /**
     * send group chat imageMessage
     */
    public function postGroupImage(Request $request)
    {
        $groupid = $request->param('id');
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('image');
        if($file && $groupid){
            // 移动到框架应用根目录/public/uploads/ 目录下
            $info = $file->validate(['size'=>41943040,'ext'=>'jpg,png,gif'])->move(ROOT_PATH . 'public' . DS . 'uploads');
            if($info){
                $content = '/uploads/'.date('Ymd').'/'.$info->getFilename();
                $this->sendMsg($groupid,'img['.$content.']');                
                $data = [
                    'fromname' => '系统',
                    'fromid' => '0',
                    'fromavatar' => '/uploads/admin.png',
                    'toid' => $groupid,
                    'content' => $content,
                    'timeline' => time(),
                    'type' => 'group',                    
                    'needsend' => '0',                    
                ];
                db('chatlog')->insert($data); 
                return json( ['code' => 0, 'data' => $data, 'msg' => 'upload success' ] );                    
                // 成功上传后 获取上传信息
            }else{
                // 上传失败获取错误信息
                echo $file->getError();
                return json( ['code' => -2, 'data' => ' ', 'msg' => $file->getError() ] );
            }               
        }else{
            return json( ['code' => -3, 'data' => '', 'msg' => 'params is null' ] );
        }

    }

    /**
     * send group chat message
     */
    public function postGroupMsg(Request $request)
    {
        $groupid = $request->param('id');
        $msg = $request->param('msg');
        if($msg && $groupid){
            $this->sendMsg($groupid,$msg);
            $data = [
                'fromname' => '系统',
                'fromid' => '0',
                'fromavatar' => '/uploads/admin.png',
                'toid' => $groupid,
                'content' => $msg,
                'timeline' => time(),
                'type' => 'group',
                'needsend' => '1',
            ];
            db('chatlog')->insert($data); 
            return json( ['code' => 0, 'data' => $data, 'msg' => 'send success' ] );                    
            // 成功上传后 获取上传信息              
        }else{
            return json( ['code' => -3, 'data' => '', 'msg' => 'params is null' ] );
        }

    }


    protected function sendMsg($groupid,$msg){
        // 设置GatewayWorker服务的Register服务ip和端口，请根据实际情况改成实际值(ip不能是0.0.0.0)
        Gateway::$registerAddress = $this->registerAddress;
        $chat_message = [
            'message_type' => 'chatMessage',
            'data' => [
                'username' => '系统',
                'avatar'   => '/uploads/admin.png',
                'id'       => $groupid,
                'type'     => 'group',
                'content'  => htmlspecialchars($msg),
                'timestamp'=> time()*1000,
            ]
        ];
        // 向任意群组的网站页面发送数据
        Gateway::sendToGroup($groupid, json_encode($chat_message));        
    }

}