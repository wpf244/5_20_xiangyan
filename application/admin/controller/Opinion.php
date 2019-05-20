<?php
namespace app\admin\controller;

class Opinion extends  BaseAdmin
{
    public function lister()
    {
        
        $list=db("opinion")->alias("a")->field("a.*,b.nickname as name")->join("user b","a.uid = b.uid")->order("id desc")->paginate(20);

        $page=$list->render();

        $this->assign("page",$page);

        $this->assign("list",$list);

        
        return $this->fetch();
    }
    public function delete()
    {
        $id=\input('id');
        $re=db("opinion")->where("id=$id")->find();
        if($re){
            $res=db("opinion")->where("id=$id")->delete();
            $this->redirect('lister');
        }else{
            $this->redirect('lister');
        }
    }
}