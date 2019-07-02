<?php
namespace app\index\controller;

use think\Request;

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

        $uid=session("userid");

        $coupon=db("user_coupon")->where(["uid"=>$uid,"status"=>0])->find();

        if($coupon){
            $re['open']=0;
        }
        $cou=db("user_coupon")->where(["uid"=>$uid])->count();

        if($cou >= $re['num']){
            $re['open']=0;
        }


        $this->assign("re",$re);

        
        $city_index=session("city_index");
        //酒店推荐
        $hotel=db("hotel")->where(["status"=>1,"recome"=>1,"type"=>1])->where("addr","like","%".$city_index."%")->order(["sort asc","id desc"])->select();

        $this->assign("hotel",$hotel);

        $hotels=db("hotel")->where(["status"=>1,"recome"=>0,"type"=>1])->where("addr","like","%".$city_index."%")->order(["sort asc","id desc"])->select();

        $this->assign("hotels",$hotels);

        //民宿
        $home=db("hotel")->where(["status"=>1,"recome"=>1,"type"=>2])->where("addr","like","%".$city_index."%")->order(["sort asc","id desc"])->select();

        $this->assign("home",$home);

        $homes=db("hotel")->where(["status"=>1,"recome"=>0,"type"=>2])->where("addr","like","%".$city_index."%")->order(["sort asc","id desc"])->select();

        $this->assign("homes",$homes);

      //  $city=session("city");

      //广告图
      $lb=db("lb")->where(["fid"=>43,"status"=>1])->order(["sort asc","id desc"])->select();

      $this->assign("lb",$lb);

        
        return $this->fetch();
    }
    /**
    * 搜索
    *
    * @return void
    */
    public function search()
    {
        $title=input("title");

        if($title){
            $map['name']=["like","%".$title."%"];
        }else{
            $map=[];
        }
        $hotel=db("hotel")->where(["status"=>1])->where($map)->order(["sort asc","id desc"])->select();

        $this->assign("hotel",$hotel);

        return $this->fetch();
    }
    /**
    * 领取优惠券
    *
    * @return void
    */
    public function save_coupon()
    {
        $re=db("coupon")->where("id",1)->find();

        $uid=session("userid");

        if($re['open'] == 1){
           
          $coupon=db("user_coupon")->where(["uid"=>$uid,"status"=>0])->find();

          if(empty($coupon)){
       
            $data['uid']=session("userid");
            $data['money']=$re['moneys'];
            $data['coupon']=$re['money'];
            $data['time']=time();

            $rea=db("user_coupon")->insert($data);

            if($rea){
                echo '0';
            }else{
                echo '3';
            }

          }else{
              echo '2';
          }

        }else{
            echo '1';
        }

        
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

        $today=\date("Y/m/d");

        $this->assign("today",$today);

        $tomorrow=date("Y/m/d",strtotime("+1 day"));

        $this->assign("tomorrow",$tomorrow);

        $room=db("hotel_room")->where(["hid"=>$id,"status"=>1])->order(["id desc"])->select();

        $this->assign("room",$room);

        $severs=explode(",",$re['severs']);

        $res=db("hotel_sever")->where(["id"=>["in",$severs]])->select();

        $this->assign("res",$res);

        //评论
        $assess=db("assess")->alias("a")->where(["status"=>1,"type"=>2,"g_id"=>$id])->join("user b","a.u_id=b.uid")->order("id desc")->select();

        $this->assign("assess",$assess);

        $cou=count($assess);

        $this->assign("cou",$cou);

        //收藏
        $uids=session("userid");
        if($uids){
            $collect=db("collect")->where(["uid"=>$uids,"gid"=>$id,"type"=>2])->find();

            if($collect){
                $collect=1;
            }else{
                $collect=0;
            }

        }else{
            $collect=0;
        }

        $this->assign("collect",$collect);

        if($uids){
            $assist=db("assist")->where(["uid"=>$uids,"nid"=>$id,"type"=>2])->find();

            if($assist){
                $assist=1;
            }else{
                $assist=0;
            }

        }else{
            $assist=0;
            $uids=0;
        }

        $this->assign("assist",$assist);

        $this->assign("uids",$uids);

        $coua=db("assist")->where(["nid"=>$id,"type"=>2])->count();

        $this->assign("coua",$coua);

        $share_title=db("lb")->where("fid",46)->find();

        $share_title['name']=$re['name'];

        $share_title['desc']=$re['addr'];

        $share_title['image']=$re['image'];

        $share_title['url']=Request::instance()->url(true);

        $share_title['urls']=Request::instance()->domain();
        
        $this->assign("share_title",$share_title);

        
        return $this->fetch();
    }
    /**
    * 删除评论
    *
    * @return void
    */
    public function delete()
    {
        $id=input("id");

        $del=db("assess")->where("id",$id)->delete();

        if($del){
            echo '0';
        }else{
            echo '1';
        }
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
        $uid=session("userid");

        $coupon=db("user_coupon")->where(["uid"=>$uid,"status"=>0])->find();

        $this->assign("coupon",$coupon);

        $oid=input("oid");

        $this->assign("oid",$oid);

        $re=db("hotel_order")->where("id",$oid)->find();

        $this->assign("re",$re);

        $rid=$re['rid'];

        $room=db("hotel_room")->where(["id"=>$rid])->find();

        $this->assign("room",$room);

        $hid=$re['hid'];

        $hotel=db("hotel")->where(["id"=>$hid])->find();

        $this->assign("hotel",$hotel);

        $price=$re['days']*$room['price'];

        $this->assign("price",$price);

        $lb=db("lb")->where("fid",22)->find();

        $this->assign("lb",$lb);

        $only=db("user_coupon_only")->where(["uid"=>$uid,"hid"=>$hid])->find();

        $this->assign("only",$only);
        
        return $this->fetch();
    }
    /**
    * 保存订单
    *
    * @return void
    */
    public function save_order()
    {
        $oid=input("oid");

        $uid=session("userid");

        $re=db("hotel_order")->where("id",$oid)->find();

        if($re){

            $rid=$re['rid'];

            $room=db("hotel_room")->where(["id"=>$rid])->find();

            $hid=$re['hid'];

            $hotel=db("hotel")->where(["id"=>$hid])->find();

            $price=$re['days']*$room['price'];

            $num=input("num");

            $prices=$price*$num;

            $data['price']=$prices;

            $data['old_price']=$prices;

            $data['uid']=$uid;

            $data['gid']=$rid;

            $data['num']=$num;

            $data['username']=input("username");

            $data['phone']=input("phone");

            $data['name']=$room['name'];

            $data['code']='ck-'.uniqid();

            $data['type']=3;

            $data['time']=time();

            $data['start_time']=$re['start'];

            $data['end_time']=$re['end'];

            $data['hotel_name']=$hotel['name'];

            $data['days']=$re['days'];

            $data['shop_id']=$hotel['id'];

            $coupon=input("coupon");

            if($coupon == 1){

                $coupons=db("user_coupon")->where(["uid"=>$uid,"status"=>0])->find();

                if($coupons){

                    $data['coupon']=$coupons['coupon'];

                    $data['cid']=$coupons['id'];

                    $data['price']=$prices-$coupons['coupon'];
                }

            }

            $only=input("only");

            if($only != 0){
                $coupon_only=db("user_coupon_only")->where("id",$only)->find();

                if($coupon_only){
                    $data['only_coupon']=$coupon_only['coupon'];
                    $data['oid']=$coupon_only['id'];
                    $data['price']=$data['price']-$coupon_only['coupon'];
                }
            }

            $old_dd=db("order")->where(["uid"=>$uid,"gid"=>$rid,"type"=>3,"status"=>0])->find();

            if($old_dd){
                db("order")->where("id",$old_dd['id'])->delete();
            }
            db("hotel_order")->where("id",$oid)->delete();

            $re=db("order")->insert($data);

            $did=db("order")->getLastInsID();

            if($re){
                echo $did;
            }else{
                echo '0';
            }

        }else{
            echo '0';
        }
    }
    /**
    * 保存评价
    *
    * @return void
    */
    public function save_assess()
    {
        $uid=session("userid");

        if($uid){

            $data=input("post.");
            $data['u_id']=$uid;
            $data['type']=2;
            $data['addtime']=time();

            $re=db("assess")->insert($data);

            if($re){
                echo '0';
            }else{
                echo '2';
            }

        }else{
            echo '1';
        }
    }
    /**
    * 收藏
    *
    * @return void
    */
    public function save_collect()
    {
        $uid=session("userid");

        if($uid){

            $nid=input("nid");

            $re=db("collect")->where(["gid"=>$nid,"uid"=>$uid,"type"=>2])->find();

            if($re){
                db("collect")->where("id",$re['id'])->delete();
            }else{
                $data['gid']=$nid;
                $data['uid']=$uid;
                $data['type']=2;

                db("collect")->insert($data);
            }
            echo '0';

        }else{
            echo '1';
        }
    }
    /**
    * 点赞
    *
    * @return void
    */
    public function save_assist()
    {
        $uid=session("userid");

        if($uid){

            $nid=input("nid");

            $re=db("assist")->where(["nid"=>$nid,"uid"=>$uid,"type"=>2])->find();

            if($re){
                db("assist")->where("id",$re['id'])->delete();
            }else{
                $data['nid']=$nid;
                $data['uid']=$uid;
                $data['type']=2;

                db("assist")->insert($data);
            }
            echo '0';

        }else{
            echo '1';
        }
    }
}