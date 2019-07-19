<?php
namespace app\api\controller;

use think\Db;

class Video extends Base
{
    //根据类别获取视频
    public function typeVideo(){
        $type_id = input("post.id");
        if(!is_numeric($type_id)){
            exit(ajaxReturn([],0,'参数有误'));
        }
        $list = Db::name("video")
            ->where(["type_id"=>$type_id,"status"=>1])
            ->field("id,title,play_count,main_img,doctor_id")
            ->paginate(config("app.page_num"))
            ->toArray();
        foreach($list["data"] as &$v){
            $v["main_img"] = config("app.url") . $v["main_img"];
        }
        exit(ajaxReturn($list,1,"获取数据成功"));
    }

    //视频详情
    public function videoInfo(){
        $video_id = input("post.id");
        if(!is_numeric($video_id)){
            exit(ajaxReturn([],0,'参数有误'));
        }
        //视频
        $video = Db::name("video") ->where(["id"=>$video_id])
            ->field("id,title,video,doctor_id,comment_count,favorite_count,like_count,send_count")
            ->find();
        if(empty($video)){
            exit(ajaxReturn([],0,'视频获取失败'));
        }
        $video["video"] = config("app.url") . $video["video"];
        //是否能看视频
        $user_id = $this ->user["id"];
        $role_id = Db::name("user") ->where(["id"=>$user_id])
            ->value("role_id");
        $video["is_video"] = Db::name("user_role")
            ->where(["id" =>$role_id])
            ->value("is_video");
        //医生信息
        $doctor = Db::name("doctor") ->where(["id"=>$video["doctor_id"]])
            ->field("avatar,name,intro")
            ->find();
        if(empty($doctor)){
            exit(ajaxReturn([],0,'医生获取失败'));
        }
        $doctor["avatar"] = config("app.url") . $doctor["avatar"];
        exit(ajaxReturn(compact("video","doctor"),1,"获取数据成功"));
    }

    //医生其他视频
    public function doctorVideo(){
        $video_id = input("post.video_id");
        $doctor_id = input("post.doctor_id");
        if(!is_numeric($video_id) || !is_numeric($doctor_id)){
            exit(ajaxReturn([],0,'参数有误'));
        }
        $list = Db::name("video") ->alias("t1")
            ->join("med_doctor t2","t1.doctor_id = t2.id")
            ->where(["t1.doctor_id"=>$doctor_id])
            ->where("t1.id","neq",$video_id)
            ->field("t1.id,t1.thumb_img,t1.title,t2.name")
            ->paginate(config("app.page_num"))
            ->toArray();
        foreach($list["data"] as &$v){
            $v["thumb_img"] = config("app.url") . $v["thumb_img"];
        }
        exit(ajaxReturn($list,1,"获取数据成功"));
    }

    //播放视频
    public function playVideo(){
        $video_id = input("post.id");
        if(!is_numeric($video_id)){
            exit(ajaxReturn([],0,'参数有误'));
        }
        //加+1观看次数
        Db::name("video") ->where(["id"=>$video_id])
            ->setInc("play_count");
        //加观看记录
        $user_id = $this ->user["id"];
        $record = ["user_id" =>$user_id,
            "video_id" =>$video_id,
            "create_at" =>time()
        ];
        Db::name("play_video_record") ->insert($record);
        exit(ajaxReturn([],1,"播放成功"));
    }


    //视频点赞
    public function doLikeVideo(){
        $video_id = input("post.id");
        if(!is_numeric($video_id)){
            exit(ajaxReturn([],0,'参数有误'));
        }
        $user_id = $this ->user["id"];

        $is_exist = Db::name("video_like")
            ->where(["user_id" =>$user_id,"video_id" =>$video_id])
            ->find();
        //取消赞
        if($is_exist){
            Db::name("video_like")
                ->where(["user_id" =>$user_id,"video_id" =>$video_id])
                ->delete();
            Db::name("video") ->where(["id" =>$video_id])
                ->setDec("like_count");
            exit(ajaxReturn([],1,'取消成功'));
            //点赞
        }else{
            $data = [
                "user_id" =>$user_id,
                "video_id" =>$video_id,
                "create_at" =>time()
            ];
            Db::name("video_like") ->insert($data);
            Db::name("video") ->where(["id" =>$video_id])
                ->setInc("like_count");
            exit(ajaxReturn([],1,'点赞成功'));
        }
    }

    //视频收藏
    public function doFavoVideo()
    {
        $video_id = input("post.id");
        if (!is_numeric($video_id)) {
            exit(ajaxReturn([], 0, '参数有误'));
        }
        $user_id = $this->user["id"];
        //判断是否已收藏
        $is_exist = Db::name("my_favorites")
            ->where(["user_id" => $user_id, "favorites_id" => $video_id, "is_del" => 0,"favorites_type" =>1])
            ->find();
        if ($is_exist) {
            //取消收藏
            Db::name("my_favorites")
                ->where(["user_id" => $user_id, "favorites_id" => $video_id,"favorites_type" =>1])
                ->update(["is_del" => 1]);
            Db::name("video")->where(["id" => $video_id])
                ->setDec("favorite_count");
            exit(ajaxReturn([], 1, '取消成功'));
        } else {
            //收藏
            $data = [
                "user_id" => $user_id,
                "favorites_id" => $video_id,
                "favorites_type" => 1,
                "create_at" => time()
            ];
            Db::name("my_favorites")->insert($data);
            Db::name("video")->where(["id" => $video_id])
                ->setInc("favorite_count");
            exit(ajaxReturn([], 1, '收藏成功'));
        }
    }
}