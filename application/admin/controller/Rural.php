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
    public function changes()
    {
        $id=\input('id');
        $re=db("rural")->where("id=$id")->find();
        if($re){
            if($re['recom'] == 1){
                $res=db("rural")->where("id=$id")->setField("recom",0);
            }
           
            if($re['recom'] == 0){
                $res=db("rural")->where("id=$id")->setField("recom",1);
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
        $this->redirect('lister');
    }
    public function lister()
    {
        $list=db("rural")->alias("a")->field("a.*,b.name")->where("a.status",1)->join("rural_type b","a.tid = b.id")->order(["a.sort asc","a.id desc"])->paginate(20);

        $this->assign("list",$list);

        $page=$list->render();

        $this->assign("page",$page);

        return $this->fetch();
    }
    public function sort(){
        $data=input('post.');
   
        foreach ($data as $id => $sort){
            db("rural")->where(array('id' => $id ))->setField('sort' , $sort);
        }
        $this->redirect('lister');
    }
    public function add()
    {
        
        $type=db("rural_type")->select();

        $this->assign("type",$type);

        $res=db("culture_city")->where("sid",0)->order(["c_sort asc","cid desc"])->select();
        $this->assign("res",$res);
        
        $city=db("culture_city")->where("pid",0)->order(["c_sort asc","cid desc"])->select();
        $this->assign("city",$city);

        return $this->fetch();
    }

    public function modifys()
    {
        $id=input("id");

        $re=db("rural")->where("id",$id)->find();

        $this->assign("re",$re);

        $type=db("rural_type")->select();

        $this->assign("type",$type);

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
        $data=input("post.");

        $id=input("id");

        $re=db("rural")->where("id",$id)->find();

        if($re){

            $image=request()->file("image");

            if($image){
                $data['image']=uploads("image");
            }else{
                $data['image']=$re['image'];
            }

            if(empty(input("images"))){
                $data['images']=$re['images'];
            }

            $addr=input("addr");

            $result=$this->query_address($addr);

            $data['longs']=$result['lng'];

            $data['lats']=$result['lat'];

            $res=db("rural")->where("id",$id)->update($data);

            if($res){
                $this->success("修改成功",url("lister"));
            }else{
                $this->error("修改失败");
            }




        }else{
            $this->error("参数错误",url('lister'));
        }
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

        $addr=input("addr");

        $result=$this->query_address($addr);

        $data['longs']=$result['lng'];

        $data['lats']=$result['lat'];
 
        $re=db("rural")->insert($data);

        if($re){
            $this->success("发布成功",url("Rural/lister"));
        }else{
            $this->error("系统繁忙,请稍后再试");
        }
    }
    public function query_address($addr){
        $key_words=$addr;
        $header[] = 'Referer: http://lbs.qq.com/webservice_v1/guide-suggestion.html';
        $header[] = 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.139 Safari/537.36';
        $url ="http://apis.map.qq.com/ws/place/v1/suggestion/?&region=&key=OB4BZ-D4W3U-B7VVO-4PJWW-6TKDJ-WPB77&keyword=".$key_words; 
 
        $ch = curl_init();
        //设置选项，包括URL
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
 
        //执行并获取HTML文档内容
        $output = curl_exec($ch);
        
        curl_close($ch);
        
        $result = json_decode($output,true);

       // var_dump($result);exit;

        if(!empty($result['data'][0])){
            return $result['data'][0]['location'];
        }
        
      
       

    }
    public function uploadimg()
    {
        
       
        $file = request()->file('image');
        
        // 移动到框架应用根目录/public/uploads/ 目录下
        $info = $file->validate(['size'=>31457280,'ext'=>'jpg,png,gif,jpeg'])->move(ROOT_PATH . 'public' . DS . 'uploads');
        if($info){
            // 成功上传后 获取上传信息
            $pa=$info->getSaveName();
            $path=str_replace("\\", "/", $pa);
            $paths='/uploads/'.$path;
            $images=\think\Image::open(ROOT_PATH.'/public'.$paths);
            $images->thumb(414,210,\think\Image::THUMB_CENTER)->save(ROOT_PATH.'/public'.$paths);
            $data['response'] =$paths;
            return json($data);
        }else{
            // 上传失败获取错误信息
            $this->error($file->getError());
        }    
        
    }















}