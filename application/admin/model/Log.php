<?php
namespace app\admin\model;
use app\admin\model\Menu;
class Log extends Common
{
    protected $table = 'option_log';
    
    /**
     * @description:查询所有的操作日志
     * @author wuyanwen(2016年8月11日)
     * @param string $where 查询提交
     */
    public function get_all_log_record($where=null,$query=null)
    {
       if(!$where){
           $where['id'] = ['<>' ,0];
       }
       return $this->where($where)->order('create_time DESC')->paginate(20,false,['query' => $query]);
    }
    
    /**
     * @description:添加操作日志
     * @author wuyanwen(2016年8月11日)
     * @param unknown $data
     */
    public function add_log_record($data)
    {
        $menu = new Menu();
        $option_name = $menu->menu_is_on($data['option_url'])['menu_name'];
        $data['option_name'] = $option_name;
        $data['create_time'] = time();

        return $this::save($data);
    }
    
    /**
     * @description:清除日志
     * @author wuyanwen(2016年9月5日)
     */
    public function delete_log()
    {
       $result = $this->where('id','>',0)->delete();
       
       return $result ? true : false;
    }
}