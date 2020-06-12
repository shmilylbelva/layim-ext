<?php
// +----------------------------------------------------------------------
// | layerIM + workerman-for-win + ThinkPHP5 即时通讯
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2012 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: NickBai <1902822973@qq.com>
// +----------------------------------------------------------------------
namespace app\index\controller;

use think\Controller;

class Index extends Controller
{
	public function _initialize()
	{
        // if (!is_mobile_request()) {
        //     print_r('error');
        //     return false;
        // }
		if( empty(session('uid')) ){
			$this->redirect( url('index/login/index') );
		}
	}
	
    public function index()
    {
        $mine = db('chatuser')->field('id,code,avatar,username,sign')->where('id', session('uid'))->find();
        $this->assign([
            'data' => $mine
        ]);         
        return $this->fetch();
    }    

    public function getUinfo()
    {
        $mine = db('chatuser')->field('id,code,avatar,username,sign')->where('id', session('uid'))->find();
        $json_data = [
            'code' => 0,
            'msg'=> '',
            'data' => $mine,                     
        ];
        return json($json_data);
    }

    //获取列表
    public function getList()
    {
    	//查询自己的信息
    	$mine = db('chatuser')->where('id', session('uid'))->find();
        //查询当前用户的所处的群组
        $groupArr = [];
        $groups = db('groupdetail')->field('groupid')->where('userid ='.session('uid').' AND state = 1')->group('groupid')->select();
        if( !empty( $groups ) ){
            foreach( $groups as $key=>$vo ){
                $ret = db('chatgroup')->field('id,groupname,avatar,group_no')->where('id', $vo['groupid'])->find();
                if( !empty( $ret ) ){
                    $groupArr[] = $ret;
                }
            }
        }
        unset( $ret, $groups );

        $online = 0;
        $group = [];  //记录分组信息
        $userGroup = config('user_group');
        $list = [];  //群组成员信息
        $i = 0;
        $j = 0;
        $friend = array();
        foreach( $userGroup as $key=>$vo ){
            $friend[0] = [
                'groupname' => $vo,
                'id' => $key,
                'online' => 0,
                'list' => []
            ];
            $i++;
        }
        unset( $userGroup );
        $friends1 = db('friend')->alias('f')->field('c.id,c.username,c.avatar,c.sign')->join('chatuser c','c.id = f.friendid')->where('f.userid ='.session('uid'))->select();
        $friends2 = db('friend')->alias('f')->field('c.id,c.username,c.avatar,c.sign')->join('chatuser c','c.id = f.userid')->where('f.friendid ='.session('uid'))->select();
        
        if (!empty($friends1) || !empty($friends2)) {
            $friend[0]['list'] = array_merge($friends1,$friends2);
        }	
        $return = [
       		'code' => 0,
       		'msg'=> '',
       		'data' => [
       			'mine' => [
	       				'username' => $mine['username'],
	       				'id' => $mine['id'],
	       				'status' => 'online',
       					'sign' => $mine['sign'],
       					'avatar' => $mine['avatar']	
       			],
       			'friend' => $friend,
				'group' => $groupArr
       		],
        ];

    	return json( $return );

    }
    //获取消息盒子列表
    public function msgbox()
    {
        $page = input('param.page');
        $page = ($page-1)*10;
        $userid = input('param.userid');
        $chatuser = db('chatuser');
        $chatgroup = db('chatgroup');
        if ($userid == session('uid')) {
            $grupid_arr = db('groupdetail')->where('userid',$userid)->where('role = 2 OR role = 1')->column('groupid');//所管理的群id
            $grupids = implode(',', $grupid_arr);
            $msg = db('msgbox')->where('(toid = '.$userid .' AND type = "friend") OR fromid = '.$userid." OR (find_in_set(toid, '".$grupids."') AND type = 'group') ")->limit($page.',10')->order('status')->select();
            $count = db('msgbox')->where('(toid = '.$userid .' AND type = "friend") OR fromid = '.$userid." OR (find_in_set(toid, '".$grupids."') AND type = 'group') ")->count();
            // $msg = db('msgbox')->where('((fromid = '.$userid ." OR find_in_set(toid, '".$grupids."')) AND type = 'group' ) OR ( (fromid = ".$userid." OR toid = ".$userid.") AND type = 'friend')")->select();
            if (!$msg) {
                $json_data = [
                    'code' => 0,
                    'msg' => '暂无数据',
                    'data' => []
                ];
                return json($json_data);
            }
            foreach ($msg as $key => $value) {
                if ($value['toid'] == $userid &&  $value['type'] == 'friend') {//接收加好友请求
                    $msgbox[$key] = $chatuser->field('username as name,id,avatar')->where('id',$value['fromid'])->find();
                    $msgbox[$key]['message'] = '发送加好友请求';
                    $msgbox[$key]['type'] = '2';
                }elseif($value['fromid'] == $userid &&  $value['type'] == 'friend'){//发送加好友请求
                    $msgbox[$key] = $chatuser->field('username as name,id,avatar')->where('id',$value['toid'])->find();
                    $msgbox[$key]['message'] = '接收加好友请求';
                    $msgbox[$key]['type'] = '1';
                }elseif ($value['fromid'] == $userid &&  $value['type'] == 'group') {//发送加群请求
                    $msgbox[$key] = $chatgroup->field('groupname as name,id,avatar,group_no as no')->where('id',$value['toid'])->find();
                    $msgbox[$key]['message'] = '发送加群请求';
                    $msgbox[$key]['type'] = '3';
                }elseif (in_array($value['toid'],$grupid_arr) &&  $value['type'] == 'group') {//接收加群请求(群主或者管理员)
                    $msgbox[$key] = $chatuser->field('username as name,id ,avatar')->where('id',$value['fromid'])->find();
                    $chatgroup_arr = $chatgroup->field('groupname ,id as groupid')->where('id',$value['toid'])->find();
                    $msgbox[$key]['groupname'] = $chatgroup_arr['groupname'];
                    $msgbox[$key]['groupid'] = $chatgroup_arr['groupid'];
                    $msgbox[$key]['message'] = '接收加群请求';
                    $msgbox[$key]['type'] = '4';
                }
                $msgbox[$key]['content'] = $value['content'];
                $msgbox[$key]['status'] = $value['status'];
            }
            $json_data = [
                'code' => 0,
                'msg' => '查询成功',
                'data' => $msgbox,
                'pages'=>ceil($count/10)
            ];
        }else{
            $json_data = [
                'code' => -1,
                'msg' => '非法获取',
                'data' => []
            ];
        }

        return json($json_data);
    }

