<?php
class LoginController extends AbstractController {
	public function IndexAction() {
		$userApi = Service::factory('User::UserApi');
		$current = $userApi->current();

		// 已经登录跳出
		if($current) {
			$backurl = $this->get('backurl')? $this->get('backurl'): $this->getConfig()->get('_BASE_URL');

			$this->redirect($backurl);
		}

		// 展示登陆页面
		$view = new View();
		// $view->layout(false);
		return $view;
	}
}