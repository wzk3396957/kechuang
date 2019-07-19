<?php
namespace app\index\controller;

use think\Db;

class User extends Base
{
    public function index(){
        //筛选
        $where = $this ->indexFiltrate();
        $list = Db::name("user")
            ->alias("t1")
            ->join("med_user_role t2","t1.role_id = t2.id")
            ->field("t1.*,t2.role_name")
            ->where($where)
            ->paginate(config("app.page_num"),false,['query'=>request()->param()]);

        $this ->assign("list",$list);

        //角色
        $role = Db::name("user_role")
            ->where(["is_del"=>0])
            ->select();
        $this ->assign("role",$role);

        return $this ->fetch();
    }

    private function indexFiltrate(){
        $data = input("");
        $where = [];
        if(!empty($data['nickname'])){
            $where[] = ['t1.nickname','like',"%".$data["nickname"]."%"];
            $this ->assign("nickname",$data['nickname']);
        }
        if(isset($data['role_id']) && is_numeric($data['role_id'])){
            $where[] = ['t1.role_id','eq',$data['role_id']];
            $this ->assign("role_id",$data['role_id']);
        }
        return $where;
    }

    public function updStatus(){
        $id = input("post.id");
        if(!is_numeric($id)){
            $this ->error("参数有误");
        }
        $status = Db::name("user") ->where(["id"=>$id]) ->value("status");
        if($status === 1){
            $status = 0;
        }elseif($status === 0){
            $status = 1;
        }else{
            $this ->error("权限获取失败");
        }
        $res = Db::name("user") ->where(["id"=>$id]) ->update(["status"=>$status]);
        if($res){
            $this ->success("修改成功");
        }else{
            $this ->error("修改失败");
        }
    }

    public function setRole(){
        if($this->request->isPost()){
            $this ->doSetRole();
        }
        $user_id = input("get.id");
        if(!is_numeric($user_id)){
            $this ->error("参数有误");
        }
        $data = Db::name("user")
            ->where(["id"=>$user_id])
            ->find();
        $this ->assign("data",$data);
        $role = Db::name("user_role")
            ->where(["is_del"=>0])
            ->select();
        $this ->assign("role",$role);
        return $this ->fetch();
    }

    public function doSetRole(){
        $user_id = input("post.id");
        $role_id = input("post.role_id");
        if(!is_numeric($user_id) || !is_numeric($role_id)){
            $this ->error("参数有误");
        }
        $res = Db::name("user") ->where(["id"=>$user_id]) ->update(["role_id" =>$role_id]);
        if($res !== false){
            $this ->success("设置成功");
        }else{
            $this ->error("设置失败");
        }
    }

    public function role(){
        $list = Db::name("user_role")
            ->alias("t1")
            ->where(["t1.is_del"=>0])
//            ->where("t1.id","neq",0)
            ->field("t1.*,(select count(*) from med_user t2 where t1.id=t2.role_id) as user_count")
            ->paginate(config("app.page_num"));
        $this ->assign("list",$list);

        return $this ->fetch();
    }

    public function addRole(){
        if($this->request->isPost()){
            $this ->doAddRole();
        }
        return $this ->fetch();
    }

    private function doAddRole(){
        $data = input("post.");
        if(!isset($data["role_name"]) || empty($data["role_name"])){
            $this ->error("参数有误");
        }
        if(isset($data["is_video"])){
            $data["is_video"] = 1;
        }else{
            $data["is_video"] = 0;
        }
        $data["create_at"] = time();
        $res = Db::name("user_role") ->insert($data);
        if($res){
            $this ->success("添加成功");
        }else{
            $this ->error("添加失败");
        }
    }



    public function delRole(){
        $id = input("post.id");
        if(!is_numeric($id)){
            $this ->error("参数有误");
        }
        $res = Db::name("user_role") ->where(["id"=>$id]) ->update(["is_del"=>1]);
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
        $data = Db::name("user_role")
            ->where(["id"=>$id,"is_del"=>0])
            ->find();
        if(empty($data)){
            $this ->error("用户获取失败");
        }
        
        $this ->assign("data",$data);
        return $this ->fetch();
    }

    private function doEditRole(){
        $data = input("post.");
        if(!isset($data["id"]) || !is_numeric($data["id"]) || !isset($data["role_name"]) || empty($data["role_name"])){
            $this ->error("参数有误");
        }
        if($data["id"] == 1){
            $this ->error("不得修改默认角色");
        }
        if(isset($data["is_video"])){
            $data["is_video"] = 1;
        }else{
            $data["is_video"] = 0;
        }
        $res = Db::name("user_role")
            ->where(["id"=>$data["id"]])
            ->update($data);
        if($res !== false){
            $this ->success("修改成功");
        }else{
            $this ->error("修改失败");
        }
    }
}