    public function find(){
        $userid = input('param.userid');
        $search = input('param.search');
        $type = input('param.type');
        if ($userid == session('uid')) {
            if ($type == 'friend') {
                $friend = db('friend');
                $chatuser = db('chatuser');
                $userinfo = $chatuser->field('id,no,username,status,sign,avatar,addtime')->where('username = "'.$search.'" OR no = "'.$search.'"')->find();
                if (!$userinfo) {//不存在该用户
                    $json_data = [
                        'code' => 1,
                        'msg' => '用户不存在',
                        'data' => []
                    ];
                    return json($json_data);
                }
                $isFriend = $friend->where( '(userid = '.$userid .' AND friendid = '.$userinfo['id'].' ) OR (friendid = '.$userid .' AND userid = '.$userinfo['id'].')')->count();
                $userinfo['isFriend'] = $isFriend?true:false;
                $userinfo['status'] = $userinfo['status'] == 'online'?'在线':'离线';
                $userinfo['addtime'] = $userinfo['addtime'] == null?'未知':date('Y年m月n日',$userinfo['addtime']);
                $json_data = [
                    'code' => 0,
                    'msg' => '查询成功',
                    'data' => $userinfo
                ];
                return json($json_data);

                //$db->alias('f')->field('')->join('chatuser c','c.id = f.')->where( '(userid = '.$userid .' AND friendid = '.$search.') OR (friendid = '.$userid .' AND userid = '.$search.')')->
            }else{
                $groupdetail = db('groupdetail');
                $chatgroup = db('chatgroup');
                $groupinfo = $chatgroup->field('id,group_no as no,groupname,avatar,createtime')->where('public = 1 AND (groupname = "'.$search.'" OR group_no = "'.$search.'")')->find();

                if (!$groupinfo) {//不存在该群
                    $json_data = [
                        'code' => 1,
                        'msg' => '群不存在',
                        'data' => []
                    ];
                    return json($json_data);
                }
                $groupinfo['number'] = $groupdetail->where('groupid = '.$groupinfo['id'])->count();//群人数
                $groupinfo['createtime'] = $groupinfo['createtime'] == null?'未知':date('Y年m月n日',$groupinfo['createtime']);
                $inGroup = $groupdetail->where('groupid = '.$groupinfo['id'].' AND userid = '.$userid)->count();
                $groupinfo['inGroup'] = $inGroup?true:false;
                $json_data = [
                    'code' => 0,
                    'msg' => '查询成功',
                    'data' => $groupinfo
                ];
                return json($json_data);
            }

        }else{
            $json_data = [
                'code' => -1,
                'msg' => '非法获取',
                'data' => []
            ];
        }
        return json($json_data);
    }


    public function sendAddUserMsg(){
        $userid = input('param.userid');
        $mine = input('param.mine');
        $content = input('param.content');
        $friend = db('friend');
        $msgbox = db('msgbox');
        if ($mine == session('uid')) {
            $isFriend = $friend->where( '(userid = '.$userid .' AND friendid = '.$mine.') OR (friendid = '.$userid .' AND userid = '.$mine.')')->count();
            if (!$isFriend) {
                $mineSend = $msgbox->field('status')->where('fromid = '.$mine .' AND toid = '.$userid.' AND type = "friend"')->find();
                $userSend = $msgbox->field('status')->where('fromid = '.$userid .' AND toid = '.$mine.' AND type = "friend"')->find();
                if ($mineSend) {//在这之前我已经发送过好友请求给对方
                    if ($mineSend['status'] == 0 || $mineSend['status'] == 2) {//消息已发送，但对方未回应/拒绝
                        $data['content'] = $content;
                        $data['timeline'] = time();
                        $data['status'] = 0;
                        $msgbox->where('fromid = '.$mine .' AND toid = '.$userid.' AND type = "friend"')->update($data);
                        $json_data = [
                            'code' => 0,
                            'msg' => '发送成功',
                            'data' => []
                        ];
                    }else{
                        $json_data = [
                            'code' => 0,
                            'msg' => '对方已经是你的好友，无需重复添加',
                            'data' => []
                        ];
                    }
                }elseif($userSend){//对方在这之前已经发送过好友请求给我
                    if ($userSend['status'] == 0) {//消息已发送，但我未回应
                        $data['content'] = $content;
                        $data['timeline'] = time();
                        $data['status'] = 1;
                        $dataFriend['userid'] = $userid;
                        $dataFriend['friendid'] = $mine;
                        $friend->insert($dataFriend);
                        $msgbox->where('fromid = '.$userid .' AND toid = '.$mine.' AND type = "friend"')->update($data);
                        $json_data = [
                            'code' => 0,
                            'msg' => '同意添加好友',
                            'data' => []
                        ];
                    }elseif($userSend['status'] == 2){//消息已发送，但我已拒绝
                        $data['content'] = $content;
                        $data['timeline'] = time();
                        $data['status'] = 0;
                        $data['type'] = 'friend';
                        $data['fromid'] = $mine;
                        $data['toid'] = $userid;
                        $msgbox->where('fromid = '.$userid .' AND toid = '.$mine.' AND type = "friend"')->delete();
                        $msgbox->insert($data);
                        $json_data = [
                            'code' => 0,
                            'msg' => '发送成功',
                            'data' => []
                        ];
                    }else{
                        $json_data = [
                            'code' => 0,
                            'msg' => '对方已经是你的好友，无需重复添加',
                            'data' => []
                        ];
                    }
                }else{
                    $data['content'] = $content;
                    $data['timeline'] = time();
                    $data['status'] = 0;
                    $data['type'] = 'friend';
                    $data['fromid'] = $mine;
                    $data['toid'] = $userid;
                    $msgbox->insert($data);
                    $json_data = [
                        'code' => 0,
                        'msg' => '发送成功',
                        'data' => []
                    ];
                }

            }else{
                $json_data = [
                    'code' => 1,
                    'msg' => '已经是好友，无需重复添加',
                    'data' => []
                ];
            }
        }else{
            $json_data = [
                'code' => -1,
                'msg' => '非法获取',
                'data' => []
            ];
        }
        return json($json_data);
    }

