<?php
namespace app\admin\controller;

class Car extends BaseAdmin
{
    public function lister()
    {
        
        $title=input("title");

        if($title){
            $map['name']=["like","%".$title."%"];
        }else{
            $map =[];
        }

        $list=db("car")->where($map)->order(["sort asc","id desc"])->paginate(20,false,['query'=>request()->param()]);

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

        $re=db("car")->insert($data);

        if($re){
            $this->success("保存成功",url("lister"));
        }else{
            $this->error("保存失败");
        }
    }
    public function modifys()
    {
        $id=input("id");

        $re=db("car")->where(["status"=>1,"id"=>$id])->find();

        $this->assign("re",$re);


        return $this->fetch();
    }
    public function usave()
    {
        $id=input("id");
        $re=db("car")->where("id",$id)->find();

        if($re){

            $data=input("post.");

            $image=request()->file("image");

            if($image){
                $data['image']=uploads("image");

              

            }else{
                $data['image']=$re['image'];
                
            }
           


            $re=db("car")->where("id",$id)->update($data);

            if($re){
                $this->success("修改成功",url("lister"));
            }else{
                $this->error("修改失败");
            }

        }else{
            $this->error("非法操作",url("lister"));
        }
    }
    public function sort(){
        $data=input('post.');
   
        foreach ($data as $id => $sort){
            db("car")->where(array('id' => $id ))->setField('sort' , $sort);
        }
        $this->redirect('lister');
    }
    public function change()
    {
        $id=\input('id');
        $re=db("car")->where("id=$id")->find();
        if($re){
            if($re['status'] == 1){
                $res=db("car")->where("id=$id")->setField("status",0);
            }
           
            if($re['status'] == 0){
                $res=db("car")->where("id=$id")->setField("status",1);
            }

            echo '0';
        }else{
            echo '1';
        }
    }
    public function delete()
    {
        $id=\input('id');
        $re=db("car")->where("id=$id")->find();
        if($re){
            $res=db("car")->where("id=$id")->delete();
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
            $re=db("car")->where("id",$v)->find();
            if($re){
                $res=db("car")->where("id",$v)->delete();
               
            }
        }
        $this->redirect('lister');
    }
    /**
    * 热门车型
    *
    * @return void
    */
    public function play()
    {
        $id=input("id");

        $list=db("car_type")->where(["cid"=>$id])->paginate(20);

        $this->assign("list",$list);

        $page=$list->render();

        $this->assign("page",$page);

        $this->assign("id",$id);
        
        return $this->fetch();
    }
    public function save_play(){
        $id=\input('id');
        if($id){
            $data=input("post.");
           $re=db('car_type')->where("id",$id)->find();
           if(request()->file("image")){
               $data['image']=uploads("image");
               deleteImg($re['image']);
           }else{
               $data['image']=$re['image'];
           }
           
           $res=db('car_type')->where("id",$id)->update($data);
           if($res){
               $this->success("修改成功！");
           }else{
               $this->error("修改失败！");
           }
        }else{
            $data=input("post.");
            if(request()->file("image")){
                $data['image']=uploads("image");
               
            }
           
            $re=db('car_type')->insert($data);
            if($re){
                $this->success("添加成功！");
            }else{
                $this->error("添加失败！");
            } 
        }
         
    }
    public function modifys_play(){
        $id=input('id');
        $re=db('car_type')->where("id",$id)->find();
        echo json_encode($re);
    }
    public function delete_play()
    {
        $id=\input('id');
        $re=db("car_type")->where("id=$id")->find();
        if($re){
            $res=db("car_type")->where("id=$id")->delete();
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