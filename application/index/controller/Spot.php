<?php
namespace app\index\controller;

class Spot extends BaseHome
{
    public function index()
    {
        $res=db("spot_city")->where(["status"=>1])->order(["sort asc","id desc"])->select();

        foreach($res as $k => $v){
            $res[$k]['list']=db("spot")->where(["cid"=>$v['id'],"status"=>1])->order(["sort asc","id desc"])->select();
        }

        $this->assign("res",$res);
        
        return $this->fetch();
    }
    public function detail()
    {
        $id=input("id");

        $re=db("spot")->where("id",$id)->find();

        $this->assign("re",$re);

        //景区攻略

        $gl=db("spot_gl")->where("sid",$id)->find();

        $this->assign("gl",$gl);

        //玩转景区

        $play=db("spot_play")->where(["sid"=>$id])->order("status desc")->limit("0,2")->select();

        $this->assign("play",$play);

        //支持服务
        $severs=explode(",",$re['severs']);

        $sever=db("spot_sever")->where(["id"=>["in",$severs]])->select();

        $this->assign("sever",$sever);

        //游记推荐
        $rural=db("rural")->alias("a")->field("a.*,b.nickname,b.image as photo")->where(["title"=>["like","%".$re['name']."%"],"status"=>1])->join("user b","a.uid = b.uid")->order(["id desc"])->limit("0,4")->select();

        if(empty($rural)){
            $rural=db("rural")->alias("a")->field("a.*,b.nickname,b.image as photo")->where(["recom"=>1,"status"=>1])->join("user b","a.uid = b.uid")->order(["id desc"])->limit("0,4")->select();
        }

        $this->assign("rural",$rural);

        //景区门票
        $ticket=db("spot_ticket")->where("sid",$id)->find();

        $this->assign("ticket",$ticket);


        
        return $this->fetch();
    }
    public function map()
    {
        $id=input("id");

        $re=db("spot")->where("id",$id)->find();

        $this->assign("re",$re);
        
        return $this->fetch();
    }
    public function gl_detail()
    {
        $id=input("id");

        $re=db("spot_gl")->where("id",$id)->find();

        $this->assign("re",$re);

        return $this->fetch();
    }
    public function play_detail()
    {
        $id=input("id");

        $re=db("spot_play")->where("id",$id)->find();

        $this->assign("re",$re);

        return $this->fetch();
    }
    public function go_buy()
    {
        $id=input("id");

        $re=db("spot_ticket")->where("id",$id)->find();

        $this->assign("re",$re);
        
        return $this->fetch();
    }
     /**
    * 保存订单
    *
    * @return void
    */
    public function save_order()
    {     
        $id=input("id");

        $uid=session("userid");

        $num=input("num");

        $re=db("spot_ticket")->where("id",$id)->find();

        if($re){
            $data['start_time']=input("start_time");
            $data['end_time']=input("end_time");
            $data['uid']=$uid;
            $data['gid']=$id;
            $data['num']=$num;
            $data['price']=$num*$re['price'];
            $data['name']=$re['title'];
            $data['username']=input("username");
            $data['phone']=input("phone");
            $data['code']='ck-'.\uniqid();
            $data['time']=time();
            $data['type']=2;

            $order=db("order")->where(["uid"=>$uid,"gid"=>$id,"type"=>2,"status"=>0])->find();

            if($order){
                db("order")->where("id",$order['id'])->delete();
            }

            $rea=db("order")->insert($data);

            $did=db("order")->getLastInsID();

            if($rea){
                echo $did;
            }else{
                echo '0';
            }

        }else{
            echo '0';
        }
    }
    

}