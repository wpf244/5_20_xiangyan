<?php
namespace app\admin\controller;

class Group extends BaseAdmin
{
    public function person()
    {
        
        $list=db("made_person")->where(["status"=>0])->order(["id desc"])->paginate(20,false,['query'=>request()->param()]);

        $this->assign("list",$list);

        $page=$list->render();

        $this->assign("page",$page);

        return $this->fetch();
    }
    public function look()
    {
        $id=input("id");

        $re=db("made_person")->where("id",$id)->find();

        $this->assign("re",$re);

        

        return $this->fetch();
    }
    public function change()
    {
        $id=\input('id');
        $re=db("made_person")->where("id=$id")->find();
        if($re){
            $res=db("made_person")->where("id=$id")->setField("status",1);
            $this->redirect('person');
        }else{
            $this->redirect('person');
        }
    }
    public function change_all()
    {
        $id=\input('id');
        $arr=\explode(",",$id);
        foreach($arr as $v){
            $re=db("made_person")->where("id",$v)->find();
            if($re){
                $res=db("made_person")->where("id",$v)->setField("status",1);
               
            }
        }
        $this->redirect('person');
    }
    public function delete()
    {
        $id=\input('id');
        $re=db("made_person")->where("id=$id")->find();
        if($re){
            $res=db("made_person")->where("id=$id")->delete();
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
            $re=db("made_person")->where("id",$v)->find();
            if($re){
                $res=db("made_person")->where("id",$v)->delete();
               
            }
        }
        $this->redirect('person');
    }
    public function delete_alls()
    {
        $id=\input('id');
        $arr=\explode(",",$id);
        foreach($arr as $v){
            $re=db("made_person")->where("id",$v)->find();
            if($re){
                $res=db("made_person")->where("id",$v)->delete();
               
            }
        }
        $this->redirect('persons');
    }
    public function persons()
    {
        
        $list=db("made_person")->where(["status"=>1])->order(["id desc"])->paginate(20,false,['query'=>request()->param()]);

        $this->assign("list",$list);

        $page=$list->render();

        $this->assign("page",$page);

        return $this->fetch();
    }
    public function team()
    {
        
        $list=db("made_team")->where(["status"=>0])->order(["id desc"])->paginate(20,false,['query'=>request()->param()]);

        $this->assign("list",$list);

        $page=$list->render();

        $this->assign("page",$page);

        return $this->fetch();
    }
    public function looks()
    {
        $id=input("id");

        $re=db("made_team")->where("id",$id)->find();

        $this->assign("re",$re);

        

        return $this->fetch();
    }
    public function change_team()
    {
        $id=\input('id');
        $re=db("made_team")->where("id=$id")->find();
        if($re){
            $res=db("made_team")->where("id=$id")->setField("status",1);
            $this->redirect('team');
        }else{
            $this->redirect('team');
        }
    }
    public function change_team_all()
    {
        $id=\input('id');
        $arr=\explode(",",$id);
        foreach($arr as $v){
            $re=db("made_team")->where("id",$v)->find();
            if($re){
                $res=db("made_team")->where("id",$v)->setField("status",1);
               
            }
        }
        $this->redirect('team');
    }
    public function delete_team()
    {
        $id=\input('id');
        $re=db("made_team")->where("id=$id")->find();
        if($re){
            $res=db("made_team")->where("id=$id")->delete();
            if($res){
                echo '0';
            }else{
                echo '2';
            }
        }else{
            echo'1';
        }
    }
    public function delete_team_all()
    {
        $id=\input('id');
        $arr=\explode(",",$id);
        foreach($arr as $v){
            $re=db("made_team")->where("id",$v)->find();
            if($re){
                $res=db("made_team")->where("id",$v)->delete();
               
            }
        }
        $this->redirect('team');
    }
    public function teams()
    {
        
        $list=db("made_team")->where(["status"=>1])->order(["id desc"])->paginate(20,false,['query'=>request()->param()]);

        $this->assign("list",$list);

        $page=$list->render();

        $this->assign("page",$page);

        return $this->fetch();
    }
    public function delete_team_alls()
    {
        $id=\input('id');
        $arr=\explode(",",$id);
        foreach($arr as $v){
            $re=db("made_team")->where("id",$v)->find();
            if($re){
                $res=db("made_team")->where("id",$v)->delete();
               
            }
        }
        $this->redirect('teams');
    }
}