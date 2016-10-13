<?php
namespace app\admin\model;

class Articles extends Common
{
    protected $table = 'article';
    
    /**
     * @description:获取所有文章
     * @author wuyanwen(2016年8月16日)
     * @param unknown $where
     */
    public function get_all_article($w=null)
    {
        $where = [
            'status' => self::NORMAL_STATUS,
        ];
        if($w) array_merge($where,$w);
        
        return $this->where($where)->order('create_time DESC')->paginate(15);
    }
    
    /**
     * @description:获取一篇文章
     * @author wuyanwen(2016年8月16日)
     * @param unknown $id
     */
    public function get_one_article($id)
    {
         $where = [
             'id' => $id,
             'status' => self::NORMAL_STATUS,
         ];

         return $this::get($where);
    }
    
    /**
     * @description:添加文章
     * @author wuyanwen(2016年8月16日)
     * @param unknown $data
     */
    public function add_article($data,$type=1)
    {
        if($type == 1){
            return $this->save($data);
        }else{
           return  $this->data($data,true)->isUpdate(false)->save();
        }
        
    }
    
    /**
     * @description:编辑文章
     * @author wuyanwen(2016年8月16日)
     * @param unknown $data
     */
    public function edit_article($data)
    {
            $where = [
              'id' => $data['id'],
              'status' => self::NORMAL_STATUS,  
            ];
            unset($data['id']);
            return $this->save($data,$where);
    }
    
    /**
     * @description:设置推荐
     * @author wuyanwen(2016年8月17日)
     * @param unknown $id
     * @param unknown $is_top
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
     * @description:操作文章
     * @author wuyanwen(2016年8月16日)
     * @param unknown $id
     * @param unknown $type 1删除 2推荐
     */
    public function del_article($id,$type=1)
    {
        $where = ['id' => $id,];
        
        $data = ['status' => 1,];

        return $this->save($data,$where);
    }
    
    /**
     * @description:查询单页面是否存在
     * @author wuyanwen(2016年8月17日)
     * @return boolean
     */
    public function is_exist_page($cid)
    {
        $where = [
            'cid' => $cid,
            'is_page' => 2,
        ];
        
        return $this::get($where) ? true : false;
    }
    /**
     * @description:获取单页面
     * @author wuyanwen(2016年8月17日)
     * @return Ambigous <multitype:\think\static , \think\false>
     */
    public function get_page_content()
    {
        $where = [
            'a.is_page' => 2,            
        ];
        
        return $this->alias('a')  
                    ->join('columns c','a.cid=c.id','left')
                    ->field('c.name,c.status as c_status,a.*')
                    ->where($where)
                    ->select();
    }
    
    /**
     * @description:判断文章是否采集过
     * @author wuyanwen(2016年8月24日)
     * @param unknown $title
     */
    public function is_exist_title($title)
    {
         $where = [
             'title'  => $title,
             'status' => 0
         ];
         
         return $this::get($where) ? true : false;
    }
}