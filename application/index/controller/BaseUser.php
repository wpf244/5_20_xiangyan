<?php
namespace app\index\controller;

use think\Controller;

class BaseUser extends Controller
{
    function _initialize(){
        if (!defined('CONTROLLER_NAME')) {
            define('CONTROLLER_NAME', $this->request->controller());
        }
        if (!defined('ACTION_NAME')) {
            define('ACTION_NAME', $this->request->action());
        }
      
        $sys=db('sys')->where("id=1")->find();
        $this->assign("sys",$sys);

        $seo=db('seo')->where("id=1")->find();
        $this->assign("seo",$seo);

        if(empty(session("userid"))){
            $this->redirect("Login/login");
        }else{
            $userid=session("userid");
            $user=db("user")->where("uid",$userid)->find();
            if(empty($user)){
                session("userid",null);
                $this->redirect("Login/login");
            }
        }

        $city_index=session("city_index");

        $this->assign("city_index",$city_index);

        //更新砍价到期的

        $time=time();

        $res=db("bargain")->where("status",0)->select();

        foreach($res as $k => $v){
            $bar_time=$v['time']+$v['times']*60*60;

            if($time >= $bar_time){
                db("bargain")->where("id",$v['id'])->setField("status",2);
            }
        }

        //更新商品活动到期的
        $goods=db("bargain_goods")->where("up",1)->select();

        foreach($goods as $vg){

           

            $goods_time=strtotime($vg['end_time']);

            if($time > $goods_time){

                db("bargain_goods")->where("id",$vg['id'])->setField("up",0);

                db("bargain")->where(["gid"=>$vg['id'],"status"=>0])->setField("g_status",1);

            }
        }
    
    }
}