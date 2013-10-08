<?php
/**
 * File:        AbstractController.php
 *
 * 抽象控制器类
 *
 */

class AbstractController {
	/**#@+
	 * @const string METHOD constant names
	 */
	const METHOD_OPTIONS = 'OPTIONS';
	const METHOD_GET     = 'GET';
	const METHOD_HEAD    = 'HEAD';
	const METHOD_POST    = 'POST';
	const METHOD_PUT     = 'PUT';
	const METHOD_DELETE  = 'DELETE';
	const METHOD_TRACE   = 'TRACE';
	const METHOD_CONNECT = 'CONNECT';
	const METHOD_PATCH   = 'PATCH';

	/**
	 * 应用对象
	 * @var AbstractModule
	 */
	protected $module;

	/**
	 * 设置Application
	 * @param Application $application
	 */
	public function setModule(AbstractModule $module) {
		$this->module = $module;
		return $this;
	}

	/**
	 * 获取Application
	 * @return Application
	 */
	public function getModule() {
		return $this->module;
	}

	/**
	 * 页面跳转
	 * @param string $redirect
	 */
	public function redirect($redirect) {
		header('Location: '.$redirect);
		exit();
	}
	public function hasPower(){
		if(!$this->getUserInfo()){
			 echo '无权限。<a href="'.$this->getConfig()->get('_BASE_URL_').'/user/login">请登录</a>';
			exit ;
		}
	}
	public function __call($method, $args = array()) {
		if(in_array($method, array(
				'getModuleName', 'getActionName', 'getControllerName', 'getConfig'
				))) {
			
			return call_user_func_array(array($this->module, $method), $args);
		}
	}

	/**
	 * Is this a GET method request?
	 *
	 * @return bool
	 */
	public function isGet() {
		return ($_SERVER['REQUEST_METHOD'] === self::METHOD_GET);
	}

	/**
	 * Is this a POST method request?
	 *
	 * @return bool
	 */
	public function isPost() {
		return ($_SERVER['REQUEST_METHOD']  === self::METHOD_POST);
	}

	/**
	 * Is this a PUT method request?
	 *
	 * @return bool
	 */
	public function isPut() {
		return ($_SERVER['REQUEST_METHOD']  === self::METHOD_PUT);
	}

	/**
	 * Is this a DELETE method request?
	 *
	 * @return bool
	 */
	public function isDelete() {
		return ($_SERVER['REQUEST_METHOD']  === self::METHOD_DELETE);
	}

	/**
	 * Is the request a Javascript XMLHttpRequest?
	 *
	 * Should work with Prototype/Script.aculo.us, possibly others.
	 *
	 * @return boolean
	 */
	public function isXmlHttpRequest() {
		return false !== isset($_SERVER['X_REQUESTED_WITH']) && $_SERVER['X_REQUESTED_WITH'] == 'XMLHttpRequest';
	}

	/**
	 * GET param
	 * @param unknown_type $var
	 * @param unknown_type $type
	 * @param unknown_type $default
	 * @author xubin
	 */
	public function get($var, $type = 'str', $default = null) {
		if(isset($_GET[$var])) return $this->formate($type, $_GET[$var]);
		return addslashes($this->formate($type, $default));
	}
	/**
	 * POST param
	 * @param unknown_type $var
	 * @param unknown_type $type
	 * @param unknown_type $default
	 * @author xubin
	 *
	 */
	public function post($var, $type = 'str', $default = null) {
		if(isset($_POST[$var])) return $this->formate($type, $_POST[$var]);
		return addslashes($this->formate($type, $default));
	}
	/**
	 * formate param
	 * @param unknown_type $type
	 * @param unknown_type $value
	 * @author xubin
	 *
	 */
	public function formate($type, $value) {
		switch (strtolower($type)) {
			case 'int' :
				$value = (int)$value;
				break;
			case 'str' :
				$value = (string)trim($value);
				break;
			case 'arr' :
				if(!is_array($value)) {
					$value = (array) $value;
				}
				break;
			case 'float' :
				$value = (float)$value;
				break;
			default:
				$value = NULL;
				break;
		}
		return $value;
	}
	/**
	 * 返回limit
	 */
	public function getLimit($size = 30) {
		$page = $this->get('page', 'int', 1);
		$limit = ($page - 1) * $size . ',' .  $size;
		return $limit;
	}
	/**
	 * 返回当前用户信息
	 * @author xubin
	 */
	public function getUserInfo(){
		$userApi = Service::factory('User::UserApi');
		$userName = $userApi->current();
		if($userName) return $userName;
		else return array();
	}
	/**
	 * 返回单状态的JsonView
	 * @param unknown_type $status
	 * @return JsonView
	 */
	public function returnStatusView($status = true) {
		if($status) {
			return new JsonView(array('status' => true));
		}
		else return new JsonView(array('status' => false));
	}
	/**
	 * 返回绘制图表需要的数据
	 * @param unknown_type $arr
	 * @param unknown_type $filed
	 * @author: xubin
	 */
	public function array2Chart($arr, $filed) {
		$return = array();
		foreach($arr as $key => $value) {
			foreach($value as $k => $v) {
				if(!in_array($k, $filed)) unset($arr[$key][$k]);
			}
			$return[] = $value;
		}
		return $return;
	}
	/**
	 * 设置当前登录的用户
	 */
	private function setUserState(){
		var_dump($_SESSION['username']);
		if($_SESSION['username']){
			if(!$_COOKIE['iamaddy_uid']) setcookie("iamaddy_uid", md5($_SESSION['username']));
			if(!$_COOKIE['iamaddy_name']) setcookie("iamaddy_name", $_SESSION['username']);
		}
	}
}
