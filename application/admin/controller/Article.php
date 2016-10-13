<?php
namespace app\admin\controller;
use app\validate\ValidateArticle;
class Article extends Base
{
    /**
     * @description:文章管理首页
     * @author wuyanwen(2016年8月16日)
     */
    public function index()
    {
        /* @var $articleModel \app\admin\model\Articles */
        $articleModel = model('Articles');
        $list = $articleModel->get_all_article();
        return $this->fetch('index',[
            'list' => $list,
        ]);
    }
    
    /**
     * @description:添加文章
     * @author wuyanwen(2016年8月16日)
     */
    public function addArticle()
    {
        if(is_post()){
            /* @var $articleModel \app\admin\model\Articles */
            $articleModel = model('Articles');
            $data = input('post.','','trim');            
            $validate = new ValidateArticle();
            if(!$validate->check_self($data)){
                $msg = $validate->getError();
                $this->returnJSON(['name'=>$msg[0]],$msg[1],config('code.validate_code'));
            }
            //获取文章所属栏目的id以及其顶级菜单的id
            $cids = explode('-',$data['cid']);
            $data['cid'] = intval($cids[0]);
            $data['fid'] = intval($cids[1]);
            //顶级栏目无法添加文章
            if($data['fid'] == 0){
                $this->returnJSON(['name'=>'cid'],'顶级栏目无法添加文章',config('code.validate_code'));
            }
       
            //如果有图片
            $thumb_img_path = config('thumb_image_path');
            if(strlen($_FILES['doc-form-file']['name']) > 1){
                $file = request()->file("doc-form-file");
                //获取图片后缀
                $ext = explode('.',$_FILES['doc-form-file']['name'])[1];
                
                //缩略图片路径
                if(!is_dir($thumb_img_path))
                {
                    $ret = mkdir($thumb_img_path,0777,true);
                }
                
                $imgName = time().'.'.$ext;
                $thumb_image_path = substr($thumb_img_path,1).'/'.$imgName;
                
                //限制上传图片类型
                if(!$file->checkImg()){
                    $this->returnJSON(['name'=>'file'],'图片格式不正确',config('code.validate_code'));
                }
                
                //缩略图片,等比例缩放图片
                $image = \think\Image::open($file);               
                $image->thumb(150, 150)->save($thumb_image_path); 
                $data['thumbimg'] = $thumb_image_path;
            }
            $data['create_time'] = time();
            if(strstr($data['content'],'_ueditor_page_break_tag_')){
                $this->addParticle($data);
            }else{
                if($articleModel->add_article($data)){
                    $this->returnJSON(['url'=>url('Article/index')],'添加成功',config('code.success'));
                }else{
                    $this->returnJSON('','添加失败',config('code.error'));
                }
            }
        }else{
            /* @var $columnModel \app\admin\Model\Column */
            $columnModel = model('Column');
            
            $columnList = get_column($columnModel->get_all_son_column());
            return $this->fetch('addarticle',[
                'column' => $columnList,
            ]);
        }
    }
    
    /**
     * @description:添加分页文章
     * @author wuyanwen(2016年10月8日)
     */
    private function addParticle($data)
    {
       $content = explode('_ueditor_page_break_tag_',$data['content']);
       $end_content = strip_tags(end($content));
       

       //如果最后一个元素为空，则删除
       if($end_content == ''){
           array_pop($content);
       }
       /* @var $pcontentModel \app\admin\model\Pcontent */
       $pcontentModel = model('Pcontent');
       /* @var $particleModel \app\admin\model\Particle */
       $particleModel = model('Particle');
       
       unset($data['content']);
       
       $particleModel->add_data($data);//添加标题
       $aid = $particleModel->id;//获取自增ID
       if($aid){
           $result = $pcontentModel->add_data($content,$aid);//添加内容
           if(!$result){
               $this->returnJSON('','添加失败',config('code.error'));
           }else{
               $this->returnJSON(['url'=>url('Article/pArticle')],'添加成功',config('code.success'));
           }
       }else{
           $this->returnJSON('','添加失败',config('code.error'));
       }
    }
    /**
     * @description:编辑文章
     * @author wuyanwen(2016年8月16日)
     */
    public function editArticle()
    {
        /* @var $articleModel \app\admin\model\Articles */
        $articleModel = model('Articles');
        if(is_post()){
            $data = input('post.','','trim');
            $validate = validate('ValidateArticle');
            if(!$validate->check_self($data)){
                $msg = $validate->getError();
                $this->returnJSON(['name'=>$msg[0]],$msg[1],config('code.validate_code'));
            }

            //获取文章所属栏目的id以及其顶级菜单的id
            $cids = explode('-',$data['cid']);
            $data['cid'] = intval($cids[0]);
            $data['fid'] = intval($cids[1]);
            //顶级栏目无法添加文章
            if($data['fid'] == 0){
                $this->returnJSON(['name'=>'cid'],'顶级栏目无法添加文章',config('code.validate_code'));
            }
            //如果有图片
            if(strlen($_FILES['doc-form-file']['name']) > 1){
                $file = request()->file("doc-form-file");
                //获取图片后缀
                $ext = explode('.',$_FILES['doc-form-file']['name'])[1];

                $thumb_image_path = config('thumb_image_path').'/'.time().'.'.$ext;
                
                //限制上传图片类型
                if(!$file->checkImg()){
                    $this->returnJSON(['name'=>'file'],'图片格式不正确',config('code.validate_code'));
                }
                
                //缩略图片,等比例缩放图片
                $image = \think\Image::open($file);
                $image->thumb(150, 150)->save($thumb_image_path);
                $data['thumbimg'] = substr($thumb_image_path,1);

                if(file_exists('.'.$data['img_path']) && $data['img_path']){
                    unlink('.'.$data['img_path']);
                }
                
            }
            unset($data['img_path']);
            $data['create_time'] = time();
            if($articleModel->edit_article($data) !== false){
                $this->returnJSON('','编辑成功',config('code.success'));
            }else{
                $this->returnJSON('','编辑失败',config('code.error'));
            }
        }else{
           /* @var $columnModel \app\admin\Model\Column */
           $columnModel = model('Column');
           $id = input('param.id/d');
           
           $article = $articleModel->get_one_article($id);
           $article['content'] = htmlspecialchars($article['content']);
           $columnList = get_column($columnModel->get_all_son_column());
           return $this->fetch('editarticle',[
               'column' => $columnList,
               'article' => $article,
               'id' => $id,
           ]);
        }
    }
    
