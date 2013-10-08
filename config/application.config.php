<?php
! isset ( $_SERVER ['HTTP_HOST'] ) ? $_SERVER ['HTTP_HOST'] = 'www.home.com' : '';
error_reporting ( E_ALL );
error_reporting(E_ALL || ~E_NOTICE);
date_default_timezone_set ( 'PRC' );

return array (
		// SERVER 系列定义
		'_SERVER_URL_' => 'http://' . $_SERVER ['HTTP_HOST'],
		'_SERVER_NAME_' => 'iamaddy',
		'_BASE_URL_' => 'http://' . $_SERVER ['HTTP_HOST'] . '/index.php',
		
		// 布局设置
		'template_map' => array (
				'layout' => 'module/Index/View/layout.html' 
		)
		/**
		 *
		 * @todo 异常，404 等模板
		 */
		,
		
		// 数据库配置
		'db' => array (
				'lodatabase' => array (
						'dsn' => 'mysql:host=127.0.0.1;dbname=lodatabase',
						'user' => 'root',
						'pass' => '123456',
						'opts' => array (
								PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8' 
						)
				)
		) 
);
