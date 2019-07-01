<?php
namespace app\index\controller;

class Culture extends BaseHome
{
    public function index()
    {
        //广告图
        $lb=db("lb")->where(["fid"=>8,"status"=>1])->order(["sort asc","id desc"])->select();

        $this->assign("lb",$lb);

        $cid=input("cid");

        $xid=input("xid");

        $zid=input("zid");

        $arr['cname']="城市选择";
        $arr['xname']="县区选择";
        $arr['zname']="乡镇选择";

        $arr['cid']=0;
        $arr['xid']=0;
        $arr['zid']=0;

        $map=[]; 
        
        //城市列表
        $city=db("culture_city")->where("sid",0)->select();

        //县区列表
        $area=db("culture_city")->where(["pid"=>0,"sid"=>['neq',0]])->select();

        //乡镇列表
        $towns=db("culture_city")->where(["pid"=>['neq',0],"sid"=>['neq',0]])->select();

        if($cid || $xid || $zid){

            if($cid){
                $map['cid']=["eq",$cid];

                $area=db("culture_city")->where(["pid"=>0,"sid"=>$cid])->select();

                $towns=db("culture_city")->where(["pid"=>['neq',0],"sid"=>$cid])->select();

                $arr['cid']=$cid;

                $arr['cname']=db("culture_city")->where("cid",$cid)->find()['c_name'];
            }

            if($xid){
                $map['xid']=$xid;

                $towns=db("culture_city")->where(["pid"=>$xid])->select();

                $arr['xid']=$xid;

                $arr['xname']=db("culture_city")->where("cid",$xid)->find()['c_name'];
            }

            if($zid){

                $map['zid']=$zid;

                $arr['zid']=$zid;

                $arr['zname']=db("culture_city")->where("cid",$zid)->find()['c_name'];

            }


        }


        $this->assign("arr",$arr);

        $this->assign("city",$city);

        $this->assign("area",$area); 

        $this->assign("towns",$towns);



        $res=db("culture")->where(["status"=>1])->where($map)->order(["sort asc","id desc"])->select();

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
    /**
    * 新的游记攻略
    *
    * @return void
    */
    public function gls()
    {
        //游记

        $title=input("title");

        if($title){
            $map['title']=["like","%".$title."%"];
        }else{
            $map=[];
        }

        $city_index=session("city_index");
        
        $publish=db("publish")->where(["status"=>1,"recom"=>1])->where("addr","like","%".$city_index."%")->where($map)->order(["sort asc","id desc"])->select();

        $this->assign("publish",$publish);

        $publishs=db("publish")->where(["status"=>1,"recom"=>0])->where("addr","like","%".$city_index."%")->where($map)->order(["sort asc","id desc"])->select();

        $this->assign("publishs",$publishs);

        //攻略
        $strat=db("strat")->where(["status"=>1,"recome"=>1])->where("addr","like","%".$city_index."%")->where($map)->order(["sort asc","id desc"])->select();

        $this->assign("strat",$strat);

        $strats=db("strat")->where(["status"=>1,"recome"=>0])->where("addr","like","%".$city_index."%")->where($map)->order(["sort asc","id desc"])->select();

        $this->assign("strats",$strats);

        //广告图
        $lb=db("lb")->where(["fid"=>44,"status"=>1])->order(["sort asc","id desc"])->select();

        $this->assign("lb",$lb);
        
        return $this->fetch();
    }
    /**
    * 游记详情
    *
    * @return void
    */
    public function detail_publish()
    {
        $id=input("id");

        $re=db("publish")->where("id",$id)->find();

        $this->assign("re",$re);

        $images=$re['images'];

        $banner=explode(",",$images);

       // var_dump($images);

        $this->assign("banner",$banner);

        $uid=$re['uid'];

        $user=db("user")->where("uid",$uid)->find();

        $this->assign("user",$user);

        db("publish")->where("id",$id)->setInc("looks",1);

        return $this->fetch();
    }
    /**
    * 攻略详情
    *
    * @return void
    */
    public function detail_strat()
    {
        
        $id=input("id");

        $re=db("strat")->where("id",$id)->find();

        $this->assign("re",$re);

        db("strat")->where("id",$id)->setInc("looks",1);
        

        return $this->fetch();
    }
}