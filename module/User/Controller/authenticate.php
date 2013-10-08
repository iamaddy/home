<?php
/**
 * File:        Authenticate.php
 *
 * 登陆验证
 * @version 	0.0.1
 */

class AuthenticateController extends AbstractController {
	private $model;
	public function __construct() {
		$this->model = new UserModel();
	}
	public function IndexAction() {
		session_start();
		if(!$this->isPost()) {
			$this->redirect($this->getConfig()->get('_BASE_URL_'));
		}
		// 登陆验证
		$username = $this->post('username');
		$password = $this->post('password');
		$url = $this->post('returnurl');
		if($this->model->login($username, $password)){
			$_SESSION['iamaddy_uid'] = $username;
			$userApi = Service::factory('User::UserApi');
			$userInfo = $userApi->userInfo(md5($username));
			setcookie("iamaddy_uid", md5($username), time()+3600, '/');
			setcookie("iamaddy_token", md5(http_build_query($userInfo)), time()+3600, '/');
			if($url){
				$this->redirect($url);
			}
			$this->redirect($this->getConfig()->get('_BASE_URL_'));
		} else{
			setcookie("loginFlag", 1);
			$this->redirect($this->getConfig()->get('_BASE_URL_') . '/user/login');
		}
	}
}