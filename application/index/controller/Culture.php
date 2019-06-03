<?php
namespace app\index\controller;

class Culture extends BaseHome
{
    public function index()
    {
        //广告图
        $lb=db("lb")->where("fid",8)->find();

        $this->assign("lb",$lb);

        $res=db("culture")->where(["status"=>1])->order(["sort asc","id desc"])->select();

        $this->assign("res",$res);
        
        return $this->fetch();
    }
    public function detail()
    {
        $id=input("id");

        $re=db("culture")->where("id",$id)->find();

        $this->assign("re",$re);

        db("culture")->where("id",$id)->setInc("looks",1);

        $res=db("culture_banner")->where("cid",$id)->select();

        $this->assign("res",$res);
        
        return $this->fetch();
    }
    /**
    * 游记攻略
    *
    * @return void
    */
    public function gl()
    {
        $re=db("lb")->where("fid",9)->find();

        $this->assign("re",$re);
        
        return $this->fetch();
    }
}