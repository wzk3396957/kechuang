<?php
namespace app\index\controller;

use think\Db;
use app\index\validate\Admin as adVali;

class Admin extends Base
{
    public function admin(){
        $list = Db::name("admin") ->alias("t1")
                                  ->join("med_role t2","t1.role_id=t2.id")
                                  ->where(["t1.is_del"=>0])
                                  ->field("t1.*,t2.role_name")
                                  ->select();
        $this ->assign("list",$list);
        return $this ->fetch();
    }

    public function addAdmin(){
        if($this->request->isPost()){
            $this ->doAddAdmin();
        }
        $role = Db::name("role") ->where(["status"=>1]) ->select();
        $this ->assign("role",$role);
        return $this ->fetch();
    }

    private function doAddAdmin(){
        $data = input("post.");
        $validate = new adVali();
        if (!$validate ->scene("add") ->check($data)) {
            $this ->error($validate->getError());
        }
        $data["password"] = md5(md5($data["password"]).config('app.salf'));
        $data["create_at"] = time();
        $res = Db::name("admin") ->insert($data);
        if($res){
            $this ->success("添加成功");
        }else{
            $this ->error("添加失败");
        }
    }

    public function updStatus(){
        $id = input("post.id");
        if(!is_numeric($id)){
            $this ->error("参数有误");
        }
        $status = Db::name("admin") ->where(["id"=>$id,"is_del"=>0]) ->value("status");
        if($status === 1){
            $status = 0;
        }elseif($status === 0){
            $status = 1;
        }else{
            $this ->error("用户获取失败");
        }
        $res = Db::name("admin") ->where(["id"=>$id]) ->update(["status"=>$status]);
        if($res){
            $this ->success("修改成功");
        }else{
            $this ->error("修改失败");
        }
    }

    public function delAdmin(){
        $id = input("post.id");
        if(!is_numeric($id)){
            $this ->error("参数有误");
        }
        $res = Db::name("admin") ->where(["id"=>$id]) ->update(["is_del"=>1]);
        if($res){
            $this ->success("删除成功");
        }else{
            $this ->error("删除失败");
        }
    }

    public function editAdmin(){
        if($this->request->isPost()){
            $this ->doEditAdmin();
        }
        $id = input("get.id");
        if(!is_numeric($id)){
            $this ->error("参数有误");
        }
        $data = Db::name("admin") ->where(["id"=>$id,"is_del"=>0]) ->find();
        if(empty($data)){
            $this ->error("用户获取失败");
        }
        $this ->assign("data",$data);
        $role = Db::name("role") ->where(["status"=>1]) ->select();
        $this ->assign("role",$role);
        return $this ->fetch();
    }

    private function doEditAdmin(){
        $data = input("post.");
        $validate = new adVali();
        if (!$validate ->scene("edit") ->check($data)) {
            $this ->error($validate->getError());
        }
        if(!isset($data["password"]) || empty($data["password"])){
            unset($data["password"]);
        }else{
            $data["password"] = md5(md5($data["password"]).config('app.salf'));
        }
        $res = Db::name("admin") ->where(["id"=>$data["id"]]) ->update($data);
        if($res !== false){
            $this ->success("修改成功");
        }else{
            $this ->error("修改失败");
        }
    }

}