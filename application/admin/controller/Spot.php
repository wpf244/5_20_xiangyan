<?php
namespace app\admin\controller;

class Spot extends BaseAdmin
{
    public function city()
    {
        $list=db("spot_city")->order(["sort asc","id desc"])->paginate(20);

        $this->assign("list",$list);

        $page=$list->render();

        $this->assign("page",$page);

        return $this->fetch();
    }
    public function save_type(){
        $id=\input('id');
        if($id){
           $re=db('spot_city')->where("id",$id)->find();
           if(request()->file("image")){
               $data['image']=uploads("image");
               if($re['image']){
                deleteImg($re['image']);
               }
               
           }
        
 
           $data['name']=input('name');
          
           $res=db('spot_city')->where("id",$id)->update($data);
           if($res){
               $this->success("修改成功！");
           }else{
               $this->error("修改失败！");
           }
        }else{
            if(request()->file("image")){
                $data['image']=uploads("image");  
            }
        
            $data['name']=input('name');
           
            $re=db('spot_city')->insert($data);
            if($re){
                $this->success("添加成功！");
            }else{
                $this->error("添加失败！");
            } 
        }
         
    }
    public function change_type(){
        $id=input("id");

        $re=db("spot_city")->where("id",$id)->find();

        if($re){
            if($re['status'] == 0){
                $res=db("spot_city")->where("id",$id)->setField("status",1);
            }
            if($re['status'] == 1){
                $res=db("spot_city")->where("id",$id)->setField("status",0);
            }
            if($res){
                echo '1';
            }else{
                echo '0';
            }
        }else{
            echo '2';
        }  
    }
    public function modifys_type(){
        $id=input("id");
        $re=db('spot_city')->where("id=$id")->find();
        echo json_encode($re);
    }
    public function delete_type()
    {
        $id=\input('id');
        $re=db("spot_city")->where("id=$id")->find();
        if($re){
            $res=db("spot_city")->where("id=$id")->delete();
            $this->redirect('city');
        }else{
            $this->redirect('city');
        }
    }
    public function sort_type(){
        $data=input('post.');
   
        foreach ($data as $id => $sort){
            db("spot_city")->where(array('id' => $id ))->setField('sort' , $sort);
        }
        $this->redirect('city');
    }
    
