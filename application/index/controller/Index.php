<?php
namespace app\index\controller;


class Index extends BaseHome
{
    public function index()
    {
        //轮播图
        $lb=db("lb")->where(["fid"=>2,"status"=>1])->order(["sort asc","id desc"])->select();

        $this->assign("lb",$lb);

        //机票车票链接
        $url=db("lb")->where(["fid"=>3])->find();

        $this->assign("url",$url);


        //乡村旅游
        $rural=db("rural")->alias("a")->field("a.*,b.nickname,b.image as photo")->where(["recom"=>1])->join("user b","a.uid=b.uid")->order(["id desc"])->select();

        $this->assign("rural",$rural);

        //热门景区
        $spot=db("spot")->where(["status"=>1,"recome"=>1])->order(["sort asc","id desc"])->select();

        $this->assign("spot",$spot);

        //区域文化
        $culture=db("culture")->where(["status"=>1,"recome"=>1])->order(["sort asc","id desc"])->select();

        $this->assign("culture",$culture);

      
        
        return $this->fetch();
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


}
