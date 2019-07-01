<?php
namespace app\admin\controller;

class Integ extends BaseAdmin
{
    public function lister()
    {
        $title=\input("title");

        $map=[];

        $title=\input("title");

        $uid=session("uid");
       
        $admin=db("admin")->where("id",$uid)->find();

        if($admin['level'] == 2){
             $map['shop_id']=['eq',$admin['shop_id']];
        }

        $this->assign("admin",$admin);

        if($title){
            $map['name']=["like","%".$title."%"];
        }
        $this->assign("title",$title);
        $list=db('integ_goods')->alias("a")->where($map)->join("goods_shop b","a.shop_id=b.sid",'left')->order(['sort'=> 'asc','a.id'=>'desc'])->paginate(20,false,['query'=>request()->param()]);
        $this->assign("list",$list);
        
        $page=$list->render();
        $this->assign("page",$page);

        $this->assign("list",$list);
        
        return $this->fetch();
    }
    public function add()
    {
        $shop=db("goods_shop")->select();

        $this->assign("shop",$shop);
        
        return $this->fetch();
    }
    public function save(){
        $data=input('post.');
        if(request()->file("image")){
            $data['image']=uploads('image');

        }
       
        $re=db("integ_goods")->insert($data);
        if($re){
            $this->success("添加成功",url("lister"));
        }else{
            $this->error("添加失败");
        }
    }
    public function changeu(){
        $id=input('id');
        $re=db('integ_goods')->where("id",$id)->find();
        if($re){
            if($re['up'] == 0){
                $res=db('integ_goods')->where("id",$id)->setField("up",1);
            }
            if($re['up'] == 1){
                $res=db('integ_goods')->where("id",$id)->setField("up",0);
    
            }
            echo '0';
        }else{
            echo '1';
        }
    }
    public function sort(){
        $data=input('post.');
    
        foreach ($data as $id => $sort){
            db('integ_goods')->where(array('id' => $id ))->setField('sort' , $sort);
        }
        $this->redirect('lister');
    }
    public function modifys(){
        $id=input('id');
        $re=db('integ_goods')->where("id",$id)->find();
        $this->assign("re",$re);
      
        $shop=db("goods_shop")->select();

        $this->assign("shop",$shop);
        return $this->fetch();
    }
    public function usave(){
        $id=input('id');
        $data=input('post.');
        $re=db("integ_goods")->where("id",$id)->find();

        if($re){
            if(request()->file("image")){
                $data['image']=uploads('image');
              
            }else{
                $data['image']=$re['image'];
            }
        
          
            $res=db("integ_goods")->where("id",$id)->update($data);
            if($res){
                $this->success("修改成功",url('lister'));
            }else{
                $this->error("修改失败");
            }
        }else{
            $this->error("参数错误");
        }
        
    }
    public function delete()
    {
        $id=\input('id');
        $re=db("integ_goods")->where("id=$id")->find();
        if($re){
            $res=db("integ_goods")->where("id=$id")->delete();
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
            $re=db("integ_goods")->where("id",$v)->find();
            if($re){
                $res=db("integ_goods")->where("id",$v)->delete();
               
            }
        }
        $this->redirect('lister');
    }
    public function index()
    {
        $re=db("integ")->where("id",1)->find();
        $this->assign("re",$re);
        
        return $this->fetch();
    }
    public function save_integ()
    {
        $data=input("post.");

        $res=db("integ")->where("id",1)->update($data);

        if($res){
            $this->success("修改成功");
        }else{
            $this->error("修改失败");
        }
    }
    public function dd()
    {
        $map=[];

        $start=input('start');
        $end=input('end');
        $code=\input('code');
      
        $addr=\input('addr');

        $uid=session("uid");

        $shop_id=input("shop_id");
       
        $admin=db("admin")->where("id",$uid)->find();

        if($admin['level'] == 2){
             $map['shop_id']=['eq',$admin['shop_id']];
        }else{
            if($shop_id){
                $map['shop_id']=['eq',$shop_id];
            }
        }
       
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
           
        }
        $this->assign("start",$start);
        $this->assign("end",$end);
      
