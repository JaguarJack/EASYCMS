<?php
namespace app\admin\controller;
use app\validate\ValidateRule;
class Collect extends Base
{
    private $content;
    /**
     * description:采集器首页
     *@author:wuyanwen
     *@时间:2016年8月20日
     */
    public function index()
    {
        /* @var $collectModel \app\admin\model\Collect */
        $collectModel = model('Collect');
        $rulelist = $collectModel->get_all_rule();

        return $this->fetch('index',[
            'rule' => $rulelist,
        ]); 
    }
    
    public function addRule()
    {
        if(is_post()){
            $data = input('post.','','trim');

            $validate = new ValidateRule();
            //验证表单字段
            if(!$validate->check_self($data)){
                $msg = $validate->getError();
                $this->returnJSON(['name'=>$msg[0]],$msg[1],config('code.validate_code'));
            }
            //如果是单页采集，则删除Page_url;
            if($data['is_page'] == 1){
                $data['page_url'] == '';
            }
            /* @var $collectModel \app\admin\model\Collect */
            $collectModel = model('Collect');
            if($collectModel->add_rule($data)){
                $this->returnJSON('','添加成功',config('code.success'));
            }else{
                $this->returnJSON('','添加失败',config('code.success'));
            }
            
        }else{
            return $this->fetch('addrule');
        }
    }
    
    public function editRule()
    {
        /* @var $collectModel \app\admin\model\Collect */
        $collectModel = model('Collect');
        if(is_post()){
            $data = input('post.','','trim');
            $validate = new ValidateRule();
            if($data['is_page'] == 1){
                $data['page_url'] = '';
            }

            //验证表单字段
            if(!$validate->check_self($data)){
                $msg = $validate->getError();
                $this->returnJSON(['name'=>$msg[0]],$msg[1],config('code.validate_code'));
            }
            /* @var $collectModel \app\admin\model\Collect */
            $collectModel = model('Collect');
            if($collectModel->edit_rule($data) === false){
                $this->returnJSON('','编辑失败',config('code.error'));
            }else{
                $this->returnJSON('','编辑成功',config('code.success'));
            }
        }else{
            $id = input('param.id/d');
            $ruleInfo = $collectModel->get_one_rule($id);

            return $this->fetch('editrule',[
                'id' => $id,
                'rule' => $ruleInfo,
            ]);            
        }
     }
     
     public function collectArticle()
     {
         /* @var $columnModel \app\admin\model\Column */
         $columnModel = model('Column');
         $columnList = get_column($columnModel->get_all_column());
         $id = input('param.id/d');
         
         return $this->fetch('collectarticle',[
             'column' => $columnList,
             'id' => $id,
         ]);
     }
    /**
     * description:采集文章
     *@author:wuyanwen
     *@时间:2016年8月20日
     */
    public function doCollectArticle()
    {
        set_time_limit(0);
        /* @var $collectModel \app\admin\model\Collect */
        $collectModel = model('Collect');
        /* @var $columnModel \app\admin\model\Column */
        $columnModel = model('Column');
        
        $id = input('post.id/d');
        $cid = input('post.cid/d');
        $url = input('post.url');
        
        //获取顶级栏目的ID
        $fid = $columnModel->get_one_column($cid)['fid'];
        if(!$fid){
            $this->returnJSON(['name'=>'cid'],'顶级栏目无法采集',config('code.validate_code'));
        }    
        //获取采集规则
        $rule = $collectModel->get_one_rule($id);

        $lastPage = input('post.lastPage/d') ? : $rule['last_collect_page'];
        if($rule['is_paginate'] != $columnModel->where(['id'=>$cid])->value('is_paginate'))
        {
            $this->returnJSON(['name'=>'cid'],'该栏目采集的内容分页不符合要求',config('code.validate_code'));
        }
        if($rule['is_paginate'] == 1){
            $this->add_article($cid,$fid,$url,$lastPage,$rule,$id);
        }else{
            $this->add_p_article($cid, $fid, $url, $lastPage, $rule, $id);
        }
    }
    /**
     * @description:匹配标题
     * @author wuyanwen(2016年8月23日)
     * @param unknown $title_rule
     * @param unknown $data
     * @return boolean|unknown
     */
    private function matcnTitle($title_rule,$data)
    {
        preg_match_all($title_rule, $data,$title);
        
        if(!$title[1]) return false;
        
        return $title;
        
    }
    
