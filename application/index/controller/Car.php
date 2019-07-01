<?php
namespace app\index\controller;

class Car extends BaseHome
{
    public function index()
    {
        $lb=db("lb")->where(["fid"=>10,"status"=>1])->order(["sort asc","id desc"])->select();

        $this->assign("lb",$lb);

        $city_index=session("city_index");

        $res=db("car")->where(["status"=>1])->where("addr","like","%".$city_index."%")->order(["sort asc","id desc"])->select();

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

        $re=db("car")->where("id",$id)->find();

        

        $this->assign("re",$re);
        
        return $this->fetch();
    }
}