<?php
namespace app\api\controller;

use think\Controller;
use think\Db;

class Base extends Controller
{
	protected $user;

    public function __construct()
    {
        parent::__construct();
        $this ->checkToken();
    }


    //比对token
    public function checkToken(){
        $time = time();
        if(!isset($_SERVER["HTTP_TOKEN"])){
            exit(ajaxReturn([],400,"token无效"));
        }
        $access_token = $_SERVER["HTTP_TOKEN"];
        $user_info = Db::name("user") ->where("access_token",$access_token)
            ->where("login_at","gt",$time)
            ->field("id,status,role_id")
            ->find();
        if(empty($user_info)){
            exit(ajaxReturn([],400,"token无效"));
        }else{
            //验证是否冻结
            if($user_info["status"] == 0){
                exit(ajaxReturn([],0,"您已被冻结"));
            }
            //是否能查看视频
            $user_info["is_video"] = Db::name("user_role")
                ->where(["id"=>$user_info["role_id"]])
                ->value("is_video");
            $this ->user = $user_info;
        }
    }
}
