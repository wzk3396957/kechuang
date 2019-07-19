<?php
namespace app\api\controller;

use think\Db;
use app\api\model\ParentType;

class Type extends Base
{
    public function getType(){
        $type = ParentType::with('sonType') ->select() ->toArray();
        foreach ($type as &$v){
            foreach ($v["son_type"] as &$item){
                $item["icon"] = config("app.url") . $item["icon"];
            }
        }
        if(empty($type)){
            exit(ajaxReturn([],0,"获取失败"));
        }
        exit(ajaxReturn($type,1,"获取成功"));
    }
}