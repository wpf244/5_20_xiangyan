<?php
namespace app\admin\controller;

class Car extends BaseAdmin
{
    public function lister()
    {
        
        $list=db("car_dd")->alias("a")->field("a.*,b.nickname")->where(["a.status"=>0])->join("user b","a.uid=b.uid",'left')->order("a.id desc")->paginate(20);

        $page=$list->render();

        $this->assign("page",$page);

        $this->assign("list",$list);
        
        return $this->fetch();
    }
    public function change()
    {
        $id=input("id");
        $re=db("car_dd")->where("id",$id)->find();
        if($re){
           db("car_dd")->where("id",$id)->update(["status"=>1,"time"=>time()]);
        }
        $this->redirect("lister");
    }
    public function delete()
    {
        $id=input("id");
        $re=db("car_dd")->where("id",$id)->find();
        if($re){
           db("car_dd")->where("id",$id)->delete();
        }
        $this->redirect("lister");
    }
    public function index()
    {
        
        $list=db("car_dd")->alias("a")->field("a.*,b.nickname")->where(["a.status"=>1])->join("user b","a.uid=b.uid",'left')->order("a.id desc")->paginate(20);

        $page=$list->render();

        $this->assign("page",$page);

        $this->assign("list",$list);
        
        return $this->fetch();
    }
    public function deletes()
    {
        $id=input("id");
        $re=db("car_dd")->where("id",$id)->find();
        if($re){
           db("car_dd")->where("id",$id)->delete();
        }
        $this->redirect("index");
    }
}