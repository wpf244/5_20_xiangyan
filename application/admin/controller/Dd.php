<?php
namespace app\admin\controller;
\header("content-type:text/html;charset=utf-8;");
class Dd extends BaseAdmin
{
    public function dai_dd()
    {
        $start=input('start');
        $end=input('end');
        $code=\input('code');
      
        $addr=\input('addr');
       
        if($start || $code ||  $addr){
            if($start){
                
                $map['time']=['between time',[$start.'00:00:01',$end.'23:59:59']];
            }
            if($code){
                $map['code']=array('like','%'.$code.'%');
            }
         
            if($addr){
                $maps['addr|addrs|username|phone']=array('like','%'.$addr.'%');
                $re=db("addr")->where($maps)->select();
           
                    $id=array();
                    foreach ($re as $v){
                        $id[]=$v['aid'];
                    }
                    $map['a.aid']=array("in",$id);
          
            }
        }else{
            
            $start="";
            $end="";
        
            $addr="";
            $code="";
            $map=[];
        }
        $this->assign("start",$start);
        $this->assign("end",$end);
      
        $this->assign("addr",$addr);
        $this->assign("code",$code);
        
        $list=db("car_dd")->alias('a')->where("status=0 and gid=0")->where($map)->join("addr b","a.aid = b.aid","LEFT")->order("did desc")->paginate(20,false,['query'=>request()->param()]);
        $this->assign("list",$list);
        $page=$list->render();
        $this->assign("page",$page);
        
        return $this->fetch();
    }
    public function out(){
        $start=input('start');
        $end=input('end');
        $code=\input('code');
      
        $addr=\input('addr');
       
        if($start || $code ||  $addr){
            if($start){
                
                $map['time']=['between time',[$start.'00:00:01',$end.'23:59:59']];
            }
            if($code){
                $map['code']=array('like','%'.$code.'%');
            }
         
            if($addr){
                $maps['addr|addrs|username|phone']=array('like','%'.$addr.'%');
                $re=db("addr")->where($maps)->select();
           
                    $id=array();
                    foreach ($re as $v){
                        $id[]=$v['aid'];
                    }
                    $map['a.aid']=array("in",$id);
          
            }
        }else{
          
            $map=[];
        }
        
        $list=db("car_dd")->alias('a')->where("status=0 and gid=0")->where($map)->join("addr b","a.aid = b.aid","LEFT")->order("did desc")->select();
        // var_dump($data);exit;
        vendor('PHPExcel.PHPExcel');//调用类库,路径是基于vendor文件夹的
        vendor('PHPExcel.PHPExcel.Worksheet.Drawing');
        vendor('PHPExcel.PHPExcel.Writer.Excel2007');
        $objExcel = new \PHPExcel();
        //set document Property
        $objWriter = \PHPExcel_IOFactory::createWriter($objExcel, 'Excel2007');
    
        $objActSheet = $objExcel->getActiveSheet();
        $key = ord("A");
        $letter =explode(',',"A,B,C,D,E,F,G");
        $arrHeader =  array("订单号","订单总金额","下单时间","收货人姓名","收货人电话","收货人地址","订单备注");
        //填充表头信息
        $lenth =  count($arrHeader);
        for($i = 0;$i < $lenth;$i++) {
            $objActSheet->setCellValue("$letter[$i]1","$arrHeader[$i]");
        }
        //填充表格信息
        foreach($list as $k=>$v){
            $k +=2;
            $objActSheet->setCellValue('A'.$k,$v['code']);
            $objActSheet->setCellValue('B'.$k, $v['zprice']);    
            // 表格内容
            $objActSheet->setCellValue('C'.$k, \date("Y-m-d H:i:s",$v['time']));
            $objActSheet->setCellValue('D'.$k, $v['username']);
            $objActSheet->setCellValue('E'.$k, $v['phone']);
            $objActSheet->setCellValue('F'.$k, $v['addr'].$v['addrs']);
            $objActSheet->setCellValue('G'.$k, $v['content']);
    
            // 表格高度
            $objActSheet->getRowDimension($k)->setRowHeight(20);
        }
    
        $width = array(20,20,15,10,10,30,10,15,15,15);
        //设置表格的宽度
        $objActSheet->getColumnDimension('A')->setWidth(20);
        $objActSheet->getColumnDimension('B')->setWidth(20);
        $objActSheet->getColumnDimension('C')->setWidth(25);
        $objActSheet->getColumnDimension('D')->setWidth(25);
        $objActSheet->getColumnDimension('E')->setWidth(25);
        $objActSheet->getColumnDimension('F')->setWidth(30);
        $objActSheet->getColumnDimension('G')->setWidth(30);

        if($start !=0 ){
             
            $times=($start."-".$end);
        }else{
            $times="";
        }
        $outfile = "$times"."待付款订单".".xls";
    
        $userBrowser=$_SERVER['HTTP_USER_AGENT'];
        
        if(preg_match('/MSIE/i', $userBrowser)){
            $outfile=urlencode($outfile);
           
        }else{
            $outfile= iconv("utf-8","gb2312",$outfile);;
            
        }
        ob_end_clean();
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header('Content-Disposition:inline;filename="'.$outfile.'"');
        header("Content-Transfer-Encoding: binary");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Pragma: no-cache");
        $objWriter->save('php://output');
    }
    public function detail()
    {
        $id=\input('id');
        $re=db("car_dd")->where("did=$id")->find();
        $pay=$re['pay'];
        $res=\explode(",", $pay);
        $arr=array();
        foreach ($res as $v){
            $arr[]=db("car_dd")->alias("a")->where("code='$v'")->join("addr b","a.aid=b.aid","left")->find();
        }
        $this->assign("list",$arr);
        return $this->fetch();
    }
    public function delete()
    {
        $id=\input('id');
        $re=db("car_dd")->where("did=$id")->find();
        if($re){
            $del=db("car_dd")->where("did=$id")->delete();
            $pay=explode(",",$re['pay']);
          
            db("car_dd")->where("code","in",$pay)->delete();
            $this->redirect("dai_dd");
        }else{
            $this->redirect("dai_dd");
        }
    }
    public function change()
    {
        $id=\input('id');
        $re=db("car_dd")->where("did=$id")->find();
        if($re){
            db("car_dd")->where("did=$id")->setField("status",1);
            $pay=explode(",",$re['pay']);
          
            db("car_dd")->where("code","in",$pay)->setField("status",1);
             
            $this->redirect("dai_dd");
        }else{
            $this->redirect("dai_dd");
        }
    }
    public function fa_dd()
    {
        $start=input('start');
        $end=input('end');
        $code=\input('code');
      
        $addr=\input('addr');
       
        if($start || $code ||  $addr){
            if($start){
                
                $map['time']=['between time',[$start.'00:00:01',$end.'23:59:59']];
            }
            if($code){
                $map['code']=array('like','%'.$code.'%');
            }
         
            if($addr){
                $maps['addr|addrs|username|phone']=array('like','%'.$addr.'%');
                $re=db("addr")->where($maps)->select();
           
                    $id=array();
                    foreach ($re as $v){
                        $id[]=$v['aid'];
                    }
                    $map['a.aid']=array("in",$id);
          
            }
        }else{
            
            $start="";
            $end="";
        
            $addr="";
            $code="";
            $map=[];
        }
        $this->assign("start",$start);
        $this->assign("end",$end);
      
        $this->assign("addr",$addr);
        $this->assign("code",$code);
        
        $list=db("car_dd")->alias('a')->where("status=1 and gid=0")->where($map)->join("addr b","a.aid = b.aid","LEFT")->order("did desc")->paginate(20,false,['query'=>request()->param()]);
        $this->assign("list",$list);
        $page=$list->render();
        $this->assign("page",$page);
        
        return $this->fetch();
    }
    public function outf(){
        $start=input('start');
        $end=input('end');
        $code=\input('code');
      
        $addr=\input('addr');
       
        if($start || $code ||  $addr){
            if($start){
                
                $map['time']=['between time',[$start.'00:00:01',$end.'23:59:59']];
            }
            if($code){
                $map['code']=array('like','%'.$code.'%');
            }
         
            if($addr){
                $maps['addr|addrs|username|phone']=array('like','%'.$addr.'%');
                $re=db("addr")->where($maps)->select();
           
                    $id=array();
                    foreach ($re as $v){
                        $id[]=$v['aid'];
                    }
                    $map['a.aid']=array("in",$id);
          
            }
        }else{
            
           
            $map=[];
        }
    
        $list=db("car_dd")->alias('a')->where("status=1 and gid=0")->where($map)->join("addr b","a.aid = b.aid","LEFT")->order("did desc")->select();
        // var_dump($data);exit;
        vendor('PHPExcel.PHPExcel');//调用类库,路径是基于vendor文件夹的
        vendor('PHPExcel.PHPExcel.Worksheet.Drawing');
        vendor('PHPExcel.PHPExcel.Writer.Excel2007');
        $objExcel = new \PHPExcel();
        //set document Property
        $objWriter = \PHPExcel_IOFactory::createWriter($objExcel, 'Excel2007');
    
        $objActSheet = $objExcel->getActiveSheet();
        $key = ord("A");
        $letter =explode(',',"A,B,C,D,E,F,G");
        $arrHeader =  array("订单号","订单总金额","下单时间","收货人姓名","收货人电话","收货人地址","订单备注");
        //填充表头信息
        $lenth =  count($arrHeader);
        for($i = 0;$i < $lenth;$i++) {
            $objActSheet->setCellValue("$letter[$i]1","$arrHeader[$i]");
        }
        //填充表格信息
        foreach($list as $k=>$v){
            $k +=2;
            $objActSheet->setCellValue('A'.$k,$v['code']);
            $objActSheet->setCellValue('B'.$k, $v['zprice']);
            // 表格内容
            $objActSheet->setCellValue('C'.$k, \date("Y-m-d H:i:s",$v['time']));
            $objActSheet->setCellValue('D'.$k, $v['username']);
            $objActSheet->setCellValue('E'.$k, $v['phone']);
            $objActSheet->setCellValue('F'.$k, $v['addr'].$v['addrs']);
            $objActSheet->setCellValue('G'.$k, $v['content']);
    
    
            // 表格高度
            $objActSheet->getRowDimension($k)->setRowHeight(20);
        }
    
        $width = array(20,20,15,10,10,30,10,15,15,15);
        //设置表格的宽度
        $objActSheet->getColumnDimension('A')->setWidth(20);
        $objActSheet->getColumnDimension('B')->setWidth(20);
        $objActSheet->getColumnDimension('C')->setWidth(25);
        $objActSheet->getColumnDimension('D')->setWidth(25);
        $objActSheet->getColumnDimension('E')->setWidth(25);
        $objActSheet->getColumnDimension('F')->setWidth(30);
        $objActSheet->getColumnDimension('G')->setWidth(30);
    
        if($start !=0 ){
             
            $times=($start."-".$end);
        }else{
            $times="";
        }
        $outfile = "$times"."待发货订单".".xls";
    
        $userBrowser=$_SERVER['HTTP_USER_AGENT'];
    
        if(preg_match('/MSIE/i', $userBrowser)){
            $outfile=urlencode($outfile);
             
        }else{
            $outfile= iconv("utf-8","gb2312",$outfile);;
    
        }
        ob_end_clean();
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header('Content-Disposition:inline;filename="'.$outfile.'"');
        header("Content-Transfer-Encoding: binary");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Pragma: no-cache");
        $objWriter->save('php://output');
    }
    public function save()
    {
        $did=\input('did');
        $re=db("car_dd")->where("did=$did")->find();
        if($re){
           if($re['status'] == 1){
               $data['status']=2;
               $data['fa_time']=\time();
               $res=db("car_dd")->where("did=$did")->update($data);
               
               $pay=$re['pay'];
               $arr=\explode(",", $pay);
               foreach ($arr as $v){
                   $ress=db("car_dd")->where("code='$v'")->update($data);
               }
               
               $this->redirect('fa_dd');
           }else{
               $this->redirect('fa_dd');
           }
        }else{
            $this->redirect('fa_dd');
        }
    }
    public function shou_dd()
    {
        $start=input('start');
        $end=input('end');
        $code=\input('code');
      
        $addr=\input('addr');
       
        if($start || $code ||  $addr){
            if($start){
                
                $map['time']=['between time',[$start.'00:00:01',$end.'23:59:59']];
            }
            if($code){
                $map['code']=array('like','%'.$code.'%');
            }
         
            if($addr){
                $maps['addr|addrs|username|phone']=array('like','%'.$addr.'%');
                $re=db("addr")->where($maps)->select();
           
                    $id=array();
                    foreach ($re as $v){
                        $id[]=$v['aid'];
                    }
                    $map['a.aid']=array("in",$id);
          
            }
        }else{
            
            $start="";
            $end="";
        
            $addr="";
            $code="";
            $map=[];
        }
        $this->assign("start",$start);
        $this->assign("end",$end);
      
        $this->assign("addr",$addr);
        $this->assign("code",$code);
        
        $list=db("car_dd")->alias('a')->where("status=2 and gid=0")->where($map)->join("addr b","a.aid = b.aid","LEFT")->order("did desc")->paginate(20,false,['query'=>request()->param()]);
        $this->assign("list",$list);
        $page=$list->render();
        $this->assign("page",$page);
        
        return \view("shou_dd");
    }
    public function outh(){
        $start=input('start');
        $end=input('end');
        $code=\input('code');
      
        $addr=\input('addr');
       
        if($start || $code ||  $addr){
            if($start){
                
                $map['time']=['between time',[$start.'00:00:01',$end.'23:59:59']];
            }
            if($code){
                $map['code']=array('like','%'.$code.'%');
            }
         
            if($addr){
                $maps['addr|addrs|username|phone']=array('like','%'.$addr.'%');
                $re=db("addr")->where($maps)->select();
           
                    $id=array();
                    foreach ($re as $v){
                        $id[]=$v['aid'];
                    }
                    $map['a.aid']=array("in",$id);
          
            }
        }else{
          
            $map=[];
        }
    
        $list=db("car_dd")->alias('a')->where("status=2 and gid=0")->where($map)->join("addr b","a.aid = b.aid","LEFT")->order("did desc")->select();
        // var_dump($data);exit;
        vendor('PHPExcel.PHPExcel');//调用类库,路径是基于vendor文件夹的
        vendor('PHPExcel.PHPExcel.Worksheet.Drawing');
        vendor('PHPExcel.PHPExcel.Writer.Excel2007');
        $objExcel = new \PHPExcel();
        //set document Property
        $objWriter = \PHPExcel_IOFactory::createWriter($objExcel, 'Excel2007');
    
        $objActSheet = $objExcel->getActiveSheet();
        $key = ord("A");
        $letter =explode(',',"A,B,C,D,E,F,G");
        $arrHeader =  array("订单号","订单总金额","下单时间","收货人姓名","收货人电话","收货人地址","订单备注");
        //填充表头信息
        $lenth =  count($arrHeader);
        for($i = 0;$i < $lenth;$i++) {
            $objActSheet->setCellValue("$letter[$i]1","$arrHeader[$i]");
        }
        //填充表格信息
        foreach($list as $k=>$v){
            $k +=2;
            $objActSheet->setCellValue('A'.$k,$v['code']);
            $objActSheet->setCellValue('B'.$k, $v['zprice']);
            // 表格内容
            $objActSheet->setCellValue('C'.$k, \date("Y-m-d H:i:s",$v['time']));
            $objActSheet->setCellValue('D'.$k, $v['username']);
            $objActSheet->setCellValue('E'.$k, $v['phone']);
            $objActSheet->setCellValue('F'.$k, $v['addr'].$v['addrs']);
            $objActSheet->setCellValue('G'.$k, $v['content']);
    
            // 表格高度
            $objActSheet->getRowDimension($k)->setRowHeight(20);
        }
    
        $width = array(20,20,15,10,10,30,10,15,15,15);
        //设置表格的宽度
        $objActSheet->getColumnDimension('A')->setWidth(20);
        $objActSheet->getColumnDimension('B')->setWidth(20);
        $objActSheet->getColumnDimension('C')->setWidth(25);
        $objActSheet->getColumnDimension('D')->setWidth(25);
        $objActSheet->getColumnDimension('E')->setWidth(25);
        $objActSheet->getColumnDimension('F')->setWidth(30);
        $objActSheet->getColumnDimension('G')->setWidth(30);
    
        if($start !=0 ){
             
            $times=($start."-".$end);
        }else{
            $times="";
        }
        $outfile = "$times"."待收货订单".".xls";
    
        $userBrowser=$_SERVER['HTTP_USER_AGENT'];
    
        if(preg_match('/MSIE/i', $userBrowser)){
            $outfile=urlencode($outfile);
             
        }else{
            $outfile= iconv("utf-8","gb2312",$outfile);;
    
        }
        ob_end_clean();
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header('Content-Disposition:inline;filename="'.$outfile.'"');
        header("Content-Transfer-Encoding: binary");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Pragma: no-cache");
        $objWriter->save('php://output');
    }
    public function ping_dd()
    {
        $start=input('start');
        $end=input('end');
        $code=\input('code');
      
        $addr=\input('addr');
       
        if($start || $code ||  $addr){
            if($start){
                
                $map['time']=['between time',[$start.'00:00:01',$end.'23:59:59']];
            }
            if($code){
                $map['code']=array('like','%'.$code.'%');
            }
         
            if($addr){
                $maps['addr|addrs|username|phone']=array('like','%'.$addr.'%');
                $re=db("addr")->where($maps)->select();
           
                    $id=array();
                    foreach ($re as $v){
                        $id[]=$v['aid'];
                    }
                    $map['a.aid']=array("in",$id);
          
            }
        }else{
            
            $start="";
            $end="";
        
            $addr="";
            $code="";
            $map=[];
        }
        $this->assign("start",$start);
        $this->assign("end",$end);
      
        $this->assign("addr",$addr);
        $this->assign("code",$code);
    
        $list=db("car_dd")->alias('a')->where("status=3 and gid=0")->where($map)->join("addr b","a.aid = b.aid","LEFT")->order("did desc")->paginate(20,false,['query'=>request()->param()]);
        $this->assign("list",$list);
        $page=$list->render();
        $this->assign("page",$page);
    
        return $this->fetch();
    }
    public function outp(){
        $start=input('start');
        $end=input('end');
        $code=\input('code');
      
        $addr=\input('addr');
       
        if($start || $code ||  $addr){
            if($start){
                
                $map['time']=['between time',[$start.'00:00:01',$end.'23:59:59']];
            }
            if($code){
                $map['code']=array('like','%'.$code.'%');
            }
         
            if($addr){
                $maps['addr|addrs|username|phone']=array('like','%'.$addr.'%');
                $re=db("addr")->where($maps)->select();
           
                    $id=array();
                    foreach ($re as $v){
                        $id[]=$v['aid'];
                    }
                    $map['a.aid']=array("in",$id);
          
            }
        }else{
           
            $map=[];
        }
    
        $list=db("car_dd")->alias('a')->where("status=3 and gid=0")->where($map)->join("addr b","a.aid = b.aid","LEFT")->order("did desc")->select();
        // var_dump($data);exit;
        vendor('PHPExcel.PHPExcel');//调用类库,路径是基于vendor文件夹的
        vendor('PHPExcel.PHPExcel.Worksheet.Drawing');
        vendor('PHPExcel.PHPExcel.Writer.Excel2007');
        $objExcel = new \PHPExcel();
        //set document Property
        $objWriter = \PHPExcel_IOFactory::createWriter($objExcel, 'Excel2007');
    
        $objActSheet = $objExcel->getActiveSheet();
        $key = ord("A");
        $letter =explode(',',"A,B,C,D,E,F,G");
        $arrHeader =  array("订单号","订单总金额","下单时间","收货人姓名","收货人电话","收货人地址","订单备注");
        //填充表头信息
        $lenth =  count($arrHeader);
        for($i = 0;$i < $lenth;$i++) {
            $objActSheet->setCellValue("$letter[$i]1","$arrHeader[$i]");
        }
        //填充表格信息
        foreach($list as $k=>$v){
            $k +=2;
            $objActSheet->setCellValue('A'.$k,$v['code']);
            $objActSheet->setCellValue('B'.$k, $v['zprice']);
            // 表格内容
            $objActSheet->setCellValue('C'.$k, \date("Y-m-d H:i:s",$v['time']));
            $objActSheet->setCellValue('D'.$k, $v['username']);
            $objActSheet->setCellValue('E'.$k, $v['phone']);
            $objActSheet->setCellValue('F'.$k, $v['addr'].$v['addrs']);
            $objActSheet->setCellValue('G'.$k, $v['content']);
    
            // 表格高度
            $objActSheet->getRowDimension($k)->setRowHeight(20);
        }
    
        $width = array(20,20,15,10,10,30,10,15,15,15);
        //设置表格的宽度
        $objActSheet->getColumnDimension('A')->setWidth(20);
        $objActSheet->getColumnDimension('B')->setWidth(20);
        $objActSheet->getColumnDimension('C')->setWidth(25);
        $objActSheet->getColumnDimension('D')->setWidth(25);
        $objActSheet->getColumnDimension('E')->setWidth(25);
        $objActSheet->getColumnDimension('F')->setWidth(30);
        $objActSheet->getColumnDimension('G')->setWidth(30);
    
        if($start !=0 ){
             
            $times=($start."-".$end);
        }else{
            $times="";
        }
        $outfile = "$times"."待评价订单".".xls";
    
        $userBrowser=$_SERVER['HTTP_USER_AGENT'];
    
        if(preg_match('/MSIE/i', $userBrowser)){
            $outfile=urlencode($outfile);
             
        }else{
            $outfile= iconv("utf-8","gb2312",$outfile);;
    
        }
        ob_end_clean();
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header('Content-Disposition:inline;filename="'.$outfile.'"');
        header("Content-Transfer-Encoding: binary");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Pragma: no-cache");
        $objWriter->save('php://output');
    }
    public function wan_dd()
    {
        $start=input('start');
        $end=input('end');
        $code=\input('code');
      
        $addr=\input('addr');
       
        if($start || $code ||  $addr){
            if($start){
                
                $map['time']=['between time',[$start.'00:00:01',$end.'23:59:59']];
            }
            if($code){
                $map['code']=array('like','%'.$code.'%');
            }
         
            if($addr){
                $maps['addr|addrs|username|phone']=array('like','%'.$addr.'%');
                $re=db("addr")->where($maps)->select();
           
                    $id=array();
                    foreach ($re as $v){
                        $id[]=$v['aid'];
                    }
                    $map['a.aid']=array("in",$id);
          
            }
        }else{
            
            $start="";
            $end="";
        
            $addr="";
            $code="";
            $map=[];
        }
        $this->assign("start",$start);
        $this->assign("end",$end);
      
        $this->assign("addr",$addr);
        $this->assign("code",$code);
    
        $list=db("car_dd")->alias('a')->where("status=4 and gid=0")->where($map)->join("addr b","a.aid = b.aid","LEFT")->order("did desc")->paginate(20,false,['query'=>request()->param()]);
        $this->assign("list",$list);
        $page=$list->render();
        $this->assign("page",$page);
    
        return $this->fetch();
    }
    public function outw(){
        $start=input('start');
        $end=input('end');
        $code=\input('code');
      
        $addr=\input('addr');
       
        if($start || $code ||  $addr){
            if($start){
                
                $map['time']=['between time',[$start.'00:00:01',$end.'23:59:59']];
            }
            if($code){
                $map['code']=array('like','%'.$code.'%');
            }
         
            if($addr){
                $maps['addr|addrs|username|phone']=array('like','%'.$addr.'%');
                $re=db("addr")->where($maps)->select();
           
                    $id=array();
                    foreach ($re as $v){
                        $id[]=$v['aid'];
                    }
                    $map['a.aid']=array("in",$id);
          
            }
        }else{
            
            $map=[];
        }
    
        $list=db("car_dd")->alias('a')->where("status=4 and gid=0")->where($map)->join("addr b","a.aid = b.aid","LEFT")->order("did desc")->select();
        // var_dump($data);exit;
        vendor('PHPExcel.PHPExcel');//调用类库,路径是基于vendor文件夹的
        vendor('PHPExcel.PHPExcel.Worksheet.Drawing');
        vendor('PHPExcel.PHPExcel.Writer.Excel2007');
        $objExcel = new \PHPExcel();
        //set document Property
        $objWriter = \PHPExcel_IOFactory::createWriter($objExcel, 'Excel2007');
    
        $objActSheet = $objExcel->getActiveSheet();
        $key = ord("A");
        $letter =explode(',',"A,B,C,D,E,F,G");
        $arrHeader =  array("订单号","订单总金额","下单时间","收货人姓名","收货人电话","收货人地址","订单备注");
        //填充表头信息
        $lenth =  count($arrHeader);
        for($i = 0;$i < $lenth;$i++) {
            $objActSheet->setCellValue("$letter[$i]1","$arrHeader[$i]");
        }
        //填充表格信息
        foreach($list as $k=>$v){
            $k +=2;
            $objActSheet->setCellValue('A'.$k,$v['code']);
            $objActSheet->setCellValue('B'.$k, $v['zprice']);
            // 表格内容
            $objActSheet->setCellValue('C'.$k, \date("Y-m-d H:i:s",$v['time']));
            $objActSheet->setCellValue('D'.$k, $v['username']);
            $objActSheet->setCellValue('E'.$k, $v['phone']);
            $objActSheet->setCellValue('F'.$k, $v['addr'].$v['addrs']);
            $objActSheet->setCellValue('G'.$k, $v['content']);
    
            // 表格高度
            $objActSheet->getRowDimension($k)->setRowHeight(20);
        }
    
        $width = array(20,20,15,10,10,30,10,15,15,15);
        //设置表格的宽度
        $objActSheet->getColumnDimension('A')->setWidth(20);
        $objActSheet->getColumnDimension('B')->setWidth(20);
        $objActSheet->getColumnDimension('C')->setWidth(25);
        $objActSheet->getColumnDimension('D')->setWidth(25);
        $objActSheet->getColumnDimension('E')->setWidth(25);
        $objActSheet->getColumnDimension('F')->setWidth(30);
        $objActSheet->getColumnDimension('G')->setWidth(30);
    
    
        if($start !=0 ){
             
            $times=($start."-".$end);
        }else{
            $times="";
        }
        $outfile = "$times"."已完成订单".".xls";
    
        $userBrowser=$_SERVER['HTTP_USER_AGENT'];
    
        if(preg_match('/MSIE/i', $userBrowser)){
            $outfile=urlencode($outfile);
             
        }else{
            $outfile= iconv("utf-8","gb2312",$outfile);;
    
        }
        ob_end_clean();
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header('Content-Disposition:inline;filename="'.$outfile.'"');
        header("Content-Transfer-Encoding: binary");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Pragma: no-cache");
        $objWriter->save('php://output');
    }
    public function tui_dd()
    {
        $start=input('start');
        $end=input('end');
        $code=\input('code');
      
        $addr=\input('addr');
       
        if($start || $code ||  $addr){
            if($start){
                
                $map['time']=['between time',[$start.'00:00:01',$end.'23:59:59']];
            }
            if($code){
                $map['code']=array('like','%'.$code.'%');
            }
         
            if($addr){
                $maps['addr|addrs|username|phone']=array('like','%'.$addr.'%');
                $re=db("addr")->where($maps)->select();
           
                    $id=array();
                    foreach ($re as $v){
                        $id[]=$v['aid'];
                    }
                    $map['a.aid']=array("in",$id);
          
            }
        }else{
            
            $start="";
            $end="";
        
            $addr="";
            $code="";
            $map=[];
        }
        $this->assign("start",$start);
        $this->assign("end",$end);
      
        $this->assign("addr",$addr);
        $this->assign("code",$code);
    
        $list=db("car_dd")->alias('a')->where("status=5 and gid=0 and state=0")->where($map)->join("addr b","a.aid = b.aid","LEFT")->order("did desc")->paginate(20,false,['query'=>request()->param()]);
        $this->assign("list",$list);
        $page=$list->render();
        $this->assign("page",$page);
    
        return \view("tui_dd");
    }
    public function ytui_dd()
    {
        $start=input('start');
        $end=input('end');
        $code=\input('code');
      
        $addr=\input('addr');
       
        if($start || $code ||  $addr){
            if($start){
                
                $map['time']=['between time',[$start.'00:00:01',$end.'23:59:59']];
            }
            if($code){
                $map['code']=array('like','%'.$code.'%');
            }
         
            if($addr){
                $maps['addr|addrs|username|phone']=array('like','%'.$addr.'%');
                $re=db("addr")->where($maps)->select();
           
                    $id=array();
                    foreach ($re as $v){
                        $id[]=$v['aid'];
                    }
                    $map['a.aid']=array("in",$id);
          
            }
        }else{
            
            $start="";
            $end="";
        
            $addr="";
            $code="";
            $map=[];
        }
        $this->assign("start",$start);
        $this->assign("end",$end);
      
        $this->assign("addr",$addr);
        $this->assign("code",$code);
    
        $list=db("car_dd")->alias('a')->where("status=5 and gid=0 and state=1")->where($map)->join("addr b","a.aid = b.aid","LEFT")->order("did desc")->paginate(20,false,['query'=>request()->param()]);
        $this->assign("list",$list);
        $page=$list->render();
        $this->assign("page",$page);
    
        return \view("ytui_dd");
    }
    public function bo_dd()
    {
        $start=input('start');
        $end=input('end');
        $code=\input('code');
      
        $addr=\input('addr');
       
        if($start || $code ||  $addr){
            if($start){
                
                $map['time']=['between time',[$start.'00:00:01',$end.'23:59:59']];
            }
            if($code){
                $map['code']=array('like','%'.$code.'%');
            }
         
            if($addr){
                $maps['addr|addrs|username|phone']=array('like','%'.$addr.'%');
                $re=db("addr")->where($maps)->select();
           
                    $id=array();
                    foreach ($re as $v){
                        $id[]=$v['aid'];
                    }
                    $map['a.aid']=array("in",$id);
          
            }
        }else{
            
            $start="";
            $end="";
        
            $addr="";
            $code="";
            $map=[];
        }
        $this->assign("start",$start);
        $this->assign("end",$end);
      
        $this->assign("addr",$addr);
        $this->assign("code",$code);
    
        $list=db("car_dd")->alias('a')->where("gid=0 and state=2")->where($map)->join("addr b","a.aid = b.aid","LEFT")->order("did desc")->paginate(20,false,['query'=>request()->param()]);
        $this->assign("list",$list);
        $page=$list->render();
        $this->assign("page",$page);
    
        return $this->fetch();
    }
    public function tong()
    {
        $did=\input('id');
        $re=db("car_dd")->where("did=$did")->find();
        if($re['status'] == 5){
            $res=db("car_dd")->where("did=$did")->setField("state",1);
            $pay=$re['pay'];
            $pays=\explode(",", $pay);
            foreach ($pays as $v){
                db("car_dd")->where("code='$v'")->setField("state",1);
            }
            $this->redirect("tui_dd");
        }else{
            $this->redirect("tui_dd");
        }
    }
    public function bo()
    {
        $did=\input('id');
        $re=db("car_dd")->where("did=$did")->find();
        if($re['status'] == 5){
            $data['status']=$re['old_status'];
            $data['state']=2;
            $res=db("car_dd")->where("did=$did")->update($data);
            $pay=$re['pay'];
            $pays=\explode(",", $pay);
            foreach ($pays as $v){
                db("car_dd")->where("code='$v'")->update($data);
            }
            $this->redirect("tui_dd");
        }else{
            $this->redirect("tui_dd");
        }
    }
    public function outs(){
        $start=input('start');
        $end=input('end');
        $code=\input('code');
      
        $addr=\input('addr');
       
        if($start || $code ||  $addr){
            if($start){
                
                $map['time']=['between time',[$start.'00:00:01',$end.'23:59:59']];
            }
            if($code){
                $map['code']=array('like','%'.$code.'%');
            }
         
            if($addr){
                $maps['addr|addrs|username|phone']=array('like','%'.$addr.'%');
                $re=db("addr")->where($maps)->select();
           
                    $id=array();
                    foreach ($re as $v){
                        $id[]=$v['aid'];
                    }
                    $map['a.aid']=array("in",$id);
          
            }
        }else{
          
            $map=[];
        }
    
        $list=db("car_dd")->alias('a')->where("status=5 and gid=0 and state=0")->where($map)->join("addr b","a.aid = b.aid","LEFT")->order("did desc")->select();
        // var_dump($data);exit;
        vendor('PHPExcel.PHPExcel');//调用类库,路径是基于vendor文件夹的
        vendor('PHPExcel.PHPExcel.Worksheet.Drawing');
        vendor('PHPExcel.PHPExcel.Writer.Excel2007');
        $objExcel = new \PHPExcel();
        //set document Property
        $objWriter = \PHPExcel_IOFactory::createWriter($objExcel, 'Excel2007');
    
        $objActSheet = $objExcel->getActiveSheet();
        $key = ord("A");
        $letter =explode(',',"A,B,C,D,E,F,G,H,I,J,K");
        $arrHeader =  array("订单号","订单总金额","下单时间","收货人姓名","收货人电话","收货人地址","申请退货时间","退货原因","快递公司","快递号码","退货备注");
        //填充表头信息
        $lenth =  count($arrHeader);
        for($i = 0;$i < $lenth;$i++) {
            $objActSheet->setCellValue("$letter[$i]1","$arrHeader[$i]");
        }
        //填充表格信息
        foreach($list as $k=>$v){
            $k +=2;
            $objActSheet->setCellValue('A'.$k,$v['code']);
            $objActSheet->setCellValue('B'.$k, $v['zprice']);
            // 表格内容
            $objActSheet->setCellValue('C'.$k, \date("Y-m-d H:i:s",$v['time']));
            $objActSheet->setCellValue('D'.$k, $v['username']);
            $objActSheet->setCellValue('E'.$k, $v['phone']);
            $objActSheet->setCellValue('F'.$k, $v['addr'].$v['addrs']);
            $objActSheet->setCellValue('G'.$k, \date("Y-m-d H:i:s",$v['t_time']));
            $objActSheet->setCellValue('H'.$k, $v['cencal']);
            $objActSheet->setCellValue('I'.$k, $v['express']);
            $objActSheet->setCellValue('J'.$k, $v['number']);
            $objActSheet->setCellValue('K'.$k, $v['remarks']);
    
    
            // 表格高度
            $objActSheet->getRowDimension($k)->setRowHeight(20);
        }
    
        $width = array(20,20,15,10,10,30,10,15,15,15);
        //设置表格的宽度
        $objActSheet->getColumnDimension('A')->setWidth(20);
        $objActSheet->getColumnDimension('B')->setWidth(20);
        $objActSheet->getColumnDimension('C')->setWidth(25);
        $objActSheet->getColumnDimension('D')->setWidth(25);
        $objActSheet->getColumnDimension('E')->setWidth(25);
        $objActSheet->getColumnDimension('F')->setWidth(30);
        $objActSheet->getColumnDimension('G')->setWidth(25);
        $objActSheet->getColumnDimension('H')->setWidth(30);
        $objActSheet->getColumnDimension('I')->setWidth(30);
        $objActSheet->getColumnDimension('J')->setWidth(30);
        $objActSheet->getColumnDimension('K')->setWidth(30);
    
        if($start !=0 ){
             
            $times=($start."-".$end);
        }else{
            $times="";
        }
        $outfile = "$times"."申请退货订单".".xls";
    
        $userBrowser=$_SERVER['HTTP_USER_AGENT'];
    
        if(preg_match('/MSIE/i', $userBrowser)){
            $outfile=urlencode($outfile);
             
        }else{
            $outfile= iconv("utf-8","gb2312",$outfile);;
    
        }
        ob_end_clean();
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header('Content-Disposition:inline;filename="'.$outfile.'"');
        header("Content-Transfer-Encoding: binary");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Pragma: no-cache");
        $objWriter->save('php://output');
    }

