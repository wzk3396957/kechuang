<?php
namespace app\index\validate;

use app\api\validate\Base;

class Doctor extends Base
{
    protected $rule = [
        'name' => 'require',
        'avatar' => 'require',
        'intro' => 'require',
    ];

    protected $field = [
        'name' => '姓名',
        'avatar' => '头像',
        'intro' => '简介',
    ];


}