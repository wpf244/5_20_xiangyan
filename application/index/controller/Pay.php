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
    public function pay_dd()
    {
        $id=input("did");

        $re=db("car_dd")->where("did",$id)->find();

        $this->assign("re",$re);

       
        
        return $this->fetch();
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