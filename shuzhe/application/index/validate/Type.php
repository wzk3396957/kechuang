<?php
namespace app\index\validate;

use app\api\validate\Base;

class Type extends Base
{
    protected $rule = [
        'type_name' => 'require',
        'icon' => 'require',
        'pid' => 'require|number',
    ];

    protected $field = [
        'type_name' => '分类名',
        'icon' => '图标',
        'pid' => '一级分类',
    ];


}