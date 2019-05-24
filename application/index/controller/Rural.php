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

        $title=input("title");

        if($title){
            $map['title']=array("like","%".$title."%");
        }else{
            $map=[];
        }

        $re=db("rural_type")->where("id",$id)->find();

        $this->assign("re",$re);

        
        $res=db("rural")->alias("a")->field("a.*,b.nickname,b.image as photo")->where($map)->where(["tid"=>$id,"status"=>1])->join("user b","a.uid=b.uid")->order(["id desc"])->select();


        $this->assign("res",$res);


        return $this->fetch();
    }
    public function detail()
    {
        
        $id=input("id");

        $re=db("rural")->where("id",$id)->find();

        $this->assign("re",$re);

        $images=$re['images'];

        $banner=explode(",",$images);

       // var_dump($images);

        $this->assign("banner",$banner);

        $uid=$re['uid'];

        $user=db("user")->where("uid",$uid)->find();

        $this->assign("user",$user);

        db("rural")->where("id",$id)->setInc("looks",1);

        
        return $this->fetch();
    }
}