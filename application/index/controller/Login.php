<?php
namespace app\index\controller;


class Login extends BaseHome
{
    public function login()
    {
        return $this->fetch();
    }
    public function register()
    {
        return $this->fetch();
    }
    public function send_code()
    {
        $phone=input('phone');
        $re=db('user')->where("phone",$phone)->find();
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
    public function save()
    {
        $phone=input("phone");
        $reu=db("user")->where("phone",$phone)->find();
        $code=input("code");
        $re=db("sms_code")->where(['phone'=>$phone,'code'=>$code])->find();
        if($re){
            $time=$re['time'];
            $times=time();
            $c_time=($times-$time);
            if($c_time < 300){
                db("sms_code")->where("id",$re['id'])->delete();
                if($reu){
                    $this->error("此手机号码已注册",url('Login/login'));
                }else{
                  $data['phone']=input("phone");
                  $data['pwd']=input("pwd");
                  $data['time']=time();
               
                  $rea=db("user")->insert($data);

                  $uid=db("user")->getLastInsID();

                  if($rea){
                       session("userid",$uid);
                       $this->success("注册成功",url('User/index')); 
                  }else{
                    $this->error("系统繁忙,请稍后再试",url('Login/login'));
                  }
                    
                        
                }
            }else{
                $this->error("验证码已失效");
            }
        }else{
            $this->error("验证码错误");
        }
    }
    public function out()
    {
        session("userid",null);
        $this->redirect('Login/login');
    }
    public function forget()
    {
        return $this->fetch();
    }
    public function send_codes()
    {
        $phone=input('phone');
        $re=db('user')->where("phone",$phone)->find();
        if($re){
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
        }else{
            
            echo '0';
           
        }
    }
    public function usave()
    {
        $phone=input("phone");
        $reu=db("user")->where("phone",$phone)->find();
        $code=input("code");
        $re=db("sms_code")->where(['phone'=>$phone,'code'=>$code])->find();
        if($re){
            $time=$re['time'];
            $times=time();
            $c_time=($times-$time);
            if($c_time < 300){
                db("sms_code")->where("id",$re['id'])->delete();
                if($reu){
                    $data['pwd']=input("pwd");
                  
                    $rea=db("user")->where("uid",$reu['uid'])->update($data);
  
                    if($rea){
                        
                         $this->success("修改成功",url('Login/Login')); 
                    }else{

                        $this->error("系统繁忙,请稍后再试",url('Login/login'));

                    }
                    
                }else{
                 
                  $this->error("此手机号码已注册",url('Login/login'));
                              
                }
            }else{
                $this->error("验证码已失效");
            }
        }else{
            $this->error("验证码错误");
        }
    }
      /**
    * 登录
    *
    * @return void
    */
    public function check()
    {
        $phone=input("phone");
        $pwd=input("pwd");
        $re=db("user")->where(["phone"=>$phone,"pwd"=>$pwd])->find();
        if($re){

               session("userid",$re['uid']);
               $this->success("登录成功",url('User/index'));
         
            
        }else{
            $this->error("账号或密码错误",url('Login/login')); 
        }
       
    }















}