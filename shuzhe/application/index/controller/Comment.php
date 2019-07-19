<?php
namespace app\index\controller;

use think\Db;

class Comment extends Base
{
    public function video(){
        $id = input("get.id");
        if(!is_numeric($id)){
            $this ->error("参数有误");
        }
        $list = Db::name("comment")
            ->alias("t1")
            ->join("med_user t2","t1.user_id = t2.id")
            ->where(["t1.is_del" =>0,"t1.comment_id"=>$id,"t1.comment_type"=>1])
            ->order("t1.id desc")
            ->field("t1.*,t2.nickname,t2.avatar")
            ->paginate(config("app.page_num"),false,['query'=>request()->param()]);
        $this ->assign("list",$list);

        return $this ->fetch();
    }

    public function delVideoComm(){
        $id = input("post.id");
        if(!is_numeric($id)){
            $this ->error("参数有误");
        }
        //评论数-1
        $video_id = Db::name("comment")
            ->where(["id" =>$id,"is_del" =>0])
            ->value("comment_id");
        if(empty($video_id)){
            $this ->error("数据有误");
        }
        Db::name("video") ->where(["id" =>$video_id])
            ->setDec("comment_count");
        $res = Db::name("comment") ->where(["id"=>$id])
            ->update(["is_del"=>1]);
        $this ->success("删除成功");
    }

    public function article(){
        $id = input("get.id");
        if(!is_numeric($id)){
            $this ->error("参数有误");
        }
        $list = Db::name("comment")
            ->alias("t1")
            ->join("med_user t2","t1.user_id = t2.id")
            ->where(["t1.is_del" =>0,"t1.comment_id"=>$id,"t1.comment_type"=>2])
            ->order("t1.id desc")
            ->field("t1.*,t2.nickname,t2.avatar")
            ->paginate(config("app.page_num"),false,['query'=>request()->param()]);
        $this ->assign("list",$list);

        return $this ->fetch();
    }

    public function delArticleComm(){
        $id = input("post.id");
        if(!is_numeric($id)){
            $this ->error("参数有误");
        }
        //评论数-1
        $article_id = Db::name("comment")
            ->where(["id" =>$id,"is_del" =>0])
            ->value("comment_id");
        if(empty($article_id)){
            $this ->error("数据有误");
        }
        Db::name("article") ->where(["id" =>$article_id])
            ->setDec("comment_count");
        Db::name("comment") ->where(["id"=>$id])
            ->update(["is_del"=>1]);
        $this ->success("删除成功");
    }
}