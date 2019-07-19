<?php
namespace app\api\validate;

use think\Validate;

class Base extends Validate
{
    //手机号的验证规则
    protected function isMobile($value) {
        $rule = '^1(3|4|5|7|8)[0-9]\d{8}$^';
        $result = preg_match($rule, $value);
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    //QQ号的验证规则
    protected function isQQ($value) {
        $rule = '/^[1-9]\d{4,9}$/';
        $result = preg_match($rule, $value);
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    //银行卡
    protected function isBank($value){
        $rule = '/^([1-9]{1})(\d{14}|\d{18})$/';
        $result = preg_match($rule, $value);
        if ($result) {
            return true;
        } else {
            return false;
        }
    }
}