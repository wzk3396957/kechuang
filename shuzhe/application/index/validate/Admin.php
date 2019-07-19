<?php
namespace app\index\validate;

use app\api\validate\Base;

class Admin extends Base
{
    protected $rule = [
        'id'   =>  'require|number',
        'username' => 'require|unique:admin',
        'password' => 'require|between:6,12',
        'role_id' => 'require|number',
    ];

    protected $field = [
        'username' => '用户名',
        'password' => '密码',
        'role_id' => '角色ID',
    ];

    //验证场景
    protected $scene = [
        'add' => ['username','password','role_id'],
        'edit' =>['id','username','role_id']
    ];
}