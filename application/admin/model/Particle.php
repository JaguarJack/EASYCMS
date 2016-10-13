<?php
namespace app\admin\model;

class Particle extends Common
{
    protected $table = 'p_article';
    /**
     * @description:获取分页文章
     * @author wuyanwen(2016年9月23日)
     */
    public function get_p_article()
    {
        $field = ['pa.id','author','is_top','title','thumbimg','create_time','c.name'];
        
        return $this->alias('pa')
                    ->join('columns c','pa.cid = c.id','LEFT')
                    ->field($field)
                    ->paginate(15);
    }
    /**
     * @description:设置推荐
     * @author wuyanwen(2016年9月26日)
     * @param unknown $id
     * @param unknown $is_top
     * @return Ambigous <number, \think\false>
     */
    public function set_recommend($id,$is_top)
    {
        $where = [
            'id' => $id,
            'status' => 0,
        ];
    
        $data = ['is_top' => $is_top];
    
        return $this->save($data,$where);
    }
    
    /**
     * @description:插入数据
     * @author wuyanwen(2016年9月23日)
     * @param unknown $data
     */
    public function add_data($data)
    {
        return $this::save($data);
    }
    
    /**
     * @description:删除文章
     * @author wuyanwen(2016年10月8日)
     * @param unknown $id
     */
    public function del_particle($id)
    {
        $where = [
            'id' => $id,
        ];
        
        return $this::destroy($where);
    }
}