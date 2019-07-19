<?php
namespace app\index\validate;

use app\api\validate\Base;

class Role extends Base
{
    protected $rule = [
        'role_name' => 'require|unique:role',
        'auth_id_list' => 'require'
    ];

    protected $field = [
        'role_name' => '角色名',
        'auth_id_list' => '权限集'
    ];


}