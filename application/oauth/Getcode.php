<?php
namespace app\oauth;
use app\oauth\Code\top\TopClient;
use app\oauth\Code\top\request\AlibabaAliqinFcSmsNumSendRequest;
class Getcode
{
    private  $app_key = "23450795";
    private  $sersct  = "467bea83f9cd38d4f3e72016da05faed";
    private  $sign    = "Easy管理系统";
    private  $setSmsTemplateCode = "SMS_14755126";
    
    public function __construct()
    {
        require "Code/TopSdk.php";
    }
    /**
     * @description:获取短信验证码
     * @author wuyanwen(2016年9月9日)
     */
    public function getMobileCode($code,$name,$phone)
    {
        $smsparam = "{\"code\":\"".$code."\",\"name\":\"".$name."\"}";
        
        $topClient = new TopClient();
        $smsNumSend = new AlibabaAliqinFcSmsNumSendRequest();
        $topClient->appkey = $this->app_key;
        $topClient->secretKey = $this->sersct;
        
        $smsNumSend->setExtend("123456");
        $smsNumSend->setSmsType("normal");
        $smsNumSend->setSmsFreeSignName($this->sign);
        
        $smsNumSend->setSmsParam($smsparam);
        $smsNumSend->setRecNum($phone);
        $smsNumSend->setSmsTemplateCode($this->setSmsTemplateCode);
        $result = $topClient->execute($smsNumSend);
        
        return $result;
    }
}