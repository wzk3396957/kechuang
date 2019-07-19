<?php
namespace app\api\controller;

use think\Controller;
use think\Db;
use app\api\service\Safety;

class Login extends Controller
{
	protected $appid = "wxd69c6276bcc3cd9e";

	protected $secret = "015f3edc6c09bc91132eef8308870951";

	protected $grant_type = "authorization_code";

	//登录
	public function userLogin(){
		//获取openid和session_id
		$res = $this ->getWxID();
		if(isset($res["openid"]) && isset($res["session_key"])){
			$user = Db::name("user") ->where(["openid"=>$res["openid"]]) ->find();
			if(empty($user)){
				$user = $this ->register($res);
			}else{
				Db::name("user") ->where(["openid"=>$res["openid"]]) ->update(["session_key"=>$res["session_key"]]);
			}

			$data = Safety::generateToken($user);
			exit(ajaxReturn($data,1,"登录成功"));
		}else{
			halt($res);
		}

	}
	//获取openid和session_id
	public function getWxID(){
		$code = input("code");
		if(empty($code)){
			exit(ajaxReturn([],0,"参数有误"));
		}
		$url = "https://api.weixin.qq.com/sns/jscode2session?appid=". $this->appid ."&secret=". $this->secret ."&js_code=". $code . "&grant_type=authorization_code";
		$res = getCurl($url);
		$res = json_decode($res, true);
		if(empty($res)){
			exit(ajaxReturn([],0,"获取失败"));
		}
		return $res;
	}

	//注册
	private function register($res){
		$iv = input("iv");
		$encryptedData = input("encryptedData");
		if(empty($iv) || empty($encryptedData)){
			exit(ajaxReturn([],0,"参数有误"));
		}
		$json = $this -> decryptData($encryptedData,$iv,$this->appid, $res['session_key']);
		$arr = json_decode($json, true);
		if(empty($arr)){
			exit(ajaxReturn([],$json,"获取用户信息失败"));
		}
		$user = ["openid"=>$res["openid"],
			"session_key"=>$res["session_key"],
			"nickname"=>$arr["nickName"],
			"avatar"=>$arr["avatarUrl"],
			"create_at"=>time()];
		$user["id"] = Db::name("user") ->insertGetId($user);
		if(!$user["id"]){
			exit(ajaxReturn([],0,"注册失败"));
		}
		return $user;
	}

	//解密函数，得到解密后的用户信息
	private function decryptData($encryptedData,$iv, $appid,$sessionKey)
	{
		if (strlen($sessionKey) != 24) {
			return '-41001';
		}
		$aesKey=base64_decode($sessionKey);


		if (strlen($iv) != 24) {
			return '-41002';
		}
		$aesIV=base64_decode($iv);

		$aesCipher=base64_decode($encryptedData);

		$result=openssl_decrypt( $aesCipher, "AES-128-CBC", $aesKey, 1, $aesIV);

		$dataObj=json_decode( $result );
		if( $dataObj  == NULL )
		{
			return '-41003';
		}
		if( $dataObj->watermark->appid != $appid )
		{
			return '-41003';
		}
		return $result;
	}
}


