<?php
namespace app\admin\controller;

class Publish extends BaseAdmin
{
    public function index()
    {
        
        $status=input("status");

        if(empty($status)){
             $status=0;
        }
        $map['status']=["eq",$status];

        $this->assign("status",$status);
        
        $list=db("publish")->where($map)->order(["sort asc","id desc"])->paginate(20);

        $this->assign("list",$list);

        $page=$list->render();

        $this->assign("page",$page);

        return $this->fetch();
    }
    public function look()
    {
        $id=input("id");

        $re=db("publish")->where("id",$id)->find();

        $this->assign("re",$re);

        $images=$re['images'];

        $arr=explode(",",$images);

        $this->assign("arr",$arr);

        return $this->fetch();
    }
    public function change()
    {
        $id=\input('id');
        $re=db("publish")->where("id=$id")->find();
        if($re){
            $res=db("publish")->where("id=$id")->setField("status",1);
            $this->redirect('index');
        }else{
            $this->redirect('index');
        }
    }
    public function changes()
    {
        $id=\input('id');
        $re=db("publish")->where("id=$id")->find();
        if($re){
            if($re['recom'] == 1){
                $res=db("publish")->where("id=$id")->setField("recom",0);
            }
           
            if($re['recom'] == 0){
                $res=db("publish")->where("id=$id")->setField("recom",1);
            }

            echo '0';
        }else{
            echo '1';
        }
    }
    public function change_all()
    {
        $id=\input('id');
        $arr=\explode(",",$id);
        foreach($arr as $v){
            $re=db("publish")->where("id",$v)->find();
            if($re){
                $res=db("publish")->where("id",$v)->setField("status",1);
               
            }
        }
        $this->redirect('index');
    }
    public function delete()
    {
        $id=\input('id');
        $re=db("publish")->where("id=$id")->find();
        if($re){
            $res=db("publish")->where("id=$id")->delete();
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
            $re=db("publish")->where("id",$v)->find();
            if($re){
                $res=db("publish")->where("id",$v)->delete();
               
            }
        }
        $this->redirect('index');
    }
    public function lister()
    {
        $list=db("strat")->order(["sort asc","id desc"])->paginate(20);

        $this->assign("list",$list);

        $page=$list->render();

        $this->assign("page",$page);
        
        return $this->fetch();
    }
    public function add()
    {
        return $this->fetch();
    }
    public function save()
    {
        $data=input("post.");
        $image=request()->file("image");
        if($image){
            $data['image']=uploads("image");
        }
        $data['time']=\time();

        $re=db("strat")->insert($data);

        if($re){
            $this->success("添加成功",url("lister"));
        }else{
            $this->error("添加失败");
        }
    }
    public function sort(){
        $data=input('post.');
   
        foreach ($data as $id => $sort){
            db("strat")->where(array('id' => $id ))->setField('sort' , $sort);
        }
        $this->redirect('lister');
    }
    public function modifys()
    {
        $id=input("id");

        $re=db("strat")->where("id",$id)->find();

        $this->assign("re",$re);

     
        
        return $this->fetch();
    }
    public function usave()
    {
        $data=input("post.");

        $id=input("id");

        $re=db("strat")->where("id",$id)->find();

        if($re){

            $image=request()->file("image");

            if($image){
                $data['image']=uploads("image");
            }else{
                $data['image']=$re['image'];
            }


            $res=db("strat")->where("id",$id)->update($data);

            if($res){
                $this->success("修改成功",url("lister"));
            }else{
                $this->error("修改失败");
            }

        }else{
            $this->error("参数错误",url('lister'));
        }
    }
    public function deletes()
    {
        $id=\input('id');
        $re=db("strat")->where("id=$id")->find();
        if($re){
            $res=db("strat")->where("id=$id")->delete();
            if($res){
                echo '0';
            }else{
                echo '2';
            }
        }else{
            echo'1';
        }
    }
    public function delete_alls()
    {
        $id=\input('id');
        $arr=\explode(",",$id);
        foreach($arr as $v){
            $re=db("strat")->where("id",$v)->find();
            if($re){
                $res=db("strat")->where("id",$v)->delete();
               
            }
        }
        $this->redirect('lister');
    }
    public function changes_s()
    {
        $id=\input('id');
        $re=db("strat")->where("id=$id")->find();
        if($re){
            if($re['recome'] == 1){
                $res=db("strat")->where("id=$id")->setField("recome",0);
            }
           
            if($re['recome'] == 0){
                $res=db("strat")->where("id=$id")->setField("recome",1);
            }

            echo '0';
        }else{
            echo '1';
        }
    }
    public function changes_r()
    {
        $id=\input('id');
        $re=db("strat")->where("id=$id")->find();
        if($re){
            if($re['status'] == 1){
                $res=db("strat")->where("id=$id")->setField("status",0);
            }
           
            if($re['status'] == 0){
                $res=db("strat")->where("id=$id")->setField("status",1);
            }

            echo '0';
        }else{
            echo '1';
        }
    }
    public function modify()
    {
        $id=input("id");

        $re=db("publish")->where("id",$id)->find();

        $this->assign("re",$re);
        
        return $this->fetch();
    }
    public function usave_s()
    {
        $id=input("id");

        $re=db("publish")->where("id",$id)->find();

        if($re){

            $data=input("post.");

            $res=db("publish")->where("id",$id)->update($data);

            if($res){
                $this->success("修改成功",url('index'));
            }else{
                $this->error("修改失败",url('index'));
            }

        }else{
            $this->error("参数错误",url('index'));
        }
    }
}