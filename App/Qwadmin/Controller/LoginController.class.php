<?php
/**
*
* 版权所有：恰维网络<www.qiawei.com>
* 作    者：寒川<admin@huikon.cn>
* 日    期：2016-01-17
* 版    本：1.0.4
* 功能说明：后台登录控制器。
*
**/

namespace Qwadmin\Controller;
use Common\Controller\BaseController;
use Think\Auth;
class LoginController extends BaseController {
    public function index(){
		
		$this -> display();
    }
    public function login(){
		$verify = isset($_POST['verify'])?trim($_POST['verify']):'';
		if (!$this->check_verify($verify,'login')) {
			$this -> error('验证码错误！','/Qwadmin/login.html');
		}

		$username = isset($_POST['user'])?trim($_POST['user']):'';
		$password = isset($_POST['password'])?password(trim($_POST['password'])):'';
		$remember = isset($_POST['remember'])?$_POST['remember']:0;
		if ($username=='') {
			$this -> error('用户名不能为空！','/Qwadmin/login.html');
		} elseif ($password=='') {
			$this -> error('密码必须！','/Qwadmin/login.html');
		}

		$model = M("Member");
		$user = $model ->field('uid,user')-> where(array('user'=>$username,'password'=>$password)) -> find();
		if($user) {
			if($remember){
				cookie('user',$user,3600*24*365);//记住我
			}else{
				cookie('user',$user);
			}
			if($user){
				addlog('登录成功。');
				header("Location: /Qwadmin/");
				exit(0);
			}
		}else{
			addlog('登录失败。',$username);
			$this -> error('登录失败，请重试！','/Qwadmin/login.html');
		}
    }
	
	public function verify() {
		$config = array(
		'fontSize' => 14, // 验证码字体大小
		'length' => 4, // 验证码位数
		'useNoise' => false, // 关闭验证码杂点
		'imageW'=>100,
		'imageH'=>30,
		);
		$verify = new \Think\Verify($config);
		$verify -> entry('login');
	}
	
	function check_verify($code, $id = '') {
		$verify = new \Think\Verify();
		return $verify -> check($code, $id);
	}
}