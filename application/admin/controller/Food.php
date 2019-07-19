<?php
namespace app\admin\controller;

use think\Db;

class Food extends BaseAdmin
{
    public function type()
    {
        $list = db('food_type')->order('id desc')->paginate(10);
        $this->assign("list",$list);

        $page=$list->render();

        $this->assign("page",$page);
        
        return $this->fetch();
    }
    public function save_type(){
        if($this->request->isAjax()){
            $id=input("id");
            if($id){
                $data['name']=input("name");
                $res=db("food_type")->where("id",$id)->update($data);
                if($res){
                    $this->success("修改成功！",url('type'));
                }else{
                    $this->error("修改失败！",url('type'));
                }
            }else{
                $data['name']=input("name");
                $re=db("food_type")->insert($data);
                if($re){
                    $this->success("添加成功！",url('type'));
                }else{
                    $this->error("添加失败！",url('type'));
                }
            }
    
        }else{
            $this->success("非法提交",url('type'));
        }
    }
    public function modify_type(){
        $id=input('id');
        $re=db('food_type')->where("id=$id")->find();
        echo json_encode($re);
    }
    function delete_type(){
        $id=input('id');
        db("food_type")->where("id",$id)->delete();
        $this->redirect('type');
    }
    public function lister()
    {
        
        $title=input("title");
        $map =[];
        if($title){
            $map['a.name']=["like","%".$title."%"];
        }

        $uid=session("uid");
        
        $admin=db("admin")->where("id",$uid)->find();

        if($admin['level'] == 2){
             $map['a.id']=['eq',$admin['shop_id']];
        }

        $this->assign("admin",$admin);

        $list=db("food")->alias("a")->field("a.*,b.name as cname,c.name as tname")->where($map)->join("spot_city b","a.cid = b.id")->join("food_type c","a.tid = c.id")->order(["a.sort asc","a.id desc"])->paginate(20,false,['query'=>request()->param()]);

        $this->assign("list",$list);

        $page=$list->render();

        $this->assign("page",$page);

        return $this->fetch();
    }
    public function add()
    {
        $res=db("spot_city")->where("status",1)->select();

        $this->assign("res",$res);

        $type=db("food_type")->select();

        $this->assign("type",$type);
        
        return $this->fetch();
    }
   
    public function save()
    {
        $data=input("post.");

        $image=request()->file("image");

        if($image){
            $data['image']=uploads("image");
        
        }
        $data['time']=time();
    
        $data['status']=1;


        $re=db("food")->insert($data);

        if($re){
            $this->success("发布成功",url("food/lister"));
        }else{
            $this->error("系统繁忙,请稍后再试");
        }
    }
    public function modifys()
    {
        $id=input("id");

        $re=db("food")->where(["status"=>1,"id"=>$id])->find();

        $this->assign("re",$re);

        $res=db("spot_city")->where("status",1)->select();

        $this->assign("res",$res);

        $type=db("food_type")->select();

        $this->assign("type",$type);

        return $this->fetch();
    }
    public function usave()
    {
        $id=input("id");

        $re=db("food")->where("id",$id)->find();

        if($re){

            $data=input("post.");

            $image=request()->file("image");

            if($image){

                $data['image']=uploads("image");

            }else{
                $data['image']=$re['image'];
                
            }
      


            $re=db("food")->where("id",$id)->update($data);

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
            db("food")->where(array('id' => $id ))->setField('sort' , $sort);
        }
        $this->redirect('lister');
    }
    public function delete()
    {
        $id=\input('id');
        $re=db("food")->where("id=$id")->find();
        if($re){
            $res=db("food")->where("id=$id")->delete();
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
            $re=db("food")->where("id",$v)->find();
            if($re){
                $res=db("food")->where("id",$v)->delete();
               
            }
        }
        $this->redirect('lister');
    }
    public function change()
    {
        $id=\input('id');
        $re=db("food")->where("id=$id")->find();
        if($re){
            if($re['status'] == 1){
                $res=db("food")->where("id=$id")->setField("status",0);
            }
           
            if($re['status'] == 0){
                $res=db("food")->where("id=$id")->setField("status",1);
            }

            echo '0';
        }else{
            echo '1';
        }
    }
    public function play()
    {
        $id=input("id");

        $list=db("food_hot")->where(["fid"=>$id])->paginate(20);

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
           $re=db('food_hot')->where("id",$id)->find();
           if(request()->file("image")){
               $data['image']=uploads("image");
               deleteImg($re['image']);
           }else{
               $data['image']=$re['image'];
           }
           
           $res=db('food_hot')->where("id",$id)->update($data);
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
           
            $re=db('food_hot')->insert($data);
            if($re){
                $this->success("添加成功！");
            }else{
                $this->error("添加失败！");
            } 
        }
         
    }
    public function modifys_play(){
        $id=input('id');
        $re=db('food_hot')->where("id",$id)->find();
        echo json_encode($re);
    }
    public function delete_play()
    {
        $id=\input('id');
        $re=db("food_hot")->where("id=$id")->find();
        if($re){
            $res=db("food_hot")->where("id=$id")->delete();
            if($res){
                echo '0';
            }else{
                echo '2';
            }
        }else{
            echo'1';
        }
    }
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
        
        // $list=db("order")->where("status=0 and type=1")->where($map)->order("id desc")->paginate(20,false,['query'=>request()->param()])->each(function($item, $key){
        //     $item['coupons'] = $item['coupon']+$item['only_coupon'];
        //     return $item;
        // });
        $list=db("order")->alias("a")->field("a.*,b.name as bname")->where("type=4")->where($map)->join("food b","a.shop_id=b.id")->order("a.id desc")->paginate(20,false,['query'=>request()->param()])->each(function($item, $key){
            $item['coupons'] = $item['coupon']+$item['only_coupon'];
            return $item;
        });
        $this->assign("list",$list);
        $page=$list->render();
        $this->assign("page",$page);

        $shop=db("food")->select();

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
        $list=db("order")->alias("a")->field("a.*,b.name as bname")->where("type=4")->where($map)->join("food b","a.shop_id=b.id")->order("a.id desc")->select();
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
        $arrHeader =  array("订单号","实付金额","美食名称","联系人","联系方式","店家");
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
       
           
        
            $objActSheet->setCellValue('D'.$k, $v['username']);
            $objActSheet->setCellValue('E'.$k, $v['phone']);
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
       

        if($start !=0 ){
             
            $times=($start."-".$end);
        }else{
            $times="";
        }
        $outfile = "$times"."美食订单".".xls";
    
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
            $data['shop_type']=5;
            $data['shop_id']=$re['shop_id'];
            $data['time']=time();
            $admin=db("admin")->where("shop_id",$re['shop_id'])->find();
            Db::startTrans();
            try{
                db("order")->where("id",$re['id'])->setField("status",2);
                db("admin_money")->insert($data);
                if($admin){
                    db("admin")->where(["shop_id"=>$re['shop_id'],"shop_type"=>5])->setInc("money",$money);
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





















}