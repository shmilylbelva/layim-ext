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

use app\admin\model\Chatuser;
use app\admin\model\Chatgroup;
use app\admin\model\Groupdetail;

class Layuser extends Base
{
    //laychat用户列表
    public function index()
    {
        if(request()->isAjax()){

            $param = input('param.');

            $limit = $param['pageSize'];
            $offset = ($param['pageNumber'] - 1) * $limit;

            $user = new Chatuser();
            $selectResult = $user->getUserByWhere($offset, $limit);

            $group = config('user_group');

            foreach($selectResult as $key=>$vo){
                $selectResult[$key]['avatar'] = "<img src='".$vo['avatar']."' width='50px' height='50px'>";
                $operate = [
                    '编辑' => "javascript:edit(".$vo['id'].")",
                    '删除' => "javascript:userDel('".$vo['id']."')"
                ];
                $selectResult[$key]['groupid'] = $group[$vo['groupid']];
                $selectResult[$key]['operate'] = showOperate($operate);

            }

            $return['total'] = $user->getAllUser();  //总数据
            $return['rows'] = $selectResult;

            return json($return);
        }

        return $this->fetch();
    }

    //添加用户
    public function userAdd()
    {
        $add_data = '';
        if(request()->isPost()){

            $param = input('post.');

            $user = new Chatuser();
            if ( empty($param['username']) ){
                return json( ['code' => -1, 'data' => '', 'msg' => '用户名不能为空'] );
            }

            if ( empty($param['groupid']) ){
                $param['groupid'] = 1;
                // return json( ['code' => -2, 'data' => '', 'msg' => '所属分组不能为空'] );
            }

            if ( empty($param['pwd']) ){
                return json( ['code' => -3, 'data' => '', 'msg' => '登录密码不能为空'] );
            }

            if ( empty($param['sign']) ){
                return json( ['code' => -4, 'data' => '', 'msg' => '个性签名不能为空'] );
            }

            $has = $user->checkName( $param['username'] );
            if ( !empty( $has ) ) {
                return json( ['code' => -5, 'data' => '', 'msg' => '用户名重复'] );
            }

            $this->_getUpFile( $param );  //处理上传图片

            $param['pwd'] = md5( $param['pwd'] );
            $param['status'] = 'outline';

            $flag = $user->insertUser( $param );

            if( 1 != $flag['code'] ){
                return json( ['code' => -6, 'data' => '', 'msg' => '添加用户失败'] );
            }

            //socket data
            $add_data = '{"type":"addUser", "data" : {"avatar":"' . $param['avatar'] . '","username":"' . $param['username'] . '",';
            $add_data .= '"groupid":"' . $param['groupid'] . '", "id":"' . $flag['data'] . '","sign":"' . $param['sign'] . '"}}';

            return json( ['code' => 1, 'data' => $add_data, 'msg' => '添加用户成功'] );
        }

        $this->assign([
            'group' => config('user_group'),
            'add_data' => $add_data
        ]);
        return $this->fetch();
    }

    //编辑用户
    public function userEdit()
    {
        $user = new Chatuser();
        if( request()->isPost() ){

            $param = input('post.');

            if ( empty($param['username']) ){
                return json( ['code' => -1, 'data' => '', 'msg' => '用户名不能为空'] );
            }

            if ( empty($param['groupid']) ){
                $param['groupid'] = 1;
                // return json( ['code' => -2, 'data' => '', 'msg' => '所属分组不能为空'] );
            }

            if ( empty($param['sign']) ){
                return json( ['code' => -3, 'data' => '', 'msg' => '个性签名不能为空'] );
            }

            $has = $user->checkNameEdit( $param['username'], $param['id'] );
            if ( !empty( $has ) ) {
                return json( ['code' => -4, 'data' => '', 'msg' => '您修改后的用户名已经存在'] );
            }

            $this->_getUpFile( $param );  //处理上传头像
            //处理密码问题
            if( empty( $param['pwd'] ) ){
                unset( $param['pwd'] );
            }else{
                $param['pwd'] = md5( $param['pwd'] );
            }

            $flag = $user->editUser( $param );
            if( 0 == $flag['code'] ){
                return json( ['code' => -5, 'data' => '', 'msg' => '编辑用户失败'] );
            }

            return json( ['code' => 1, 'data' => '', 'msg' => '编辑用户成功'] );
        }

        $id = input('param.id');

        $this->assign([
            'user' => $user->getOneUser($id),
            'group' => config('user_group')
        ]);
        return $this->fetch();
    }

