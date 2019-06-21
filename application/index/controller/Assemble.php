<?php
namespace app\index\controller;

class Assemble extends BaseUser
{
    public function index()
    {
        $res=db("assemble_goods")->where(["up"=>1])->order(["sort asc","id desc"])->select();

        $this->assign("res",$res);
        
        return $this->fetch();
    }
    public function detail()
    {
        //团购规则
        $rule=db("lb")->where("fid",34)->find();

        $this->assign("rule",$rule);

        $id=input("id");

        $re=db("assemble_goods")->where("id",$id)->find();

        $this->assign("re",$re);

        $res=db("assemble")->alias("a")->field("a.*,b.nickname,b.image")->where(["gid"=>$id,"status"=>1])->join("user b","a.uid=b.uid")->select();
       
        foreach($res as $k => $v){
            $res[$k]['nums']=$v['number']-$v['num'];
            $date=$v['time']+$v['date']*60*60;
            $res[$k]['dates']=date("Y/m/d H:i:s",$date);
        }
      
       // var_dump($res);

        $this->assign("res",$res);

        $cou=count($res);

        $this->assign("cou",$cou);

        
        return $this->fetch();
    }
    public function save()
    {
        $uid=session("userid");

        $id=input("id");

        $num=input("num");

        $re=db("assemble")->where(["uid"=>$uid,"gid"=>$id,"status"=>0])->find();

        if($re){
            db("assemble")->where("id",$re['id'])->delete();
        }

        $res=db("assemble")->where(["uid"=>$uid,"gid"=>$id,"status"=>1])->find();

        if(empty($res)){

            $goods=db("assemble_goods")->where("id",$id)->find();
            
            $data['uid']=$uid;
            $data['gid']=$id;
            $data['name']=$goods['name'];
            $data['tag']=$goods['tag'];
            $data['image']=$goods['image'];
            $data['price']=$goods['price'];
            $data['floor_price']=$goods['floor_price'];
            $data['number']=$goods['group_number'];
            $data['date']=$goods['group_time'];
            $data['buy_num']=$num;
            $data['time']=time();

            db("assemble")->insert($data);

            $id=db("assemble")->getLastInsID();

            if($id){
                echo $id;
            }else{
                echo '0';
            }

        }else{
            echo '0';
        }
    }

    /**
    * 确认购买
    *
    * @return void
    */
    public function buy()
    {
        $uid=session("userid");

        $addr=db("addr")->where(["uid"=>$uid,"a_status"=>1])->find();

        $this->assign("addr",$addr);

        $id=input("id");

        $re=db("assemble")->where("id",$id)->find();

        $this->assign("re",$re);

        $gid=$re['gid'];
 
        $goods=db("assemble_goods")->where("id",$gid)->find();

        $this->assign("goods",$goods);

        $price=$goods['floor_price']*$re['buy_num'];

        $this->assign("price",$price);
        
        return $this->fetch();
    }
    /**
    * 生产订单
    *
    * @return void
    */
    public function sdd()
    {
        $did=input('did');

        $aid=input("aid");

        $content=\input("content");

        $re=db("assemble")->where("id",$did)->find();

        $gid=$re['gid'];
     
        $uid=session("userid");
        $ob=db("assemble_dd");
        $old_dd=db("assemble_dd")->where(["gid"=>$gid,"uid"=>$uid,"status"=>0,"a_id"=>$did])->find();
        if($old_dd){
            $ob->where("id",$old_dd['id'])->delete();
         
        }
        $good=db("assemble_goods")->where("id",$gid)->find();
     
        $arr=array();
        $arr['gid']=$gid;
        $arr['uid']=$uid;
        $arr['a_id']=$did;
        $arr['goods_price']=$good['price'];
        $arr['num']=$re['buy_num'];
        $arr['price']=$good['floor_price']*$re['buy_num'];
        $arr['name']=$good['name'];
       
        $arr['image']=$good['image'];
        $arr['code']="CK-".uniqid();
        $arr['content']=$content;
        $arr['aid']=$aid;
        $arr['time']=time();
        
        $re=$ob->insert($arr);
        
        $dids = db('assemble_dd')->getLastInsID();
        if($dids){
            echo $dids;
        }else{
            echo '0';
        }
    }
    /**
    * 拼团人确认购买
    *
    * @return void
    */
    public function buys()
    {
        $uid=session("userid");

        $addr=db("addr")->where(["uid"=>$uid,"a_status"=>1])->find();

        $this->assign("addr",$addr);

        $id=input("id");

        $re=db("assemble_log")->where("id",$id)->find();

        $this->assign("re",$re);

        $gid=$re['gid'];
 
        $goods=db("assemble_goods")->where("id",$gid)->find();

        $this->assign("goods",$goods);

        $price=$goods['floor_price'];

        $this->assign("price",$price);
        
        return $this->fetch();
    }
     /**
    * 生产订单
    *
    * @return void
    */
    public function sdds()
    {
        $did=input('did');

        $aid=input("aid");

        $content=\input("content");

        $re=db("assemble_log")->where("id",$did)->find();

        $gid=$re['gid'];
     
        $uid=session("userid");
        $ob=db("assemble_dd");
        $old_dd=db("assemble_dd")->where(["gid"=>$gid,"uid"=>$uid,"status"=>0,"a_id"=>$did,"lid"=>$re['id']])->find();
        if($old_dd){
            $ob->where("id",$old_dd['id'])->delete();
         
        }
        $good=db("assemble_goods")->where("id",$gid)->find();
     
        $arr=array();
        $arr['gid']=$gid;
        $arr['uid']=$uid;
        $arr['a_id']=$re['aid'];
        $arr['goods_price']=$good['price'];
        $arr['num']=1;
        $arr['price']=$good['floor_price'];
        $arr['name']=$good['name'];
        $arr['lid']=$re['id'];
       
        $arr['image']=$good['image'];
        $arr['code']="CK-".uniqid();
        $arr['content']=$content;
        $arr['aid']=$aid;
        $arr['time']=time();
        
        $re=$ob->insert($arr);
        
        $dids = db('assemble_dd')->getLastInsID();
        if($dids){
            echo $dids;
        }else{
            echo '0';
        }
    }
    /**
    * 我发起的拼团
    *
    * @return void
    */
    public function start()
    {
        
        $uid=session("userid");

        $rec=db("assemble")->where(["uid"=>$uid,"status"=>1])->select();

        $this->assign("rec",$rec);

        $res=db("assemble")->where(["uid"=>$uid,"status"=>2])->select();

        $this->assign("res",$res);

        $reg=db("assemble")->where(["uid"=>$uid,"status"=>3])->select();

        $this->assign("reg",$reg);


        
        return $this->fetch();
    }
    /**
    * 我参与的拼团
    *
    * @return void
    */
    public function partake()
    {

        $uid=session("userid");

        $rec=db("assemble_log")->alias("a")->where("a.uid",$uid)->where("b.status",1)->join("assemble b","a.aid=b.id")->select();

        $this->assign("rec",$rec);

        $res=db("assemble_log")->alias("a")->where("a.uid",$uid)->where("b.status",2)->join("assemble b","a.aid=b.id")->select();

        $this->assign("res",$res);

        $reg=db("assemble_log")->alias("a")->where("a.uid",$uid)->where("b.status",3)->join("assemble b","a.aid=b.id")->select();

        $this->assign("reg",$reg);



        return $this->fetch();
    }

}