<?php
namespace app\index\controller;

class Spot extends BaseHome
{
    public function index()
    {
        $id=input("id");
        
        $res=db("spot_city")->where(["status"=>1,"pid"=>0])->order(["sort asc","id desc"])->select();

       
        $pid=input("pid");

        if($id){
            $re=db("spot_city")->where("id",$id)->find();

                $pid=$re['pid'];
            foreach($res as $k => $v){
            $res[$k]['list']=db("spot")->where(["xid"=>$id,"status"=>1])->order(["sort asc","id desc"])->select();
            $res[$k]['type']=db("spot_city")->where(["status"=>1,"pid"=>$v['id']])->select();
            }
        }elseif($pid){
            foreach($res as $k => $v){
                $res[$k]['list']=db("spot")->where(["cid"=>$pid,"status"=>1])->order(["sort asc","id desc"])->select();
                $res[$k]['type']=db("spot_city")->where(["status"=>1,"pid"=>$v['id']])->select();
                }
        }else{
            $pid=0;
            foreach($res as $k => $v){
            $res[$k]['list']=db("spot")->where(["cid"=>$v['id'],"status"=>1])->order(["sort asc","id desc"])->select();
            $res[$k]['type']=db("spot_city")->where(["status"=>1,"pid"=>$v['id']])->select();
            }
        }
       
        // foreach($res as $k => $v){
        //    if($id){

        //     $re=db("spot_city")->where("id",$id)->find();

        //     $pid=$re['pid'];

            
        //     // if($pid == $v['id']){
        //       //  var_dump($id);
             
        //     // }else{
        //     //   //  var_dump(22);
        //     //     $res[$k]['list']=db("spot")->where(["cid"=>$v['id'],"status"=>1])->order(["sort asc","id desc"])->select();
        //     // }

        //    } else {
        //    // var_dump(33);
            
        //    }
           

        //     $res[$k]['type']=db("spot_city")->where(["status"=>1,"pid"=>$v['id']])->select();
        // }

        $this->assign("res",$res);

  

        $this->assign("pid",$pid);

        //热门抢购banner
        $assmeble=db("lb")->where("fid",39)->find();

        $this->assign("assemble",$assmeble);

        //热门砍价banner

        $bargain=db("lb")->where("fid",40)->find();

        $this->assign("bargain",$bargain);
        
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
        $hotel=db("spot")->where(["status"=>1])->where($map)->order(["sort asc","id desc"])->select();

        $this->assign("hotel",$hotel);

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
        $rural=db("rural")->where(["title"=>["like","%".$re['name']."%"],"status"=>1])->order(["id desc"])->limit("0,4")->select();

        if(empty($rural)){
            $rural=db("rural")->where(["recom"=>1,"status"=>1])->order(["id desc"])->limit("0,4")->select();
        }

        $this->assign("rural",$rural);

        //景区门票
        $ticket=db("spot_ticket")->where("sid",$id)->find();

        $this->assign("ticket",$ticket);


        $assess=db("assess")->alias("a")->where(["status"=>1,"type"=>3,"g_id"=>$id])->join("user b","a.u_id=b.uid")->order("id desc")->select();

        $this->assign("assess",$assess);

        $cou=count($assess);

        $this->assign("cou",$cou);

        //收藏
        $uids=session("userid");
        if($uids){
            $collect=db("collect")->where(["uid"=>$uids,"gid"=>$id,"type"=>3])->find();

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
            $assist=db("assist")->where(["uid"=>$uids,"nid"=>$id,"type"=>3])->find();

            if($assist){
                $assist=1;
            }else{
                $assist=0;
            }

        }else{
            $assist=0;
        }

        $this->assign("assist",$assist);


        
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
            $data['shop_id']=$re['sid'];
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
            $data['type']=3;
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

            $re=db("collect")->where(["gid"=>$nid,"uid"=>$uid,"type"=>3])->find();

            if($re){
                db("collect")->where("id",$re['id'])->delete();
            }else{
                $data['gid']=$nid;
                $data['uid']=$uid;
                $data['type']=3;

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

            $re=db("assist")->where(["nid"=>$nid,"uid"=>$uid,"type"=>3])->find();

            if($re){
                db("assist")->where("id",$re['id'])->delete();
            }else{
                $data['nid']=$nid;
                $data['uid']=$uid;
                $data['type']=3;

                db("assist")->insert($data);
            }
            echo '0';

        }else{
            echo '1';
        }
    }

}