    //删除用户
    public function userDel()
    {
        $id = input('param.id');

        $user = new Chatuser();
        $flag = $user->delUser($id);

        return json(['code' => $flag['code'], 'data' => '', 'msg' => $flag['msg']]);
    }


    //laychat群列表
    public function group()
    {
        if(request()->isAjax()){

            $param = input('param.');

            $limit = $param['pageSize'];
            $offset = ($param['pageNumber'] - 1) * $limit;

            $user = new Chatgroup();
            $selectResult = $user->getGroupByWhere($offset, $limit);

            foreach($selectResult as $key=>$vo){
                $selectResult[$key]['avatar'] = "<img src='".$vo['avatar']."' width='50px' height='50px'>";
                $state = '';
                if ($vo['state'] == 1) {
                    $state = 'checked';
                }
                $selectResult[$key]['state'] = "<input type='checkbox' id='".$vo['id']."' class='js-switch'  ".$state." />";
                $operate = [
                    '编辑' => "javascript:edit(".$vo['id'].")",
                    '删除' => "javascript:userDel('".$vo['id']."')"
                ];
                $edit = [
                    '群员管理' => "javascript:manage(".$vo['id'].")"
                ];                
                $selectResult[$key]['operate'] = showOperate($operate);
                $selectResult[$key]['edit'] = showOperate2($edit);

            }

            $return['total'] = $user->getAllGroup();  //总数据
            $return['rows'] = $selectResult;

            return json($return);
        }

        return $this->fetch();
    }

    public function switchState(){
        $groupid = input('post.groupid');
        $state = input('post.state');
        $state = $state == 'true' ? 1:-1;
        db('chatgroup')->where('id',$groupid)->update(['state'=>$state]);
        
        if ($state == 1) {
            $groupInfo = db('chatgroup')->field('video')->where('id',$groupid)->find();
            $add_data = '{"type":"liveState", "data" : {"groupid":"' . $groupid . '","state":"' . $state .'","video":"'.$groupInfo['video'].'"}}';
        }else{
            $add_data = '{"type":"liveState", "data" : {"groupid":"' . $groupid. '","state":"' . $state .'"}}';
        }
        return json( ['code' => 1, 'data' => $add_data, 'msg' => '编辑成功'] );

    }

    //添加用户
    public function groupAdd()
    {
        $add_data = '';
        if(request()->isPost()){

            $param = input('post.');

            $user = new Chatgroup();
            if ( empty($param['groupname']) ){
                return json( ['code' => -1, 'data' => '', 'msg' => '群名不能为空'] );
            }

            if ( empty($param['video']) ){
                return json( ['code' => -1, 'data' => '', 'msg' => '直播源不能为空'] );
            }
            $has = $user->checkName( $param['groupname'] );
            if ( !empty( $has ) ) {
                return json( ['code' => -5, 'data' => '', 'msg' => '群名重复'] );
            }

            $this->_getUpFile( $param );  //处理上传图片

            $flag = $user->insertGroup( $param );

            if( 1 != $flag['code'] ){
                return json( ['code' => -6, 'data' => '', 'msg' => '添加群失败'] );
            }

            //socket data
            $add_data = '{"type":"addGroup", "data" : {"avatar":"' . $param['avatar'] . '","groupname":"' . $param['groupname'] . '",';
            $add_data .= '"id":"' . $flag['data'] . '"}}';
            return json( ['code' => 1, 'data' => $add_data, 'msg' => '添加群成功'] );
        }

        $this->assign([
            'add_data' => $add_data
        ]);
        return $this->fetch();
    }

    //编辑群
    public function groupEdit()
    {
        $user = new Chatgroup();
        $id = input('param.id');
        $groupInfo = $user->getOneGroup($id);
        if( request()->isPost() ){

            $param = input('post.');

            if ( empty($param['groupname']) ){
                return json( ['code' => -1, 'data' => '', 'msg' => '群名不能为空'] );
            }

            if ( empty($param['video']) ){
                return json( ['code' => -1, 'data' => '', 'msg' => '直播源不能为空'] );
            }
            $has = $user->checkNameEdit( $param['groupname'], $param['id'] );
            if ( !empty( $has ) ) {
                return json( ['code' => -4, 'data' => '', 'msg' => '您修改后的群名已经存在'] );
            }

            $this->_getUpFile( $param );  //处理上传头像

            $flag = $user->editGroup( $param );
            if( 0 == $flag['code'] ){
                return json( ['code' => -5, 'data' => '', 'msg' => '编辑失败'] );
            }

            $add_data = '';
            if ($param['state'] != $groupInfo['state']) {
                if ($param['state'] == 1) {
                    $add_data = '{"type":"liveState", "data" : {"groupid":"' . $param['id'] . '","state":"' . $param['state'] .'","video":"'.$groupInfo['video'].'"}}';
                }else{
                    $add_data = '{"type":"liveState", "data" : {"groupid":"' . $param['id'] . '","state":"' . $param['state'] .'"}}';
                }
            }

            return json( ['code' => 1, 'data' => $add_data, 'msg' => '编辑成功'] );
        }

        

        $this->assign([
            'group' => $groupInfo
        ]);
        return $this->fetch();
    }

