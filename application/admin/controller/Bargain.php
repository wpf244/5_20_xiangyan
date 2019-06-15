<?php
namespace app\admin\controller;

class Bargain extends BaseAdmin
{
    public function goods()
    {
        
        $title=\input("title");

        if($title){
            $map['name']=["like","%".$title."%"];
        }else{
            $map=[];
            $title='';
        }
        $this->assign("title",$title);
        $list=db('bargain_goods')->where($map)->order(['sort'=> 'asc','id'=>'desc'])->paginate(20,false,['query'=>request()->param()]);
        $this->assign("list",$list);
        
        $page=$list->render();
        $this->assign("page",$page);
        
        return $this->fetch();
    }
    public function add()
    {
        return $this->fetch();
    }
}