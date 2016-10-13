<?php
namespace app\admin\controller;

class Image extends Base
{
    /**
     * @description:图片管理首页
     * @author wuyanwen(2016年8月26日)
     */
    public function index()
    {
        /* @var $imageModel \app\admin\model\Image */
        $imageModel = model('Image');
        $list = $imageModel->get_all_image();

        return $this->fetch('index',[
            'list' => $list,
        ]);
    }
    
    
    /**
     * @description:上传图片
     * @author wuyanwen(2016年8月26日)
     */
    public function addImage()
    {
        if(is_post()){
            /* @var $imageModel \app\admin\model\Image */
            $imageModel = model("Image");
            $cid = input('param.cid');
            $file = request()->file('file');
            $imagePath = config('image_path');  
            $info = $file->move($imagePath);            
            if($info){
                $data = [
                    'cid'        => $cid, 
                    'file_name'  => $info->getFilename(),
                    'add_time'   => time(),
                    'path'       => substr($imagePath.'/'.date('Ymd'),1),
                ];
                if(!$imageModel->add_image($data)){
                    return false;
                }
            }else{
               return false;
            }
        }else{
            /* @var $columnModel \app\admin\model\Column */
            $columnModel = model("Column");
            $columnList = get_column($columnModel->get_all_column());
            
            return $this->fetch('addimage',[
                'column' => $columnList,
            ]);
        }
    }
    
    /**
     * @description:删除图片
     * @author wuyanwen(2016年8月26日)
     */
    public function delImage()
    {
        $id = input('post.id');
       /* @var $imageModel \app\admin\model\Image */
       $imageModel = model("Image");
     
       //批量删除
       if(strpos($id,',')){
           $idArray = explode(',',substr($id,0,-1));

           if($imageModel->delete_all_image($idArray)){
               $this->returnJSON('','删除成功',config('code.success'));
           }else{
               $this->returnJSON('','删除失败',config('code.error'));
           }
       }
       
       //单张删除
       if($imageModel->delete_image($id)){
           $this->returnJSON('','',config('code.success'));
       }else{
           $this->returnJSON('','删除失败',config('code.error'));
       }
    }
}