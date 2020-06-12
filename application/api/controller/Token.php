<?php
/**
 * Created by PhpStorm.
 * User: sxj
 * Date: 2019/3/3
 * Time: 15:31
 */

namespace app\api\controller;


use Firebase\JWT\JWT;
use think\Controller;
use think\Db;

class Token extends Controller
{
    //密钥,用于生成token 请不要随意更改
    protected $key = '1gHuiop975cdashyex9Ud23ldsvm2Xq';
    //密钥，用于生成refresh_token
    protected $refreshKey = 'QWERTYUIOP';
    //token过期时间2小时
    protected $expireTime = 7200;
    //refresh_token过期时间30天
    protected $refreshExpireTime = 60 * 60 * 12 * 30;

    public function authorizations()
    {
        //判断请求类型refresh_token 还是 password 或者短信验证码登陆
        $grantType = input('param.grant_type');
        $request = input('param.');
        if (empty($grantType)) {
            return self::returnMsg(-1, 'grant_type未传递', []);
        }
        switch ($grantType) {
            case 'password':
                //用户名密码形式登陆，获取token,必传参数：phone，password
                $this->chechUser($request);
            case 'refresh_token':
                //刷新token，返回token和refresh_token,必传参数：refresh_token
                $this->refreshToken(input('param.refresh_token'));
            case 'code':
                //短信验证码形式
            default:
                return self::returnMsg(-1, '缺少参数', []);
        }
    }


    /**
     * 验证用户信息，并生成token
     */
    public function chechUser($data)
    {
        //参数校验
        $userInfo = [
            'username' => $data['username'],
            'password' => $data['password'],
        ];

        $validate = new \app\api\validate\Login;
                

        if (!$validate->check($userInfo)) {
            return self::returnMsg(-1, $validate->getError(), []);
        }
        //1、判断数据库中有没有这个phone,没有就去注册
        $res = db('user')->where('username',$userInfo['username'])->where('typeid',1)->find();
        if (!$res) {
            return $this->returnMsg(-1, '用户不存在', []);
        }

        //2、判断用户名和密码是否正确
        if (md5($userInfo['password']) != $res['password']) {
            return $this->returnMsg(-1, '用户密码错误', []);
        }

        //验证成功，返回token
        $data = ['userid' => $res['id'], 'username' => $res['username']];
        //生成普通token,过期时间是7200s，2小时
        $token = $this->createToken($this->key, $this->expireTime, $data);
        //生成刷新的token，过期时间是15天
        $refreshToken = $this->createToken($this->refreshKey, $this->refreshExpireTime, $data);
        $data = [
            'token' => $token,
            'expire' => time() + $this->expireTime,
            'refresh_token' => $refreshToken,
            'refresh_expire' => time() + $this->refreshExpireTime
        ];
        return $this->returnMsg(0, 'success', $data);
    }

    /**
     * 生成token/refresh_token
     * @param $setKey 加密密钥
     * @param int $expTime 过期时间，默认2小时
     * @param array $data 用户相关信息
     */
    public function createToken($setKey, $expTime = 7200, $data = [])
    {
        $time = time(); //当前时间
        $token = [
            'iss' => 'http://www.wumingxiaozu.com', //签发者 可选
            'aud' => 'http://www.wumingxiaozu.com', //接收该JWT的一方，可选
            'iat' => $time, //签发时间
            'nbf' => $time, //(Not Before)：某个时间点后才能访问，比如设置time+30，表示当前时间30秒后才能使用
            'exp' => $time + $expTime, //过期时间,这里设置2个小时
            'data' => $data
        ];
        return JWT::encode($token, $setKey); //输出Token
    }


    /**
     * 验证token，将对应token解密
     * @param $setKey 密钥
     * @param $token jwt生成的token
     * @return mixed
     */
    public function varification($setKey, $token)
    {
        try {
                JWT::$leeway = 60;//当前时间减去60，把时间留点余地
                $decoded = JWT::decode($token, $setKey, ['HS256']); //HS256方式，这里要和签发的时候对应
                return (array)$decoded;
                 
            } catch(\Firebase\JWT\SignatureInvalidException $e) {  //签名不正确
              echo $e->getMessage();
            }catch(\Firebase\JWT\BeforeValidException $e) {  // 签名在某个时间点之后才能用
              echo $e->getMessage();
            }catch(\Firebase\JWT\ExpiredException $e) {  // token过期
              echo $e->getMessage();
          }catch(Exception $e) {  //其他错误
              echo $e->getMessage();
        }

        // JWT::$leeway = 60;//将时间留点余地。当前时间减去60
        // $decoded = JWT::decode($token, $setKey, ['HS256']);
        // $arr = (array)$decoded;
        // return $arr;
    }

    /**
     * 刷新token，获取新的token和refresh_token
     * @param $refreshToken 旧的refresh_token
     */
    public function refreshToken($refreshToken)
    {
        if (empty($refreshToken)) {
            return $this->returnMsg(-1, '缺少参数refresh_token', []);
        }
        //旧的refresh_token解密
        $oldRefreshTokenInfo = $this->varification($this->refreshKey, $refreshToken);
        if ($oldRefreshTokenInfo['exp'] < time()) {
            return $this->returnMsg(-2, '请重新登陆', []);
        }

        //生成新的token
        $token = $this->createToken($this->key, $this->expireTime, $oldRefreshTokenInfo['data']);
        //生成新的refresh_token
        // $refreshToken = $this->createToken($this->refreshKey, $this->refreshExpireTime, $oldRefreshTokenInfo['data']);
        $data = [
            'token' => $token,
            'expire' => time() + $this->expireTime,
            // 'refresh_token' => $refreshToken,
            // 'refresh_expire' => time() + $this->refreshExpireTime
        ];
        return $this->returnMsg(0, 'success', $data);
    }


    /**
     * 返回成功
     */
    protected function returnMsg($code = 200,$message = '',$data = [],$header = [])
    {

        $return['code'] = (int)$code;
        $return['message'] = $message;
        $return['data'] = is_array($data) ? $data : ['info'=>$data];
        // 发送头部信息
            
        
        foreach ($header as $name => $val) {
            if (is_null($val)) {
                header($name);
            } else {
                header($name . ':' . $val);
            }
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

}