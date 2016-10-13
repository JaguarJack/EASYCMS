<?php
namespace app\admin\model;

class Column extends Common
{
    protected $table = 'columns';
    /**
     * @description:获取所有栏目
     * @author wuyanwen(2016年8月9日)
     */
    public function get_all_column()
    {
        $where = [
            'status' => self::NORMAL_STATUS,
         ];
        
        return $this::all($where);
    }
    
    /**
     * @description:查询菜单下是否有子菜单
     * @author wuyanwen(2016年8月9日)
     * @param unknown $id
     */
    public function get_son_column($id)
    {
        $where = [
            'fid' => $id,
            'status' => self::NORMAL_STATUS,
        ];
        
        return $this::get($where) ? true :false;
    }
    /**
     * @description:获取顶级菜单
     * @author wuyanwen(2016年8月9日)
     * @return Ambigous <number, boolean, string>
     */
    public function get_top_column()
    {
        $where = [
            'fid' => 0,
            'status' => self::NORMAL_STATUS,
        ];
        
        return $this::all($where);
    }
    /**
     * @description:添加column
     * @author wuyanwen(2016年8月9日)
     * @param unknown $data
     */
    public function add_column($data)
    {
        return $this::save($data);
    }
    
    /**
     * @description:查找一条栏目
     * @author wuyanwen(2016年8月9日)
     */
    public function get_one_column($id)
    {
        $where = [
            'id' => $id,
            'status' => self::NORMAL_STATUS,
        ];
        
        return $this::get($where);
    }
    
    /**
     * @description:编辑栏目
     * @author wuyanwen(2016年8月9日)
     */
    public function edit_coluln($data)
    {
        $where = [
            'status' => self::NORMAL_STATUS,
            'id' => $data['id'],
        ];
        
        return $this::save($data,$where);
    }
    
    /**
     * @description:改变显示状态
     * @author wuyanwen(2016年8月9日)
     * @param unknown $id
     */
    public function change_show($id,$show)
    {
        $where = [
            'id' => $id,
            'status' => self::NORMAL_STATUS,
        ];

        $data = [ 'show' => $show ];
        
        return $this::save($data,$where);
    }
    /**
     * @description:删除栏目
     * @author wuyanwen(2016年8月9日)
     * @param unknown $id
     */
    public function del_column($id,$status=1)
    {
        $where = [
            'id' => $id,
        ];
        
        $data = ['status' => $status];

        return $this->save($data,$where);
    }
    
    /**
     * @description:获取非单页面栏目
     * @author wuyanwen(2016年8月17日)
     */
    public function get_all_son_column()
    {
        $where = [
            'status'   => self::NORMAL_STATUS,
            'is_page'  => 1,
        ];
        
        return $this::all($where);
    }
    
    /**
     * @description:获取单页面栏目
     * @author wuyanwen(2016年8月17日)
     */
    public function get_page_column()
    {
        $where = [
            'status'   => self::NORMAL_STATUS,
            'is_page'  => 2,
        ];
    
        return $this::all($where);
    }
    
    /**
     * @description:获取一条单页面栏目
     * @author wuyanwen(2016年8月18日)
     * @param unknown $id
     * @return \think\static
     */
    public function get_one_page_column($id)
    {
        $where = [
            'id' => $id,
        ];
  
        return $this::get($where);
    }
}