<?php
namespace app\api\model;

use think\Model;

class Type extends Model
{
    protected $hidden = ["pid","create_at","status","is_del"];
}