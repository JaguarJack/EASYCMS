<?php
namespace app\admin\controller;

class Admanager extends Base
{
  /**
   * @description:广告管理首页
   * @author wuyanwen(2016年8月18日)
   */
  public function index()
  {

      return $this->fetch('index');
  } 
  
  /**
   * @description:添加广告
   * @author wuyanwen(2016年8月18日)
   */
  public function addAd()
  {
      return $this->fetch('addAd');
  }
}