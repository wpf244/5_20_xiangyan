<?php
namespace app\admin\controller;

class Riders extends BaseAdmin
{
    public function lister()
    {
        $list=db("riders")->alias("a")->field("a.*,b.name as fname")->where(["a.is_delete"=>0,"type"=>0])->join("shop b","a.sid=b.id")->order(["a.sort asc","a.id desc"])->paginate(20,false,['query'=>request()->param()]);
        $this->assign("list",$list);

        $page=$list->render();
        $this->assign("page",$page);
        return $this->fetch();
    }
    public function add()
    {
        $res=db("shop")->where(['is_delete'=>0,'up'=>1])->select();
        $this->assign("res",$res);

        return $this->fetch();
    }
    public function save()
    {
        $data=input("post.");
        $data['time']=time();
        $re=db("riders")->insert($data);
        if($re){
           if(input('type')){
            $this->success("添加成功",url('index'));
           }else{
            $this->success("添加成功",url('lister'));
           } 
            
        }else{
            $this->error("添加失败");
        }
    }
    public function modifys()
    {
      
        $res=db("shop")->where(['is_delete'=>0,'up'=>1])->select();
        $this->assign("res",$res);

        $id=input("id");
        $re=db("riders")->where("id",$id)->find();
        $this->assign("re",$re);
        
        return $this->fetch();
    }
    public function usave()
    {
        $id=input("id");
        $re=db("riders")->where(['id'=>$id,'is_delete'=>0])->find();
        if($re){
            $data=input("post.");
          
            $res=db("riders")->where(['id'=>$id,'is_delete'=>0])->update($data);
            if($res){
                if($re['type'] == 1){
                    $this->success("修改成功",url('index'));
                   } else{
                    $this->success("修改成功",url('lister'));
                   }
                
            }else{
                if($re['type'] == 1){
                    $this->error("修改失败",url('index'));
                   } else{
                    $this->error("修改失败",url('lister'));
                   }
               
            }
        }else{
           if($re['type'] == 1){
            $this->error("参数错误",url('index'));
           } else{
            $this->error("参数错误",url('lister'));
           }
            
        }
    }
    public function delete(){
        $id=input('id');
        $re=db("riders")->where("id=$id")->find();
        if($re){
           $del=db("riders")->where("id=$id")->setField("is_delete",-1);
           if($del){
               echo '1';
           }else{
               echo '2';
           }
        }else{
            echo '0';
        }
       
    }
    public function delete_all(){
        $id=input('id');
        $arr=explode(",", $id);
        foreach ($arr as $v){
            $re=db('riders')->where("id=$v")->find();
            if($re){
                $del=db('riders')->where("id=$v")->setField("is_delete",-1);
        }
        
       }
       if($re['type'] == 1){
        $this->redirect('index');
       }else{
        $this->redirect('lister');
       }
       
    }
    public function sort(){
        $data=input('post.');
        foreach ($data as $id => $sort){
            db('riders')->where(array('id' => $id ))->setField('sort' , $sort);
        }
        $this->redirect('lister');
    }
    public function sorts(){
        $data=input('post.');
        foreach ($data as $id => $sort){
            db('riders')->where(array('id' => $id ))->setField('sort' , $sort);
        }
        $this->redirect('index');
    }
    public function index()
    {
        $list=db("riders")->alias("a")->field("a.*,b.name as fname")->where(["a.is_delete"=>0,"type"=>1])->join("shop b","a.sid=b.id")->order(["a.sort asc","a.id desc"])->paginate(20,false,['query'=>request()->param()]);
        $this->assign("list",$list);

        $page=$list->render();
        $this->assign("page",$page);
        return $this->fetch();
    }
    public function adds()
    {
        $res=db("shop")->where(['is_delete'=>0,'up'=>1])->select();
        $this->assign("res",$res);

        return $this->fetch();
    }
    public function img(){
        $id=input('id');
        $list=db('riders_img')->where("rid=$id")->paginate(10);
        $this->assign("list",$list);
        
        $page=$list->render();
        $this->assign("page",$page);
        
        $this->assign("id",$id);
        
        return $this->fetch();
    }
    public function i_save(){
        $id=input('id');
        if($id){
            $data=input('post.');
            $re=db('riders_img')->where("id=$id")->find();
            if(!is_string(input('image'))){
                $data['image']=uploads("image");
            }else{
                $data['image']=$re['image'];
            }
            if(input('status')){
                $data['status']=1;
            }else{
                $data['status']=$re['status'];
            }
            $res=db("riders_img")->where("id",$id)->update($data);
            if($res){
                $this->success("修改成功！");
            }else{
                $this->error("修改失败！");
            }
        }else{
            $data=input('post.');
            if(!is_string(input('image'))){
                $data['image']=uploads("image");
            }
            if(input('status')){
                $data['status']=1;
            }
    
            $re=db("riders_img")->insert($data);
            if($re){
                $this->success("添加成功！");
            }else{
                $this->error("添加失败！");
            }
        }
         
    }
    public function modify_i(){
        $id=input('id');
        $re=db('riders_img')->where("id=$id")->find();
    
        echo json_encode($re);
    }
    public function change_i(){
        $id=input('id');
        $re=db('riders_img')->where("id=$id")->find();
        if($re){
            if($re['status'] == 0){
                $res=db('riders_img')->where("id=$id")->setField("status",1);
            }
            if($re['status'] == 1){
                $res=db('riders_img')->where("id=$id")->setField("status",0);
    
            }
            echo '0';
        }else{
            echo '1';
        }
    }
    public function delete_i(){
        $id=input('id');
        $re=db('riders_img')->where("id=$id")->find();
        if($re){
            $del=db('riders_img')->where("id=$id")->delete();
            echo '1';
        }else{
            echo '0';
        }
    }















}