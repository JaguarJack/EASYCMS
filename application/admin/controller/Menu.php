<?php
namespace app\admin\controller;

class Menu extends Base{
    
    /**
     * 菜单列表
     */
    public function index()
    {
        /* @var $menuModel \app\admin\Model\Menu.php */
        $menuModel = model('Menu');
                
        $column = get_column($menuModel->get_menu_list());
        return $this->fetch('index',[
            'column' => $column,
        ]);
    }
    
    /**
     * 添加菜单
     */
    public function addmenu()
    {
        /* @var $menuModel app\admin\Model\Menu.php */
        $menuModel = model('Menu');
        
        if(is_post()){
            $data = input('post.','','trim');
            $data['menu_link'] = strtolower($data['menu_link']);
            $result = $menuModel->add_menu($data);
            if($result){
                $this->success("添加成功",url('menu/index'));
            }else{
                $this->error("添加失败");
            }
        }else{
            $fmenu = $menuModel->get_fmenu();
            return $this->fetch('addmenu',[
                'fmenu' => $fmenu,
            ]);
        }
    }
    
    
    /**
     *编辑菜单
     */
    public function editmenu()
    {
        /* @var $menuModel /admin/Model/Menu.php */
        $menuModel = model('Menu');
        if(is_post()){
            $data = input('post.','','trim');
            $data['menu_link'] = strtolower($data['menu_link']);
            if($menuModel->edit_menu($data)){
                $this->success("编辑成功!");
            }else{
                $this->error("编辑失败!");
            }
        }else{
            $id = input('get.id','','intval');
            $menuinfo = $menuModel->get_one($id);

            return $this->fetch('editmenu',[
                'menuinfo' => $menuinfo,
                'id'        => $id,
            ]);
        }
    }
    
    /**
     * @description:开启或是关闭功能
     * @author wuyanwen(2016年8月9日)
     */
    public function changeStatus()
    {
        $id = input('post.id','','intval');
        /* @var $menuModel /admin/Model/Menu.php */
        $menuModel = model('Menu');
        
        $menuinfo = $menuModel->get_one($id);
        
        //显示就变为不显示
        $on = $menuinfo['on'] == 1 ? 2 : 1;
        
        if($menuinfo->getoff_or_geton($id, $on)){
            $this->returnJSON(['show'=>$on]);
        }
    }
    /**
     * 删除菜单
     */
    public function delmenu()
    {
        $id = input('post.id','','intval');
        /* @var $menuModel /admin/Model/Menu.php */
        $menuModel = model('Menu');

        //判断是否有子菜单
        if($menuModel->get_son_menu($id)){
            $this->returnJSON('', '请先删除所有子菜单后,再进行该操作', config('code.error'));
        }
        
        //删除
        if($menuModel->del_menu($id)){
            $this->returnJSON('',"删除成功",config('code.success'));
        }else{
            $this->returnJSON('', '删除失败', config('code.error'));
            
        }
    }
}