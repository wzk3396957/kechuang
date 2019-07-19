<?php
namespace app\index\controller;

class Index extends Base
{
    public function index()
    {
        $this ->assign("title","首页");
        $this ->assign("website","术者");
        return $this->fetch();
    }

    public function welcome(){
        $this ->assign("title","欢迎页");
        return $this->fetch();
    }
}