        $this->assign("addr",$addr);
        $this->assign("code",$code);

        $this->assign("shop_id",$shop_id);
    
        $list=db("integ_dd")->alias('a')->where("status=0")->where($map)->join("addr b","a.aid = b.aid","LEFT")->join("goods_shop c","a.shop_id=c.sid")->order("id desc")->paginate(20,false,['query'=>request()->param()]);
        $this->assign("list",$list);
        $page=$list->render();
        $this->assign("page",$page);

        $shop=db("goods_shop")->select();

        $this->assign("shop",$shop);
    
        return $this->fetch();
    }
    public function out(){
        $map=[];

        $start=input('start');
        $end=input('end');
        $code=\input('code');
      
        $addr=\input('addr');

        $uid=session("uid");

        $shop_id=input("shop_id");
       
        $admin=db("admin")->where("id",$uid)->find();

        if($admin['level'] == 2){
             $map['shop_id']=['eq',$admin['shop_id']];
        }else{
            if($shop_id){
                $map['shop_id']=['eq',$shop_id];
            }
        }
       
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
           
        }
    
        $list=db("integ_dd")->alias('a')->where("status=0")->where($map)->join("addr b","a.aid = b.aid","LEFT")->join("goods_shop c","a.shop_id=c.sid")->order("id desc")->select();
        // var_dump($data);exit;
        vendor('PHPExcel.PHPExcel');//调用类库,路径是基于vendor文件夹的
        vendor('PHPExcel.PHPExcel.Worksheet.Drawing');
        vendor('PHPExcel.PHPExcel.Writer.Excel2007');
        $objExcel = new \PHPExcel();
        //set document Property
        $objWriter = \PHPExcel_IOFactory::createWriter($objExcel, 'Excel2007');
    
        $objActSheet = $objExcel->getActiveSheet();
        $key = ord("A");
        $letter =explode(',',"A,B,C,D,E,F,G,H,I");
        $arrHeader =  array("订单号","商品名称","积分","兑换时间","订单备注","收货人姓名","收货人电话","收货人地址","商户名称");
        //填充表头信息
        $lenth =  count($arrHeader);
        for($i = 0;$i < $lenth;$i++) {
            $objActSheet->setCellValue("$letter[$i]1","$arrHeader[$i]");
        }
        //填充表格信息
        foreach($list as $k=>$v){
            $k +=2;
            $objActSheet->setCellValue('A'.$k,$v['code']);
            $objActSheet->setCellValue('B'.$k, $v['name']);
            // 表格内容
            $objActSheet->setCellValue('C'.$k, $v['integ']);
            $objActSheet->setCellValue('D'.$k, \date("Y-m-d H:i:s",$v['time']));
            $objActSheet->setCellValue('E'.$k, $v['content']);
            $objActSheet->setCellValue('F'.$k, $v['username']);
            $objActSheet->setCellValue('G'.$k, $v['phone']);
            $objActSheet->setCellValue('H'.$k, $v['addr'].$v['addrs']);
            $objActSheet->setCellValue('I'.$k, $v['sname']);
           
            
    
    
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
        if($start !=0 ){
             
            $times=($start."-".$end);
        }else{
            $times="";
        }
        $outfile = "$times"."积分兑换订单".".xls";
    
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
    public function tong()
    {
        $did=\input('id');
        $re=db("integ_dd")->where("id=$did")->find();
        if($re['status'] == 0){
            $res=db("integ_dd")->where("id=$did")->setField("status",1);
            
            $this->redirect("dd");
        }else{
            $this->redirect("dd");
        }
    }
    public function bo()
    {
        $did=\input('id');
        $re=db("integ_dd")->where("id=$did")->find();
        if($re['status'] == 0){
            
            $uid=$re['uid'];
            $integ=$re['integ'];

            $data['uid']=$uid;
            $data['integ']=$integ;
            $data['type']=1;
            $data['content']="系统返还";
            $data['time']=time();

            db("user")->where("uid",$uid)->setInc("integ",$integ);

            db("integ_log")->insert($data);

            $res=db("integ_dd")->where("id=$did")->setField("status",2);
           
            $this->redirect("dd");
        }else{
            $this->redirect("dd");
        }
    }
    public function delete_dd()
    {
        $did=\input('id');
        $re=db("integ_dd")->where("id=$did")->find();
        if($re['status'] == 0){
            
            $uid=$re['uid'];
            $integ=$re['integ'];

            $data['uid']=$uid;
            $data['integ']=$integ;
            $data['type']=1;
            $data['content']="系统返还";
            $data['time']=time();

            db("user")->where("uid",$uid)->setInc("integ",$integ);

            db("integ_log")->insert($data);

            $res=db("integ_dd")->where("id=$did")->delete();
           
            echo '0';
        }else{
            echo '1';
        }
    }
    public function delete_dds()
    {
        $did=\input('id');
        $re=db("integ_dd")->where("id=$did")->find();
        if($re){
            

            $res=db("integ_dd")->where("id=$did")->delete();
           
            echo '0';
        }else{
            echo '1';
        }
    }
    public function wan()
    {
        $map=[];

        $start=input('start');
        $end=input('end');
        $code=\input('code');
      
        $addr=\input('addr');

        $uid=session("uid");

        $shop_id=input("shop_id");
       
        $admin=db("admin")->where("id",$uid)->find();

        if($admin['level'] == 2){
             $map['shop_id']=['eq',$admin['shop_id']];
        }else{
            if($shop_id){
                $map['shop_id']=['eq',$shop_id];
            }
        }
       
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
           
        }
        $this->assign("start",$start);
        $this->assign("end",$end);
      
        $this->assign("addr",$addr);
        $this->assign("code",$code);

        $this->assign("shop_id",$shop_id);
    
        $list=db("integ_dd")->alias('a')->where("status=1")->where($map)->join("addr b","a.aid = b.aid","LEFT")->join("goods_shop c","a.shop_id=c.sid")->order("id desc")->paginate(20,false,['query'=>request()->param()]);
        $this->assign("list",$list);
        $page=$list->render();
        $this->assign("page",$page);

        $shop=db("goods_shop")->select();

        $this->assign("shop",$shop);
    
        return $this->fetch();
    }
    public function outw(){
        $map=[];

        $start=input('start');
        $end=input('end');
        $code=\input('code');
      
        $addr=\input('addr');

        $uid=session("uid");

        $shop_id=input("shop_id");
       
        $admin=db("admin")->where("id",$uid)->find();

        if($admin['level'] == 2){
             $map['shop_id']=['eq',$admin['shop_id']];
        }else{
            if($shop_id){
                $map['shop_id']=['eq',$shop_id];
            }
        }
       
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
           
        }
    
        $list=db("integ_dd")->alias('a')->where("status=1")->where($map)->join("addr b","a.aid = b.aid","LEFT")->join("goods_shop c","a.shop_id=c.sid")->order("id desc")->select();
        // var_dump($data);exit;
        vendor('PHPExcel.PHPExcel');//调用类库,路径是基于vendor文件夹的
        vendor('PHPExcel.PHPExcel.Worksheet.Drawing');
        vendor('PHPExcel.PHPExcel.Writer.Excel2007');
        $objExcel = new \PHPExcel();
        //set document Property
        $objWriter = \PHPExcel_IOFactory::createWriter($objExcel, 'Excel2007');
    
        $objActSheet = $objExcel->getActiveSheet();
        $key = ord("A");
        $letter =explode(',',"A,B,C,D,E,F,G,H,I");
        $arrHeader =  array("订单号","商品名称","积分","兑换时间","订单备注","收货人姓名","收货人电话","收货人地址","商户名称");
        //填充表头信息
        $lenth =  count($arrHeader);
        for($i = 0;$i < $lenth;$i++) {
            $objActSheet->setCellValue("$letter[$i]1","$arrHeader[$i]");
        }
        //填充表格信息
        foreach($list as $k=>$v){
            $k +=2;
            $objActSheet->setCellValue('A'.$k,$v['code']);
            $objActSheet->setCellValue('B'.$k, $v['name']);
            // 表格内容
            $objActSheet->setCellValue('C'.$k, $v['integ']);
            $objActSheet->setCellValue('D'.$k, \date("Y-m-d H:i:s",$v['time']));
            $objActSheet->setCellValue('E'.$k, $v['content']);
            $objActSheet->setCellValue('F'.$k, $v['username']);
            $objActSheet->setCellValue('G'.$k, $v['phone']);
            $objActSheet->setCellValue('H'.$k, $v['addr'].$v['addrs']);
            $objActSheet->setCellValue('I'.$k, $v['sname']);
           
            
    
    
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
    public function bo_dd()
    {
        $map=[];

        $start=input('start');
        $end=input('end');
        $code=\input('code');
      
        $addr=\input('addr');

        $uid=session("uid");

        $shop_id=input("shop_id");
       
        $admin=db("admin")->where("id",$uid)->find();

        if($admin['level'] == 2){
             $map['shop_id']=['eq',$admin['shop_id']];
        }else{
            if($shop_id){
                $map['shop_id']=['eq',$shop_id];
            }
        }
       
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
           
        }
        $this->assign("start",$start);
        $this->assign("end",$end);
      
        $this->assign("addr",$addr);
        $this->assign("code",$code);

        $this->assign("shop_id",$shop_id);
    
        $list=db("integ_dd")->alias('a')->where("status=2")->where($map)->join("addr b","a.aid = b.aid","LEFT")->join("goods_shop c","a.shop_id=c.sid")->order("id desc")->paginate(20,false,['query'=>request()->param()]);
        $this->assign("list",$list);
        $page=$list->render();
        $this->assign("page",$page);

        $shop=db("goods_shop")->select();

        $this->assign("shop",$shop);
    
        return $this->fetch();
    }
    public function outb(){
        $map=[];

        $start=input('start');
        $end=input('end');
        $code=\input('code');
      
        $addr=\input('addr');

        $uid=session("uid");

        $shop_id=input("shop_id");
       
        $admin=db("admin")->where("id",$uid)->find();

        if($admin['level'] == 2){
             $map['shop_id']=['eq',$admin['shop_id']];
        }else{
            if($shop_id){
                $map['shop_id']=['eq',$shop_id];
            }
        }
       
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
           
        }
    
        $list=db("integ_dd")->alias('a')->where("status=2")->where($map)->join("addr b","a.aid = b.aid","LEFT")->join("goods_shop c","a.shop_id=c.sid")->order("id desc")->select();
        // var_dump($data);exit;
        vendor('PHPExcel.PHPExcel');//调用类库,路径是基于vendor文件夹的
        vendor('PHPExcel.PHPExcel.Worksheet.Drawing');
        vendor('PHPExcel.PHPExcel.Writer.Excel2007');
        $objExcel = new \PHPExcel();
        //set document Property
        $objWriter = \PHPExcel_IOFactory::createWriter($objExcel, 'Excel2007');
    
        $objActSheet = $objExcel->getActiveSheet();
        $key = ord("A");
        $letter =explode(',',"A,B,C,D,E,F,G,H,I");
        $arrHeader =  array("订单号","商品名称","积分","兑换时间","订单备注","收货人姓名","收货人电话","收货人地址","商户名称");
        //填充表头信息
        $lenth =  count($arrHeader);
        for($i = 0;$i < $lenth;$i++) {
            $objActSheet->setCellValue("$letter[$i]1","$arrHeader[$i]");
        }
        //填充表格信息
        foreach($list as $k=>$v){
            $k +=2;
            $objActSheet->setCellValue('A'.$k,$v['code']);
            $objActSheet->setCellValue('B'.$k, $v['name']);
            // 表格内容
            $objActSheet->setCellValue('C'.$k, $v['integ']);
            $objActSheet->setCellValue('D'.$k, \date("Y-m-d H:i:s",$v['time']));
            $objActSheet->setCellValue('E'.$k, $v['content']);
            $objActSheet->setCellValue('F'.$k, $v['username']);
            $objActSheet->setCellValue('G'.$k, $v['phone']);
            $objActSheet->setCellValue('H'.$k, $v['addr'].$v['addrs']);
            $objActSheet->setCellValue('I'.$k, $v['sname']);
           
            
    
    
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
}