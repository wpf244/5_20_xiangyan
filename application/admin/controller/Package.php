<?php
namespace app\admin\controller;

use think\Db;

class Package extends BaseAdmin
{
    public function lister()
    {
        
        $map=[];

        $title=input("title");

        if($title){
            $map['title|ticket']=["like","%".$title."%"];
        }
        $uid=session("uid");
       
        $admin=db("admin")->where("id",$uid)->find();

        if($admin['level'] == 2){
             $map['fid']=['eq',$admin['shop_id']];
        }

        $this->assign("admin",$admin);
        
        $list=db("package")->alias("a")->field("a.*,b.name")->where($map)->join("travel b","a.fid=b.id","left")->order(["sort asc","a.id desc"])->paginate(20,false,['query'=>request()->param()]);

        $this->assign("list",$list);

        $page=$list->render();

        $this->assign("page",$page);
        
        return $this->fetch();
    }
    public function add()
    {
        $res=db("travel")->order("id desc")->select();

        $this->assign("res",$res);

        $uid=session("uid");
       
        $admin=db("admin")->where("id",$uid)->find();

        $this->assign("admin",$admin);
        
        return $this->fetch();
    }
    public function save()
    {
        $data=input("post.");

        $image=request()->file("image");

        if($image){
            $data['image']=uploads('image');
        }
        $data['time']=time();

        $re=db("package")->insert($data);

        if($re){
            $this->success("保存成功",url('lister'));
        }else{
            $this->error("保存失败");
        }

    }
    public function modifys()
    {
        $id=input("id");

        $re=db("package")->where("id",$id)->find();

        $this->assign("re",$re);

        $res=db("travel")->order("id desc")->select();

        $this->assign("res",$res);
        
        return $this->fetch();
    }
    public function usave()
    {
        $id=input("id");
        $data=input("post.");

        $re=db("package")->where("id",$id)->find();

        if($re){
            $image=request()->file("image");

            if($image){
                $data['image']=uploads('image');
            }else{
                $data['image']=$re['image'];
            }
            $res=db("package")->where("id",$id)->update($data);

            if($res){
                $this->success("修改成功",url("lister"));
            }else{
                $this->error("修改失败",url("lister"));
            }

        }else{
            $this->error("参数错误",url("lister"));
        }

    }
    public function delete()
    {
        $id=\input('id');
        $re=db("package")->where("id=$id")->find();
        if($re){
            $res=db("package")->where("id=$id")->delete();
            if($res){
                echo '0';
            }else{
                echo '2';
            }
        }else{
            echo'1';
        }
    }
    public function sort(){
        $data=input('post.');
   
        foreach ($data as $id => $sort){
            db("package")->where(array('id' => $id ))->setField('sort' , $sort);
        }
        $this->redirect('lister');
    }
    public function delete_all()
    {
        $id=\input('id');
        $arr=\explode(",",$id);
        foreach($arr as $v){
            $re=db("package")->where("id",$v)->find();
            if($re){
                $res=db("package")->where("id",$v)->delete();
               
            }
        }
        $this->redirect('lister');
    }
    public function change()
    {
        $id=\input('id');
        $re=db("package")->where("id=$id")->find();
        if($re){
            if($re['status'] == 1){
                $res=db("package")->where("id=$id")->setField("status",0);
            }
           
            if($re['status'] == 0){
                $res=db("package")->where("id=$id")->setField("status",1);
            }

            echo '0';
        }else{
            echo '1';
        }
    }
    /**
    * 订单
    *
    * @return void
    */
    public function dd()
    {
    
        // $start=input('start');
        // $end=input('end');
        // $code=\input('code');
      
        // $addr=\input('addr');
       
        // if($start || $code ||  $addr){
        //     if($start){
                
        //         $map['time']=['between time',[$start.'00:00:01',$end.'23:59:59']];
        //     }
        //     if($code){
        //         $map['code']=array('like','%'.$code.'%');
        //     }
         
        //     if($addr){
        //         $map['username|phone']=array('like','%'.$addr.'%');
              

        //     }
        // }else{
            
        //     $start="";
        //     $end="";
        
        //     $addr="";
        //     $code="";
        //     $map=[];
        // }
        // $this->assign("start",$start);
        // $this->assign("end",$end);
      
        // $this->assign("addr",$addr);
        // $this->assign("code",$code);

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
        
        // $list=db("order")->where("status=0 and type=1")->where($map)->order("id desc")->paginate(20,false,['query'=>request()->param()])->each(function($item, $key){
        //     $item['coupons'] = $item['coupon']+$item['only_coupon'];
        //     return $item;
        // });
        $list=db("order")->alias("a")->field("a.*,b.name as bname")->where("type=1")->where($map)->join("travel b","a.shop_id=b.id")->order("a.id desc")->paginate(20,false,['query'=>request()->param()])->each(function($item, $key){
            $item['coupons'] = $item['coupon']+$item['only_coupon'];
            return $item;
        });
        $this->assign("list",$list);
        $page=$list->render();
        $this->assign("page",$page);

        $shop=db("travel")->select();

        $this->assign("shop",$shop);
        
        return $this->fetch();
    
    }
    public function out(){
        // $start=input('start');
        // $end=input('end');
        // $code=\input('code');
      
        // $addr=\input('addr');
       
        // if($start || $code ||  $addr){
        //     if($start){
                
        //         $map['time']=['between time',[$start.'00:00:01',$end.'23:59:59']];
        //     }
        //     if($code){
        //         $map['code']=array('like','%'.$code.'%');
        //     }
         
        //     if($addr){
        //         $map['username|phone']=array('like','%'.$addr.'%');
              

        //     }
        // }else{
            
        //     $map=[];
        // }
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
           

            $map['a.status']=['eq',0];
            
        }
        
