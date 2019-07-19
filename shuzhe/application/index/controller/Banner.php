<?php
namespace app\index\controller;

use think\Db;
use app\index\validate\Banner as bannerVali;

class Banner extends Base
{
    //banner
    public function index(){
        $list = Db::name("banner") ->where(["is_del"=>0]) ->paginate(config("app.page_num"));
        $this ->assign("list",$list);
        return $this ->fetch();
    }

    public function addBanner(){
        if($this->request->isPost()){
            $this ->doAddBanner();
        }
        return $this ->fetch();
    }

    private function doAddBanner(){
        $data = input("post.");
        $validate = new bannerVali();
        if (!$validate ->check($data)) {
            $this ->error($validate->getError());
        }
        $data["create_at"] = time();
        unset($data["file"]);
        $res = Db::name("banner") ->insert($data);
        if($res){
            $this ->success("添加成功");
        }else{
            $this ->error("添加失败");
        }
    }

    public function delBanner(){
        $id = input("post.id");
        if(!is_numeric($id)){
            $this ->error("参数有误");
        }
        $res = Db::name("banner") ->where(["id"=>$id]) ->update(["is_del"=>1]);
        if($res){
            $this ->success("删除成功");
        }else{
            $this ->error("删除失败");
        }
    }

    public function editBanner(){
        if($this->request->isPost()){
            $this ->doEditBanner();
        }
        $id = input("get.id");
        if(!is_numeric($id)){
            $this ->error("参数有误");
        }
        $data = Db::name("banner") ->where(["id"=>$id,"is_del"=>0]) ->find();
        if(empty($data)){
            $this ->error("用户获取失败");
        }
        $this ->assign("data",$data);
        return $this ->fetch();
    }

    private function doEditBanner(){
        $data = input("post.");
        if(!isset($data["id"]) || empty($data["id"])){
            $this ->error("参数有误");
        }
        $validate = new bannerVali();
        if (!$validate ->check($data)) {
            $this ->error($validate->getError());
        }
        unset($data["file"]);
        $res = Db::name("banner") ->where(["id"=>$data["id"]]) ->update($data);
        if($res !== false){
            $this ->success("修改成功");
        }else{
            $this ->error("修改失败");
        }
    }


    public function updBannerStatus(){
        $id = input("post.id");
        if(!is_numeric($id)){
            $this ->error("参数有误");
        }
        $status = Db::name("banner") ->where(["id"=>$id]) ->value("status");
        if($status === 1){
            $status = 0;
        }elseif($status === 0){
            $status = 1;
        }else{
            $this ->error("权限获取失败");
        }
        $res = Db::name("banner") ->where(["id"=>$id]) ->update(["status"=>$status]);
        if($res){
            $this ->success("修改成功");
        }else{
            $this ->error("修改失败");
        }
    }
}