<?php
namespace app\index\controller;

use think\Db;
use app\index\validate\Video as videoVali;
use app\index\service\Excel;

class Video extends Base
{
    public function index(){
        //筛选
        $where = $this ->indexFiltrate();
        $list = Db::name("video") ->alias("t1")
            ->join("med_type t2","t1.type_id = t2.id")
            ->join("med_parent_type t3","t2.pid = t3.id")
            ->join("med_doctor t4","t1.doctor_id = t4.id")
            ->where($where)
            ->field("t1.*,t2.type_name,t3.type_name as p_name,t4.name as doctor")
            ->order("t1.id desc")
            ->paginate(config("app.page_num"),false,['query'=>request()->param()]);
        $this ->assign("list",$list);

        return $this ->fetch();
    }

    private function indexFiltrate(){
        $data = input("");
        $where = [];
        if(isset($data["doctor_id"]) && is_numeric($data["doctor_id"])){
            $where[] = ["t1.doctor_id","eq",$data["doctor_id"]];
        }
        if(!empty($data['title'])){
            $where[] = ['t1.title','like',"%".$data["title"]."%"];
            $this ->assign("title",$data['title']);
        }
        if(isset($data['status']) && is_numeric($data['status'])){
            $where[] = ['t1.status','eq',$data['status']];
            $this ->assign("status",$data['status']);
        }
        return $where;
    }

    public function addVideo(){
        if($this->request->isPost()){
            $this ->doAddVideo();
        }
        $p_type = Db::name("parent_type") ->select();
        $this ->assign("p_type",$p_type);
        
        //$this ->getDoctor();
//        $this ->getType();

        return $this ->fetch();
    }
    
//    private function getDoctor(){
//        $search = input("get.search");
//        if(empty($search)){
//            $doctor = "";
//        }else{
//            $this ->assign("search",$search);
//            $doctor = Db::name("doctor")
//                ->where("name","like","%".$search."%")
//                ->where(["is_del"=>0])
//                ->select();
//        }
//        $this ->assign("doctor",$doctor);
//    }
//
//    private function getType(){
//        $pid = input("get.p_type");
//        if(empty($pid)){
//            $type = "";
//        }else{
//            $this ->assign("pid",$pid);
//            $type = Db::name("type")
//                ->where(["pid"=>$pid,"is_del"=>0,"status"=>1])
//                ->select();
//        }
//        $this ->assign("type",$type);
//    }
    
    private function doAddVideo(){
        $data = input("post.");
        $validate = new videoVali();
        if (!$validate ->check($data)) {
            $this ->error($validate->getError());
        }
        $data["create_at"] = time();
        unset($data["file"]);
        if(isset($data["status"])){
            $data["status"] = 1;
        }else{
            $data["status"] = 0;
        }
        $res = Db::name("video") ->insert($data);
        if($res){
            $this ->success("添加成功");
        }else{
            $this ->error("添加失败");
        }
    }



    public function editVideo(){
        if($this->request->isPost()){
            $this ->doEditVideo();
        }
        $id = input("get.id");
        if(!is_numeric($id)){
            $this ->error("参数有误");
        }
        $data = Db::name("video") ->where(["id"=>$id]) ->find();
        if(empty($data)){
            $this ->error("用户获取失败");
        }
        $p_type = Db::name("parent_type") ->select();
        //父级分类
        $this ->assign("p_type",$p_type);
        $data["type_pid"] = Db::name("type") ->where(["id"=>$data["type_id"]]) ->value("pid");
        //医生
        $doctor = Db::name("doctor") ->where(["id"=>$data["doctor_id"]]) ->find();
        $this ->assign("doctor",$doctor);
        //子级分类
        $type = Db::name("type")
            ->where(["pid"=>$data["type_pid"],"is_del"=>0,"status"=>1])
            ->select();
        $this ->assign("type",$type);
        $this ->assign("data",$data);
        return $this ->fetch();
    }

    private function doEditVideo(){
        $data = input("post.");
        if(!isset($data["id"]) || !is_numeric($data["id"])){
            $this ->error("参数有误");
        }
        $validate = new videoVali();
        if (!$validate ->check($data)) {
            $this ->error($validate->getError());
        }
        unset($data["file"]);
        if(isset($data["status"])){
            $data["status"] = 1;
        }else{
            $data["status"] = 0;
        }
        $res = Db::name("video") ->where(["id"=>$data["id"]]) ->update($data);
        if($res !== false){
            $this ->success("修改成功");
        }else{
            $this ->error("修改失败");
        }
    }

    public function updVideoStatus(){
    $id = input("post.id");
    if(!is_numeric($id)){
        $this ->error("参数有误");
    }
    $status = Db::name("video") ->where(["id"=>$id]) ->value("status");
    if($status === 1){
        $status = 0;
    }elseif($status === 0){
        $status = 1;
    }else{
        $this ->error("权限获取失败");
    }
    $res = Db::name("video") ->where(["id"=>$id]) ->update(["status"=>$status]);
    if($res){
        $this ->success("修改成功");
    }else{
        $this ->error("修改失败");
    }
}

    public function updType(){
        $pid = input("post.pid");
        if(!is_numeric($pid)){
            $this ->error("参数有误");
        }
        $type = Db::name("type")
            ->where(["pid"=>$pid,"is_del"=>0,"status"=>1])
            ->select();
        if(empty($type)){
            $this ->error("暂无数据");
        }
        $this ->success("获取成功","",$type);
    }
    
    public function searchDoctor(){
        $name = input("post.search");
        if(empty($name)){
            $this ->error("参数有误");
        }
        $doctor = Db::name("doctor")
            ->where(["is_del"=>0])
            ->where("name","like","%".$name."%")
            ->select();
        if(empty($doctor)){
            $this ->error("暂无数据");
        }
        $this ->success("获取成功","",$doctor);
    }

    public function doVideoExcel(){
        $header = ['标题','一级分类','二级分类','医生','播放量','添加时间','点赞数','收藏数','转发数','评论数','状态'];
        $list = Db::name("video") ->alias("t1")
            ->join("med_type t2","t1.type_id = t2.id")
            ->join("med_parent_type t3","t2.pid = t3.id")
            ->join("med_doctor t4","t1.doctor_id = t4.id")
            ->field("t1.*,t2.type_name,t3.type_name as p_name,t4.name as doctor")
            ->select();
        $body = [];
        foreach($list as $k => $val){
            $body[$k][] = $val['title'];
            $body[$k][] = $val['p_name'];
            $body[$k][] = $val['type_name'];
            $body[$k][] = $val['doctor'];
            $body[$k][] = $val['play_count'];
            $body[$k][] = date("Y-m-d H:i:s",$val['create_at']);
            $body[$k][] = $val['like_count'];
            $body[$k][] = $val['favorite_count'];
            $body[$k][] = $val['send_count'];
            $body[$k][] = $val['comment_count'];
            switch($val['status']){
                case 1:
                    $body[$k][] = '正常';
                    break;
                case 0:
                    $body[$k][] = '下架中';
                    break;
            }
        }
        Excel::export($header, $body);
    }
}