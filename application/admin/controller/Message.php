<?php
namespace app\admin\controller;

class Message extends BaseAdmin
{
    public function lister()
    {
        $list=db("message")->order("id desc")->paginate(20);
        $this->assign("list",$list);
        return $this->fetch();
    }
    public function delete()
    {
        $id=input('id');
        $re=db("message")->where("id=$id")->find();
        if($re){
           $del=db("message")->where("id=$id")->delete();
           echo '0';
        }else{
            echo '1';
        }
    }
}