    //发送加群验证
    public function sendAddGroupMsg(){
        $groupid = input('param.groupid');
        $mine = input('param.mine');
        $content = input('param.content');
        $group = db('groupdetail');
        $msgbox = db('msgbox');
        if ($mine == session('uid')) {
            $inGroup = $group->where( 'userid = '.$mine .' AND groupid = '.$groupid )->count();
            if (!$inGroup) {
                $mineSend = $msgbox->field('status')->where('fromid = '.$mine .' AND toid = '.$groupid.' AND type = "group"')->find();
                if ($mineSend) {//在这之前我已经发送过加群请求
                    if ($mineSend['status'] == 0 || $mineSend['status'] == 2) {//消息已发送，但对方未回应/拒绝
                        $data['content'] = $content;
                        $data['timeline'] = time();
                        $data['status'] = 0;
                        $msgbox->where('fromid = '.$mine .' AND toid = '.$groupid.' AND type = "group"')->update($data);
                        $json_data = [
                            'code' => 0,
                            'msg' => '发送成功',
                            'data' => []
                        ];
                    }else{
                        $json_data = [
                            'code' => 0,
                            'msg' => '你已加入该群，无需重复添加',
                            'data' => []
                        ];
                    }
                }else{
                    $data['content'] = $content;
                    $data['timeline'] = time();
                    $data['status'] = 0;
                    $data['type'] = 'group';
                    $data['fromid'] = $mine;
                    $data['toid'] = $groupid;
                    $msgbox->insert($data);
                    $json_data = [
                        'code' => 0,
                        'msg' => '发送成功',
                        'data' => []
                    ];
                }

            }else{
                $json_data = [
                    'code' => 1,
                    'msg' => '你已加入该群，无需重复添加',
                    'data' => []
                ];
            }
        }else{
            $json_data = [
                'code' => -1,
                'msg' => '非法获取',
                'data' => []
            ];
        }
        return json($json_data);
    }

    //我的好友
    public function getMyfriend()
    {
        $friends1 = db('friend')->alias('f')->field('c.id,c.username,c.avatar,c.sign')->join('chatuser c','c.id = f.friendid')->where('f.userid ='.session('uid'))->select();
        $friends2 = db('friend')->alias('f')->field('c.id,c.username,c.avatar,c.sign')->join('chatuser c','c.id = f.userid')->where('f.friendid ='.session('uid'))->select();
        $friends = array();
        if (!empty($friends1) || !empty($friends2)) {
            $friends = array_merge($friends1,$friends2);
        }

        // $friends = db('chatuser')->where('id','neq',session('uid'))->field('id,username,avatar,sign')->select();
        $return = [
            'code' => 0,
            'msg'=> '',
            'data' => [
                'friends' => $friends
            ],
        ];

        return json( $return );
    }

    //通过或拒绝添加请求
    public function agree(){
        $id = input('param.id');
        $type = input('param.type');
        $status = input('param.status');
        $status = $status == 'agree'?1:2;
        $userid = session('uid');
        if ($type == 'group') {
            $groupid = input('param.groupid');
            $grupid_arr = db('groupdetail')->where('userid',$userid)->where('role = 2 OR role = 1')->column('groupid');
            $grupInfo = '';
            $isset = db('msgbox')->field('content')->where('fromid',$id)->where('toid',$groupid)->where('type','group')->find();
            if(in_array($groupid,$grupid_arr) && $isset){
                db('msgbox')->where('fromid',$id)->where('toid',$groupid)->where('type','group')->where('status',0)->setField('status', $status);
                if ($status == 1) {
                    $time = time();
                    $grupInfo = db('chatgroup')->field('groupname,avatar,group_no,id')->where('id',$groupid)->find();
                    $userinfo = db('chatuser')->field('username,avatar,sign,id')->where('id',$id)->find();
                    $memberData = ['userid' => $id,'groupid' => $groupid,'state' => 1,'role' => 3,'apply_time' => $time,'add_time' => $time,'gag' => 0];
                    db('groupdetail')->insert($memberData);
                    $grupInfo['content'] = $isset['content'];
                    $return = [
                        'code' => 0,
                        'msg'=> '已通过申请',
                        'data' => [
                            'group' => $grupInfo,
                            'userinfo' => $userinfo
                        ],
                    ];
                }else{
                    $return = [
                        'code' => 0,
                        'msg'=> '已拒绝申请',
                        'data' => [],
                    ];
                }

            }else{
                $return = [
                    'code' => -1,
                    'msg'=> '非法请求',
                    'data' => [],
                ];
            }
        }else{
            $isset = db('msgbox')->field('content')->where('fromid',$id)->where('toid',$userid)->where('type','friend')->find();
            if ($isset) {
                db('msgbox')->where('fromid',$id)->where('toid',$userid)->where('status',0)->where('type','friend')->setField('status', $status);
                if ($status == 1) {
                    $userInfo = db('chatuser')->field('username,avatar,sign,id')->where('id',$id)->find();
                    $data = ['userid' =>$id,'friendid' => $userid];
                    db('friend')->insert($data);
                    $userInfo['content'] = $isset['content'];
                    $return = [
                        'code' => 0,
                        'msg'=> '已通过申请',
                        'data' => [
                            'userinfo' =>$userInfo,
                        ],
                    ];
                }else{
                    $return = [
                        'code' => 0,
                        'msg'=> '已拒绝申请',
                        'data' => [],
                    ];
                }

            }else{
                $return = [
                    'code' => -1,
                    'msg'=> '非法请求',
                    'data' => [],
                ];
            }
        }

        return json( $return );
    }

