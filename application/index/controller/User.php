<?php
namespace app\index\controller;

class User extends BaseUser
{
    /**
    * 个人中心
    *
    * @return void
    */
    public function index()
    {
        $uid=session("userid");

        $re=db("user")->where("uid",$uid)->find();

        $this->assign("re",$re);
        
        return $this->fetch();
    }
    /**
    * 通用设置
    *
    * @return void
    */
    public function my_setting()
    {
        $uid=session("userid");

        $user=db("user")->where("uid",$uid)->find();

        $this->assign("user",$user);
        
        return $this->fetch();
    }
    /**
    * 修改密码
    *
    * @return void
    */
    public function pwd_change()
    {
        return $this->fetch();
    }
    /**
    * 保存密码
    *
    * @return void
    */
    public function pwd_save()
    {
        $uid=session("userid");

        $old_pwd=input("old_pwd");

        $pwd=input("pwd");

        $re=db("user")->where(["uid"=>$uid,"pwd"=>$old_pwd])->find();

        if($re){
            $res=db("user")->where("uid",$uid)->update(["pwd"=>$pwd]);

            if($res){
                echo '0';
            }else{
                echo '2';
            }

        }else{
            echo '1';
        }
    }
    /**
    * 手机号码换绑
    *
    * @return void
    */
    public function tel_change()
    {
        $uid=session("userid");

        $user=db("user")->where("uid",$uid)->find();

        $this->assign("user",$user);
        
        return $this->fetch();
    }
    /**
    * 发送验证码
    *
    * @return void
    */
    public function send_code()
    {
        $uid=session("userid");
        $phone=input('phone');
        $re=db('user')->where(["phone"=>$phone,"uid"=>["neq",$uid]])->find();
        if($re){
            echo '0';
        }else{
            $code =mt_rand(100000,999999);       
            $data['phone']=$phone;
            $data['code']=$code;
            $data['time']=time();
            $re=\db("sms_code")->where("phone",$phone)->find();
            if($re){
                $del=db("sms_code")->where("phone",$phone)->delete();
            }
            $rea=db("sms_code")->insert($data);
            Post($phone,$code);
            echo '1';
           
        }
    }
    /**
    * 换绑手机号
    *
    * @return void
    */
    public function phone_save()
    {
        $uid=session("userid");
        $phone=input("phone");
        $reu=db("user")->where(["phone"=>$phone,"uid"=>['neq',$uid]])->find();
        $code=input("code");
        $re=db("sms_code")->where(['phone'=>$phone,'code'=>$code])->find();
        if($re){
            $time=$re['time'];
            $times=time();
            $c_time=($times-$time);
            if($c_time < 300){
                db("sms_code")->where("id",$re['id'])->delete();
                if($reu){
                    echo '4';
                }else{
                  $data['phone']=input("phone");
               
                  $rea=db("user")->where("uid",$uid)->update($data);

                  if($rea){
                      
                       echo '0';
                  }else{
                    echo '3';
                  }
                    
                        
                }
            }else{
                echo '2';
            }
        }else{
            echo '1';
        }
    }
    /**
    * 个人信息修改
    *
    * @return void
    */
    public function infor_change()
    {
        
        $uid=session("userid");

        $user=db("user")->where("uid",$uid)->find();

        $this->assign("user",$user);
        
        return $this->fetch();
    }
    /**
    * 保存修改
    *
    * @return void
    */
    public function save_info()
    {
        $uid=session("userid"); 
        
        $file=request()->file("image");

        if($file){

          $data['image']=uploads("image");
            
        }
        $data['nickname']=input("nickname");

        $res=db("user")->where("uid",$uid)->update($data);

        if($res){
          $this->success("修改成功");
        }else{
           $this->error("修改失败");
        }
    }
    /**
    * 意见反馈
    *
    * @return void
    */
    public function feed_back()
    {
        
        $re=db("lb")->where("id",1)->find();

        $this->assign("re",$re);
        
        return $this->fetch();
    }
    /**
    * 保存意见反馈
    *
    * @return void
    */
    public function save_feed()
    {
        $uid=session("userid");
        $data=input("post.");
        $data['time']=time();
        $data['uid']=$uid;

        $re=db("opinion")->where("uid",$uid)->whereTime("time","d")->find();

        if(empty($re)){
            $rea=db("opinion")->insert($data);

            if($rea){
                echo '0';
            }else{
                echo '1';
            }
        }else{
            echo '1';
        }
        
    }














}