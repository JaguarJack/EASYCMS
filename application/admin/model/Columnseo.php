<?php
namespace app\admin\model;

class Columnseo extends Common
{
    protected $table = 'columnseo';
    
    /**
     * @description:设置seo
     * @author wuyanwen(2016年8月18日)
     * @param unknown $data
     */
    public function set_seo($data)
    {
        $where = [
            'cid' => $data['cid'],
        ];

        if(!$this::get($where)){
            return $this->save($data);
        }else{
            unset($data['cid']);
            return $this->save($data,$where);
        }
    }
    
    /**
     * @description:获取一条SEO信息
     * @author wuyanwen(2016年8月18日)
     * @param unknown $id
     */
    public function get_column_seo($cid)
    {
        $where = ['cid' => $cid];
        
        return $this::get($where);
    }
   
}