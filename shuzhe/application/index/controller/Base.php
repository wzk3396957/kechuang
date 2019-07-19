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
use think\facade\Request;
use think\Db;

class Base extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this ->checkSession();
    }

    private function checkSession(){
        //$user_id = Session::get('user_id');
        $role_id = Session::get('role_id');
        if(empty($role_id)){
            $this->redirect('/index.php/index/login/login');
        }
        $auth_list = Db::name("role") ->where(["id"=>$role_id]) ->value("auth_id_list");
        if($auth_list != "*"){
            $auth_list = explode(",", $auth_list);
            $controller = Request::controller();
            $action = Request::action();
            $auth = strtolower($controller."/".$action);
            if($auth != "index/index" && $auth != "index/welcome" && !in_array($auth,$auth_list)){
                exit("您没有权限");
            }
        }
    }

    public function upload(){
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('file');
        // 移动到框架应用根目录/public/uploads/ 目录下
        $info = $file->validate(['ext'=>'jpg,png,jpeg,gif'])->move(ROOT_PATH.'/uploads');
        if($info){
            //获取图片的存放相对路径
            $filePath =  '/uploads/'.$info->getSaveName();

            //整理数据,写入数据库
            $arr = array(
                'code' => '0',
                'msg' => '上传成功',
                'src' => $filePath
            );
            echo json_encode($arr);
        }else{
            $arr = array(
                'code' => '4004',
                'msg' => $file->getError(),
                'src' => ''
            );
            echo json_encode($arr);
        }
    }

    public function uploadVideo(){
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('file');
        // 移动到框架应用根目录/public/uploads/ 目录下
        $info = $file->validate(['ext'=>'avi,mov,rmvb,rm,flv,mp4,3gp,mp3'])->move(ROOT_PATH.'/uploads');
        if($info){
            //获取图片的存放相对路径
            $filePath =  '/uploads/'.$info->getSaveName();

            //整理数据,写入数据库
            $arr = array(
                'code' => '0',
                'msg' => '上传成功',
                'src' => $filePath
            );
            echo json_encode($arr);
        }else{
            $arr = array(
                'code' => '4004',
                'msg' => $file->getError(),
                'src' => ''
            );
            echo json_encode($arr);
        }
    }
}