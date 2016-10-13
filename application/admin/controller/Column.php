<?php
namespace app\admin\controller;

class Column extends Base
{
    /**
     * @description:前台栏目
     * @author wuyanwen(2016年8月9日)
     */
    public function index()
    {
        
        /* @var $columnModel \app\admin\Model\Column */
        $columnModel = model('Column');
        
        $columnList = get_column($columnModel->get_all_column());
        return $this->fetch('index',[
            'column' => $columnList,
        ]);
    }
    
    /**
     * @description:添加顶级栏目
     * @author wuyanwen(2016年8月9日)
     */
    public function addColumn()
    {
        if(is_post()){
            $data = input('post.','','trim');
            /* @var $columnModel \app\admin\Model\Column */
            $columnModel = model('Column');
            if($columnModel->add_column($data)){
                $this->success("添加成功",url('Column/index'));
            }else{
                $this->error("添加失败");
            }
        }else{
           return $this->fetch("addcolumn");
        }
    }
    
    /**
     * @description:添加子菜单
     * @author wuyanwen(2016年8月9日)
     */
    public function addSonColumn()
    {
        if(is_post()){
            $data = input('post.','','trim');
            /* @var $columnModel \app\admin\Model\Column */
            $columnModel = model('Column');

            if($columnModel->add_column($data)){
                $this->success("添加成功",url('Column/index'));
            }else{
                $this->error("添加失败");
            }
        }else{
            $id = input('get.id','','intval');

            return $this->fetch("addsoncolumn",[
                'id' => $id,
            ]);
        }
    }
    /**
     * @description:编辑栏目
     * @author wuyanwen(2016年8月9日)
     */
    public function editColumn()
    {
        /* @var $columnModel \app\admin\Model\Column */
        $columnModel = model('Column');
        if(is_post()){
            $data = input('post.','','trim');
            
            $columnInfo = $columnModel->get_one_column($data['id']);
            
            if($columnInfo['fid'] == 0 && $data['fid']){
                $this->error("顶级分类只能修改名称");
            }
            if($columnModel->edit_coluln($data)){
                $this->success("修改成功");
            }else{
                $this->error("修改失败");
            }
        }else{
           $id = input('get.id','','intval');
           
           $column = $columnModel->get_one_column($id);
           
           $topColumn = get_column($columnModel->get_all_column());
           
           return $this->fetch("editcolumn",[
               'column' => $column,
               'topColumn' => $topColumn,
               'id' => $id,
           ]);
        }
    }
    
    /**
     * @description:改变显示状态
     * @author wuyanwen(2016年8月9日)
     */
    public function changeStatus()
    {
        $id = input('post.id','','trim');
        /* @var $columnModel \app\admin\Model\Column */
        $columnModel = model('Column');
        
        $result = $columnModel->get_one_column($id);

        //显示就变为不显示
        $show = $result['show'] == 1 ? 2 : 1;

        if($columnModel->change_show($id, $show)){  
            $this->returnJSON(['show'=>$show]);
        }
        
    }
    /**
     * @description:删除栏目
     * @author wuyanwen(2016年8月9日)
     */
    public function delColumn()
    {
        $id = input('post.id','','intval');
        /* @var $columnModel \app\admin\Model\Column */
        $columnModel = model('Column');
        
        if($columnModel->get_son_column($id)){
            $this->returnJSON('', '请先删除该菜单的下所有子菜单', config('code.error'));
        }
        
        if($columnModel->del_column($id)){
            $this->returnJSON('', '删除成功', config('code.success'));
        }else{
            $this->returnJSON('', '删除失败', config('code.error'));
        }
    }
}