    public function sever()
    {
        
        $list=db("spot_sever")->order(["id desc"])->paginate(20);

        $this->assign("list",$list);

        $page=$list->render();

        $this->assign("page",$page);

        return $this->fetch();
    }
    public function save_sever(){
        $id=\input('id');
        if($id){
           $re=db('spot_sever')->where("id",$id)->find();
           if(request()->file("image")){
               $data['sever_image']=uploads("image");
               if($re['sever_image']){
                deleteImg($re['sever_image']);
               }
               
           }
        
 
           $data['sever_name']=input('name');
          
           $res=db('spot_sever')->where("id",$id)->update($data);
           if($res){
               $this->success("修改成功！");
           }else{
               $this->error("修改失败！");
           }
        }else{
            if(request()->file("image")){
                $data['sever_image']=uploads("image");  
            }
        
            $data['sever_name']=input('name');
           
            $re=db('spot_sever')->insert($data);
            if($re){
                $this->success("添加成功！");
            }else{
                $this->error("添加失败！");
            } 
        }
         
    }
    public function modifys_sever(){
        $id=input("id");
        $re=db('spot_sever')->where("id=$id")->find();
        echo json_encode($re);
    }
    public function delete_sever()
    {
        $id=\input('id');
        $re=db("spot_sever")->where("id=$id")->find();
        if($re){
            $res=db("spot_sever")->where("id=$id")->delete();
            $this->redirect('sever');
        }else{
            $this->redirect('sever');
        }
    }
    public function lister()
    {

        $title=input("title");

        if($title){
            $map['a.name']=["like","%".$title."%"];
        }else{
            $map =[];
        }

        $list=db("spot")->alias("a")->field("a.*,b.name as cname")->where($map)->join("spot_city b","a.cid = b.id")->order(["a.sort asc","a.id desc"])->paginate(20,false,['query'=>request()->param()]);

        $this->assign("list",$list);

        $page=$list->render();

        $this->assign("page",$page);

        return $this->fetch();
    }
    public function add()
    {
        $res=db("spot_city")->where("status",1)->select();

        $this->assign("res",$res);

        $sever=db("spot_sever")->select();

        $this->assign("sever",$sever);
        
        return $this->fetch();
    }
    public function save()
    {
        $data=input("post.");

        $image=request()->file("image");

        if($image){
            $data['image']=uploads("image");
        
        }
        $banner=request()->file("banner");

        if($banner){
            $data['banner']=uploads("banner");
        
        }

        $sever=$data['severs'];
     //   var_dump($sever);exit;

        $data['severs']=implode(",",$sever);

       $data['time']=\time();

        $re=db("spot")->insert($data);

        if($re){
            $this->success("保存成功",url("lister"));
        }else{
            $this->error("保存失败");
        }
    }
    public function modifys()
    {
        $id=input("id");

        $re=db("spot")->where(["status"=>1,"id"=>$id])->find();

        $this->assign("re",$re);

        $severs=explode(",",$re['severs']);

        $this->assign("severs",$severs);

        $res=db("spot_city")->where("status",1)->select();

        $this->assign("res",$res);

        $sever=db("spot_sever")->select();

        $this->assign("sever",$sever);

        return $this->fetch();
    }
    public function usave()
    {
        $id=input("id");
        $re=db("spot")->where("id",$id)->find();

        if($re){

            $data=input("post.");

            $image=request()->file("image");

            if($image){
                $data['image']=uploads("image");

                deleteImg($re['image']);
            
            }else{
                $data['image']=$re['image'];
                
            }
            $banner=request()->file("banner");

            if($banner){

                $data['banner']=uploads("banner");

                deleteImg($re['banner']);
            
            }else{
                $data['banner']=$re['banner'];
            }

            $sever=$data['severs'];
        //   var_dump($sever);exit;

            $data['severs']=implode(",",$sever);


            $re=db("spot")->where("id",$id)->update($data);

            if($re){
                $this->success("修改成功",url("lister"));
            }else{
                $this->error("修改失败");
            }

        }else{
            $this->error("非法操作",url("lister"));
        }
    }
    public function sort(){
        $data=input('post.');
   
        foreach ($data as $id => $sort){
            db("spot")->where(array('id' => $id ))->setField('sort' , $sort);
        }
        $this->redirect('lister');
    }
    public function change()
    {
        $id=\input('id');
        $re=db("spot")->where("id=$id")->find();
        if($re){
            if($re['status'] == 1){
                $res=db("spot")->where("id=$id")->setField("status",0);
            }
           
            if($re['status'] == 0){
                $res=db("spot")->where("id=$id")->setField("status",1);
            }

            echo '0';
        }else{
            echo '1';
        }
    }
    public function changes()
    {
        $id=\input('id');
        $re=db("spot")->where("id=$id")->find();
        if($re){
            if($re['recome'] == 1){
                $res=db("spot")->where("id=$id")->setField("recome",0);
            }
           
            if($re['recome'] == 0){
                $res=db("spot")->where("id=$id")->setField("recome",1);
            }

            echo '0';
        }else{
            echo '1';
        }
    }
    public function delete()
    {
        $id=\input('id');
        $re=db("spot")->where("id=$id")->find();
        if($re){
            $res=db("spot")->where("id=$id")->delete();
            if($res){
                echo '0';
            }else{
                echo '2';
            }
        }else{
            echo'1';
        }
    }
    public function delete_all()
    {
        $id=\input('id');
        $arr=\explode(",",$id);
        foreach($arr as $v){
            $re=db("spot")->where("id",$v)->find();
            if($re){
                $res=db("spot")->where("id",$v)->delete();
               
            }
        }
        $this->redirect('lister');
    }
    public function stretagy()
    {
        
        $id=input("id");

        $re=db("spot_gl")->where("sid",$id)->find();

        $this->assign("re",$re);

        $this->assign("id",$id);

        return $this->fetch();
    }
    public function save_s()
    {
        $data=input("post.");

        $image=request()->file("s_image");

       

        $id=input("sid");

        $re=db("spot_gl")->where("sid",$id)->find();

        if($re){
            if($image){
                $data['s_image']=uploads("s_image");
            }else{
                $data['s_image']=$re['s_image'];
            }
            $res=db("spot_gl")->where("sid",$id)->update($data);

            if($res){
                $this->success("修改成功");
            }else{
                $this->error("修改失败");
            }
        }else{
            if($image){
                $data['s_image']=uploads("s_image");
            }
            $rea=db("spot_gl")->insert($data);
            if($rea){
                $this->success("修改成功");
            }else{
                $this->error("修改失败");
            }
        }

    }
    /**
    * 玩转景区
    *
    * @return void
    */
    public function play()
    {
        $id=input("id");

        $list=db("spot_play")->where(["sid"=>$id])->paginate(20);

        $this->assign("list",$list);

        $page=$list->render();

        $this->assign("page",$page);

        $this->assign("id",$id);
        
        return $this->fetch();
    }
    public function save_play(){
        $id=\input('id');
        if($id){
            $data=input("post.");
           $re=db('spot_play')->where("id",$id)->find();
           if(request()->file("image")){
               $data['image']=uploads("image");
               deleteImg($re['image']);
           }else{
               $data['image']=$re['image'];
           }
           
           $res=db('spot_play')->where("id",$id)->update($data);
           if($res){
               $this->success("修改成功！");
           }else{
               $this->error("修改失败！");
           }
        }else{
            $data=input("post.");
            if(request()->file("image")){
                $data['image']=uploads("image");
               
            }
           
            $re=db('spot_play')->insert($data);
            if($re){
                $this->success("添加成功！");
            }else{
                $this->error("添加失败！");
            } 
        }
         
    }
    public function modifys_play(){
        $id=input('id');
        $re=db('spot_play')->where("id",$id)->find();
        echo json_encode($re);
    }
    public function delete_play()
    {
        $id=\input('id');
        $re=db("spot_play")->where("id=$id")->find();
        if($re){
            $res=db("spot_play")->where("id=$id")->delete();
            if($res){
                echo '0';
            }else{
                echo '2';
            }
        }else{
            echo'1';
        }
    }
    public function ticket()
    {
        
        $id=input("id");

        $list=db("spot_ticket")->where(["sid"=>$id])->paginate(20);

        $this->assign("list",$list);

        $page=$list->render();

        $this->assign("page",$page);

        $this->assign("id",$id);

        return $this->fetch();
    }
    public function save_ticket(){
        $id=\input('id');
        if($id){

            $data=input("post.");
         
           $res=db('spot_ticket')->where("id",$id)->update($data);
           if($res){
               $this->success("修改成功！");
           }else{
               $this->error("修改失败！");
           }
        }else{
            $data=input("post.");
            
            $re=db('spot_ticket')->insert($data);
            if($re){
                $this->success("添加成功！");
            }else{
                $this->error("添加失败！");
            } 
        }
         
    }
    public function modifys_ticket(){
        $id=input('id');
        $re=db('spot_ticket')->where("id",$id)->find();
        echo json_encode($re);
    }
    /**
    * 订单
    *
    * @return void
    */
    public function dd()
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
                $map['username|phone']=array('like','%'.$addr.'%');
              

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
        
