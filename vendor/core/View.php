<?php

class View implements View_Interface {
	/**
	 * 模板路径
	 * @var string
	 */
	protected $template;

	/**
	 * 模板变量
	 * @var array
	 */
	protected $variables = array();

	/**
	 * 所属模块
	 * @var string
	 */
	protected $moduleName;

	/**
	 * 所属控制器
	 * @var string
	 */
	protected $controllerName;

	/**
	 * 所属动作名
	 * @var string
	 */
	protected $actionName;

	/**
	 * 是否启用布局
	 * @var string
	 */
	protected $layout = true;

	/**
	 * 头信息编码
	 * @var array
	 */
	protected $charset = 'UTF-8';

	/**
	 * 构造器
	 * @param array|null $vars
	 */
	public function __construct($vars = null) {
		$this->getUserState();
		if(is_array($vars)) {
			$this->setVariables($vars);
		}
	}
	/**
	 * 获取登录用户的状态
	 */
	private function getUserState(){
		session_start();
		$username = $_SESSION['username'];
		if(!empty($username) && empty($_COOKIE['iamaddy_uid'])){
			setcookie("iamaddy_uid", md5($username));
			setcookie("iamaddy_name", $username);
		}
		return empty($username) ? false : true;
	}

	/**
	 * 设置字符集
	 * @param string $charset
	 */
	public function setCharset($charset) {
		/**
		 * @todo 检查编码是否合法
		 */
		$this->charset = $charset;

		return $this;
	}

	// 获取字符集
	public function getCharset() {
		return $this->charset;
	}

	/**
	 * 发送头信息
	 */
	public function sendHeader() {
		header('Content-Type: text/html; charset='.$this->getCharset());

		return $this;
	}

	/**
	 * 是否启用布局
	 * @param boolean $boolean
	 * @return View
	 */
	public function layout($boolean) {
		$this->layout = (boolean) $boolean;
		return $this;
	}

	// 渲染输出
	public function renderer($isLayout = true) {
		$templateFile = $this->getTemplate();
		if(!file_exists($templateFile)) {
			throw new RuntimeException('Template file "'.$templateFile.'" is not exists.');
		}
		
		/**
		 * @todo 模板引擎加入
		 */
		
		if($isLayout && $this->layout && $this->cfg('template_map.layout')) {
			include $this->cfg('template_map.layout');
		} else {
			include $templateFile;
		}
		
	}

	/**
	 * 获取模板
	 * @return string
	 */
	public function getTemplate() {
		if(!$this->template) {
			$this->setTemplate();
		}

		return $this->template;
	}

	/**
	 * 设置模板
	 * @param null|string $template
	 * @return View
	 */
	public function setTemplate($template = null) {
		$tmpl[] = 'module';
		$tmpl[] = $this->getModuleName();
		$tmpl[] = 'View';
		$tmpl[] = strtolower($this->getControllerName());

		if($template) $tmpl[] = $template;
		else $tmpl[] = strtolower($this->getActionName()).'.html';

		$this->template = implode('/', $tmpl);

		return $this;
	}

	/**
	 * 设置模板
	 * @param null|string $template
	 * @return View
	 */
	public function setWidgetTemplate($module, $widgetName) {
		$tmpl[] = 'module';
		$tmpl[] = $module;
		$tmpl[] = 'Widget';
		$tmpl[] = ucfirst($widgetName);
		$tmpl[] = 'widget.html';

		$this->template = implode('/', $tmpl);

		return $this;
	}

	/**
	 * 调用Widget
	 * @param string $widget 挂件名称
	 * @param array  $params 传递的参数
	 * @throws RuntimeException
	 */
	public function widget($widget, $params = array()) {
		
		$widgetArr = explode('::', $widget);
		if(count($widgetArr) == 2) {
			$module 	= ucfirst($widgetArr[0]);
			$widgetName = ucfirst($widgetArr[1]);
		} else if(count($widgetArr) == 1) {
			$module 	= ucfirst($this->getModuleName());
			$widgetName = ucfirst($widgetArr[0]);
		}
		$widgetClassName = $widgetName.'Widget';
		
		$widgetFile = 'module/'.$module.'/Widget/'.$widgetName.'/widget.php';
		if(!file_exists($widgetFile)) {
			throw new RuntimeException('Widget file "'.$widgetFile.'" is not exists.');
		}

		include $widgetFile;
		if(!class_exists($widgetClassName)) {
			throw new RuntimeException('Widget Class "'.$widgetClassName.'" is not exists.');
		}
		$widgetObj = new $widgetClassName;
		$view = $widgetObj->excute($params);
		
		// 视图渲染输出
		if(!($view instanceof View)) {
			$view = new View();
		}
		
		$view->setWidgetTemplate($module, $widgetName);
		if($params) $view->setVariables($params);

		$view->setModule($this->getModule());
		$view->renderer(false);
	}

	/**
	 * 设置变量值
	 * @param array $variable
	 */
	public function setVariables($variable) {
		$this->variables = array_merge($this->variables, $variable);

		return $this;
	}

	/**
	 * 赋值
	 * @param string     $key    key值
	 * @param mixed|null $value  键值
	 * @return View
	 */
	public function assign($key, $value = null) {
		$this->variables[$key] = $value;

		return $this;
	}

	/**
	 * 获取配置信息
	 * @param string $key
	 */
	public function cfg($key, $default = '') {
		
		return $this->getModule()->getConfig()->get($key, $default);
	}

	public function setModule(AbstractModule $module) {
		$this->module = $module;

		return $this;
	}

	public function getModule() {
		return $this->module;
	}

	// 获取当前模块名称
	public function getModuleName() {
		return $this->moduleName;
	}

	/**
	 * 设置当前模块名称
	 * @param string $moduleName
	 */
	public function setModuleName($moduleName) {
		$this->moduleName = $moduleName;
		return $this;
	}

	// 获取当前控制器名称
	public function getControllerName() {
		return $this->controllerName;
	}

	/**
	 * 设置当前控制器名称
	 * @param string $controllerName
	 */
	public function setControllerName($controllerName) {
		$this->controllerName = $controllerName;
		return $this;
	}

	// 获取当前动作名称
	public function getActionName() {
		return $this->actionName;
	}

	/**
	 * 设置当前动作名称
	 * @param string $actionName
	 */
	public function setActionName($actionName) {
		$this->actionName = $actionName;
		return $this;
	}

	public function __get($key) {
		if(isset($this->variables[$key])) {
			return $this->variables[$key];
		}
	}
	
	/**
	 * 截取字符串
	 * @param string $str
	 * @param number $num
	 * @return string
	 */
	public function trimWords($str, $num) {
		if (mb_strlen($str, 'utf-8') <= $num) {
			return $str;
		}
		return mb_substr($str, 0, $num, 'utf-8') . '...';
	}
}