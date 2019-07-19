<?php
namespace app\api\service;

use think\Db;

class Safety
{
    public static $expire_in = 60*60*24*7;

    public static function generateToken($user)
    {
        $randChar = getRandChar(32);
        $timestamp = $_SERVER['REQUEST_TIME'];
        $openid = $user["openid"];
        $token = md5($randChar . $timestamp . $openid);//加上账号

        return self::saveToCache($token,md5($randChar),$timestamp,$user);
    }

    private static function saveToCache($token,$randChar,$timestamp,$user)
    {
        $values = [
            //'user'=>$user,
            'access_token'=>$token,
//            'timestamp'=>$timestamp,
        ];
        //令牌插入数据库
        $result = Db::name("user") ->where(["id" => $user["id"]]) ->update(["access_token"=>$token,"login_at"=>time()+self::$expire_in]);
        if (!$result){
            exit(ajaxReturn([],0,"生成token失败"));
        }
        return $values;
    }
}