    private function matchContent($content_rule,$data)
    {
        preg_match_all($content_rule, $data,$content);
        
        if(!$content[1] || $content[1] == "") return false;
        
        return $content[1];
    }
    
    /**
     * @description:匹配图片
     * @author wuyanwen(2016年8月26日)
     * @param unknown $data
     * @return unknown
     */
    private  function matchImg($data)
    {
        $img_rule = "/<img.*?src=\"(.*?)\".*?>/ism";
        preg_match_all($img_rule,$data,$match);
        if($match[1]){
            return $match[1];
        }
    }
    
    /**
     * @description:获取数据
     * @author wuyanwen(2016年8月26日)
     * @param unknown $data
     * @param unknown $site_url
     * @param unknown $cid
     * @param unknown $fid
     * @param unknown $title
     * @return multitype:string unknown NULL Ambigous <string, unknown>
     */
    private function getData($data,$site_url,$cid,$fid,$title)
    {
        $imgArr = $this->matchImg($data);
        if($imgArr){
            foreach($imgArr as $vo){
                $imgpath = downloadImg($vo, $site_url);
                if(!$imgpath) continue;
                $data = str_ireplace($vo,$imgpath, $data);
            }
        }
        
        $datas = [
            'cid'         => $cid,
            'fid'         => $fid,
            'thumbimg'    => isset($imgpath) ? $imgpath : '',
            'title'       => $title,
            'content'     => htmlspecialchars($data),
            'create_time' => time(),
            'intro'       => mb_substr(strip_tags($data), 0,100,'utf-8'),
            'author'      => 'admin',
        ];
        
        return $datas;
    }
    
    /**
     * @description:组装分页内容
     * @author wuyanwen(2016年9月23日)
     */
    private function get_content_data($_content_url,$articel_url,$content_rule,$content)
    {

        for($i=2;$i<20;$i++){
            $content_url = substr($articel_url,0,-5).$_content_url.$i.'.html';
            $data = get_curl($content_url);
            $_content = $this->matchContent($content_rule, $data)[0];
            if(!$_content || $_content == ''){
                break;
            }
            $content .= $_content;
        }
        
        return preg_replace("/<a[^>]*>(.*)<\/a>/isU",'${1}',convUtf($content));
    }
    /**
     * @description:删除规则
     * @author wuyanwen(2016年8月23日)
     */
    public function delRule()
    {
        /* @var $collectModel \app\admin\model\Collect */
        $collectModel = model('Collect');
        
        $id = input('param.id/d');
        if($collectModel->del_rule($id)){
            $this->returnJSON('','删除成功',config('code.success'));
        }else{
            $this->returnJSON('','删除失败',config('code.error'));
        }
    }
    
