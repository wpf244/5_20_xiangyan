<?php
namespace app\index\controller;

class Food extends BaseHome
{
    public function index()
    {
        
        $cid=input("cid");
        $tid=input("tid");
        $prices=input("prices");

        $arr['cname']="区域";
        $arr['tname']="分类";
        $arr['pname']="价格";
        $order=["sort asc","id desc"];

        $arr['cid']=0;
        $arr['tid']=0;
        $arr['pid']=0;

        $map=[]; 

        if($cid || $tid || $prices){

            if($cid){
                $map['cid']=['eq',$cid];
                $arr['cname']=db("spot_city")->where("id",$cid)->find()['name'];
                $arr['cid']=$cid;
            }
            if($tid){
                $map['tid']=['eq',$tid];
                $arr['tname']=db("food_type")->where("id",$tid)->find()['name'];
                $arr['tid']=$tid;
            }

            if($prices){
                if($prices == 1){
                    $order=["price desc","sort asc","id desc"];
                    $arr['pname']="价格由高到低";
                    $arr['pid']=1;
                }else{
                    $order=["price asc","sort asc","id desc"];
                    $arr['pname']="价格由低到高";
                    $arr['pid']=2;
                }
            }

        }

        $this->assign("arr",$arr);
        
        $res=db("food")->alias("a")->field("a.*,b.name as tname")->where(["status"=>1])->where($map)->join("food_type b","a.tid=b.id")->order($order)->select();

        $this->assign("res",$res);

        $city=db("spot_city")->select();

        $this->assign("city",$city);

        $type=db("food_type")->select();

        $this->assign("type",$type);
        
        return $this->fetch();
    }
    public function detail()
    {
       $id=input("id");

       $re=db("food")->where("id",$id)->find();

       $this->assign("re",$re);

       $res=db("food_hot")->where("fid",$id)->select();

       $this->assign("res",$res);

       $cid=$re['cid'];

       $list=db("food")->alias("a")->field("a.*,b.name as tname")->where(["cid"=>$cid,"status"=>1])->join("food_type b","a.tid=b.id")->order(["sort asc","id desc"])->select();

       $this->assign("list",$list);

       $type=db("food_type")->where("id",$re['tid'])->find();

       $this->assign("type",$type);
       
        return $this->fetch();
    }
    
}