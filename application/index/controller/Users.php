<?php
namespace app\index\controller;

use think\Db;

class Users extends BaseUser
{
    public function add_addr()
    {
        return $this->fetch();
    }
    /**
    * 保存收货地址
    *
    * @return void
    */
    public function save_addr(){
        $data=input('post.');
        $uid=session("userid");
        $re=db("addr")->where("uid",$uid)->find();
        if(empty($re)){
            $data['a_status']=1;
        }
        $data['uid']=$uid;
        $rea=db("addr")->insert($data);
        if($rea){
            echo '0';
        }else{
            echo '1';
        }
    }
    /**
    * 收货地址列表
    *
    * @return void
    */
    public function addr()
    {
        $uid=session("userid");
        $res=db("addr")->where("uid",$uid)->order("aid asc")->select();
        $this->assign("res",$res);
        
        return $this->fetch();
    }
    public function choose_addr()
    {
        $uid=session("userid");
        $res=db("addr")->where("uid",$uid)->order("a_status desc")->select();
        $this->assign("res",$res);
        
        return $this->fetch();
    }
     //修改默认地址
     public function change_status()
     {
         $uid=session("userid");
         $id=input("id");
         $re=db("addr")->where("aid=$id")->find();
         if($re['a_status'] == 0){
             $rea=db("addr")->where("uid",$uid)->setField("a_status",0);
             $res=db("addr")->where("aid",$id)->setField("a_status",1);
             if($res){
                 echo '1';
             }else{
                 echo '0';
             }
         }else{
             echo '3';
         }
     }
     //删除收货地址
    public function delete_addr()
    {
        $aid=input('id');
        $re=db("addr")->where("aid",$aid)->find();
        if($re){
            $del=db("addr")->where("aid",$aid)->delete();
            echo '0';
        }else{
            echo '1';
        }
    }
      //修改收货地址
      public function update_addr()
      {
          $aid=input('aid');
          $re=db("addr")->where("aid",$aid)->find();
          $this->assign("re",$re);
          return $this->fetch();
      }
      public function usave_addr()
      {
          $aid=input('aid');
          $data=input('post.');
          $re=db("addr")->where("aid",$aid)->find();
          if($re){
              $res=db("addr")->where("aid",$aid)->update($data);
              echo '0';
          }else{
            echo '1';
          }
      }
     /**
     * 个人中心
     *
     * @return void
     */
     public function indexs()
     {
        $uid=session("userid");
        
        $user=db("user")->where("uid",$uid)->find();

        $this->assign("user",$user);
        
        return $this->fetch();
     }
     /**
     * 我的收藏
     *
     * @return void
     */
     public function collect()
     {
        $uid=session("userid");
        
        $res=db("collect")->alias("a")->where("uid",$uid)->join("goods b","a.gid=b.id")->select();

        $this->assign("res",$res);
        
        return $this->fetch();
     }
     /**
     * 待付款订单
     *
     * @return void
     */
     public function order_1()
     {
        $uid=session("userid");

        $res=db("car_dd")->where(["uid"=>$uid,"gid"=>0,"status"=>0])->select();

        foreach($res as $k => $v){
            $pay=explode(",",$v['pay']);

            $res[$k]['list']=db("car_dd")->where("code","in",$pay)->select();
        }

        $this->assign("res",$res);
        
        return $this->fetch();
     }
     /**
     * 取消订单
     *
     * @return void
     */
     public function delete_dd()
     {
         $did=input('id');
         $re=db("car_dd")->where("did",$did)->find();
         if($re){
             db("car_dd")->where("did",$did)->delete();

             $pay=\explode(",",$re['pay']);

             $res=db("car_dd")->where("code","in",$pay)->find();

             if($res){
                db("car_dd")->where("code","in",$pay)->delete();
             }

             echo '0';
         }else{
             echo '1';
         }
     }
     /**
     * 待发货订单
     *
     * @return void
     */
     public function order_2()
     {
        $uid=session("userid");

        $res=db("car_dd")->where(["uid"=>$uid,"gid"=>0,"status"=>1])->select();

        foreach($res as $k => $v){
            $pay=explode(",",$v['pay']);

            $res[$k]['list']=db("car_dd")->where("code","in",$pay)->select();
        }

        $this->assign("res",$res); 
        
        return $this->fetch();
     }
     /**
     * 待收货订单
     *
     * @return void
     */
     public function order_3()
     {
        $uid=session("userid");

        $res=db("car_dd")->where(["uid"=>$uid,"gid"=>0,"status"=>2])->select();

        foreach($res as $k => $v){
            $pay=explode(",",$v['pay']);

            $res[$k]['list']=db("car_dd")->where("code","in",$pay)->select();
        }

        $this->assign("res",$res); 
        
        return $this->fetch();
     }
      /**
     * 待评价订单
     *
     * @return void
     */
     public function order_4()
     {
        $uid=session("userid");

        $res=db("car_dd")->where(["uid"=>$uid,"gid"=>0,"status"=>3])->select();

        foreach($res as $k => $v){
            $pay=explode(",",$v['pay']);

            $res[$k]['list']=db("car_dd")->where("code","in",$pay)->select();
        }

        $this->assign("res",$res); 
        
        return $this->fetch();
     }
     /**
     * 已完成订单
     *
     * @return void
     */
     public function order_6()
     {
        $uid=session("userid");

        $res=db("car_dd")->where(["uid"=>$uid,"gid"=>0,"status"=>4])->select();

        foreach($res as $k => $v){
            $pay=explode(",",$v['pay']);

            $res[$k]['list']=db("car_dd")->where("code","in",$pay)->select();
        }

        $this->assign("res",$res); 
        
        return $this->fetch();
     }
      /**
     * 退款售后
     *
     * @return void
     */
     public function order_5()
     {
        $uid=session("userid");

        $res=db("car_dd")->where(["uid"=>$uid,"gid"=>0,"status"=>5])->whereOr("state",2)->select();

        foreach($res as $k => $v){
            $pay=explode(",",$v['pay']);

            $res[$k]['list']=db("car_dd")->where("code","in",$pay)->select();
        }

        $this->assign("res",$res); 
        
        return $this->fetch();
     }
     /**
     * 取消退货退款
     *
     * @return void
     */
     public function cancel()
     {
         $id=input("id");

         $re=db("car_dd")->where("did",$id)->find();

         if($re['status'] == 5){
            
            $status=$re['old_status'];

            $res=db("car_dd")->where("did",$id)->setField("status",$status);

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
     * 确定收货
     *
     * @return void
     */
     public function change_dd()
     {
         $did=\input('id');
         $re=db("car_dd")->where("did",$did)->find();
         if($re){
            if($re['status'] == 2){
                $data['status']=3;
                $data['shou_time']=\time();
               
                $pay=$re['pay'];
                $arr=\explode(",", $pay);
               
                $integ=db("integ")->where("id",1)->find();
                
                 // 启动事务
                 Db::startTrans();
                 try{
                    db("car_dd")->where("did",$did)->update($data);
                    db("car_dd")->where("code","in",$arr)->update($data);

                    if($integ['open'] == 1){
                        $num=$integ['num'];

                        $integs=$re['zprice']*$num/100;

                        $log['uid']=$re['uid'];
                        $log['integ']=$integs;
                        $log['type']=1;
                        $log['content']="购物增加";
                        $log['time']=time();

                        db("integ_log")->insert($log);

                        db("user")->where("uid",$re['uid'])->setInc("integ",$integs);
                    }

                    echo '0';
                     // 提交事务
                     Db::commit();    
                 } catch (\Exception $e) {
                     // 回滚事务
                     Db::rollback();

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
     * 退货退款
     *
     * @return void
     */
     public function refund()
     {
        $did=\input("did");
        
        $re=db("car_dd")->where("did",$did)->find();

        $pay=explode(",",$re['pay']);

        $res=db("car_dd")->where("code","in",$pay)->select();

        $this->assign("re",$re);

        $this->assign("res",$res);
        
        return $this->fetch();
     }
     /**
     * 仅退款
     *
     * @return void
     */
     public function refund_money()
     {
        $did=input("did");
        
        $re=db("car_dd")->where("did",$did)->find();

        $this->assign("re",$re);
        
        return $this->fetch();
     }
     /**
     * 提交申请
     *
     * @return void
     */
     public function save_money()
     {
         $did=input("did");

         $re=db("car_dd")->where("did",$did)->find();

         if($re){
            if($re['status'] == 1){
                $data['remarks']="系统未发货,客户申请退款";
            }else{
               $data['remarks']="系统已发货,客户申请退款";
            }
            $data['status']=5;
            $data['cencal']=input("content");
            $data['t_time']=time();
            $data['old_status']=$re['status'];
            $data['state']=0;

            $res=db("car_dd")->where("did",$did)->update($data);

            $pay=explode(",",$re['pay']);

            db("car_dd")->where("code","in",$pay)->update($data);

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
     * 申请提交成功
     *
     * @return void
     */
     public function refund_success()
     {
        $did=input("did");
        
        $re=db("car_dd")->where("did",$did)->find();

        $this->assign("re",$re);
        
        return $this->fetch();
     }
     /**
     * 退货和退款
     *
     * @return void
     */
     public function refund_goods()
     {
        $did=input("did");
        
        $re=db("car_dd")->where("did",$did)->find();

        $this->assign("re",$re); 
        
        return $this->fetch();
     }
      /**
     * 提交申请
     *
     * @return void
     */
     public function save_goods()
     {
         $did=input("did");

         $re=db("car_dd")->where("did",$did)->find();

         if($re){
            if($re['status'] == 1){
                $data['remarks']="系统未发货,客户申请退货退款";
            }else{
               $data['remarks']="系统已发货,客户申请退货退款";
            }
            $data['status']=5;
            $data['cencal']=input("content");
            $data['t_time']=time();
            $data['old_status']=$re['status'];
            $data['state']=0;
            $data['express']=input("express");
            $data['number']=input("number");

            $res=db("car_dd")->where("did",$did)->update($data);

            $pay=explode(",",$re['pay']);

            db("car_dd")->where("code","in",$pay)->update($data);

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
     * 商品评价
     *
     * @return void
     */
     public function assess()
     {
         
        $did=input("did");

        $re=db("car_dd")->where("did",$did)->find();

        $this->assign("re",$re);

        $pay=explode(",",$re['pay']);

        $res=db("car_dd")->where("code","in",$pay)->select();

        $this->assign("res",$res);

        return $this->fetch();
     }
     /**
     * 保存商品评价
     *
     * @return void
     */
     public function save_assess()
     {
       

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
              
                $arr[]=$paths;
            }else{
                // 上传失败获取错误信息
                $this->error($file->getError());
            }    
        }
        
        $did=input("did");
        $re=db("car_dd")->where("did",$did)->find();
        $pay=explode(",",$re['pay']);
        db("car_dd")->where("did",$did)->setField("status",4);
        $res=db("car_dd")->where("code","in",$pay)->select();
        db("car_dd")->where("code","in",$pay)->setField("status",4);
        $data['image']=implode(",",$arr);
        $data['addtime']=time();
        $data['u_id']=session("userid");
        $data['number']=input("num");
        $data['content']=input("content");
       // var_dump($data['image']);exit;
        foreach($res as $v){
            $data['g_id']=$v['gid'];
            $rea=db("assess")->insert($data);
        }
        

        if($rea){
            $this->success("评价成功",url("order_4"));
        }else{
            $this->error("系统繁忙,请稍后再试");
        }
     }
}