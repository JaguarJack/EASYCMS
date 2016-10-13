<?php
namespace app\admin\model;

class Visitor extends Common
{
    protected $table = 'visit_time';
    
    /**
     * @description:查询条件
     * @author wuyanwen(2016年8月18日)
     * @param unknown $w
     */
    public function get_visit_data($w)
    {
        return $this->alias('v')
                    ->join('visit_ip vi','vi.id = v.ip_id','left')
                    ->field('SUM(pv) as pv,count(ip_id) as ip,v.date_time,v.visit_time')
                    ->where($w)
                    ->group('date_time')
                    ->select();
    }
}