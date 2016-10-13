<?php
namespace app\admin\controller;

class Common extends Base
{
    public function left()
    {
        return $this->fetch();
    }
    
    public function top()
    {
        return $this->fetch();
    }
    
    
    public function main()
    {
        /* @var $userModel \app\admin\model\User */
        $userModel = model("User");
        $userList = $userModel->get_lastest_login_user(5);
        $serverInfo = [
            '远程IP地址'  => $_SERVER['REMOTE_ADDR'],
            'Apache信息' => $_SERVER["SERVER_SOFTWARE"],
            'PHP版本'    => PHP_VERSION,
            'Mysql版本'  => "5.0.11",
         '是否远程文件获取' => ini_get("allow_url_fopen") ? "支持" : "不支持",
            '最大执行时间' => ini_get("max_execution_time")."秒",
            '最大支持上传' => ini_get("file_uploads") ? ini_get("upload_max_filesize") : "Disabled",
            '服务器时间'   => date("Y年-m月-d日 H时:i分:s秒",time()),
        ];

        return $this->fetch('main',[
            'userList' => $userList,
            'serverInfo' => $serverInfo,
        ]);
    }
}