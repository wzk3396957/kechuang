<?php
namespace app\index\controller;

use think\Db;
use app\index\validate\Role as roVali;

class Role extends Base
{
    public function role(){
        $list = Db::name("role") ->where(["is_del"=>0]) ->select();
        $this ->assign("list",$list);
        return $this ->fetch();
    }

    public function addRole(){
        if($this->request->isPost()){
            $this ->doAddRole();
        }
        $auth = Db::name("auth") ->where(["is_del"=>0,"type" =>1])
            ->where("pid","neq",0)
            ->select();
        foreach($auth as &$v){
            $v["son"] = Db::name("auth") ->where(["is_del"=>0,"pid"=>$v["id"]]) ->select();
        }
        $this ->assign("auth",$auth);
        return $this ->fetch();
    }

    private function doAddRole(){
        $data = input("post.");
        if(!isset($data["auth_id_list"])){
            $this ->error("权限必选");
        }
        $data["auth_id_list"] = implode(",", $data["auth_id_list"]);
        $validate = new roVali();
        if (!$validate ->check($data)) {
            $this ->error($validate->getError());
        }
        //$data["create_at"] = time();
        $res = Db::name("role") ->insert($data);
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
        $status = Db::name("role") ->where(["id"=>$id]) ->value("status");
        if($status === 1){
            $status = 0;
        }elseif($status === 0){
            $status = 1;
        }else{
            $this ->error("权限获取失败");
        }
        $res = Db::name("role") ->where(["id"=>$id]) ->update(["status"=>$status]);
        if($res){
            $this ->success("修改成功");
        }else{
            $this ->error("修改失败");
        }
    }

    public function delRole(){
        $id = input("post.id");
        if(!is_numeric($id)){
            $this ->error("参数有误");
        }
        $res = Db::name("role") ->where(["id"=>$id]) ->update(["is_del"=>1]);
        if($res){
            $this ->success("删除成功");
        }else{
            $this ->error("删除失败");
        }
    }

    public function editRole(){
        if($this->request->isPost()){
            $this ->doEditRole();
        }
        $id = input("get.id");
        if(!is_numeric($id)){
            $this ->error("参数有误");
        }
        $data = Db::name("role") ->where(["id"=>$id,"is_del"=>0]) ->find();
        if(empty($data)){
            $this ->error("用户获取失败");
        }
        $data["auth_id_list"] = explode(",", $data["auth_id_list"]);
        $this ->assign("data",$data);
        $auth = Db::name("auth") ->where(["is_del"=>0,"type" =>1])
            ->where("pid","neq",0)
            ->select();
        foreach($auth as &$v){
            $v["son"] = Db::name("auth") ->where(["is_del"=>0,"pid"=>$v["id"]]) ->select();
        }
        $this ->assign("auth",$auth);
        return $this ->fetch();
    }

    private function doEditRole(){
        $data = input("post.");
        if(!isset($data["id"]) || empty($data["id"]) || !isset($data["auth_id_list"])){
            $this ->error("参数有误");
        }
        $data["auth_id_list"] = implode(",", $data["auth_id_list"]);
        $validate = new roVali();
        if (!$validate ->check($data)) {
            $this ->error($validate->getError());
        }
        $res = Db::name("role") ->where(["id"=>$data["id"]]) ->update($data);
        if($res !== false){
            $this ->success("修改成功");
        }else{
            $this ->error("修改失败");
        }
    }

}