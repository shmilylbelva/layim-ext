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
namespace app\admin\model;

use think\Model;

class Groupdetail extends Model
{
    protected $table = 'ext_groupdetail';

    /**
     * 根据搜索条件获去用户列表信息
     * @param $id
     * @param $offset
     * @param $limit
     */
    public function getDetailByWhere($id, $offset, $limit )
    {
        return $this->where('groupid', $id)->limit($offset, $limit)->select();
    }

    /**
     * 根据搜索条件获取所有的用户
     * @param $where
     */
    public function getAllDetail()
    {
        return $this->count();
    }
    /**
     * 查看指定分组中的用户id
     * @param $where
     */
    public function checkUserDetail( $groupid = '' )
    {
        if( !$groupid ){
            $res = db('chatuser')->field('id as userid,username,avatar as useravatar')->select();
        }else{
            $res = $this->field('userid,username,useravatar')->where('groupid = '. $groupid. ' AND state = 1')->select();
        }   
        if( !count($res) ){
            return json_encode([]);
        }

        foreach( $res as $key=>$vo ){
            $ids[$key]['userid'] = $vo['userid'];
            $ids[$key]['username'] = $vo['username'];
            $ids[$key]['useravatar'] = $vo['useravatar'];
        }
        return json_encode($ids);
    }    

    /**
     * 
     * @param $where
     */
    public function editGroupManage( $params )
    {
        $this->where('groupid', $params['groupid'])->delete();
        
        $db = db('chatuser');
        if (!empty($params['check'])) {
            foreach ($params['check'] as $key => $value) {
                $res[$key] = $db->field('id as userid,username, avatar as useravatar ,sign as usersign')->where('id', $value)->find();
                $res[$key]['groupid'] = $params['groupid'];
                $res[$key]['state'] = 1;

            }
            db('groupdetail')->insertAll($res);
        }

        return true;
    }

    /**
     * 移除用户
     * @param $id
     */
    public function removeUser($id)
    {
        return $this->where('userid', $id)->delete();
    }
}