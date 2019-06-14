<?php
namespace app\index\controller;

use think\Controller;

class BaseHome extends Controller
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

    //     vendor("Jssdk.Jssdk");
    //     $payment=db("payment")->where("id",1)->find();

    //     $appid=$payment['appid'];

    //     $appserect=$payment['appsecret'];
        
    //      $jssdk = new \JSSDK("$appid", "$appserect");

    //      $signPackage = $jssdk->GetSignPackage();
    //    //  var_dump($signPackage);
    //     $this->assign("signPackage",$signPackage);  
    
    }
}