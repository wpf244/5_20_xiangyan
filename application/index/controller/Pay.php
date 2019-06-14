<?php
namespace app\index\controller;

use think\Loader;
use think\Request;


Loader::import('WxPay.WxPay', EXTEND_PATH, '.Api.php');
Loader::import('WxPay.WxPay', EXTEND_PATH, '.JsApiPay.php');

class Pay extends BaseHome
{
    public function pay()
    {
        $id=input("did");

        $re=db("order")->where("id",$id)->find();

        $order=$re['code'];
        $money=($re['price']*100);

        $this->assign("re",$re);

        $data=db("payment")->where("id",1)->find();

        $uid=$re['uid'];

        $user=db("user")->where("uid",$uid)->find();

        $openid=$user['openid'];

        if(empty($openid)){
            $openid=$this->getopenid();
            db("user")->where("uid",$uid)->setField("openid",$openid);
        }

        $input = new \WxPayUnifiedOrder();
        $input->SetBody("商品");
        $input->SetOut_trade_no("$order");
        $input->SetTotal_fee("$money");
        $input->SetNotify_url("http://xiangyan.dd371.com/Index/Pay/notify/");
        $input->SetTrade_type("JSAPI");
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        //     由小程序端传给服务端
        $input->SetOpenid($openid);
        //     向微信统一下单，并返回order，它是一个array数组
        $order = \WxPayApi::unifiedOrder($input,$data);
        //     json化返回给小程序端
        $tools=new \JsApiPay();
        $jsApiParameters = $tools->GetJsApiParameters($order,$data);

        $this->assign("data",$jsApiParameters);


        
        return $this->fetch();
    }
    public function notify()
    {
        
        //获取返回的xml
        $testxml  = file_get_contents("php://input");
        //将xml转化为json格式
        $jsonxml = json_encode(simplexml_load_string($testxml, 'SimpleXMLElement', LIBXML_NOCDATA));
        
        //转成数组
        $result = json_decode($jsonxml, true);
        
        if($result){
            //如果成功返回了
            if($result['return_code'] == 'SUCCESS'){
                //进行改变订单状态等操作。。。。
                $order_code= $result['out_trade_no'];
                $re=db("order")->where("code",$order_code)->find();
                $id=$re['id'];
                if($re['status'] == 0){
                    
                    $data['status']=1;
                    $changestatus=db("order")->where("id",$id)->update($data);
                   
                }
                
            }
        }
        
    }
    public function pay_success()
    {
        return $this->fetch();
    }
    public function pay_success_dd()
    {
        return $this->fetch();
    }
    public function pay_dd()
    {
        $id=input("did");

        $re=db("car_dd")->where("did",$id)->find();

        $this->assign("re",$re);

        $order=$re['code'];
        $money=($re['zprice']*100);

        $data=db("payment")->where("id",1)->find();

        $uid=$re['uid'];

        $user=db("user")->where("uid",$uid)->find();

        $openid=$user['openid'];

        if(empty($openid)){
            $openid=$this->getopenid();
            db("user")->where("uid",$uid)->setField("openid",$openid);
        }

        $input = new \WxPayUnifiedOrder();
        $input->SetBody("商品");
        $input->SetOut_trade_no("$order");
        $input->SetTotal_fee("$money");
        $input->SetNotify_url("http://xiangyan.dd371.com/Index/Pay/notify_dd/");
        $input->SetTrade_type("JSAPI");
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        //     由小程序端传给服务端
        $input->SetOpenid($openid);
        //     向微信统一下单，并返回order，它是一个array数组
        $order = \WxPayApi::unifiedOrder($input,$data);
        //     json化返回给小程序端
        $tools=new \JsApiPay();
        $jsApiParameters = $tools->GetJsApiParameters($order,$data);

        $this->assign("data",$jsApiParameters);

       
        
        return $this->fetch();
    }
    public function notify_dd()
    {
        
        //获取返回的xml
        $testxml  = file_get_contents("php://input");
        //将xml转化为json格式
        $jsonxml = json_encode(simplexml_load_string($testxml, 'SimpleXMLElement', LIBXML_NOCDATA));
        
        //转成数组
        $result = json_decode($jsonxml, true);
        
        if($result){
            //如果成功返回了
            if($result['return_code'] == 'SUCCESS'){
                //进行改变订单状态等操作。。。。
                $order_code= $result['out_trade_no'];
                $re=db("car_dd")->where("code",$order_code)->find();
                $id=$re['did'];
                if($re['status'] == 0){
                    $data['fu_time']=time();
                    $data['status']=1;
                    $changestatus=db("car_dd")->where("did=$id")->update($data);
                    if($changestatus){
                        $pay = $re['pay'];
                        $res = explode(",",$pay);
                        foreach($res as $v){
                            $dd = db("car_dd")->where("code",$v)->find();
                            $uid = $dd['uid'];
                            $gid = $dd['gid'];
                            $did = $dd['did'];
                            $num = $dd['num'];
                            $re_d = db("car_dd")->where("did=$did")->update($data);
                            
                            //增加销量
                            db("goods")->where("id=$gid")->setInc("sales",$num);

                            //减少库存

                            db("goods")->where("id=$gid")->setDec("kc",$num);
    
                        }
                        
                    }
                }
                
            }
        }
        
    }
    public function getopenid()
    {
        $pay=db("payment")->where("id",1)->find();

        $appid=$pay['appid'];

        $appsecret=$pay['appsecret'];

        $url=Request::instance()->url(true);

        $urlcode="https://open.weixin.qq.com/connect/oauth2/authorize?appid=$appid&redirect_uri=$url&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect";

		if(empty(input("code"))){
			header("location:".$urlcode);
		}

		$code=input("code");
	
		$url="https://api.weixin.qq.com/sns/oauth2/access_token?appid=$appid&secret=$appsecret&code=".$code."&grant_type=authorization_code";

		$result=json_decode(file_get_contents($url),true);
		if(empty($result['access_token'])){
			echo '失败:access_token';exit;
		}
		//$token=$result['access_token'];
        $openid=$result['openid'];
        
        return $openid;

        
    }
}