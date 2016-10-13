<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
/**
 * 判断url请求的方法
 */
function is_post()
{
    return $_SERVER['REQUEST_METHOD'] == 'POST' ? true : false;
}

function is_get()
{
    return $_SERVER['REQUEST_METHOD'] == 'GET' ? true : false;
}           

/**
 * description: 递归菜单
 * @author: wuyanwen(2016年8月7日)
 * @param unknown $array
 * @param number $fid
 * @param number $level
 * @param number $type 1:顺序菜单 2树状菜单
 * @return multitype:number
 */
function get_column($array,$type=1,$fid=0,$level=0)
{
    $column = [];
    if($type == 2)
        foreach($array as $key => $vo){
            if($vo['fid'] == $fid){
                $vo['level'] = $level;
                $column[$key] = $vo;
                $column [$key][$vo['id']] = get_column($array,$type=2,$vo['id'],$level+1);
            }
    }else{
        foreach($array as $key => $vo){
            if($vo['fid'] == $fid){
                $vo['level'] = $level;
                $column[] = $vo;
                $column =array_merge($column, get_column($array,$type=1,$vo['id'],$level+1));
            }
        }
    }
    return $column;
}

/**
 * @description:获取基本配置信息
 * @author wuyanwen(2016年8月12日)
 * @param unknown $name
 */
function get_base_config_info($name)
{
    /* @var $baseConfigModel \app\admin\Model\Setconfig */
    $baseConfigModel = model('Setconfig');
    
    echo $baseConfigModel->get_config_by_name($name)['data'];
}

/**
 * @description:curl GET请求
 * @author wuyanwen(2016年8月25日)
 * @param unknown $url
 * @return unknown
 */
function get_curl($url)
{

    $curl = curl_init();
    //设置抓取的url
    curl_setopt($curl, CURLOPT_URL, $url);
    //设置头文件的信息作为数据流输出

    curl_setopt($curl, CURLOPT_HEADER,0);
    //设置获取的信息以文件流的形式返回,而不是直接输出.
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    //执行命令
    $data = curl_exec($curl);
    //关闭URL请求
    curl_close($curl);
    
    return $data;
}

/**
 * @description:post CURL请求
 * @author wuyanwen(2016年8月25日)
 * @param unknown $url
 * @param unknown $param
 * @return unknown
 */
function post_curl($url,$param)
{
    $curl = curl_init();
    //设置抓取的url
    curl_setopt($curl, CURLOPT_URL, $url);    
    //设置头文件的信息作为数据流输出  
    curl_setopt($curl, CURLOPT_HEADER, 1);   
    //设置获取的信息以文件流的形式返回,而不是直接输出.    
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);   
    //设置post方式提交
    curl_setopt($curl, CURLOPT_POST, 1);
    //设置post参数
    curl_setopt($curl, CURLOPT_POSTFIELDS, $param);
    $data = curl_exec($curl);  
    //关闭curl
    curl_close($curl);
    return $data; 
}
/**
 * @description:转换编码格式
 * @author wuyanwen(2016年8月23日)
 * @param unknown $str
 * @return unknown
 */
function convUtf($str){
    $encode = strtolower(mb_detect_encoding($str, array("ASCII","UTF-8","GB2312","GBK","BIG5","EUC-CN","CP936")));

    if ($encode != 'utf-8'){
        $str = iconv($encode,"utf-8",$str);
    }
    return $str;
}

/**
 * @description:下载
 * @author wuyanwen(2016年8月23日)
 */
function downloadImg($img_url,$site_url)
{  
    
    //图片链接是否有"?"
    $strpose = strpos($img_url,'?');
    if($strpose){
        $img_url = substr($img_url,0,$strpose);
    }
    //图片链接是否是完整的http链接
    if(strpos($img_url,'http') === false){
        $img_url = $site_url.$img_url;
    }
    
    //获取图片后缀
    $ext = substr($img_url,-4);
    if(!in_array($ext,['.png','.bmp','.gif','.jpg'])){
       $ext = '.jpeg'; 
    }
    $filenameext = time().rand(1000,10000).$ext;
    
    $remote_file_dir="./upload/download/".date("Y-m-d",time()).'/';
   
    if(!is_dir($remote_file_dir)){
        mkdir($remote_file_dir, 0777, true);
    }
    //图片路径
    $filename = $remote_file_dir.$filenameext;
    //curl下载图片
    $hander = curl_init();
    $fp = fopen($filename,'wb');
    curl_setopt($hander,CURLOPT_URL,$img_url);
    curl_setopt($hander,CURLOPT_FILE,$fp);
    curl_setopt($hander,CURLOPT_HEADER,0);
    curl_setopt($hander,CURLOPT_FOLLOWLOCATION,1);
    $result = curl_exec($hander);
    curl_close($hander);
    fclose($fp);   
    
    //默认采集网站的图片地址
    $imageurl = $img_url;
    
    $qiniu = qiniu_upload();
    if(filesize($filename) > 0){
        $imageurl = $qiniu->uploadImage($filename, $ext);
    }

    return $imageurl;

}

//实例化七牛类
function qiniu_upload()
{
    $qiniu = new \app\oauth\Qnupload();
    static $instance;
    if(!$instance){
        $instance = $qiniu;
    }
    return $qiniu;
}


//手机端访问判断
function check_wap(){
    // 先检查是否为wap代理，准确度高
    if(stristr($_SERVER['HTTP_VIA'],"wap")){
        return true;
    }
    // 检查浏览器是否接受 WML.
    elseif(strpos(strtoupper($_SERVER['HTTP_ACCEPT']),"VND.WAP.WML") > 0){
        return true;
    }
    //检查USER_AGENT
    elseif(preg_match('/(blackberry|configuration\/cldc|hp |hp-|htc |htc_|htc-|iemobile|kindle|midp|mmp|motorola|mobile|nokia|opera mini|opera |Googlebot-Mobile|YahooSeeker\/M1A1-R2D2|android|iphone|ipod|mobi|palm|palmos|pocket|portalmmm|ppc;|smartphone|sonyericsson|sqh|spv|symbian|treo|up.browser|up.link|vodafone|windows ce|xda |xda_)/i', $_SERVER['HTTP_USER_AGENT'])){
        return true;
    }
    else{
        return false;
    }
}

/**
 * @description:清楚文章a标签
 * @author wuyanwen(2016年9月28日)
 * @param unknown $start_article_id
 * @param unknown $end_article_id
 */
function clear_a($start_article_id,$end_article_id)
{
    $content = \think\Db::table('p_content')->where('aid','between',[$start_article_id,$end_article_id])->select();
    
    foreach($content as $key => $vo){
        $a = preg_replace("/<a[^>]*>(.*)<\/a>/isU",'${1}',html_entity_decode($vo['content']));
        $result = \think\Db::table('p_content')->where('id',$vo['id'])->setField('content', htmlspecialchars($a));
        if($result){
            echo "第".($key+1)."次成功<br/>";
        }
      
    }
}