    public function myGroupTotal(){
        $count = db('chatgroup')->where('owner_id',session('uid'))->count();
        if ($count < 5) {
            $return = [
                'code' => 0,
                'msg'=> '',
                'data' => [],
            ];
        }else{
            $return = [
                'code' => -1,
                'msg'=> '',
                'data' => [],
            ];
        }
        return json( $return );
    }
    //确认邀请好友入群（群主和管理员都可以邀请）
    public function addSureIntoGroup(){
        $userid = input('param.userid');
        $groupid = input('param.groupid');
        $members = input('param.members/a');
        if ($userid && $userid == session('uid')) {
            $db = db('groupdetail');
            $user = $db->field('role')->where('userid',$userid)->where('groupid',$groupid)->find();
            $groupno = db('chatgroup')->field('group_no as no')->where('id',$groupid)->find();
            if ($user['role'] == 1 || $user['role'] == 2) {
                $time = time();
                foreach ($members as $key => $value) {
                    $memberData = ['userid' => $value,'groupid' => $groupid,'state' => 1,'role' => 3,'apply_time' => $time,'add_time' => $time,'gag' => 0];
                    $db->insert($memberData);
                }
                $return = [
                    'code' => 0,
                    'msg'=> '邀请成功',
                    'data' => [
                        'groupid' => $groupid,
                        'no' => $groupno['no'],
                        'avatar' => '/uploads/group.png',
                        'groupname' => '群聊',
                    ],
                ];
            }else{
                $return = [
                    'code' => 0,
                    'msg'=> '非法请求',
                    'data' => [],
                ];
            }

        }else{
            $return = [
                'code' => -1,
                'msg'=> '非法操作',
                'data' => [],
            ];
        }
        return json( $return );
    }

    //确认删除群成员（群主和管理员都可以删除）
    public function delSureFromGroup(){
        $userid = input('param.userid');
        $groupid = input('param.groupid');
        $members = input('param.members/a');
        if ($userid && $userid == session('uid')) {
            $db = db('groupdetail');
            $user = $db->field('role')->where('userid',$userid)->where('groupid',$groupid)->find();
            $group = db('chatgroup')->field('avatar,groupname,id')->where('id',$groupid)->find();
            if ($user['role'] == 1 || $user['role'] == 2) {
                foreach ($members as $key => $value) {
                    $db->where('userid',$value)->where('groupid',$groupid)->where('role',3)->delete();
                }
                $return = [
                    'code' => 0,
                    'msg'=> '删除成功',
                    'data' => [
                        'group' => $group
                    ],
                ];
            }

        }else{
            $return = [
                'code' => -1,
                'msg'=> '非法操作',
                'data' => [],
            ];
        }
        return json( $return );
    }

    //创建群聊
    public function suregroup(){
        $userid = input('param.userid');
        $members = input('param.members/a');
        if ($userid && $userid == session('uid')) {
            $no = sprintf("%05d",$userid);
            $count = db('chatgroup')->where('owner_id',$userid)->count();
            $no = ($count+1).$no;
            $groupname = '群聊'.date('W').date("w").date('His').$userid;
            $data = ['groupname' => $groupname,'owner_id' => $userid,'group_no' => $no,'state' => -1,'createtime' => time()];
            db('chatgroup')->insert($data);
            $groupid = db('chatgroup')->getLastInsID();
            if ($groupid) {
                $db = db('groupdetail');
                $time = time();
                foreach ($members as $key => $value) {
                    $memberData = ['userid' => $value,'groupid' => $groupid,'state' => 1,'role' => 3,'apply_time' => $time,'add_time' => $time,'gag' => 0];
                    $db->insert($memberData);
                }
                //将群主添加入群
                $memberData = ['userid' => $userid,'groupid' => $groupid,'state' => 1,'role' => 1,'apply_time' => $time,'add_time' => $time,'gag' => 0];
                $db->insert($memberData);
                $return = [
                    'code' => 0,
                    'msg'=> '创建成功',
                    'data' => [
                        'groupid' => $groupid,
                        'avatar' => '/uploads/group.png',
                        'no' => $no,
                        'groupname' => $groupname,
                    ],
                ];
            }else{
                $return = [
                    'code' => -1,
                    'msg'=> '创建失败',
                    'data' => [],
                ];
            }
        }else{
            $return = [
                'code' => -1,
                'msg'=> '非法操作',
                'data' => [],
            ];
        }
        return json( $return );
    }

