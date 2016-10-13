<?php
namespace app\admin\model;

class Collect extends Common
{
    protected $table = 'collect_rule';
    
    /**
     * @description:获取所有规则
     * @author wuyanwen(2016年8月22日)
     */
    public function get_all_rule()
    {
        $where = [
            'c.status' => self::NORMAL_STATUS,
        ];
        
        return $this->alias('c')->where($where)->paginate(15);
    }
    
    /**
     * @description:添加一条记录
     * @author wuyanwen(2016年8月22日)
     * @param unknown $data
     */
    public function add_rule($data)
    {
        return $this->save($data);
    }
    
    /**
     * @description:编辑规则
     * @author wuyanwen(2016年8月23日)
     * @param unknown $data
     * @return Ambigous <number, boolean, string>
     */
    public function edit_rule($data)
    {
        $where = [
            'id' => $data['id'],
            'status' => self::NORMAL_STATUS,
        ];
        unset($data['id']);
        return $this->save($data,$where);
    }
    /**
     * @description:查询一条记录
     * @author wuyanwen(2016年8月22日)
     * @param unknown $id
     * @return \think\static
     */
    public function get_one_rule($id)
    {
        $where = [
            'id' => $id,
            'status' => self::NORMAL_STATUS,
        ];
        
        return $this::get($where);
    }
    
    /**
     * @description:删除规则
     * @author wuyanwen(2016年8月22日)
     */
    public function del_rule($id)
    {
        $data = [
            'status' => self::DEL_STATUS,
        ];
        $where = [
            'id' => $id,
        ];
        return $this->save($data,$where);
    }
}