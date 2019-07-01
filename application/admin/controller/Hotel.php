<?php
namespace app\admin\controller;

use think\Db;

class Hotel extends BaseAdmin
{
    public function lister()
    {
        
        $title=input("title");

        $map=[];

        $uid=session("uid");
        
        $admin=db("admin")->where("id",$uid)->find();

        if($admin['level'] == 2){
             $map['id']=['eq',$admin['shop_id']];
        }

        $this->assign("admin",$admin);

        if($title){
            $map['name']=["like","%".$title."%"];
        }
        
        $list=db("hotel")->where($map)->order(["sort asc","id desc"])->paginate(20,false,['query'=>request()->param()]);

        $this->assign("list",$list);

        $page=$list->render();

        $this->assign("page",$page);

        return $this->fetch();
    }
    public function coupon()
    {
        $re=db("coupon")->where("id",1)->find();

        $this->assign("re",$re);
        
        return $this->fetch();
    }
    public function save_coupon()
    {
        $data=input("post.");

        $res=db("coupon")->where("id",1)->update($data);

        if($res){
            $this->success("修改成功");
        }else{
            $this->error("修改失败");
        }
    }
    public function sever()
    {
        
        $list=db("hotel_sever")->order(["id desc"])->paginate(20);

        $this->assign("list",$list);

        $page=$list->render();

        $this->assign("page",$page);

        return $this->fetch();
    }
    
