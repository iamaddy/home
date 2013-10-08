<?php
/**
 * File:        UserApi.php
 *
 * 用户接口
 */

class UserApi extends AbstractService {
	// 构造器
	public function __construct() {
		$this->userModel = new UserModel();
	}

	/**
	 * 获取用户信息
	 * @param int $uid
	 * @return Ambigous <multitype:, boolean, unknown>
	 */
	public function userInfo($uid) {
		$return = $this->userModel->userInfo($uid);
		return $return;
	}

	// 获取当前登录用户
	public function current() {
		if(!isset($_COOKIE['iamaddy_uid']) || !isset($_COOKIE['iamaddy_token'])) return false;
		session_start();
		$userInfo = $this->userInfo(md5($_SESSION['iamaddy_uid']));
 		if(!$userInfo) return false;
 		if($_COOKIE['iamaddy_token'] === md5(http_build_query($userInfo))) {
 			return $userInfo;
 		} else return false;
	}

	// 用户注销登陆
	public function logout() {
		setcookie("ddclick_uid", 	'');
		setcookie("ddclick_token",  '');
	}

 	public function search($username) {
		return $this->userModel->search($username);
	}
}
