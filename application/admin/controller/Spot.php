<?php
namespace app\admin\controller;

class Spot extends BaseAdmin
{
    public function city()
    {
        $list=db("spot_city")->order(["sort asc","id desc"])->paginate(20);

        $this->assign("list",$list);

        $page=$list->render();

        $this->assign("page",$page);

        return $this->fetch();
    }
    public function save_type(){
        $id=\input('id');
        if($id){
           $re=db('spot_city')->where("id",$id)->find();
           if(request()->file("image")){
               $data['image']=uploads("image");
               if($re['image']){
                deleteImg($re['image']);
               }
               
           }
        
 
           $data['name']=input('name');
          
           $res=db('spot_city')->where("id",$id)->update($data);
           if($res){
               $this->success("修改成功！");
           }else{
               $this->error("修改失败！");
           }
        }else{
            if(request()->file("image")){
                $data['image']=uploads("image");  
            }
        
            $data['name']=input('name');
           
            $re=db('spot_city')->insert($data);
            if($re){
                $this->success("添加成功！");
            }else{
                $this->error("添加失败！");
            } 
        }
         
    }
    public function change_type(){
        $id=input("id");

        $re=db("spot_city")->where("id",$id)->find();

        if($re){
            if($re['status'] == 0){
                $res=db("spot_city")->where("id",$id)->setField("status",1);
            }
            if($re['status'] == 1){
                $res=db("spot_city")->where("id",$id)->setField("status",0);
            }
            if($res){
                echo '1';
            }else{
                echo '0';
            }
        }else{
            echo '2';
        }  
    }
    public function modifys_type(){
        $id=input("id");
        $re=db('spot_city')->where("id=$id")->find();
        echo json_encode($re);
    }
    public function delete_type()
    {
        $id=\input('id');
        $re=db("spot_city")->where("id=$id")->find();
        if($re){
            $res=db("spot_city")->where("id=$id")->delete();
            $this->redirect('city');
        }else{
            $this->redirect('city');
        }
    }
    public function sort_type(){
        $data=input('post.');
   
        foreach ($data as $id => $sort){
            db("spot_city")->where(array('id' => $id ))->setField('sort' , $sort);
        }
        $this->redirect('city');
    }
    
