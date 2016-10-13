<?php
namespace app\admin\controller;

class Routeconfig extends Common
{
    /**
     * description:路由配置首页
     *@author:wuyanwen
     *@时间:2016年9月18日
     */
    public function index()
    {
        $routeArr = include(APP_PATH.'/route.php');
        $routeArr['blog/:id'] = ['Blog/read', ['method' => 'get'], ['id' => '\d+']];

        $str = "<?php\n return".var_export($routeArr,true).";";
        $str = str_replace("array (", '[', $str);
        $str = str_replace(")", ']', $str);
        file_put_contents(APP_PATH.'/route.php', $str);
        return $this->fetch('index',[
            'routeArr' => $routeArr,
        ]);
    }
    
    /**
     * description:添加路由
     *@author:wuyanwen
     *@时间:2016年9月18日
     */
    public function addRoute()
    {
        
    }
    
    /**
     * description:修改路由规则
     *@author:wuyanwen
     *@时间:2016年9月18日
     */
    public function editRoute()
    {
        
    }
}