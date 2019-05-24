<?php
namespace app\index\controller;


class Index extends BaseHome
{
    public function index()
    {
        //轮播图
        $lb=db("lb")->where(["fid"=>2,"status"=>1])->order(["sort asc","id desc"])->select();

        $this->assign("lb",$lb);

        //机票车票链接
        $url=db("lb")->where(["fid"=>3])->find();

        $this->assign("url",$url);


        //乡村旅游
        $rural=db("rural")->alias("a")->field("a.*,b.nickname,b.image as photo")->where(["recom"=>1])->join("user b","a.uid=b.uid")->order(["id desc"])->select();

        $this->assign("rural",$rural);
        
        return $this->fetch();
    }

}
