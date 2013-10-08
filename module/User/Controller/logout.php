<?php
/**
 * File:        logout.php
 *
 * 退出登陆
 */

class LogoutController extends AbstractController {
	public function IndexAction() {
		session_start();
		unset($_SESSION['phone_num']);
		unset($_SESSION['code']);
		unset($_SESSION['iamaddy_uid']);
		$this->redirect($this->getConfig()->get('_BASE_URL_'));
	}
}