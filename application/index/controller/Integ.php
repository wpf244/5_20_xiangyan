<?php
namespace app\index\controller;
use think\Db;
class Integ extends BaseUser
{
    public function index()
    {
        
        $uid=\session("userid");

        $re=db("user")->where("uid",$uid)->find();

        $this->assign("re",$re);

        $res=db("integ_goods")->where(["up"=>1])->order(["sort asc","id desc"])->select();

        $this->assign("res",$res);
        
        return $this->fetch();
    }
    public function pan()
    {
        $id=input("id");

        $re=db("integ_goods")->where("id",$id)->find();

        if($re){
            
            $uid=\session("userid");

            $user=db("user")->where("uid",$uid)->find();

            if($user){
                if($re['kc'] > 0){
                     if($user['integ'] >= $re['integ']){
                         echo '0';
                     }else{
                         echo '4';
                     }
                }else{
                    echo '3';
                }

            }else{
                echo '2';
            }
            
        }else{
            echo '1';
        }
    }

     /**
    * 确认订单
    *
    * @return void
    */
    public function buy()
    {
        $uid=session("userid");

           
        $addr=db("addr")->where(["uid"=>$uid,"a_status"=>1])->find();

        $this->assign("addr",$addr);

        $id=input("id");

        $re=db("integ_goods")->where("id",$id)->find();

        $this->assign("re",$re);


        return $this->fetch();
        
 
    }
    /**
    * 立即兑换
    *
    * @return void
    */
    public function sdd()
    {
        $aid=input("aid");
        $id=input("id");
        $content=input("content");

        $re=db("integ_goods")->where("id",$id)->find();

        if($re){
            
            $uid=\session("userid");

            $user=db("user")->where("uid",$uid)->find();

            if($user){
                if($re['kc'] > 0){
                     if($user['integ'] >= $re['integ']){
                         
                        $data['uid']=$uid;
                        $data['gid']=$id;
                        $data['name']=$re['name'];
                        $data['integ']=$re['integ'];
                        $data['code']='CK-'.uniqid();
                        $data['time']=time();
                        $data['content']=$content;
                        $data['aid']=$aid;

                        $log['uid']=$uid;
                        $log['integ']=$re['integ'];
                        $log['type']=0;
                        $log['content']="兑换商品";
                        $log['time']=time();

                        // 启动事务
                        Db::startTrans();
                        try{
                        db("integ_dd")->insert($data);
                        db("integ_log")->insert($log);
                        db("user")->where("uid",$uid)->setDec("integ",$re['integ']);
                        db("integ_goods")->where("id",$id)->setDec("kc",1);
                        echo '0';
                            // 提交事务
                            Db::commit();    
                        } catch (\Exception $e) {
                            // 回滚事务
                            Db::rollback();

                            echo '5';
                        }

                     }else{
                         echo '4';
                     }
                }else{
                    echo '3';
                }

            }else{
                echo '2';
            }
            
        }else{
            echo '1';
        }

    }
    /**
    * 积分明细
    *
    * @return void
    */
    public function mx()
    {
        $uid=session("userid");

        $res=db("integ_log")->where("uid",$uid)->order(["id desc"])->select();

        $this->assign("res",$res);
        
        return $this->fetch();
    }
    /**
    * 兑换记录
    *
    * @return void
    */
    public function exchange()
    {
        $uid=session("userid");

        $res=db("integ_dd")->where("uid",$uid)->order(["id desc"])->select();

        $this->assign("res",$res);
        
        return $this->fetch();
    }
    /**
    * 酒店优惠券
    *
    * @return void
    */
    public function coupon()
    {
       //商品列表
       $goods=db("goods")->where(["status"=>1,"up"=>1])->order(["sort asc","id desc"])->select();

       $this->assign("goods",$goods);

       //优惠券列表
       $res=db("hotel_coupon")->alias("a")->field("a.*,b.name")->where("a.status",1)->join("hotel b","a.hid=b.id")->order(["a.sort asc","a.id desc"])->limit(3)->select();

       $this->assign("res",$res);
       
        return $this->fetch();
    }
    /**
    * 领取优惠券
    *
    * @return void
    */
    public function save_coupon()
    {
         $id=input("id");

         $re=db("hotel_coupon")->where("id",$id)->find();

         if($re){

            $uid=session("userid");

            $coupon=db("user_coupon_only")->where(["uid"=>$uid,"cid"=>$id])->find();

            if(empty($coupon)){

                $data['uid']=$uid;
                $data['cid']=$id;
                $data['hid']=$re['hid'];
                $data['money']=$re['money'];
                $data['coupon']=$re['coupon'];
                $data['time']=time();

                $rea=db("user_coupon_only")->insert($data);

                if($rea){
                    echo '0';
                }else{
                    echo '3';
                }

            }else{
                echo '2';
            }

         }else{
             echo '1';
         }
    }

}