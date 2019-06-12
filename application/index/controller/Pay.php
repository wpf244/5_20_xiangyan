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
    public function pay_dd()
    {
        $id=input("did");

        $re=db("car_dd")->where("did",$id)->find();

        $this->assign("re",$re);

       
        
        return $this->fetch();
    }
}