    //全部群
    public function getAllGroup(){
        $data = db('chatgroup')->field('id,groupname,avatar')->select();
        return json($data);
    }
    //我创建的群和我在的群
    public function getMyGroup(){
        $userid = input('param.userid');
        if ($userid = session('uid')) {
            $myGroup = db('chatgroup')->where('owner_id',$userid)->field('id,groupname,avatar,group_no as no')->select();
            $group = db('groupdetail')->alias('g')->field('c.id,c.groupname,c.avatar,c.group_no as no')->join('chatgroup c','c.id = g.groupid')->where('g.userid ='.$userid.' AND g.state = 1 AND ( g.role = 2 OR g.role = 3)')->group('g.groupid')->select();
            $json_data = [
                'code' => 0,
                'msg' => '查询成功',
                'data' => [
                    'myGroup' => $myGroup,
                    'group' => $group,
                ]
            ];
        }else{
            $json_data = [
                'code' => -1,
                'msg' => '非法获取',
                'data' => []
            ];
        }

        return json($json_data);
    }

    //可申请的群
    public function getGroup(){
        $data = db('chatgroup')->field('id,groupname,avatar')->select();
        $group = db('groupdetail')->field('groupid as id')->where('userid ='.session('uid').' AND state = 1')->group('groupid')->select();
        $json_data = [
            'myGroup' => $group,
            'allGroup' => $data,
        ];
        return json($json_data);
    }

    //查看群信息
    public function getGroupSet(){
        $userid = input('param.userid');
        $groupid = input('param.groupid');
        $groupInfo = db('chatgroup')->field('groupname,avatar,owner_id,public,verify,group_no ')->where('id',$groupid)->find();
        if (empty($groupInfo)) {
            $json_data = [
                'code' => -1,
                'msg' => '不存在的群',
                'data' => []
            ];
            return json($json_data);
        }
        if ($groupInfo['owner_id'] != $userid || $groupInfo['owner_id'] != session('uid')) {
            unset($groupInfo['public']);
            unset($groupInfo['verify']);
        }
        $groupInfo['avatar'] = $groupInfo['avatar'].'?v='.time();
        $json_data = [
            'code' => 0,
            'msg' => '查询成功',
            'data' => [
                'info' => $groupInfo
            ]
        ];
        return json($json_data);
    }

    //      退群
    function leaveGroup(){
        $userid = input('param.userid');
        $groupid = input('param.groupid');
        if ($userid != session('uid')) {
            $json_data = [
                'code' => -1,
                'msg' => '非法操作',
                'data' => []
            ];
            return json($json_data);
        }

        $user = db('groupdetail')->field('role')->where('groupid',$groupid)->where('userid',$userid)->find();
        if ($user['role'] = 3) {
            db('groupdetail')->where('groupid',$groupid)->where('userid',$userid)->delete();
            $json_data = [
                'code' => 0,
                'msg' => '操作成功',
                'data' => []
            ];
        }else{
            $json_data = [
                'code' => -1,
                'msg' => '你现在是管理员不能退出',
                'data' => []
            ];
        }
        return json($json_data);
    }

    //查看好友信息
    public function getFriendSet(){
        $userid = input('param.userid');
        $mine = input('param.mine');
        if ($mine == session('uid')) {
            $friend = db('friend')->alias('f')->field('c.id,c.username,c.avatar,c.sign,c.status,c.no,c.addtime')->join('chatuser c','c.id = f.friendid')->where('f.userid ='.$mine. ' AND f.friendid ='.$userid)->find();
            if (!$friend) {
                $friend = db('friend')->alias('f')->field('c.id,c.username,c.avatar,c.sign,c.status,c.no,c.addtime')->join('chatuser c','c.id = f.userid')->where('f.friendid ='.$mine. ' AND f.userid ='.$userid)->find();
                if (!$friend) {
                    $json_data = [
                        'code' => -1,
                        'msg' => '不是好友',
                        'data' => []
                    ];
                    return json($json_data);
                }
            }
        }else{
            $json_data = [
                'code' => -1,
                'msg' => '非法操作',
                'data' => []
            ];
            return json($json_data);
        }
        $friend['status'] = $friend['status'] == 'online'?'在线':'离线';
        $json_data = [
            'code' => 0,
            'msg' => '查询成功',
            'data' =>  $friend
        ];
        return json($json_data);
    }

    //删除好友
    function removeFriend(){
        $userid = input('param.userid');
        $mine = input('param.mine');
        if ($mine != session('uid')) {
            $json_data = [
                'code' => -1,
                'msg' => '非法操作',
                'data' => []
            ];
            return json($json_data);
        }

        $isFriend = db('friend')->where('(userid ='.$mine .' AND friendid ='.$userid .' ) OR ( userid ='.$userid .' AND friendid ='.$mine)->count();
        if ($isFriend) {
            db('friend')->where('(userid ='.$mine .' AND friendid ='.$userid .' ) OR ( userid ='.$userid .' AND friendid ='.$mine)->delete();
            $json_data = [
                'code' => 0,
                'msg' => '操作成功',
                'data' => []
            ];
        }else{
            $json_data = [
                'code' => -1,
                'msg' => '不是好友',
                'data' => []
            ];
        }
        return json($json_data);
    }

