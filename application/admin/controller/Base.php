<?php
namespace app\admin\controller;
use think\Controller;
use app\admin\controller\Auth;
class Base extends Controller
{
    /**
     *  description:构造方法
     *  @author: wuyanwen(2016年8月5日)
     */
    public function __construct()
    {
       parent::__construct();
       $controller = request()->controller();
       $action     = request()->action();
       $url = "{$controller}/{$action}";

       if($controller != 'Common' && $controller != 'Index'){
           $this->isLogin();
           //功能关闭提示
           /* @var $menuModel \app\admin\Model\Menu */
           $menuModel = model('Menu');
           $menuinfo = $menuModel->menu_is_on($url);
           if($menuinfo['on'] == 2){
               if(request()->isAjax()){
                   $this->returnJSON('', '该功能已经关闭',config('code.auth_code'));
               }else{
                   $this->error("该功能已经关闭");
               }
           }
           //权限判断
            $auth = new Auth();
            if(!$auth->check($url, session('uid'))){
               if(request()->isAjax()){
                  $this->returnJSON('', '没有权限操作',config('code.auth_code'));
               }else{
                  $this->error("没有权限操作"); 
               }             
            }

            //记录日志
            \think\Hook::listen('action_init',$url);
       }
       return true;
    }
    /**
     * @todo 是否登录
     */
    protected function isLogin()
    {
        if(!session('user')){
            echo "<script>window.parent.location.href='/admin/index/login.html';</script>";
        }else{
            return true;
        }
    }
    
    /**
     * JSON数据返回
     */
    protected  function returnJSON($data,$msg='',$code='')
    {
        $this->result($data,$code,$msg,'JSON');
        exit();
    }
}