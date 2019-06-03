<?php
namespace app\index\controller;

class Food extends BaseHome
{
    public function index()
    {
        
        $cid=input("cid");
        $tid=input("tid");
        $prices=input("prices");

        if($cid || $tid || $prices){
            $map=[];
            $order=["sort asc","id desc"];

            if($cid){
                $map['cid']=['eq',$cid];
            }
            if($tid){
                $map['tid']=['eq',$tid];
            }

            if($prices){
                if($prices == 1){
                    $order=["price desc","sort asc","id desc"];
                }else{
                    $order=["price asc","sort asc","id desc"];
                }
            }


        }else{
            $map=[];
            $order=["sort asc","id desc"];
        }
        
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