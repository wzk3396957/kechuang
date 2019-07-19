<?php
namespace app\api\controller;

use think\Db;

class Banner extends Base
{
    public function index(){
        $list = Db::name("banner")
            ->where(["is_del" =>0,"status" =>1])
            ->order("sort desc")
            ->field("title,img")
            ->select();
        foreach ($list as &$v){
            $v["img"] = config("app.url") . $v["img"];
        }
        exit(ajaxReturn($list,1,'获取成功'));
    }
}