<?php
namespace app\admin\model;

class Image extends Common
{
    protected $table = "image";
    
    /**
     * @description:获取所有图片
     * @author wuyanwen(2016年8月26日)
     */
    public function get_all_image()
    {
        $where = [
            'i.status' => self::NORMAL_STATUS,
        ];
        
        $field = ['i.*','c.name as cname'];
        return $this->alias('i')
                    ->join('columns c','i.cid = c.id AND c.status=0','left')
                    ->where($where)
                    ->field($field)->order("add_time DESC")->paginate(15);
    }
    /**
     * @description:上传图片
     * @author wuyanwen(2016年8月26日)
     * @param unknown $data
     * @return Ambigous <number, boolean, string>
     */
    public function add_image($data)
    {
        return $this->save($data);
    }
    
    /**
     * @description:查询一张图片信息
     * @author wuyanwen(2016年9月5日)
     * @param unknown $id
     * @return \think\static
     */
    public function get_one_image($id)
    {
        $where = [
            'id' => $id,
            'status' => self::NORMAL_STATUS,
        ];
        
        return $this::get($where);
    }
    /**
     * @description:删除图片
     * @author wuyanwen(2016年8月26日)
     */
    public function delete_image($id)
    {
        
        $imageInfo = $this->get_one_image($id);

        $imagePath = '.'.$imageInfo['path'].'/'.$imageInfo['file_name'];
  
        $ret = $this->where(['id' => $id])->delete();
        
       
        if($ret && unlink($imagePath)){   
            return true;
        }else{
            return false;
        }
    }
    
    /**
     * @description:批量删除
     * @author wuyanwen(2016年9月5日)
     * @param unknown $ids
     */
    public function delete_all_image($ids)
    {
        foreach($ids as $vo)
        {
            $imageInfo = $this->get_one_image($vo);
            
            $imagePath = '.'.$imageInfo['path'].'/'.$imageInfo['file_name'];
            
            $ret = $this->where(['id' => $vo])->delete();
            
            $result = unlink($imagePath);
            
            if(!$ret || !$result){   
                return false;
            }
        }
        
        return true;
    }
}