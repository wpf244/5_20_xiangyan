<?php
namespace app\index\controller;

class Publish extends BaseUser
{
    public function publish()
    {
        
        $dates=date("Y-m-d");

        $this->assign("dates",$dates);
        
        return $this->fetch();
    }
    public function save()
    {
        $data=input("post.");

        $arr=array();

        $files = request()->file('image');
        foreach($files as $file){
            // 移动到框架应用根目录/public/uploads/ 目录下
            $info = $file->validate(['size'=>31457280,'ext'=>'jpg,png,gif'])->move(ROOT_PATH . 'public' . DS . 'uploads');
            if($info){
                // 成功上传后 获取上传信息
                $pa=$info->getSaveName();
                $path=str_replace("\\", "/", $pa);
                $paths='/uploads/'.$path;
                $images=\think\Image::open(ROOT_PATH.'/public'.$paths);
                $images->thumb(414,210,\think\Image::THUMB_CENTER)->save(ROOT_PATH.'/public'.$paths);
                $arr[]=$paths;
            }else{
                // 上传失败获取错误信息
                $this->error($file->getError());
            }    
        }
        $data['image']=$arr[0];
        $data['images']=implode(",",$arr);
        $data['tid']=session("rural");

        $data['time']=time();
        $data['uid']=session("userid");
 
 
        $re=db("rural")->insert($data);

        if($re){
            $this->success("发布成功",url("Rural/index"));
        }else{
            $this->error("系统繁忙,请稍后再试");
        }

    }















}
