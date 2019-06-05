<?php
namespace app\index\controller;

class Group extends BaseUser
{
    public function index()
    {
        $lb=db("lb")->where("fid",11)->find();

        $this->assign("lb",$lb);

        //定制流程
        $flow=db("lb")->where(["fid"=>12,"status"=>1])->order(["sort asc","id asc"])->select();

        $this->assign("flow",$flow);

        //个人定制

        $personal=db("lb")->where(["fid"=>13,"status"=>1])->order(["sort asc","id asc"])->select();

        $this->assign("personal",$personal);

        //团队定制

        $team=db("lb")->where(["fid"=>14,"status"=>1])->order(["sort asc","id asc"])->select();

        $this->assign("team",$team);

        //跟团游

        $res=db("package")->where(["status"=>1])->order(["sort asc","id desc"])->select();

        $this->assign("res",$res);

        return $this->fetch();
    }
    /**
    * 个人定制
    *
    * @return void
    */
    public function made_person()
    {
        
        $lb=db("lb")->where("fid",15)->find();

        $this->assign("lb",$lb);

        //提供服务
        $res=db("lb")->where(["fid"=>16,"status"=>1])->order(["sort asc","id asc"])->select();

        $this->assign("res",$res);
        
        return $this->fetch();
    }
    /**
    * 保存个人定制
    *
    * @return void
    */
    public function save_person()
    {
        $data=input("post.");

        $data['sever']=implode(",",$data['sever']);

        $data['time']=time();

        $data['uid']=session("userid");

        $re=db("made_person")->insert($data);

        if($re){
            echo '0';
        }else{
            echo '1';
        }
    }
    /**
    * 我的定制
    *
    * @return void
    */
    public function my_made()
    {
        $uid=session("userid");

        $person=db("made_person")->where(["uid"=>$uid,"status"=>0])->order(["id desc"])->select();

        $this->assign("person",$person);

        $team=db("made_team")->where(["uid"=>$uid,"status"=>0])->order(["id desc"])->select();

        $this->assign("team",$team);

        
        return $this->fetch();
    }
    /**
    * 团队定制
    *
    * @return void
    */
    public function made_team()
    {
        $lb=db("lb")->where("fid",17)->find();

        $this->assign("lb",$lb);

        $hotel=db("lb")->where(["fid"=>18,"status"=>1])->order(["sort asc","id asc"])->select();

        $this->assign("hotel",$hotel);

        $car=db("lb")->where(["fid"=>19,"status"=>1])->order(["sort asc","id asc"])->select();

        $this->assign("car",$car);

        
        return $this->fetch();
    }
    /**
    * 保存团队定制
    *
    * @return void
    */
    public function save_team()
    {
        $data=input("post.");

        $data['time']=time();

        $data['uid']=session("userid");

        $re=db("made_team")->insert($data);

        if($re){
            echo '0';
        }else{
            echo '1';
        }
    }
    /**
    * 个人定制详情
    *
    * @return void
    */
    public function person_detail()
    {
        $lb=db("lb")->where("fid",15)->find();

        $this->assign("lb",$lb);

        $id=input("id");

        $re=db("made_person")->where("id",$id)->find();

        $this->assign("re",$re);

        $sever=explode(",",$re['sever']);

        $this->assign("sever",$sever);

        return $this->fetch();
    }
    /**
    * 团队定制详情
    *
    * @return void
    */
    public function team_detail()
    {
        $lb=db("lb")->where("fid",17)->find();

        $this->assign("lb",$lb);

        $id=input("id");

        $re=db("made_team")->where("id",$id)->find();

        $this->assign("re",$re);
        
        return $this->fetch();
    }
    /**
    * 团游详情
    *
    * @return void
    */
    public function detail()
    {
        $id=input("id");

        $re=db("package")->where("id",$id)->find();

        $this->assign("re",$re);
        
        return $this->fetch();
    }
    /**
    * 提交订单
    *
    * @return void
    */
    public function go_buy()
    {
        $id=input("id");

        $re=db("package")->where("id",$id)->find();

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

        $re=db("package")->where("id",$id)->find();

        if($re){
            
            $data['uid']=$uid;
            $data['gid']=$id;
            $data['num']=$num;
            $data['price']=$num*$re['price'];
            $data['name']=$re['ticket'];
            $data['username']=input("username");
            $data['phone']=input("phone");
            $data['code']='ck-'.\uniqid();
            $data['time']=time();
            $data['type']=1;

            $order=db("order")->where(["uid"=>$uid,"gid"=>$id,"type"=>1,"status"=>0])->find();

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