<?php
namespace app\index\controller;

class Pay extends BaseHome
{
    public function pay()
    {
        $id=input("did");

        $re=db("order")->where("id",$id)->find();

        $this->assign("re",$re);

       
        
        return $this->fetch();
    }
}