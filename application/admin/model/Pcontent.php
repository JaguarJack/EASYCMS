<?php
namespace app\admin\model;
class Pcontent extends Common
{
    protected $table = "p_content";
    
    /**
     * @description:获取文章相关内容
     * @author wuyanwen(2016年10月8日)
     * @param unknown $aid
     */
    public function get_content_id($aid)
    {
        $where = [
            'aid' => $aid,
        ];
        
        return $this::where($where)->column('id');
    }
    
    /**
     * @description:修改或是编辑内容
     * @author wuyanwen(2016年10月8日)
     * @param unknown $data
     * @param number $type
     */
    public function get_content($data)
    {
        if(!is_array($data)){
            $where = [
                'id' => $data,
            ];
            
            return $this->get($where);
        }else{
            $where = [
                'id' => $data['id'],
            ];
          
            unset($data['id']);

            return $this::save($data,$where);
        }
    }
    /**
     * @description:删除内容
     * @author wuyanwen(2016年10月8日)
     */
    public function del_content($ids)
    {

        return $this::destroy($ids);
    }
    
    
    public function add_data($data,$aid)
    {
        foreach($data as $vo)
        {
            $data = ['aid' => $aid,'content' => $vo];
            $result = $this->insert($data);
            if(!$result) return false;
        }
        
        return true;
    }
}