<?php
namespace app\index\controller;

use think\Db;
use app\index\validate\Doctor as DoctorVali;

class Doctor extends Base
{
    public function index(){
        $id = input("get.id");
        $where = [];
        if(is_numeric($id)){
            $where = ["t1.id"=>$id];
        }
        $list = Db::name("doctor")
            ->alias("t1")
            ->where(["t1.is_del" =>0])
            ->where($where)
            ->order("t1.id desc")
            ->field("t1.*,(SELECT count(*) from med_video t2 where t1.id=t2.doctor_id) as video_count")
            ->paginate(config("app.page_num"));
        $this ->assign("list",$list);

        return $this ->fetch();
    }

    public function addDoctor(){
        if($this->request->isPost()){
            $this ->doAddDoctor();
        }
        return $this ->fetch();
    }

    private function doAddDoctor(){
        $data = input("post.");
        $validate = new DoctorVali();
        if (!$validate ->check($data)) {
            $this ->error($validate->getError());
        }
        $data["create_at"] = time();
        unset($data["file"]);
        $res = Db::name("doctor") ->insert($data);
        if($res){
            $this ->success("添加成功");
        }else{
            $this ->error("添加失败");
        }
    }

    public function delDoctor(){
        $id = input("post.id");
        if(!is_numeric($id)){
            $this ->error("参数有误");
        }
        $res = Db::name("doctor") ->where(["id"=>$id]) ->update(["is_del"=>1]);
        if($res){
            $this ->success("删除成功");
        }else{
            $this ->error("删除失败");
        }
    }

    public function editDoctor(){
        if($this->request->isPost()){
            $this ->doEditDoctor();
        }
        $id = input("get.id");
        if(!is_numeric($id)){
            $this ->error("参数有误");
        }
        $data = Db::name("doctor") ->where(["id"=>$id,"is_del"=>0]) ->find();
        if(empty($data)){
            $this ->error("用户获取失败");
        }
        $this ->assign("data",$data);
        return $this ->fetch();
    }

    private function doEditDoctor(){
        $data = input("post.");
        if(!isset($data["id"]) || !is_numeric($data["id"])){
            $this ->error("参数有误");
        }
        $validate = new DoctorVali();
        if (!$validate ->check($data)) {
            $this ->error($validate->getError());
        }
        unset($data["file"]);
        $res = Db::name("doctor") ->where(["id"=>$data["id"]]) ->update($data);
        if($res !== false){
            $this ->success("修改成功");
        }else{
            $this ->error("修改失败");
        }
    }

    
}