        // $list=db("order")->where("status=0 and type=1")->where($map)->order("id desc")->select();
        $list=db("order")->alias("a")->field("a.*,b.name as bname")->where("type=1")->where($map)->join("travel b","a.shop_id=b.id")->order("a.id desc")->select();
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
        $arrHeader =  array("订单号","实付金额","门票名称","门票数量","联系人","联系方式","旅行社");
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
       
            $objActSheet->setCellValue('D'.$k, $v['num']);
        
            $objActSheet->setCellValue('E'.$k, $v['username']);
            $objActSheet->setCellValue('F'.$k, $v['phone']);
            $objActSheet->setCellValue('F'.$k, $v['bname']);
          
    
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
        $outfile = "$times"."团游订单".".xls";
    
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
            $data['shop_type']=2;
            $data['shop_id']=$re['shop_id'];
            $data['time']=time();
            $admin=db("admin")->where("shop_id",$re['shop_id'])->find();
            Db::startTrans();
            try{
                db("order")->where("id",$re['id'])->setField("status",2);
                db("admin_money")->insert($data);
                if($admin){
                    db("admin")->where(["shop_id"=>$re['shop_id'],"shop_type"=>2])->setInc("money",$money);
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
        
        $list=db("order")->where("status=1 and type=1")->where($map)->order("id desc")->paginate(20,false,['query'=>request()->param()])->each(function($item, $key){
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
        
        $list=db("order")->where("status=1 and type=1")->where($map)->order("id desc")->select();
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
        $arrHeader =  array("订单号","实付金额","门票名称","门票数量","联系人","联系方式");
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
       
            $objActSheet->setCellValue('D'.$k, $v['num']);
        
            $objActSheet->setCellValue('E'.$k, $v['username']);
            $objActSheet->setCellValue('F'.$k, $v['phone']);
          
    
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
        $outfile = "$times"."已付款团游订单".".xls";
    
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
        
        $list=db("order")->where("status=2 and type=1")->where($map)->order("id desc")->paginate(20,false,['query'=>request()->param()])->each(function($item, $key){
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
        
        $list=db("order")->where("status=2 and type=1")->where($map)->order("id desc")->select();
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
        $arrHeader =  array("订单号","实付金额","门票名称","门票数量","联系人","联系方式");
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
       
            $objActSheet->setCellValue('D'.$k, $v['num']);
        
            $objActSheet->setCellValue('E'.$k, $v['username']);
            $objActSheet->setCellValue('F'.$k, $v['phone']);
          
    
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
        $outfile = "$times"."已核销团游订单".".xls";
    
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
    public function travel()
    {
        $list=db("travel")->order("id desc")->select();

        $this->assign("list",$list);
        
        return $this->fetch();
    }
    public function save_t(){
        $id=input('id');
        $data=input("post.");
        if($id){
           
            $res=db('travel')->where("id",$id)->update($data);
            if($res){
                $this->success("修改成功");
            }else{
                $this->error("修改失败");
            }
        }else{
           
            $re=db('travel')->insert($data);
            if($re){
                $this->success("保存成功");
            }else{
                $this->error("保存失败");
            }
        }
    }
    public function modify_t(){
        $id=input('id');
        $re=db('travel')->where("id=$id")->find();
        
        echo json_encode($re);
    }
    public function delete_t(){
        $id=input('id');
       
        $del=db('travel')->where("id",$id)->delete();
        
        $this->redirect('travel');
          
    }

}