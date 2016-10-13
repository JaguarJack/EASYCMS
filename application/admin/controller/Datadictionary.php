<?php
namespace app\admin\controller;
use think\Db;
class Datadictionary extends Base
{
    /**
     *description:数据字典首页
     *@author:wuyanwen
     *@时间:2016年10月8日
     */
    public function index()
    {
        $tables = Db::query("show tables");
        return $this->fetch('index',[
            'tables' => $tables,
        ]);
    }
    
    /**
     *description:获取表信息
     *@author:wuyanwen
     *@时间:2016年10月8日
     *@return mixed
     */
    public function getTableInfo()
    {
        $table_name = input('post.name');
        
        $tableInfo = Db::query("show full fields from {$table_name}");

        return $this->fetch('getTableInfo',[
            'tableInfo' => $tableInfo,
        ]);
    }
    
    /**
     *description:添加表字段
     *@author:wuyanwen
     *@时间:2016年10月8日
     */
    public function insertField()
    {
        $sql = input('post.sql');
        //ALTER TABLE `web_news`  ADD COLUMN `myqq` INT(1) NULL DEFAULT '0' AFTER `catid`;
    }
}