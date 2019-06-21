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
            $info = $file->validate(['size'=>31457280,'ext'=>'jpg,png,gif,jpeg'])->move(ROOT_PATH . 'public' . DS . 'uploads');
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

        $data['time']=time();
        $data['uid']=session("userid");

        $addrs=input("addr");

        $addr=str_replace(' ', '', $addrs);



        $result=$this->query_address($addr);

        $data['longs']=$result['lng'];

        $data['lats']=$result['lat'];

        $data['addr']=$addr;
 
 
        $re=db("publish")->insert($data);

        if($re){
            $this->success("发布成功");
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
        }else{
            echo '0';
        }
        
      
       

    }















}
