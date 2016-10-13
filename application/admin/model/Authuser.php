<?php
namespace app\admin\model;
class AUthuser extends Common
{
    protected $table = "auth_user";
    
    /**
     *  description:获取成员列表
     *  @author: wuyanwen(2016年8月5日)
     */
    public function get_user_list($type=false)
    {
        $where = [
            'status' => self::NORMAL_STATUS,
        ];
        
        if($type){
            return $this::all($where);
        }
        
        return $this->where($where)->paginate(10);
    }
    
    /**
     * description:编辑或是添加用户
     * @author: wuyanwen(2016年8月5日)
     * @param unknown $data
     * @param string $type:1 添加 2 编辑 3 删除
     */
    public function option_user($data,$type=1,$id=null)
    {
        if($type == 1){
            return $this->save($data);
        }else if($type == 2){
            $where = [
                'id' => $data['id'],
            ];
            unset($data['id']);
            return $this->save($data,$where);
        }else{
            $where = [
                'id' => $id,
            ];
            return $this->save($data,$where);
        }
        
    }
    
    /**
     * description:查询特定用户
     * @author: wuyanwen(2016年8月5日)
     * @param unknown $id
     */
    public function get_one_user($id)
    {
        $where = [
            'id' => $id,
            'status' => self::NORMAL_STATUS,
        ];
        
        return $this::get($where);
    }
    
    /**
     * description:判断该用户是否符合登录条件
     * @author: wuyanwen(2016年8月5日)
     * @param unknown $name
     * @param unknown $pwd
     */
    public function is_login_user($name,$pwd)
    {
        $where = [
            'name'     => $name,
            'password' => $pwd,
            'status'   => self::NORMAL_STATUS,
        ];
        
        return $this::get($where);
    }
    /**
     *  description:查询是否有用户名相同的
     *  @author: wuyanwen(2016年8月5日)
     * @param unknown $name
     */
    public function get_same_user($name)
    {
        $where = [
            'status' => self::NORMAL_STATUS,
            'name'   => $name,
        ];
        
        return $this::get($where);
    }
}
