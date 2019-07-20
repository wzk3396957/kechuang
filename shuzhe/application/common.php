<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
/**
 * @param array $data
 * @param int $code
 * @param string $message
 * @return \think\response\Json
 */
function ajaxReturn($data = [],$code = 1,$message = 'success')
{
    header('Content-Type:application/json; charset=utf-8');
    if (!is_array($data) || empty($data)){
        return json_encode(['data'=>array(),'code'=>$code,'message'=>$message]);
    }
    if(array_key_exists('message',$data)){
        if(array_key_exists('status',$data)){
            return json_encode(['data'=>array(),'code'=>$data['status'],'message'=>$data['message']]);
        }else{
            return json_encode(['data'=>array(),'code'=>0,'message'=>$data['message']]);
        }
    }
    return json_encode(['data'=> !array_key_exists(0,$data) ? array($data) : $data,'code'=>$code,'message'=>$message]);
}
//curl
function getCurl($url){
    $info=curl_init();
    curl_setopt($info,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($info,CURLOPT_HEADER,0);
    curl_setopt($info,CURLOPT_NOBODY,0);
    curl_setopt($info,CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($info,CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($info,CURLOPT_URL,$url);
    $output= curl_exec($info);
    curl_close($info);
    return $output;
}
//curl(post)
function postCurl($url,$post_data){
    //初始化
    $curl = curl_init();
    //设置抓取的url
    curl_setopt($curl, CURLOPT_URL,$url);
    //设置头文件的信息作为数据流输出
    curl_setopt($curl, CURLOPT_HEADER, 1);
    //设置获取的信息以文件流的形式返回，而不是直接输出。
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    //设置post方式提交
    curl_setopt($curl, CURLOPT_POST, 1);

    curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
    //执行命令
    $data = curl_exec($curl);
    //关闭URL请求
    curl_close($curl);
    //显示获得的数据
    return $data;
}

//生成随机字符串（一般用于生成订单号）
function getRandChar($length)
{
    $str = null;
    $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
    $max = strlen($strPol) - 1;

    for ($i = 0;
         $i < $length;
         $i++) {
        $str .= $strPol[rand(0, $max)];
    }

    return $str;
}

/**
 * 加密
 */
function emoji_encode($str){
    $str_encode = '';
    $length = mb_strlen($str,'utf-8');
    for($i=0;$i<$length;$i++){
        $_tmp_str = mb_substr($str,$i,1,'utf-8');
        if(strlen($_tmp_str) >= 4){
            $str_encode .= '[EMOJI:' . rawurlencode($_tmp_str) . ']]';
        }else{
            $str_encode .= $_tmp_str;
        }
    }
    return $str_encode;
}

/**
 * 解密
 */
function emoji_decode($str){
    $str_decode = preg_replace_callback('|\[EMOJI:(.*?)\]\]|', function($matches){
        return rawurldecode($matches[1]);
    },$str);
    return $str_decode;
}