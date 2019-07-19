<?php
namespace app\index\validate;

use app\api\validate\Base;

class Article extends Base
{
    protected $rule = [
        'title' => 'require|max:50',
        'img' => 'require',
//        'note' => 'require',
    ];

    protected $field = [
        'title' => '标题',
        'img' => '图片',
//        'note' => '内容',
    ];


}