<?php
namespace app\index\controller;

class Rural extends BaseHome
{
    public function index()
    {
        
     //   session("rural",null);

    

    


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

        if($cid || $xid || $zid ){

           

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

        $res=db("rural_type")->where(["status"=>1])->order(["sort asc","id desc"])->select();

        foreach($res as $k => $v){
            $res[$k]['list']=db("rural")->where(["tid"=>$v['id'],"status"=>1,"recom"=>1])->where($map)->order(["sort asc","id desc"])->select();
        }

        $this->assign("res",$res);

        $lb=db("lb")->where(["fid"=>6,"status"=>1])->order(["sort asc","id desc"])->select();

        $this->assign("lb",$lb);

        

        return $this->fetch();
    }
    public function lister()
    {
        $id=input("id");

        $title=input("title");


        $cid=input("cid");

        $xid=input("xid");

        $zid=input("zid");

        // $arr['cname']="城市选择";
        // $arr['xname']="县区选择";
        // $arr['zname']="乡镇选择";

        // $arr['cid']=0;
        // $arr['xid']=0;
        // $arr['zid']=0;

        $map=[]; 
        
        // //城市列表
        // $city=db("culture_city")->where("sid",0)->select();

        // //县区列表
        // $area=db("culture_city")->where(["pid"=>0,"sid"=>['neq',0]])->select();

        // //乡镇列表
        // $towns=db("culture_city")->where(["pid"=>['neq',0],"sid"=>['neq',0]])->select();

        if($cid || $xid || $zid || $title){

            if($title){
           
                $map['title']=array("like","%".$title."%");
             
            }

            if($cid){
                $map['cid']=["eq",$cid];

                // $area=db("culture_city")->where(["pid"=>0,"sid"=>$cid])->select();

                // $towns=db("culture_city")->where(["pid"=>['neq',0],"sid"=>$cid])->select();

                // $arr['cid']=$cid;

                // $arr['cname']=db("culture_city")->where("cid",$cid)->find()['c_name'];
            }

            if($xid){
                $map['xid']=$xid;

                // $towns=db("culture_city")->where(["pid"=>$xid])->select();

                // $arr['xid']=$xid;

                // $arr['xname']=db("culture_city")->where("cid",$xid)->find()['c_name'];
            }

            if($zid){

                $map['zid']=$zid;

                // $arr['zid']=$zid;

                // $arr['zname']=db("culture_city")->where("cid",$zid)->find()['c_name'];

            }


        }


        // $this->assign("arr",$arr);

        // $this->assign("city",$city);

        // $this->assign("area",$area); 

        // $this->assign("towns",$towns);

        
        $res=db("rural")->where($map)->where(["tid"=>$id,"status"=>1])->order(["sort asc","id desc"])->select();


        $this->assign("res",$res);

        
        if(empty($id)){
            $id=db("rural_type")->limit(1)->find()['id'];
        }

        $re=db("rural_type")->where("id",$id)->find();

        $this->assign("re",$re);

        $banner=db("rural_type_banner")->where("tid",$id)->select();

        $this->assign("banner",$banner);


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
        

        //评论
        $assess=db("assess")->alias("a")->where(["status"=>1,"type"=>1,"g_id"=>$id])->join("user b","a.u_id=b.uid")->order("id desc")->select();

        $this->assign("assess",$assess);

        $cou=count($assess);

        $this->assign("cou",$cou);

        //收藏
        $uids=session("userid");
        if($uids){
            $collect=db("collect")->where(["uid"=>$uids,"gid"=>$id,"type"=>1])->find();

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
            $assist=db("assist")->where(["uid"=>$uids,"nid"=>$id,"type"=>1])->find();

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

        $coua=db("assist")->where(["nid"=>$id,"type"=>1])->count();

        $this->assign("coua",$coua);


       

        
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

        $addr=session("city_index");

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

        $addr=session("city_index");

        
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

        $addr=session("city_index");
        
        $where['addr'] = ['like', "%".$addr."%"];
        
        $res=db("hotel")->where(["status"=>1])->where($where)->order(["sort asc","id desc"])->select();

        $this->assign("res",$res);

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
            $data['type']=1;
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

            $re=db("collect")->where(["gid"=>$nid,"uid"=>$uid,"type"=>1])->find();

            if($re){
                db("collect")->where("id",$re['id'])->delete();
            }else{
                $data['gid']=$nid;
                $data['uid']=$uid;
                $data['type']=1;

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

            $re=db("assist")->where(["nid"=>$nid,"uid"=>$uid,"type"=>1])->find();

            if($re){
                db("assist")->where("id",$re['id'])->delete();
            }else{
                $data['nid']=$nid;
                $data['uid']=$uid;
                $data['type']=1;

                db("assist")->insert($data);
            }
            echo '0';

        }else{
            echo '1';
        }
    }
}