    //删除用户
    public function groupDel()
    {
        $id = input('param.id');

        $user = new Chatgroup();
        $flag = $user->delGroup($id);

        return json(['code' => $flag['code'], 'data' => '', 'msg' => $flag['msg']]);
    }

    //群员管理
    public function groupManage()
    {
        $user = new Groupdetail();

        if( request()->isPost() ){
            $param = input('post.');
            $param['check'] = !empty($param['check'])? $param['check']: [];
            $oldGroup = $user->checkUserDetail($param['groupid']);//old群成员  1234
            // if (!empty($param['check'])) {
                $flag = $user->editGroupManage( $param );//new群成员   2345
                if( !$flag ){
                    return json( ['code' => -5, 'data' => '', 'msg' => '编辑失败'] );
                }
            // }
            if ($oldGroup != '[]') {
                $group_decode = json_decode($oldGroup,true);
                foreach ($group_decode as $key => $value) {
                    $group[] = $value['userid'];
                } 
                if (!empty($param['check'])) {
                    $toast = array_diff($group,$param['check']);
                }else{
                    $toast = $group;
                }
                
            }else{
                $toast = $param['check'];
            }
            $uids = implode(',',$toast);           
            $groupinfo = db('chatgroup')->field('avatar,groupname')->where('id',$param['groupid'])->find();
            //socket data
            $add_data = '{"type":"delMember", "data" : {"avatar":"' . $groupinfo['avatar'] . '","groupname":"' . $groupinfo['groupname'] . '",';
            $add_data .= '"id":"' . $param['groupid'] . '","uids":"'.$uids.'"}}';
            return json( ['code' => 1, 'data' => $add_data, 'msg' => '编辑成功'] );
        }
        $id = input('param.id');
        $this->assign([
            'group' => $user->checkUserDetail($id),
            'allUser' => $user->checkUserDetail(),
            'groupid' => $id,
        ]);
        return $this->fetch();
    }

    public function apply()
    {
        if(request()->isAjax()){

            $param = input('param.');

            $limit = $param['pageSize'];
            $offset = ($param['pageNumber'] - 1) * $limit;

            $selectResult = db('groupdetail')->where('state',0)->limit($offset, $limit)->select();
            $db = db('chatgroup');
            foreach($selectResult as $key=>$vo){
                $selectResult[$key]['avatar'] = "<img src='".$vo['useravatar']."' width='50px' height='50px'>";
                $groupname = $db->field('groupname')->where('id',$vo['groupid'])->find();
                $selectResult[$key]['operate'] = showButton($vo['userid'],$vo['groupid']);
                $selectResult[$key]['groupname'] = $groupname['groupname'];
                $selectResult[$key]['groupid'] = $vo['groupid'];
            }

            $return['total'] = db('groupdetail')->where('state',0)->count();  //总数据
            $return['rows'] = $selectResult;

            return json($return);
        }

        return $this->fetch();
    }

    public function changeState(){
        $userid = input('param.userid');
        $groupid = input('param.groupid');
        if ($userid && $groupid) {
            db('groupdetail')->where('userid',$userid)->where('groupid',$groupid)->update(['state'=>1,'add_time'=>time()]);
            $group = db('chatgroup')->field('groupname,avatar')->where('id',$groupid)->find();
            $return = [
                'code' => 1,
                'msg'=> '审核成功',
                'data' => [
                    'userid' => $userid,
                    'groupid' => $groupid,
                    'groupname' => $group['groupname'],
                    'groupavatar' => $group['avatar'],
                    // 'code' => $this->code,
                ],
            ];
        }else{
            $return = [
                'code' => -1,
                'msg'=> '参数错误',
                'data' => [],
            ]; 
        }


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
                $param['avatar'] =  '/uploads' . '/' . date('Ymd') . '/' . $info->getFilename();
            }else{
                // 上传失败获取错误信息
                echo $file->getError();
            }
        }else{
            unset( $param['avatar'] );
        }

    }
}