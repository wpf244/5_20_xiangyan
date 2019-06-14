<?php
namespace app\index\controller;

class Rural extends BaseHome
{
    public function index()
    {
        
        session("rural",null);

        $res=db("rural_type")->where(["status"=>1])->order(["sort asc","id desc"])->select();

        foreach($res as $k => $v){
            $res[$k]['list']=db("rural")->where(["tid"=>$v['id'],"status"=>1,"recom"=>1])->order("id desc")->select();
        }

        $this->assign("res",$res);

        $lb=db("lb")->where("fid",6)->find();

        $this->assign("lb",$lb);

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

        if(empty($id)){
            $id=db("rural_type")->limit(1)->find()['id'];
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
    /**
    * 附近好玩
    *
    * @return void
    */
    public function play()
    {
        //banner图
        $lb=db("lb")->where("fid",7)->find();

        $this->assign("lb",$lb);

        $id=input("id");

        $re=db("rural")->where("id",$id)->find();

        $addr=$re['addr'];

        $res=db("spot")->where(["status"=>1,"addr"=>["like","%".$addr."%"]])->order(["sort asc","id desc"])->select();

        $this->assign("res",$res);

        
        return $this->fetch();
    }
    /**
    * 附近特产
    *
    * @return void
    */
    public function goods()
    {
        //banner图
        $lb=db("lb")->where("fid",29)->find();

        $this->assign("lb",$lb);

        $id=input("id");

        $re=db("rural")->where("id",$id)->find();

        $addr=$re['addr'];

        
        $res=db("goods")->where(["up"=>1,"city"=>["like","%".$addr."%"]])->order(["sort asc","id desc"])->select();

        $this->assign("res",$res);

      //  var_dump($res);

        return $this->fetch();
    }
    /**
    * 附近民宿
    *
    * @return void
    */
    public function hotel()
    {
        //banner图
        $lb=db("lb")->where("fid",30)->find();

        $this->assign("lb",$lb);

        $id=input("id");

        $re=db("rural")->where("id",$id)->find();

        $addr=$re['addr'];

        $where['addr'] = ['like', "%".$addr."%"];
        
        $res=db("hotel")->where(["status"=>1])->where($where)->order(["sort asc","id desc"])->select();

        $this->assign("res",$res);

        return $this->fetch();
    }
}