<?php
namespace app\behavior;
use app\admin\model\Log;
class RecordLog
{
    /**
     * @description:记录日志
     * @author wuyanwen(2016年8月11日)
     */
    public function action_init($url)
    {
            /* @var $logModel \app\admin\Model\Log */
            $logModel = new Log();
            
            $data = [
                'user_id' => session('uid'),
                'user_name' => session('user'),
                'option_url' => $url,
                ];

          return $logModel->add_log_record($data);
    }
}