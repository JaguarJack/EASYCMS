<?php
namespace app\admin\model;

class Setconfig extends Common
{
    protected $table = 'base_config';
    
    /**
     * @description:查询配置项信息
     * @author wuyanwen(2016年8月12日)
     * @param unknown $name
     */
    public function get_config_by_name($name)
    {
        $where = [
            'name' => $name,
        ];

        return $this::get($where);
    }
    /**
     * @description:设置配置
     * @author wuyanwen(2016年8月10日)
     */
    public function set_config($data)
    {
        foreach($data as $key => $vo){
            if(!$this->get_config_by_name($key)){
                $datas = [
                    'name' => $key,
                    'data' => $vo,
                ];
               if(!$this->isUpdate(false)->save($datas,[],false)) return false;
            }else{                
                $result = $this->save(['data' => $vo],['name' => $key]);
                if($result === false) return false;
            }
        }
        return true;
    }
}