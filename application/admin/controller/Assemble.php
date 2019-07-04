<?php
namespace app\admin\controller;

class Assemble extends BaseAdmin
{
    public function goods()
    {
        $title=\input("title");
        $map=[];
        if($title){
            $map['name']=["like","%".$title."%"];
        }else{
            
            $title='';
        }
        $uid=session("uid");

        $admin=db("admin")->where("id",$uid)->find();

        if($admin['level'] == 2){
             $map['a.shop_id']=['eq',$admin['id']];
        }

        $this->assign("admin",$admin);
        $this->assign("title",$title);
        $list=db('assemble_goods')->alias("a")->field("a.*,b.username")->where($map)->join("admin b","a.shop_id=b.id",'left')->order(['sort'=> 'asc','id'=>'desc'])->paginate(20,false,['query'=>request()->param()]);
        $this->assign("list",$list);
        
        $page=$list->render();
        $this->assign("page",$page);
        
        return $this->fetch();
    }
    public function add()
    {
        $uid=session("uid");
        
        $admin=db("admin")->where("id",$uid)->find();

        $this->assign("admin",$admin);

        $user=db("admin")->where("level",2)->select();

        $this->assign("user",$user);
        
        return $this->fetch();
    }
    public function save()
    {
        $data=input("post.");

        $image=request()->file("image");

        if($image){
            $data['image']=uploads("image");
        }
        $re=db("assemble_goods")->insert($data);

        if($re){
            $this->success("保存成功",url('goods'));
        }else{
            $this->error("保存失败");
        }

    }
    public function changeu(){
        $id=input('id');
        $re=db('assemble_goods')->where("id",$id)->find();
        if($re){
            if($re['up'] == 0){
                $res=db('assemble_goods')->where("id",$id)->setField("up",1);
            }
            if($re['up'] == 1){
                $res=db('assemble_goods')->where("id",$id)->setField("up",0);
    
            }
            echo '0';
        }else{
            echo '1';
        }
    }
    public function change(){
        $id=input('id');
        $re=db('assemble_goods')->where("id",$id)->find();
        if($re){
            if($re['status'] == 0){
                $res=db('assemble_goods')->where("id",$id)->setField("status",1);
            }
            if($re['status'] == 1){
                $res=db('assemble_goods')->where("id",$id)->setField("status",0);
    
            }
            echo '0';
        }else{
            echo '1';
        }
    }
    public function sort(){
        $data=input('post.');
    
        foreach ($data as $id => $sort){
            db('assemble_goods')->where(array('id' => $id ))->setField('sort' , $sort);
        }
        $this->redirect('goods');
    }
    public function modifys()
    {
        $id=input('id');
        $re=db('assemble_goods')->where("id",$id)->find();
        $this->assign("re",$re);

        $uid=session("uid");
        
        $admin=db("admin")->where("id",$uid)->find();

        $this->assign("admin",$admin);

        $user=db("admin")->where("level",2)->select();

        $this->assign("user",$user);
        
        return $this->fetch();
    }
    public function usave(){
        $id=input('id');
        $data=input('post.');
        $re=db("assemble_goods")->where("id",$id)->find();
        if($re){
            if(request()->file("image")){
                $data['image']=uploads('image');
              
            }else{
                $data['image']=$re['image'];
            }
        
           
            $res=db("assemble_goods")->where("id",$id)->update($data);
            if($res){
                $this->success("修改成功",url('goods'));
            }else{
                $this->error("修改失败");
            }
        }else{
            $this->error("参数错误");
        }
        
    }
    public function delete(){
        $id=input('id');
        $re=db("assemble_goods")->where("id",$id)->find();

        if($re){
            
            db("assemble_goods")->where("id",$id)->delete();

            $this->redirect('goods');

        }else{
            $this->redirect('goods');
        }
        
    }
    public function lister()
    {
        $title=\input("title");

        $status=input("status");
        $map=[];
     //   var_dump($status);
        if($title || $status ){
            if($title){
                $map['name']=["like","%".$title."%"];
            }else{
                $title='';
            }
            if($status){
                $map['a.status']=['eq',$status];
            }else{
                $status=0;
            }
            
        }else{
            
            $title='';
            $status=0;
            
        }
        $uid=session("uid");

        $admin=db("admin")->where("id",$uid)->find();

        if($admin['level'] == 2){
             $map['a.shop_id']=['eq',$admin['id']];
        }

        $this->assign("admin",$admin);
        $this->assign("title",$title);
        $this->assign("status",$status);
        $list=db('assemble')->alias("a")->field("a.*,b.phone,c.username")->where($map)->join("user b","a.uid=b.uid",'left')->join("admin c","a.shop_id=c.id",'left')->order(['a.id'=>'desc'])->paginate(20,false,['query'=>request()->param()]);
        $this->assign("list",$list);
        
        $page=$list->render();
        $this->assign("page",$page);
        
        return $this->fetch();
    }
    public function dd()
    {
        
        $start=input('start');
        $end=input('end');
        $code=\input('code');
      
        $addr=\input('addr');

        $status=input("status");
        $map=[];
        if($start || $code ||  $addr || $status || $status === '0'){
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
            if($status || $status === '0'){
                $map['status']=["eq",$status];
            }else{
                $status=10;
            }
        }else{
            
            $start="";
            $end="";
        
            $addr="";
            $code="";
            $status=10;
         
        }
        $uid=session("uid");

        $admin=db("admin")->where("id",$uid)->find();

        if($admin['level'] == 2){
             $map['a.shop_id']=['eq',$admin['id']];
        }
        $this->assign("start",$start);
        $this->assign("end",$end);
      
        $this->assign("addr",$addr);
        $this->assign("code",$code);
        $this->assign("status",$status);
        
        $list=db("assemble_dd")->alias('a')->field("a.*,b.*,c.username")->where($map)->join("addr b","a.aid = b.aid","LEFT")->join("admin c","a.shop_id=c.id")->order("a.id desc")->paginate(20,false,['query'=>request()->param()]);
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

        $status=input("status");
        $map=[];
        if($start || $code ||  $addr || $status || $status === '0'){
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
            if($status || $status === '0'){
                if($status == 10){
                    $map=[];
                }else{
                    $map['status']=["eq",$status];
                }
                
            }else{
                $status=10;
            }
        }
        $uid=session("uid");

        $admin=db("admin")->where("id",$uid)->find();

        if($admin['level'] == 2){
             $map['a.shop_id']=['eq',$admin['id']];
        }
        $list=db("assemble_dd")->alias('a')->where($map)->join("addr b","a.aid = b.aid","LEFT")->order("id desc")->select();
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
        $arrHeader =  array("商品名称","商品价格","订单号码","实际付款","收货人姓名","收货人电话","收货人地址","订单备注","订单状态");
        //填充表头信息
        $lenth =  count($arrHeader);
        for($i = 0;$i < $lenth;$i++) {
            $objActSheet->setCellValue("$letter[$i]1","$arrHeader[$i]");
        }
        //填充表格信息
        foreach($list as $k=>$v){
            if($v['status'] == 0){
                $v['type']="未付款";
            }
            if($v['status'] == 1){
                $v['type']="拼团中";
            }
            if($v['status'] == 2){
                $v['type']="待发货";
            }
            if($v['status'] == 3){
                $v['type']="待收货";
            }
            if($v['status'] == 4){
                $v['type']="已完成";
            }
            if($v['status'] == 5){
                $v['type']="拼团失败";
            }
            $k +=2;
            $objActSheet->setCellValue('A'.$k,$v['name']);
            $objActSheet->setCellValue('B'.$k, $v['goods_price']);    
            // 表格内容
            $objActSheet->setCellValue('C'.$k, $v['code']);
            $objActSheet->setCellValue('D'.$k, $v['price']);
            $objActSheet->setCellValue('E'.$k, $v['username']);
            $objActSheet->setCellValue('F'.$k, $v['phone']);
            $objActSheet->setCellValue('G'.$k, $v['addr'].$v['addrs']);
            $objActSheet->setCellValue('H'.$k, $v['content']);
            $objActSheet->setCellValue('I'.$k, $v['type']);
    
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

        if($start !=0 ){
             
            $times=($start."-".$end);
        }else{
            $times="";
        }
        if($status == 0){
            $name="未付款订单";
        }elseif($status == 1){
            $name="拼团中订单";
        }elseif($status == 2){
            $name="待发货订单";
        }elseif($status == 3){
            $name="待收货订单";
        }elseif($status == 4){
            $name="已完成订单";
        }elseif($status == 5){
            $name="拼团失败订单";
        }else{
            $name="全部订单";
        }

        $outfile = "$times"."$name".".xls";
    
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
    public function changes()
    {
        $id=\input('id');
        $re=db("assemble_dd")->where("id",$id)->find();
        if($re){
            $id=$re['id'];
            if($re['status'] == 0){
                $data['fu_time']=time();
                $data['status']=1;
                $changestatus=db("assemble_dd")->where("id=$id")->update($data);
                if($changestatus){
                    $bid=$re['a_id'];

                    $assemble=db("assemble")->where("id",$bid)->find();

                    if($assemble){
                        if($assemble['status'] == 0){
                            db("assemble")->where("id",$bid)->setField("status",1);
                        }
                        $num=$assemble['number']-$assemble['num'];
                        
                        if($re['lid'] != 0){
                            db("assemble_log")->where("id",$re['lid'])->setField("status",1);
                        }
                        if($num <= 1){
                            db("assemble")->where("id",$bid)->setInc("num",1);
                            db("assemble")->where("id",$bid)->setField("status",2);
                            $res=db("assemble_dd")->where(["a_id"=>$bid,"status"=>1])->select();
                            if($res){
                                db("assemble_dd")->where(["a_id"=>$bid,"status"=>1])->setField("status",2);
                            }
                            // if($re['lid'] != 0){
                            //     db("assemble")->where("id",$re['lid'])->setField("status",2);
                            // }
                        }else{
                            db("assemble")->where("id",$bid)->setInc("num",1);

                        }
                    }

                    echo '0';
                }else{
                    echo '1';
                }
            }else{
                echo '1';
            }
        }else{
            echo '1';
        }
    }
    public function delete_dd()
    {
        $id=\input('id');
        $re=db("assemble_dd")->where("id=$id")->find();
        if($re){
            $del=db("assemble_dd")->where("id=$id")->delete();
            
            echo '0';
            
        }else{
            echo '1';
        }
    }
    public function fa_dd()
    {
        $id=\input('id');
        $re=db("assemble_dd")->where("id",$id)->find();
        if($re){
           if($re['status'] == 2){
            db("assemble_dd")->where("id",$id)->setField("status",3);
            echo '0';
           } else{
               echo '2';
           }
           
        }else{
            echo '1';
        }
    }
    public function que_dd()
    {
        $id=\input('id');
        $re=db("assemble_dd")->where("id",$id)->find();
        if($re){
           if($re['status'] == 3){
            db("assemble_dd")->where("id",$id)->setField("status",4);
            echo '0';
           } else{
               echo '2';
           }
           
        }else{
            echo '1';
        }
    }
}