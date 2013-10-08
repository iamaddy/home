<?php

class IndexController extends AbstractController {
	public function IndexAction() {
		$config = $this->getConfig();
		$userModel = new UserModel();
		$userInfo = $userModel->userInfo(1000);
		var_dump(Url::getBaseUrl());
		return new View();
	}
}