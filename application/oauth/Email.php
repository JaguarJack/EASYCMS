<?php
namespace app\oauth;

class Email
{
    private $mail_address;
    private $userName;
    private $password;
    private $from;//发送的邮箱地址
    public function __construct($mail_address,$userName,$password,$from)
    {
        $this->mail_address = $mail_address;
        $this->userName     = $userName;
        $this->password     = $password;
        $this->from         = $from;
        require "/Mailer/PHPMailerAutoload.php";
    }
    /**
     * @description:发送邮件
     * @author wuyanwen(2016年9月9日)
     * @param unknown $to
     * @param unknown $subject
     * @param unknown $body
     * @param string $Attachment
     */
    public function sendEmail($to,$subject,$body,$Attachment="")
    {
            //邮件服务器信息配置
            $mail = new \PHPMailer();
            $mail -> ISSMTP();  //设置邮件发送协议 smtp
            $mail -> CharSet = "utf-8";  //设置邮件编码
            $mail->Mailer    = "smtp";
            $mail -> Port = 465;
            $mail -> Host = $this->mail_address;
            $mail -> SMTPAuth = true;  //设置phpmail发送邮件是否需要验证(username&&password)
        
            if($mail -> SMTPAuth){
                $mail -> Username = $this->userName;
                $mail -> Password = $this->password;
            }
            $mail -> From = $this->from; //来源from
            $mail -> IsHTML(true);
            
            //发送邮件
            $mail -> Addaddress($to);
            $mail -> Subject = $subject;
            $mail -> Body = $body;
            //发送附件
            if(!empty($Attachment)){
                $mail->AddAttachment($Attachment,$Attachment);
            }
            if(!$mail -> Send()){
                echo "发送失败！";
                echo $mail -> ErrorInfo;
            }else{
                echo "邮件已经发送！";
            }

        
       
    }
    
    //$arr = array("1454346617","634285693","353830223","1160689507","415877405","1924826300","917859822","915984408","395341146","961623681");
    
    //$content = "请马上点击以下注册确认链接，激活你的帐号！
		 // <a href=\"本站域名/index.php?m=User&a=check&keyCode=hP1Px7GtcDh5%2F8dy9LU7nQ%3D%3D&setTime=\"".time().">点击验证</a>";
    
    //$str = file_get_contents("a.html");
    
    //foreach($arr as $vo){
       // sendMail($vo."@qq.com","今天好多好吃的！",$str);
       // echo "<br />";
} 