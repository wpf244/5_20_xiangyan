<?php
namespace app\index\controller;

class Car extends BaseHome
{
    public function index()
    {
        $lb=db("lb")->where("id",10)->find();

        $this->assign("lb",$lb);

        $res=db("car")->where(["status"=>1])->order(["sort asc","id desc"])->select();

        $this->assign("res",$res);
        
        return $this->fetch();
    }
    public function detail()
    {
        $id=input("id");

        $re=db("car")->where(["id"=>$id])->find();

        $this->assign("re",$re);

        $res=db("car_type")->where(["cid"=>$id])->select();

        $this->assign("res",$res);
        
        return $this->fetch();
    }
    public function map()
    {
        $id=input("id");

        $re=db("spot")->where("id",$id)->find();

        $this->assign("re",$re);
        
        return $this->fetch();
    }
}