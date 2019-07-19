<?php
namespace app\api\validate;


class User extends Base
{
    protected $rule = [
        'phone'   =>  'require|isMobile',
        'code' => 'require',
        'hosp' => 'require',
        'section' => 'require',
        'jobs' => 'require',
    ];

    protected $field = [
        'phone'   =>  '手机号',
        'code' => '验证码',
        'hosp' => '医院',
        'section' => '科室',
        'jobs' => '职位',
    ];
}