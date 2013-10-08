<?php

class Autoloader {
	/**
	 * 默认加载目录
	 * @var array
	 */
	private static $_defaultPath = array();

	/**
	 * 类前缀和路径对应 类似namespace
	 * @var array
	 */
	private static $_load = array();

	/**
	 * 添加自动加载目录<namespace形式>
	 * @param string 	$ns 		// 类库前缀<namespace>
	 * @param string 	$path       // 类库路径
	 * @param boolean 	$default    // 是否默认加载目录
	 */
	public static function add(string $ns, string $path) {
		self::$_load[$ns] = $path;
	}

	/**
	 * 设置默认加载目录
	 * @param string $path
	 */
	public static function addDefaultPath($path) {
		if(is_array($path)) {
			self::$_defaultPath = array_merge(self::$_defaultPath, $path);
		} else if(is_string($path)) {
			array_push(self::$_defaultPath, $path);
		}
		self::$_defaultPath = array_unique(self::$_defaultPath);
	}

	// 注册成SPL自动加载
	public static function register() {
		spl_autoload_register(array(__CLASS__, 'loadClass'));
	}

	/**
	 * 加载类
	 * @param string $classname
	 */
	public static function loadClass($classname) {
		$nameExploded = explode('_', $classname);
		
		$ns = array_shift($nameExploded);

		if(isset(self::$_load[$ns])) {
			$classfile = self::$_load[$ns].'/'.str_replace('_', '/', $classname).'.php';
			if(file_exists($classfile)) include $classfile;
		}

		// 未加载到，直接从默认加载目录加载
		if(!class_exists($classname)) {
			foreach (self::$_defaultPath as $path) {
				$classfile = $path.'/'.str_replace('_', '/', $classname).'.php';
				if(file_exists($classfile)) include $classfile;
			}
		}
	}
}
