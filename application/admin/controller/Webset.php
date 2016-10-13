<?php
namespace app\admin\controller;
use app\validate\ValidateSiteInfo;
use app\validate\ValidateCompanyInfo;
use app\validate\ValidateSeoInfo;

class Webset extends Base
{
    /**
     * @description:网站设置
     * @author wuyanwen(2016年8月8日)
     */
    public function index()
    {
        return $this->fetch('index');
    }
    
    /**
     * @description:添加网站信息
     * @author wuyanwen(2016年8月10日)
     */
    public function addSiteInfo()
    {
        /* @var $baseConfigModel \app\admin\Model\Setconfig */
        $baseConfigModel = model('Setconfig');
        $data = input('post.','','trim');
        $validate = new ValidateSiteInfo();
        if(!$validate->check_self($data)){
           $msg = $validate->getError();
           $this->returnJSON(['name'=>$msg[0]],$msg[1],config('code.validate_code'));
        }

        //如果有图片
        if(strlen($_FILES['doc-form-file']['name']) > 1){
            $file = request()->file("doc-form-file");
            
            //获取图片后缀
            $ext = explode('.',$_FILES['doc-form-file']['name'])[1];
            //创建logo图片路径
            if(!is_dir(config('logo_path'))){
                $ret = mkdir(config('logo_path'),0777,true);
                if(!$ret){
                    $this->returnJSON('','请设置publi文件夹可写权限后,创建完毕后再关闭其可写权限',config('code.validate_code'));
                    
                }
            }
            //显示图片类型
            if(!$file->checkImg()){
                $this->returnJSON(['name'=>'file'],'图片格式不正确',config('code.validate_code'));
            }
            
            //删除旧的logo
            $logo_path = $baseConfigModel->get_config_by_name('logo');
            if(file_exists($logo_path))
            {
                unlink($logo_path);
            }
            
            //新的logo
            $logo_path = config('logo_path').'/logo.'.$ext;
            //缩略图片,等比例缩放图片
            $image = \think\Image::open($file);               
            //$image->thumb(150, 150)->save($logo_path);
            $image->save($logo_path);
            $data['logo'] = substr($logo_path,1);
        }
        
        if($baseConfigModel->set_config($data) != false){
            $this->returnJSON('','设置成功',config('code.success'));
        }else{
            $this->returnJSON('','设置失败',config('code.error'));       
        }
    }
    
    /**
     * @description:设置公司信息
     * @author wuyanwen(2016年8月11日)
     */
    public function setCompanyInfo()
    {
        /* @var $baseConfigModel \app\admin\Model\Setconfig */
        $baseConfigModel = model('Setconfig');
        
        $data = input('post.','','trim');
        //验证数据
        $validate = new ValidateCompanyInfo();
        if(!$validate->check_self($data)){
            $msg = $validate->getError();
            $this->returnJSON(['name'=>$msg[0]],$msg[1],config('code.validate_code'));
        }
        //插入数据
        if($baseConfigModel->set_config($data)){
            $this->returnJSON('','设置成功',config('code.success'));
        }else{
            $this->returnJSON('','设置失败',config('code.error'));
        }
    }
    
    /**
     * @description:设置seo信息
     * @author wuyanwen(2016年8月12日)
     */
    public function setSeoInfo()
    {
        /* @var $baseConfigModel \app\admin\Model\Setconfig */
        $baseConfigModel = model('Setconfig');
        
        $data = input('post.','','trim');
        //验证数据
        $validate = new ValidateSeoInfo();
        if(!$validate->check_self($data)){
            $msg = $validate->getError();
            $this->returnJSON(['name'=>$msg[0]],$msg[1],config('code.validate_code'));
        }
        //插入数据
        if($baseConfigModel->set_config($data)){
            $this->returnJSON('','设置成功',config('code.success'));
        }else{
            $this->returnJSON('','设置失败',config('code.error'));
        }
    }
    
    /**
     * @description:设置第三方登录信息
     * @author wuyanwen(2016年8月12日)
     */
    public function setthirdlogin()
    {
        /* @var $baseConfigModel \app\admin\Model\Setconfig */
        $baseConfigModel = model('Setconfig');
        
        if(is_post()){
            $data = input('post.','','trim');
            $key = $data['type'] ?   'oauth_qq' : 'oauth_sina';
            $datas[$key] = serialize($data);
            //插入数据
            if($baseConfigModel->set_config($datas)){
                $this->returnJSON('','设置成功',config('code.success'));
            }else{
                $this->returnJSON('','设置失败',config('code.error'));
            }
        }else{
            $oatuqq = unserialize($baseConfigModel->get_config_by_name('oauth_qq')['data']);
            $oatusina = unserialize($baseConfigModel->get_config_by_name('oauth_sina')['data']);
            return $this->fetch('setthirdlogin',[
                'qqinfo' => $oatuqq,
                'sinainfo' => $oatusina,
            ]);
        }
        
    }
    /**
     * @description:清理缓存
     * @author wuyanwen(2016年8月8日)
     */
    public function delcache($dir="./../runtime/")
	{
		if(!is_dir($dir)) return;
		$dirArr = scandir($dir);
		foreach($dirArr as $vo){
			if($vo != '.' && $vo != ".."){
				$path = $dir.$vo;
				if(!is_dir($path)){
					unlink($path);
					echo $path.'已经删除<br>';
				}else {   
			     //如果是目录,递归本身删除下级目录                
				    $this->delCache($path.'/');   
				}   
			}
		}
	}
	
	/**
	 * @description:设置栏目seo信息
	 * @author wuyanwen(2016年8月18日)
	 */
	public function setColumnSeo()
	{
	    if(is_post()){
	        /* @var $columnseoModel \app\admin\model\Columnseo */
	        $columnseoModel = model('Columnseo');
	        $data = input('post.');
	        if($columnseoModel->set_seo($data)){
	            $this->returnJSON('','设置成功',config('code.success'));
	        }else{
	            $this->returnJSON('','设置成功',config('code.error'));
	        }
	    }else{
	        /* @var $columnModel \app\admin\Model\Column */
	        $columnModel = model('Column');
	         
	        $columnList = get_column($columnModel->get_all_column());
	         
	        return $this->fetch('webset/setColumnSeo',[
	            'column' => $columnList,
	        ]);
	    }
	   
	}
	
	/**
	 * @description:获取栏目seo信息
	 * @author wuyanwen(2016年8月18日)
	 */
	public function getColumnSeo()
	{
	    $cid = input('post.cid/d');
	   /* @var $columnseoModel \app\admin\model\Columnseo */
	   $columnseoModel = model('Columnseo');
	   
	   $info = $columnseoModel->get_column_seo($cid);
	   
	   $data = [
	       'title' => $info['title'],
	       'keywords' => $info['keywords'],
	       'description' => $info['description'],
	   ];
	   
	   $this->returnJSON($data);
	}
}