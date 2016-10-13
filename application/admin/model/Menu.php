<?php
namespace app\admin\model;

class Menu extends Common
{
    protected $table = 'menu';
    
    /**
     * @description:获取菜单
     * @author wuyanwen(2016年8月4日)
     */
    public function get_menu_list()
    {
        $where = [
            'status' => self::NORMAL_STATUS,            
        ];
        return $this->where($where)->order('sort DESC')->select();
    }
    
    /**
     * @description:添加菜单
     * @author wuyanwen(2016年8月4日)
     * @param unknown $data
     * @return Ambigous <number, boolean, string>
     */
    public function add_menu($data)
    {
        return $this->save($data);
    }
    
    /**
     * @description:修改菜单
     * @author wuyanwen(2016年8月4日)
     * @param unknown $id
     * @return Ambigous <number, boolean, string>
     */
    public function edit_menu($data)
    {
        $where = ['id' => $data['id']];
        return $this->save($data,$where);
    }
    
    /**
     * @description:查询单挑记录
     * @author wuyanwen(2016年8月4日)
     * @param unknown $id
     */
    public function get_one($id)
    {
        $where = ['id',$id];
        return $this::get($where);
    }
    /**
     * @description:删除菜单
     * @author wuyanwen(2016年8月4日)
     * @param unknown $id
     */
    public function del_menu($id)
    {
        $where = [
            'id' => $id,
            'status' => self::NORMAL_STATUS,
        ];
 
        $data  = ['status' => self::DEL_STATUS];
        
        return $this::save($data,$where);
    }
    
    /**
     *  description:获取父级菜单
     *  @author: wuyanwen(2016年8月5日)
     * @param unknown $id
     */
    public function get_fmenu()
    {
        $where = [
            'fid' => 0,
            'status' => self::NORMAL_STATUS,
        ];
        
        return $this::all($where);
    }
    
    /**
     * @description:查询当前连接功能是否关闭
     * @author wuyanwen(2016年8月9日)
     * @param unknown $link
     * @return \think\static
     */
    public function menu_is_on($link)
    {
        $where = [
            'status' => self::NORMAL_STATUS,
            'menu_link' => $link,
        ];
        
        return $this::get($where);
    }
    /**
     * @description:开启或是关闭功能
     * @author wuyanwen(2016年8月9日)
     */
    public function getoff_or_geton($id,$on)
    {
        $where = [
            'id' => $id,
            'status' => self::NORMAL_STATUS,
        ];
        
        $data = ['on' => $on];
        
        return $this::save($data,$where);
    }
    /**
     * description:查询当前菜单是否有子菜单
     * @author: wuyanwen(2016年8月5日)
     * @param unknown $fid
     * @return Ambigous <multitype:\think\static , \think\false>
     */
    public function get_son_menu($fid)
    {
        $where = [
            'fid' => $fid,
            'status' => self::NORMAL_STATUS,
        ];
        
       return $this::all($where) ? true : false;
    }
}