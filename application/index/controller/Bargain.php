<?php
namespace app\index\controller;

use think\Db;
use think\Request;


class Bargain extends BaseUser
{
    public function index()
    {
        
        $res=db("bargain_goods")->where(["up"=>1])->order(["sort asc","id desc"])->select();

        $this->assign("res",$res);

        $uid=session("userid");

        $re=db("bargain")->where(["uid"=>$uid,"status"=>["in",[0,1]],"pay_status"=>0,"look"=>0,"g_status"=>0])->find();

        $this->assign("re",$re);

        $gid=$re['gid'];

        $goods=db("bargain_goods")->where("id",$gid)->find();

        $this->assign("goods",$goods);

        $rel=db("bargain")->where(["uid"=>$uid,"status"=>1,"look"=>0])->find();

        if($rel){
            db("bargain")->where(["id"=>$rel['id']])->setField("look",1);
        }

        $times=$re['times']*60*60;

        $end_time=$re['time']+$times;

        if(\time() >= $end_time){

            $date=0;

        }else{

            $date=date("Y-m-d H:i:s",$end_time);
        }

        $this->assign("date",$date);

        vendor("Jssdk.Jssdk");
        $payment=db("payment")->where("id",1)->find();

        $appid=$payment['appid'];

        $appserect=$payment['appsecret'];
        
         $jssdk = new \JSSDK("$appid", "$appserect");

         $signPackage = $jssdk->GetSignPackage();
       //  var_dump($signPackage);
        $this->assign("signPackage",json_encode($signPackage));  

        $title=db("lb")->where("fid",32)->find();

        $title['desc']=strip_tags($title['desc']);

        $this->assign("title",$title);

        $desc=db("lb")->where("fid",33)->find();
        $desc['desc']=strip_tags($desc['desc']);

        $this->assign("desc",$desc);

        $urls=Request::instance()->domain();

        $this->assign("urls",$urls);

        
        return $this->fetch();
    }
    public function detail()
    {
        $id=input("id");

        $re=db("bargain_goods")->where(["id"=>$id,"up"=>1])->find();

        $this->assign("re",$re);

        $lb=db("lb")->where(["fid"=>25,"status"=>1])->order(["sort asc","id asc"])->select();

        $this->assign("lb",$lb);
        
        return $this->fetch();
    }
    /**
    * 保存砍价
    *
    * @return void
    */
    public function save_bargain()
    {
        $id=input("id");

        $re=db("bargain_goods")->where("id",$id)->find();

        $uid=session("userid");

        if($re){

            $reb=db("bargain")->where(["uid"=>$uid,"gid"=>$id,"status"=>0])->find();

            if(empty($reb)){

                $data['uid']=$uid;
                $data['gid']=$id;
                $data['name']=$re['name'];
                $data['price']=$re['price'];
                $data['floor_price']=$re['floor_price'];
                $data['time']=time();
                $data['times']=$re['time'];
                $data['surplus_price']=$re['price'];
                $data['can_price']=$re['price']-$re['floor_price'];

                $rea=db("bargain")->insert($data);

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
    /**
    * 砍价
    *
    * @return void
    */
    public function take()
    {
        $uid=session("userid");

        $id=input("id");

        $log=db("bargain_log")->where(["bid"=>$id,"uid"=>$uid])->find();

        $user=db("user")->where("uid",$uid)->find();


        if(empty($log)){

            $bargain=db("bargain")->where(["id"=>$id,"status"=>0])->find();

            if($bargain){

                $gid=$bargain['gid'];

                $goods=db("bargain_goods")->where(["id"=>$gid,"up"=>1])->find();

                if($goods){

                    //查询已砍价人数 得出砍价区间

                    $number=$bargain['number'];

                    $goods_nums=$goods['nums'];

                    if($number >= $goods_nums){

                        $first_price=$goods['end_price'];

                        $end_price=$goods['end_prices'];

                    }else{

                        $first_price=$goods['first_price'];

                        $end_price=$goods['first_prices'];

                    }

                    //算出本次砍价金额

                    $money=mt_rand($first_price*100,$end_price*100)/100;

                    //查询还能砍的价格
                    $can_price=$bargain['can_price'];

                    if($money > $can_price){
                        $money=$money-$can_price;
                        $data['status']=1;
                    }
                    $data['already_price']=$bargain['already_price']+$money;
                    $data['surplus_price']=$bargain['surplus_price']-$money;
                    $data['can_price']=$bargain['can_price']-$money;
                    $data['number']=$bargain['number']+1;
                    if($goods['number'] != 0){
                        $goods_number=$goods['number']-$bargain['number'];
                        if($goods_number <= 1){
                            $data['status']=1;
                        }
                    }

                    //日志数据
                    $log['uid']=$uid;
                    $log['bid']=$id;
                    $log['price']=$money;
                    $log['time']=time();
                  

                    // 启动事务
                    Db::startTrans();
                    try{
                        db("bargain")->where("id",$id)->update($data);
                        db("bargain_log")->insert($log);

                        echo $money;
                        // 提交事务
                        Db::commit();    
                    } catch (\Exception $e) {

                        echo '-4';
                        // 回滚事务
                        Db::rollback();
                    }





                }else{
                    echo '-3';
                }

            }else{
                echo '-2';
            }

        }else{
            echo '-1';
        }
    }
    /**
    * 详情
    *
    * @return void
    */
    public function help()
    {
        $id=input("id");

        $re=db("bargain")->where(["id"=>$id])->find();

        $this->assign("re",$re);

        $times=$re['times']*60*60;

        $end_time=$re['time']+$times;

        if(\time() >= $end_time){

            $date=0;

        }else{

            $date=date("Y-m-d H:i:s",$end_time);
        }

        $this->assign("date",$date);

        $res=db("bargain_log")->where("bid",$id)->select();

        $this->assign("res",$res);


        $scale=\intval($re['already_price']/($re['price']-$re['floor_price'])*100);

        $this->assign("scale",$scale);

             vendor("Jssdk.Jssdk");
        $payment=db("payment")->where("id",1)->find();

        $appid=$payment['appid'];

        $appserect=$payment['appsecret'];
        
         $jssdk = new \JSSDK("$appid", "$appserect");

         $signPackage = $jssdk->GetSignPackage();
       //  var_dump($signPackage);
        $this->assign("signPackage",$signPackage);  

    $title=db("lb")->where("fid",32)->find();

    $this->assign("title",$title);

    $desc=db("lb")->where("fid",33)->find();

    $this->assign("desc",$desc);
        
        
        return $this->fetch();
    }
    /**
    * 历史记录
    *
    * @return void
    */
    public function history()
    {
        $uid=session("userid");
        //砍价成功
        $rec=db("bargain")->where(["status"=>1,"pay_status"=>0,"g_status"=>0,"uid"=>$uid])->order(["id desc"])->select();

        $this->assign("rec",$rec);

        //砍价失败

        $res=db("bargain")->where(["status"=>2,"g_status"=>0,"uid"=>$uid])->order(["id desc"])->select();

        $this->assign("res",$res);

        //已过期

        $reg=db("bargain")->where(["g_status"=>1,"uid"=>$uid])->order(["id desc"])->select();

        $this->assign("reg",$reg);
        

        
        return $this->fetch();
    }
    
    /**
    * 确认购买
    *
    * @return void
    */
    public function buy()
    {
        
        $uid=session("userid");

        $addr=db("addr")->where(["uid"=>$uid,"a_status"=>1])->find();

        $this->assign("addr",$addr);

        $id=input("id");

        $re=db("bargain")->where("id",$id)->find();

        $this->assign("re",$re);

        $gid=$re['gid'];
 
        $goods=db("bargain_goods")->where("id",$gid)->find();

        $this->assign("goods",$goods);

       
        return $this->fetch();
    }
     /**
    * 生产订单
    *
    * @return void
    */
    public function sdd()
    {
        $did=input('did');

        $aid=input("aid");

        $content=\input("content");

        $re=db("bargain")->where("id",$did)->find();

        $gid=$re['gid'];
     
        $uid=session("userid");
        $ob=db("bargain_dd");
        $old_dd=db("bargain_dd")->where(["gid"=>$gid,"uid"=>$uid,"status"=>0,"bid"=>$did])->find();
        if($old_dd){
            $ob->where("id",$old_dd['id'])->delete();
         
        }
        $good=db("bargain_goods")->where("id",$gid)->find();
     
        $arr=array();
        $arr['gid']=$gid;
        $arr['uid']=$uid;
        $arr['bid']=$did;
        $arr['goods_price']=$re['price'];
        $arr['price']=$re['surplus_price'];
        $arr['name']=$good['name'];
       
        $arr['image']=$good['image'];
        $arr['code']="CK-".uniqid();
        $arr['content']=$content;
        $arr['aid']=$aid;
        $arr['time']=time();
        
        $re=$ob->insert($arr);
        
        $dids = db('bargain_dd')->getLastInsID();
        if($dids){
            echo $dids;
        }else{
            echo '0';
        }
    }

    
   
}