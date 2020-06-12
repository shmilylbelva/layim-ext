<?php

use think\Route;
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

Route::post('api/authorizations', 'api/token/authorizations');//获取token

//以下内容需要走中间件进行token验证
Route::group('api', function () {
    Route::get('group', 'api/group/getGroupList');
    Route::get('detail', 'api/group/getGroupDetail');
    Route::get('getmsg', 'api/group/getGroupMsg');
    Route::post('msg', 'api/group/postGroupMsg');
    Route::post('image', 'api/group/postGroupImage');
});