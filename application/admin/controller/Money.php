<?php
namespace app\admin\controller;

use think\Db;

class Money extends BaseAdmin
{
    public function lister()
    {
        
        $uid=session("uid");
        $list=db("money_log")->where("shopid",$uid)->order("mid desc")->paginate(20);
        $this->assign("list",$list);
        $page=$list->render();
        $this->assign("page",$page);

        $shop=db("admin")->where("id",$uid)->find();
        $this->assign("shop",$shop);

        $ti=db("cash_s")->where("id",1)->find();
        $this->assign("ti",$ti);
        
        return $this->fetch();
    }
    public function saves()
    {
        $uid=session("uid");
        $shop=db("admin")->where("id",$uid)->find();
        $money=$shop['money'];
        $moneys=input("money");
        $ti=db("cash_s")->where("id",1)->find();
        $fei=$ti['num'];
        if($money < $moneys){
            $this->success("账户余额不足");
        }else{
            $data['shopid']=$uid;
            $data['moneys']=$moneys;
            $data['proce']=$moneys*$fei/100;
            $data['money']=$moneys-$data['proce'];
            $data['content']=input("content");
            $data['time']=time();

            // 启动事务
            Db::startTrans();
            try{
                db("money_log")->insert($data);
                db("admin")->where("id",$uid)->setDec("money",$moneys);
                
               
                // 提交事务
                Db::commit();    

                
            } catch (\Exception $e) {

             
                // 回滚事务
                Db::rollback();

                $this->error("系统繁忙");
            }

            $this->success("提交成功,等待审核");
        }
    }
}