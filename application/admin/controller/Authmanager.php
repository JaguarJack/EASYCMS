<?php
namespace app\admin\controller;

class Authmanager extends Base
{
    /**
     * 查看组别权限
     */
    public function seegroupauth()
    {
        return $this->fetch();
    }
    
    /**
     * 权限组别
     */
    public function authGroup()
    {
        $groupModel = model('Authgroup');
        
        $grouplist = $groupModel->get_group_list();
        return $this->fetch('authgroup',[
            'grouplist' => $grouplist,
        ]);
    }
    
    /**
     * 查看组别的成员
     */
    public function groupuser()
    {
        $users = null;
        return $this->fetch('groupuser',[
            'users' => $users,
        ]);
    }
    
    /**
     * 后台成员列表
     */
    public function userlist()
    {
        $userModel = model('Authuser');
        $users = $userModel->get_user_list();
        
        return $this->fetch('userlist',[
            'users' => $users,
        ]);
    }
    
    /**
     * description:添加后台成员
     * @author: wuyanwen(2016年8月5日)
     * @return mixed
     */
    public function adduser()
    {
        if(is_post()){
            $data = input('post.','','trim');
            $data['add_time'] = time();
            $data['password'] = md5($data['password']);
            $userModel = model('Authuser');

            if($userModel->option_user($data,1)){
                $this->success('添加成功');
            }else{
                $this->error("添加失败");
            }
        }else{
            return $this->fetch();
        }
    }
    
    /**
     * description:编辑用户
     * @author: wuyanwen(2016年8月5日)
     * @return mixed
     */
    public function edituser()
    {
        $userModel = model('Authuser');
        if(is_post()){
            $data = input('post.','','trim');

            if(!$data['password']){
                unset($data['password']);
            }else{
                $data['password'] = md5($data['password']);
            }
            if($userModel->option_user($data,2) === false){
                $this->error("编辑失败");
            }else{
                $this->success('编辑成功');
            }
        }else{
            $id = input('get.id','','intval');  
            $userinfo = $userModel->get_one_user($id);
            
            return $this->fetch('edituser',[
                'userinfo' => $userinfo,
                'id' => $id,
            ]);
        }
    }
    
    /**
     * description:删除用户
     * @author: wuyanwen(2016年8月5日)
     */
    public function deluser()
    {
        $id = input('post.id','','intval');
        $userModel = model('Authuser');
        
        if($userModel->option_user(['status'=>1],3,$id)){
            $this->returnJSON('', "删除成功", config('code.success'));
        }else{
            $this->returnJSON('', "删除失败", config('code.error'));
        }
    }
    /**
     * description:判断该用户名是否存在
     * @author: wuyanwen(2016年8月5日)
     */
    public function isExistName()
    {
        if(is_post()){
            $name = input('post.name','','trim');
            $userModel = model('Authuser');
            if($userModel->get_same_user($name))
            {
                $this->returnJSON('',"该用户名已经存在",config('code.error'));
            }
        }
    }
    /**
     * 添加成员到组别
     */
    public function addusertogroup()
    {
        /* @var $groupAccessModel \app\admin\Model\Authgroupaccess */
        $groupAccessModel = model('Authgroupaccess');
        
        if(is_post()){
            $data = input('post.');
            if($groupAccessModel->add_group_relate_user($data)){
                $this->success("操作成功");
            }else{
                $this->error("操作失败");
            }
        }else{
            $id = input('get.id','','intval');
            /* @var $userMode \app\admin\Model\Authuser */
            $userModel = model('Authuser');
            
            $users = $userModel::all(['status' => 0]);
            $groupuser = $groupAccessModel->get_group_relate_user($id);
            
            foreach ($users as $key => $vo){
                foreach($groupuser as $v){
                    if($vo['id'] == $v['uid']){
                        $users[$key]['status'] = 1;
                    }
                }
            }           
            return $this->fetch('addusertogroup',[
                'users'     => $users,
                'id'        => $id,
            ]);
        }
        
    }
    
    /**
     * 添加组别权限
     */
    public function addauthtogroup()
    {
        /* @var $groupMode \app\admin\Model\Authgroup */
        $groupMode = model('Authgroup');
        
        if(is_post()){
            $data = input('post.');

            $rules = implode($data['menuId'],',');
            
            if($groupMode->add_auth_rule($data['groupid'], $rules) !== false){
                $this->success("编辑成功");
            }else{
                $this->error("编辑失败");
            }
        }else{
            $id = input('get.id','','intval');
            /* @var $menuModel \app\admin\Model\Menu.php */
            $menuModel = model('Menu');
            $column = $menuModel->get_menu_list();
            $rules = $groupMode->get_one_group($id);
            $rulesArr = $rules['rules'] ? explode(',', $rules['rules']) : null;
            if($rulesArr){
                foreach($column as $key => $vo){
                    foreach($rulesArr as $v){
                        if($vo['id'] == $v){
                            $column[$key]['status'] = 1;
                        }
                    }
                }
            }            
            $columnlist = get_column($column,2);            
            //dump($column);            
            return $this->fetch('addauthtogroup',[
                'column' => $columnlist,
                'id'     => $id,
            ]);
        }
    }
    
    public function addgroup()
    {
        if(is_post()){
            $data = input('post.','','trim');
            $groupModel = model('Authgroup');
            if($groupModel->option_group($data)){
                $this->success("添加成功");
            }else{
                $this->error("添加失败");
            }
        }else{
            return $this->fetch();
        }
    }
    /**
     * 编辑组别
     */
    public function editgroup()
    {
        /* @var $groupModel \app\admin\Model\Authgroup */
        $groupModel = model('Authgroup');
        
        if(is_post()){
            $data = input('post.','','trim');
            if($groupModel->option_group($data,2) !== false){
                $this->success("编辑成功");
            }else{
                $this->error("编辑失败");
            }
        }else{
            $id = input('get.id','','intval');
            $groupinfo  = $groupModel->get_one_group($id);

            return $this->fetch('editgroup',[
                'groupinfo' => $groupinfo,
                'id' => $id,
            ]);
        }
    }
}