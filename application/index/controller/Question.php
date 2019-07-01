<?php
namespace app\index\controller;

class Question extends BaseHome
{
    public function index()
    {
        $lb=db("lb")->where(["fid"=>5,"status"=>1])->order(["sort asc","id desc"])->select();

        $this->assign("lb",$lb);

        $id=input("id");

        if($id){
            $res=db("question")->alias("a")->field("a.*,b.image,b.nickname")->where("status",1)->whereTime("a.time","d")->join("user b","a.uid=b.uid",'left')->order("id desc")->select();
            
        }else{
            $res=db("question")->alias("a")->field("a.*,b.image,b.nickname")->where("status",1)->join("user b","a.uid=b.uid",'left')->order("id desc")->select();
            $id=0;
        }

        foreach($res as $k => $v){
            $res[$k]['cou']=db("answer")->where(["qid"=>$v['id'],"status"=>1])->count();
        }

        $this->assign("res",$res);

        $this->assign("id",$id);

        
        return $this->fetch();
    }
    /**
    * 问答
    *
    * @return void
    */
    public function ask()
    {
        $uid=session("userid");

        if($uid){

            return $this->fetch();

        }else{
            $this->redirect("Login/login");
        }
    }
    /**
    * 保存提问
    *
    * @return void
    */
    public function save()
    {
        $uid=session("userid");

        $data=input("post.");

        $data['uid']=$uid;

        $data['time']=time();

        $re=db("question")->insert($data);

        if($re){
            $this->success("保存成功",url("index"));
        }else{
            $this->error("系统繁忙请稍后再试",url("index"));
        }

    }
    /**
    * 问题详情
    *
    * @return void
    */
    public function detail()
    {
        $lb=db("lb")->where("fid",5)->find();

        $this->assign("lb",$lb);

        $id=input("id");

        $re=db("question")->where("id",$id)->find();

        $this->assign("re",$re);

        $uid=$re['uid'];

        $user=db("user")->where("uid",$uid)->find();

        $this->assign("user",$user);

        $res=db("answer")->alias("a")->field("a.*,b.image,b.nickname")->where(["status"=>1,"qid"=>$id])->join("user b","a.uid=b.uid",'left')->order("id desc")->select();

        $this->assign("res",$res);

        $cou=count($res);

        $this->assign("cou",$cou);

        
        return $this->fetch();
    }
    /**
    * 回答
    *
    * @return void
    */
    public function answer()
    {
        $uid=session("userid");

        if($uid){

            $id=input("qid");

            $re=db("question")->where("id",$id)->find();

            $this->assign("re",$re);

            return $this->fetch();

        }else{
            $this->redirect("Login/login");
        }
    }
    /**
    * 保存回答
    *
    * @return void
    */
    public function saves()
    {
        $uid=session("userid");

        $data=input("post.");

        $data['uid']=$uid;

        $data['time']=time();

        $data['status']=1;

        $re=db("answer")->insert($data);

        if($re){
            $this->success("保存成功",url("index"));
        }else{
            $this->error("系统繁忙请稍后再试",url("index"));
        }

    }

}