    public function outb(){
        $start=input('start');
        $end=input('end');
        $code=\input('code');
      
        $addr=\input('addr');
       
        if($start || $code ||  $addr){
            if($start){
                
                $map['time']=['between time',[$start.'00:00:01',$end.'23:59:59']];
            }
            if($code){
                $map['code']=array('like','%'.$code.'%');
            }
         
            if($addr){
                $maps['addr|addrs|username|phone']=array('like','%'.$addr.'%');
                $re=db("addr")->where($maps)->select();
           
                    $id=array();
                    foreach ($re as $v){
                        $id[]=$v['aid'];
                    }
                    $map['a.aid']=array("in",$id);
          
            }
        }else{
          
            $map=[];
        }
    
        $list=db("car_dd")->alias('a')->where("gid=0 and state=2")->where($map)->join("addr b","a.aid = b.aid","LEFT")->order("did desc")->select();
        // var_dump($data);exit;
        vendor('PHPExcel.PHPExcel');//调用类库,路径是基于vendor文件夹的
        vendor('PHPExcel.PHPExcel.Worksheet.Drawing');
        vendor('PHPExcel.PHPExcel.Writer.Excel2007');
        $objExcel = new \PHPExcel();
        //set document Property
        $objWriter = \PHPExcel_IOFactory::createWriter($objExcel, 'Excel2007');
    
        $objActSheet = $objExcel->getActiveSheet();
        $key = ord("A");
        $letter =explode(',',"A,B,C,D,E,F,G,H,I,J,K");
        $arrHeader =  array("订单号","订单总金额","下单时间","收货人姓名","收货人电话","收货人地址","申请退货时间","退货原因","快递公司","快递号码","退货备注");
        //填充表头信息
        $lenth =  count($arrHeader);
        for($i = 0;$i < $lenth;$i++) {
            $objActSheet->setCellValue("$letter[$i]1","$arrHeader[$i]");
        }
        //填充表格信息
        foreach($list as $k=>$v){
            $k +=2;
            $objActSheet->setCellValue('A'.$k,$v['code']);
            $objActSheet->setCellValue('B'.$k, $v['zprice']);
            // 表格内容
            $objActSheet->setCellValue('C'.$k, \date("Y-m-d H:i:s",$v['time']));
            $objActSheet->setCellValue('D'.$k, $v['username']);
            $objActSheet->setCellValue('E'.$k, $v['phone']);
            $objActSheet->setCellValue('F'.$k, $v['addr'].$v['addrs']);
            $objActSheet->setCellValue('G'.$k, \date("Y-m-d H:i:s",$v['t_time']));
            $objActSheet->setCellValue('H'.$k, $v['cencal']);
            $objActSheet->setCellValue('I'.$k, $v['express']);
            $objActSheet->setCellValue('J'.$k, $v['number']);
            $objActSheet->setCellValue('K'.$k, $v['remarks']);
    
    
            // 表格高度
            $objActSheet->getRowDimension($k)->setRowHeight(20);
        }
    
        $width = array(20,20,15,10,10,30,10,15,15,15);
        //设置表格的宽度
        $objActSheet->getColumnDimension('A')->setWidth(20);
        $objActSheet->getColumnDimension('B')->setWidth(20);
        $objActSheet->getColumnDimension('C')->setWidth(25);
        $objActSheet->getColumnDimension('D')->setWidth(25);
        $objActSheet->getColumnDimension('E')->setWidth(25);
        $objActSheet->getColumnDimension('F')->setWidth(30);
        $objActSheet->getColumnDimension('G')->setWidth(25);
        $objActSheet->getColumnDimension('H')->setWidth(30);
        $objActSheet->getColumnDimension('I')->setWidth(30);
        $objActSheet->getColumnDimension('J')->setWidth(30);
        $objActSheet->getColumnDimension('K')->setWidth(30);
    
        if($start !=0 ){
             
            $times=($start."-".$end);
        }else{
            $times="";
        }
        $outfile = "$times"."已驳回订单".".xls";
    
        $userBrowser=$_SERVER['HTTP_USER_AGENT'];
    
        if(preg_match('/MSIE/i', $userBrowser)){
            $outfile=urlencode($outfile);
             
        }else{
            $outfile= iconv("utf-8","gb2312",$outfile);;
    
        }
        ob_end_clean();
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header('Content-Disposition:inline;filename="'.$outfile.'"');
        header("Content-Transfer-Encoding: binary");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Pragma: no-cache");
        $objWriter->save('php://output');
    }
    
