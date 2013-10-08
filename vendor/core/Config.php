<?php

class Config {

	/**
	 * 配置信息
	 * @var array
	 */
	private $config = array();

	/**
	 * 构造器
	 * @param array $config
	 */
	public function __construct($config = null) {
		if(is_array($config)) $this->set($config);
	}

	/**
	 * 获取配置
	 * @param null|string $key
	 * @param null|mixed $default
	 * @return multitype:|NULL|Ambigous <unknown, multitype:>
	 */
	public function get($key = null, $default = null) {
		if(is_null($key)) return $this->$config;

		$arr = explode('.', $key);
		$return = $this->config;
		foreach ($arr as $value) {
			if(isset($return[$value])) $return = $return[$value];
			else return null;
		}
		return $return;
	}

	/**
	 * 设置配置
	 * @param array $var
	 */
	public function set(array $var) {
		$this->config = $this->_arrayMergeRecursive($this->config, $var);
		return $this;
	}

	/**
	 * Merge two arrays recursively, overwriting keys of the same name
	 * in $firstArray with the value in $secondArray.
	 *
	 * @copyright copy from zend framework
	 *
	 * @param  mixed $firstArray  First array
	 * @param  mixed $secondArray Second array to merge into first array
	 * @return array
	 */
	private function _arrayMergeRecursive($firstArray, $secondArray)
	{
		if (is_array($firstArray) && is_array($secondArray)) {
			foreach ($secondArray as $key => $value) {
				if (isset($firstArray[$key])) {
					$firstArray[$key] = self::_arrayMergeRecursive($firstArray[$key], $value);
				} else {
					if($key === 0) {
						$firstArray= array(0=>self::_arrayMergeRecursive($firstArray, $value));
					} else {
						$firstArray[$key] = $value;
					}
				}
			}
		} else {
			$firstArray = $secondArray;
		}

		return $firstArray;
	}
}