    public function friendship(){
        $userid = input('param.userid');
        $mine = input('param.mine');
        if ($mine  != session('uid')) {
            $json_data = [
                'code' => -1,
                'msg' => '非法操作',
                'data' => []
            ];
            return json($json_data);
        }
        $isFriend = db('friend')->where('(userid ='.$mine .' AND friendid ='.$userid .') OR ( userid ='.$userid .' AND friendid ='.$mine.')')->find();
        if (!$isFriend) {
            $json_data = [
                'code' => -1,
                'msg' => '非法操作',
                'data' => []
            ];
            return json($json_data);
        }
        $json_data = [
            'code' => 0,
            'msg' => '',
            'data' => $isFriend
        ];
        return json($json_data);
    }

    //设置群名称
    public function setGroupname(){
        $userid = input('param.userid');
        $groupid = input('param.groupid');
        $groupname = input('param.groupname');
        if ($userid  != session('uid')) {
            $json_data = [
                'code' => -1,
                'msg' => '非法操作',
                'data' => []
            ];
            return json($json_data);
        }
        $group = db('chatgroup')->field('groupname')->where('id',$groupid)->where('owner_id',$userid)->find();
        if ($group['groupname']) {
            db('chatgroup')->where('id',$groupid)->where('owner_id',$userid)->setField('groupname', $groupname);
            $json_data = [
                'code' => 0,
                'msg' => '设置成功',
                'data' => []
            ];
        }else{
            $json_data = [
                'code' => -1,
                'msg' => '非法操作',
                'data' => []
            ];
        }
        return json($json_data);
    }

