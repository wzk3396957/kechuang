<?php
namespace app\api\controller;

use think\Db;

class Comment extends Base
{
    //发表视频评论
    public function doVideoComm(){
        $video_id = input("post.id");
        $content = input("post.content");
        if(!is_numeric($video_id) || empty($content)){
            exit(ajaxReturn([],0,'参数有误'));
        }
        $user_id = $this ->user["id"];
        $data = [
            "user_id" =>$user_id,
            "comment_id" =>$video_id,
            "comment_type" =>1,
            "content" =>$content,
            "create_at" =>time()
        ];
        Db::name("comment") ->insert($data);
        Db::name("video") ->where(["id" =>$video_id])
            ->setInc("comment_count");
        exit(ajaxReturn([],1,'评论成功'));
    }

    //视频评论列表
    public function VideoComment(){
        $video_id = input("post.id");
        if(!is_numeric($video_id)){
            exit(ajaxReturn([],0,'参数有误'));
        }
        $list = Db::name("comment") ->alias("t1")
            ->join("med_user t2","t1.user_id = t2.id")
            ->where(["t1.comment_id" =>$video_id,"t1.is_del" =>0,"t1.comment_type" =>1])
            ->field("t1.content,t1.create_at,t2.nickname,t2.avatar")
            ->paginate(config("app.page_num"))
            ->toArray();
        foreach($list["data"] as &$v){
            $v["create_at"] = date("Y-m-d h:i:s",$v["create_at"]);
            $v["avatar"] = config("app.url") . $v["avatar"];
        }
        exit(ajaxReturn($list,1,"获取数据成功"));
    }

    //发表文章评论
    public function doArtiComm(){
        $article_id = input("post.id");
        $content = input("post.content");
        if(!is_numeric($article_id) || empty($content)){
            exit(ajaxReturn([],0,'参数有误'));
        }
        $user_id = $this ->user["id"];
        $data = [
            "user_id" =>$user_id,
            "comment_id" =>$article_id,
            "comment_type" =>2,
            "content" =>$content,
            "create_at" =>time()
        ];
        Db::name("comment") ->insert($data);
        Db::name("article") ->where(["id" =>$article_id])
            ->setInc("comment_count");
        exit(ajaxReturn([],1,'评论成功'));
    }

    //视频评论列表
    public function ArtiComment(){
        $article_id = input("post.id");
        if(!is_numeric($article_id)){
            exit(ajaxReturn([],0,'参数有误'));
        }
        $list = Db::name("comment") ->alias("t1")
            ->join("med_user t2","t1.user_id = t2.id")
            ->where(["t1.comment_id" =>$article_id,"t1.is_del" =>0,"t1.comment_type" =>2])
            ->field("t1.content,t1.create_at,t2.nickname,t2.avatar")
            ->paginate(config("app.page_num"))
            ->toArray();
        foreach($list["data"] as &$v){
            $v["create_at"] = date("Y-m-d h:i:s",$v["create_at"]);
            $v["avatar"] = config("app.url") . $v["avatar"];
        }
        exit(ajaxReturn($list,1,"获取数据成功"));
    }
}