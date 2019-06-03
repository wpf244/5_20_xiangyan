<?php
namespace app\admin\controller;

class Food extends BaseAdmin
{
    public function type()
    {
        $list = db('food_type')->order('id desc')->paginate(10);
        $this->assign("list",$list);

        $page=$list->render();

        $this->assign("page",$page);
        
        return $this->fetch();
    }
    public function save_type(){
        if($this->request->isAjax()){
            $id=input("id");
            if($id){
                $data['name']=input("name");
                $res=db("food_type")->where("id",$id)->update($data);
                if($res){
                    $this->success("修改成功！",url('type'));
                }else{
                    $this->error("修改失败！",url('type'));
                }
            }else{
                $data['name']=input("name");
                $re=db("food_type")->insert($data);
                if($re){
                    $this->success("添加成功！",url('type'));
                }else{
                    $this->error("添加失败！",url('type'));
                }
            }
    
        }else{
            $this->success("非法提交",url('type'));
        }
    }
    public function modify_type(){
        $id=input('id');
        $re=db('food_type')->where("id=$id")->find();
        echo json_encode($re);
    }
    function delete_type(){
        $id=input('id');
        db("food_type")->where("id",$id)->delete();
        $this->redirect('type');
    }
    public function lister()
    {
        
        $title=input("title");

        if($title){
            $map['a.name']=["like","%".$title."%"];
        }else{
            $map =[];
        }

        $list=db("food")->alias("a")->field("a.*,b.name as cname,c.name as tname")->where($map)->join("spot_city b","a.cid = b.id")->join("food_type c","a.tid = c.id")->order(["a.sort asc","a.id desc"])->paginate(20,false,['query'=>request()->param()]);

        $this->assign("list",$list);

        $page=$list->render();

        $this->assign("page",$page);

        return $this->fetch();
    }
    public function add()
    {
        $res=db("spot_city")->where("status",1)->select();

        $this->assign("res",$res);

        $type=db("food_type")->select();

        $this->assign("type",$type);
        
        return $this->fetch();
    }
   
    public function save()
    {
        $data=input("post.");

        $image=request()->file("image");

        if($image){
            $data['image']=uploads("image");
        
        }
        $data['time']=time();
    
        $data['status']=1;


        $re=db("food")->insert($data);

        if($re){
            $this->success("发布成功",url("food/lister"));
        }else{
            $this->error("系统繁忙,请稍后再试");
        }
    }
    public function modifys()
    {
        $id=input("id");

        $re=db("food")->where(["status"=>1,"id"=>$id])->find();

        $this->assign("re",$re);

        $res=db("spot_city")->where("status",1)->select();

        $this->assign("res",$res);

        $type=db("food_type")->select();

        $this->assign("type",$type);

        return $this->fetch();
    }
    public function usave()
    {
        $id=input("id");

        $re=db("food")->where("id",$id)->find();

        if($re){

            $data=input("post.");

            $image=request()->file("image");

            if($image){

                $data['image']=uploads("image");

            }else{
                $data['image']=$re['image'];
                
            }
      


            $re=db("food")->where("id",$id)->update($data);

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
            db("food")->where(array('id' => $id ))->setField('sort' , $sort);
        }
        $this->redirect('lister');
    }
    public function delete()
    {
        $id=\input('id');
        $re=db("food")->where("id=$id")->find();
        if($re){
            $res=db("food")->where("id=$id")->delete();
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
            $re=db("food")->where("id",$v)->find();
            if($re){
                $res=db("food")->where("id",$v)->delete();
               
            }
        }
        $this->redirect('lister');
    }
    public function change()
    {
        $id=\input('id');
        $re=db("food")->where("id=$id")->find();
        if($re){
            if($re['status'] == 1){
                $res=db("food")->where("id=$id")->setField("status",0);
            }
           
            if($re['status'] == 0){
                $res=db("food")->where("id=$id")->setField("status",1);
            }

            echo '0';
        }else{
            echo '1';
        }
    }
    public function play()
    {
        $id=input("id");

        $list=db("food_hot")->where(["fid"=>$id])->paginate(20);

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
           $re=db('food_hot')->where("id",$id)->find();
           if(request()->file("image")){
               $data['image']=uploads("image");
               deleteImg($re['image']);
           }else{
               $data['image']=$re['image'];
           }
           
           $res=db('food_hot')->where("id",$id)->update($data);
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
           
            $re=db('food_hot')->insert($data);
            if($re){
                $this->success("添加成功！");
            }else{
                $this->error("添加失败！");
            } 
        }
         
    }
    public function modifys_play(){
        $id=input('id');
        $re=db('food_hot')->where("id",$id)->find();
        echo json_encode($re);
    }
    public function delete_play()
    {
        $id=\input('id');
        $re=db("food_hot")->where("id=$id")->find();
        if($re){
            $res=db("food_hot")->where("id=$id")->delete();
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