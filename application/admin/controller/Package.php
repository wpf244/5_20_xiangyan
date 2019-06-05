<?php
namespace app\admin\controller;

class Package extends BaseAdmin
{
    public function lister()
    {
        
        $title=input("title");

        if($title){
            $map['title|ticket']=["like","%".$title."%"];
        }else{
            $map=[];
        }

        
        $list=db("package")->where($map)->order(["sort asc","id desc"])->paginate(20,false,['query'=>request()->param()]);

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
            $data['image']=uploads('image');
        }
        $data['time']=time();

        $re=db("package")->insert($data);

        if($re){
            $this->success("保存成功",url('lister'));
        }else{
            $this->error("保存失败");
        }

    }
    public function modifys()
    {
        $id=input("id");

        $re=db("package")->where("id",$id)->find();

        $this->assign("re",$re);
        
        return $this->fetch();
    }
    public function usave()
    {
        $id=input("id");
        $data=input("post.");

        $re=db("package")->where("id",$id)->find();

        if($re){
            $image=request()->file("image");

            if($image){
                $data['image']=uploads('image');
            }else{
                $data['image']=$re['image'];
            }
            $res=db("package")->where("id",$id)->update($data);

            if($res){
                $this->success("修改成功",url("lister"));
            }else{
                $this->error("修改失败",url("lister"));
            }

        }else{
            $this->error("参数错误",url("lister"));
        }

    }
    public function delete()
    {
        $id=\input('id');
        $re=db("package")->where("id=$id")->find();
        if($re){
            $res=db("package")->where("id=$id")->delete();
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
            db("package")->where(array('id' => $id ))->setField('sort' , $sort);
        }
        $this->redirect('lister');
    }
    public function delete_all()
    {
        $id=\input('id');
        $arr=\explode(",",$id);
        foreach($arr as $v){
            $re=db("package")->where("id",$v)->find();
            if($re){
                $res=db("package")->where("id",$v)->delete();
               
            }
        }
        $this->redirect('lister');
    }
    public function change()
    {
        $id=\input('id');
        $re=db("package")->where("id=$id")->find();
        if($re){
            if($re['status'] == 1){
                $res=db("package")->where("id=$id")->setField("status",0);
            }
           
            if($re['status'] == 0){
                $res=db("package")->where("id=$id")->setField("status",1);
            }

            echo '0';
        }else{
            echo '1';
        }
    }

}