    /**
     * @description:设置推荐文章
     * @author wuyanwen(2016年8月16日)
     */
    public function setRecommend()
    {
        $id = input('post.id/s');
        $type =input('post.type/d');
        
        $data = explode('-',$id);
        
        $is_top = $data[1] == 1 ? 2 : 1;
        
        if($type == 1){
            /* @var $articleModel \app\admin\model\Articles */
            $articleModel = model('Articles');
            if($articleModel->set_recommend($id, $is_top)){
                $this->returnJSON(['code'=>$is_top,'data'=>$data[0].'-'.$is_top]);
            }
        }else{
            /* @var $particleModel \app\admin\model\Particle */
            $particleModel = model('Particle');
            if($particleModel->set_recommend($id, $is_top)){
                $this->returnJSON(['code'=>$is_top,'data'=>$data[0].'-'.$is_top]);
            }
        }
       
    }
    
    /**
     * @description:删除文章
     * @author wuyanwen(2016年8月16日)
     */
    public function delArticle()
    {
        $id = input('post.id/d');

        /* @var $articleModel \app\admin\model\Articles */
        $articleModel = model('Articles');
        
        if($articleModel->del_article($id)){
            $this->returnJSON('','',config('code.success'));
        }
    }
    
    /**
     * @description:设置单页面
     * @author wuyanwen(2016年8月17日)
     */
    public function setPageIndex()
    {
        /* @var $articleModel \app\admin\model\Articles */
        $articleModel = model('Articles');
        $page = $articleModel->get_page_content();
 
        return $this->fetch('setPageIndex',[
            'page' => $page,
        ]);
     
    }
    
    public function addPage()
    {
  
        if(is_post()){
            $data = input('post.');
            $data['is_page'] = 2;
            /* @var $articleModel \app\admin\model\Articles */
            $articleModel = model('Articles');
            
            //判断单页面是否存在
            if($articleModel->is_exist_page($data['cid'])){
                $this->returnJSON('','该页面已经存在，请勿重复添加',config('code.error'));
            }
            
            if($articleModel->add_article($data)){
                $this->returnJSON('','添加成功',config('code.success'));
            }else{
                $this->returnJSON('','添加失败',config('code.error'));
            }
        }else{
            /* @var $columnModel \app\admin\Model\Column */
            $columnModel = model('Column');
            
            $pageColumn = $columnModel->get_page_column();
            
            return $this->fetch('addPage',[
                'pageColumn' => $pageColumn,
            ]);
        }
        
    }
    /**
     * @description:配置单页面
     * @author wuyanwen(2016年8月17日)
     */
    public function editPage()
    {
        /* @var $articleModel \app\admin\model\Articles */
        $articleModel = model('Articles');
        if(is_post()){
            $data = input('post.');

            //编辑单页面
            if($articleModel->edit_article($data) !== false){
                $this->returnJSON('','编辑成功',config('code.success'));
            }else{
                $this->returnJSON('','编辑失败',config('code.error'));
            }
        }else{
            $id = input('param.id/d');
            /* @var $columnModel \app\admin\Model\Column */
            $columnModel = model('Column');
            
            $pageColumn = $columnModel->get_page_column();
            $page = $articleModel->get_one_article($id);
            
            return $this->fetch('editPage',[
                'pageColumn' => $pageColumn,
                'id' => $id,
                'page' => $page,
            ]);
        }
        
    }
    
