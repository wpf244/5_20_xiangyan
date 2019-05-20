<?php
namespace app\admin\controller;

use app\admin\model\Share as AppShare;

\header("content-type:text/html;charset=utf-8;");
class Dd extends BaseAdmin
{
    public function dai_dd()
    {
        $start=input('start');
        $end=input('end');
        $code=\input('code');
        $phone=\input('phone');
        $hid=input("hid");
        $map=[];
        if($start){
            $arrdateone = strtotime($start);
            $arrdatetwo = strtotime($end . ' 23:55:55');
            $map['time'] = array(
                array(
                    'egt',
                    $arrdateone
                ),
                array(
                    'elt',
                    $arrdatetwo
                ),
                'AND'
            );
        }
        if($code){
            $map['code']=array('like','%'.$code.'%');
        }
    
        if($phone){
            $map['phone']=array('like','%'.$phone.'%');
            
        }
        if($hid != 0){
            $map['hid']=['eq',$hid];
        }
         
        
        $this->assign("start",$start);
        $this->assign("end",$end);
 
        $this->assign("phone",$phone);

        $this->assign("code",$code);

        $this->assign("hid",$hid);
        
        $list=db("order")->where(['status'=>0,"is_delete"=>0])->where($map)->order("id desc")->paginate(20,false,['query'=>request()->param()]);
        $this->assign("list",$list);
        $page=$list->render();
        $this->assign("page",$page);

        $hotel=db("hotel")->where(["up"=>1,"is_delete"=>0])->select();
        $this->assign("hotel",$hotel);
        
        return $this->fetch();
    }
    public function out(){
        $start=input('start');
        $end=input('end');
        $code=\input('code');
        $phone=\input('phone');
        $hid=input("hid");
        $map=[];
        if($start){
            $arrdateone = strtotime($start);
            $arrdatetwo = strtotime($end . ' 23:55:55');
            $map['time'] = array(
                array(
                    'egt',
                    $arrdateone
                ),
                array(
                    'elt',
                    $arrdatetwo
                ),
                'AND'
            );
        }
        if($code){
            $map['code']=array('like','%'.$code.'%');
        }
    
        if($phone){
            $map['phone']=array('like','%'.$phone.'%');
            
        }
        if($hid != 0){
            $map['hid']=['eq',$hid];
        }
         
        $list=db("order")->where(['status'=>0,"is_delete"=>0])->where($map)->order("id desc")->paginate(20,false,['query'=>request()->param()]);
        // var_dump($data);exit;
        vendor('PHPExcel.PHPExcel');//调用类库,路径是基于vendor文件夹的
        vendor('PHPExcel.PHPExcel.Worksheet.Drawing');
        vendor('PHPExcel.PHPExcel.Writer.Excel2007');
        $objExcel = new \PHPExcel();
        //set document Property
        $objWriter = \PHPExcel_IOFactory::createWriter($objExcel, 'Excel2007');
    
        $objActSheet = $objExcel->getActiveSheet();
        $key = ord("A");
        $letter =explode(',',"A,B,C,D,E,F,G,H,I,J");
        $arrHeader =  array("订单号","订单金额","酒店名称","房间名称","房间类型","会议时间","会议人数","会议需求","联系电话","下单时间");
        //填充表头信息
        $lenth =  count($arrHeader);
        for($i = 0;$i < $lenth;$i++) {
            $objActSheet->setCellValue("$letter[$i]1","$arrHeader[$i]");
        }
        //填充表格信息
        foreach($list as $k=>$v){
            $k +=2;
            $objActSheet->setCellValue('A'.$k,$v['code']);
            $objActSheet->setCellValue('B'.$k, $v['price']);    
            // 表格内容
            $objActSheet->setCellValue('C'.$k, $v['hotel']);
            $objActSheet->setCellValue('D'.$k, $v['room']);
            $objActSheet->setCellValue('E'.$k, $v['room_type']);
            $objActSheet->setCellValue('F'.$k, $v['dates']);
            $objActSheet->setCellValue('G'.$k, $v['number']);
            $objActSheet->setCellValue('H'.$k, $v['ment']);
            $objActSheet->setCellValue('I'.$k, $v['phone']);
            $objActSheet->setCellValue('J'.$k, \date("Y-m-d H:i:s",$v['time']));
         

    
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
        $objActSheet->getColumnDimension('H')->setWidth(30);
        $objActSheet->getColumnDimension('I')->setWidth(30);
        $objActSheet->getColumnDimension('J')->setWidth(30);
  

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
   
    public function delete()
    {
        $id=\input('id');
        $re=db("order")->where("id=$id")->find();
        if($re){
            $del=db("order")->where("id=$id")->setField("is_delete",-1);
           
            $this->redirect("dai_dd");
        }else{
            $this->redirect("dai_dd");
        }
    }
    public function change()
    {
        $id=\input('id');
        $re=db("order")->where("id=$id")->find();
        if($re){
            $data['status']=1;
            $data['fu_time']=time();
            db("order")->where("id=$id")->update($data);
          
            
            //是否开启分销
            $basic=db("basic")->where("id",1)->find();
            if($basic['status'] == 1){
                //分销返利
                $moneys=$re['price'];
                $uid=$re['uid'];
                $share=new AppShare();
                $share->share($uid,$moneys);
            }
            
           
            
            $this->redirect("dai_dd");
        }else{
            $this->redirect("dai_dd");
        }
    }
    public function changes()
    {
        $id=\input('id');
        $re=db("order")->where("id=$id")->find();
        if($re){
            $data['status']=2;
            
            db("order")->where("id=$id")->update($data);
          
            
            $this->redirect("fa_dd");
        }else{
            $this->redirect("fa_dd");
        }
    }
    public function fa_dd()
    {
        $start=input('start');
        $end=input('end');
        $code=\input('code');
        $phone=\input('phone');
        $hid=input("hid");
        $map=[];
        if($start){
            $arrdateone = strtotime($start);
            $arrdatetwo = strtotime($end . ' 23:55:55');
            $map['time'] = array(
                array(
                    'egt',
                    $arrdateone
                ),
                array(
                    'elt',
                    $arrdatetwo
                ),
                'AND'
            );
        }
        if($code){
            $map['code']=array('like','%'.$code.'%');
        }
    
        if($phone){
            $map['phone']=array('like','%'.$phone.'%');
            
        }
        if($hid != 0){
            $map['hid']=['eq',$hid];
        }
         
        
        $this->assign("start",$start);
        $this->assign("end",$end);
 
        $this->assign("phone",$phone);

        $this->assign("code",$code);

        $this->assign("hid",$hid);
        
        $list=db("order")->where(['status'=>['gt',0],"is_delete"=>0])->where($map)->order(["status asc","id desc"])->paginate(20,false,['query'=>request()->param()]);
        $this->assign("list",$list);
        $page=$list->render();
        $this->assign("page",$page);

        $hotel=db("hotel")->where(["up"=>1,"is_delete"=>0])->select();
        $this->assign("hotel",$hotel);
        
        return \view("fa_dd");
    }
    public function outf(){
        $start=input('start');
        $end=input('end');
        $code=\input('code');
        $phone=\input('phone');
        $hid=input("hid");
        $map=[];
        if($start){
            $arrdateone = strtotime($start);
            $arrdatetwo = strtotime($end . ' 23:55:55');
            $map['time'] = array(
                array(
                    'egt',
                    $arrdateone
                ),
                array(
                    'elt',
                    $arrdatetwo
                ),
                'AND'
            );
        }
        if($code){
            $map['code']=array('like','%'.$code.'%');
        }
    
        if($phone){
            $map['phone']=array('like','%'.$phone.'%');
            
        }
        if($hid != 0){
            $map['hid']=['eq',$hid];
        }
         
        $list=db("order")->where(['status'=>['gt',0],"is_delete"=>0])->where($map)->order("id desc")->paginate(20,false,['query'=>request()->param()]);
        // var_dump($data);exit;
        vendor('PHPExcel.PHPExcel');//调用类库,路径是基于vendor文件夹的
        vendor('PHPExcel.PHPExcel.Worksheet.Drawing');
        vendor('PHPExcel.PHPExcel.Writer.Excel2007');
        $objExcel = new \PHPExcel();
        //set document Property
        $objWriter = \PHPExcel_IOFactory::createWriter($objExcel, 'Excel2007');
    
        $objActSheet = $objExcel->getActiveSheet();
        $key = ord("A");
        $letter =explode(',',"A,B,C,D,E,F,G,H,I,J");
        $arrHeader =  array("订单号","订单金额","酒店名称","房间名称","房间类型","会议时间","会议人数","会议需求","联系电话","下单时间");
        //填充表头信息
        $lenth =  count($arrHeader);
        for($i = 0;$i < $lenth;$i++) {
            $objActSheet->setCellValue("$letter[$i]1","$arrHeader[$i]");
        }
        //填充表格信息
        foreach($list as $k=>$v){
            $k +=2;
            $objActSheet->setCellValue('A'.$k,$v['code']);
            $objActSheet->setCellValue('B'.$k, $v['price']);    
            // 表格内容
            $objActSheet->setCellValue('C'.$k, $v['hotel']);
            $objActSheet->setCellValue('D'.$k, $v['room']);
            $objActSheet->setCellValue('E'.$k, $v['room_type']);
            $objActSheet->setCellValue('F'.$k, $v['dates']);
            $objActSheet->setCellValue('G'.$k, $v['number']);
            $objActSheet->setCellValue('H'.$k, $v['ment']);
            $objActSheet->setCellValue('I'.$k, $v['phone']);
            $objActSheet->setCellValue('J'.$k, \date("Y-m-d H:i:s",$v['time']));
         

    
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
        $objActSheet->getColumnDimension('H')->setWidth(30);
        $objActSheet->getColumnDimension('I')->setWidth(30);
        $objActSheet->getColumnDimension('J')->setWidth(30);
  

        if($start !=0 ){
             
            $times=($start."-".$end);
        }else{
            $times="";
        }
        $outfile = "$times"."已付款订单".".xls";
    
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
               $data['f_time']=\time();
               $res=db("car_dd")->where("did=$did")->update($data);
               
               $pay=$re['pay'];
               $arr=\explode(",", $pay);
               foreach ($arr as $v){
                   $ress=db("car_dd")->where("code='$v'")->update($data);
               }
               //增加积分
              /*  $uid=$re['uid'];
               $user=db("user")->where("uid=$uid")->find();
               if($user){
                   $money=$re['zprice'];
                   $resu=db("user")->where("uid=$uid")->setInc("integ",$money);
               
                   //增加积分日志
                   $arrs['uid']=$uid;
                   $arrs['type']="购物增加积分".$money;
                   $arrs['time']=\time();
                   $arrs['money']=$money;
                   $arrs['status']=1;
                   db("ji_log")->insert($arrs);
               
               } */
               
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
        $username=\input('username');
        $addr=\input('addr');
        $phone=\input('phone');
        if($start || $code || $username || $addr || $phone){
            if($start){
                $arrdateone = strtotime($start);
                $arrdatetwo = strtotime($end . ' 23:55:55');
                $map['time'] = array(
                    array(
                        'egt',
                        $arrdateone
                    ),
                    array(
                        'elt',
                        $arrdatetwo
                    ),
                    'AND'
                );
            }
            if($code){
                $map['code']=array('like','%'.$code.'%');
            }
            if($username){
                $maps['username']=array('like','%'.$username.'%');
                $re=db("addr")->where($maps)->select();
              //  if($re){
                    $id=array();
                    foreach ($re as $v){
                        $id[]=$v['aid'];
                    }
                    $map['a_id']=array("in",$id);
             //   }else{
              //      $map=[];
             //   }
            }
            if($phone){
                $maps['phone']=array('like','%'.$phone.'%');
                $re=db("addr")->where($maps)->select();
          //      if($re){
                    $id=array();
                    foreach ($re as $v){
                        $id[]=$v['aid'];
                    }
                    $map['a_id']=array("in",$id);
           //     }else{
            //        $map=[];
            //    }
            }
            if($addr){
                $maps['addr|addrs']=array('like','%'.$addr.'%');
                $re=db("addr")->where($maps)->select();
           //     if($re){
                    $id=array();
                    foreach ($re as $v){
                        $id[]=$v['aid'];
                    }
                    $map['a_id']=array("in",$id);
          //      }else{
          //          $map=[];
           //     }
            }
        }else{
        
            $start="";
            $end="";
            $username="";
            $phone="";
            $addr="";
            $code="";
            $map=[];
        }
        $this->assign("start",$start);
        $this->assign("end",$end);
        $this->assign("username",$username);
        $this->assign("phone",$phone);
        $this->assign("addr",$addr);
        $this->assign("code",$code);
        
        $list=db("car_dd")->alias('a')->where("status=2 and gid=0")->where($map)->join("addr b","a.a_id = b.aid","LEFT")->order("did desc")->paginate(20,false,['query'=>request()->param()]);
        $this->assign("list",$list);
        $page=$list->render();
        $this->assign("page",$page);
        
        return \view("shou_dd");
    }
    public function outh(){
        $start=input('start');
        $end=input('end');
        $code=\input('code');
        $username=\input('username');
        $addr=\input('addr');
        $phone=\input('phone');
        if($start || $code || $username || $addr || $phone){
            if($start){
                $arrdateone = strtotime($start);
                $arrdatetwo = strtotime($end . ' 23:55:55');
                $map['time'] = array(
                    array(
                        'egt',
                        $arrdateone
                    ),
                    array(
                        'elt',
                        $arrdatetwo
                    ),
                    'AND'
                );
            }
            if($code){
                $map['code']=array('like','%'.$code.'%');
            }
            if($username){
                $maps['username']=array('like','%'.$username.'%');
                $re=db("addr")->where($maps)->select();
           //     if($re){
                    $id=array();
                    foreach ($re as $v){
                        $id[]=$v['aid'];
                    }
                    $map['a_id']=array("in",$id);
            //    }else{
            //        $map=[];
             //   }
            }
            if($phone){
                $maps['phone']=array('like','%'.$phone.'%');
                $re=db("addr")->where($maps)->select();
            //    if($re){
                    $id=array();
                    foreach ($re as $v){
                        $id[]=$v['aid'];
                    }
                    $map['a_id']=array("in",$id);
           //     }else{
           //         $map=[];
           //     }
            }
            if($addr){
                $maps['addr|addrs']=array('like','%'.$addr.'%');
                $re=db("addr")->where($maps)->select();
          //      if($re){
                    $id=array();
                    foreach ($re as $v){
                        $id[]=$v['aid'];
                    }
                    $map['a_id']=array("in",$id);
         //       }else{
         //           $map=[];
         //       }
            }
        }else{
    
            $start="";
            $end="";
            $username="";
            $phone="";
            $addr="";
            $code="";
            $map=[];
        }
        $this->assign("start",$start);
        $this->assign("end",$end);
        $this->assign("username",$username);
        $this->assign("phone",$phone);
        $this->assign("addr",$addr);
        $this->assign("code",$code);
    
        $list=db("car_dd")->alias('a')->where("status=2 and gid=0")->where($map)->join("addr b","a.a_id = b.aid","LEFT")->order("did desc")->select();
        // var_dump($data);exit;
        vendor('PHPExcel.PHPExcel');//调用类库,路径是基于vendor文件夹的
        vendor('PHPExcel.PHPExcel.Worksheet.Drawing');
        vendor('PHPExcel.PHPExcel.Writer.Excel2007');
        $objExcel = new \PHPExcel();
        //set document Property
        $objWriter = \PHPExcel_IOFactory::createWriter($objExcel, 'Excel2007');
    
        $objActSheet = $objExcel->getActiveSheet();
        $key = ord("A");
        $letter =explode(',',"A,B,C,D,E,F");
        $arrHeader =  array("订单号","订单总金额","下单时间","收货人姓名","收货人电话","收货人地址");
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
        $username=\input('username');
        $addr=\input('addr');
        $phone=\input('phone');
        if($start || $code || $username || $addr || $phone){
            if($start){
                $arrdateone = strtotime($start);
                $arrdatetwo = strtotime($end . ' 23:55:55');
                $map['time'] = array(
                    array(
                        'egt',
                        $arrdateone
                    ),
                    array(
                        'elt',
                        $arrdatetwo
                    ),
                    'AND'
                );
            }
            if($code){
                $map['code']=array('like','%'.$code.'%');
            }
            if($username){
                $maps['username']=array('like','%'.$username.'%');
                $re=db("addr")->where($maps)->select();
           //     if($re){
                    $id=array();
                    foreach ($re as $v){
                        $id[]=$v['aid'];
                    }
                    $map['a_id']=array("in",$id);
          //      }else{
          //          $map=[];
           //     }
            }
            if($phone){
                $maps['phone']=array('like','%'.$phone.'%');
                $re=db("addr")->where($maps)->select();
         //       if($re){
                    $id=array();
                    foreach ($re as $v){
                        $id[]=$v['aid'];
                    }
                    $map['a_id']=array("in",$id);
         //       }else{
         //           $map=[];
         //       }
            }
            if($addr){
                $maps['addr|addrs']=array('like','%'.$addr.'%');
                $re=db("addr")->where($maps)->select();
          //      if($re){
                    $id=array();
                    foreach ($re as $v){
                        $id[]=$v['aid'];
                    }
                    $map['a_id']=array("in",$id);
           //     }else{
           //         $map=[];
           //     }
            }
        }else{
    
            $start="";
            $end="";
            $username="";
            $phone="";
            $addr="";
            $code="";
            $map=[];
        }
        $this->assign("start",$start);
        $this->assign("end",$end);
        $this->assign("username",$username);
        $this->assign("phone",$phone);
        $this->assign("addr",$addr);
        $this->assign("code",$code);
    
        $list=db("car_dd")->alias('a')->where("status=3 and gid=0")->where($map)->join("addr b","a.a_id = b.aid","LEFT")->order("did desc")->paginate(20,false,['query'=>request()->param()]);
        $this->assign("list",$list);
        $page=$list->render();
        $this->assign("page",$page);
    
        return \view("ping_dd");
    }
    public function outp(){
        $start=input('start');
        $end=input('end');
        $code=\input('code');
        $username=\input('username');
        $addr=\input('addr');
        $phone=\input('phone');
        if($start || $code || $username || $addr || $phone){
            if($start){
                $arrdateone = strtotime($start);
                $arrdatetwo = strtotime($end . ' 23:55:55');
                $map['time'] = array(
                    array(
                        'egt',
                        $arrdateone
                    ),
                    array(
                        'elt',
                        $arrdatetwo
                    ),
                    'AND'
                );
            }
            if($code){
                $map['code']=array('like','%'.$code.'%');
            }
            if($username){
                $maps['username']=array('like','%'.$username.'%');
                $re=db("addr")->where($maps)->select();
            //    if($re){
                    $id=array();
                    foreach ($re as $v){
                        $id[]=$v['aid'];
                    }
                    $map['a_id']=array("in",$id);
            //    }else{
            //        $map=[];
            //    }
            }
            if($phone){
                $maps['phone']=array('like','%'.$phone.'%');
                $re=db("addr")->where($maps)->select();
              //  if($re){
                    $id=array();
                    foreach ($re as $v){
                        $id[]=$v['aid'];
                    }
                    $map['a_id']=array("in",$id);
              //  }else{
              //      $map=[];
              //  }
            }
            if($addr){
                $maps['addr|addrs']=array('like','%'.$addr.'%');
                $re=db("addr")->where($maps)->select();
              //  if($re){
                    $id=array();
                    foreach ($re as $v){
                        $id[]=$v['aid'];
                    }
                    $map['a_id']=array("in",$id);
              //  }else{
             //       $map=[];
             //   }
            }
        }else{
    
            $start="";
            $end="";
            $username="";
            $phone="";
            $addr="";
            $code="";
            $map=[];
        }
        $this->assign("start",$start);
        $this->assign("end",$end);
        $this->assign("username",$username);
        $this->assign("phone",$phone);
        $this->assign("addr",$addr);
        $this->assign("code",$code);
    
        $list=db("car_dd")->alias('a')->where("status=3 and gid=0")->where($map)->join("addr b","a.a_id = b.aid","LEFT")->order("did desc")->select();
        // var_dump($data);exit;
        vendor('PHPExcel.PHPExcel');//调用类库,路径是基于vendor文件夹的
        vendor('PHPExcel.PHPExcel.Worksheet.Drawing');
        vendor('PHPExcel.PHPExcel.Writer.Excel2007');
        $objExcel = new \PHPExcel();
        //set document Property
        $objWriter = \PHPExcel_IOFactory::createWriter($objExcel, 'Excel2007');
    
        $objActSheet = $objExcel->getActiveSheet();
        $key = ord("A");
        $letter =explode(',',"A,B,C,D,E,F");
        $arrHeader =  array("订单号","订单总金额","下单时间","收货人姓名","收货人电话","收货人地址");
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
        $username=\input('username');
        $addr=\input('addr');
        $phone=\input('phone');
        if($start || $code || $username || $addr || $phone){
            if($start){
                $arrdateone = strtotime($start);
                $arrdatetwo = strtotime($end . ' 23:55:55');
                $map['time'] = array(
                    array(
                        'egt',
                        $arrdateone
                    ),
                    array(
                        'elt',
                        $arrdatetwo
                    ),
                    'AND'
                );
            }
            if($code){
                $map['code']=array('like','%'.$code.'%');
            }
            if($username){
                $maps['username']=array('like','%'.$username.'%');
                $re=db("addr")->where($maps)->select();
           //     if($re){
                    $id=array();
                    foreach ($re as $v){
                        $id[]=$v['aid'];
                    }
                    $map['a_id']=array("in",$id);
            //    }else{
            //        $map=[];
             //   }
            }
            if($phone){
                $maps['phone']=array('like','%'.$phone.'%');
                $re=db("addr")->where($maps)->select();
            //    if($re){
                    $id=array();
                    foreach ($re as $v){
                        $id[]=$v['aid'];
                    }
                    $map['a_id']=array("in",$id);
            //    }else{
            //        $map=[];
             //   }
            }
            if($addr){
                $maps['addr|addrs']=array('like','%'.$addr.'%');
                $re=db("addr")->where($maps)->select();
              //  if($re){
                    $id=array();
                    foreach ($re as $v){
                        $id[]=$v['aid'];
                    }
                    $map['a_id']=array("in",$id);
             //   }else{
             //       $map=[];
             //   }
            }
        }else{
    
            $start="";
            $end="";
            $username="";
            $phone="";
            $addr="";
            $code="";
            $map=[];
        }
        $this->assign("start",$start);
        $this->assign("end",$end);
        $this->assign("username",$username);
        $this->assign("phone",$phone);
        $this->assign("addr",$addr);
        $this->assign("code",$code);
    
        $list=db("car_dd")->alias('a')->where("status=4 and gid=0")->where($map)->join("addr b","a.a_id = b.aid","LEFT")->order("did desc")->paginate(20,false,['query'=>request()->param()]);
        $this->assign("list",$list);
        $page=$list->render();
        $this->assign("page",$page);
    
        return \view("wan_dd");
    }
    public function outw(){
        $start=input('start');
        $end=input('end');
        $code=\input('code');
        $username=\input('username');
        $addr=\input('addr');
        $phone=\input('phone');
        if($start || $code || $username || $addr || $phone){
            if($start){
                $arrdateone = strtotime($start);
                $arrdatetwo = strtotime($end . ' 23:55:55');
                $map['time'] = array(
                    array(
                        'egt',
                        $arrdateone
                    ),
                    array(
                        'elt',
                        $arrdatetwo
                    ),
                    'AND'
                );
            }
            if($code){
                $map['code']=array('like','%'.$code.'%');
            }
            if($username){
                $maps['username']=array('like','%'.$username.'%');
                $re=db("addr")->where($maps)->select();
             //   if($re){
                    $id=array();
                    foreach ($re as $v){
                        $id[]=$v['aid'];
                    }
                    $map['a_id']=array("in",$id);
             //   }else{
             //       $map=[];
             //   }
            }
            if($phone){
                $maps['phone']=array('like','%'.$phone.'%');
                $re=db("addr")->where($maps)->select();
             //   if($re){
                    $id=array();
                    foreach ($re as $v){
                        $id[]=$v['aid'];
                    }
                    $map['a_id']=array("in",$id);
             //   }else{
              //      $map=[];
             //   }
            }
            if($addr){
                $maps['addr|addrs']=array('like','%'.$addr.'%');
                $re=db("addr")->where($maps)->select();
             //   if($re){
                    $id=array();
                    foreach ($re as $v){
                        $id[]=$v['aid'];
                    }
                    $map['a_id']=array("in",$id);
             //   }else{
             //       $map=[];
             //   }
            }
        }else{
    
            $start="";
            $end="";
            $username="";
            $phone="";
            $addr="";
            $code="";
            $map=[];
        }
        $this->assign("start",$start);
        $this->assign("end",$end);
        $this->assign("username",$username);
        $this->assign("phone",$phone);
        $this->assign("addr",$addr);
        $this->assign("code",$code);
    
        $list=db("car_dd")->alias('a')->where("status=4 and gid=0")->where($map)->join("addr b","a.a_id = b.aid","LEFT")->order("did desc")->select();
        // var_dump($data);exit;
        vendor('PHPExcel.PHPExcel');//调用类库,路径是基于vendor文件夹的
        vendor('PHPExcel.PHPExcel.Worksheet.Drawing');
        vendor('PHPExcel.PHPExcel.Writer.Excel2007');
        $objExcel = new \PHPExcel();
        //set document Property
        $objWriter = \PHPExcel_IOFactory::createWriter($objExcel, 'Excel2007');
    
        $objActSheet = $objExcel->getActiveSheet();
        $key = ord("A");
        $letter =explode(',',"A,B,C,D,E,F");
        $arrHeader =  array("订单号","订单总金额","下单时间","收货人姓名","收货人电话","收货人地址");
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
        $username=\input('username');
        $addr=\input('addr');
        $phone=\input('phone');
        if($start || $code || $username || $addr || $phone){
            if($start){
                $arrdateone = strtotime($start);
                $arrdatetwo = strtotime($end . ' 23:55:55');
                $map['time'] = array(
                    array(
                        'egt',
                        $arrdateone
                    ),
                    array(
                        'elt',
                        $arrdatetwo
                    ),
                    'AND'
                );
            }
            if($code){
                $map['code']=array('like','%'.$code.'%');
            }
            if($username){
                $maps['username']=array('like','%'.$username.'%');
                $re=db("addr")->where($maps)->select();
           //     if($re){
                    $id=array();
                    foreach ($re as $v){
                        $id[]=$v['aid'];
                    }
                    $map['a_id']=array("in",$id);
            //    }else{
            //        $map=[];
            //    }
            }
            if($phone){
                $maps['phone']=array('like','%'.$phone.'%');
                $re=db("addr")->where($maps)->select();
           //     if($re){
                    $id=array();
                    foreach ($re as $v){
                        $id[]=$v['aid'];
                    }
                    $map['a_id']=array("in",$id);
           //     }else{
           //         $map=[];
            //    }
            }
            if($addr){
                $maps['addr|addrs']=array('like','%'.$addr.'%');
                $re=db("addr")->where($maps)->select();
          //      if($re){
                    $id=array();
                    foreach ($re as $v){
                        $id[]=$v['aid'];
                    }
                    $map['a_id']=array("in",$id);
          //      }else{
          //          $map=[];
          //      }
            }
        }else{
    
            $start="";
            $end="";
            $username="";
            $phone="";
            $addr="";
            $code="";
            $map=[];
        }
        $this->assign("start",$start);
        $this->assign("end",$end);
        $this->assign("username",$username);
        $this->assign("phone",$phone);
        $this->assign("addr",$addr);
        $this->assign("code",$code);
    
        $list=db("car_dd")->alias('a')->where("status=6 and gid=0")->where($map)->join("addr b","a.a_id = b.aid","LEFT")->order("did desc")->paginate(20,false,['query'=>request()->param()]);
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
        $username=\input('username');
        $addr=\input('addr');
        $phone=\input('phone');
        if($start || $code || $username || $addr || $phone){
            if($start){
                $arrdateone = strtotime($start);
                $arrdatetwo = strtotime($end . ' 23:55:55');
                $map['time'] = array(
                    array(
                        'egt',
                        $arrdateone
                    ),
                    array(
                        'elt',
                        $arrdatetwo
                    ),
                    'AND'
                );
            }
            if($code){
                $map['code']=array('like','%'.$code.'%');
            }
            if($username){
                $maps['username']=array('like','%'.$username.'%');
                $re=db("addr")->where($maps)->select();
            //    if($re){
                    $id=array();
                    foreach ($re as $v){
                        $id[]=$v['aid'];
                    }
                    $map['a_id']=array("in",$id);
           //     }else{
           //         $map=[];
            //    }
            }
            if($phone){
                $maps['phone']=array('like','%'.$phone.'%');
                $re=db("addr")->where($maps)->select();
            //    if($re){
                    $id=array();
                    foreach ($re as $v){
                        $id[]=$v['aid'];
                    }
                    $map['a_id']=array("in",$id);
            //    }else{
            //        $map=[];
            //    }
            }
            if($addr){
                $maps['addr|addrs']=array('like','%'.$addr.'%');
                $re=db("addr")->where($maps)->select();
            //    if($re){
                    $id=array();
                    foreach ($re as $v){
                        $id[]=$v['aid'];
                    }
                    $map['a_id']=array("in",$id);
            //    }else{
            //        $map=[];
            //    }
            }
        }else{
    
            $start="";
            $end="";
            $username="";
            $phone="";
            $addr="";
            $code="";
            $map=[];
        }
        $this->assign("start",$start);
        $this->assign("end",$end);
        $this->assign("username",$username);
        $this->assign("phone",$phone);
        $this->assign("addr",$addr);
        $this->assign("code",$code);
    
        $list=db("car_dd")->alias('a')->where("status=7 and gid=0")->where($map)->join("addr b","a.a_id = b.aid","LEFT")->order("did desc")->paginate(20,false,['query'=>request()->param()]);
        $this->assign("list",$list);
        $page=$list->render();
        $this->assign("page",$page);
    
        return \view("ytui_dd");
    }
    public function tong()
    {
        $did=\input('id');
        $re=db("car_dd")->where("did=$did")->find();
        if($re['status'] == 6){
            $res=db("car_dd")->where("did=$did")->setField("status",7);
            $pay=$re['pay'];
            $pays=\explode(",", $pay);
            foreach ($pays as $v){
                db("car_dd")->where("code='$v'")->setField("status",7);
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
            $res=db("car_dd")->where("did=$did")->setField("status",1);
            $pay=$re['pay'];
            $pays=\explode(",", $pay);
            foreach ($pays as $v){
                db("car_dd")->where("code='$v'")->setField("status",1);
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
        $username=\input('username');
        $addr=\input('addr');
        $phone=\input('phone');
        if($start || $code || $username || $addr || $phone){
            if($start){
                $arrdateone = strtotime($start);
                $arrdatetwo = strtotime($end . ' 23:55:55');
                $map['time'] = array(
                    array(
                        'egt',
                        $arrdateone
                    ),
                    array(
                        'elt',
                        $arrdatetwo
                    ),
                    'AND'
                );
            }
            if($code){
                $map['code']=array('like','%'.$code.'%');
            }
            if($username){
                $maps['username']=array('like','%'.$username.'%');
                $re=db("addr")->where($maps)->select();
           //     if($re){
                    $id=array();
                    foreach ($re as $v){
                        $id[]=$v['aid'];
                    }
                    $map['a_id']=array("in",$id);
            //    }else{
            //        $map=[];
            //    }
            }
            if($phone){
                $maps['phone']=array('like','%'.$phone.'%');
                $re=db("addr")->where($maps)->select();
           //     if($re){
                    $id=array();
                    foreach ($re as $v){
                        $id[]=$v['aid'];
                    }
                    $map['a_id']=array("in",$id);
           //     }else{
            //        $map=[];
            //    }
            }
            if($addr){
                $maps['addr|addrs']=array('like','%'.$addr.'%');
                $re=db("addr")->where($maps)->select();
            //    if($re){
                    $id=array();
                    foreach ($re as $v){
                        $id[]=$v['aid'];
                    }
                    $map['a_id']=array("in",$id);
            //    }else{
            //        $map=[];
             //   }
            }
        }else{
    
            $start="";
            $end="";
            $username="";
            $phone="";
            $addr="";
            $code="";
            $map=[];
        }
        $this->assign("start",$start);
        $this->assign("end",$end);
        $this->assign("username",$username);
        $this->assign("phone",$phone);
        $this->assign("addr",$addr);
        $this->assign("code",$code);
    
        $list=db("car_dd")->alias('a')->where("status=6 and gid=0")->where($map)->join("addr b","a.a_id = b.aid","LEFT")->order("did desc")->select();
        // var_dump($data);exit;
        vendor('PHPExcel.PHPExcel');//调用类库,路径是基于vendor文件夹的
        vendor('PHPExcel.PHPExcel.Worksheet.Drawing');
        vendor('PHPExcel.PHPExcel.Writer.Excel2007');
        $objExcel = new \PHPExcel();
        //set document Property
        $objWriter = \PHPExcel_IOFactory::createWriter($objExcel, 'Excel2007');
    
        $objActSheet = $objExcel->getActiveSheet();
        $key = ord("A");
        $letter =explode(',',"A,B,C,D,E,F,G,H");
        $arrHeader =  array("订单号","订单总金额","下单时间","收货人姓名","收货人电话","收货人地址","申请退货时间","退货原因");
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
            $objActSheet->setCellValue('G'.$k, \date("Y-m-d H:i:s",$v['tui_time']));
            $objActSheet->setCellValue('H'.$k, $v['tui_content']);
    
    
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
    
    public function outsy(){
        $start=input('start');
        $end=input('end');
        $code=\input('code');
        $username=\input('username');
        $addr=\input('addr');
        $phone=\input('phone');
        if($start || $code || $username || $addr || $phone){
            if($start){
                $arrdateone = strtotime($start);
                $arrdatetwo = strtotime($end . ' 23:55:55');
                $map['time'] = array(
                    array(
                        'egt',
                        $arrdateone
                    ),
                    array(
                        'elt',
                        $arrdatetwo
                    ),
                    'AND'
                );
            }
            if($code){
                $map['code']=array('like','%'.$code.'%');
            }
            if($username){
                $maps['username']=array('like','%'.$username.'%');
                $re=db("addr")->where($maps)->select();
              //  if($re){
                    $id=array();
                    foreach ($re as $v){
                        $id[]=$v['aid'];
                    }
                    $map['a_id']=array("in",$id);
             //   }else{
            //        $map=[];
             //   }
            }
            if($phone){
                $maps['phone']=array('like','%'.$phone.'%');
                $re=db("addr")->where($maps)->select();
           //     if($re){
                    $id=array();
                    foreach ($re as $v){
                        $id[]=$v['aid'];
                    }
                    $map['a_id']=array("in",$id);
            //    }else{
            //        $map=[];
            //    }
            }
            if($addr){
                $maps['addr|addrs']=array('like','%'.$addr.'%');
                $re=db("addr")->where($maps)->select();
            //    if($re){
                    $id=array();
                    foreach ($re as $v){
                        $id[]=$v['aid'];
                    }
                    $map['a_id']=array("in",$id);
            //    }else{
            //        $map=[];
            //    }
            }
        }else{
    
            $start="";
            $end="";
            $username="";
            $phone="";
            $addr="";
            $code="";
            $map=[];
        }
        $this->assign("start",$start);
        $this->assign("end",$end);
        $this->assign("username",$username);
        $this->assign("phone",$phone);
        $this->assign("addr",$addr);
        $this->assign("code",$code);
    
        $list=db("car_dd")->alias('a')->where("status=7 and gid=0")->where($map)->join("addr b","a.a_id = b.aid","LEFT")->order("did desc")->select();
        // var_dump($data);exit;
        vendor('PHPExcel.PHPExcel');//调用类库,路径是基于vendor文件夹的
        vendor('PHPExcel.PHPExcel.Worksheet.Drawing');
        vendor('PHPExcel.PHPExcel.Writer.Excel2007');
        $objExcel = new \PHPExcel();
        //set document Property
        $objWriter = \PHPExcel_IOFactory::createWriter($objExcel, 'Excel2007');
    
        $objActSheet = $objExcel->getActiveSheet();
        $key = ord("A");
        $letter =explode(',',"A,B,C,D,E,F,G,H");
        $arrHeader =  array("订单号","订单总金额","下单时间","收货人姓名","收货人电话","收货人地址","申请退货时间","退货原因");
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
            $objActSheet->setCellValue('G'.$k, \date("Y-m-d H:i:s",$v['tui_time']));
            $objActSheet->setCellValue('H'.$k, $v['tui_content']);
    
    
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