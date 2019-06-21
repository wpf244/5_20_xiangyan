<?php
namespace app\index\controller;

use think\Request;
use think\Db;

class Index extends BaseHome
{
    public function index()
    {
        
      //  var_dump(111);exit;
        //轮播图
        $lb=db("lb")->where(["fid"=>2,"status"=>1])->order(["sort asc","id desc"])->select();

        $this->assign("lb",$lb);

        //机票车票链接
        $url=db("lb")->where(["fid"=>3])->find();

        $this->assign("url",$url);


        //乡村旅游
        $rural=db("rural")->where(["recom"=>1])->order(["id desc"])->select();

        $this->assign("rural",$rural);

        //热门景区
        $spot=db("spot")->where(["status"=>1,"recome"=>1])->order(["sort asc","id desc"])->select();

        $this->assign("spot",$spot);

        //区域文化
        $culture=db("culture")->where(["status"=>1,"recome"=>1])->order(["sort asc","id desc"])->select();

        $this->assign("culture",$culture);

        //热门抢购banner
        $assmeble=db("lb")->where("fid",37)->find();

        $this->assign("assemble",$assmeble);

        //热门砍价banner

        $bargain=db("lb")->where("fid",38)->find();

        $this->assign("bargain",$bargain);

        $index_city=db("culture_city")->where(["sid"=>0])->select();

        $this->assign("index_city",$index_city);

      
      
        return $this->fetch();
    }
    /**
    * 设置选择城市
    *
    * @return void
    */
    public function save_city()
    {
        $id=input("id");
        $re=db("culture_city")->where("cid",$id)->find();

        session("city_index",null);

        session("city_index",$re['c_name']);

        $this->redirect("Index/index");

    }
    public function save_citys()
    {
        $city=input("city");

        session("city_index",null);
 
        session("city_index",$city);


    }
    public function save_addr()
    {
        $longs=input("longs");

        $lats=input("lats");

        $test_key = 'BV04dK5lArS5E54VcYv6YH3GxmWzn2Yi'; //这里是申请的百度appkey
        $url = 'http://api.map.baidu.com/geocoder/v2/';
        //37.863036
        $longitude = number_format(doubleval($longs), 6); 
        //113.598909
        $latitude = number_format(doubleval($lats), 6); 
        $gps = $latitude . ',' . $longitude;
         $params = array(
             'location' => $gps,
              'pois' => 0,
          );
        $info = $this->get_contents($url, $params,$test_key);
        $city=$info['result']['addressComponent']['city'];

        session("city",$city);

        echo $city;

    }
    public function get_contents($url, $param,$baidu_key)
    {
     
        $param['ak'] = $baidu_key;
        $param['output'] = 'json';
        $url = $url . '?' . http_build_query($param, '', '&');
        $retry = 2;
        $result = array();
        
        while ($retry > 0) {
            $result = json_decode($this->curl_get($url), true);
         //   var_dump($result);exit;
            if (!empty($result) && isset($result['status']) && $result['status'] == 0) {
                return $result;
            }
            if (!empty($result) && isset($result['status'])) {
                $status = $result['status'];
            } else {
                $status = ' http_error:';
            }
        //    EdjLog::info("baidu_response retry status is" . $status.'params'.json_encode($param));
            $retry--;
        }
      //  EdjLog::warning('request to baidu lbs api failed after retries');
        
        return $result;
    }
    public function curl_get($url, $milliseconds = 300)
    {
        $start_time = microtime(true);

        $ch = curl_init();
        //这个参数很重要，设置这个才可以支持毫秒级的超时设置
        curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
       // curl_setopt($ch, CURLOPT_USERAGENT, self::$useragent);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_FAILONERROR, 0);
       // curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, self::$connecttimeout);
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, $milliseconds);


        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_URL, $url);
        $response = curl_exec($ch);
        $http_error_code = curl_errno($ch);
        curl_close($ch);

      //  EdjLog::info("request to baidu lbs api, url is $url, cost time:" . (microtime(true) - $start_time));
      //  EdjLog::info("baidu http_error:$http_error_code response is " . str_replace(PHP_EOL, '', $response));
        return $response;
    }

    /**
    * 微信分享砍价页面
    *
    * @return void
    */
    public function wx()
    {
        
        if(empty(session("userid"))){
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
            $token=$result['access_token'];
            $openid=$result['openid'];
            $url="https://api.weixin.qq.com/sns/userinfo?access_token=".$token."&openid=".$openid."&lang=zh_CN";
            $result=json_decode(file_get_contents($url),true);
            //   	    var_dump($result);exit;
            if(empty($result['openid'])){
                echo '失败:openid';
                exit;
            }
    
        
            $user=db("user")->where(["openid"=>$result['openid']])->find();
    
            if(empty($user)){
                $datas['openid']=$result['openid'];
                $datas['nickname']=$result['nickname'];
                $datas['image']=$result['headimgurl'];
                $datas['time']=time();
    
                db("user")->insert($datas);
    
                $uid=db("user")->getLastInsID();
    
                session("userid",$uid);
            }else{
                session("userid",$user['uid']);
            }
        }
        
        $uid=session("userid");

        $id=input("id");

        $re=db("bargain")->where(["id"=>$id])->find();

        $this->assign("re",$re);

        $gid=$re['gid'];

        $goods=db("bargain_goods")->where("id",$gid)->find();

        $this->assign("goods",$goods);

        $log=db("bargain_log")->where(["bid"=>$id,"uid"=>$uid])->find();

        $this->assign("log",$log);

        $times=$re['times']*60*60;

        $end_time=$re['time']+$times;

        if(\time() >= $end_time){

            $date=0;

        }else{

            $date=date("Y-m-d H:i:s",$end_time);
        }

        $this->assign("date",$date);

        $res=db("bargain_log")->alias("a")->where("bid",$id)->join("user b","a.uid=b.uid")->select();

        $this->assign("res",$res);


        $scale=floatval($re['already_price']/($re['price']-$re['floor_price'])*100);

        $this->assign("scale",$scale);

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
    /**
    * 砍价
    *
    * @return void
    */
    public function take()
    {
        $openid=input("openid");

        $id=input("id");

        $uid=session("userid");

        $log=db("bargain_log")->where(["bid"=>$id,"uid"=>$uid])->find();


        if(empty($log)){

            $bargain=db("bargain")->where(["id"=>$id,"status"=>0,"g_status"=>0])->find();

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
                    $data['number']=$bargain['number']+1;
                    if($goods['number'] != 0){
                        $goods_number=$goods['number']-$bargain['number'];
                        if($goods_number <= 1){
                            $data['status']=1;
                            $money=$can_price;
                        }

                    }
                    $data['already_price']=$bargain['already_price']+$money;
                    $data['surplus_price']=$bargain['surplus_price']-$money;
                    $data['can_price']=$bargain['can_price']-$money;
                    

                    //日志数据
                    
                    $log['bid']=$id;
                    $log['price']=$money;
                    $log['time']=time();
                    $log['uid']=$uid;
                  

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
    * 微信分享拼团页面
    *
    * @return void
    */
    public function assemble()
    {
        if(empty(session("userid"))){
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
            $token=$result['access_token'];
            $openid=$result['openid'];
            $url="https://api.weixin.qq.com/sns/userinfo?access_token=".$token."&openid=".$openid."&lang=zh_CN";
            $result=json_decode(file_get_contents($url),true);
            //   	    var_dump($result);exit;
            if(empty($result['openid'])){
                echo '失败:openid';
                exit;
            }
    
        
            $user=db("user")->where(["openid"=>$result['openid']])->find();
    
            if(empty($user)){
                $datas['openid']=$result['openid'];
                $datas['nickname']=$result['nickname'];
                $datas['image']=$result['headimgurl'];
                $datas['time']=time();
    
                db("user")->insert($datas);
    
                $uid=db("user")->getLastInsID();
    
                session("userid",$uid);
            }else{
                session("userid",$user['uid']);
            }
        }
        
        $uid=session("userid");

        $id=input("id");

        $re=db("assemble")->where("id",$id)->find();

        $this->assign("re",$re);

        $userid=$re['uid'];

        $users=db("user")->where("uid",$userid)->find();

        $this->assign("users",$users);

        $num=$re['number']-$re['num'];

        $this->assign("num",$num);

        $time=$re['time']+$re['date']*60*60;

        $date=date("Y-m-d H:i:s",$time);

        $this->assign("date",$date);

        $gid=$re['gid'];

        $goods=db("assemble_goods")->where("id",$gid)->find();

        $this->assign("goods",$goods);

        $res=db("assemble_log")->alias("a")->where(["aid"=>$id])->join("user b","a.uid=b.uid")->select();

        $this->assign("res",$res);

        vendor("Jssdk.Jssdk");
        $payment=db("payment")->where("id",1)->find();

        $appid=$payment['appid'];

        $appserect=$payment['appsecret'];
        
         $jssdk = new \JSSDK("$appid", "$appserect");

         $signPackage = $jssdk->GetSignPackage();
       //  var_dump($signPackage);
        $this->assign("signPackage",json_encode($signPackage));  

        $title=db("lb")->where("fid",35)->find();

        $title['desc']=strip_tags($title['desc']);

        $this->assign("title",$title);

        $desc=db("lb")->where("fid",36)->find();
        $desc['desc']=strip_tags($desc['desc']);

        $this->assign("desc",$desc);

        $urls=Request::instance()->domain();

        $this->assign("urls",$urls);

        return $this->fetch();

    }
    public function save()
    {
        $uid=session("userid");

        $id=input("id");

        $re=db("assemble")->where("id",$id)->find();

        if($re['status'] == 1){
            if($re['uid'] == $uid){

                echo '-1';exit;

            }else{
                
                $log=db("assemble_log")->where(["uid"=>$uid])->find();

                if($log){

                    if($log['status'] == 0){
                        echo $log['id'];
                    }else{
                        echo '-2';
                    }

                }else{

                    $data['uid']=$uid;
                    $data['gid']=$re['gid'];
                    $data['aid']=$id;
                    $data['time']=time();

                    db("assemble_log")->insert($data);

                    $id=db("assemble_log")->getLastInsID();

                    echo $id;



                }

            }
        }else{
            echo '0';
        }
    }



}
