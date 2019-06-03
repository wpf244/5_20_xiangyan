<?php
namespace app\admin\controller;

class Package extends BaseAdmin
{
    public function lister()
    {
        
        $list=db("package")->order(["sort asc","id desc"])->paginate(20,false,['query'=>requset()->param()]);

        $this->assign("list",$list);

        $page=$list->render();

        $this->assign("page",$page);
        
        return $this->fetch();
    }
}