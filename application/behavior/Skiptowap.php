<?php
namespace app\behavior;
class Skiptowap
{
    public function run()
    {
       /*  if(stripos($_SERVER['HTTP_USER_AGENT'],"android")!=false||stripos($_SERVER['HTTP_USER_AGENT'],"ios")!=false||stripos($_SERVER['HTTP_USER_AGENT'],"wap")!=false)
        {
           
            $www = "http://wap.cms.com";
            
            $require_uri = $_SERVER['REQUEST_URI'];

            if($_SERVER["SERVER_NAME"] == "www.cms.com"){
                header("Location:".$www);
            }
            if($_SERVER["SERVER_NAME"] == "wap.cms.com" && $require_uri == '/'){
                exit;
            }
            $url = $www.$require_uri;
            
            header("Location:".$url);
        } */
    }
}