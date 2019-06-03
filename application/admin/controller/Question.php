<?php
namespace app\admin\controller;

class Question extends BaseAdmin
{
    public function index()
    {
        
        $keywords=input("keywords");

        if($keywords){
            $map['title']=['like','%'.$keywords.'%'];
        }else{
            $keywords="";
            $map=[];
        }
        $this->assign("keywords",$keywords);
        
        $list=db("question")->alias("a")->field("a.*,b.nickname")->where("a.status",0)->where($map)->join("user b","a.uid = b.uid",'left')->order("a.id desc")->paginate(20,false,['query'=>request()->param()]);

        $this->assign("list",$list);

        $page=$list->render();

        $this->assign("page",$page);
        
        return $this->fetch();
    }
    public function change()
    {
        $id=\input('id');
        $re=db("question")->where("id",$id)->find();
        if($re){
            $res=db("question")->where("id",$id)->setField("status",1);
            $this->redirect('index');
        }else{
            $this->redirect('index');
        }
    }
    public function change_all()
    {
        $id=\input('id');
        $arr=\explode(",",$id);
        foreach($arr as $v){
            $re=db("question")->where("id",$v)->find();
            if($re){
                $res=db("question")->where("id",$v)->setField("status",1);
               
            }
        }
        $this->redirect('index');
    }
    public function delete()
    {
        $id=\input('id');
        $re=db("question")->where("id=$id")->find();
        if($re){
            $res=db("question")->where("id=$id")->delete();
            if($res){
                echo '0';
            }else{
                echo '2';
            }
        }else{
            echo'1';
        }
    }
    public function delete_all()
    {
        $id=\input('id');
        $arr=\explode(",",$id);
        foreach($arr as $v){
            $re=db("question")->where("id",$v)->find();
            if($re){
                $res=db("question")->where("id",$v)->delete();
               
            }
        }
        $this->redirect('index');
    }
    public function add()
    {
        return $this->fetch();
    }
    public function find_user()
    {
        $name=input("name");

        $re=db("user")->where("nickname|phone",$name)->find();

        if($re){
            echo '0';
        }else{
            echo '1';
        }
    }
    public function save()
    {
        $data=input("post.");

        $data['time']=time();
        $name=input("name");
        if($name){
            $user=db("user")->where("nickname|phone",$name)->find();

            $data['uid']=$user['uid'];
        }
   
        $data['status']=1;

        unset($data['name']);
 
 
        $re=db("question")->insert($data);

        if($re){
            $this->success("发布成功",url("Question/lister"));
        }else{
            $this->error("系统繁忙,请稍后再试");
        }
    }
    public function lister()
    {
        $keywords=input("keywords");

        if($keywords){
            $map['title']=['like','%'.$keywords.'%'];
        }else{
            $keywords="";
            $map=[];
        }
        $this->assign("keywords",$keywords);
        
        $list=db("question")->alias("a")->field("a.*,b.nickname")->where("a.status",1)->where($map)->join("user b","a.uid = b.uid",'left')->order("a.id desc")->paginate(20,false,['query'=>request()->param()]);

        $this->assign("list",$list);

        $page=$list->render();

        $this->assign("page",$page);
        
        return $this->fetch();
    }
    public function delete_alls()
    {
        $id=\input('id');
        $arr=\explode(",",$id);
        foreach($arr as $v){
            $re=db("question")->where("id",$v)->find();
            if($re){
                $res=db("question")->where("id",$v)->delete();
               
            }
        }
        $this->redirect('lister');
    }
    public function look()
    {
        
     
        
        $id=input("id");


       
        $list=db("answer")->alias("a")->field("a.*,b.nickname")->where("qid",$id)->join("user b","a.uid = b.uid",'left')->order("a.id desc")->paginate(20,false,['query'=>request()->param()]);

        $this->assign("list",$list);

        $page=$list->render();

        $this->assign("page",$page);

        $this->assign("id",$id);


        
    
        return $this->fetch();
    }
    public function saves()
    {
        $data=input("post.");

        $data['time']=time();
        $name=input("name");
        if($name){
            $user=db("user")->where("nickname|phone",$name)->find();

            $data['uid']=$user['uid'];
        }
   
        $data['status']=1;

        unset($data['name']);
 
 
        $re=db("answer")->insert($data);

        if($re){
            $this->success("发布成功");
        }else{
            $this->error("系统繁忙,请稍后再试");
        }
    }
    public function deletes()
    {
        $id=\input('id');
        $re=db("answer")->where("id=$id")->find();
        if($re){
            $res=db("answer")->where("id=$id")->delete();
            if($res){
                echo '0';
            }else{
                echo '2';
            }
        }else{
            echo'1';
        }
    }

}