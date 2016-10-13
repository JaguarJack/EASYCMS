<?php
namespace app\admin\model;

class User extends Common
{
    protected $table = 'user';
    /**
     * @description:获取最近登录的用户
     * @author wuyanwen(2016年8月25日)
     */
    public function get_lastest_login_user($num)
    {
        $where = [
            'status' => self::NORMAL_STATUS,
        ];
        
        return $this->where($where)->order('last_login_time DESC')->limit(0,$num)->select();
    }

    /**
     * @description:获取所有用户
     * @author wuyanwen(2016年9月30日)
     */
    public function get_all_user()
    {
        return $this->paginate(15);
    }
    
    /**
     * @description:修改用户信息
     * @author wuyanwen(2016年9月30日)
     */
    public function edit_user_info($data)
    {
        $where = [
            'id' => $data['id'],
        ];
        
        unset($data['id']);
        
        return $this::save($data,$where);
    }
    
    /**
     * @description:禁止/解禁 用户
     * @author wuyanwen(2016年9月30日)
     * @param unknown $id
     */
    public function ban_user($id,$status)
    {
        $where = [
            'id' => $id,
        ];

        return $this::save(['status'=> $status],$where);
    }
    
    /**
     * @description:获取用户信息
     * @author wuyanwen(2016年9月30日)
     * @param unknown $id
     * @return \think\static
     */
    public function get_user_info($id)
    {
        $where = [
            'id' => $id,
        ];
        
        return $this::get($where);
    }
}