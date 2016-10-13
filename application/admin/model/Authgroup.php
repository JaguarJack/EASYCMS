<?php
namespace app\admin\model;

class Authgroup extends Common
{
    protected $table = 'auth_group';
    
    /**
     * description:获取所有组别
     * @author: wuyanwen(2016年8月5日)
     * @return Ambigous <multitype:\think\static , \think\false>
     */
    public function get_group_list()
    {
        return $this->order('sort','desc')->select();
    }
    /**
     * @description:添加，编辑组别
     * @author wuyanwen(2016年8月4日)
     * @param $type 1:添加 2：编辑
     */
    public function option_group($data,$type=1)
    {
        if($type == 1){
            return $this->save($data);
        }else{
            $where = ['id' => $data['id']];
            unset($data['id']);
            return $this::save($data,$where);
        }
        
    }
    
    /**
     * description:根据Id获取组别信息
     * @author: wuyanwen(2016年8月7日)
     * @param unknown $id
     */
    public function get_one_group($id)
    {
        $where = ['id' => $id];
        return $this::get($where);
    }
    
   /**
    * description:添加组别权限
    * @author: wuyanwen(2016年8月7日)
    * @param unknown $groupId
    */
    public function add_auth_rule($groupId,$rule)
    {
        $where = ['id' => $groupId];
        $data['rules'] = $rule;
        
        return $this::save($data,$where);
    }
}