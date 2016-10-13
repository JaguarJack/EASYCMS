<?php
namespace app\oauth;

use app\oauth\Qn\Qiniu\Auth;
use app\oauth\Qn\Qiniu\Storage\UploadManager;
class Qnupload
{
    private $appkey;
    private $secretkey;
    private $imgurl;
    private static $instances;
    
    public function __construct()
    {
        $this->appkey = "dw1jkmtSNjxyPkMdyBfVeaWzCAOiMSOTk35adV8W";
        $this->secretkey = "_KfsmnV8d-rlYtABD27xF7y-ZNxblvdk9_VVZ67g";
        $this->imgurl = "http://7tsyl4.com1.z0.glb.clouddn.com/";
    }
    public function uploadImage($path,$ext)
    {   
     
        if(!self::$instances){
           self::$instances = require 'Qn/autoload.php';
        }
        $auth = new Auth($this->appkey, $this->secretkey);
        // 要上传的空间
        $bucket = 'aimeihuli';
        
        // 生成上传 Token
        $token = $auth->uploadToken($bucket);
        
        // 要上传文件的本地路径
        $filePath = $path;
        
        // 上传到七牛后保存的文件名
        $key = time().rand(10000,99999).$ext;
        // 初始化 UploadManager 对象并进行文件的上传
        $uploadMgr = new UploadManager();
        
        // 调用 UploadManager 的 putFile 方法进行文件的上传
        list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);

        if ($err !== null) {
            return false;
        } else {
            return $this->imgurl.$ret['key'];
        }
        
    }
}