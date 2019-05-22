<?php
namespace app\admin\controller;

class Rural extends BaseAdmin
{
    public function type()
    {
        
        $list=db("rural_type")->order(["sort asc","id desc"])->paginate(20);

        $this->assign("list",$list);

        $page=$list->render();

        $this->assign("page",$page);

        return $this->fetch();
    }
    public function save_type(){
        $id=\input('id');
        if($id){
           $re=db('rural_type')->where("id",$id)->find();
           if(request()->file("image")){
               $data['image']=uploads("image");
               if($re['image']){
                deleteImg($re['image']);
               }
               
           }
           if(request()->file("images")){
            $data['images']=uploads("images");
            if($re['images']){
             deleteImg($re['images']);
            }
            
        }
 
           $data['name']=input('name');
          
           $res=db('rural_type')->where("id",$id)->update($data);
           if($res){
               $this->success("修改成功！");
           }else{
               $this->error("修改失败！");
           }
        }else{
            if(request()->file("image")){
                $data['image']=uploads("image");  
            }
            if(request()->file("images")){
                $data['images']=uploads("images");  
            }
            $data['name']=input('name');
           
            $re=db('rural_type')->insert($data);
            if($re){
                $this->success("添加成功！");
            }else{
                $this->error("添加失败！");
            } 
        }
         
    }
    public function change_type(){
        $id=input("id");

        $re=db("rural_type")->where("id",$id)->find();

        if($re){
            if($re['status'] == 0){
                $res=db("rural_type")->where("id",$id)->setField("status",1);
            }
            if($re['status'] == 1){
                $res=db("rural_type")->where("id",$id)->setField("status",0);
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
        $re=db('rural_type')->where("id=$id")->find();
        echo json_encode($re);
    }
    public function delete_type()
    {
        $id=\input('id');
        $re=db("rural_type")->where("id=$id")->find();
        if($re){
            $res=db("rural_type")->where("id=$id")->delete();
            $this->redirect('type');
        }else{
            $this->redirect('type');
        }
    }
    public function sort_type(){
        $data=input('post.');
   
        foreach ($data as $id => $sort){
            db("rural_type")->where(array('id' => $id ))->setField('sort' , $sort);
        }
        $this->redirect('type');
    }
    public function index()
    {
        $list=db("rural")->alias("a")->field("a.*,b.name")->where("a.status",0)->join("rural_type b","a.tid = b.id")->order("a.id desc")->paginate(20);

        $this->assign("list",$list);

        $page=$list->render();

        $this->assign("page",$page);

        return $this->fetch();
    }
    public function look()
    {
        $id=input("id");

        $re=db("rural")->alias("a")->field("a.*,b.name")->where("a.id",$id)->join("rural_type b","a.tid=b.id")->find();

        $this->assign("re",$re);

        $images=$re['images'];

        $arr=explode(",",$images);

        $this->assign("arr",$arr);

        return $this->fetch();
    }
    public function change()
    {
        $id=\input('id');
        $re=db("rural")->where("id=$id")->find();
        if($re){
            $res=db("rural")->where("id=$id")->setField("status",1);
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
            $re=db("rural")->where("id",$v)->find();
            if($re){
                $res=db("rural")->where("id",$v)->setField("status",1);
               
            }
        }
        $this->redirect('index');
    }
    public function delete()
    {
        $id=\input('id');
        $re=db("rural")->where("id=$id")->find();
        if($re){
            $res=db("rural")->where("id=$id")->delete();
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
            $re=db("rural")->where("id",$v)->find();
            if($re){
                $res=db("rural")->where("id",$v)->delete();
               
            }
        }
        $this->redirect('index');
    }
    public function lister()
    {
        $list=db("rural")->alias("a")->field("a.*,b.name")->where("a.status",1)->join("rural_type b","a.tid = b.id")->order("a.id desc")->paginate(20);

        $this->assign("list",$list);

        $page=$list->render();

        $this->assign("page",$page);

        return $this->fetch();
    }
    public function add()
    {
        
        $res=db("rural_type")->select();

        $this->assign("res",$res);

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

        
        
        $images=input("images");

        $arr=explode(",",$images);
        
        $data['image']=$arr[0];
        $data['time']=time();
        $name=input("name");
        $user=db("user")->where("nickname|phone",$name)->find();

        $data['uid']=$user['uid'];
        $data['status']=1;

        unset($data['name']);
 
 
        $re=db("rural")->insert($data);

        if($re){
            $this->success("发布成功",url("Rural/lister"));
        }else{
            $this->error("系统繁忙,请稍后再试");
        }
    }
    public function uploadimg()
    {
        
       
        $file = request()->file('image');
        
        // 移动到框架应用根目录/public/uploads/ 目录下
        $info = $file->validate(['size'=>31457280,'ext'=>'jpg,png,gif'])->move(ROOT_PATH . 'public' . DS . 'uploads');
        if($info){
            // 成功上传后 获取上传信息
            $pa=$info->getSaveName();
            $path=str_replace("\\", "/", $pa);
            $paths='/uploads/'.$path;
            $images=\think\Image::open(ROOT_PATH.'/public'.$paths);
            $images->save(ROOT_PATH.'/public'.$paths,null,60,true);
            $data['response'] =$paths;
            return json($data);
        }else{
            // 上传失败获取错误信息
            $this->error($file->getError());
        }    
        
    }















}