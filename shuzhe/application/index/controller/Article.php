<?php
namespace app\index\controller;

use think\Db;
use app\index\validate\Article as ArticleVali;

class Article extends Base
{
    public function index(){
        $list = Db::name("article") ->where(["is_del"=>0]) ->paginate(config("app.page_num"));
        $this ->assign("list",$list);
        return $this ->fetch();
    }

    public function addArticle(){
        if($this->request->isPost()){
            $this ->doAddArticle();
        }
        return $this ->fetch();
    }

    private function doAddArticle(){
        $data = input("post.");
        $validate = new ArticleVali();
        if (!$validate ->check($data)) {
            $this ->error($validate->getError());
        }
        if(isset($data["type"])){
            $data["type"] = 2;
            if(!isset($data["article"]) || empty($data["article"])){
                $this ->error("参数有误");
            }
            $data["note"] = $data["article"];
        }else{
            $data["type"] = 1;
            if(!isset($data["url"]) || empty($data["url"])){
                $this ->error("参数有误");
            }
            $data["note"] = $data["url"];
        }

        $data["create_at"] = time();
        unset($data["file"]);
        unset($data["article"]);
        unset($data["url"]);
        $res = Db::name("article") ->insert($data);
        if($res){
            $this ->success("添加成功");
        }else{
            $this ->error("添加失败");
        }
    }

    public function delArticle(){
        $id = input("post.id");
        if(!is_numeric($id)){
            $this ->error("参数有误");
        }
        $res = Db::name("article") ->where(["id"=>$id]) ->update(["is_del"=>1]);
        if($res){
            $this ->success("删除成功");
        }else{
            $this ->error("删除失败");
        }
    }

    public function editArticle(){
        if($this->request->isPost()){
            $this ->doEditArticle();
        }
        $id = input("get.id");
        if(!is_numeric($id)){
            $this ->error("参数有误");
        }
        $data = Db::name("article") ->where(["id"=>$id,"is_del"=>0]) ->find();
        if(empty($data)){
            $this ->error("获取失败");
        }
        $this ->assign("data",$data);
        return $this ->fetch();
    }

    private function doEditArticle(){
        $data = input("post.");
        if(!isset($data["id"]) || empty($data["id"])){
            $this ->error("参数有误");
        }
        $validate = new ArticleVali();
        if (!$validate ->check($data)) {
            $this ->error($validate->getError());
        }
        if(isset($data["type"])){
            $data["type"] = 2;
            if(!isset($data["article"]) || empty($data["article"])){
                $this ->error("参数有误");
            }
            $data["note"] = $data["article"];
        }else{
            $data["type"] = 1;
            if(!isset($data["url"]) || empty($data["url"])){
                $this ->error("参数有误");
            }
            $data["note"] = $data["url"];
        }
        unset($data["file"]);
        unset($data["article"]);
        unset($data["url"]);
        $res = Db::name("article") ->where(["id"=>$data["id"]]) ->update($data);
        if($res !== false){
            $this ->success("修改成功");
        }else{
            $this ->error("修改失败");
        }
    }

    public function updArticleStatus(){
        $id = input("post.id");
        if(!is_numeric($id)){
            $this ->error("参数有误");
        }
        $status = Db::name("article") ->where(["id"=>$id]) ->value("status");
        if($status === 1){
            $status = 0;
        }elseif($status === 0){
            $status = 1;
        }else{
            $this ->error("权限获取失败");
        }
        $res = Db::name("article") ->where(["id"=>$id]) ->update(["status"=>$status]);
        if($res){
            $this ->success("修改成功");
        }else{
            $this ->error("修改失败");
        }
    }
}