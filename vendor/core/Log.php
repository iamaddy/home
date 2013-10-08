<?php
/**
 * Project:     Library classes
 * File:        Log.php
 * 
 * Log::write($message, Log::ERROR, $email, $phone);
 * 
 */

class Log {
	const ERROR = 'ERROR'; //严重错误，导致系统崩溃无法使用
	const ALERT = 'ALERT'; //警戒性错误， 必须被立即修改的错误
	const CRIT  = 'CRIT';  //临界值错误， 超过临界值的错误，例如一天 24 小时，而输入的是 25 小时这样
	const WARN  = 'WARN';  //警告性错误， 需要发出警告的错误
	const INFO  = 'INFO';  //信息，程序输出信息
	const DEBUG = 'DEBUG'; //调试，用于调试信息
	const SQL   = 'SQL';   //SQL 语句，该级别只在调试模式开启时有效
	
	/**
	 * 写日志到文件：错误等级\t 服务器IP\t 客户端IP\t 客户端信息\t 错误信息\t 是否发送email\t 是否发送手机\r\n
	 * @param string $message
	 * @param string $level
	 * @param string $email
	 * @param string $phone
	 */
	public static function write($message, $level = 'ERROR', $email = NULL, $phone = NULL) {
		//去除$message中的html标签
		$message = strip_tags($message);
		
		//去除$message中的换行符,制表符
		$message = preg_replace('/([\s]{2,})/', "", $message);
		if(!file_exists(_ROOT_.'/data/log')) {
			@mkdir(_ROOT_.'/data/log');
		}
		@chmod(_ROOT_.'/data/log', 0777);
		
		if (is_writable(_ROOT_.'/data/log')) {
			$log = date('Y-m-d').'.log';
			
			$content[] = $level;
			$content[] = date('Y-m-d H:i:s');
			$content[] = isset($_SERVER['SERVER_ADDR'])? $_SERVER['SERVER_ADDR']: '';
			$content[] = Util::get_client_ip();
			$content[] = isset($_SERVER['HTTP_USER_AGENT'])? $_SERVER['HTTP_USER_AGENT']: '';
			$content[] = $message;
			$content[] = $email;
			$content[] = $phone;
			
			$content = implode("\t", $content);
			
			return @error_log($content.PHP_EOL, 3, _ROOT_.'/data/log/'.$log);
		}
	}
}