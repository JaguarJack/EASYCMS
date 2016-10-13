<?php
namespace app\admin\model;

class Authgroupaccess extends Common
{
    protected $table = "auth_group_access";
    
    /**
     * description:获取组别相关的成员
     * @author: wuyanwen(2016年8月7日)
     * @param unknown $groupId
     */
    public function get_group_relate_user($groupId)
    {
        $where = ['group_id' => $groupId];
        
        return $this::all($where);
    }
    
    /**
     * description:添加组别成员
     * @author: wuyanwen(2016年8月7日)
     * @param unknown $data
     */
    public function add_group_relate_user($data)
    {
        $userIds = $this->where(['group_id' => $data['groupid']])->field('uid')->select();
        
        $array = [];
        if(!$userIds){
            foreach($data['users'] as $key => $vo){
                $array[$key]['uid'] = $vo;
                $array[$key]['group_id'] = $data['groupid'];
            }
            return $this::saveAll($array);
        }else{
            foreach($userIds as $vo){
               $where = ['uid' => $vo['uid'],'group_id' => $data['groupid']];
               if(!$this->destroy($where)){
                   return false;
               }
            }
            if(count($data) > 1){
                foreach($data['users'] as $key => $vo){
                    $array[$key]['uid'] = $vo;
                    $array[$key]['group_id'] = $data['groupid'];
                }
                
                if(!$this::saveAll($array)){
                    return false;
                }
            }
            
            return true;
             
        }
    }
}