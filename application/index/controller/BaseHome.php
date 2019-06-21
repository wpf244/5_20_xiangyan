<?php
namespace app\index\controller;

use think\Controller;

use think\Loader;
use think\Request;


Loader::import('WxPay.WxPay', EXTEND_PATH, '.Api.php');
Loader::import('WxPay.WxPay', EXTEND_PATH, '.JsApiPay.php');

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
         
        $city_index=session("city_index");


        $this->assign("city_index",$city_index);

        //更新拼团过期
        $time=time();

        $res=db("assemble")->where("status",1)->select();


        foreach($res as $k => $v){
            $bar_time=$v['time']+$v['date']*60*60;

            if($time >= $bar_time){
                //修改拼团为过期

                db("assemble")->where("id",$v['id'])->setField("status",3);

                //退款
                $res=db("assemble_dd")->where(["a_id"=>$v['id'],"status"=>1])->select();

                if($res){

                    foreach($res as $v){
  
                        $out_trade_no=$v['code'];
                        $total_fee=$v['price']*100;
                        $refund_fee=$v['price']*100;

                        $data=db("payment")->where("id",1)->find();

                        $input = new \WxPayRefund();
                        $input->SetOut_trade_no($out_trade_no);
                        $input->SetTotal_fee($total_fee);
                        $input->SetRefund_fee($refund_fee);
                        $input->SetOut_refund_no("sdkphp".date("YmdHis"));
                        $input->SetOp_user_id($data['mchid']);

                        $order = \WxPayApi::refund($input,$data);

                        db("assemble_dd")->where("id",$v['id'])->update(["status"=>5]);

                    }


                }


            }
        }
    
    }
}