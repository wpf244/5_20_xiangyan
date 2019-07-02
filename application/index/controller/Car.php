<?php
namespace app\index\controller;

use think\Request;

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

        $share_title=db("lb")->where("fid",46)->find();

        $share_title['name']=$re['name'];

        $share_title['desc']=\strip_tags($re['addr']);

        $share_title['image']=$re['image'];

        $share_title['url']=Request::instance()->url(true);

        $share_title['urls']=Request::instance()->domain();
        
        $this->assign("share_title",$share_title);
        
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