<?php
/**
 * File:        Application.php
 *
 * 应用类
 *
 */

class Application {
	/**
	 * 配置管理器
	 * @var Config
	 */
	private $config;

	/**
	 * 当前模块名称
	 * @var Module
	 */
	private $moduleName;

	/**
	 * 控制器名称
	 * @var string
	 */
	private $controllerName;

	/**
	 * 动作名称
	 * @var string
	 */
	private $actionName;
	
	private $layout = true;

	/**
	 * 初始化应用
	 * @param array|null $configure
	 * @return DDClick_Application
	 */
	public static function init($configure = null) {
		//初始化配置
		$config = new Config($configure);

		$self = new self();
		$self->setConfig($config);
		Db::setConfig($config);
		return $self;
	}

	// 应用执行
	public function run() {
		// 获取请求
		$requestPath = Url::getRequestPath();
		// 解析请求
		$pieces = explode('/', $requestPath);
		$moduleName 	 = isset($pieces[0]) && $pieces[0]? ucfirst($pieces[0]): 'Index';
		$controllerName = isset($pieces[1]) && $pieces[1]? ucfirst($pieces[1]): 'Index';
		$actionName 	 = isset($pieces[2]) && $pieces[2]? ucfirst($pieces[2]): 'Index';
		if(substr($controllerName, -4) == '.php') $controllerName = substr($controllerName, 0, -4);
		
		//设置自动加载目录
		Autoloader::addDefaultPath(array(
								'module/'.$moduleName.'/Model',
								'module/'.$moduleName.'/Service',
							));

		// 加载模块
		$module = $this->loadModule($moduleName);
		$module->setControllerName($controllerName)
		       ->setActionName($actionName);
		
		// 初始化模块
		if(method_exists($module, '_autoloadConfig')) {
			$config = $module->_autoloadConfig();
			$this->layout = is_array($config) && isset($config['layout']) ? $config['layout'] : true;
			$conf = $this->getConfig();
			$conf->set($config);
		}
		
		$module->setConfig($this->getConfig());

		// 初始化模块
		if(method_exists($module, '_init')) {
			$config = $module->_init();
			$this->layout = is_array($config) && isset($config['layout']) ? $config['layout'] : true;
		}
		// 控制器执行
		$view = $module->loadController($controllerName)
		       		   ->runAction($actionName);
		
		$view->setModuleName($moduleName)
		     ->setControllerName($controllerName)
		     ->setActionName($actionName)
		     ->setModule($module)
			 ->sendHeader()
			 ->renderer($this->layout);
	}

	/**
	 * 加载模块
	 * @param string $moduleName
	 * @throws RuntimeException
	 * @return Module
	 */
	private function loadModule($moduleName) {
		$moduleFile = 'module/'.ucfirst($moduleName).'/Module.php';
		if(!file_exists($moduleFile)) {
			throw new RuntimeException('Module file "'.ucfirst($moduleName).'" is not exist.');
		}

		include $moduleFile;

		if(!class_exists('Module')) {
			throw new RuntimeException('Class "Module" is not defined.');
		}

		$module = new Module();
		$module->setModuleName($moduleName);

		return $module;
	}

	// 设置配置管理器
	public function setConfig($config) {
		$this->config = $config;
		return $this;
	}

	// 获取配置管理器
	public function getConfig() {
		if(!$this->config) {
			$this->setConfig(new Config());
		}
		return $this->config;
	}
}
