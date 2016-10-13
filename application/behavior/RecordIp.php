<?php
namespace app\behavior;
use app\home\model\GetIp;
use app\home\model\VisitIpTime;

class RecordIp
{
    private $getIpInfo = 'http://ip.taobao.com/service/getIpInfo.php';//淘宝获取IP接口
    public function run()
    {
        $ip  = request()->ip();
         
        $url = $this->getIpInfo.'?ip='.$ip;
        
        $module = request()->module();
        
        if($module == 'home'){
            $data = json_decode(file_get_contents($url),true);
            $ipModel = new GetIp();
            $visitTimeModel = new VisitIpTime();
            
            $visitTime = time();//访问时间
            
            $Ipdata = [
                    'ip' => $ip,
                  'area' => $data['data']['country'].'-'.$data['data']['city'],
                'is_old' => 1,
            ];
            
            $Timedata = [
                     'ip_id' => $ip,
                      'year' => date('Y',$visitTime),
                     'month' => date('m',$visitTime),
                       'day' => date('d',$visitTime),
                'date_time'  => date('Y-m-d',$visitTime),
                'visit_time' => $visitTime,
            ];
            
            $ipModel->insert_ip($Ipdata);
            $visitTimeModel->insert_visit_time($Timedata);
        }
    }
}