        $list=db("order")->where("status=0 and type=2")->where($map)->order("id desc")->paginate(20,false,['query'=>request()->param()])->each(function($item, $key){
            $item['coupons'] = $item['coupon']+$item['only_coupon'];
            return $item;
        });
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
                $map['username|phone']=array('like','%'.$addr.'%');
              

            }
        }else{
            
            $map=[];
        }
        
        $list=db("order")->where("status=0 and type=2")->where($map)->order("id desc")->select();
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
        $arrHeader =  array("订单号","实付金额","门票名称","游玩日期","归来日期","门票数量","联系人","联系方式");
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
          
            $objActSheet->setCellValue('C'.$k, $v['name']);
            $objActSheet->setCellValue('D'.$k, $v['start_time']);
            $objActSheet->setCellValue('E'.$k, $v['end_time']);
            $objActSheet->setCellValue('F'.$k, $v['num']);
        
            $objActSheet->setCellValue('G'.$k, $v['username']);
            $objActSheet->setCellValue('H'.$k, $v['phone']);
          
    
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
        $objActSheet->getColumnDimension('H')->setWidth(25);
       

        if($start !=0 ){
             
            $times=($start."-".$end);
        }else{
            $times="";
        }
        $outfile = "$times"."待付款门票订单".".xls";
    
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
    public function delete_dd()
    {
        $id=\input('id');
        $re=db("order")->where("id=$id")->find();
        if($re){
            $del=db("order")->where("id=$id")->delete();
            echo '0';
        }else{
            echo '1';
        }
    }
    public function change_dd()
    {
        $id=\input('id');
        $re=db("order")->where("id=$id")->find();
        if($re){
            db("order")->where("id=$id")->setField("status",1);
            echo '0';
        }else{
            echo '1';
        }
    }
    public function change_dds()
    {
        $id=\input('id');
        $re=db("order")->where("id=$id")->find();
        if($re){
            db("order")->where("id=$id")->setField("status",2);
            echo '0';
        }else{
            echo '1';
        }
    }
    public function dds()
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
                $map['username|phone']=array('like','%'.$addr.'%');
              

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
        
        $list=db("order")->where("status=1 and type=2")->where($map)->order("id desc")->paginate(20,false,['query'=>request()->param()])->each(function($item, $key){
            $item['coupons'] = $item['coupon']+$item['only_coupon'];
            return $item;
        });
        $this->assign("list",$list);
        $page=$list->render();
        $this->assign("page",$page);
        
        return $this->fetch();
    
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
                $map['username|phone']=array('like','%'.$addr.'%');
              

            }
        }else{
            
            $map=[];
        }
        
        $list=db("order")->where("status=1 and type=2")->where($map)->order("id desc")->select();
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
        $arrHeader =  array("订单号","实付金额","门票名称","游玩日期","归来日期","门票数量","联系人","联系方式");
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
          
            $objActSheet->setCellValue('C'.$k, $v['name']);
            $objActSheet->setCellValue('D'.$k, $v['start_time']);
            $objActSheet->setCellValue('E'.$k, $v['end_time']);
            $objActSheet->setCellValue('F'.$k, $v['num']);
        
            $objActSheet->setCellValue('G'.$k, $v['username']);
            $objActSheet->setCellValue('H'.$k, $v['phone']);
          
    
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
        $objActSheet->getColumnDimension('H')->setWidth(25);

        if($start !=0 ){
             
            $times=($start."-".$end);
        }else{
            $times="";
        }
        $outfile = "$times"."已付款门票订单".".xls";
    
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
    public function ddh()
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
                $map['username|phone']=array('like','%'.$addr.'%');
              

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
        
        $list=db("order")->where("status=2 and type=2")->where($map)->order("id desc")->paginate(20,false,['query'=>request()->param()])->each(function($item, $key){
            $item['coupons'] = $item['coupon']+$item['only_coupon'];
            return $item;
        });
        $this->assign("list",$list);
        $page=$list->render();
        $this->assign("page",$page);
        
        return $this->fetch();
    
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
                $map['username|phone']=array('like','%'.$addr.'%');
              

            }
        }else{
            
            $map=[];
        }
        
        $list=db("order")->where("status=2 and type=2")->where($map)->order("id desc")->select();
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
        $arrHeader =  array("订单号","实付金额","门票名称","游玩日期","归来日期","门票数量","联系人","联系方式");
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
          
            $objActSheet->setCellValue('C'.$k, $v['name']);
            $objActSheet->setCellValue('D'.$k, $v['start_time']);
            $objActSheet->setCellValue('E'.$k, $v['end_time']);
            $objActSheet->setCellValue('F'.$k, $v['num']);
        
            $objActSheet->setCellValue('G'.$k, $v['username']);
            $objActSheet->setCellValue('H'.$k, $v['phone']);
          
    
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
        $objActSheet->getColumnDimension('H')->setWidth(25);

        if($start !=0 ){
             
            $times=($start."-".$end);
        }else{
            $times="";
        }
        $outfile = "$times"."已核销门票订单".".xls";
    
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
    












}