    public function outsy(){
        $start=input('start');
        $end=input('end');
        $code=\input('code');
      
        $addr=\input('addr');
       
        if($start || $code ||  $addr){
            if($start){
                
                $map['time']=['between time',[$start.'00:00:01',$end.'23:59:59']];
            }
            if($code){
                $map['code']=array('like','%'.$code.'%');
            }
         
            if($addr){
                $maps['addr|addrs|username|phone']=array('like','%'.$addr.'%');
                $re=db("addr")->where($maps)->select();
           
                    $id=array();
                    foreach ($re as $v){
                        $id[]=$v['aid'];
                    }
                    $map['a.aid']=array("in",$id);
          
            }
        }else{
          
            $map=[];
        }
    
        $list=db("car_dd")->alias('a')->where("status=5 and gid=0 and state=1")->where($map)->join("addr b","a.aid = b.aid","LEFT")->order("did desc")->select();
        // var_dump($data);exit;
        vendor('PHPExcel.PHPExcel');//调用类库,路径是基于vendor文件夹的
        vendor('PHPExcel.PHPExcel.Worksheet.Drawing');
        vendor('PHPExcel.PHPExcel.Writer.Excel2007');
        $objExcel = new \PHPExcel();
        //set document Property
        $objWriter = \PHPExcel_IOFactory::createWriter($objExcel, 'Excel2007');
    
        $objActSheet = $objExcel->getActiveSheet();
        $key = ord("A");
        $letter =explode(',',"A,B,C,D,E,F,G,H,I,J,K");
        $arrHeader =  array("订单号","订单总金额","下单时间","收货人姓名","收货人电话","收货人地址","申请退货时间","退货原因","快递公司","快递号码","退货备注");
        //填充表头信息
        $lenth =  count($arrHeader);
        for($i = 0;$i < $lenth;$i++) {
            $objActSheet->setCellValue("$letter[$i]1","$arrHeader[$i]");
        }
        //填充表格信息
        foreach($list as $k=>$v){
            $k +=2;
            $objActSheet->setCellValue('A'.$k,$v['code']);
            $objActSheet->setCellValue('B'.$k, $v['zprice']);
            // 表格内容
            $objActSheet->setCellValue('C'.$k, \date("Y-m-d H:i:s",$v['time']));
            $objActSheet->setCellValue('D'.$k, $v['username']);
            $objActSheet->setCellValue('E'.$k, $v['phone']);
            $objActSheet->setCellValue('F'.$k, $v['addr'].$v['addrs']);
            $objActSheet->setCellValue('G'.$k, \date("Y-m-d H:i:s",$v['t_time']));
            $objActSheet->setCellValue('H'.$k, $v['cencal']);
            $objActSheet->setCellValue('I'.$k, $v['express']);
            $objActSheet->setCellValue('J'.$k, $v['number']);
            $objActSheet->setCellValue('K'.$k, $v['remarks']);
    
    
            // 表格高度
            $objActSheet->getRowDimension($k)->setRowHeight(20);
        }
    
        $width = array(20,20,15,10,10,30,10,15,15,15);
        //设置表格的宽度
        $objActSheet->getColumnDimension('A')->setWidth(20);
        $objActSheet->getColumnDimension('B')->setWidth(20);
        $objActSheet->getColumnDimension('C')->setWidth(25);
        $objActSheet->getColumnDimension('D')->setWidth(25);
        $objActSheet->getColumnDimension('E')->setWidth(25);
        $objActSheet->getColumnDimension('F')->setWidth(30);
        $objActSheet->getColumnDimension('G')->setWidth(25);
        $objActSheet->getColumnDimension('H')->setWidth(30);
        $objActSheet->getColumnDimension('I')->setWidth(30);
        $objActSheet->getColumnDimension('J')->setWidth(30);
        $objActSheet->getColumnDimension('K')->setWidth(30);
    
        if($start !=0 ){
             
            $times=($start."-".$end);
        }else{
            $times="";
        }
        $outfile = "$times"."已退货订单".".xls";
    
        $userBrowser=$_SERVER['HTTP_USER_AGENT'];
    
        if(preg_match('/MSIE/i', $userBrowser)){
            $outfile=urlencode($outfile);
             
        }else{
            $outfile= iconv("utf-8","gb2312",$outfile);;
    
        }
        ob_end_clean();
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header('Content-Disposition:inline;filename="'.$outfile.'"');
        header("Content-Transfer-Encoding: binary");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Pragma: no-cache");
        $objWriter->save('php://output');
    }
    public function updates()
    {
        $id=\input('id');
        $re=db("car_dd")->where("did=$id")->find();
        $this->assign("re",$re);
        return \view("updates");
    }
    public function saved()
    {
        $did=input('did');
        $data=\input('post.');
        $re=db("car_dd")->where("did=$did")->find();
        if($re){
            $res=db("car_dd")->where("did=$did")->update($data);
            if($res){
                $this->success("修改成功",url('dai_dd'));
            }else{
                $this->error("修改失败",url('dai_dd'));
            }
        }else{
            $this->error("非法操作",url('dai_dd'));
        }
    }
    
    
    
    
    
    
    
    
    
    
}