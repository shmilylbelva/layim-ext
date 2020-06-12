<?php
// +----------------------------------------------------------------------
// | layerIM + layimext + ThinkPHP5 即时通讯
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2012 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author:     administrator
// +----------------------------------------------------------------------
namespace app\index\controller;

use think\Controller;

class Login extends Controller
{
    public function index()
    {      
        if(cookie( 'uid') && cookie( 'username')) $this->redirect(url('index/index'));
        $this->assign([
            'isReg' => db('config')->field('vdata')->where('kdata', 'regPermission')->find()
        ]);        
        return $this->fetch();
    }
    
    public function doLogin()
    {
    	$uname = input('param.username');
    	$userinfo = db('chatuser')->where('username', $uname)->find();

    	if( empty($userinfo) ){
    		$this->error("用户不存在");
    	}

        $pwd = input('param.pwd');
		if( md5($pwd) != $userinfo['pwd'] ){
            $this->error("密码不正确");
        }
        $str = $userinfo['id'].'-'.time();
        $key = 'shmily@laykefu';
        $code = authcode($str,'ENCODE',$key,0); //加密 
    	//设置为登录状态
    	db('chatuser')->where('username', $uname)->update(['status' =>'online','code' => $code]);
    	
    	session( 'uid', $userinfo['id'] );
    	cookie( 'username', $userinfo['username'] );
        cookie( 'avatar', $userinfo['avatar'] );
        cookie( 'sign', $userinfo['sign'] );

    	$this->redirect(url('/'));
    }

    public function reg()
    {
        return $this->fetch();
    }    
    
    public function doreg()
    {
        $isReg = db('config')->field('vdata')->where('kdata', 'regPermission')->find();
        if($isReg['vdata'] != 1) {
            $return = [
                'code' => -1,
                'msg'=> '非法请求',
                'data' => [],
            ];
            return json( $return );
        }
        $uname = input('param.username');
        $avatar = input('param.avatar');
        $userinfo = db('chatuser')->where('username', $uname)->find();

        if( !empty($userinfo) ){
            $return = [
                'code' => -1,
                'msg'=> '用户已存在',
                'data' => [],
            ];

            return json( $return );            
        }

        $pwd = input('param.pwd');
        if( !$pwd ){
            $return = [
                'code' => -1,
                'msg'=> '密码不为空',
                'data' => [],
            ];

            return json( $return );             
        }
        if ($avatar) {
            $avatar = $this->_getUpFile( $avatar );  //处理上传图片
        }else{
            $avatar = config('uimage');  //默认图片
        }
        
        $data = [
            'username' => $uname,
            'pwd' =>md5($pwd),
            'status' =>'online',
            'avatar' => $avatar,
        ];
         db('chatuser')->insert($data);
         $uid = db('chatuser')->getLastInsID();
        $uinfo = db('chatuser')->where('id', $uid)->find();
        session( 'uid', $uinfo['id'] );
        // cookie( 'username', $uinfo['username'] );
        // cookie( 'avatar', $uinfo['avatar'] );
        // cookie( 'sign', $uinfo['sign'] );
        $return = [
            'code' => 0,
            'msg'=> '注册成功',
            'data' => [],
        ];

        return json( $return );
    }   

    /**
     * 上传图片方法
     * @param $param
     */
    private function _getUpFile(&$param)
    {
        // 获取表单上传文件
        $file = request()->file('avatar');

        // 移动到框架应用根目录/public/uploads/ 目录下
        if( !is_null( $file ) ){

            $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
            if($info){
                // 成功上传后 获取上传信息
                $avatar =  '/uploads' . '/' . date('Ymd') . '/' . $info->getFilename();
                return $avatar;
            }else{
                // 上传失败获取错误信息
                echo $file->getError();
            }
        }else{
            unset( $param['avatar'] );
        }

    }     
}
