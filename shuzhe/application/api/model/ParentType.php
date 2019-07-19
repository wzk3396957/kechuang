<?php
namespace app\api\model;

use think\Model;

class ParentType extends Model
{
    protected $hidden = ["id"];

    public function sonType()
    {
        return $this->hasMany('Type',"pid","id") ->where(["status"=>1,"is_del"=>0]);
    }
}