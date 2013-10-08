<?php

class AbstractModule {
	/**
	 * 配置管理器
	 * @var Config
	 */
	protected $config;

	/**
	 * 模块名称
	 * @var string
	 */
	protected $moduleName;

	/**
	 * 控制器名称
	 * @var string
	 */
	protected $controllerName;

	/**
	 * 动作名称
	 * @var string
	 */
	protected $actionName;

	/**
	 * 控制器
	 * @var Controller
	 */
	protected $controller;

	// 获取当前模块名称
	public function getModuleName() {
		return $this->module;
	}

	/**
	 * 设置当前模块名称
	 * @param string $moduleName
	 */
	public function setModuleName($moduleName) {
		$this->module = $moduleName;
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

	/**
	 * 加载控制器
	 * @param  string 	$controllerName 控制器名称
	 * @throws RuntimeException
	 * @return Module 					模块对象
	 */
	public function loadController($controllerName) {
		$controllerName = strtolower($controllerName);

		$controllerFile = 'module/'.ucfirst($this->getModuleName()).'/Controller/'.$controllerName.'.php';
		if(!file_exists($controllerFile)) {
			throw new RuntimeException('Controller "'.$controllerName.'" is not exist.');
		}
		
		include $controllerFile;
		$controllerClassName = $controllerName.'Controller';
		if(!class_exists($controllerClassName)) {
			throw new RuntimeException('Controller class "'.$controllerClassName.'" is not exist.');
		}

		$controller = new $controllerClassName;

		$controller->setModule($this);

		$this->setController($controller);
		
		return $this;
	}

	/**
	 * 设置控制器
	 * @param Controlller $controller
	 */
	protected function setController(AbstractController $controller) {
		$this->controller = $controller;
	}

	/**
	 * 执行
	 * @param  string $actionName
	 * @throws RuntimeException
	 * @return view
	 */
	public function runAction($actionName) {
		$action = ucfirst($actionName).'Action';

		if(!method_exists($this->controller, $action)) {
			throw new RuntimeException('method "'.$action.'" is not exist.');
		}
		$view = $this->controller->{$action}();
		// 视图渲染输出
		if(!($view instanceof View)) {
			$view = new View();
		}
		return $view;
	}

	/**
	 * 获取配置管理器
	 * @param Controlller $controller
	 */
	public function getConfig() {
		if(!$this->config) {
			$this->setConfig(new Config());
		}

		return $this->config;
	}

	/**
	 * 设置配置管理器
	 * @param Config $config
	 */
	public function setConfig(Config $config) {
		$this->config = $config;

		return $this;
	}
}
