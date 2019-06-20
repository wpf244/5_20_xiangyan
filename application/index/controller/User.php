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
    /**
    * 我的优惠券
    *
    * @return void
    */
    public function my_coupon()
    {
        
        //全部优惠券
        $uid=session("userid");

        $res=db("user_coupon_only")->alias("a")->field("a.*,b.name")->where("uid",$uid)->join("hotel b","a.hid=b.id")->select();

        $this->assign("res",$res);

        $rey=db("user_coupon_only")->alias("a")->field("a.*,b.name")->where(["uid"=>$uid,"a.status"=>1])->join("hotel b","a.hid=b.id")->select();

        $this->assign("rey",$rey);

        $rew=db("user_coupon_only")->alias("a")->field("a.*,b.name")->where(["uid"=>$uid,"a.status"=>0])->join("hotel b","a.hid=b.id")->select();

        $this->assign("rew",$rew);
        
        return $this->fetch();
    } 
    /**
    * 我的发布
    *
    * @return void
    */
    public function my_publish()
    {
        $uid=session("userid");
        
        $res=db("rural")->alias("a")->field("a.*,b.nickname,b.image as photo")->where(["a.uid"=>$uid])->join("user b","a.uid=b.uid")->order(["id desc"])->select();


        $this->assign("res",$res);
        
        return $this->fetch();
    }
    /**
    * 酒店订单
    *
    * @return void
    */
    public function hotel_dd()
    {
        $uid=session("userid");

        $re=db("order")->where(["uid"=>$uid,"type"=>3,"status"=>0])->order(["id desc"])->select();

        $this->assign("re",$re);

        $res=db("order")->where(["uid"=>$uid,"type"=>3,"status"=>1])->order(["id desc"])->select();

        $this->assign("res",$res);

        $reh=db("order")->where(["uid"=>$uid,"type"=>3,"status"=>2])->order(["id desc"])->select();

        $this->assign("reh",$reh);

        
        return $this->fetch();
    }
    /**
    * 取消订单
    *
    * @return void
    */
    public function delete_dd()
    {
        $id=\input('id');
        $re=db("order")->where("id=$id")->find();
        if($re){
            $del=db("order")->where("id=$id")->delete();
            echo '0';
        }else{
            echo '1';
        }
    }
    /**
    * 团游订单
    *
    * @return void
    */
    public function package_dd()
    {
        $uid=session("userid");

        $re=db("order")->where(["uid"=>$uid,"type"=>1,"status"=>0])->order(["id desc"])->select();

        $this->assign("re",$re);

        $res=db("order")->where(["uid"=>$uid,"type"=>1,"status"=>1])->order(["id desc"])->select();

        $this->assign("res",$res);

        $reh=db("order")->where(["uid"=>$uid,"type"=>1,"status"=>2])->order(["id desc"])->select();

        $this->assign("reh",$reh);

        
        return $this->fetch();
    }
     /**
    * 门票订单
    *
    * @return void
    */
    public function spot_dd()
    {
        $uid=session("userid");

        $re=db("order")->where(["uid"=>$uid,"type"=>2,"status"=>0])->order(["id desc"])->select();

        $this->assign("re",$re);

        $res=db("order")->where(["uid"=>$uid,"type"=>2,"status"=>1])->order(["id desc"])->select();

        $this->assign("res",$res);

        $reh=db("order")->where(["uid"=>$uid,"type"=>2,"status"=>2])->order(["id desc"])->select();

        $this->assign("reh",$reh);

        
        return $this->fetch();
    }
    /**
    * 砍价订单
    *
    * @return void
    */
    public function bargain_dd()
    {
        $uid=session("userid");

        //未付款订单
        $rew=db("bargain_dd")->where(["uid"=>$uid,"status"=>0])->order(["id desc"])->select();

        $this->assign("rew",$rew);

        $res=db("bargain_dd")->where(["uid"=>$uid,"status"=>1])->order(["id desc"])->select();

        $this->assign("res",$res);

        $ref=db("bargain_dd")->where(["uid"=>$uid,"status"=>2])->order(["id desc"])->select();

        $this->assign("ref",$ref);

        $rec=db("bargain_dd")->where(["uid"=>$uid,"status"=>3])->order(["id desc"])->select();

        $this->assign("rec",$rec);


        
        return $this->fetch();
    }
    /**
    * 取消订单
    *
    * @return void
    */
    public function delete_bargain_dd()
    {
        $id=\input('id');
        $re=db("bargain_dd")->where("id=$id")->find();
        if($re){
            $del=db("bargain_dd")->where("id=$id")->delete();
            echo '0';
        }else{
            echo '1';
        }
    }
    /**
    * 确认收货
    *
    * @return void
    */
    public function change_bargain_dd()
    {
        $id=\input('id');
        $re=db("bargain_dd")->where("id",$id)->find();
        if($re){
           if($re['status'] == 2){
            db("bargain_dd")->where("id",$id)->setField("status",3);
            echo '0';
           } else{
               echo '2';
           }
           
        }else{
            echo '1';
        }
    }
    /**
    * 拼团订单
    *
    * @return void
    */
    public function assemble_dd()
    {
        $uid=session("userid");

        //拼团中
        $rew=db("assemble_dd")->where(["uid"=>$uid,"status"=>1])->order(["id desc"])->select();

        $this->assign("rew",$rew);

        //待发货

        $res=db("assemble_dd")->where(["uid"=>$uid,"status"=>2])->order(["id desc"])->select();

        $this->assign("res",$res);

        //待收货

        $ref=db("assemble_dd")->where(["uid"=>$uid,"status"=>3])->order(["id desc"])->select();

        $this->assign("ref",$ref);

        //已完成

        $rec=db("assemble_dd")->where(["uid"=>$uid,"status"=>4])->order(["id desc"])->select();

        $this->assign("rec",$rec);

         //拼团失败

         $rep=db("assemble_dd")->where(["uid"=>$uid,"status"=>5])->order(["id desc"])->select();

         $this->assign("rep",$rep);


        
        return $this->fetch();
    }
    /**
    * 确认收货
    *
    * @return void
    */
    public function change_assemble_dd()
    {
        $id=\input('id');
        $re=db("assemble_dd")->where("id",$id)->find();
        if($re){
           if($re['status'] == 3){
            db("assemble_dd")->where("id",$id)->setField("status",4);
            echo '0';
           } else{
               echo '2';
           }
           
        }else{
            echo '1';
        }
    }












}