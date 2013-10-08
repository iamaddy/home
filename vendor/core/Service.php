<?php
/**
 * File:        Service.php
 *
 */

class Service {
	/**
	 * 创建服务对象工厂方法
	 * @param  string $service
	 * @throws RuntimeException
	 * @return AbstractService
	 */
	public static function factory($service) {
		$serviceArr = explode('::', $service);

		if(count($serviceArr) != 2) {
			throw new RuntimeException('Create Service param error. "'.$service.'"');
		}

		$serviceClassName = ucfirst($serviceArr[1]);

		if(class_exists($serviceClassName)) return new $serviceClassName;

		$module = ucfirst($serviceArr[0]);

		Autoloader::addDefaultPath(array(
									'module/'.$module.'/Model',
									'module/'.$module.'/Service',
								));

		$serviceFile = 'module/'.$module.'/Service/'.str_replace('_', '/', $serviceClassName).'.php';

		if(!file_exists($serviceFile)) {
			throw new RuntimeException('Service file "'.$serviceFile.'" is not exists.');
		}
		include $serviceFile;
		if(!class_exists($serviceClassName)) {
			throw new RuntimeException('Service Class "'.$serviceClassName.'" is not exists.');
		}

		return new $serviceClassName;
	}
}