    public function save_sever(){
        $id=\input('id');
        if($id){
           $re=db('hotel_sever')->where("id",$id)->find();
           if(request()->file("image")){
               $data['sever_image']=uploads("image");
               if($re['sever_image']){
                deleteImg($re['sever_image']);
               }
               
           }
        
 
           $data['sever_name']=input('name');
          
           $res=db('hotel_sever')->where("id",$id)->update($data);
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
           
            $re=db('hotel_sever')->insert($data);
            if($re){
                $this->success("添加成功！");
            }else{
                $this->error("添加失败！");
            } 
        }
         
    }
    public function add()
    {
        $sever=db("hotel_sever")->select();

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
       

        $sever=$data['severs'];
    

        $data['severs']=implode(",",$sever);

   

        $re=db("hotel")->insert($data);

        if($re){
            $this->success("保存成功",url("lister"));
        }else{
            $this->error("保存失败");
        }
    }
    public function modifys()
    {
        $id=input("id");

        $re=db("hotel")->where(["status"=>1,"id"=>$id])->find();

        $this->assign("re",$re);

        $severs=explode(",",$re['severs']);

        $this->assign("severs",$severs);

     

        $sever=db("hotel_sever")->select();

        $this->assign("sever",$sever);

        return $this->fetch();
    }
    public function usave()
    {
        $id=input("id");
        $re=db("hotel")->where("id",$id)->find();

        if($re){

            $data=input("post.");

            $image=request()->file("image");

            if($image){
                $data['image']=uploads("image");

                deleteImg($re['image']);
            
            }else{
                $data['image']=$re['image'];
                
            }
           
            $sever=$data['severs'];
        //   var_dump($sever);exit;

            $data['severs']=implode(",",$sever);


            $re=db("hotel")->where("id",$id)->update($data);

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
            db("hotel")->where(array('id' => $id ))->setField('sort' , $sort);
        }
        $this->redirect('lister');
    }
    public function change()
    {
        $id=\input('id');
        $re=db("hotel")->where("id=$id")->find();
        if($re){
            if($re['status'] == 1){
                $res=db("hotel")->where("id=$id")->setField("status",0);
            }
           
            if($re['status'] == 0){
                $res=db("hotel")->where("id=$id")->setField("status",1);
            }

            echo '0';
        }else{
            echo '1';
        }
    }
    public function changes()
    {
        $id=\input('id');
        $re=db("hotel")->where("id=$id")->find();
        if($re){
            if($re['recome'] == 1){
                $res=db("hotel")->where("id=$id")->setField("recome",0);
            }
           
            if($re['recome'] == 0){
                $res=db("hotel")->where("id=$id")->setField("recome",1);
            }

            echo '0';
        }else{
            echo '1';
        }
    }
    public function delete()
    {
        $id=\input('id');
        $re=db("hotel")->where("id=$id")->find();
        if($re){
            $res=db("hotel")->where("id=$id")->delete();
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
            $re=db("hotel")->where("id",$v)->find();
            if($re){
                $res=db("hotel")->where("id",$v)->delete();
               
            }
        }
        $this->redirect('lister');
    }
    public function banner()
    {
        $id=input("id");

        $list=db("hotel_banner")->where(["hid"=>$id])->paginate(20);

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
           $re=db('hotel_banner')->where("id",$id)->find();
           if(request()->file("image")){
               $data['image']=uploads("image");
               deleteImg($re['image']);
           }else{
               $data['image']=$re['image'];
           }
           
           $res=db('hotel_banner')->where("id",$id)->update($data);
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
           
            $re=db('hotel_banner')->insert($data);
            if($re){
                $this->success("添加成功！");
            }else{
                $this->error("添加失败！");
            } 
        }
         
    }
    public function modifys_play(){
        $id=input('id');
        $re=db('hotel_banner')->where("id",$id)->find();
        echo json_encode($re);
    }
    public function delete_play()
    {
        $id=\input('id');
        $re=db("hotel_banner")->where("id=$id")->find();
        if($re){
            $res=db("hotel_banner")->where("id=$id")->delete();
            if($res){
                echo '0';
            }else{
                echo '2';
            }
        }else{
            echo'1';
        }
    }
    /**
    * 酒店客房
    *
    * @return void
    */
    public function room()
    {
        $id=input("id");

        $list=db("hotel_room")->where(["hid"=>$id])->paginate(20);

        $this->assign("list",$list);

        $page=$list->render();

        $this->assign("page",$page);

        $this->assign("id",$id);
        
        return $this->fetch();
    }
    public function save_room(){
        $id=\input('id');
        if($id){
            $data=input("post.");
           $re=db('hotel_room')->where("id",$id)->find();
           if(request()->file("image")){
               $data['image']=uploads("image");
               deleteImg($re['image']);
           }else{
               $data['image']=$re['image'];
           }
           
           $res=db('hotel_room')->where("id",$id)->update($data);
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
           
            $re=db('hotel_room')->insert($data);
            if($re){
                $this->success("添加成功！");
            }else{
                $this->error("添加失败！");
            } 
        }
         
    }
    public function modifys_room(){
        $id=input('id');
        $re=db('hotel_room')->where("id",$id)->find();
        echo json_encode($re);
    }
    public function delete_room()
    {
        $id=\input('id');
        $re=db("hotel_room")->where("id=$id")->find();
        if($re){
            $res=db("hotel_room")->where("id=$id")->delete();
            if($res){
                echo '0';
            }else{
                echo '2';
            }
        }else{
            echo'1';
        }
    }
    public function change_room()
    {
        $id=\input('id');
        $re=db("hotel_room")->where("id=$id")->find();
        if($re){
            if($re['status'] == 1){
                $res=db("hotel_room")->where("id=$id")->setField("status",0);
            }
           
            if($re['status'] == 0){
                $res=db("hotel_room")->where("id=$id")->setField("status",1);
            }

            echo '0';
        }else{
            echo '1';
        }
    }
    public function change_rooms()
    {
        $id=\input('id');
        $re=db("hotel_room")->where("id=$id")->find();
        if($re){
            if($re['cancel'] == 1){
                $res=db("hotel")->where("id=$id")->setField("cancel",0);
            }
           
            if($re['cancel'] == 0){
                $res=db("hotel_room")->where("id=$id")->setField("cancel",1);
            }

            echo '0';
        }else{
            echo '1';
        }
    }
    public function only()
    {
        $list=db("hotel_coupon")->alias("a")->field("a.*,b.name")->join("hotel b","a.hid=b.id")->order(["a.sort asc","a.id desc"])->paginate(20);

        $this->assign("list",$list);

        $page=$list->render();

        $this->assign("page",$page);

        $res=db("hotel")->where("status",1)->select();

        $this->assign("res",$res);
        
        return $this->fetch();
    }
    public function save_only(){
        $id=\input('id');
        if($id){
            $data=input("post.");
          
           
           $res=db('hotel_coupon')->where("id",$id)->update($data);
           if($res){
               $this->success("修改成功！");
           }else{
               $this->error("修改失败！");
           }
        }else{
            $data=input("post.");
           
           
            $re=db('hotel_coupon')->insert($data);
            if($re){
                $this->success("添加成功！");
            }else{
                $this->error("添加失败！");
            } 
        }
         
    }
    public function modifys_only(){
        $id=input('id');
        $re=db('hotel_coupon')->where("id",$id)->find();
        echo json_encode($re);
    }
    public function change_only()
    {
        $id=\input('id');
        $re=db("hotel_coupon")->where("id=$id")->find();
        if($re){
            if($re['status'] == 1){
                $res=db("hotel_coupon")->where("id=$id")->setField("status",0);
            }
           
            if($re['status'] == 0){
                $res=db("hotel_coupon")->where("id=$id")->setField("status",1);
            }

            echo '0';
        }else{
            echo '1';
        }
    }

    public function delete_only()
    {
        $id=\input('id');
        $re=db("hotel_coupon")->where("id=$id")->find();
        if($re){
            $res=db("hotel_coupon")->where("id=$id")->delete();
            if($res){
                echo '0';
            }else{
                echo '2';
            }
        }else{
            echo'1';
        }
    }
    public function sort_only(){
        $data=input('post.');
   
        foreach ($data as $id => $sort){
            db("hotel_coupon")->where(array('id' => $id ))->setField('sort' , $sort);
        }
        $this->redirect('only');
    }
    /**
    * 订单
    *
    * @return void
    */
    public function dd()
    {
    
        $map=[];
        $start=input('start');
        $end=input('end');
        $code=\input('code');
      
        $addr=\input('addr');

        $status=input("status");

        $shop_id=input("shop_id");

        $uid=session("uid");
       
        $admin=db("admin")->where("id",$uid)->find();

        if($admin['level'] == 2){
             $map['shop_id']=['eq',$admin['shop_id']];
        }else{
            if($shop_id){
                $map['shop_id']=['eq',$shop_id];
            }
        }

        $this->assign("admin",$admin);
        
        if($start || $code ||  $addr || $status){
            if($start){
                
                $map['a.time']=['between time',[$start.'00:00:01',$end.'23:59:59']];
            }
            if($code){
                $map['code']=array('like','%'.$code.'%');
            }
         
            if($addr){
                $map['username|phone']=array('like','%'.$addr.'%');
              
            }
            if($status){
                $map['a.status']=['eq',$status];
            }
        }else{
            
            $start="";
            $end="";
        
            $addr="";
            $code="";

            $status=0;

            $map['a.status']=['eq',0];
            
        }
        $this->assign("start",$start);
        $this->assign("end",$end);
      
        $this->assign("addr",$addr);
        $this->assign("code",$code);

        $this->assign("status",$status);

        $this->assign("shop_id",$shop_id);
        
        // $list=db("order")->where("status=0 and type=3")->where($map)->order("id desc")->paginate(20,false,['query'=>request()->param()])->each(function($item, $key){
        //     $item['coupons'] = $item['coupon']+$item['only_coupon'];
        //     return $item;
        // });

        $list=db("order")->alias("a")->field("a.*,b.name as bname")->where("a.type=3")->where($map)->join("hotel b","a.shop_id=b.id")->order("a.id desc")->paginate(20,false,['query'=>request()->param()])->each(function($item, $key){
            $item['coupons'] = $item['coupon']+$item['only_coupon'];
            return $item;
        });

        $this->assign("list",$list);
        $page=$list->render();
        $this->assign("page",$page);

        $shop=db("hotel")->where("status",1)->select();

        $this->assign("shop",$shop);
        
        return $this->fetch();
    
    }
    public function out(){
        $map=[];
        $start=input('start');
        $end=input('end');
        $code=\input('code');
      
        $addr=\input('addr');

        $status=input("status");

        $shop_id=input("shop_id");

        $uid=session("uid");
       
        $admin=db("admin")->where("id",$uid)->find();

        if($admin['level'] == 2){
             $map['shop_id']=['eq',$admin['shop_id']];
        }else{
            if($shop_id){
                $map['shop_id']=['eq',$shop_id];
            }
        }

        $this->assign("admin",$admin);
        
        if($start || $code ||  $addr || $status){
            if($start){
                
                $map['a.time']=['between time',[$start.'00:00:01',$end.'23:59:59']];
            }
            if($code){
                $map['code']=array('like','%'.$code.'%');
            }
         
            if($addr){
                $map['username|phone']=array('like','%'.$addr.'%');
              
            }
            if($status){
                $map['a.status']=['eq',$status];
            }
        }else{
            
            $start="";
            $end="";
        
            $addr="";
            $code="";

            $status=0;

            $map['a.status']=['eq',0];
            
        }
        
        $list=db("order")->alias("a")->field("a.*,b.name as bname")->where("a.type=3")->where($map)->join("hotel b","a.shop_id=b.id")->order("a.id desc")->select();
        // var_dump($data);exit;
        vendor('PHPExcel.PHPExcel');//调用类库,路径是基于vendor文件夹的
        vendor('PHPExcel.PHPExcel.Worksheet.Drawing');
        vendor('PHPExcel.PHPExcel.Writer.Excel2007');
        $objExcel = new \PHPExcel();
        //set document Property
        $objWriter = \PHPExcel_IOFactory::createWriter($objExcel, 'Excel2007');
    
        $objActSheet = $objExcel->getActiveSheet();
        $key = ord("A");
        $letter =explode(',',"A,B,C,D,E,F,G,H,I,J,K,L");
        $arrHeader =  array("订单号","实付金额","酒店名称","房间名称","入住时间","离开时间","房间数量","预定天数","入住人","联系方式","优惠券","订单金额");
        //填充表头信息
        $lenth =  count($arrHeader);
        for($i = 0;$i < $lenth;$i++) {
            $objActSheet->setCellValue("$letter[$i]1","$arrHeader[$i]");
        }
        //填充表格信息
        foreach($list as $k=>$v){
            $k +=2;
            $v['coupons']=$v['coupon']+$v['only_coupon'];
            $objActSheet->setCellValue('A'.$k,$v['code']);
            $objActSheet->setCellValue('B'.$k, $v['price']);    
            // 表格内容
            $objActSheet->setCellValue('C'.$k, $v['hotel_name']);
            $objActSheet->setCellValue('D'.$k, $v['name']);
            $objActSheet->setCellValue('E'.$k, $v['start_time']);
            $objActSheet->setCellValue('F'.$k, $v['end_time']);
            $objActSheet->setCellValue('G'.$k, $v['num']);
            $objActSheet->setCellValue('H'.$k, $v['days']);
            $objActSheet->setCellValue('I'.$k, $v['username']);
            $objActSheet->setCellValue('J'.$k, $v['phone']);
            $objActSheet->setCellValue('K'.$k, $v['coupons']);
            $objActSheet->setCellValue('L'.$k, $v['old_price']);
    
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
        $objActSheet->getColumnDimension('I')->setWidth(25);
        $objActSheet->getColumnDimension('J')->setWidth(25);
        $objActSheet->getColumnDimension('K')->setWidth(30);
        $objActSheet->getColumnDimension('L')->setWidth(30);

        if($start !=0 ){
             
            $times=($start."-".$end);
        }else{
            $times="";
        }
        $outfile = "$times"."酒店订单".".xls";
    
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
        $code=input("code");
        $re=db("order")->where(["id"=>$id,"time"=>$code])->find();
        if($re){
            $money=$re['price'];
            $data['money']=$money;
            $data['did']=$re['id'];
            $data['shop_type']=3;
            $data['shop_id']=$re['shop_id'];
            $data['time']=time();
            $admin=db("admin")->where("shop_id",$re['shop_id'])->find();
            Db::startTrans();
            try{
                db("order")->where("id",$re['id'])->setField("status",2);
                db("admin_money")->insert($data);
                if($admin){
                    db("admin")->where(["shop_id"=>$re['shop_id'],"shop_type"=>3])->setInc("money",$money);
                }
                
                 echo '0';
                // 提交事务
                Db::commit();    
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();

                echo '1';
            }
            
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
        
        $list=db("order")->where("status=1 and type=3")->where($map)->order("id desc")->paginate(20,false,['query'=>request()->param()])->each(function($item, $key){
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
        
        $list=db("order")->where("status=1 and type=3")->where($map)->order("id desc")->select();
        // var_dump($data);exit;
        vendor('PHPExcel.PHPExcel');//调用类库,路径是基于vendor文件夹的
        vendor('PHPExcel.PHPExcel.Worksheet.Drawing');
        vendor('PHPExcel.PHPExcel.Writer.Excel2007');
        $objExcel = new \PHPExcel();
        //set document Property
        $objWriter = \PHPExcel_IOFactory::createWriter($objExcel, 'Excel2007');
    
        $objActSheet = $objExcel->getActiveSheet();
        $key = ord("A");
        $letter =explode(',',"A,B,C,D,E,F,G,H,I,J,K,L");
        $arrHeader =  array("订单号","实付金额","酒店名称","房间名称","入住时间","离开时间","房间数量","预定天数","入住人","联系方式","优惠券","订单金额");
        //填充表头信息
        $lenth =  count($arrHeader);
        for($i = 0;$i < $lenth;$i++) {
            $objActSheet->setCellValue("$letter[$i]1","$arrHeader[$i]");
        }
        //填充表格信息
        foreach($list as $k=>$v){
            $k +=2;
            $v['coupons']=$v['coupon']+$v['only_coupon'];
            $objActSheet->setCellValue('A'.$k,$v['code']);
            $objActSheet->setCellValue('B'.$k, $v['price']);    
            // 表格内容
            $objActSheet->setCellValue('C'.$k, $v['hotel_name']);
            $objActSheet->setCellValue('D'.$k, $v['name']);
            $objActSheet->setCellValue('E'.$k, $v['start_time']);
            $objActSheet->setCellValue('F'.$k, $v['end_time']);
            $objActSheet->setCellValue('G'.$k, $v['num']);
            $objActSheet->setCellValue('H'.$k, $v['days']);
            $objActSheet->setCellValue('I'.$k, $v['username']);
            $objActSheet->setCellValue('J'.$k, $v['phone']);
            $objActSheet->setCellValue('K'.$k, $v['coupons']);
            $objActSheet->setCellValue('L'.$k, $v['old_price']);
    
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
        $objActSheet->getColumnDimension('I')->setWidth(25);
        $objActSheet->getColumnDimension('J')->setWidth(25);
        $objActSheet->getColumnDimension('K')->setWidth(30);
        $objActSheet->getColumnDimension('L')->setWidth(30);

        if($start !=0 ){
             
            $times=($start."-".$end);
        }else{
            $times="";
        }
        $outfile = "$times"."已付款酒店订单".".xls";
    
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
        
        $list=db("order")->where("status=2 and type=3")->where($map)->order("id desc")->paginate(20,false,['query'=>request()->param()])->each(function($item, $key){
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
        
        $list=db("order")->where("status=2 and type=3")->where($map)->order("id desc")->select();
        // var_dump($data);exit;
        vendor('PHPExcel.PHPExcel');//调用类库,路径是基于vendor文件夹的
        vendor('PHPExcel.PHPExcel.Worksheet.Drawing');
        vendor('PHPExcel.PHPExcel.Writer.Excel2007');
        $objExcel = new \PHPExcel();
        //set document Property
        $objWriter = \PHPExcel_IOFactory::createWriter($objExcel, 'Excel2007');
    
        $objActSheet = $objExcel->getActiveSheet();
        $key = ord("A");
        $letter =explode(',',"A,B,C,D,E,F,G,H,I,J,K,L");
        $arrHeader =  array("订单号","实付金额","酒店名称","房间名称","入住时间","离开时间","房间数量","预定天数","入住人","联系方式","优惠券","订单金额");
        //填充表头信息
        $lenth =  count($arrHeader);
        for($i = 0;$i < $lenth;$i++) {
            $objActSheet->setCellValue("$letter[$i]1","$arrHeader[$i]");
        }
        //填充表格信息
        foreach($list as $k=>$v){
            $k +=2;
            $v['coupons']=$v['coupon']+$v['only_coupon'];
            $objActSheet->setCellValue('A'.$k,$v['code']);
            $objActSheet->setCellValue('B'.$k, $v['price']);    
            // 表格内容
            $objActSheet->setCellValue('C'.$k, $v['hotel_name']);
            $objActSheet->setCellValue('D'.$k, $v['name']);
            $objActSheet->setCellValue('E'.$k, $v['start_time']);
            $objActSheet->setCellValue('F'.$k, $v['end_time']);
            $objActSheet->setCellValue('G'.$k, $v['num']);
            $objActSheet->setCellValue('H'.$k, $v['days']);
            $objActSheet->setCellValue('I'.$k, $v['username']);
            $objActSheet->setCellValue('J'.$k, $v['phone']);
            $objActSheet->setCellValue('K'.$k, $v['coupons']);
            $objActSheet->setCellValue('L'.$k, $v['old_price']);
    
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
        $objActSheet->getColumnDimension('I')->setWidth(25);
        $objActSheet->getColumnDimension('J')->setWidth(25);
        $objActSheet->getColumnDimension('K')->setWidth(30);
        $objActSheet->getColumnDimension('L')->setWidth(30);

        if($start !=0 ){
             
            $times=($start."-".$end);
        }else{
            $times="";
        }
        $outfile = "$times"."已核销酒店订单".".xls";
    
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