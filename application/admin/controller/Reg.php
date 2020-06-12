<?php
// +----------------------------------------------------------------------
// | snake
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2022 http://baiyf.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author:     administrator
// +----------------------------------------------------------------------
namespace app\admin\controller;


class Reg extends Base
{
    //用户列表
    public function index()
    {
        if(request()->isAjax()){

            $param = input('param.');
            db('config')->where('vdata', 1)->update(['vdata' => '-1']);
            foreach($param as $key=>$vo){
                $result = db('config')->where('kdata', $key)->update(['vdata' => 1]);
            }

                return ['code' => 1, 'data' => '', 'msg' => 'success'];
        }

		$data = db('config')->where('kdata','regPermission')->find();
		$this->assign([
			'isReg' => $data
		]);
        return $this->fetch();
    }

}