    //设置群头像
    public function setGroupAvatar(){
        $userid = input('param.userid');
        $groupid = input('param.groupid');
        $groupAvatar = input('param.dataURL');
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/',$groupAvatar,$res)) {
            //获取图片类型
            $type = $res[2];
            //图片保存路径
            $new_file = "uploads/avatar/";
            if (!file_exists($new_file)) {
                mkdir($new_file,0755,true);
            }
            $uid = session('uid');
            if ($uid == $userid) {
                $group = db('chatgroup')->field('group_no')->where('id',$groupid)->where('owner_id',$userid)->find();
                //图片名字
                $new_file = $new_file.$group['group_no'].time().'.'.$type;
                if (file_put_contents($new_file,base64_decode(str_replace($res[1],'', $groupAvatar)))) {
                    db('chatgroup')->where('id',$groupid)->where('owner_id',$userid)->setField('avatar',$new_file);
                    $json_data = [
                        'code' => 0,
                        'msg' => '修改成功',
                        'data' => [
                            'avatar' => $new_file
                        ]
                    ];
                } else {
                    $json_data = [
                        'code' => -1,
                        'msg' => '修改失败',
                        'data' => []
                    ];
                }
            }else{
                $json_data = [
                    'code' => -1,
                    'msg' => '权限不足',
                    'data' => []
                ];
            }

        } else{
            $json_data = [
                'code' => -1,
                'msg' => '文件格式错误',
                'data' => []
            ];
        }
        return json($json_data);
    }
    //设置群头像
    public function setavatar(){
        $userid = input('param.userid');
        $avatar = input('param.dataURL');
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/',$avatar,$res)) {
            //获取图片类型
            $type = $res[2];
            //图片保存路径
            $new_file = "uploads/avatar/";
            if (!file_exists($new_file)) {
                mkdir($new_file,0755,true);
            }
            $uid = session('uid');
            if ($uid == $userid) {
                //图片名字
                $new_file = $new_file.$userid.time().'.'.$type;
                if (file_put_contents($new_file,base64_decode(str_replace($res[1],'', $avatar)))) {
                    db('chatuser')->where('id',$userid)->setField('avatar',$new_file);
                    $json_data = [
                        'code' => 0,
                        'msg' => '修改成功',
                        'data' => [
                            'avatar' => $new_file
                        ]
                    ];
                } else {
                    $json_data = [
                        'code' => -1,
                        'msg' => '修改失败',
                        'data' => []
                    ];
                }
            }else{
                $json_data = [
                    'code' => -1,
                    'msg' => '权限不足',
                    'data' => []
                ];
            }

        } else{
            $json_data = [
                'code' => -1,
                'msg' => '文件格式错误',
                'data' => []
            ];
        }
        return json($json_data);
    }
    //设置个人信息
    public function setInfo(){
        $userid = input('param.userid');
        $val = input('param.val');
        $type = input('param.type');
        if ($userid  != session('uid')) {
            $json_data = [
                'code' => -1,
                'msg' => '非法操作',
                'data' => []
            ];
            return json($json_data);
        }

        $info = db('chatuser')->field($type)->where('id',$userid)->find();
        if ($info[$type]) {
            db('chatuser')->where('id',$userid)->setField($type, $val);
            $json_data = [
                'code' => 0,
                'msg' => '设置成功',
                'data' => []
            ];
        }else{
            $json_data = [
                'code' => -1,
                'msg' => '非法操作',
                'data' => []
            ];
        }
        return json($json_data);
    }

    public function getUserInfo(){
        $userid = input('param.userid');
        $groupid = input('param.groupid');
        $status = db('chatuser')->field('status,avatar,username,id,sign,no,addtime')->where('id',$userid)->find();
        $add_time = db('groupdetail')->field('add_time,role,gag')->where('userid',$userid)->where('groupid',$groupid)->find();

        if (empty($add_time)) {
            $json_data = [
                'code' => -1,
                'msg' => '该成员不存在',
                'data' => []
            ];
            return json($json_data);
        }
        $mine = db('groupdetail')->field('role')->where('userid',session('uid'))->where('groupid',$groupid)->find();
        if (empty($mine)) {
            $json_data = [
                'code' => -1,
                'msg' => '你已经不在该群',
                'data' => []
            ];
            return json($json_data);
        }
        $lastlog = db('chatlog')->field('timeline')->where('fromid',$userid)->where('toid',$groupid)->where('type','group')->order('id desc')->find();
        $add_status = db('config')->field('vdata')->where('kdata','addFriend')->find();

        if ($add_time['gag'] < time()) {
            $add_time['gag'] = 0;
        }
        if (empty($lastlog)) {
            $lastlog = '从未发言';
        }else{
            $lastlog = date('Y年m月n日 H:i:s',$lastlog['timeline']);
        }
        $status['status'] = $status['status'] == 'online'?'在线':'离线';
        $json_data = [
            'code' => 0,
            'msg' => '查询成功',
            'data' => [
                'addStatus' => $add_status['vdata'],
                'userinfo' => $status,
                'status' => $status['status'],
                'add_time' => date('Y年m月n日',$add_time['add_time']),
                'lastlog' => $lastlog,
                'role' => $add_time['role'],
                'mine' => $mine['role'],
                'gag' => $add_time['gag'],
            ]

        ];
        return json($json_data);
    }
    //设置管理员
    public function manager(){
        $userid = input('param.userid');
        $groupid = input('param.groupid');
        $role = input('param.role');
        $user = db('groupdetail')->field('role')->where('groupid',$groupid)->where('userid',session('uid'))->find();
        $msg = $role==2?'设置':'取消';
        if ($user['role'] == 1) {
            db('groupdetail')->where('groupid',$groupid)->where('userid',$userid)->setField('role', $role);
            $json_data = [
                'code' => 1,
                'msg' => $msg.'成功',
                'data' => []
            ];
        }else{
            $json_data = [
                'code' => -1,
                'msg' => $msg.'无效',
                'data' => []
            ];
        }
        return json($json_data);
    }
    //设置群搜索
    public function ispublic(){
        $userid = input('param.userid');
        $groupid = input('param.groupid');
        $public = input('param.public');
        if ($userid == session('uid')) {
            $user = db('chatgroup')->field('public')->where('id',$groupid)->where('owner_id',$userid)->find();
            $msg = $public==1?'设置':'取消';
            if ($user['public']) {
                db('chatgroup')->where('id',$groupid)->where('owner_id',$userid)->setField('public', $public);
                $json_data = [
                    'code' => 1,
                    'msg' => $msg.'成功',
                    'data' => []
                ];
            }else{
                $json_data = [
                    'code' => -1,
                    'msg' => $msg.'无效',
                    'data' => []
                ];
            }
        }else{
            $json_data = [
                'code' => -1,
                'msg' => '非法操作',
                'data' => []
            ];
        }
        return json($json_data);
    }
    //设置群审核
    public function verify(){
        $userid = input('param.userid');
        $groupid = input('param.groupid');
        $verify = input('param.verify');
        if ($userid == session('uid')) {
            $user = db('chatgroup')->field('verify')->where('id',$groupid)->where('owner_id',$userid)->find();
            $msg = $verify==1?'设置':'取消';
            if ($user['verify']) {
                db('chatgroup')->where('id',$groupid)->where('owner_id',$userid)->setField('verify', $verify);
                $json_data = [
                    'code' => 1,
                    'msg' => $msg.'成功',
                    'data' => []
                ];
            }else{
                $json_data = [
                    'code' => -1,
                    'msg' => $msg.'无效',
                    'data' => []
                ];
            }
        }else{
            $json_data = [
                'code' => -1,
                'msg' => '非法操作',
                'data' => []
            ];
        }
        return json($json_data);
    }

    function disbandedGroup(){
        $userid = input('param.userid');
        $groupid = input('param.groupid');
        if ($userid == session('uid')) {
            $mine = db('chatgroup')->field('id')->where('id',$groupid)->where('owner_id',$userid)->find();
            if ($mine['id']) {
                db('chatgroup')->where('id',$mine['id'])->delete();
                db('groupdetail')->where('groupid',$groupid)->delete();
                $json_data = [
                    'code' => 0,
                    'msg' => '操作成功',
                    'data' => []
                ];
            }else{
                $json_data = [
                    'code' => -1,
                    'msg' => '非法操作',
                    'data' => []
                ];
            }
        }else{
            $json_data = [
                'code' => -1,
                'msg' => '非法操作',
                'data' => []
            ];
        }
        return json($json_data);
    }

    function gag(){
        $userid = input('param.userid');
        $groupid = input('param.groupid');
        $gag = input('param.gag');
        $mine = db('groupdetail')->field('role')->where('groupid',$groupid)->where('userid',session('uid'))->find();
        $user = db('groupdetail')->field('role')->where('groupid',$groupid)->where('userid',$userid)->find();
        $msg = $gag==0?'解除':'禁言';
        if ($mine['role'] < $user['role'] && $mine['role'] > 0) {
            $gag = $gag==0?0:$gag+time();
            db('groupdetail')->where('groupid',$groupid)->where('userid',$userid)->setField('gag', $gag);
            $json_data = [
                'code' => 1,
                'msg' => $msg.'成功',
                'data' => [
                    'endtime' => $gag
                ]
            ];
        }else{
            $json_data = [
                'code' => -1,
                'msg' => $msg.'无效',
                'data' => []
            ];
        }
        return json($json_data);
    }

    function removegroup(){
        $userid = input('param.userid');
        $groupid = input('param.groupid');
        $mine = db('groupdetail')->field('role')->where('groupid',$groupid)->where('userid',session('uid'))->find();
        $user = db('groupdetail')->field('role')->where('groupid',$groupid)->where('userid',$userid)->find();
        if ($mine['role'] < $user['role'] && $mine['role'] > 0) {
            db('groupdetail')->where('groupid',$groupid)->where('userid',$userid)->delete();
            $json_data = [
                'code' => 1,
                'msg' => '操作成功',
                'data' => []
            ];
        }else{
            $json_data = [
                'code' => -1,
                'msg' => '操作无效',
                'data' => []
            ];
        }
        return json($json_data);
    }


    public function live(){
        $groupid = input('param.id');
        $id = session('uid');
        $isEmpty = db('groupdetail')->field('state,gag')->where('groupid = ' . $groupid . ' AND userid = '. $id)->find();
        if (!$isEmpty) {
            $return = [
                    'code' => -1,
                    'msg' => '不是该群群员',//不是群员
                    'data' => []
            ];

            return json( $return );
        }
        if ($isEmpty['gag'] < time()) {
            $isEmpty['gag'] = 0;
            db('groupdetail')->where('groupid = ' . $groupid . ' AND userid = '. $id)->setField('gag', $isEmpty['gag']);
        }
        if ($isEmpty['state'] == 1) {
            $data = db('chatgroup')->field('video,video2,state,owner_id')->where('id = ' . $groupid)->find();
            if ($data['state'] == 1) {
                $return = [
                        'code' => 1,
                        'msg' => '已获取',
                        'data' => [
                            'video' => $data['video'],
                            'video2' => $data['video2'],
                            'gag' => $isEmpty['gag'],
                            'owner' => $data['owner_id'],
                        ]
                ];
            }else{
                $return = [
                        'code' => -2,
                        'msg' => '',
                        'data' => [
                            'gag' => $isEmpty['gag'],
                            'owner' => $data['owner_id'],
                        ]
                ];
            }
            return json( $return );
        }else{
            $return = [
                    'code' => -1,
                    'msg' => '非法获取',//不是群员
                    'data' => []
            ];

            return json( $return );
        }
    }

    public function live2(){
        $groupid = input('param.id');
        $id = session('uid');
        $isEmpty = db('groupdetail')->field('state,gag')->where('groupid = ' . $groupid . ' AND userid = '. $id)->find();
        if (!$isEmpty) {
            $return = [
                'code' => -1,
                'msg' => '不是该群群员',//不是群员
                'data' => []
            ];

            return json( $return );
        }
        if ($isEmpty['gag'] < time()) {
            $isEmpty['gag'] = 0;
            db('groupdetail')->where('groupid = ' . $groupid . ' AND userid = '. $id)->setField('gag', $isEmpty['gag']);
        }
        if ($isEmpty['state'] == 1) {
            $data = db('chatgroup')->field('video2,state,owner_id')->where('id = ' . $groupid)->find();
            if (!$data['video2']) {
                $return = [
                    'code' => -2,
                    'msg' => '没有设置',
                    'data' => [
                        'gag' => $isEmpty['gag'],
                        'owner' => $data['owner_id'],
                    ]
                ];
                return json( $return );
            }
            if ($data['state'] == 1) {
                $return = [
                    'code' => 1,
                    'msg' => '已获取',
                    'data' => [
                        'video' => $data['video2'],
                        'gag' => $isEmpty['gag'],
                        'owner' => $data['owner_id'],
                    ]
                ];
            }else{
                $return = [
                    'code' => -2,
                    'msg' => '',
                    'data' => [
                        'gag' => $isEmpty['gag'],
                        'owner' => $data['owner_id'],
                    ]
                ];
            }
            return json( $return );
        }else{
            $return = [
                'code' => -1,
                'msg' => '非法获取',//不是群员
                'data' => []
            ];
            return json( $return );
        }
    }

    public function apply(){
        $groupid = input('param.groupid');
        $id = session('uid');
        $isEmpty = db('groupdetail')->field('state ')->where('groupid = ' . $groupid . ' AND userid = '. $id)->find();
        if ($isEmpty && $isEmpty['state'] == 0) {
            $return = [
                    'code' => -1,
                    'msg' => '您已经申请加入此群，请通知群管理同意进群',
                    'data' => []
            ];

            return json( $return );
        }
        // $username = cookie('username');
        // $avatar = cookie('avatar');
        // $sign = cookie('sign');
        $data = [
            'userid' => $id,
            // 'username' => $username,
            // 'useravatar' => $avatar,
            // 'usersign' => $sign,
            'groupid' => $groupid,
            'apply_time' => time(),
        ];
        db('groupdetail')->insert($data);
            $return = [
                    'code' => 0,
                    'msg' => '您已经申请加入此群，请通知群管理同意进群',
                    'data' => []
            ];

            return json( $return );

    }
    //获取组员信息
    public function getMembers()
    {
    	$id = input('param.groupid');
    	//群主信息
    	// $owner = db('chatgroup')->field('owner_name,owner_id,owner_avatar,owner_sign')->where('id = ' . $id)->find();
    	//群成员信息
    	$list = db('groupdetail')->alias('sg')->field('sc.id userid,sc.username,sc.avatar useravatar,sc.sign usersign,sg.role')->join('chatuser sc','sg.userid = sc.id')->where('sg.groupid = ' . $id)->order('sg.role')->select();

    	$return = [
    			'code' => 0,
    			'msg' => '',
    			'data' => [
    				'list' => $list
    			]
    	];

    	return json( $return );
    }

    public function logout(){
        $userid = input('param.userid');
        $id = session('uid');
        if ($id != $userid) {
            $return = [
                'code' => -1,
                'msg' => '非法操作',
                'data' => []
            ];
        }else{
            //设置为登录状态
            db('chatuser')->where('id', $userid)->setField('status', 'outline');
            cookie( 'uid', null);
            cookie( 'username', null);
            cookie( 'avatar', null);
            cookie( 'sign', null);
            $return = [
                    'code' => 0,
                    'msg' => '退出成功',
                    'data' => []
            ];
        }
        return json( $return );
    }

}
