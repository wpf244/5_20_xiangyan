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
        $res=db("culture_city")->where("sid",0)->order(["c_sort asc","cid desc"])->select();
        $this->assign("res",$res);
        
        $city=db("culture_city")->where("pid",0)->order(["c_sort asc","cid desc"])->select();
        $this->assign("city",$city);
        
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

        $res=db("culture_city")->where("sid",0)->order(["c_sort asc","cid desc"])->select();
        $this->assign("res",$res);

        $sid=$re['cid'];
        
        $city=db("culture_city")->where(["pid"=>0,"sid"=>$sid])->order(["c_sort asc","cid desc"])->select();
        $this->assign("city",$city);

        $citys=db("culture_city")->where("pid",$re['xid'])->order(["c_sort asc","cid desc"])->select();
        $this->assign("citys",$citys);



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
    public function city()
    {
        $list=db('culture_city')->where("sid=0")->order(["c_sort asc","cid asc"])->select();
        foreach($list as $k => $v){
            $list1=db("culture_city")->where(["sid"=>$v['cid'],"pid"=>0])->order(["c_sort asc","cid asc"])->select();

            $list[$k]['list1']=$list1;

            foreach($list1 as $kk => $vv){
                
                $list2=db("culture_city")->where("pid",$vv['cid'])->order(["c_sort asc","cid asc"])->select();
                
                $list[$k]['list1'][$kk]['list2']=$list2;
            }
        }
        $this->assign("list",$list);

        return $this->fetch();
    }
    public function add_city()
    {
        $res=\db("culture_city")->where("sid=0")->order("c_sort asc")->select();
        $this->assign("res",$res);
        
        return $this->fetch();
    }
    public function save_city()
    {
        $data=input('post.');
        $re=db("culture_city")->insert($data);
        if($re){
            $this->success("添加成功",url('city'));
        }else{
            $this->error("添加失败",url('city'));
        }
    }
    public function modifys_city()
    {
        $res=\db("culture_city")->where("sid=0")->select();
        $this->assign("res",$res);

        $id=input('id');
        $re=db("culture_city")->where("cid=$id")->find();
        $this->assign("re",$re);

        $sid=$re['sid'];

        $city=db("culture_city")->where(["sid"=>$sid,"pid"=>0])->select();

        $this->assign("city",$city);
        
        return $this->fetch();
    }
    public function usave_city()
    {
        $cid=input('cid');
        $sid=input("sid");
        $pid=input("pid");
        if($cid != $sid && $pid != $cid){
            $data=input('post.');
            $re=\db("culture_city")->where("cid=$cid")->find();
            if($re){
               $res=\db("culture_city")->where("cid=$cid")->update($data);
               if($res){
                   $this->success("修改成功",url('city'));
               }else{
                   $this->error("修改失败",url('city'));
               }
            }else{
                $this->error("参数错误");
            }
        }else{
            $this->error("非法操作");
        }
        
    }
    public function getnext()
    {
        $cid=input("cid");
        $re=db("culture_city")->where("pid",$cid)->order(["c_sort asc","cid desc"])->select();
       
        if($re){
           echo json_encode($re);
        }else{
            echo 0;
        }
    }
    public function getnexts()
    {
        $sid=input("sid");
        $re=db("culture_city")->where(["sid"=>$sid,"pid"=>0])->order(["c_sort asc","cid desc"])->select();
     //   var_dump($re);exit;
        if($re){
           echo json_encode($re);
        }else{
            echo 0;
        }
    }
    public function change_sort()
    {
        $cid=input('fieldid');
        $val=input('fieldvalue');
       // var_dump($val,$cid);exit;
        $re=db("culture_city")->where("cid=$cid")->find();
        if($re){
            $res=db("culture_city")->where("cid=$cid")->setField("c_sort",$val);
            if($res){
                echo '1';
            }else{
                echo '0';
            }
        }else{
            echo '0';
        }
    }
    public function delete_city()
    {
        $id=input('id');
        $re=\db("culture_city")->where("cid=$id")->find();
        if($re){
            $del=db("culture_city")->where("cid=$id")->delete();
            $res=db("culture_city")->where("pid=$id")->select();
            if($res){
                $dels=db("culture_city")->where("pid=$id")->delete();
            }
            echo '0';
        }else{
            echo '1';
        }
    }



















}