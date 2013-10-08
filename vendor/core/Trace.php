<?php
/**
 * Project:     Library classes
 * File:        Trace.php
 * 
 * Trace跟踪
 * 
 */

class Trace {
	
	private static $trace = array();
	/**
	 * 设置当前行执行的时间点
	 * @param unknown_type $line
	 */
	public static function line($line) {
		self::$trace[$line] = self::microtimeFloat();
	}

	public static function time() {
		list($usec, $sec) = explode(" ", microtime());
		return ((float)$usec + (float)$sec);
	}
	/**
	 * 时间
	 */
	private static function microtimeFloat() {
		list($usec, $sec) = explode(" ", microtime());
    	return ((float)$usec + (float)$sec);
	}

	/**
	 * trace 日志
	 * @param unknown_type $method
	 * @param unknown_type $file
	 * @param unknown_type $line_start
	 * @param unknown_type $line_end
	 */
	public static function log($method, $file, $line_start, $line_end) {
		$time = self::$trace[$line_end]-self::$trace[$line_start];
		
		$message = 'Method: '.$method .' time: '.$time.' file: '.$file;
		Log::write($message, 'DEBUG');
	}
}