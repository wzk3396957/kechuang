<?php
namespace app\index\controller;

use think\Db;

class About extends Base
{
    public function index(){
        $data = Db::name("about_us") ->find();
        $this ->assign("data",$data);
        return $this ->fetch();
    }

    public function editAbout(){
        $id = input("post.id");
        $content = input("post.content");
        if(!is_numeric($id) || empty($content)){
            exit(ajaxReturn([],0,'参数有误'));
        }
        $res = Db::name("about_us") ->where(["id"=>$id]) ->update(["content"=>$content,"update_at"=>time()]);
        if($res !== false){
            $this ->success("修改成功");
        }else{
            $this ->error("修改失败");
        }
    }
}