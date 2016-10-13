<?php
namespace app\admin\controller;

class Friendlylink extends Base
{
    /**
     * @description:友情链接首页
     * @author wuyanwen(2016年8月9日)
     */
    public function index()
    {

        /* @var $linkModel \app\admin\Model\Link */
        $linkModel = model('Link');
        
        $list = $linkModel->get_all_link();
        
        return $this->fetch('index',[
            'list' => $list,
        ]);
    }
    
    /**
     * @description:添加链接
     * @author wuyanwen(2016年8月9日)
     */
    public function addlink()
    {
       if(is_post()){
           $data = input('post.','','trim');
           $data['type'] = 1;
           
           /* @var $linkModel \app\admin\Model\Link */
           $linkModel = model('Link');
           
           if($linkModel->add_link($data)){
               $this->success("添加成功");
           }else{
               $this->error("添加失败");
           }
       }else{
           return $this->fetch('addlink');
       }
    }
    
    /**
     * @description:修改链接
     * @author wuyanwen(2016年8月9日)
     */
    public function editlink()
    {
        /* @var $linkModel \app\admin\Model\Link */
        $linkModel = model('Link');
        if(is_post()){
             $data = input('post.','','trim');
             if($linkModel->edit_link($data)){
                 $this->success("编辑成功");
             }else{
                 $this->error("编辑失败");
             }
        }else{
            $id = input('get.id','','intval');
            $linkInfo = $linkModel->get_one_link($id);
            return $this->fetch('editlink',[
                'id' => $id,
                'linkInfo' => $linkInfo,
            ]);
        }
    }
    
    /**
     * @description:是否检测链接
     * @author wuyanwen(2016年8月9日)
     */
    public function isCheckLink()
    {
        $id = input('post.id','','trim');
        $data = explode('-',$id);
        
        /* @var $linkModel \app\admin\Model\Link */
        $linkModel = model('Link');
        $linkInfo = $linkModel->get_one_link($data[0]);
        if($data[1] == 1){
            $on = $linkInfo['is_check'] == 1 ? 2 : 1;
        }else{
            $on = $linkInfo['show'] == 1 ? 2 : 1;
        }
        
        if($linkModel->change_status($data[0], $on,$data[1])){
            $this->returnJSON(['show' => $on]);
        }
    }
    
    /**
     * @description:删除链接
     * @author wuyanwen(2016年8月9日)
     */
    public function dellink()
    {
        /* @var $linkModel \app\admin\Model\Link */
        $linkModel = model('Link');
        
        $id = input('post.id','','intval');
        
        $result = $linkModel::destroy(['id' => $id]);
        
        if($result){
            $this->returnJSON('','删除成功',config('code.success'));
        }else{
            $this->returnJSON('','删除失败',config('code.error'));
        }
    }
}