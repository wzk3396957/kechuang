<?php
namespace app\api\controller;

use think\Db;

class Article extends Base
{
    public function articleList(){
        $tab = input("post.tab");
        if($tab == 2){
            $order = "read_count desc";
        }else{
            $order = "id desc";
        }
        //关键字搜索
        $title = input("post.title");
        if(empty($title)){
            $where = [];
        }else{
            $where[] = ["title","like","%".$title."%"];
        }
        $list = Db::name("article")
            ->where(["is_del" =>0,"status" =>1])
            ->order($order)
            ->where($where)
            ->field("id,img,title,type,like_count,favorite_count,comment_count,send_count")
            ->paginate(config("app.page_num"),false,['query'=>request()->param()])
            ->toArray();
        $user_id = $this ->user["id"];
        foreach ($list["data"] as &$v){
            $v["img"] = config("app.url") . $v["img"];
            $this ->renderRecord($user_id,$v);
        }
        exit(ajaxReturn($list,1,'获取成功'));
    }

    //渲染 点赞/收藏
    private function renderRecord($user_id,&$v){
        //是否点赞
        $like_record = Db::name("article_like")
            ->where(["user_id"=>$user_id,"article_id"=>$v["id"]])
            ->find();
        if(empty($like_record)){
            $v["is_like"] = 0;
        }else{
            $v["is_like"] = 1;
        }
        $favorites_record = Db::name("my_favorites")
            ->where(["user_id"=>$user_id,"favorites_id"=>$v["id"],"favorites_type"=>2,"is_del"=>0])
            ->find();
        if(empty($favorites_record)){
            $v["is_favorites"] = 0;
        }else{
            $v["is_favorites"] = 1;
        }
    }
    //文章详情
    public function articleInfo(){
        $article_id = input("post.id");
        if(!is_numeric($article_id)){
            exit(ajaxReturn([],0,'参数有误'));
        }
        $data = Db::name("article")
            ->where(["id" =>$article_id,"is_del" =>0,"status" =>1])
            ->field("id,type,note,like_count,favorite_count,comment_count,send_count")
            ->find();
        if(empty($data)){
            exit(ajaxReturn([],0,'获取失败'));
        }
        $user_id = $this ->user["id"];
        $this ->renderRecord($user_id,$data);
        //+1查看次数
        Db::name("article") ->where(["id" =>$article_id])
            ->setInc("read_count");
        exit(ajaxReturn($data,1,'获取成功'));
    }

    //文章点赞
    public function doLikeArti(){
        $article_id = input("post.id");
        if(!is_numeric($article_id)){
            exit(ajaxReturn([],0,'参数有误'));
        }
        $user_id = $this ->user["id"];

        $is_exist = Db::name("article_like")
            ->where(["user_id" =>$user_id,"article_id" =>$article_id])
            ->find();
        //取消赞
        if($is_exist){
            Db::name("article_like")
                ->where(["user_id" =>$user_id,"article_id" =>$article_id])
                ->delete();
            Db::name("article") ->where(["id" =>$article_id])
                ->setDec("like_count");
            exit(ajaxReturn([],1,'取消成功'));
        //点赞
        }else{
            $data = [
                "user_id" =>$user_id,
                "article_id" =>$article_id,
                "create_at" =>time()
            ];
            Db::name("article_like") ->insert($data);
            Db::name("article") ->where(["id" =>$article_id])
                ->setInc("like_count");
            exit(ajaxReturn([],1,'点赞成功'));
        }
    }

    //文章收藏
    public function doFavoArti(){
        $article_id = input("post.id");
        if(!is_numeric($article_id)){
            exit(ajaxReturn([],0,'参数有误'));
        }
        $user_id = $this ->user["id"];
        //判断是否已收藏
        $is_exist = Db::name("my_favorites")
            ->where(["user_id" =>$user_id,"favorites_id" =>$article_id,"is_del" =>0,"favorites_type" =>2])
            ->find();
        if($is_exist){
            //取消收藏
            Db::name("my_favorites")
                ->where(["user_id" =>$user_id,"favorites_id" =>$article_id,"favorites_type" =>2])
                ->update(["is_del" =>1]);
            Db::name("article") ->where(["id" =>$article_id])
                ->setDec("favorite_count");
            exit(ajaxReturn([],1,'取消成功'));
        }else{
            //收藏
            $data = [
                "user_id" =>$user_id,
                "favorites_id" =>$article_id,
                "favorites_type" =>2,
                "create_at" =>time()
            ];
            Db::name("my_favorites") ->insert($data);
            Db::name("article") ->where(["id" =>$article_id])
                ->setInc("favorite_count");
            exit(ajaxReturn([],1,'收藏成功'));
        }
    }

    public function sendArti(){
        $article_id = input("post.id");
        if(!is_numeric($article_id)){
            exit(ajaxReturn([],0,'参数有误'));
        }
        Db::name("article") ->where(["id" =>$article_id])
            ->setInc("send_count");
        exit(ajaxReturn([],1,'转发成功'));
    }
}