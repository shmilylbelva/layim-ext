<?php

namespace app\api\behavior;

class JWTAuth
{
    public function run()
    {
        if('user/0'==request()->url()){
            return false;
        }
    }
}

use Firebase\JWT\JWT;
use think\facade\Log;

class JWTAuth
{
    //密钥,用于生成token 请不要随意更改
    protected $key = '1gHuiop975cdashyex9Ud23ldsvm2Xq';

    //使用中间件验证用户token是否过期，对应api2中的jwt
    public function handle($request, \Closure $next)
    {
        //$jwt = Request::instance()->header('token');
        try {
            //获取header中的加密信息
            $jwt = isset($_SERVER['HTTP_X_TOKEN']) ? $_SERVER['HTTP_X_TOKEN'] : '';
            Log::info($jwt);

            if (empty($jwt)) {
                echo json_encode(['code' => -1, 'msg' => "You do not have permission to access.", 'data' => []]);
                die;
            }
            JWT::$leeway = 60;//将时间留点余地。当前时间减去60
            $decoded = JWT::decode($jwt, $this->key, ['HS256']);
            //$arr = (array)$decoded;
            //定义一个header header("token: 66666");
        } catch (\Exception $e) {
            echo json_encode(['code' => -1, 'msg' => $e->getMessage(), 'data' => []]);
            die;
        }


        return $next($request);
    }

}