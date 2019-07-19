<?php
namespace app\index\controller;

use think\Db;
use app\index\validate\Type as TypeVali;

class Type extends Base
{
    public function index(){
        $list = Db::name("type") ->alias("t1")
                                    ->join("med_parent_type t2","t1.pid = t2.id")
                                    ->field("t1.*,t2.type_name as p_name")
                                    ->where(["t1.is_del"=>0])
                                    ->order("t1.id desc")
                                    ->paginate(config("app.page_num"));
        $this ->assign("list",$list);
        return $this ->fetch();
    }

    public function addType(){
        if($this->request->isPost()){
            $this ->doAddType();
        }
        $select = Db::name("parent_type") ->select();
        $this ->assign("select",$select);
        return $this ->fetch();
    }

    private function doAddType(){
        $data = input("post.");
        $validate = new TypeVali();
        if (!$validate ->check($data)) {
            $this ->error($validate->getError());
        }
        $data["create_at"] = time();
        unset($data["file"]);
        $res = Db::name("type") ->insert($data);
        if($res){
            $this ->success("添加成功");
        }else{
            $this ->error("添加失败");
        }
    }

    public function delType(){
        $id = input("post.id");
        if(!is_numeric($id)){
            $this ->error("参数有误");
        }
        $res = Db::name("type") ->where(["id"=>$id]) ->update(["is_del"=>1]);
        if($res){
            $this ->success("删除成功");
        }else{
            $this ->error("删除失败");
        }
    }

    public function editType(){
        if($this->request->isPost()){
            $this ->doEditType();
        }
        $id = input("get.id");
        if(!is_numeric($id)){
            $this ->error("参数有误");
        }
        $data = Db::name("type") ->where(["id"=>$id,"is_del"=>0]) ->find();
        if(empty($data)){
            $this ->error("用户获取失败");
        }
        $this ->assign("data",$data);
        $select = Db::name("parent_type") ->select();
        $this ->assign("select",$select);
        return $this ->fetch();
    }

    private function doEditType(){
        $data = input("post.");
        if(!isset($data["id"]) || !is_numeric($data["id"])){
            $this ->error("参数有误");
        }
        $validate = new TypeVali();
        if (!$validate ->check($data)) {
            $this ->error($validate->getError());
        }
        unset($data["file"]);
        $res = Db::name("type") ->where(["id"=>$data["id"]]) ->update($data);
        if($res !== false){
            $this ->success("修改成功");
        }else{
            $this ->error("修改失败");
        }
    }

    public function updTypeStatus(){
        $id = input("post.id");
        if(!is_numeric($id)){
            $this ->error("参数有误");
        }
        $status = Db::name("type") ->where(["id"=>$id]) ->value("status");
        if($status === 1){
            $status = 0;
        }elseif($status === 0){
            $status = 1;
        }else{
            $this ->error("权限获取失败");
        }
        $res = Db::name("type") ->where(["id"=>$id]) ->update(["status"=>$status]);
        if($res){
            $this ->success("修改成功");
        }else{
            $this ->error("修改失败");
        }
    }
}