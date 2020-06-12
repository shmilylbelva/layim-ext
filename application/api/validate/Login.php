<?php
/**
 * Created by PhpStorm.
 * User: sxj
 * Date: 2019/3/3
 * Time: 17:26
 */

namespace app\api\validate;


use think\Validate;

class Login extends Validate
{
    protected $rule = [
        'username'      => 'require',
        'password'     => 'require|min:5',
    ];

    protected $message = [
        'username.require'     => '用户名不能为空',
        'password.require'  => '密码不能为空',
        'password.min'      => '密码最少5位',
    ];
}