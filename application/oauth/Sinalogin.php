<?php 
namespace app\oauth;
use app\admin\model\Setconfig;
class Sinalogin
{
    private $login_page_url = "https://api.weibo.com/oauth2/authorize";//QQ登录界面
    private $get_accessToken_url = "https://api.weibo.com/oauth2/access_token";//后去token的url
    private $get_openId_url = 'https://api.weibo.com/oauth2/get_token_info';//获取用户授权信息
    private $get_user_info = "https://api.weibo.com/2/users/show.json";//获取用户信息的url
    private $app_id;
    private $app_key;
    private $redirect_url = 'http://www.rllady.com/home/index/sinaLogin';//回调地址
    private $access_token;
    private $scope = 'all';
    
    public function __construct()
    {
        $baseconfigModel = new Setconfig();
        $sinaInfo = unserialize($baseconfigModel->get_config_by_name('oauth_sina')['data']);   
        $this->app_id = $sinaInfo['key'];
        $this->app_key = $sinaInfo['serect'];
    }
    //QQ登录页面
    private function get_qq_login_page()
    {
        $state = md5(rand(1,1000));
        $query = [
            'client_id' => $this->app_id,
            'redirect_uri' => $this->redirect_url,
            'state' => $state,
            'scope' => $this->scope,
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
            'client_id' => $this->app_id,
            'client_secret' => $this->app_key,
            'grant_type' => 'authorization_code',
            'code'       => $code,
            'redirect_uri' => $this->redirect_url, 
        ];
        //依据网上的说法，要将参数组装到url中，post方式提交空数据即可
        $url = $this->get_accessToken_url .'?'.http_build_query($query);

        return $this->post_curl($url,[]);

    }
    
    //获取token
    private function get_access_token()
    {
        $data = $this->get_code();
        //参数组装数组
        $arr = json_decode($data,true);
 
        $this->access_token = $arr["access_token"];
        $url = $this->get_openId_url .'?'.http_build_query(['access_token' => $arr["access_token"]]);
        return $this->post_curl($url,[]);
    }
    
    //获取openid&&获取用户信息
    public function getUserInfo()
    {
        $data = $this->get_access_token();
        //截取json字符串
        $arr = json_decode($data,true);

        $query = [
            'access_token' => $this->access_token,
            'uid' => $arr['uid'],
        ];
 
        $data = $this->get_curl($this->get_user_info, http_build_query($query));
        
        return json_decode($data,true);
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
    
    //curl post请求
    private function post_curl($url,$query)
    {
        $curl = curl_init();        
        //设置抓取的url       
        curl_setopt($curl, CURLOPT_URL, $url);
        //设置头文件的信息作为数据流输出        
        curl_setopt($curl, CURLOPT_HEADER, 0);       
        //设置获取的信息以文件流的形式返回,而不是直接输出.        
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);        
        //设置post方式提交       
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $query);
        //执行命令        
        $data = curl_exec($curl);       
        //关闭URL请求        
        curl_close($curl);
        
        //显示获得的数据       
        return $data;
    }
}