    /**
     * @description:添加单页内容文章
     * @author wuyanwen(2016年9月23日)
     */
    public function add_article($cid,$fid,$url,$lastPage,$rule)
    {
        /* @var $articleModel \app\admin\model\Articles */
        $articleModel = model("Articles");
        $title_rule = $rule['title_rule']; //标题规则
        $content_rule = $rule['content_rule'];//内容规则
        $site_url = $rule['site_url'];//采集站点地址
        //单页采集
        if($rule['is_page'] == 1){
            $data = get_curl($url);
            if(!$data){
                $this->error("页面未采集到任何相关内容!");
            }
        
            $title = $this->matcnTitle($title_rule, $data);
            $content = $this->matchContent($content_rule, $data);
        
            if(!$title){
                $this->error("标题未采集到,检查标题正则是否正确?");
            }
        
            if(!$content){
                $this->error("内容未采集到,检查内容采集正则是否正确?");
            }
            
            $content = $content[0];
            if($rule['content_url']){
                $content = $this->get_content_data($rule['content_url'],$vo, $content_rule, $content);
            }
               
            $data = $this->getData($content, $site_url, $cid, $fid, convUtf($title[1]));
            $imgpath = null;
            
            if($articleModel->add_article($data)){
                $this->success("采集成功",url('Collect/index'));
            }else{
                $this->error("采集失败");
            }
            //分类采集
        }else if($rule['is_page'] == 2){
            //记录采集信息
            $error = [];
            //分页采集
            if($rule['page_url']){
                $lastNewPage = 0;
                for($i=0;$i<$rule['page_num'];$i++){
                    $lastNewPage = $i+$lastPage;
                     
                    $url = $rule['page_url'].$lastNewPage;
                    $data = get_curl($url);
        
                    if(!$data){
                        $this->error("第".($i+1)."页面为采集到任何相关内容!");
                    }
        
                    $title = $this->matcnTitle($title_rule, $data);
        
                    if(count($title) < 1){
                        $this->error("第".($i+1)."标题未采集到,检查标题正则是否正确?");
                    }
                    if(!is_array($title[1])){
                        $this->error("第".($i+1)."标题未采集到,检查标题正则是否正确?");
                    }
        
                    foreach($title[1] as $key => $vo){
        
                        if(strpos($vo,'http') === false){
                            $vo = $site_url.$vo;
                        }
        
                        $data = get_curl($vo);
                         
                        if(!$data){
                            $this->error("第".($i+1)."页未采集到任何相关内容!");
                        }
                        //判断端文章否采集过
                        if($articleModel->is_exist_title($title[2][$key])){
                            $error[$i ? :''.$key] = '<span style="color:red;">【'.$title[2][$key]."】</span>该文章已经采集过";
                            continue;
                        }
                        $content = $this->matchContent($content_rule, $data);
        
                        if(!$content){
                            $error[$i ? :''.$key]['url'] = $url."内容未采集到,检查内容采集正则是否正确?";
                            continue;
                        }
                        
                        $content = $content[0];
                        if($rule['content_url']){
                            $content = $this->get_content_data($rule['content_url'],$vo,$content_rule, $content);
                        }
                        
                        $data = $this->getData($content, $site_url, $cid, $fid, $title[2],$key);
                        $imgpath = null;
                        if(!$articleModel->add_article($data,2)){
                            $error[$i ? :''.$key] = '<span style="color:red;">【'.$title[2][$key]."】</span>采集失败";
                            continue;
                        }else{
                            $error[$i ? :''.$key] = '<span style="color:green;">【'.$title[2][$key]."】</span>采集成功";
                            continue;
                        }
                    }
                }
                $collectModel->save(['last_collect_page'=> $lastNewPage],['id' => $id]);
            }else{
                //单分类采集
                $data = get_curl($url);
                if(!$data){
                    $this->returnJSON(''.'页面未采集到任何相关内容!');
                }
                $title = $this->matcnTitle($title_rule, $data);
        
                if(!$title){
                    $this->returnJSON(''.'标题未采集到,检查标题正则是否正确?');
                }
        
                foreach($title[1] as $key => $vo){
                    //如果连接没有http:则拼装连接
                    if(strpos($vo,'http') === false){
                        $vo = $site_url.$vo;
                    }
                    if(strpos($vo,'html') === false){
                        continue;
                    }
                    $data = get_curl($vo);
        
                    if(!$data){
                        $this->error("页面为采集到任何相关内容!");
                    }
        
                    //标题转码
                    $article_title = convUtf($title[2][$key]);
        
                    //判断文章是否采集过
                    if($articleModel->is_exist_title($article_title)){
                        $error[$key] = '<span style="color:red;">【'.$article_title."】</span>该文章已经采集过";
                        continue;
                    }
        
                    $content = $this->matchContent($content_rule, $data);
        
                    if(!$content){
                        $error[$key]['url'] = $url."内容未采集到,检查内容采集正则是否正确?";
                        continue;
                    }
                    
                    //组装内容分页
                    $content = $content[0];
                    if($rule['content_url']){
                        $content = $this->get_content_data($rule['content_url'], $vo,$content_rule, $content);
                    }
        
                    $data = $this->getData($content, $site_url, $cid, $fid, $article_title);
                    $imgpath = null;
        
                    if(!$articleModel->add_article($data,2)){
                        $error[$key] = '<span style="color:red;">【'.$article_title."】</span>采集失败";
                        continue;
                    }else{
                        $error[$key] = '<span style="color:green;">【'.$article_title."】</span>采集成功";
                        continue;
                    }
                }
            }
        
            $this->returnJSON($error,'',config('code.success'));
        }else{
            $this->error("没有该类型的采集???");
        }
    }
    
    
    public function add_p_article($cid,$fid,$url,$lastPage,$rule,$id)
    {
        $title_rule = $rule['title_rule']; //标题规则
        $content_rule = $rule['content_rule'];//内容规则
        $site_url = $rule['site_url'];//采集站点地址
        //单页采集
        if($rule['is_page'] == 1){
            $data = get_curl($url);
            if(!$data){
                $this->error("页面未采集到任何相关内容!");
            }
        
            $title = $this->matcnTitle($title_rule, $data);
            $content = $this->matchContent($content_rule, $data);
        
            if(!$title){
                $this->error("标题未采集到,检查标题正则是否正确?");
            }
        
            if(!$content){
                $this->error("内容未采集到,检查内容采集正则是否正确?");
            }
            $data = $this->getData($content[0], $site_url, $cid, $fid, $title[1],0);
            if($articleModel->add_article($data)){
                $this->success("采集成功",url('Collect/index'));
            }else{
                $this->error("采集失败");
            }
            //分类采集
        }else if($rule['is_page'] == 2){
            //记录采集信息
            $error = [];
            //分页采集
            if($rule['page_url']){
                $lastNewPage = 3;
                for($i=0;$i<$rule['page_num'];$i++){
                    $lastNewPage = $i+3;
                    //默认分类结尾为.html
                    $url = $rule['page_url'].$lastNewPage.'.html';
                    
                    $data = get_curl($url);
                    
                    if(!$data){
                        $this->error("第".($i+1)."页面为采集到任何相关内容!");
                    }

                    $title = $this->matcnTitle($title_rule, $data);
        
                    if(count($title) < 1){
                        $this->error("第".($i+1)."标题未采集到,检查标题正则是否正确?");
                    }
                    if(!is_array($title[1])){
                        $this->error("第".($i+1)."标题未采集到,检查标题正则是否正确?");
                    }
        
                    foreach($title[1] as $key => $vo){
        
                        if(strpos($vo,'http') === false){
                            $vo = $site_url.$vo;
                        }
        
                        $data = get_curl($vo);
                         
                        if(!$data){
                            $this->error("第".($i+1)."页未采集到任何相关内容!");
                        }
                        $article_title = convUtf($title[2][$key]);
                          //判断文章是否采集过
                        $where = ['title' => $article_title];
                        $result = \think\Db::table('p_article')->where($where)->find();
                        if($result){
                            $error[$key] = '<span style="color:red;">【'.$article_title."】</span>该文章已经采集过";
                            continue;
                        }
                        $content = $this->matchContent($content_rule, $data);
        
                        if(!$content){
                            $error[$i ? :''.$key]['url'] = $url."内容未采集到,检查内容采集正则是否正确?";
                            continue;
                        }
                        
                        $p_article_datas = [
                            'cid'         => $cid,
                            'fid'         => $fid,
                            'title'       => $article_title,
                            'create_time' => time(),
                            'author'      => 'admin',
                        ];
                        
                        $p_data='';
                        //首先添加文章信息
                        $aid = \think\Db::table('p_article')->insertGetId($p_article_datas);
                        
                        $content = preg_replace("/<a[^>]*>(.*)<\/a>/isU",'${1}',convUtf($content[0]));
                        
                        $p_data = $this->get_p_content($content, $aid,$site_url);
                        
                        
                        \think\Db::table('p_content')->insert($p_data[0]);
                        $intro = mb_substr(strip_tags($content), 0,100,'utf-8');
                        
                        if($aid){
                            if($rule['content_url']){
                                for($i=2;$i<20;$i++){
                                    $content_url = substr($vo,0,-5).$rule['content_url'].$i.'.html';
                                    $data = get_curl($content_url);
                                    $content =convUtf($this->matchContent($content_rule, $data)[0]);
                                    if(!$content || $content == ''){
                                        break;
                                    }
                                    $p_data = $this->get_p_content($content, $aid,$site_url);
                                    \think\Db::table('p_content')->insert($p_data[0]);
                                }
                            }
                            
                            $p_data[1]['intro'] = $intro;
                            \think\Db::table('p_article')->where(['id'=>$aid])->setField($p_data[1]);
                            $error[$i ? :''.$key] = '<span style="color:green;">【'.$article_title."】</span>采集成功";
                            continue;
                        }else{
                            $error[$i ? :''.$key] = '<span style="color:red;">【'.$article_title."】</span>采集失败";
                            continue;
                        }                        
                    }
                }
                model('Collect')->save(['last_collect_page'=> $lastNewPage],['id' => $id]);
            }else{
                //单分类采集
                $data = get_curl($url);

                if(!$data){
                    $this->returnJSON(''.'页面未采集到任何相关内容!');
                }

                $title = $this->matcnTitle($title_rule, $data);
        
                if(!$title){
                    $this->returnJSON(''.'标题未采集到,检查标题正则是否正确?');
                }  
                foreach($title[1] as $key => $vo){

                    //如果连接没有http:则拼装连接
                    if(strpos($vo,'http') === false){
                        $vo = $site_url.$vo;
                    }
                    if(strpos($vo,'html') === false){
                        continue;
                    }
                    $data = get_curl($vo);
                    
                    if(!$data){
                        $error[$key] = "页面为采集到任何相关内容!";
                        continue;
                    }

                    //标题转码
                    $article_title = convUtf($title[2][$key]);
        
                    //判断文章是否采集过
                    $where = ['title' => $article_title];
                    $result = \think\Db::table('p_article')->where($where)->find();
                    if($result){
                        $error[$key] = '<span style="color:red;">【'.$article_title."】</span>该文章已经采集过";
                        continue;
                    }

                    $content = $this->matchContent($content_rule, $data);
                 
                    if(!$content){
                        $error[$key] = $url."内容未采集到,检查内容采集正则是否正确?";
                        continue;
                    }
                    $p_article_datas = [
                        'cid'         => $cid,
                        'fid'         => $fid,
                        'title'       => $article_title,
                        'create_time' => time(),
                        'author'      => 'admin',
                    ];
                    
                    $p_data='';
                    //首先添加文章信息
                    $aid = \think\Db::table('p_article')->insertGetId($p_article_datas);
                    
                    $content = preg_replace("/<a[^>]*>(.*)<\/a>/isU",'${1}',convUtf($content[0]));
                    $p_data = $this->get_p_content($content, $aid,$site_url);
                    
                    \think\Db::table('p_content')->insert($p_data[0]);
                   
                    $intro = mb_substr(strip_tags($content), 0,100,'utf-8');
                    $p_data[1]['intro'] = $intro;
                    \think\Db::table('p_article')->where(['id'=>$aid])->setField($p_data[1]);
                    
                    if($aid){
                        if($rule['content_url']){
                            for($i=2;$i<20;$i++){
                                $content_url = substr($vo,0,-5).$rule['content_url'].$i.'.html';
                                $data = get_curl($content_url);
                                $content =convUtf($this->matchContent($content_rule, $data)[0]);
                                $content = preg_replace("/<a[^>]*>(.*)<\/a>/isU",'${1}',$content);
                                if(!$content || $content == ''){
                                    break;
                                }
                                $p_data = $this->get_p_content($content, $aid,$site_url);
                                \think\Db::table('p_content')->insert($p_data[0]);
                            }
                        }
    
                        $error[$key] = '<span style="color:green;">【'.$article_title."】</span>采集成功";
                        continue;
                    }else{
                        $error[$key] = '<span style="color:red;">【'.$article_title."】</span>采集失败";
                        continue;
                    }

                }
            }
        
            $this->returnJSON($error,'',config('code.success'));
        }else{
            $this->error("没有该类型的采集???");
        }
    }
    
    /**
     * @description:处理文章内容
     * @author wuyanwen(2016年9月23日)
     * @param unknown $content
     * @param unknown $aid
     */
    private function get_p_content($content,$aid,$site_url)
    {
        
        $content = preg_replace("/<img.*?>.*?<noscript>(.*)<\/noscript>/isU",'${1}',$content);
        
        $imgArr = $this->matchImg($content);

        if($imgArr){
            foreach($imgArr as $vo){
                $imgpath = downloadImg($vo, $site_url);
                if(!$imgpath) continue;
                $content = str_ireplace($vo,$imgpath, $content);
            }
        }
        //文章内容
        $datas = [
            'aid'         => $aid,
            'content'     => htmlspecialchars($content),
        ];
        //文章缩略图和简介
        $_datas = [
            'thumbimg'    => isset($imgpath) ? $imgpath : '',
        ];
        
        return [$datas,$_datas];
    }
}