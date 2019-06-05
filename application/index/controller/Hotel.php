<?php
namespace app\index\controller;

class Hotel extends BaseUser
{
    public function index()
    {
        //酒店广告
        $hotel_lb=db("lb")->where("fid",20)->find();

        $this->assign("hotel_lb",$hotel_lb);

        //民宿广告

        $home_lb=db("lb")->where("fid",21)->find();

        $this->assign("home_lb",$home_lb);

        //优惠券管理
        $re=db("coupon")->where("id",1)->find();

        $this->assign("re",$re);


        //酒店推荐
        $hotel=db("hotel")->where(["status"=>1,"recome"=>1,"type"=>1])->order(["sort asc","id desc"])->select();

        $this->assign("hotel",$hotel);

        $hotels=db("hotel")->where(["status"=>1,"recome"=>0,"type"=>1])->order(["sort asc","id desc"])->select();

        $this->assign("hotels",$hotels);

        //民宿
        $home=db("hotel")->where(["status"=>1,"recome"=>1,"type"=>2])->order(["sort asc","id desc"])->select();

        $this->assign("home",$home);

        $homes=db("hotel")->where(["status"=>1,"recome"=>0,"type"=>2])->order(["sort asc","id desc"])->select();

        $this->assign("homes",$homes);

        
        return $this->fetch();
    }
    /**
    * 酒店详情
    *
    * @return void
    */
    public function detail()
    {
        
        $id=input("id");

        $re=db("hotel")->where("id",$id)->find();

        $this->assign("re",$re);

        $banner=db("hotel_banner")->where("hid",$id)->select();

        $this->assign("banner",$banner);

        $today=\date("Y-m-d");

        $this->assign("today",$today);

        $tomorrow=date("Y-m-d",strtotime("+1 day"));

        $this->assign("tomorrow",$tomorrow);

        $room=db("hotel_room")->where(["hid"=>$id,"status"=>1])->order(["id desc"])->select();

        $this->assign("room",$room);

        $severs=explode(",",$re['severs']);

        $res=db("hotel_sever")->where(["id"=>["in",$severs]])->select();

        $this->assign("res",$res);
        
        return $this->fetch();
    }
    /**
    * 保存订房信息
    *
    * @return void
    */
    public function save()
    {
        $data=input("post.");
 
        $uid=session("userid");

        $rid=input("rid");

        $hid=input("hid");

        $data['uid']=$uid;

        $re=db("hotel_order")->where(["uid"=>$uid,"rid"=>$rid,"hid"=>$hid])->find();

        if($re){
            db("hotel_order")->where("id",$re['id'])->delete();
        }

        $rea=db("hotel_order")->insert($data);

        $oid=db("hotel_order")->getLastInsId();

        if($rea){
            echo $oid;
        }else{
            echo '0';
        }

    
    }
    public function go_buy()
    {
        return $this->fetch();
    }
}