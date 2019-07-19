<?php
namespace app\index\validate;

use app\api\validate\Base;

class Video extends Base
{
    protected $rule = [
        'title' => 'require',
        'main_img' => 'require',
        'thumb_img' => 'require',
        'video' => 'require',
        'type_id' => 'require|number',
        'doctor_id' => 'require'
    ];

    protected $field = [
        'title' => '视频标题',
        'main_img' => '主图',
        'thumb_img' => '缩略图',
        'video' => '视频文件',
        'type_id' => '类型',
        'doctor_id' => '医生'
    ];


}