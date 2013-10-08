<?php
class AuthController extends AbstractController {
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
		$phone_num = $this->post('phonenum');
		$code = $this->post('code');
		$_SESSION['phone_num'] = $phone_num;
		$_SESSION['code'] = $code;
		$this->redirect($this->getConfig()->get('_BASE_URL_') . '/order');
	}
}