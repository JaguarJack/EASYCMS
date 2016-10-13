<?php
namespace app\admin\controller;

class Usermanager extends Base
{
    /**
     * @description:用户管理
     * @author wuyanwen(2016年8月19日)
     */
    public function index()
    {
        /* @var $userModel \app\admin\model\User */
        $userModel = model("User");
        
        $user = $userModel->get_all_user();

        return $this->fetch('index',[
            'user' => $user,
        ]);
    }
    
    /**
     * @description:修改用户信息
     * @author wuyanwen(2016年9月30日)
     */
    public function editUserInfo()
    {
        /* @var $userModel \app\admin\model\User */
        $userModel = model("User");
        if(is_post()){
            $data = input('post.');
            if($userModel->edit_user_info($data) === false){
                $this->success("修改失败");
            }else{
                $this->error("修改成功");
            }
        }else{
            $id = input('get.id/d');
            $userInfo = $userModel->get_user_info($id);

            return $this->fetch('editUserInfo',[
                'userInfo' => $userInfo,
                'id' => $id,
            ]);
        }
    }
    /**
     * @description:禁止用户
     * @author wuyanwen(2016年8月19日)
     */
    public function banUser()
    {
        $id = input('post.id/d');
        
        /* @var $userModel \app\admin\model\User */
        $userModel = model("User");
        
        //获取用户状态
        $status = $userModel->get_user_info($id)['status'];

        //改变用户状态
        $status = $status == 0 ? 1 : 0;

        if($userModel->ban_user($id, $status)){
            $this->returnJSON('',$status,config('code.success'));
        }else{
            $this->returnJSON('','出了问题，没有成功',config('code.success'));
        }
    }
}