<?php
namespace app\api\controller;

use think\Db;

class Doctor extends Base
{
    //医生主页
    public function doctorInfo(){
        $doctor_id = input("post.doctor_id");
        if(!is_numeric($doctor_id)){
            exit(ajaxReturn([],0,'参数有误'));
        }
        $data = Db::name("doctor") ->where(["id"=>$doctor_id])
            ->field("avatar,name,intro")
            ->find();
        $data["avatar"] = config("app.url") . $data["avatar"];
        exit(ajaxReturn($data,1,"获取数据成功"));
    }

    //医生视频列表
    public function doctorVideo(){
        $doctor_id = input("post.doctor_id");
        if(!is_numeric($doctor_id)){
            exit(ajaxReturn([],0,'参数有误'));
        }
        $list = Db::name("video") ->where(["doctor_id"=>$doctor_id])
            ->field("thumb_img,title,play_count")
            ->order("id desc")
            ->paginate(config("app.page_num"))
            ->toArray();
        foreach($list["data"] as &$v){
            $v["thumb_img"] = config("app.url") . $v["thumb_img"];
        }
        exit(ajaxReturn($list,1,"获取数据成功"));
    }
}