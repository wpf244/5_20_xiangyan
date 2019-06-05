<?php
namespace app\admin\controller;

class Hotel extends BaseAdmin
{
    public function lister()
    {
        
        $title=input("title");

        if($title){
            $map['name']=["like","%".$title."%"];
        }else{
            $map =[];
        }
        
        $list=db("hotel")->where($map)->order(["sort asc","id desc"])->paginate(20,false,['query'=>request()->param()]);

        $this->assign("list",$list);

        $page=$list->render();

        $this->assign("page",$page);

        return $this->fetch();
    }
    public function coupon()
    {
        $re=db("coupon")->where("id",1)->find();

        $this->assign("re",$re);
        
        return $this->fetch();
    }
    public function save_coupon()
    {
        $data=input("post.");

        $res=db("coupon")->where("id",1)->update($data);

        if($res){
            $this->success("修改成功");
        }else{
            $this->error("修改失败");
        }
    }
    public function sever()
    {
        
        $list=db("hotel_sever")->order(["id desc"])->paginate(20);

        $this->assign("list",$list);

        $page=$list->render();

        $this->assign("page",$page);

        return $this->fetch();
    }
    
    public function save_sever(){
        $id=\input('id');
        if($id){
           $re=db('hotel_sever')->where("id",$id)->find();
           if(request()->file("image")){
               $data['sever_image']=uploads("image");
               if($re['sever_image']){
                deleteImg($re['sever_image']);
               }
               
           }
        
 
           $data['sever_name']=input('name');
          
           $res=db('hotel_sever')->where("id",$id)->update($data);
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
           
            $re=db('hotel_sever')->insert($data);
            if($re){
                $this->success("添加成功！");
            }else{
                $this->error("添加失败！");
            } 
        }
         
    }
    public function add()
    {
        $sever=db("hotel_sever")->select();

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
       

        $sever=$data['severs'];
    

        $data['severs']=implode(",",$sever);

   

        $re=db("hotel")->insert($data);

        if($re){
            $this->success("保存成功",url("lister"));
        }else{
            $this->error("保存失败");
        }
    }
    public function modifys()
    {
        $id=input("id");

        $re=db("hotel")->where(["status"=>1,"id"=>$id])->find();

        $this->assign("re",$re);

        $severs=explode(",",$re['severs']);

        $this->assign("severs",$severs);

     

        $sever=db("hotel_sever")->select();

        $this->assign("sever",$sever);

        return $this->fetch();
    }
    public function usave()
    {
        $id=input("id");
        $re=db("hotel")->where("id",$id)->find();

        if($re){

            $data=input("post.");

            $image=request()->file("image");

            if($image){
                $data['image']=uploads("image");

                deleteImg($re['image']);
            
            }else{
                $data['image']=$re['image'];
                
            }
           
            $sever=$data['severs'];
        //   var_dump($sever);exit;

            $data['severs']=implode(",",$sever);


            $re=db("hotel")->where("id",$id)->update($data);

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
            db("hotel")->where(array('id' => $id ))->setField('sort' , $sort);
        }
        $this->redirect('lister');
    }
    public function change()
    {
        $id=\input('id');
        $re=db("hotel")->where("id=$id")->find();
        if($re){
            if($re['status'] == 1){
                $res=db("hotel")->where("id=$id")->setField("status",0);
            }
           
            if($re['status'] == 0){
                $res=db("hotel")->where("id=$id")->setField("status",1);
            }

            echo '0';
        }else{
            echo '1';
        }
    }
    public function changes()
    {
        $id=\input('id');
        $re=db("hotel")->where("id=$id")->find();
        if($re){
            if($re['recome'] == 1){
                $res=db("hotel")->where("id=$id")->setField("recome",0);
            }
           
            if($re['recome'] == 0){
                $res=db("hotel")->where("id=$id")->setField("recome",1);
            }

            echo '0';
        }else{
            echo '1';
        }
    }
    public function delete()
    {
        $id=\input('id');
        $re=db("hotel")->where("id=$id")->find();
        if($re){
            $res=db("hotel")->where("id=$id")->delete();
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
            $re=db("hotel")->where("id",$v)->find();
            if($re){
                $res=db("hotel")->where("id",$v)->delete();
               
            }
        }
        $this->redirect('lister');
    }
    public function banner()
    {
        $id=input("id");

        $list=db("hotel_banner")->where(["hid"=>$id])->paginate(20);

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
           $re=db('hotel_banner')->where("id",$id)->find();
           if(request()->file("image")){
               $data['image']=uploads("image");
               deleteImg($re['image']);
           }else{
               $data['image']=$re['image'];
           }
           
           $res=db('hotel_banner')->where("id",$id)->update($data);
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
           
            $re=db('hotel_banner')->insert($data);
            if($re){
                $this->success("添加成功！");
            }else{
                $this->error("添加失败！");
            } 
        }
         
    }
    public function modifys_play(){
        $id=input('id');
        $re=db('hotel_banner')->where("id",$id)->find();
        echo json_encode($re);
    }
    public function delete_play()
    {
        $id=\input('id');
        $re=db("hotel_banner")->where("id=$id")->find();
        if($re){
            $res=db("hotel_banner")->where("id=$id")->delete();
            if($res){
                echo '0';
            }else{
                echo '2';
            }
        }else{
            echo'1';
        }
    }
    /**
    * 酒店客房
    *
    * @return void
    */
    public function room()
    {
        $id=input("id");

        $list=db("hotel_room")->where(["hid"=>$id])->paginate(20);

        $this->assign("list",$list);

        $page=$list->render();

        $this->assign("page",$page);

        $this->assign("id",$id);
        
        return $this->fetch();
    }
    public function save_room(){
        $id=\input('id');
        if($id){
            $data=input("post.");
           $re=db('hotel_room')->where("id",$id)->find();
           if(request()->file("image")){
               $data['image']=uploads("image");
               deleteImg($re['image']);
           }else{
               $data['image']=$re['image'];
           }
           
           $res=db('hotel_room')->where("id",$id)->update($data);
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
           
            $re=db('hotel_room')->insert($data);
            if($re){
                $this->success("添加成功！");
            }else{
                $this->error("添加失败！");
            } 
        }
         
    }
    public function modifys_room(){
        $id=input('id');
        $re=db('hotel_room')->where("id",$id)->find();
        echo json_encode($re);
    }
    public function delete_room()
    {
        $id=\input('id');
        $re=db("hotel_room")->where("id=$id")->find();
        if($re){
            $res=db("hotel_room")->where("id=$id")->delete();
            if($res){
                echo '0';
            }else{
                echo '2';
            }
        }else{
            echo'1';
        }
    }
    public function change_room()
    {
        $id=\input('id');
        $re=db("hotel_room")->where("id=$id")->find();
        if($re){
            if($re['status'] == 1){
                $res=db("hotel_room")->where("id=$id")->setField("status",0);
            }
           
            if($re['status'] == 0){
                $res=db("hotel_room")->where("id=$id")->setField("status",1);
            }

            echo '0';
        }else{
            echo '1';
        }
    }
    public function change_rooms()
    {
        $id=\input('id');
        $re=db("hotel_room")->where("id=$id")->find();
        if($re){
            if($re['cancel'] == 1){
                $res=db("hotel")->where("id=$id")->setField("cancel",0);
            }
           
            if($re['cancel'] == 0){
                $res=db("hotel_room")->where("id=$id")->setField("cancel",1);
            }

            echo '0';
        }else{
            echo '1';
        }
    }

















}