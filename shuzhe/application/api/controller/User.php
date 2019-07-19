<?php
namespace app\api\controller;

use think\Db;
use app\api\validate\User as userVali;
use app\api\service\AliSms;

class User extends Base
{
    //注册
    public function register(){
        $data = input("post.");
        $validate = new userVali();
        if (!$validate ->check($data)) {
            exit(ajaxReturn([],0,$validate->getError()));
        }
        //验证是否已注册
        $user_id = $this ->user["id"];
        $is_exist = Db::name("user")
            ->where(["id"=>$user_id])
            ->value("phone");
        if($is_exist){
            exit(ajaxReturn([],0,'您已注册'));
        }
        //插入数据库
        unset($data["code"]);
        $res = Db::name("user")
             ->where(["id"=>$user_id])
             ->update($data);
        if($res !== false){
            exit(ajaxReturn([],1,'注册成功'));
        }else{
            exit(ajaxReturn([],0,'注册失败'));
        }
    }

    //我的信息
    public function myInfo(){
        $user_id = $this ->user["id"];
        $data = Db::name("user") ->where(["id" =>$user_id])
            ->field("nickname,avatar,phone,hosp,section,jobs")
            ->find();
        
        exit(ajaxReturn($data,1,'获取成功'));
    }

    //观看记录
    public function playVideoRecord(){
        $user_id = $this ->user["id"];
        $list = Db::name("play_video_record") ->alias("t1")
            ->join("med_video t2","t1.video_id=t2.id")
            ->where(["t1.user_id"=>$user_id])
            ->order("t1.id desc")
            ->field("t2.id,t1.create_at,t2.title,t2.play_count,t2.thumb_img")
            ->paginate(config("app.page_num"))
            ->toArray();
        foreach($list["data"] as &$v){
            $v["create_at"] = date("Y-m-d h:i:s",$v["create_at"]);
            $v["thumb_img"] = config("app.url") . $v["thumb_img"];
        }
        exit(ajaxReturn($list,1,"获取数据成功"));
    }

    //我的收藏
    public function myFavorites(){
        $user_id = $this ->user["id"];
        $list = Db::name("my_favorites")
            ->where(["is_del" =>0,"user_id" =>$user_id])
            ->field("id,favorites_id,favorites_type,create_at")
            ->order("id desc")
            ->paginate(config("app.page_num"))
            ->toArray();
        foreach($list["data"] as &$v){
            //视频
            if($v["favorites_type"] == 1){
                $res = Db::name("video")
                    ->where(["id" =>$v["favorites_id"]])
                    ->field("thumb_img as img,title,doctor_id")
                    ->find();
                $res["img"] = config("app.url") . $res["img"];
                $v["doctor"] = Db::name("doctor")
                    ->where(["id" =>$res["doctor_id"]])
                    ->value("name");
                unset($res["doctor_id"]);

            //术者文章
            }else{
                $res = Db::name("article")
                    ->where(["id" =>$v["favorites_id"]])
                    ->field("img,title")
                    ->find();
                $res["img"] = config("app.url") . $res["img"];

            }
            //合并
            $v["create_at"] = date("Y-m-d h:i:s",$v["create_at"]);
            $v = array_merge($v, $res);
        }
        exit(ajaxReturn($list,1,"获取数据成功"));
    }

    //取消收藏
    public function cancelFavorites(){
        $favorites_id = input("post.id");
        if(!is_numeric($favorites_id)){
            exit(ajaxReturn([],0,'参数有误'));
        }
        $user_id = $this ->user["id"];
        $res = Db::name("my_favorites")
            ->where(["user_id" =>$user_id,"id" =>$favorites_id])
            ->update(["is_del" =>1]);
        if($res){
            exit(ajaxReturn([],1,"取消成功"));
        }else{
            exit(ajaxReturn([],0,"取消失败"));
        }
    }

    //我的评论
    public function myComment(){
        $user_id = $this ->user["id"];
        $list = Db::name("comment") ->alias("t1")
            ->join("med_user t2","t1.user_id = t2.id")
            ->where(["t1.is_del" =>0,"t1.user_id" =>$user_id])
            ->field("t1.id,t1.comment_id,t1.comment_type,t1.create_at,t1.content,t2.nickname,t2.avatar")
            ->order("t1.id desc")
            ->paginate(config("app.page_num"))
            ->toArray();
        foreach($list["data"] as &$v){
            //视频
            if($v["comment_type"] == 1){
                $res = Db::name("video")
                    ->where(["id" =>$v["comment_id"]])
                    ->field("thumb_img as img,title")
                    ->find();
                $res["img"] = config("app.url") . $res["img"];

                //术者文章
            }else{
                $res = Db::name("article")
                    ->where(["id" =>$v["comment_id"]])
                    ->field("img,title")
                    ->find();
                $res["img"] = config("app.url") . $res["img"];

            }
            //合并
            $v["avatar"] = config("app.url") . $v["avatar"];
            $v["create_at"] = date("Y-m-d h:i:s",$v["create_at"]);
            $v = array_merge($v, $res);
        }
        exit(ajaxReturn($list,1,"获取数据成功"));
    }

    //删除评论
    public function delComment(){
        $comment_id = input("post.id");
        if(!is_numeric($comment_id)){
            exit(ajaxReturn([],0,'参数有误'));
        }
        $user_id = $this ->user["id"];
        $res = Db::name("comment")
            ->where(["user_id" =>$user_id,"id" =>$comment_id])
            ->update(["is_del" =>1]);
        if($res){
            exit(ajaxReturn([],1,"删除成功"));
        }else{
            exit(ajaxReturn([],0,"删除失败"));
        }
    }

    //关于我们
    public function aboutUs(){
        $data = Db::name("about_us")
            ->field("content")
            ->find();
        exit(ajaxReturn($data,1,"获取成功"));
    }

    //判断是否已注册
    public function isRegister(){
        $user_id = $this ->user["id"];
        $is_register = Db::name("user")
            ->where(["id"=>$user_id])
            ->value("phone");
        if($is_register){
            exit(ajaxReturn([],1,"已注册"));
        }else{
            exit(ajaxReturn([],0,"未注册"));
        }
    }
    
    public function sendSms($mobile="18011834048"){
        dump(AliSms::sendSms($mobile));
    }
}