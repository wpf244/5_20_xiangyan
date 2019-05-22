<?php
namespace app\index\controller;

class Rural extends BaseHome
{
    public function index()
    {
        
        session("rural",null);

        $res=db("rural_type")->where(["status"=>1])->order(["sort asc","id desc"])->select();

        $this->assign("res",$res);

        return $this->fetch();
    }
    public function lister()
    {
        $id=input("id");

        session("rural",$id);

        $re=db("rural_type")->where("id",$id)->find();

        $this->assign("re",$re);

        return $this->fetch();
    }
}