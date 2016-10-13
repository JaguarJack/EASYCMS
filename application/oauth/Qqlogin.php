<?php 
namespace app\oauth;
use app\admin\model\Setconfig;
class Qqlogin
{
    private $login_page_url = "https://graph.qq.com/oauth2.0/authorize";//QQ登录界面
    private $get_accessToken_url = "https://graph.qq.com/oauth2.0/token";//后去token的url
    private $get_openId_url = 'https://graph.qq.com/oauth2.0/me';//获取openid的url
    private $get_user_info = "https://graph.qq.com/user/get_user_info";//获取用户信息的url
    private $app_id;
    private $app_key;
    private $redirect_url = 'http://www.rllady.com/home/index/qqLogin';//回调地址
    private $access_token;
    
    public function __construct()
    { 
        $baseconfigModel = new Setconfig();
        $qqInfo = unserialize($baseconfigModel->get_config_by_name('oauth_qq')['data']);
        $this->app_id = $qqInfo['key'];       
        $this->app_key = $qqInfo['serect'];
    }
    //QQ登录页面
    private function get_qq_login_page()
    {
        $state = md5(rand(1,1000));
        $query = [
            'response_type' => 'code',
            'client_id' => $this->app_id,
            'redirect_uri' => $this->redirect_url,
            'state' => $state,
        ];
       
        session('state',$state);//保存state验证
        $url= $this->login_page_url.'?'.http_build_query($query);
        header("Location:$url");
        exit;
    }
    
    //获取code
    private function get_code()
    {
        $code = input('get.code','','string');
        if(!$code){
            $this->get_qq_login_page();
        }
        $state = input('get.state','','string');

        if($state != session('state')){
            echo "state is wrong!";
            exit;
        }
        session('state',null);
        $query = [
            'grant_type' => 'authorization_code',
            'code'       => $code,
            'client_secret' => $this->app_key,
            'client_id' => $this->app_id,
            'redirect_uri' => $this->redirect_url, 
        ];
        
        return $this->get_curl($this->get_accessToken_url, http_build_query($query));

    }
    
    //获取token
    private function get_access_token()
    {
        $data = $this->get_code();
        //参数组装数组
        parse_str($data,$arr);
        
        $this->access_token = $arr["access_token"];
        
        return $this->get_curl($this->get_openId_url, http_build_query(['access_token' => $arr["access_token"]]));
    }
    
    //获取openid&&获取用户信息
    public function getUserInfo()
    {
        $data = $this->get_access_token();
        //截取json字符串
        $str = substr($data,strpos($data,'(')+1,-3);

        $arr = json_decode($str,true);
        
        $query = [
            'access_token' => $this->access_token,
            'oauth_consumer_key' => $this->app_id,
            "openid" => $arr['openid'],
        ];
 
        $data = $this->get_curl($this->get_user_info, http_build_query($query));
        
        $data = json_decode($data,true);
        
        $data['openid'] = $arr['openid'];
        
        return $data;
    }
    
    //curl GET请求
    private function get_curl($url,$query)
    {
        $url_request = $url.'?'.$query;
        $curl = curl_init();
        
        //设置抓取的url        
        curl_setopt($curl, CURLOPT_URL, $url_request);        
        //设置头文件的信息作为数据流输出        
        curl_setopt($curl, CURLOPT_HEADER, 0);        
        //设置获取的信息以文件流的形式返回,而不是直接输出.        
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);       
        //执行命令        
        $data = curl_exec($curl);        
        //关闭URL请求       
        curl_close($curl);
        return $data;
        
    }
}