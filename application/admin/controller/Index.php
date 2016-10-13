<?php
namespace app\admin\controller;

class Index extends Base
{
    public function index()
    {
        $this->isLogin();
        return $this->fetch();
    }
    
    public function login()
    {
       if(is_post()){
           $data = input('post.','','trim');
           $captcha = new \think\captcha\Captcha();
           if (!$captcha->check($data['captcha'])) {
               $this->returnJSON('','验证码错误',config('code.code_error'));
           }
           $user = model('Authuser');
           $result = $user->is_login_user($data['name'],md5($data['password']));
           if(!$result){
               $this->returnJSON('','登录失败,请检查用户名和密码',config('code.error'));
           }else{
               session('user',$data['name']);
               session('uid',$result['id']);
               $this->returnJSON('','登录成功',config('code.success'));  
           }           
       }else{

            return $this->fetch();
       }
    }
    
    public function logout()
    {
        if(session('user')){
            session('user',null);
            session('uid',null);
        }
    }
    
    /**
     * @description:日志列表
     * @author wuyanwen(2016年8月11日)
     */
    public function logRecord()
    {
        /* @var $logModel \app\admin\Model\Log */
        $logModel = model('Log');
        /* @var $userModel \app\admin\Model\Authuser */
        $userModel = model("Authuser");
        
        $user_id    = input('user/d');
        $start_time = input('start_time');
        $end_time    = input('end_time');
        
        $where = [];
        $query = [];
        if($user_id){
            $where['user_id'] = $user_id;
            $query['user_id'] = $user_id;
        }
        if($start_time){
            $where['create_time'] =['egt',strtotime($start_time)];
            $query['start_time'] = $start_time;
        }
        if($end_time){
            $where['create_time'] =['elt',strtotime($end_time)];
            $query['end_time'] = $end_time;
        }
        if($start_time && $end_time){
            $where['create_time'] =['between',[strtotime($start_time),strtotime($end_time)]];
            $query['start_time'] = $start_time;
            $query['end_time'] = $end_time;
        }

        $loglist = $logModel->get_all_log_record($where,$query);
        $userlist = $userModel->get_user_list(true);
        
        return $this->fetch('logrecord',[
            'loglist'    => $loglist,
            'start_time' => $start_time ? : null,
            'end_time'   => $end_time ? : null,
            'user_id'    => $user_id ? : null,
            'userlist'   => $userlist,
        ]);
    }
    
    
    public function siteFlow()
    {
        $where['date_time'] = date("Y-m-d",time());

        if($start_time = input('post.start_time')){
            $where['date_time'] = ['gt',$start_time];
        }
        
        if($end_time = input('post.end_time')){
            $where['date_time'] = ['lt',input('post.end_time')];
        }
        
        if($start_time && $end_time){
            $where['date_time'] = ['between',[$start_time,$end_time]];
        }

        /* @var $visitorModel \app\admin\model\visitor */
        $visitorModel = model('visitor');
        
        $datas = $visitorModel->get_visit_data($where);
        
        if(!$datas){
            $this->error("未查询到任何数据");
        }
        foreach($datas as $vo)
        {
            $time[] = $vo['date_time'];
            $data[] = (int)$vo['pv'];
        }
           
        return $this->fetch('siteflow',[
            'time' => json_encode($time),
            'data' => json_encode($data),
            'start_time' => $start_time ? : '',
            'end_time' => $end_time ? : '',
        ]);
    }
    
    
    /**
     * @description:清楚日志
     * @author wuyanwen(2016年9月5日)
     */
    public function cleanLog()
    {
        /* @var $logModel \app\admin\model\Log */
        $logModel = model("Log");
        
        if($logModel->delete_log()){
            $this->returnJSON('','日志已清空',config('code.success'));
        }else{
            $this->returnJSON('','日志清除失败',config('code.error'));
        }
    }
    
    /**
     * @description:清理缓存
     * @author wuyanwen(2016年9月28日)
     */
    public function clear_cache($dir = './runtime/')
    {
        
        if(!is_dir($dir)) return;
        
        //读取目录
		$dirArr = scandir($dir);
		foreach($dirArr as $vo){
			if($vo != '.' && $vo != ".."){
				$path = $dir.$vo;
				if(!is_dir($path)){
					unlink($path);
				}else {   
				    //如果是目录,递归本身删除下级目录                
				    $this->clear_cache($path.'/');   
				}   
			}
		}
        
        return $this->returnJSON('','缓存已清理',config('code.success'));
    }
}
