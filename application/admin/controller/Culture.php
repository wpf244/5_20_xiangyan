<?php
namespace app\admin\controller;

class Culture extends BaseAdmin
{
    public function lister()
    {
        $title=input("title");

        if($title){
            $map['title']=["like","%".$title."%"];
        }else{
            $map =[];
        }

        $list=db("culture")->where($map)->order(["sort asc","id desc"])->paginate(20,false,['query'=>request()->param()]);

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

        $re=db("culture")->insert($data);

        if($re){
            $this->success("保存成功",url("lister"));
        }else{
            $this->error("保存失败");
        }
    }
    public function change()
    {
        $id=\input('id');
        $re=db("culture")->where("id=$id")->find();
        if($re){
            if($re['status'] == 1){
                $res=db("culture")->where("id=$id")->setField("status",0);
            }
           
            if($re['status'] == 0){
                $res=db("culture")->where("id=$id")->setField("status",1);
            }

            echo '0';
        }else{
            echo '1';
        }
    }
    public function changes()
    {
        $id=\input('id');
        $re=db("culture")->where("id=$id")->find();
        if($re){
            if($re['recome'] == 1){
                $res=db("culture")->where("id=$id")->setField("recome",0);
            }
           
            if($re['recome'] == 0){
                $res=db("culture")->where("id=$id")->setField("recome",1);
            }

            echo '0';
        }else{
            echo '1';
        }
    }
   
    public function tu()
    {
        $id=input("id");

        $list=db("culture_banner")->where(["cid"=>$id])->paginate(20);

        $this->assign("list",$list);

        $page=$list->render();

        $this->assign("page",$page);

        $this->assign("id",$id);
        
        return $this->fetch();
    }
    public function save_banner(){
        $id=\input('id');
        if($id){
            $data=input("post.");
           $re=db('culture_banner')->where("id",$id)->find();
           if(request()->file("image")){
               $data['image']=uploads("image");
               deleteImg($re['image']);
           }else{
               $data['image']=$re['image'];
           }
           
           $res=db('culture_banner')->where("id",$id)->update($data);
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
           
            $re=db('culture_banner')->insert($data);
            if($re){
                $this->success("添加成功！");
            }else{
                $this->error("添加失败！");
            } 
        }
         
    }
    public function modifys_tu(){
        $id=input('id');
        $re=db('culture_banner')->where("id",$id)->find();
        echo json_encode($re);
    }
    public function delete_tu()
    {
        $id=\input('id');
        $re=db("culture_banner")->where("id=$id")->find();
        if($re){
            $res=db("culture_banner")->where("id=$id")->delete();
            if($res){
                echo '0';
            }else{
                echo '2';
            }
        }else{
            echo'1';
        }
    }
    public function sort(){
        $data=input('post.');
   
        foreach ($data as $id => $sort){
            db("culture")->where(array('id' => $id ))->setField('sort' , $sort);
        }
        $this->redirect('lister');
    }
    public function modifys()
    {
        $id=input("id");

        $re=db("culture")->where(["status"=>1,"id"=>$id])->find();

        $this->assign("re",$re);


        return $this->fetch();
    }
    public function usave()
    {
        $id=input("id");
        $re=db("culture")->where("id",$id)->find();

        if($re){

            $data=input("post.");

            $image=request()->file("image");

            if($image){
                $data['image']=uploads("image");

                deleteImg($re['image']);
            
            }else{
                $data['image']=$re['image'];
                
            }
           
         


            $re=db("culture")->where("id",$id)->update($data);

            if($re){
                $this->success("修改成功",url("lister"));
            }else{
                $this->error("修改失败");
            }

        }else{
            $this->error("非法操作",url("lister"));
        }
    }
    public function delete()
    {
        $id=\input('id');
        $re=db("culture")->where("id=$id")->find();
        if($re){
            $res=db("culture")->where("id=$id")->delete();
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
            $re=db("culture")->where("id",$v)->find();
            if($re){
                $res=db("culture")->where("id",$v)->delete();
               
            }
        }
        $this->redirect('lister');
    }



















}