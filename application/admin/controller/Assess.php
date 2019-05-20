<?php
namespace app\admin\controller;

use think\Request;
class Assess extends BaseAdmin
{
    public function lister()
    {
        $list=\db("assess")->alias('a')->field("a.id,a.content,name,addtime")->where("a.status=0")->join("riders b","a.rid=b.id")->paginate(10);
        $this->assign("list",$list);
        
        $page=$list->render();
        $this->assign("page",$page);
        
        return \view('lister');
    }
    public function change()
    {
        $id=\input('id');
        $re=db("assess")->where("id=$id")->find();
        if($re){
            $res=db("assess")->where("id=$id")->setField("status",1);
            $this->redirect('lister');
        }else{
            $this->redirect('lister');
        }
    }
    public function change_all()
    {
        $id=\input('id');
        $arr=\explode(",",$id);
        foreach($arr as $v){
            $re=db("assess")->where("id",$v)->find();
            if($re){
                $res=db("assess")->where("id",$v)->setField("status",1);
               
            }
        }
        $this->redirect('lister');
    }
    public function delete()
    {
        $id=\input('id');
        $re=db("assess")->where("id=$id")->find();
        if($re){
            $res=db("assess")->where("id=$id")->delete();
            $this->redirect('lister');
        }else{
            $this->redirect('lister');
        }
    }
    public function delete_all()
    {
        $id=\input('id');
        $arr=\explode(",",$id);
        foreach($arr as $v){
            $re=db("assess")->where("id",$v)->find();
            if($re){
                $res=db("assess")->where("id",$v)->delete();
               
            }
        }
        $this->redirect('lister');
    }
    public function index()
    {
        $start = Request::instance()->param('start', '');
        $end = Request::instance()->param('end', '');
        $g_name = Request::instance()->param('g_name', '');
        $map = [];
        if($g_name != ''){
            $map['name'] = array('like', '%'.$g_name.'%');
        }
        if($start != '' && $end != ''){
            $map['create_time'] = array(array('egt',strtotime($start)),array('elt',strtotime($end.' 23:55:55')),'AND');
        }elseif($start == '' && $end != ''){
            $map['create_time'] = array('elt',strtotime($end.' 23:55:55'));
        }elseif($start != '' && $end == ''){
            $map['create_time'] = array('egt',strtotime($start));
        }
        $list=\db("assess")->alias('a')->field("a.id,a.content,name,addtime")->where($map)->where("a.status=1")->join("riders b","a.rid=b.id")->paginate(10);
        $this->assign("list",$list);
        
        $this->assign("start",$start);
        $this->assign("end",$end);
        $this->assign("g_name",$g_name);
    
        $page=$list->render();
        $this->assign("page",$page);
    
        return \view('index');
    }
    public function deletes()
    {
        $id=\input('id');
        $re=db("assess")->where("id=$id")->find();
        if($re){
            $res=db("assess")->where("id=$id")->delete();
            $this->redirect('index');
        }else{
            $this->redirect('index');
        }
    }
    
    
    
    
    
    
    
}