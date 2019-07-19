<?php
/**
 * Created by PhpStorm.
 * User: D.Chen
 * Date: 2018/12/6
 * Time: 15:58
 */

namespace app\index\controller;

use think\Controller;
use think\facade\Session;
use think\Db;

class Login extends Controller
{
    public function login(){
        if($this->request->isPost()){
            $this ->checkLogin();
        }
        $this->assign('title','术者管理后台');
        $this->assign('website','术者管理后台');
        echo $this->fetch();
    }

    private function checkLogin(){
        $username = input("post.username");
        $pwd = input("post.password");
        if(empty($username) || empty($pwd)){
            $this ->error("参数有误");
        }
        $pwd = md5(md5($pwd).config('app.salf'));
        $admin = Db::name("admin") ->where(["username"=>$username,"password"=>$pwd]) ->find();
        if(empty($admin)){
            $this ->error("账号密码有误");
        }
        if($admin["status"] == 0){
            $this ->error("账号已被冻结");
        }
        Session::set('nickname',$admin['username']);
        //Session::set('user_id',$admin['id']);
        Session::set('role_id',$admin['role_id']);
        $menu = Db::name("auth") ->where(["is_del"=>0,"pid"=>0]) ->select();
        foreach($menu as &$v){
            $v["sub"] = Db::name("auth") ->where(["is_del"=>0,"pid"=>$v["id"]]) ->select();
        }
        Session::set('menu',$menu);
        $this ->success("登录成功");
    }

    public function logout(){
        session(null);
        $this->redirect('index/login/login');
    }
}