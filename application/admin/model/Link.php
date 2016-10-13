<?php
namespace app\admin\model;

class Link extends Common
{
    protected $table = 'link';
    
    
    /**
     * @description:获取所有链接
     * @author wuyanwen(2016年8月9日)
     */
    public function get_all_link()
    {
        return $this->paginate(20);
    }
    
    /**
     * @description:添加链接
     * @author wuyanwen(2016年8月9日)
     * @param unknown $data
     */
    public function add_link($data)
    {
        return $this::save($data);
    }
    
    /**
     * @description:修改链接
     * @author wuyanwen(2016年8月9日)
     * @param unknown $data
     */
    public function edit_link($data)
    {
        $where = [
            'id' => $data['id'],
         ];
        unset($data['id']);
        return $this::save($data,$where);
    }
    
    /**
     * @description:获取链接信息
     * @author wuyanwen(2016年8月9日)
     * @param unknown $id
     */
    public function get_one_link($id)
    {
        $where = [
            'id' => $id,
        ];
        
        return $this::get($where);
    }
    
    /**
     * @description:关闭或开启功能
     * @author wuyanwen(2016年8月9日)
     * @param unknown $id
     * @param unknown $on
     * @param unknown $type 1:检测功能 2：显示功能
     */
    public function change_status($id,$on,$type=1)
    {
        $where = [
            'id' => $id,
        ];
               
        $type == 1 ? $data = ['is_check' => $on] : $data = ['show' => $on];
        
        return $this::save($data,$where);
        
    }
}