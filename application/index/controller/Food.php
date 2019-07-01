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

        $lb=db("lb")->where(["fid"=>45,"status"=>1])->order(["sort asc","id desc"])->select();

        $this->assign("lb",$lb);
        
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

       $assess=db("assess")->alias("a")->where(["status"=>1,"type"=>4,"g_id"=>$id])->join("user b","a.u_id=b.uid")->order("id desc")->select();

        $this->assign("assess",$assess);

        $cou=count($assess);

        $this->assign("cou",$cou);

        //收藏
        $uids=session("userid");
        if($uids){
            $collect=db("collect")->where(["uid"=>$uids,"gid"=>$id,"type"=>4])->find();

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
            $assist=db("assist")->where(["uid"=>$uids,"nid"=>$id,"type"=>4])->find();

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
            $data['type']=4;
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

            $re=db("collect")->where(["gid"=>$nid,"uid"=>$uid,"type"=>4])->find();

            if($re){
                db("collect")->where("id",$re['id'])->delete();
            }else{
                $data['gid']=$nid;
                $data['uid']=$uid;
                $data['type']=4;

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

            $re=db("assist")->where(["nid"=>$nid,"uid"=>$uid,"type"=>4])->find();

            if($re){
                db("assist")->where("id",$re['id'])->delete();
            }else{
                $data['nid']=$nid;
                $data['uid']=$uid;
                $data['type']=4;

                db("assist")->insert($data);
            }
            echo '0';

        }else{
            echo '1';
        }
    }
    
}