    public function sever()
    {
        
        $list=db("spot_sever")->order(["id desc"])->paginate(20);

        $this->assign("list",$list);

        $page=$list->render();

        $this->assign("page",$page);

        return $this->fetch();
    }
    public function save_sever(){
        $id=\input('id');
        if($id){
           $re=db('spot_sever')->where("id",$id)->find();
           if(request()->file("image")){
               $data['sever_image']=uploads("image");
               if($re['sever_image']){
                deleteImg($re['sever_image']);
               }
               
           }
        
 
           $data['sever_name']=input('name');
          
           $res=db('spot_sever')->where("id",$id)->update($data);
           if($res){
               $this->success("修改成功！");
           }else{
               $this->error("修改失败！");
           }
        }else{
            if(request()->file("image")){
                $data['sever_image']=uploads("image");  
            }
        
            $data['sever_name']=input('name');
           
            $re=db('spot_sever')->insert($data);
            if($re){
                $this->success("添加成功！");
            }else{
                $this->error("添加失败！");
            } 
        }
         
    }
    public function modifys_sever(){
        $id=input("id");
        $re=db('spot_sever')->where("id=$id")->find();
        echo json_encode($re);
    }
    public function delete_sever()
    {
        $id=\input('id');
        $re=db("spot_sever")->where("id=$id")->find();
        if($re){
            $res=db("spot_sever")->where("id=$id")->delete();
            $this->redirect('sever');
        }else{
            $this->redirect('sever');
        }
    }
    public function lister()
    {

        $title=input("title");

        if($title){
            $map['a.name']=["like","%".$title."%"];
        }else{
            $map =[];
        }

        $list=db("spot")->alias("a")->field("a.*,b.name")->where($map)->join("spot_city b","a.cid = b.id")->order(["a.sort asc","a.id desc"])->paginate(20);

        $this->assign("list",$list);

        $page=$list->render();

        $this->assign("page",$page);

        return $this->fetch();
    }
    public function add()
    {
        $res=db("spot_city")->where("status",1)->select();

        $this->assign("res",$res);

        $sever=db("spot_sever")->select();

        $this->assign("sever",$sever);
        
        return $this->fetch();
    }
    public function save()
    {
        $data=input("post.");

        $image=request()->file("image");

        if($image){
            $data['image']=uploads("image");
        
        }
        $banner=request()->file("banner");

        if($banner){
            $data['banner']=uploads("banner");
        
        }

        $sever=$data['severs'];
     //   var_dump($sever);exit;

        $data['severs']=implode(",",$sever);

       $data['time']=\time();

        $re=db("spot")->insert($data);

        if($re){
            $this->success("保存成功",url("lister"));
        }else{
            $this->error("保存失败");
        }
    }
    public function modifys()
    {
        $id=input("id");

        $re=db("spot")->where(["status"=>1,"id"=>$id])->find();

        $this->assign("re",$re);

        $severs=explode(",",$re['severs']);

        $this->assign("severs",$severs);

        $res=db("spot_city")->where("status",1)->select();

        $this->assign("res",$res);

        $sever=db("spot_sever")->select();

        $this->assign("sever",$sever);

        return $this->fetch();
    }
    public function usave()
    {
        $id=input("id");
        $re=db("spot")->where("id",$id)->find();

        if($re){

            $data=input("post.");

            $image=request()->file("image");

            if($image){
                $data['image']=uploads("image");

                deleteImg($re['image']);
            
            }else{
                $data['image']=$re['image'];
                
            }
            $banner=request()->file("banner");

            if($banner){

                $data['banner']=uploads("banner");

                deleteImg($re['banner']);
            
            }else{
                $data['banner']=$re['banner'];
            }

            $sever=$data['severs'];
        //   var_dump($sever);exit;

            $data['severs']=implode(",",$sever);


            $re=db("spot")->where("id",$id)->update($data);

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
            db("spot")->where(array('id' => $id ))->setField('sort' , $sort);
        }
        $this->redirect('lister');
    }
    public function change()
    {
        $id=\input('id');
        $re=db("spot")->where("id=$id")->find();
        if($re){
            if($re['status'] == 1){
                $res=db("spot")->where("id=$id")->setField("status",0);
            }
           
            if($re['status'] == 0){
                $res=db("spot")->where("id=$id")->setField("status",1);
            }

            echo '0';
        }else{
            echo '1';
        }
    }
    public function delete()
    {
        $id=\input('id');
        $re=db("spot")->where("id=$id")->find();
        if($re){
            $res=db("spot")->where("id=$id")->delete();
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
            $re=db("spot")->where("id",$v)->find();
            if($re){
                $res=db("spot")->where("id",$v)->delete();
               
            }
        }
        $this->redirect('lister');
    }
    public function stretagy()
    {
        
        $id=input("id");

        $re=db("spot_gl")->where("sid",$id)->find();

        $this->assign("re",$re);

        $this->assign("id",$id);

        return $this->fetch();
    }
    public function save_s()
    {
        $data=input("post.");

        $image=request()->file("s_image");

       

        $id=input("sid");

        $re=db("spot_gl")->where("sid",$id)->find();

        if($re){
            if($image){
                $data['s_image']=uploads("s_image");
            }else{
                $data['s_image']=$re['s_image'];
            }
            $res=db("spot_gl")->where("sid",$id)->update($data);

            if($res){
                $this->success("修改成功");
            }else{
                $this->error("修改失败");
            }
        }else{
            if($image){
                $data['s_image']=uploads("s_image");
            }
            $rea=db("spot_gl")->insert($data);
            if($rea){
                $this->success("修改成功");
            }else{
                $this->error("修改失败");
            }
        }

    }
    /**
    * 玩转景区
    *
    * @return void
    */
    public function play()
    {
        $id=input("id");

        $list=db("spot_play")->where(["sid"=>$id])->paginate(20);

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
           $re=db('spot_play')->where("id",$id)->find();
           if(request()->file("image")){
               $data['image']=uploads("image");
               deleteImg($re['image']);
           }else{
               $data['image']=$re['image'];
           }
           
           $res=db('spot_play')->where("id",$id)->update($data);
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
           
            $re=db('spot_play')->insert($data);
            if($re){
                $this->success("添加成功！");
            }else{
                $this->error("添加失败！");
            } 
        }
         
    }
    public function modifys_play(){
        $id=input('id');
        $re=db('spot_play')->where("id",$id)->find();
        echo json_encode($re);
    }
    public function delete_play()
    {
        $id=\input('id');
        $re=db("spot_play")->where("id=$id")->find();
        if($re){
            $res=db("spot_play")->where("id=$id")->delete();
            if($res){
                echo '0';
            }else{
                echo '2';
            }
        }else{
            echo'1';
        }
    }
    public function ticket()
    {
        
        $id=input("id");

        $list=db("spot_ticket")->where(["sid"=>$id])->paginate(20);

        $this->assign("list",$list);

        $page=$list->render();

        $this->assign("page",$page);

        $this->assign("id",$id);

        return $this->fetch();
    }
    public function save_ticket(){
        $id=\input('id');
        if($id){

            $data=input("post.");
         
           $res=db('spot_ticket')->where("id",$id)->update($data);
           if($res){
               $this->success("修改成功！");
           }else{
               $this->error("修改失败！");
           }
        }else{
            $data=input("post.");
            
            $re=db('spot_ticket')->insert($data);
            if($re){
                $this->success("添加成功！");
            }else{
                $this->error("添加失败！");
            } 
        }
         
    }
    public function modifys_ticket(){
        $id=input('id');
        $re=db('spot_ticket')->where("id",$id)->find();
        echo json_encode($re);
    }
    












}