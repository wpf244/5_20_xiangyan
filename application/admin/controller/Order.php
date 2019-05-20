<?php
namespace app\admin\controller;

class Order extends BaseAdmin
{
    public function lister()
    {
        
        $list=db("order")->alias("a")->field("a.*,b.name")->where(["a.is_delete"=>0,"a.status"=>0])->join("shop b","a.sid=b.id",'left')->order("a.id desc")->paginate(20);

        $page=$list->render();

        $this->assign("page",$page);

        $this->assign("list",$list);
        
        return $this->fetch();
    }
    public function delete()
    {
        $id=input("id");
        $re=db("order")->where("id",$id)->find();
        if($re){
           db("order")->where("id",$id)->setField("is_delete",-1);
        }
        $this->redirect("lister");
    }
    public function change()
    {
        $id=input("id");
        $re=db("order")->where("id",$id)->find();
        if($re){
           db("order")->where("id",$id)->setField("status",1);
        }
        $this->redirect("lister");
    }
    public function index()
    {
        
        $list=db("order")->alias("a")->field("a.*,b.name")->where(["a.is_delete"=>0,"a.status"=>1])->join("shop b","a.sid=b.id",'left')->order("a.id desc")->paginate(20);

        $page=$list->render();

        $this->assign("page",$page);

        $this->assign("list",$list);
        
        return $this->fetch();
    }
    public function deletes()
    {
        $id=input("id");
        $re=db("order")->where("id",$id)->find();
        if($re){
           db("order")->where("id",$id)->setField("is_delete",-1);
        }
        $this->redirect("index");
    }
}