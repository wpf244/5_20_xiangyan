<?php
namespace app\admin\controller;

class Package extends BaseAdmin
{
    public function lister()
    {
        
        $title=input("title");

        if($title){
            $map['title|ticket']=["like","%".$title."%"];
        }else{
            $map=[];
        }

        
        $list=db("package")->where($map)->order(["sort asc","id desc"])->paginate(20,false,['query'=>request()->param()]);

        $this->assign("list",$list);

        $page=$list->render();

        $this->assign("page",$page);
        
        return $this->fetch();
    }
    public function add()
    {
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
        
        $list=db("order")->where("status=0 and type=1")->where($map)->order("id desc")->paginate(20,false,['query'=>request()->param()])->each(function($item, $key){
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
        
        $list=db("order")->where("status=0 and type=1")->where($map)->order("id desc")->select();
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
        $outfile = "$times"."待付款团游订单".".xls";
    
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

}