    /**
     * @description:关闭页面
     * @author wuyanwen(2016年8月17日)
     */
    public function closePage()
    {
        /* @var $columnModel \app\admin\model\Column */
        $columnModel = model('Column');
        
        $id = input('post.id/d');
        $columnInfo = $columnModel->get_one_page_column($id);

        $status = $columnInfo['status'] == 1 ? 0 : 1;

        if($columnModel->del_column($id,$status)){
            $this->returnJSON(['status' => $status]);
        }
    }
    
    /**
     * @description:分页文章
     * @author wuyanwen(2016年9月23日)
     */
    public function pArticle()
    {
        /* @var $particleModel \app\admin\model\Particle */
        $particleModel = model('Particle');
        
        $article = $particleModel->get_p_article();
        
        return $this->fetch('pArticle',[
           'article' => $article, 
        ]);
    }
    /**
     * @description:获取分页文章内容
     * @author wuyanwen(2016年10月8日)
     */
    public function getArticleContent()
    {
        $id = input('post.id/d');
        /* @var $pcontentModel \app\admin\model\Pcontent */
        $pcontentModel = model('Pcontent');
                
        $content_id = $pcontentModel->get_content_id($id);
       
        return $this->fetch('pContent',[
           'content_id' => $content_id,
        ]);
          
    }
    /**
     * @description:修改编辑内容
     * @author wuyanwen(2016年10月8日)
     */
    public function editContent()
    {
        if(is_get()){
            $id = input('param.id/d');
            /* @var $pcontentModel \app\admin\model\Pcontent */
            $pcontentModel = model('Pcontent');
            
            $content = $pcontentModel->get_content($id);
            
            return $this->fetch('editContent',[
                'content' => $content,
            ]);
        }else{
            $data = input('post.');
             /* @var $pcontentModel \app\admin\model\Pcontent */
            $pcontentModel = model('Pcontent');
            
            $result = $pcontentModel->get_content($data);

            if($result === false){
                $this->error("编辑失败");
            }else{
                $this->success("编辑成功",url('article/pArticle'));
            }
        }
        
    }
    /**
     * @description:删除分页文章
     * @author wuyanwen(2016年10月8日)
     */
    public function delArticleContent()
    {
        $aid = input('post.id/d');
        /* @var $pcontentModel \app\admin\model\Pcontent */
        $pcontentModel = model('Pcontent');
        /* @var $particleModel \app\admin\model\Particle */
        $particleModel = model('Particle');
        
        $content_id = $pcontentModel->get_content_id($aid);
        $result = $pcontentModel->del_content($content_id);
        
        
      /*   $particleModel::startTrans();

        
        // 提交事务
        if($result && $result1){
            $particleModel::commit();
            $this->returnJSON('','',config('code.success'));
        }else{
            // 回滚事务
            $particleModel::rollback();
            $this->returnJSON('','删除失败',config('code.error'));
        }  */ 

        if($result){
            $result = $particleModel->del_particle($aid);
            if($result){
                $this->returnJSON('','',config('code.success'));
            }else{
                $this->returnJSON('','删除失败',config('code.error'));
            }
        }else{
            $this->returnJSON('','删除失败',config('code.error'));
        }
    }
    
    /**
     * @description:生成sitemap
     * @author wuyanwen(2016年8月18日)
     */
    public function sitemap()
    {
        $aid = \think\Db::table('p_article')->field(['id',"create_time"])->select();

        $site = config('site_url');
        $str = "<urlset xmlns='http://www.sitemaps.org/schemas/sitemap/0.9'>\r\n";
        foreach($aid as $vo)
        {
            $str .= "<url>\r\n";    
            $str .= "<loc>".$site.'detail-'.$vo['id'].'.html'."</loc>\r\n";    
            $str .= "<priority>1.0 </priority>\r\n</url>\r\n"; 
        }
        $str .= "</urlset>";
        
        $sitemap = './sitemap.xml';
        $myfile = fopen($sitemap, "w+");
        
        $result = fwrite($myfile, $str);

        fclose($myfile);
        
        if($result){
            $this->returnJSON('','sitemap生成成功',config('code.success'));
        }else{
            $this->returnJSON('','sitemap生成失败',config('code.error'));
        }

    }
    
    /**
     * @description:推送百度
     * @author wuyanwen(2016年8月18日)
     */
    public function postUrl()
    {
        $aid = \think\Db::table('p_article')->field(['id'])->order('id DESC')->limit(0,2000)->select();
        $site = config('site_url');

        foreach($aid as $vo){
            $urls[] = $site.'detail-'.$vo['id'].'.html';
        }

        $api = 'http://data.zz.baidu.com/urls?site=www.rllady.com&token=fik0G9y68QRPq1bQ';

        $ch = curl_init();
        $options =  array(
            CURLOPT_URL => $api,
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => implode("\n", $urls),
            CURLOPT_HTTPHEADER => array('Content-Type: text/plain'),
        );
        
        curl_setopt_array($ch, $options);
        
        $result = json_decode(curl_exec($ch),true);

        if($result['success']){
            $this->returnJSON('','百度推送成功',config('code.success'));
        }else{
            $this->returnJSON('','百度推送失败',config('code.error'));
        }
    }
}